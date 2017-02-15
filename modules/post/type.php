<?php


$tpl = eZTemplate::factory();

$module = $Params['Module'];
$adType = $Params['type'];

PostingWorkflow::resetWorkflow();

$check = PostingWorkflow::checkStep($Params); 
if (!$check) return false;


$steps = PostingWorkflow::getEnabledSteps($Params['FunctionName']);

$listingClasses = eZContentClass::fetchAllClasses( true, true, array(5) );
$listingClass = null;
foreach ($listingClasses as $listingClassOption)
{
	$validListingIdentifier = $listingClassOption->Identifier == $adType ? true:false;
	if($validListingIdentifier)
	{
		$listingClass = $listingClassOption;
		break;
	}
} 	

if($listingClass instanceof eZContentClass)
{
		PostingOperations::setCurrentObject($listingClass->ID);		
		PostingOperations::setImagesCacheNodeId();
		PostingWorkflow::authorizeStep('type');
		$module->redirectToView('edit');		
}
else
{
	$http = eZHTTPTool::instance();
	$tpl->setVariable( 'steps', $steps );
	$Result['content'] = $tpl->fetch("design:post/classifiedtype.tpl");
	$Result['path'] = array( array('url' => 'post/type', 'text' => 'Select your ad type'));	
}



?>