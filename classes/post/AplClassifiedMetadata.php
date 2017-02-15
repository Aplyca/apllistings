<?php
class AplClassifiedMetadata
{
	
	var $classifiedObject;
	var $package;
	var $packageData;
	var $selectedImproves;
		
	function __construct($objectId, $packageId, $packageData=null, $selectedImproves=null) 
    {    
    	$this->classifiedObject = eZContentObject::fetch($objectId);   
    	$this->package = eZContentObject::fetch($packageId);
    	$this->packageData = $packageData;
    	$this->selectedImproves = $selectedImproves;     
    }   
    
    
    static function instance($objectId, $packageId, $rawImprovesSelected=null)
    {
    	$classifiedObject = eZContentObject::fetch($objectId);   
    	$package = eZContentObject::fetch($packageId);    	
    	if(! $classifiedObject instanceof eZContentObject)
    		return null;
		if(! $package instanceof eZContentObject)
    		return null;    		
    	if($rawImprovesSelected == null)
    	{
    		return new AplClassifiedMetadata($objectId, $packageId);
    	}	

		$promotion = Promotion::instance($packageId);
		if($promotion)
		{
			return new AplClassifiedMetadata($objectId, $packageId, $rawImprovesSelected);	
		}
		else
		{
			$packagesData = self::getPackagesData($objectId);    
	    	if(! isset ($packagesData[$packageId]))
	    		return false;    		    	   		
	    	// TODO  Fix improves feature according to new package features mechanism
    		//$selectedImproves = self::filterValidImproveSelection($rawImprovesSelected, $packagesData[$packageId]['features']);
    		$selectedImproves = array();	    	
	    	return new AplClassifiedMetadata($objectId, $packageId, $packagesData[$packageId], $selectedImproves);	
		}    	    	    	    	    	    	    	    		 
    }
    
    static function getPackageFromID($packageID)
    {
    	if(!$packageID)
		{
			return false;
		}
    	$package = eZContentObject::fetch($packageID);	
    	if($package instanceof eZContentObject)
    	{
    		if($package->ClassIdentifier == 'package' || $package->ClassIdentifier == 'promotion')
    		{
    			return $package;
    		}	
    		else return false;
    	}
    	else return false;
    }
    
    static function filterValidImproveSelection($rawImprovesSelected, $packageImproves)
    {    	        	
	    $improveData = array();
	    $improveString = array();
		foreach($rawImprovesSelected as $key => $improve)
		{			
			if(!self::isValidImprove($improve,$packageImproves))
				continue;
			if($improve['checked']=='on')
			{		
				if(array_key_exists('quantity',$improve)) 
				{			
					$iv = new eZIntegerValidator(1, 100);													
					if($iv->validate($improve['quantity']) == eZInputValidator::STATE_ACCEPTED)
					{
					 
						$improveData[$key]= array('identifier' => $key, 'id' =>  $improve['id'], 'price' => $packageImproves[$key]['price'] , 'quantity' => $improve['quantity'], 'has_quantity' => $packageImproves[$key]['has_quantity'],   'description' => 'empty');
						
					}				
				}	
				else
				{
					//TODO check with the packageimprove if it requires quantity					
					$improveData[$key]= array('identifier' => $key, 'id' =>  $improve['id'], 'price' => $packageImproves[$key]['price'] , 'quantity' => $improve['quantity'], 'has_quantity' => $packageImproves[$key]['has_quantity'],   'description' => 'empty');
					
				}				
			}
		}
		return $improveData;
    }
    
    static function isValidImprove($improve, $packageImproves)
    {
    	if(!is_array($packageImproves))
    		return false;
    	foreach($packageImproves as $packageImprove)
    	{
    		if($packageImprove['id'] == $improve['id'])
    			return true;
    	}
    	return false;
    }
    
    
	static function getPackagesData($objectId)
    {
    	// fetch object
    	$object = eZContentObject::fetch($objectId);    
    	return self::getPackagesDataByClass($object->ClassIdentifier);		
    }
    
    static function getPackagesDataByClass($classIdentifier)
    {
    	$ini = eZINI::instance('apllistings.ini');	
		$packagesSubtreeID =  $ini->variable('Settings','PackagesSubtreeID');
		
		$filter = array('package/related_listings', '=', $classIdentifier);
    	
    	$result = eZContentFunctionCollection::fetchObjectTree( $packagesSubtreeID, 
												                array( 'priority', false ), 
												                false, 
												                false, 
												                0, 
												                100, 
												                0, 
												                '=', 
												                false, 
												                false, 
												                false, 
												                'include', 
												                array( 'package' ), 
												                false, 
												                false, 
												                false, 
												                false, 
												                true, 
												                false);
    	
    	
    	
		$packages = array();
		// get related packages trough eZ find search
    	//$ezFindSearch = new ezfModuleFunctionCollection();    	
		//$result = $ezFindSearch->search( '', 0, 100, null, $filter, null, eZContentClass::classIDByIdentifier('package') ); // query, offset, limit, facets, filters, sortby, $classid				
		return self::packagesResultToArray($result['result']);// build package data array
    }
    
