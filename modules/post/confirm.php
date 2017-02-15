<?php

$tpl = eZTemplate::factory();
$module = $Params['Module'];
$http = eZHTTPTool::instance();
$objectId = $Params['ObjectID'];
$ini = eZINI::instance('apllistings.ini');	

$workflowHandlerClass =  $ini->variable('Settings','WorkflowHandlerClass');
$workflowHandler = new $workflowHandlerClass();

$postingObject = PostingObject::instance($objectId);
if(!$postingObject)
{
	return $module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );		
}

$packageData = AplClassifiedMetadata::packageToArray($postingObject->getPackage());

if($postingObject->state() == 'published')
{
	return $module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );		
}
$multieditHandlerClass =  $ini->variable('Settings','MultieditHandlerClass');
$multieditHandler = call_user_func(array($multieditHandlerClass, "instance"), $objectId);
$initialEditStep = $multieditHandler->checkAttributesCompleted();
if($initialEditStep)
{
	$module->redirect('post', 'multiedit', array($objectId));
}



if($module->isCurrentAction('Store') )
{

	$postingObject->save();	
	PostingWorkflow::resetWorkflow();
	$module->redirectTo('/');   	
	
}

else if($module->isCurrentAction('Publish'))
{		

	if($workflowHandler->isConfirmApproved($objectId))
	{	
		$postingObject->save();
		$postingObject->publish();
		$object = eZContentObject::fetch($objectId);	// fetch	
		$publishActionParams =  array('object' => $object);	
		$publishActionsResponse = $workflowHandler->runPublishActions($module->currentModule(), $module->currentView(), $publishActionParams);		
		PostingWorkflow::finishWorkflow();
		$tpl->setVariable( 'object', $object );
		$tpl->setVariable( 'order', $publishActionsResponse['order']);
		$tpl->setVariable( 'package_data', $packageData );
		$Result['content'] = $tpl->fetch("design:post/confirm_response.tpl");
		$Result['path'] = array( array('url' => 'post/confirm', 'text' => 'Confirm listing result'));
	}		
	else
	{   
		$object = eZContentObject::fetch($objectId);
		$tpl->setVariable( 'confirmDenied', 1 );
		$tpl->setVariable( 'object', $object );
		$tpl->setVariable( 'modulename', $Params['ModuleName'] );	
		$tpl->setVariable( 'package_data', $packageData );
		$Result['content'] = $tpl->fetch("design:post/confirm.tpl");
		$Result['path'] = array( array('url' => 'post/confirm', 'text' => 'Confirm listing'));
	}
}
else if($module->isCurrentAction('Purchase'))
{
	if($workflowHandler->isConfirmApproved($objectId))
	{
		
		if(!$postingObject->save())
		{
			return $module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );	
		}
		PostingWorkflow::finishWorkflow();
		$redirectData =  $workflowHandler->getNextStep($Params['FunctionName']);
		$module->redirect($redirectData['module'], $redirectData['view'], array($objectId));	
	}
	else
	{
		$object = eZContentObject::fetch($objectId);
		$tpl->setVariable( 'confirmDenied', 1 );
		$tpl->setVariable( 'object', $object );
		$tpl->setVariable( 'modulename', $Params['ModuleName'] );	
		$tpl->setVariable( 'package_data', $packageData );
		$Result['content'] = $tpl->fetch("design:post/confirm.tpl");
		$Result['path'] = array( array('url' => 'post/confirm', 'text' => 'Confirm listing'));
	}
}
else
{
	$postingObject->save();
	$object = eZContentObject::fetch($objectId);
	$tpl->setVariable( 'object', $object );
	$tpl->setVariable( 'modulename', $Params['ModuleName'] );	
	$tpl->setVariable( 'package_data', $packageData );
	$Result['content'] = $tpl->fetch("design:post/confirm.tpl");
	$Result['path'] = array( array('url' => 'post/confirm', 'text' => 'Confirm listing'));
	
}

?>
