<?php

require_once( "kernel/common/template.php" );

$module = $Params['Module'];
$http = eZHTTPTool::instance();
$tpl = templateInit();

$Result['content'] = $tpl->fetch( 'design:classifiedexport/exportcsvprint.tpl' );

?>