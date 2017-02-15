<?php

$tpl = eZTemplate::factory();

$apllistingsIni = eZINI::instance('apllistings.ini');	
$groupIdentifier = $apllistingsIni->variable('General','WorkStatesGroup');;
$group = eZContentObjectStateGroup::fetchByIdentifier( $groupIdentifier );

$tpl->setVariable( 'group', $group );

$Result = array();
$Result['content'] = $tpl->fetch( 'design:myprofile/dashboard.tpl' );
$Result['content'] = $tpl->fetch( "design:myprofile/dashboard.tpl" );
$Result['path'] = array ( array ('url' => 'myprofile/dashboard', 'text' => "My Account") );

?>