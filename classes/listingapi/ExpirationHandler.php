<?php

class ExpirationHandler
{

	var $object;
	var $currentState;
	var $currentTime;
	var $startDate;
	var $endDate;
	var $daysFromStart;
	var $daysForExpire;
	var $order;
	var $package;
	
	protected function __construct($object)
	{
		$this->object = $object;
		$this->currentState = self::getState();
		$this->currentTime = time();
		$this->startDate = self::getStartDate();
		$this->endDate = self::getEndDate();
		$this->daysFromStart = self::getDaysFromStart();
		$this->daysForExpire = self::getDaysForExpire();
		$this->order = self::getRelatedOrder();
		$this->package = self::getRelatedPackage();
	}
	
	static function instance($objectID)
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
		return new ExpirationHandler($object);		
	}
	
	private function getState()
	{
		return $this->object->stateIdentifierArray();
		
	}
	
	private function getStartDate()
	{
		$dataMap = $this->object->dataMap();
		return $dataMap['start_date']->DataInt;
		
	}
	
	private function getEndDate()
	{
		$dataMap = $this->object->dataMap();
		return $dataMap['end_date']->DataInt;
		
	}
	
	private function getDaysFromStart()
	{
		$difference = $this->currentTime - $this->startDate;
		$result = floor($difference / 86400);
		return $result;
		
	}	
	
	private function getDaysForExpire()
	{
		$difference = $this->endDate - $this->currentTime;
		$result = floor($difference / 86400);
		return $result;
	}
	
	private function getRelatedOrder()
	{
		$query = "SELECT ezorder.* 
				  FROM ezorder, ezproductcollection_item 
				  WHERE ezorder.id = ezproductcollection_item.id  
				  AND ezproductcollection_item.contentobject_id = " .$this->object->ID . " ORDER BY ezorder.created DESC ";
		
		$db = eZDB::instance();
 		$orders = $db->arrayQuery( $query);
 		
 		$order = false;
 		if (count($orders) > 0)
 		{
 			$order = new eZOrder( $orders[0] );
 		}
 		
 		return $order;
 		
 		$db->close();
	}
	
	private function getRelatedPackage()
	{
		$dataMap = $this->object->dataMap();
		return $dataMap['package']->content();
		
	}
				
}

?>