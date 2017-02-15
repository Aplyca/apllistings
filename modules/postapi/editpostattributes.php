<?php

$module = $Params['Module'];
$response = array('status' => array(), 'message' => '');
if ( $module->isCurrentAction( 'EditAttributes' ) and $module->hasActionParameter( 'ObjectID' ) and $module->hasActionParameter( 'Attributes' ))
{
	$objectID = $module->actionParameter( 'ObjectID' );
	$object = eZContentObject::fetch($objectID);
	
	if (($object instanceof eZContentObject) and $object->attribute('can_read') and $object->attribute( 'can_edit' ))
	{
		$attributes = $module->actionParameter( 'Attributes' );	
		$apllistings_ini = eZINI::instance('apllistings.ini');
		$classes_edit_attributes = $apllistings_ini->variable('EditListingAttributes','Classes');
		$edit_attributes = array_flip(explode(';', $classes_edit_attributes['hotel']));		
		$valid_atributes = array_intersect_key($attributes, $edit_attributes);
		
		
		$object_attributes = $object->dataMap();
		foreach($valid_atributes as $identifier => $content)
		{
			$content_attribute = isset($object_attributes[$identifier])?$object_attributes[$identifier]:false;
			if ($content_attribute instanceof eZContentObjectAttribute)
			{
				$db = eZDB::instance();
			    $db->begin();
				$content_attribute->fromString($content);
				$content_attribute->store();
				$db->commit();
				$response['status'][$identifier]=true;
			}
		}
	}	
}

$Result['pagelayout'] = false;
eZDebug::updateSettings(array("debug-enabled" => false, "debug-by-ip" => false));
eZDB::checkTransactionCounter();
eZExecution::cleanExit();

?>