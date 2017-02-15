<?php

$offset = $Params['offset'];

$tpl = eZTemplate::factory();
$tpl->setVariable( 'view_parameters', array( 'offset' => $offset ));

$Result = array();
$Result['content'] = $tpl->fetch( "design:myprofile/orders.tpl" );
$Result['path'] = array ( array ('url' => 'myprofile/orders', 'text' => "full") );

$Result['pagelayout'] = false;
eZDebug::updateSettings(array("debug-enabled" => false, "debug-by-ip" => false));
$response['m']=$Result['content'];
$response['s']='s';
print_r(json_encode($response));
eZDB::checkTransactionCounter();
eZExecution::cleanExit();

?>