<?php

class EmailNotifications{

	public function EmailNotifications(){
	}

	static function purchasedClassifiedNotification($orderId) 
	{	
		if(! (eZOrder::fetch($orderId) instanceof eZOrder) )
			return false;
		$user = eZUser::currentUser();				
		$template_file = 'notification/purchaseclassified.tpl';
		$sender = "noreply@vanguardia.com.mx";
		$receiver = $user->Email;	
		$set_tpl_variables = self::getOrderInformation($orderId);		
		$main_params = AplManageMail::fetchMailTemplate($template_file, $set_tpl_variables);
		$main_params['content_type'] = 'text/html';	
		$main_params['subject'] = "Anuncio pagado en Vanguardia Clasificados";
		$main_params['receiver'] = $receiver;	
		$main_params['email_sender'] = $sender;
		return AplManageMail::sendMail($main_params);				
	}
	
	static function getOrderInformation($orderId)
	{		
		$orderManager = new ProductOrderManager($orderId);
		//get price data
		$products = $orderManager->order->productCollection()->itemList();
		$obj_id =$products[0]->ContentObjectID;
		$packageId = AplClassifiedMetadata::getPackage($obj_id)->ID;
		$metadataHandler = new AplClassifiedMetadata($obj_id, $packageId);
		$priceData = $metadataHandler->buildPrice(false);
		// Order response data
		$orderResponseData = $orderManager->getOrderResponseData();
		// Account info
		$accountInfo = $orderManager->accountInformation();
		// get url alias
		$obj = eZContentObject::fetch($obj_id);			
		$attributes = $obj->dataMap();
		$nodepath = "";
		$publishLocationNode = $attributes['category']->value()->mainNode();	
		$publishLocationPath = $publishLocationNode->PathIdentificationString;
		$nodes = eZContentObject::fetch($obj_id)->assignedNodes();
		foreach($nodes as $node)
		{
			$currentpath = $node->PathIdentificationString;
			$pos = strpos($currentpath, $publishLocationPath);	
			if($pos !== false)
			{
				$nodepath = $node->urlAlias();
			}
		}	
		// fill return array
		$notificationData['order'] =  $orderManager->order;
		$notificationData['status'] = 'success';
		$notificationData['price_data'] = $priceData;
		$notificationData['transaction_response'] = $orderResponseData;
		$notificationData['account_information'] = $accountInfo;
		$notificationData['url'] = $nodepath;
		return $notificationData;		
	}

	static function removeClassifiedPublishNotification($notificationData) 
	{	
		$template_file = 'notification/removeclassified.tpl';
		$sender = $notificationData['user_email'];
		$receiver = "info@clasificados.vanguardia.com.mx";		
		$set_tpl_variables = array('notification_data' => $notificationData);
		$main_params = AplManageMail::fetchMailTemplate($template_file, $set_tpl_variables);
		$main_params['content_type'] = 'text/html';	
		$main_params['receiver'] = $receiver;	
		$main_params['email_sender'] = $sender;
		return AplManageMail::sendMail($main_params);				
	}
	
}

?>