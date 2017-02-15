<?php

$tpl = eZTemplate::factory();
$module = $Params['Module'];
$objectId = $Params['ObjectID'];

$postingObject = PostingObject::instance($objectId);
if(!$postingObject)
{
	return $module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );		
}



?>

