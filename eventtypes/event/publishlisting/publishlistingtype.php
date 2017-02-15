<?php
//
// Definition of AddType class
//


class PublishListingType extends eZWorkflowEventType
{
	const WORKFLOW_TYPE_STRING = "publishlisting";

	function PublishListingType()
	{
		$this -> eZWorkflowEventType(PublishListingType::WORKFLOW_TYPE_STRING, ezpI18n::tr( 'kernel/workflow/event', 'Publish Listing' ) );
		$this -> setTriggerTypes(array('content' => array('publish' => array('after'))));
	}
	
	function execute( $process, $event )
	{
		$parameters = $process->attribute( 'parameter_list');	
		$objectID = $parameters[object_id];	
		$object = eZContentObject::fetch( $objectID );
						
		if ($object instanceof eZContentObject and self::validateEvent($object))
		{
			$dataMap = $object->dataMap();
			
			if($dataMap['save']->content())
			{
				return eZWorkflowType::STATUS_ACCEPTED;	
			}
			
			if($dataMap['category']->content())
			{
				$category = $dataMap['category']->content();
				$categoryRelation = reset($category['relation_list']);
				$publishNodeID = $categoryRelation['node_id'];
			}
			else
			{
				 $apllistingsIni = eZINI::Instance('apllistings.ini'); 
	        	$publishNodeID = $apllistingsIni -> variable("General", "PublishNodeID");
	        }

	        $location = eZContentObjectTreeNode::fetch($publishNodeID);
	        $result = PostingObject::simplePublish($object, $location);
	        
		    if (!$result)
		    {
		        return eZWorkflowType::STATUS_REJECTED;
		    }             
		}
				
		return eZWorkflowType::STATUS_ACCEPTED;				
	}
	
	function validateEvent($object)
	{
		$apllistingsIni = eZINI::Instance('apllistings.ini'); 
		$listingClasses = $apllistingsIni -> variable('General', 'ListingsClasses');
		$content_class = $object -> contentClass(); 

		if( in_array($content_class -> Identifier, $listingClasses))
		{
			return true;
		}
		return false;
	}
}

eZWorkflowEventType::registerEventType( PublishListingType::WORKFLOW_TYPE_STRING, 'PublishListingType' );

?>
