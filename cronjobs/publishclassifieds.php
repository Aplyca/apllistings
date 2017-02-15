<?php
//current time
$current_time = time();

//Login as admin User
$import_user_name ='admin';
$import_user = eZUser::fetchByName( $import_user_name );
if (is_object($import_user))
{
	$adminUserContentObjectID = $import_user->attribute( 'contentobject_id' );
	eZUser::setCurrentlyLoggedInUser( $import_user, $adminUserContentObjectID );
     
	$managerpublish_ini = eZINI::instance('managerpublish.ini');
	$current_state = $managerpublish_ini -> variable( 'PublishAnnouncements', 'CurrentState' );
	$new_state = $managerpublish_ini -> variable( 'PublishAnnouncements', 'NewState' );
	$ini_class_identifiers = $managerpublish_ini -> variable( 'PublishAnnouncements', 'ClassIdentifier' );
	$ini_attributes = $managerpublish_ini -> variable( 'PublishAnnouncements', 'Attribute' );	
	$ini_publish = $managerpublish_ini -> variable( 'PublishAnnouncements', 'ValPublish' );	
	$ini_section = $managerpublish_ini -> variable( 'PublishAnnouncements', 'SelectedSectionId' );	
	$total_info_log = array();	
	$info_log = array();	
	
	foreach ($ini_class_identifiers as $class_identifier => $parentNodeID)
	{		
		$ini_packages = $managerpublish_ini -> variable( 'PackagesAnnouncements', 'Package-'.$class_identifier );
		$state = array('state', '=', $current_state);
		$start_date = array($class_identifier . '/start_date', '<=', $current_time);	
		$web_end_date = array($class_identifier . '/web_end_date', '>', $current_time);			
		$packages = array($class_identifier . '/packages', 'in', $ini_packages);
		$attribute_filter =array( $state, $start_date, $web_end_date, $packages);
		$announcements = ManagerPublish::fetchObjects( $parentNodeID, $class_identifier, $attribute_filter );
		$info_log = ManagerPublish::publishObjects( $announcements, $ini_attributes, $new_state, $ini_publish, $ini_section );
		$total_info_log = array_merge($total_info_log, $info_log);
	}
	
	ManagerPublish::printLog( $total_info_log );
}
else
{
	$cli->output( "User doesn't exist" );
}

?>
