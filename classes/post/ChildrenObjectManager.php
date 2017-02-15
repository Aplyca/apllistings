<?php 

class ChildrenObjectManager
{
	
	var $classIdentifier;
	var $object;
			
	public function __construct($classIdentifier, $object) 
    {    
		$this->classIdentifier = $classIdentifier;
		if($object)
		{
			$this->object = $object;
		}
    }   
    
    public function setRelatedChildren($parentObjectID)
    {   	
    	$classID = eZContentClass::classIDByIdentifier($this->classIdentifier); 
    	if(!$classID)
    		return false;    		
    	$obj = AplContentHandler::createContentObjectDraft($this->classIdentifier, 1, true); // omiting permissions 
    	if(!$obj instanceof eZContentObject)
    		return false;
    		
    	$postingObject = PostingObject::instance($parentObjectID);	
    	$this->object = $obj;   
    	if($postingObject->type == 'draft' )
		{ 
	   		$http = eZHTTPTool::instance();
			if(!$parentObjectID)
				return false;
			
	    	if(!$http->hasSessionVariable("classified_obj_related"))
	    	{
			    	$relatedObjs = array();		    		
	    	}	
	    	else
	    	{
	    		$relatdObjs = $http->sessionVariable("classified_obj_related");	
	    	}
	    	if(!is_array($relatdObjs[$parentObjectID]))
	    	{
	    		$relatdObjs[$parentObjectID] = array();
	    	}    	
	    	if(!is_array($relatdObjs[$parentObjectID][$this->classIdentifier]))
	    	{
	    		$relatdObjs[$parentObjectID][$this->classIdentifier] = array();
	
	    	}	    	 	
	   		$relatdObjs[$parentObjectID][$this->classIdentifier][] = $obj->ID;
	   		$http->setSessionVariable("classified_obj_related", $relatdObjs);
		}    		    	
		else
		{   
		    $announcementNodeId = $postingObject->mainNodeId();  
		    $db = eZDB::instance(); 
		    ChildrenObjectManager::saveChildrenObject($announcementNodeId, $obj->ID, $db);        
		}	    	
    }
    
    
    public function getTemplatePath()
    {
    	$contentObjectFree = eZINI::instance('apllistings.ini');	
		$templateMap =  $contentObjectFree->variable('Settings','ChildrenObjectsTemplateMap');
		return $templateMap[$this->classIdentifier];
    }
    
    static function setChildrenObjects($objectId)
    {
    	$announcementObject = eZContentObject::fetch($objectId);
    	if(! $announcementObject instanceof eZContentObject)
    		return false;
		$http = eZHTTPTool::instance();
    	$announcementNode = $announcementObject->mainNode();    	   		
    	$childrenObjectTypes = $http->sessionVariable("classified_obj_related");
    	if(!is_array($childrenObjectTypes[$objectId]))
    		return false; 
    	$db = eZDB::instance();    	     	
    	foreach($childrenObjectTypes[$objectId] as $childrenObjectGroup)
    	{    	    			   		
    		foreach($childrenObjectGroup as$childrenObjectId)
    		{
    			$childrenObject = eZContentObject::fetch($childrenObjectId);
	   			if(! $childrenObject instanceof eZContentObject)
		    		continue;
		    	$versionNum = $childrenObject->currentVersion()->attribute( 'version' );			
	    		$db->begin();	
				$childrenObject->createNodeAssignment( $announcementNode->NodeID, true );				
				$childrenObject->store();
				$db->commit();								
				eZOperationHandler::execute('content','publish',array('object_id' => $childrenObjectId, 'version'=>$versionNum));	
    		}	    			  				
    	}    	  	 
    }
    
    static function saveChildrenObject($parentNodeId, $childrenObjectId, $db)
    {
    	$childrenObject = eZContentObject::fetch($childrenObjectId);
	   	if(! $childrenObject instanceof eZContentObject)
			return false;
    	$versionNum = $childrenObject->currentVersion()->attribute( 'version' );			
   		$db->begin();	
		$childrenObject->createNodeAssignment( $parentNodeId, true );				
		$childrenObject->store();
		$db->commit();								
		eZOperationHandler::execute('content','publish',array('object_id' => $childrenObjectId, 'version'=>$versionNum));
    }
    
    
    public function getChildrenObjects()
    {

    	$http = eZHTTPTool::instance();
    	$additionalAttributes = array();
    	
   	  	$postingObject = PostingObject::instance($this->object->ID);	    	
    	if($postingObject->type == 'draft' )
		{
			$relatdObjs = $http->sessionVariable("classified_obj_related");    	    	    	
		    if(!is_array($relatdObjs))
		    {
		    	return false;	
		    }	       
	  		if(!is_array($relatdObjs[$this->object->ID]))
	    	{
	    		return false;	    		
	    	}	    	    	
	    	if(!is_array($relatdObjs[$this->object->ID][$this->classIdentifier]))
	    	{
	    		return false;	    		
	    	}	    	    	    	    	    
	    	foreach($relatdObjs[$this->object->ID][$this->classIdentifier] as $objectId)
	    	{	    			    	
	    		$tempObj = eZContentObject::fetch($objectId);
	    		if($tempObj instanceof eZContentObject)
	    		{
	    			$additionalAttributes[$objectId] = $tempObj->dataMap();
	    		}   
	    	}  
		}
		else
		{
		    $mainNode = eZContentObjectTreeNode::fetch($postingObject->mainNodeId());
   			$childrenNodes = $mainNode->subTree( array( 'Depth' => 1, 'ClassFilterType' => 'include', 'ClassFilterArray' => array($this->classIdentifier) ));
   			foreach($childrenNodes as $childrenNode)
   			{
   				$childrenObjectId = $childrenNode->object()->ID;
   				$additionalAttributes[$childrenObjectId] = $childrenNode->object()->dataMap();
   			}
		}    
    	return $additionalAttributes;
    }
    
  	static function addChildrenObjectsLocation($objectId, $newParentNode)
    {    	
   		$ini = eZINI::instance('apllistings.ini');	
		$childrenObjectIdentifiers =  array_keys($ini->variable('Settings','ChildrenObjectsTemplateMap'));  
    	$obj = eZContentObject::fetch($objectId);
    	$attributes = $obj->dataMap();	    	    
    	$nodes = $obj->assignedNodes();
    	foreach($nodes as $node)
    	{
    		if($node->ParentNodeID == $newParentNode->NodeID)
    		{
    			$mainNode = $obj->mainNode();
    			$childrenImageNodes = $mainNode->subTree( array( 'Depth' => 1, 'ClassFilterType' => 'include', 'ClassFilterArray' => $childrenObjectIdentifiers ));    			
    			foreach($childrenImageNodes as $imageNode)
    			{    		
    				ApleZTools::AddNodeAssignment($imageNode->NodeID, $imageNode->object()->ID, array($node->NodeID));	
    			}    		
    		}    		
    	}
    }         		   
}

?>