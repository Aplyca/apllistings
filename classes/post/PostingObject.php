<?php
class PostingObject
{
	
	const STORED = 3;
	const PUBLISHED = 4;
	const SUSPENDED = 5;
	const DRAFT = 6;
	
	const SECTION_STANDARD = 1;
	const SECTION_USERS = 2;
	
	private $object;
	var $type;
	var $state;
	var $classIdentifier;
	
	private function __construct($object)
	{		
		$this->object = $object;	
		$objectVersion = $object->currentVersion();
		if($objectVersion->attribute('status') == eZContentObjectVersion::STATUS_DRAFT)
		{
			$this->type = 'draft';	
		}
		else
		{
			$this->type = 'saved';
		}
		$stateArray = $object->stateIDArray();
		$stateID = $stateArray[3];
		$state = eZContentObjectState::fetchById($stateID);
		$this->state = $state->Identifier;
		$this->classIdentifier = $object->contentClassIdentifier();
	}
	
	public function id()
	{
		return $this->object->ID;
	}
	
	public function mainNodeId()
	{
		if($this->type == 'saved')
			return $this->object->mainNodeId();
		else return false;
	}
	
	public function state()
	{
		return $this->state;
	}
			
	static function instance($objectID)
	{
		$object = self::checkObject($objectID);
		if($object)
		{
			return new PostingObject($object);
		}
		else return false;		
	}
	
    static function instanceNew($listingClass)
    {
    	$hapd_ini = eZINI::instance('apllistings.ini');		
		$parentUserNodeID = $hapd_ini -> variable('UserSettings', 'DefaultUserPlacement');
		
		$user = eZUser::currentUser();
		$userContentObject = $user->contentObject();
		
		//Validation for supporting to sub-users
		if ( $userContentObject->mainParentNodeID() == $parentUserNodeID )
		{		
	    	$section = 2; // user section
			$object = AplContentHandler::createContentObjectDraft($listingClass, $section);
			self::setState($object, 'draft');
			PostingWorkflow::registerObjectId($object->ID);	
			return new PostingObject($object);
		}
		else
			return false;
    }	
	
	
	// 	user has edit permisions, user is owner, status is stored, only one node if stored, main location under user
	static function checkObject($objectID)
	{			
		$ini = eZINI::instance('apllistings.ini');	
		$classIdentifier =  $ini->variable('Settings','ListingClassIdentifier');		
		$object = eZContentObject::fetch($objectID);
		if(! $object instanceof eZContentObject )
		{
			return false;
		}
		if($object->ClassIdentifier != $classIdentifier)
		{
			return false;
		}		
		$objectVersion = $object->currentVersion();
		if($objectVersion->attribute('status') == eZContentObjectVersion::STATUS_DRAFT)
		{
			if($object->OwnerID != eZUser::currentUserID())
			{
				return false;
			}
		}
		else
		{
			if( !$object->canRead() || !$object->canEdit() )
				return false;		
		}
		if(!self::checkState($object))
		{
			return false;
		}
		return $object;
	}	
	
	public function setPackage($package)
	{			
		AplContentHandler::setAttributeContentFromString('package', $this->object->ID, $package->ID);
		$adMetadata = AplClassifiedMetadata::instance($this->object->ID, $package->ID, AplClassifiedMetadata::packageToArray($package));			
		if($adMetadata instanceof AplClassifiedMetadata)
		{
			return true;			
		}
		else
			return false;
	}
	
	

	public function getPackage()
	{
		$dataMap = $this->object->dataMap();
		$package = eZContentObject::fetch($dataMap['package']->content()->ID);
		return $package;
	}
	
	
	static function checkState($object)
	{
		$userNodeId = eZUser::currentUser()->contentObject()->mainNodeID();
		$roleLists = eZUser::currentUser()->roleIDList();
		$states  = $object->stateIdentifierArray();		
		
		if(in_array('listings/stored', $states) || in_array('listings/suspended', $states))
		{
			if ( count($object->assignedNodes()) == 1 && in_array(2,$roleLists) )
			{ 
				return true;
			}
			//elseif( count($object->assignedNodes()) == 1 &&  $object->mainParentNodeID() == $userNodeId)
			elseif( count($object->assignedNodes()) == 1 )
			{
				return true;
			}	
		}
		elseif(in_array('listings/draft', $states))
		{
			$objectVersion = $object->currentVersion();
			if($objectVersion->attribute('status') == eZContentObjectVersion::STATUS_DRAFT)
			{
				return true;
			}
		}
		elseif(in_array('listings/published', $states))
		{	
			if ( count($object->assignedNodes()) == 2 && in_array(2,$roleLists) )
			{ 
				return true;
			}
			//elseif( count($object->assignedNodes()) == 2 &&  $object->mainParentNodeID() == $userNodeId )
			elseif( count($object->assignedNodes()) == 2 )
			{			
				return true;
			}			
		}
		else return false;
	}
	
	
    private function remove()
    {
		//AplContentHandler::removeDraftContentObject($obj_id);    	  
    }
    
