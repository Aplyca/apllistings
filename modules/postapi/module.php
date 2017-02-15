<?php

$Module = array( 'name' => 'classifiedsapi', 'variable_params' => false );

$ViewList = array();	

$ViewList['getclassifiedsall'] = array('functions' => array( 'getclassifiedsall' ),
					    	        'script' => 'getclassifiedsall.php',
							        'params' => array('ClassifiedType', 'Limit') );	

$ViewList['getclassifiedsfeatures'] = array('functions' => array( 'getclassifiedsfeatures' ),
					    	        'script' => 'getclassifiedsfeatures.php',
							        'params' => array('ClassifiedType', 'Limit') );	

$ViewList['getclassifieds'] = array('functions' => array( 'getclassifieds' ),
					    	        'script' => 'getclassifieds.php',
							        'params' => array('ClassifiedType', 'Limit') );	

$ViewList[ 'editpostattributes' ] = array('functions' => array( 'editpostattributes' ),
										 'script' => 'editpostattributes.php',
										 'params' => array(),
										 'single_post_actions' => array( 'EditAttributesButton' => 'EditAttributes'),
							    		 'post_action_parameters' => array( 'EditAttributes' =>  array('ObjectID' => 'ObjectID',
																									  'Attributes' => 'Attributes')));
																												
$SiteAccess = array('name'=> 'SiteAccess',
					'values'=> array(),
					'path' => 'classes/',
					'file' => 'ezsiteaccess.php',
					'class' => 'eZSiteAccess',
					'function' => 'siteAccessList',
					'parameter' => array());	

$FunctionList['getclassifiedsall'] = array( 'SiteAccess' => $SiteAccess );
$FunctionList['getclassifiedsfeatures'] = array( 'SiteAccess' => $SiteAccess );
$FunctionList['getclassifieds'] = array( 'SiteAccess' => $SiteAccess );
$FunctionList['editpostattributes'] = array( 'SiteAccess' => $SiteAccess );

?>