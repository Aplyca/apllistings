<?php
include_once( 'lib/ezutils/classes/ezhttptool.php');

$module = $Params['Module'];
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
	$parent_node_id = $classifieds_api_ini->variable('ClassifiedsApi','ParentNodeID');
	$state = $classifieds_api_ini->variable('ClassifiedsApi','State');
	$attribute_filter = array('and',array('state',"=",$state));	;
	$attributes = $classifieds_api_ini->variable('Attributes-'.$class_identifier,'AttributeIdentifier');
	$attributes_filter = $classifieds_api_ini->variable('AttributesFilter-'.$class_identifier,'AttributeFilterIdentifier');
	$nodes = ClassifiedsApi::fetchNodes($parent_node_id[$class_identifier], $class_identifier, $attribute_filter, $in_limit);
	$classifieds = ClassifiedsApi::getContentAttributes( $nodes, $attributes, $attributes_filter );	
	$xml = ClassifiedsApi::getXml( $classifieds, $in_classified_type, $attributes );

	echo $xml;	
}

$Result['pagelayout'] = false;
eZDB::checkTransactionCounter();
eZExecution::cleanExit(); 

?>