    public function republishRelatedObjects($identifier)
    {
    	if($this->type == 'saved')
    	{
    	    $mainNode = eZContentObjectTreeNode::fetch($this->mainNodeId());
	   		$childrenNodes = $mainNode->subTree( array( 'Depth' => 1, 'ClassFilterType' => 'include', 'ClassFilterArray' => array($identifier) ));
	   		foreach($childrenNodes as $childrenNode)
	   		{
	   			$childrenObjectId = $childrenNode->object()->ID;
	   			$versionNum = $childrenNode->object()->currentVersion()->attribute( 'version' );	
	   			eZOperationHandler::execute('content','publish',array('object_id' => $childrenObjectId, 'version'=>$versionNum));
	   		}
    	}	    	   
    }
    
    public function getImagesNodeId()
    {
    	if($this->type == 'draft')
    	{
   	   		$user = eZUser::currentUser();
	    	$userNode = $user->contentObject()->mainNode();
	    	$childrenImageNodes = $userNode->subTree( array( 'Depth' => 1, 'ClassFilterType' => 'include', 'ClassFilterArray' => array('folder') ));
	    	if(!empty($childrenImageNodes))
			{
				return $childrenImageNodes[0]->NodeID;	
			}    	
			else 
			{
				return $this->createImagesCacheNode();
			}	
    	}
    	else
    	{
    		return $this->object->mainNodeID();
    	}
    }	    
    
	public function resetImagesNode()
    {
    	if($this->type == 'draft')
    	{
  	   		$user = eZUser::currentUser();
	    	$userNode = $user->contentObject()->mainNode();
	    	$childrenImageNodes = $userNode->subTree( array( 'Depth' => 1, 'ClassFilterType' => 'include', 'ClassFilterArray' => array('folder') ));
	    	if(!empty($childrenImageNodes))
			{
				$cacheNodeId = $childrenImageNodes[0]->NodeID;	
				AplContentHandler::deleteChildren($cacheNodeId); 
				return true;
			}  
    	}
    	return false;
	}   
	
    static function createImagesCacheNode()
    {
		$user = eZUser::currentUser();
		$userNode = $user->contentObject()->mainNode();
		$userNodeId = $user->contentObject()->mainNodeId();
		$childrenImageNodes = $userNode->subTree( array( 'Depth' => 1, 'ClassFilterType' => 'include', 'ClassFilterArray' => array('folder') ));		
		if(empty($childrenImageNodes))
		{
			$folder_attributes = array('name'=>'Images Cache');
			$params = array();
			$params['creator_id'] = $user->id();
			$params['parent_node_id'] = $userNodeId;
			$params['class_identifier'] = 'folder';
			$params['attributes'] = $folder_attributes;		
			$object = eZContentFunctions::createAndPublishObject( $params );
    		if($object instanceof eZContentObject)
    		{
    			return $object->mainNodeID();
    		} 
    		else return false;						
		}	
		else 
		{
			return $childrenImageNodes[0]->NodeID;	
		}			    		
    }	
       
    public function save()
    {       		 			
		if($this->type == 'draft')
		{				
			$user = eZUser::currentUser();
			$userNode = $user->contentObject()->mainNode();
			$this->setDefaultAttributesValue();
			if(! AplContentHandler::publishDraft($this->object->ID, $userNode))
			{
				return false;
			}
			$this->moveImagesToAnnouncement();
			ChildrenObjectManager::setChildrenObjects($this->object->ID);		
			self::setState($this->object, 'stored');		
			$this->type = 'saved';	
			$this->state = 'stored';	
		}				
		return true;
				 
    }    
    
