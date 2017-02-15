<?php
class ListingUpgrader
{
	
	var $currentPackage;
	var $upgradePackages;
	var $postingObject;
	var $object;
	var $remainDays;
	
	private function __construct($postingObject, $object, $remainDays, $currentPackage, $upgradePackages)
	{		
		$this->postingObject = $postingObject;	
		$this->object = $object;	
		$this->remainDays = $remainDays;	
		$this->currentPackage = $currentPackage;	
		$this->upgradePackages = $upgradePackages;	
	}
				
	static function instance($objectID)
	{		
		$postingObject = PostingObject::instance($objectID);
		if(!$postingObject)
		{
			return false;		
		}
		$expirationHandler = ExpirationHandler::instance($objectID);
		if(!$expirationHandler)
		{
			return false;		
		}		
		$remainDays = $expirationHandler->daysForExpire;	
		$ini = eZINI::instance('apllistings.ini');	
		$listingClass =  $ini->variable('Settings','ListingClassIdentifier');
		$packagesData = AplClassifiedMetadata::getPackagesDataByClass($listingClass);
		$package = $postingObject->getPackage();
		if(! $package instanceof eZContentObject)
		{
			return false;
		}
		
		$object = eZContentObject::fetch($objectID);
		
		$packageNode = $package->mainNode();
		$priority = $packageNode->Priority;
		
		$upgradePackages = array();
		$upgradePackages['upgrade'] = array();
		
		foreach($packagesData as $packageData)
		{
			if($packageData['priority'] >= $priority)
			{
				if($packageData['priority'] == $priority)
				{
					if($packageData['id'] == $package->ID)
					{
						$upgradePackages['current'] = $packageData;	
					}					
				}
				else
				{
					$upgradePackages['upgrade'][$packageData['id']] = $packageData;
				}
			}
		}			
		if(! isset($upgradePackages['current']) )
		{
			return false;
		}
		return new ListingUpgrader($postingObject, $object, $remainDays, $upgradePackages['current'], $upgradePackages['upgrade']);		
	}
	
	public function getUpgradesInfo()
	{
		$upgradeInfo = array();
		foreach($this->upgradePackages as $key => $upgradePackage)
		{
			$upgradeInfo[$key] = $upgradePackage;
			$upgradeInfo[$key]['upgrade_price'] =	$this->calculateUpgrade($upgradePackage['id']);
		}
		return $upgradeInfo;
		
	}
	
	public function calculateUpgrade($upgradePackageID)
	{
		$currentPackageDayPrice = $this->currentPackage['price'] / $this->currentPackage['duration'];
		$upgradePackageDayPrice = $this->upgradePackages[$upgradePackageID]['price'] / $this->upgradePackages[$upgradePackageID]['duration'];							
		$creditBalance =  $currentPackageDayPrice * $this->remainDays;			
		$upgradeDaysPrice = $upgradePackageDayPrice * $this->remainDays;				
		$upgradePrice = $upgradeDaysPrice - $creditBalance;
		return (int) $upgradePrice;
	}
	
	public function upgrade($upgradePackageID)
	{			
		$upgradeCalculation = $this->calculateUpgrade($upgradePackageID);		
		$this->postingObject->modifyPackage($upgradePackageID);
		$this->createUpgradeOrder($upgradePackageID, $upgradeCalculation); 
		$this->object = eZContentObject::fetch($this->object->ID); // updating object
		$object=$this->object;
		AplClassifiedMetadata::updatePriceFromCurrentPackage($this->object);
		$this->createUpgradeOrder($upgradePackageID, $upgradeCalculation); 	
		$this->sendUpgradeNotification($upgradePackageID,$upgradeCalculation,$object);
				
	}
	
	private function createUpgradeOrder($upgradePackageID, $upgradeCalculation)
	{
		$user = eZUser::currentUser();
		$currentPackageName = $this->currentPackage['name'];
		$upgradePackageName = $this->upgradePackages[$upgradePackageID]['name'];					
		$upgradeName = $currentPackageName . ' to ' . $upgradePackageName . ' for ' . $this->remainDays . ' days';		
		$product = ProductOrderManager::productArrayFromObject($this->object, 1, $upgradeCalculation);		
		$orderManager = new ProductOrderManager($user->id(), array($product), 'Manual Billing', HapdWorkflow::PENDING);
		$orderManager->setAdditionalInformation('responseData', array('response' => 'SUCCESS'));	
			
		$username  = $user->Login;
		$dateObj = new eZDateTime();
		$date = $dateObj->toString();
		$commentData = array("comment" => array('user' => $username, 'date' => $date, 'comment_text' => $upgradeName));
		$orderManager->setAdditionalInformation('comments', $commentData, false);	
		
		$orderManager->activate();
		return $orderManager;
	}
	
	private function sendUpgradeNotification($upgradePackageID, $upgradeCalculation, $object)
	{
		
		$currentPackageName = $this->currentPackage['name'];
		$upgradePackageName = $this->upgradePackages[$upgradePackageID]['name'];
		$remainDays = $this->remainDays;
		
		$user = eZUser::currentUser();
		$userData = eZUser::currentUser()->contentObject()->dataMap();
		$notificationData['object'] = $object;
		$notificationData['user'] = array('firstName' => $userData['first_name']->content(), 
									  'last_name' => $userData['last_name']->content(), 
									  'email' => $user->Email,
		                              'phone_number' => $userData['personal_phone_number']->content(),
		 							  'address' => $userData['personal_address_number']->content(),
		                              'street_name' => $userData['personal_street_name']->content(),
									  'city' => $userData['personal_city']->content(),
		                              'id' => $user->ContentObjectID,

									  );

		$notificationData['upgradeCalculation'] = $upgradeCalculation;		
		$notificationData['currentPackageName'] = $currentPackageName;	
		$notificationData['upgradePackageName'] = $upgradePackageName;
		$notificationData['remainDays'] = $remainDays;					  
		AplManageMail::sendNotification('upgrade_notification',$notificationData);					
		
		
	}
	
      
}

?>
