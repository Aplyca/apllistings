<?php 

$Module = array('name' => 'listingservices', 'variable_params' => true);

$ViewList = array();

$ViewList['managelisting']= array(  'functions' => array( 'managelisting' ),
									'script' => 'managelisting.php',
   		  						    'params' => array('ObjectID') );


$ViewList['listingfetcher']= array('functions' => array( 'listingfetcher' ),
							        'script' => 'listingfetcher.php',
									'single_post_actions' => array( 'search' => 'Search'),
									'unordered_params' => array('offset' => 'offset'),
									'post_action_parameters' => array( 'Search' => array('CreationType' => 'CreationType',
																		'ListingStatus' => 'ListingStatus', 	
																		'ExpirationFrom' => 'ExpirationFrom', 	
																		'ExpirationTo' => 'ExpirationTo', 
																		'Package' => 'Package', 
																		'Offset' => 'Offset', 
																		'SortBy' => 'SortBy')),
   		  						    'params' => array('') );

$ViewList['browse']= array( 'functions' => array( 'browse' ),
									'script' => 'browse.php',
   		  						    'params' => array('') );






$SiteAccess = array('name'=> 'SiteAccess',
					'values'=> array(),
					'path' => 'classes/',
					'file' => 'ezsiteaccess.php',
					'class' => 'eZSiteAccess',
					'function' => 'siteAccessList',
					'parameter' => array());	

$FunctionList['managelisting'] = array( 'SiteAccess' => $SiteAccess );
$FunctionList['browse'] = array( 'SiteAccess' => $SiteAccess );
$FunctionList['listingfetcher'] = array( 'SiteAccess' => $SiteAccess );