	public function publish()
    {     	    	
    	if($this->type == 'saved' && ( $this->state == 'stored' || $this->state == 'suspended' ) )
		{
		    $areaNode = self::getPublicationNode($this->object);		    	    	    
		    if($areaNode instanceof eZContentObjectTreeNode)
		    {		    	
		    	$this->setPublicationMetadata();		   		
				self::setState($this->object, 'published');						    	
		    	AplContentHandler::assignSection(1, $this->object, true); // omit assign sections permisions with 3rd parameter		    	
				/* Single way for adding location									    
		    	$publishLocationNodeId = $areaNode->NodeID;
		    	AplContentHandler::addNodeAssignment($this->object->mainNodeId(), $this->id(), array($publishLocationNodeId), true); */
		    	$publicationNode = $this->addPublishLocation($this->object->mainNodeId(), $this->object, $areaNode, true);
		    	/* We don't need secondary locations for listing childrens, they are fetched from the main node 
		    		self::addImagesLocation($this->id(), $publicationNode);	    		
		    		ChildrenObjectManager::addChildrenObjectsLocation($this->id(), $publicationNode);		    	 
		    	*/
		    	$this->state = 'published';	    	
		    	return $this->object;	    	
		    } 
		} 
		return false;  	    	    	 	   					
    }
    
    public function simplePublish($object, $location)
    {
        if($object instanceof eZContentObject)
        {
            $apllistingIni = eZINI::Instance('apllistings.ini');            
            $publishState=$apllistingIni -> variable("General", "PublishState");
            $publishSection=$apllistingIni -> variable("General", "PublishSection");
            self::setState($object, $publishState);
            AplContentHandler::assignSection($publishSection, $object, true); 
            AplContentHandler::addNodeAssignment( $object->mainNodeId(), $object -> ID, array($location -> NodeID), true );
            return $object;
        }
            return false;
    }
    
    public function republish()
    {
    	if($this->type != 'draft')
    	{
    		$versionNum = $this->object->currentVersion()->attribute( 'version' );	
			eZOperationHandler::execute('content','publish',array('object_id' => $this->object->ID, 'version'=>$versionNum));	
    	}
		
    }
    
    public function setPublicationMetadata()
    {
   		$attributes = $this->object->dataMap();
   		$package = $attributes['package']->content();
   		$adMetadata = AplClassifiedMetadata::instance($this->object->ID, $package->ID, AplClassifiedMetadata::packageToArray($package));
	   	if($adMetadata instanceof AplClassifiedMetadata)
		{
			$adMetadata->store();			
		}	
    }
    
    
    public function addPublishLocation( $mainNodeID, $object, $areaNode,  $omitPermissions)
    {
    	$attributes = $object->dataMap();
    	$city  = $attributes['city_name']->DataText;
    	$countryContent  = $attributes['country_name']->content();
    	$country = $countryContent['value'][$attributes['country_name']->DataText]['Name']; 
  		$trans = eZCharTransform::instance();	    	
    	$filter = array(array('folder/name', '=', $country));     	
    	$countryID = 'country_' . $country;
   		$cityID = 'city_' . $city;
    	$countryObject = eZContentObject::fetchByRemoteID ($countryID); 
    	$newCountry = false;
		 // Preventing to fetch objects that had been moved to trash, deleting and recreating			 
		if($countryObject instanceof eZContentObject && count($countryObject->assignedNodes()) == 0 &&   $countryObject->Status == 2)
		{
			eZContentObjectOperations::remove( $countryObject->ID, true );
			$countryObject = null;	
		}     	    	   											               
		if(!$countryObject instanceof eZContentObject)
		{
			$attributesData = array (); 
			$attributesData['name'] = $country; 
			$countryObject = AplContentHandler::createAndPublish('folder', eZUser::currentUserID(), $areaNode->NodeID, 1, $attributesData, $countryID);	
			$newCountry = true;
		}
		$countryNode = $countryObject->mainNode();
		if($newCountry)
		{
			$attributesData = array (); 
			$attributesData['name'] = $city; 
			$cityObject = AplContentHandler::createAndPublish('folder', eZUser::currentUserID(), $countryNode->NodeID, 1, $attributesData, $cityID);
			$cityNode = $cityObject->mainNode();
			$newCity = true;
		}
		else
		{
			 $cityObject = eZContentObject::fetchByRemoteID ($cityID);  
			 // Preventing to fetch objects that had been moved to trash, deleting and recreating			 
			 if($cityObject instanceof eZContentObject && count($cityObject->assignedNodes()) == 0 &&   $cityObject->Status == 2)
			 {
			 	  eZContentObjectOperations::remove( $cityObject->ID, true );
			 	  $cityObject = null;	
			 } 
			 if(!$cityObject instanceof eZContentObject)
			 {
				$attributesData = array (); 
				$attributesData['name'] = $city; 
				$cityObject = AplContentHandler::createAndPublish('folder', eZUser::currentUserID(), $countryNode->NodeID, 1, $attributesData, $cityID);
				$newCity = true;
			 }	
			 
			 $cityNode = $cityObject->mainNode();								                								               
		}																		                    	        	      	    	
		AplContentHandler::addNodeAssignment($mainNodeID, $object->ID, array($cityNode->NodeID), $omitPermissions);
		return $cityNode;    	
    }
    


    
    public function moveImagesToAnnouncement()
    {    				
		$imagesCacheNodeId = $this->getImagesNodeId();
		$imagesNode = eZContentObjectTreeNode::fetch($imagesCacheNodeId);
		if(is_object($imagesNode))
		{
			$childrenImageNodes = $imagesNode->subTree( array( 'Depth' => 1, 'ClassFilterType' => 'include', 'ClassFilterArray' => array('image') ));
		}
		$announcementNode = $this->object->mainNode();
		foreach($childrenImageNodes as $imageNode)
		{
			AplContentHandler::moveNode($imageNode, $announcementNode);	
		}				
    }     
    
