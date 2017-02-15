<?php

$tpl = eZTemplate::factory();
$module = $Params['Module'];
$http = eZHTTPTool::instance();
$promotionID = $Params['PromotionID'];




$promotion = Promotion::instance($promotionID);
if(! $promotion)
{
	return $module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );		
}
else
{
	if($promotion->isExpired())
		return $module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );
}

$http = eZHTTPTool::instance();
$http->setSessionVariable("promotion_enabled", $promotionID);

//setcookie ("promotion_enabled", $PromotionID, time() + 3600);
//print_r($_COOKIE); die();
$module->redirectTo('post/listingplan/0/' . $promotionID);
$Result['pagelayout'] = false;
eZDebug::updateSettings(array("debug-enabled" => false, "debug-by-ip" => false));

?>