    static function packagesResultToArray($searchResult)
    {
    	foreach($searchResult as $item)
		{				
			$packageObject = $item->ContentObject;
			$packageData = self::packageToArray($packageObject);
			$packageData['priority'] = $item->Priority;		
			$packages[$packageData['id']] = $packageData;			 			
		}
		return $packages;
    }
    
    static function packageToArray($packageObject)
    {
    	$packageData = array();
		$packageData['name'] = $packageObject->Name;		
		$packageData['id'] = $packageObject->ID;			
		$packageAttributes = $packageObject->dataMap();			
		$packageData['price'] = $packageAttributes['price']->DataFloat;
		$packageData['duration'] = $packageAttributes['duration']->DataInt;
		$packageData['description'] = $packageAttributes['description']->DataText;
		
		if($packageObject->ClassIdentifier == 'promotion')
		{
			$promotion = Promotion::instance($packageObject->ID);
			$relatedPackage = $promotion->relatedPackage(); 
			$relatedPackageAttributes = $relatedPackage->dataMap();
			$packageData['content_restrictions'] = self::featureMatrixToArray($relatedPackageAttributes['content_restrictions']);
			$packageData['available_features'] = self::featureMatrixToArray($relatedPackageAttributes['available_features']);		
			$specialFeaturesRelation = $relatedPackageAttributes['special_features']->content();					
		}
		else
		{
			$packageData['content_restrictions'] = self::featureMatrixToArray($packageAttributes['content_restrictions']);
			$packageData['available_features'] = self::featureMatrixToArray($packageAttributes['available_features']);		
			$specialFeaturesRelation = $packageAttributes['special_features']->content(); 	
		}								
		if(sizeof($specialFeaturesRelation['relation_list']) > 0)
		{
			$packageData['features'] = array();
			foreach($specialFeaturesRelation['relation_list'] as $sfRelation)
			{
				$sf = eZContentObject::fetch($sfRelation['contentobject_id']);													
				$sfAttributes = $sf->dataMap();
				$sfData = array();
				$sfData['id'] = $sf->ID;
				$sfData['title'] = $sfAttributes['title']->DataText;
				$sfData['identifier'] = $sfAttributes['identifier']->DataText;
				$sfData['price'] = $sfAttributes['price']->DataInt;
				$sfData['has_quantity'] = $sfAttributes['quantity']->DataInt;
				$packageData['features'][$sfData['identifier']] = $sfData;
			}				
		}
		return $packageData;
    }
    
    function store()
    {
    	$this->storePublicationData();
    	$this->storePrice();
    }
    
	function storePublicationData()
	{
		
		$startDateIdentifier = "start_date";
		$endDateIdentifier = "end_date";
		$startDate = new eZDate();		
		$startDate->adjustDate(0,1,0);						
    	$additionalDays = 0;    		    	    	    
    	$endDate = $this->getEndDate($startDate, $additionalDays); 	
    	AplContentHandler::setAttributeContentFromString($startDateIdentifier, $this->classifiedObject->ID, $startDate->timeStamp());
		AplContentHandler::setAttributeContentFromString($endDateIdentifier, $this->classifiedObject->ID, $endDate->timeStamp());
	}
	
	function getPublicationData()
	{
		$startDate = new eZDate();
		$startDate->adjustDate(0,1,0);
    	$additionalDays = 0;    		    	    	    
    	$endDate = $this->getEndDate($startDate, $additionalDays); 	
    	return array('startDate' => $startDate->timeStamp (), 'endDate' => $startDate->timeStamp () );
	}
	
	
	