    static function setState($object, $stateIdentifier)
    {
        $apllistingsIni = eZINI::instance('apllistings.ini');	
        $groupIdentifier = $apllistingsIni->variable('General','WorkStatesGroup');
        $group = eZContentObjectStateGroup::fetchByIdentifier( $groupIdentifier );
    	$state = eZContentObjectState::fetchByIdentifier( $stateIdentifier, $group -> ID );	    	
	    $object->assignState($state);   
	    return true;  
    }        

    static function getPublicationNode($object)
    {    	
		$rootNode = eZContentObjectTreeNode::fetch(2);
		$areaNodes = $rootNode->subTree( array( 'Depth' => 1, 'ClassFilterType' => 'include', 'ClassFilterArray' => array('area') ));
		foreach($areaNodes as $areaNode)
		{
			$areaDataMap = $areaNode->dataMap();
			if($object->ClassID == $areaDataMap['children_class_id']->DataText)
			{
					return $areaNode;
			}
		}	
		return null;
    }
    
    static function addImagesLocation($objectId, $newParentNode)
    {
    	$obj = eZContentObject::fetch($objectId);
    	$attributes = $obj->dataMap();	    	    
    	$nodes = $obj->assignedNodes();
    	foreach($nodes as $node)
    	{
    		if($node->ParentNodeID == $newParentNode->NodeID)
    		{
    			$mainNode = $obj->mainNode();
    			$childrenImageNodes = $mainNode->subTree( array( 'Depth' => 1, 'ClassFilterType' => 'include', 'ClassFilterArray' => array('image') ));    			
    			foreach($childrenImageNodes as $imageNode)
    			{
    		
    				AplContentHandler::addNodeAssignment($imageNode->NodeID, $imageNode->object()->ID, array($node->NodeID));	
    			}    		
    		}    		
    	}
    } 
    
   
                
    // Avoid eZ Bug
    public function setDefaultAttributesValue()
    {    	
    	$dataMap = $this->object->dataMap();
    	$db = eZDB::instance();
        $db->begin();
    	foreach($dataMap as $attr)
    	{
    		if($attr->DataTypeString == 'ezdate')
    		{
    			if($attr->DataInt == '')
    			{    				
					  $attr->fromString(0);
					  $attr->store();  						 		
    			}    				 	
    		}
    		elseif($attr->DataTypeString == 'ezinteger')
    		{
    			if($attr->DataInt == '')
    			{    				
					  $attr->fromString(0);
					  $attr->store();  						 		
    			}    				 	
    		}    		
    		else{}
    	}
    	$db->commit();
    }    
    
    
    public function modifyPackage( $packageID )
    {
    	$package = eZContentObject::fetch($packageID);    	
    	if(! $package instanceof eZContentObject )
		{
			return false;
		}
		if($package->ClassIdentifier != 'package')
		{
			return false;
		}		
		
		$dataMap = $this->object->dataMap();
		$dataMap['package']->fromString($packageID);
		$dataMap['package']->store();    	    	
    }
    
    public function getCurrentPrice()
    {
    	$dataMap = $this->object->dataMap();
    	return $dataMap['listing_price']->DataFloat;
    }
    
    
    public function getPublicNode( )
    {
    	$nodes = $this->object->assignedNodes();
    	
    	foreach ($nodes as $node)
    	{
    		if ( $node->NodeID != $this->object->mainNodeID() )
    		{
    			return $node;
    		}	
    	}
    }
    
    
    public function unpublish( )
    {
   		if($this->state == 'published')
   		{
	    	$node = $this->getPublicNode();    	
	    	$resultState = AplContentHandler::setState( $this->object, self::SUSPENDED );
			$resultLocation = AplContentHandler::removeLocation( $node->NodeID );
			$resultSection = AplContentHandler::assignSection( self::SECTION_USERS, $this->object );
			$this->state == 'suspended';
			return true;   			
   		}	    
   		else return false;				  
    }
    
   
}

?>