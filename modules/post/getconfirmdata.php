<?php


$http = eZHTTPTool::instance();
$packageId = $_GET['packageId'];
$improves = $_GET['improve_options'];
$moduleName = $_GET['module'];
$user = eZUser::currentUser();
if($moduleName == 'listingactions')
{
	$objectId = $_GET['objectId'];
}
else
{
	$objectId = $http->sessionVariable("classified_obj_id");	
}	


$object = eZContentObject::fetch($objectId);
$classifiedObject = eZContentObject::fetch($objectId);
$adMetadata = AplClassifiedMetadata::instance($objectId, $packageId, $improves);

if($adMetadata instanceof AplClassifiedMetadata)
{
	$adMetadata->store();
	
}
else
	return false;

$totalPrice = $adMetadata->getTotalPrice();

$tpl = eZTemplate::factory();

if($moduleName == 'listingactions')
{
	$tpl->setVariable( 'classifiedName', $object->Name);
}
else
{
	$draft = eZContentObjectVersion::fetchUserDraft($adMetadata->classifiedObject->ID, $user->ContentObjectID);
	$tpl->setVariable( 'classifiedName', $draft->versionName());	
}	

$tpl->setVariable( 'totalPrice', $totalPrice );
$tpl->setVariable( 'modulename', $moduleName );
$tpl->setVariable( 'modulename', $moduleName );
$tpl->setVariable( 'objectId', $objectId );
$tpl->setVariable( 'publicationData', $adMetadata->getPublicationData());
$tpl->setVariable( 'selectedImproves', $adMetadata->selectedImproves);

$result =  $tpl->fetch( "design:post/confirmbox.tpl" );

ob_clean();
$charBreak = array("\r\n", "\n", "\r", "\t");
$result = str_replace($charBreak, "", $result);
echo $result;

$Result['pagelayout'] = false;
eZDB::checkTransactionCounter();
eZExecution::cleanExit();


?>