    function storePrice()
    {   
    	$totalPrice = $this->getTotalPrice(); 	    	
    	$priceIdentifier = "listing_price";
    	$improvesIdentifier = "improves";
    	$attrs = $this->classifiedObject->dataMap();
		$priceAttr = $attrs[$priceIdentifier]->value();
		$vatType = $priceAttr->attribute( 'selected_vat_type' );
    	$vatId = $vatType->attribute( 'id' );
    	$vatIncluded = 1;
    	$priceString = $totalPrice . "|" . $vatId . "|" . $vatIncluded;
   		AplContentHandler::setAttributeContentFromString($priceIdentifier, $this->classifiedObject->ID, $priceString);    	
		$serializedImproves = $this->serializeImproves();		
		if(array_key_exists($improvesIdentifier, $attrs))
		{
			if($attrs[$improvesIdentifier] instanceof eZContentObjectAttribute)
			{
				$attrs[$improvesIdentifier]->fromString($serializedImproves);
				$attrs[$improvesIdentifier]->store();	
			}	
		}				   		
    }
    
    function serializeImproves()
    {    	
    	$improveString = array();
    	foreach($this->selectedImproves as $key => $improve)
    	{
    			$improveString[]= $key . "|" . $improve['id'] . "|" . $improve['quantity'] . "|" . "n/a" ;
    	}
    	return implode("&", $improveString); 
    }
	
	function getTotalPrice()
	{
		$totalPrice = 0;
		$basePrice = $this->packageData['price'];
		$totalPrice +=  $basePrice;
		foreach($this->selectedImproves as $improve)
		{
			if($improve['has_quantity'])
			{
				$totalPrice += $improve['quantity'] * $improve['price'];
			}
			else
			{
				$totalPrice += $improve['price'];
			}
		}
		return $totalPrice;
	}
	
	
	function getEndDate($startDate, $additionalDays)
    {
    	$baseDays = $this->packageData['duration'];
    	$endDateTimestamp =   $startDate->timeStamp() +  ( $baseDays * 86400 ) +   ( $additionalDays * 86400 );    	
    	$endDate = new eZDate();
    	$endDate->setTimeStamp($endDateTimestamp);
    	return $endDate;	
    }	

    function savePackageDateData($params)
    {   
    	$check = $this->checkParams($params);
    	if( ! $check )
    		return false;    	
		$package_identifier = "packages";
    	AplContentHandler::setAttributeContentFromString($package_identifier, $this->classifiedId, $this->packageId);    	      
    }	
	

    static function buildDate($rawDate)    
    {
    	$rawDateArray = explode("/",$rawDate);
    	$date = eZDate::create($rawDateArray[1],$rawDateArray[0],$rawDateArray[2]);
    	
    	if($date->isValid())
    	{
    		return $date;
    	}
    	else
    	{
    		return false;
    	}				
    }    
    
    static function updatePriceFromCurrentPackage($object)
    {
    	$dataMap = $object->dataMap();
    	$package = $dataMap['package']->content();
    	$packageInfo = $package->dataMap();
    	$packagePrice = $packageInfo['price']->DataFloat; 
    	$dataMap['listing_price']->fromString($packageInfo['price']->toString());
    	$dataMap['listing_price']->store();
    }
    
    
 	static function updatePriceFromInput($object, $price)
    {
    	if(!is_numeric($price))
    		return false;
    	$dataMap = $object->dataMap();
		$priceAttr = $dataMap['listing_price']->value();
		$vatType = $priceAttr->attribute( 'selected_vat_type' );
    	$vatId = $vatType->attribute( 'id' );
    	$vatIncluded = 1;
    	$priceString = $price . "|" . $vatId . "|" . $vatIncluded;	    		    	    	
    	$dataMap['listing_price']->fromString($priceString);
    	$dataMap['listing_price']->store();    	
    	return true;
    }
        
    
    
    function getAdditionalImagesCount($isDraft = true)
    {
    	$imagesNode = null;
    	if($isDraft)
    	{
    		
    		$imagesCacheNodeId = PostingOperations::getImagesCacheNodeId();
    		$imagesNode = eZContentObjectTreeNode::fetch($imagesCacheNodeId);
    	}
    	else
    	{
    		$imagesNode = $this->classifiedObject->mainNode(); 		
    	}
    		
		if (is_object($imagesNode))
		{
			$childrenNodes = $imagesNode->subTree( array( 'Depth' => 1, 'ClassFilterType' => 'include', 'ClassFilterArray' => array('image') ));
			return count($childrenNodes);
		}
    	return 0;
    }
    
    static function featureMatrixToArray($eZMatrix)
    {
    	$serializedMatrix = $eZMatrix->toString();
    	if ($serializedMatrix == "")
    		return null;
		$tempArray = explode('&', $serializedMatrix);
		//print_r($tempArray); die();			
		$data = array();
		foreach($tempArray as $item)
		{
			$tempArrayContent = explode('|', $item);
			$data[$tempArrayContent[0]] = $tempArrayContent[1]; 
		}
		return $data;
    }
    


 
}

?>