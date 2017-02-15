<?php

$tpl = eZTemplate::factory();
$module = $Params['Module'];
$http = eZHTTPTool::instance();

$parentObjectID = $Params['ObjectID'];
$childrenObjMgr = new ChildrenObjectManager($Params['ClassIdentifier']);
$childrenObjMgr->setRelatedChildren($parentObjectID);



$objectID = $childrenObjMgr->object->ID;

$attributes = $childrenObjMgr->object->dataMap();
$tplPath = $childrenObjMgr->getTemplatePath();

$tpl->setVariable( 'attributes', $attributes);
$tpl->setVariable( 'object_id', $objectID);
$Result['content'] = $tpl->fetch("design:$tplPath");
$Result['path'] = array( array('url' => 'post/createchildrenobject', 'text' => 'Fill your post info'));	

$Result['pagelayout'] = false;
eZDebug::updateSettings(array("debug-enabled" => false, "debug-by-ip" => false));


?>