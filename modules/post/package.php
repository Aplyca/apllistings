<?php

$tpl = eZTemplate::factory();
$module = $Params['Module'];

$check = PostingWorkflow::checkStep($Params);
if (!$check) return false;

$steps = PostingWorkflow::getEnabledSteps($Params['FunctionName']);

$http = eZHTTPTool::instance();
$objectId = $http->sessionVariable("classified_obj_id");	
$object = eZContentObject::fetch($objectId);
$packages = PostingOperations::getPackagesData($objectId); 


if($module->isCurrentAction('Store') )
{	
	PostingOperations::storeClassified($objectId);	
	$http = eZHTTPTool::instance();
	PostingWorkflow::resetWorkflow();
	$module->redirectTo('/');
   	
}

else if($module->isCurrentAction('Publish'))
{
	PostingOperations::storeClassified($objectId);
	PostingOperations::publishClassified($objectId);	
	$http = eZHTTPTool::instance();
	PostingWorkflow::resetWorkflow();
	$module->redirectTo('/');	

}
else
{
	$tpl->setVariable( 'object', $object );
	$tpl->setVariable( 'objectID', $object->ID );
	$tpl->setVariable( 'packages', $packages );
	$tpl->setVariable( 'steps', $steps );
	$tpl->setVariable( 'modulename', $Params['ModuleName'] );	
	$Result['content'] = $tpl->fetch("design:post/classifiedpackage.tpl");
	$Result['path'] = array( array('url' => 'post/package', 'text' => 'Select your package'));
	
}

?>
