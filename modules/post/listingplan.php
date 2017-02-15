<?php

/**
 *  @author David Sánchez Escobar
 *  This is a starting view for the posting workflow, manages the package selection and the posting object creation
 *  Params: packageID, objectID (Optional)
 *  Setting Variables: workflowHandler, intefaceData, redirectData
 *  Actions/Data Set:
 *   	Set listing object: Listing object created if objectID is not set		    
 *  	Store Metadata: Publication Data ( 1 year hardcoded ), Price from package in listing object 
 *  	Clean images cache node
 *  	Redirect to the view defined by redirectData
 */


// Main variables definition
$tpl = eZTemplate::factory();
$module = $Params['Module'];
$objectID = $Params['ObjectID'];
$packageID = $Params['PackageID'];

$ini = eZINI::instance('apllistings.ini');	
$listingClass =  $ini->variable('Settings','ListingClassIdentifier');

$http = eZHTTPTool::instance();
$promotionID = $http->sessionVariable("promotion_enabled");

$hapd_ini = eZINI::instance('hapd.ini');		
$parentUserNodeID = $hapd_ini -> variable('UserSettings', 'DefaultUserPlacement');

$user = eZUser::currentUser();
$userContentObject = $user->contentObject();

//Validation for supporting to sub-users
if ( $userContentObject->mainParentNodeID() != $parentUserNodeID )
{		
	return $module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );
}

// ObjectID Validation : Since we don't have objectID when creating a new post we set it to 0 for default
if(ctype_digit($objectID))
{
	$objectID = (int) $objectID;
}
if($objectID == '')
$objectID = 0;

if( $objectID !== 0 )
{
	$postingObject = PostingObject::instance($objectID);
	if(!$postingObject)
	{
		return $module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );		
	}
}


// Check if we are editing a stored listing or creating one
$ini = eZINI::instance('apllistings.ini');	
$workflowHandlerClass =  $ini->variable('Settings','WorkflowHandlerClass');
$workflowHandler = new $workflowHandlerClass();
$intefaceData = $workflowHandler->getPostInterfaceData($Params['FunctionName'],$objectID);

// List of packages aviable for the specific listing
$packages = AplClassifiedMetadata::getPackagesDataByClass($listingClass);
$tpl->setVariable( 'packages', $packages );

$package = AplClassifiedMetadata::getPackageFromID($packageID);
$classID = eZContentClass::classIDByIdentifier($listingClass); 


if($package && $classID)
{	
	if( $objectID === 0 )
	{
		$postingObject = PostingObject::instanceNew($listingClass);
	}

	if(!$postingObject)
	{
		return $module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );			
	}
	
	if(!$postingObject->setPackage($package))
	{
		return $module->handleError( eZError::KERNEL_NOT_FOUND, 'kernel' );	
	}
				
	$postingObject->resetImagesNode();
	$redirectData =  $workflowHandler->getNextStep($Params['FunctionName']);		
	$module->redirect($redirectData['module'], $redirectData['view'], array($postingObject->id()));		
}
else 
{
	$http = eZHTTPTool::instance();	
	if($objectID)
	{
		$redirectData =  $workflowHandler->getNextStep($Params['FunctionName']);		
		$module->redirect($redirectData['module'], $redirectData['view'], array($objectID));	
		/*$object = eZContentObject::fetch($objectID);
		$dataMap = $object->dataMap();
		if ( eZContentObject::fetch($dataMap['package']->hasContent()) )
		{
			$package = eZContentObject::fetch($dataMap['package']->content()->ID);     
			$packageData = AplClassifiedMetadata::packageToArray($package);
			$tpl->setVariable( 'package_data', $packageData );	
		}*/
		
	}	
	else
	{
		if($promotionID)
		{
			$promotion = Promotion::instance($promotionID);
			$tpl->setVariable( 'promotion', $promotion->promotionData() );	
		}
		$tpl->setVariable( 'edition_mode', $intefaceData['mode'] );  
		$tpl->setVariable( 'steps', $intefaceData['steps'] );
		$tpl->setVariable( 'ObjectID', $objectID);
		$Result['content'] = $tpl->fetch("design:post/listingplan.tpl");
		$Result['path'] = array( array('url' => 'post/listingplan', 'text' => 'Select your plan'));
	}	
}



?>