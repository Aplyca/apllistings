<?php

$module = $Params[ 'Module' ];

require_once( "kernel/common/template.php" );

$http = eZHTTPTool::instance();
$obj_id = $Params['objectID'];

if (empty($obj_id))
	$obj_id = $http->sessionVariable('classified_obj_id');

$object = eZContentObject::fetch($obj_id);

$node = $object->mainNode();

$tpl = templateInit();
$tpl->setVariable( 'node', $node );
$tpl->setVariable( 'preview', false );
$html = $tpl->fetch( 'design:post/preview.tpl' );

echo $html;

$Result = array();
$Result['pagelayout'] = false;
eZDebug::updateSettings(array("debug-enabled" => false, "debug-by-ip" => false));

?>