<?php

require_once( "kernel/common/template.php" );

$module = $Params['Module'];
$http = eZHTTPTool::instance();
$tpl = templateInit();

$Result = array();
$Result['content'] = $tpl->fetch( "design:classifieds_api/featured_all.tpl" );
$Result['path'] = array ( array ('url' => 'payment/confirm', 'text' => "full") );


$Result['pagelayout'] = false;
eZDB::checkTransactionCounter();

?>