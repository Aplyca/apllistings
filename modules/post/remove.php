<?php

require_once( "kernel/common/template.php" );

$module = $Params[ 'Module' ];
$http = eZHTTPTool::instance();

$objectId = $Params['ObjectID'];

eZContentObjectOperations::remove( $objectId,  true );		

$module->redirectTo( '/myprofile/dashboard' );
							
?>