<?php

$Module = array( 'name' => 'myprofile', 'variable_params' => false );

$ViewList = array();	

$ViewList['dashboard'] = array('functions' => array( 'dashboard' ),
					    	   'script' => 'dashboard.php',
							   'params' => array() );

$ViewList['listings'] = array(  'functions' => array( 'dashboard' ),
                                'script' => 'listings.php',
                                'ui_component' => 'myprofile',
                                'default_navigation_part' => 'ezmyprofilenavigationpart',
                                'params' => array('StateID'),
                                'unordered_params' => array( 'offset' => 'offset' ) );	
	
$ViewList['orders'] = array(  'functions' => array( 'dashboard' ),
                                'script' => 'orders.php',
                                'ui_component' => 'myprofile',
                                'default_navigation_part' => 'ezmyprofilenavigationpart',
                                'params' => array(),
                                'unordered_params' => array( 'offset' => 'offset' ) );							
																												
$SiteAccess = array('name'=> 'SiteAccess',
					'values'=> array(),
					'path' => 'classes/',
					'file' => 'ezsiteaccess.php',
					'class' => 'eZSiteAccess',
					'function' => 'siteAccessList',
					'parameter' => array());	

$FunctionList['dashboard'] = array( 'SiteAccess' => $SiteAccess );

?>