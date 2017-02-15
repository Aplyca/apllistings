<?php

$Module = array( 'name' => 'classifiedexport', 'variable_params' => false );

$ViewList = array();	

$ViewList['exportcsvprint'] = array('functions' => array( 'exportcsvprint' ),
					    	        'script' => 'exportcsvprint.php',
							        'params' => array() );

$ViewList['download'] = array(
    'default_navigation_part' => 'ezclassifiedexport',
    'script' => 'download.php',
    'functions' => array( 'download' ),
	'params' => array(),
	'single_post_actions' => array( 'ExportCSVButton' => 'ExportCSV'),
    'post_action_parameters' => array( 'ExportCSV' => array( 'VehicleAnnouncement' => 'VehicleAnnouncement',
														     'RealestateAnnouncement' => 'RealestateAnnouncement',
														     'EmploymentAnnouncement' => 'EmploymentAnnouncement',
														     'GenericAnnouncement' => 'GenericAnnouncement')));									
																												
$SiteAccess = array('name'=> 'SiteAccess',
					'values'=> array(),
					'path' => 'classes/',
					'file' => 'ezsiteaccess.php',
					'class' => 'eZSiteAccess',
					'function' => 'siteAccessList',
					'parameter' => array());	

$FunctionList['exportcsvprint'] = array( 'SiteAccess' => $SiteAccess );
$FunctionList['download'] = array( 'SiteAccess' => $SiteAccess );

?>