<?php

$state_id = $Params['StateID'];
$offset = $Params['offset'];

$tpl = eZTemplate::factory();
$tpl->setVariable( 'state_id', $state_id);
$tpl->setVariable( 'view_parameters', array( 'offset' => $offset ));

$Result = array();
$Result['content'] = $tpl->fetch( "design:myprofile/listings.tpl" );
$Result['path'] = array ( array ('url' => 'myprofile/listings', 'text' => "full") );

$Result['pagelayout'] = false;
eZDebug::updateSettings(array("debug-enabled" => false, "debug-by-ip" => false));
$response['m']=$Result['content'];
$response['s']='s';
print_r(json_encode($response));
eZDB::checkTransactionCounter();
eZExecution::cleanExit();

?>