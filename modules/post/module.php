<?php 

$Module = array('name' => 'apllistings', 'variable_params' => true);

$ViewList = array();

$ViewList['listingplan']= array('script' => 'listingplan.php', 'params' => array('ObjectID', 'PackageID'));

$ViewList['type']= array('script' => 'type.php', 'params' => array('type'));
$ViewList['edit']= array('script' => 'edit.php',
						 'single_post_actions' => array('nextstep' => 'NextStep', 'backpackage' => 'BackPackage', 'cancel' => 'Cancel'),							 
						 'params' => array('objectId'));


$ViewList['multiedit']= array('script' => 'multiedit.php',
						 	  'params' => array('ObjectID', 'Action') );

$ViewList['editattributes']= array('script' => 'editattributes.php',
   		  						    'params' => array('Action', 'ObjectID' , 'EditStep') );


$ViewList['createchildrenobject']= array('script' => 'createchildrenobject.php',
   		  						    'params' => array('ObjectID', 'ClassIdentifier') );

$ViewList['deletechildrenobject']= array('script' => 'deletechildrenobject.php',
   		  						    'params' => array('ObjectID') );

$ViewList['promotion']= array('script' => 'promotion.php',
   		  						    'params' => array('PromotionID') );


$ViewList['confirm']= array('script' => 'confirm.php',
							'single_post_actions' => array('purchase' => 'Purchase', 'publish' => 'Publish', 'store' => 'Store'),
 							'params' => array('ObjectID'));

$ViewList[ 'remove' ] = array('functions' => array( 'remove' ),
					    	  'script' => 'remove.php',
							  'params' => array( 'ObjectID' ));	


$ViewList['package']= array('script' => 'package.php',
							'single_post_actions' => array('publish' => 'Publish', 'store' => 'Store', 'cancel' => 'Cancel'),
							'post_action_parameters' => array('NextStep' => array('PackageID' => 'package_id', 'StartDate' => 'start_date')), 
							'params' => array());

$ViewList['getconfirmdata']= array('script' => 'getconfirmdata.php',
							'single_post_actions' => array('purchase' => 'Purchase', 'publish' => 'Publish', 'store' => 'Store', 'edit' => 'Edit', 'backpackage' => 'BackPackage', 'cancel' => 'Cancel'),														
							'params' => array());

$ViewList[ 'preview' ] = array('script' => 'preview.php','params' => array('objectID'));




    
$SiteAccess = array(
    'name'=> 'SiteAccess',
    'values'=> array(),
    'path' => 'classes/',
    'file' => 'ezsiteaccess.php',
    'class' => 'eZSiteAccess',
    'function' => 'siteAccessList',
    'parameter' => array()
    );  


$FunctionList['listingplan'] = array( 'SiteAccess' => $SiteAccess );
$FunctionList['type'] = array( 'SiteAccess' => $SiteAccess );    
$FunctionList['edit'] = array( 'SiteAccess' => $SiteAccess );
$FunctionList['confirm'] = array( 'SiteAccess' => $SiteAccess );
$FunctionList['package'] = array( 'SiteAccess' => $SiteAccess );
$FunctionList['editattributes'] = array( 'SiteAccess' => $SiteAccess );
$FunctionList['createchildrenobject'] = array( 'SiteAccess' => $SiteAccess );
$FunctionList['remove'] = array( 'SiteAccess' => $SiteAccess );

