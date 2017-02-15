<?php
class Promotion
{
	
	var $object;
	
	private function __construct($postingObject)
	{		
		$this->object = $postingObject;			
	}

	static function instance($objectID)
	{
		$postingObject = self::checkPromotion($objectID);				
		if($postingObject)
		{	
			return new Promotion($postingObject);
		}
		else return false;		
	}
	
 
	// 	user has edit permisions, user is owner, status is stored, only one node if stored, main location under user
	static function checkPromotion($objectID)
	{		
		$postingObject = eZContentObject::fetch($objectID);				
		
		if(! $postingObject instanceof eZContentObject )
		{
			return false;
		}
		if($postingObject->ClassIdentifier != 'promotion')
		{
			return false;
		}		
		return $postingObject;	
	}	
	
	public function relatedPackage()
	{
		$dataMap = $this->object->dataMap();
		return $dataMap['related_package']->content();				
	}
	
	public function promotionData()
	{
		$promotionData = array();
		$dataMap = $this->object->dataMap();
		$promotionData['name'] = $dataMap['title']->DataText;
		$promotionData['id'] = $this->object->ID;		
		$promotionData['duration'] = $dataMap['duration']->DataInt;
		$promotionData['price'] = $dataMap['price']->DataFloat;
		$promotionData['description'] = $dataMap['description']->DataText;
		$promotionData['related_package'] = $this->relatedPackage();				
		return $promotionData;
	}
	
	public function relatedPackageData()
	{
		$relatedPackage = $this->relatedPackage();
		$dataMap = $relatedPackage->dataMap();
		$relatedPackageData['name'] = $dataMap['title']->DataText;
		$relatedPackageData['id'] = $relatedPackage->ID;		
		$relatedPackageData['duration'] = $dataMap['duration']->DataInt;
		$relatedPackageData['price'] = $dataMap['price']->DataFloat;
		$relatedPackageData['description'] = $dataMap['description']->DataText;
		return $relatedPackageData; 					
	}
	
	public function isExpired()
	{
		$dataMap = $this->object->dataMap();
		$validUntil = $dataMap['valid_until']->DataInt;
		return  ($validUntil < time())?1:0;				
	}
	
	
    
   
}

?>