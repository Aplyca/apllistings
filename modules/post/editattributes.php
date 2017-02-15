<?php

/**
 *  @author David Sánchez Escobar
 *  This is a inline view, called trhough ajax in the mutliedit view, brings the html attribute edition interface
 *  defined by each step in the multiedit handler class. You must define your customized handler class, extending the
 *  ListingMultiedit and implementing the required abastract methods
 *  Params: objectID
 *  Setting Variables: multiediHandler, additionalAttributes, intefaceData, redirectData
 *  Actions/Data Set:
 *  	For Store Action:
 *  		validate attributes input
 *   		do aditional validations by step	    
 *  		redirect to the proper view
 *  	If not action show edition interface
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

$ini = eZINI::instance('apllistings.ini');	
$multieditHandlerClass =  $ini->variable('Settings','MultieditHandlerClass');
//$multieditHandler = $multieditHandlerClass::instance($objectId); TODO: Reenable after hapd dev
$multieditHandler = HapdMultiedit::instance($objectId);
$attributes = $multieditHandler->getAttributes($Params['EditStep']);
$tplPath = $multieditHandler->getTemplatePathByStep($Params['EditStep']);  
$additionalAttributes = $multieditHandler->getAditionalAttributes($Params['EditStep']);
$packageData = AplClassifiedMetadata::packageToArray($postingObject->getPackage());    	


if($Params['Action'] == 'store')
{
	$http = eZHTTPTool::instance();	
	$plainAdditionalAttributes = $multieditHandler->plainArray($additionalAttributes);
	if(!empty($plainAdditionalAttributes))
	{
		$attributes = array_merge($attributes, $plainAdditionalAttributes);
	}		
	$validation = ApleZTools::validateAttributesInput($attributes);			
	$fielsetsResponse = $multieditHandler->checkFieldSets($Params['EditStep']);
	$fieldsets = $fielsetsResponse['attributes'];	
	$additionalValidation = $multieditHandler->doAdditionalValidations($Params['EditStep'], $validation);
	$validation = $additionalValidation['validation'];
 
	if( $validation['result'] &&  $fielsetsResponse['result'])
	{		
		ApleZTools::setAttributesFromImput($attributes);	
		$multieditHandler->republishRelatedObjects($Params['EditStep'], $postingObject);
		if($_POST['republish'])
		{
			$postingObject->republish();
		}
		$Result['content'] = 1;					
	}
	else
	{
		$tpl->setVariable( 'package_data', $packageData );
		$tpl->setVariable( 'objectID', $objectId );
		$tpl->setVariable( 'obj_attributes', $attributes );
		$tpl->setVariable( 'additional_attributes', $additionalAttributes );
		$tpl->setVariable( 'fieldsets', $fieldsets );	
		$tpl->setVariable( 'step', $Params['EditStep'] );
		$tpl->setVariable( 'identifier', $postingObject->classIdentifier );	
		$tpl->setVariable( 'error_msg', $additionalValidation['error_msg'] );	
		$tpl->setVariable( 'posting_type', $postingObject->type ); 
		$Result['content'] = $tpl->fetch("design:$tplPath");
		$Result['path'] = array( array('url' => 'post/edit', 'text' => 'Fill your post info'));					
	}		
}
else
{	
	$fieldsets = $multieditHandler->getFieldSets($Params['EditStep']);	
	$tpl->setVariable( 'package_data', $packageData );
	$tpl->setVariable( 'objectID', $objectId );
	$tpl->setVariable( 'obj_attributes', $attributes );
	$tpl->setVariable( 'additional_attributes', $additionalAttributes );
	$tpl->setVariable( 'fieldsets', $fieldsets );		
	$tpl->setVariable( 'step', $Params['EditStep'] );	
	$tpl->setVariable( 'posting_type', $postingObject->type ); 
	$Result['content'] = $tpl->fetch("design:$tplPath");
	$Result['path'] = array( array('url' => 'post/editattributes', 'text' => 'Fill your post info'));	
}

$Result['pagelayout'] = false;
eZDebug::updateSettings(array("debug-enabled" => false, "debug-by-ip" => false));


?>