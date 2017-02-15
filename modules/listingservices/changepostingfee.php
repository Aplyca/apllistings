<?php

$tpl = eZTemplate::factory();
$module = $Params['Module'];
$objectId = $Params['ObjectID'];



$postingObject = PostingObject::instance($objectId);

if(!$postingObject)
{
	return $module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );		
}

$object = eZContentObject::fetch($objectId);

/*
print_r($module->currentAction()); 
print_r($_POST);
die();
*/

if($module->isCurrentAction('ChangeFee') )
{	
	$updateListingResult = AplClassifiedMetadata::updatePriceFromInput($object, $_POST['NewFee']);
	if($updateListingResult)
	{	
		$orders = ProductOrderManager::getRelatedOrders($objectId);	
		$currentOrder = reset($orders);
		
		$items = $currentOrder->productItems();
		$productItem = $items[0]['item_object'];
		$productItem->setAttribute('price', $_POST['NewFee']);
		$productItem->store();
		$tpl->setVariable( 'fee_updated', 1);
	}
	else
	{
		$tpl->setVariable( 'invalid', 1);
	}
	//$orderManager = new ProductOrderManager($currentOrder->ID);

}

$tpl->setVariable( 'object', $object);
//$tpl->setVariable( 'orders', $orders);
$Result['content'] = $tpl->fetch("design:listingservices/changepostingfee.tpl");
$Result['path'] = array( array('url' => 'listingservices/changepostingfee', 'text' => 'Change hotel price'));

?>


