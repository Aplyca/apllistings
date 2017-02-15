<?php

$http = eZHTTPTool::instance();
$objectId = $Params['ObjectID'];
$module = $Params['Module'];
$relatdObjs = $http->sessionVariable("classified_obj_related");
$response = 0;


$object = eZContentObject::fetch($objectId);
if(!$object instanceof eZContentObject)
{
	echo "object not found";
	$Result['pagelayout'] = false;
	die();
}



if( !($object->attribute('can_edit') && $object->attribute( 'can_remove' )) )
{
	echo "access denied";
	$Result['pagelayout'] = false;
	die();	
}

if(! $object->mainNodeId() )
{
	foreach($relatdObjs as $parentId => $relatdObjsGroup)
	{
		foreach($relatdObjsGroup as $key=> $relatedIdentifier)
		{
			foreach($relatedIdentifier as $subkey => $relatedObjId)
			{
				if($objectId == $relatedObjId)
				{
					eZContentObjectOperations::remove( $objectId, true );	
					unset($relatdObjs[$parentId][$key][$subkey]);
					$http->setSessionVariable("classified_obj_related", $relatdObjs);			
					$response = 1;
					break;			
				}
			}
		}	
	}
}
else
{
	eZContentObjectOperations::remove( $objectId, true );	
	$response = 1;			
}


echo $response;
$Result['pagelayout'] = false;
eZDB::checkTransactionCounter();
eZExecution::cleanExit();


?>