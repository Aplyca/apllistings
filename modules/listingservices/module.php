<?php 

$Module = array('name' => 'listingservices', 'variable_params' => true);

$ViewList = array();

$ViewList['promotion']= array('script' => 'promotion.php',
   		  						    'params' => array('PromotionID') );

$ViewList['upgradeoptions']= array('script' => 'upgradeoptions.php',
   		  						    'params' => array('ObjectID') );

$ViewList['upgradeconfirm']= array('script' => 'upgradeconfirm.php',
   		  						    'params' => array('ObjectID', 'PackageID') );

$ViewList['changepostingfee']= array('script' => 'changepostingfee.php',
									'single_post_actions' => array('ChangeFee' => 'ChangeFee'),
   		  						    'params' => array('ObjectID') );



$SiteAccess = array('name'=> 'SiteAccess',
					'values'=> array(),
					'path' => 'classes/',
					'file' => 'ezsiteaccess.php',
					'class' => 'eZSiteAccess',
					'function' => 'siteAccessList',
					'parameter' => array());	

$FunctionList['promotion'] = array( 'SiteAccess' => $SiteAccess );
$FunctionList['upgradeoptions'] = array( 'SiteAccess' => $SiteAccess );
$FunctionList['upgradeconfirm'] = array( 'SiteAccess' => $SiteAccess );
$FunctionList['changepostingfee'] = array( 'SiteAccess' => $SiteAccess );