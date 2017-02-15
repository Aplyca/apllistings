<?php

$tpl = eZTemplate::factory();
$module = $Params['Module'];
$objectId = $Params['ObjectID'];
$packageId = $Params['PackageID'];




$listingUpgrader = ListingUpgrader::instance($objectId);

if(!$listingUpgrader)
{
	return $module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );		
}
$upgradePackages = $listingUpgrader->getUpgradesInfo();
$validPackages = array_keys($upgradePackages);
if(! in_array($packageId, $validPackages))
{
	return $module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );
}

$upgradesInfo = $listingUpgrader->upgrade($packageId); // !!!!  =) 



$Result['content'] = $tpl->fetch("design:listingservices/upgradeconfirm.tpl");
$Result['path'] = array( array('url' => 'listingservices/upgradeoptions/'.$objectId, 'text' => 'Upgrade Options'),array('text' => 'Upgrade Confirmation'));


?>

