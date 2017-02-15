<?php

require_once( "kernel/common/template.php" );

$module = $Params['Module'];
$http = eZHTTPTool::instance();
$tpl = templateInit();
$in_classified_type = $Params['ClassifiedType'];
$in_limit = $Params['Limit'];
$classifieds_api_ini = eZINI::instance('classifiedsapi.ini');
$allow_classes = $classifieds_api_ini->variable('ClassifiedsApi','Classes');

if (strlen($in_limit) <= 2){
	foreach ($allow_classes as $key => $allow_class_identifier)
	{	
		if ($key == $in_classified_type){
			$class_identifier = $allow_class_identifier;	
		}
	}
}

if($class_identifier){
	$parents_nodes_id = $classifieds_api_ini->variable('ClassifiedsApi','ParentNodeID');
	$parent_node_id = $parents_nodes_id[$class_identifier];	
	$tpl->setVariable( 'page_limit', $in_limit);
	$tpl->setVariable( 'subtree_array', $parent_node_id);	
	$Result = array();
	$Result['content'] = $tpl->fetch( "design:classifieds_api/featured.tpl" );
	$Result['path'] = array ( array ('url' => 'payment/confirm', 'text' => "full") );
}

$Result['pagelayout'] = false;
eZDB::checkTransactionCounter();

?>