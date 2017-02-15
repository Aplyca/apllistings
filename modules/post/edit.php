<?php

$tpl = eZTemplate::factory();
$module = $Params['Module'];

//$check = PostingWorkflow::checkStep($Params);
//if (!$check) return false;

$steps = PostingWorkflow::getEnabledSteps($Params['FunctionName']);

$http = eZHTTPTool::instance();
$objectId = $http->sessionVariable("classified_obj_id");
$object = eZContentObject::fetch($objectId);
$attributes = array();

foreach($object->dataMap() as $attribute)
{
	
	$attributeClass = $attribute->contentClassAttribute();
	$attributeCategory =  $attributeClass->Category;
	if($attributeCategory == 'content' || $attributeCategory == 'filter')
	{
		$attributes[$attribute->ContentClassAttributeIdentifier] = $attribute;
	}
	else
	{
		if($attribute->ContentClassAttributeIdentifier == 'package')
		{
			if($attribute->content() instanceof eZContentObject && $attribute->content()->ClassIdentifier == 'package')
			{
				$package = $attribute->content();				
			}			
		}
	}
}

if($package)
{
	$packageData = AplClassifiedMetadata::packageToArray($package);	
	foreach($packageData['content_restrictions'] as $attributeIdentifier => $restrictionValue)
	{
		if($restrictionValue == 0)
		{
			unset($attributes[$attributeIdentifier]);
		}
	}	
}


$imagesCacheNodeId = PostingOperations::getImagesCacheNodeId();
$tpl->setVariable( 'images_cache_node_id', $imagesCacheNodeId );

if($module->isCurrentAction('NextStep'))
{
	$http = eZHTTPTool::instance();	
	$validation = ApleZTools::validateAttributesInput($attributes);

	if( $validation['result'] )
	{
		PostingOperations::setDefaultAttributesValue($objectId);
		ApleZTools::setAttributesFromImput($attributes);
		
		$module->redirectToView('confirm');
		
		//PostingWorkflow::authorizeStep('edit');
		//$module->redirectToView('package');	
	}
	else
	{

		$tpl->setVariable( 'obj_attributes', $attributes );
		$tpl->setVariable( 'steps', $steps );
		$tpl->setVariable( 'identifier', $object->contentClassIdentifier() );			
		$Result['content'] = $tpl->fetch("design:post/classifiededit.tpl");
		$Result['path'] = array( array('url' => 'post/edit', 'text' => 'Fill your post info'));		
			
	}		
}
else
{
	$tpl->setVariable( 'obj_attributes', $attributes );
	$tpl->setVariable( 'steps', $steps );
	$Result['content'] = $tpl->fetch("design:post/classifiededit.tpl");
	$Result['path'] = array( array('url' => 'post/edit', 'text' => 'Fill your post info'));	
}


?>