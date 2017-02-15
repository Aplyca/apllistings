<?php
//current date
$current_day = date('d');
$current_month = date('m');
$current_year = date('Y');
//$next_date = date( mktime(0, 0, 0, $current_month, $current_day + 1, $current_year)); 
$next_date = time() + 86400;


//Login as admin User
$import_user_name ='admin';
$import_user = eZUser::fetchByName( $import_user_name );
if (is_object($import_user))
{
	$adminUserContentObjectID = $import_user->attribute( 'contentobject_id' );
	eZUser::setCurrentlyLoggedInUser( $import_user, $adminUserContentObjectID );
     
	$adminnotification_ini = eZINI::instance('adminnotification.ini');	
	$ini_class_identifiers = $adminnotification_ini -> variable( 'Announcements', 'ClassIdentifier' );
	$ini_from = $adminnotification_ini -> variable( 'EmailContent', 'From' );
	$ini_subject = $adminnotification_ini -> variable( 'EmailContent', 'Subject' );
	//$ini_addresslistcc = $adminnotification_ini -> variable( 'EmailContent', 'AddressListCC' );
	//$ini_addresslistbcc = $adminnotification_ini -> variable( 'EmailContent', 'AddressListBCC' );
	$ini_content_type = $adminnotification_ini -> variable( 'EmailContent', 'ContentType' );
	
	foreach ($ini_class_identifiers as $class_identifier => $parentNodeID)
	{		
		$ini_packages = $adminnotification_ini -> variable( 'PackagesAnnouncements', 'Package-'.$class_identifier );	
		$ini_state = $adminnotification_ini -> variable( 'Announcements', 'State' );
		$state = array('state', '=', $ini_state);		
		$packages = array($class_identifier . '/packages', 'in', $ini_packages);	
		$web_end_date = array($class_identifier . '/web_end_date', '<=', $next_date);
		$attribute_filter =array( $state, $packages, $web_end_date);
		$nodes = AdminNotification::fetchObjects( $parentNodeID, $class_identifier, $attribute_filter );
		foreach ($nodes as $node)
		{	
			$template_file = 'notification/expire.tpl';			
			$set_tpl_variables = array('node' => $node); 			 
			$main_params = AplManageMail::fetchMailTemplate($template_file, $set_tpl_variables);								
		//	$main_params['subject'] = $ini_subject;
		//	$main_params['from'] = $ini_from;
			//$main_params['addresslistcc'] = $ini_addresslistcc;
			//$main_params['addresslistbcc'] = $ini_addresslistbcc;
			$main_params['content_type'] = $ini_content_type;
			$result = AplManageMail::sendMail($main_params);			
		}
	}
	
	
	
}
else
{
	$cli->output( "User doesn't exist" );
}

?>
