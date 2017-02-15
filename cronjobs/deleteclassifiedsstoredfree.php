<?php

//Login as admin User
$import_user_name ='admin';
$import_user = eZUser::fetchByName( $import_user_name );
if (is_object($import_user))
{
	$adminUserContentObjectID = $import_user->attribute( 'contentobject_id' );
	eZUser::setCurrentlyLoggedInUser( $import_user, $adminUserContentObjectID );
     
	$managerpublish_ini = eZINI::instance('managerpublish.ini');
	$current_state = $managerpublish_ini -> variable( 'DeleteAnnouncements', 'CurrentStateFree' );
	$ini_class_identifiers = $managerpublish_ini -> variable( 'DeleteAnnouncements', 'ClassIdentifier' );
	$ini_attributes = $managerpublish_ini -> variable( 'DeleteAnnouncements', 'Attribute' );	
	$expiration_days = $managerpublish_ini -> variable( 'DeleteAnnouncements', 'ExpireDays' );
	$timestampt_expiration_days = date(strtotime("-$expiration_days days"));
	$total_info_log = array();	
	$info_log = array();	 
	
	foreach ($ini_class_identifiers as $class_identifier => $parentNodeID)
	{
		$ini_packages = $managerpublish_ini -> variable( 'PackagesAnnouncements', 'Package-free' );
		$state = array('state', '=', $current_state);
		$start_date = array($class_identifier . '/start_date', '<=', $timestampt_expiration_days);	
		$packages = array($class_identifier . '/packages', 'in', $ini_packages);
		$attribute_filter =array( $state, $start_date, $packages);
		$announcements = ManagerPublish::fetchObjects( $parentNodeID, $class_identifier, $attribute_filter );
		$info_log = ManagerPublish::deleteObjects( $announcements, $ini_attributes );
		$total_info_log = array_merge($total_info_log, $info_log);		
	}
	
	ManagerPublish::printLog( $total_info_log );
}
else
{
	$cli->output( "User doesn't exist" );
}

?>
