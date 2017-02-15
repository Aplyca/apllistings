<?php

/**
 *  @author David Sánchez Escobar
 *  Manages the posting edition in multiple steps
 *  Params: objectID
 *  Setting Variables: workflowHandler, multiediHandler, stepsNumber, intefaceData, redirectData
 *  Actions/Data Set:
 *   	Store image cache node id into session variable		    
 *  	If action is 'store' validate attribute inputs and redirect to thwe proper view
 */

$tpl = eZTemplate::factory();
$module = $Params['Module'];
$http = eZHTTPTool::instance();
$objectId = $Params['ObjectID'];

$postingObject = PostingObject::instance($objectId);

if(!$postingObject)
{
	return $module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );		
}

$packageData = AplClassifiedMetadata::packageToArray($postingObject->getPackage());
$ini = eZINI::instance('apllistings.ini');	
$multieditHandlerClass =  $ini->variable('Settings','MultieditHandlerClass');
//  $multieditHandler = $multieditHandlerClass::instance($objectId); TODO Reenable after hapd development
$multieditHandler = HapdMultiedit::instance($objectId);
$editStepAttributes = $multieditHandler->getAttributesList();  
$editStepsNumer = $multieditHandler->getStepsNumber();
$workflowHandlerClass =  $ini->variable('Settings','WorkflowHandlerClass');
$workflowHandler = new $workflowHandlerClass();
$intefaceData = $workflowHandler->getPostInterfaceData($Params['FunctionName'],$objectId);
$imagesNodeId = $postingObject->getImagesNodeId();


$tpl->setVariable( 'images_cache_node_id', $imagesNodeId );
$tpl->setVariable( 'package_data', $packageData );
$tpl->setVariable( 'object_id', $postingObject->id() );
$tpl->setVariable( 'editstepsnumber', $editStepsNumer );
$tpl->setVariable( 'edition_mode', $intefaceData['mode'] ); 
$tpl->setVariable( 'steps', $intefaceData['steps'] );
$tpl->setVariable( 'hotel_type', $postingObject->type );
$tpl->setVariable( 'hotel_state', $postingObject->state );  


if($Params['Action']=='store')
{
	
	$initialEditStep = $multieditHandler->checkAttributesCompleted();
	if(!$initialEditStep)
	{
		$postingObject->setDefaultAttributesValue();		
		$redirectParams = array('hotel_state' => $postingObject->state, 'hotel_type'=> $postingObject->type);
		
		if($postingObject->type == 'saved')
		{			
			$object = eZContentObject::fetch($objectId);
			if($object instanceof eZContentObject)
			{
				$version = $object -> currentVersion();
				$versionNum = $version -> attribute( 'version' );	
				eZOperationHandler::execute('content', 'publish', array('object_id' => $object->attribute( 'id' ),
											'version'   => $versionNum,));	
			}										
		}		
		$redirectData =  $workflowHandler->getNextStep($Params['FunctionName'], $redirectParams);
		$module->redirect($redirectData['module'], $redirectData['view'], array($objectId));
	}				
	else
	{
		$tpl->setVariable( 'initialeditstep', $initialEditStep );				
		$Result['content'] = $tpl->fetch("design:post/classifiedmultiedit.tpl");
		$Result['path'] = array( array('url' => 'post/multiedit', 'text' => 'Fill your post info'));	
	}
}
else
{
	
	$tpl->setVariable( 'initialeditstep', 1 );	
	$Result['content'] = $tpl->fetch("design:post/classifiedmultiedit.tpl");
	$Result['path'] = array( array('url' => 'post/multiedit', 'text' => 'Fill your post info'));	
}

?>