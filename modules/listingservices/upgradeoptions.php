<?php

$tpl = eZTemplate::factory();
$module = $Params['Module'];
$objectId = $Params['ObjectID'];

$listingUpgrader = ListingUpgrader::instance($objectId);

if(!$listingUpgrader)
{
	return $module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );		
}


$upgradesInfo = $listingUpgrader->getUpgradesInfo();


$tpl->setVariable( 'remain_days', $listingUpgrader->remainDays );
$tpl->setVariable( 'current_package', $listingUpgrader->currentPackage );
$tpl->setVariable( 'upgrade_packages', $upgradesInfo );
$tpl->setVariable( 'object_id', $objectId );
$Result['content'] = $tpl->fetch("design:listingservices/upgradeoptions.tpl");
$Result['path'] = array( array('url' => 'listingservices/upgradeoptions/'.$objectId, 'text' => 'Upgrade your hotel'));

//$packageList = $packagesFolder->subTree( array( 'Depth' => 1, 'ClassFilterType' => 'include', 'ClassFilterArray' => array('package'), 'SortBy' => array('priority', false) ));

?>
