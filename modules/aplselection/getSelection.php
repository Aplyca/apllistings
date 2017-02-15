<?php

include_once('kernel/content/ezcontentfunctioncollection.php');

$http = eZHttpTool::instance();

extract( $_GET);

	$encoded_parameters = $http -> postVariable( 'fetch_params' );
	$parameters_array = json_decode( $encoded_parameters );

	$functionCollection = new eZContentFunctionCollection();
	$categories_string = '[';



	/*
	fetchObjectTree( $parentNodeID, $sortBy, $onlyTranslated, $language, $offset, $limit, $depth, $depthOperator,
	$classID, $attribute_filter, $extended_attribute_filter, $class_filter_type, $class_filter_array,
	$groupBy, $mainNodeOnly, $ignoreVisibility, $limitation, $asObject, $objectNameFilter, $loadDataMap = true )
	*/
	$parent_node_id = 2;
	$limit = 200;
	$language = false;
	$class_filter_type = false;
	$secondlevelclass_filter_type = false;
	$class_filter_array = false;
	$secondlevelclass_filter_array =false;
	if( $p_node_id)
	{
		$parent_node_id = $p_node_id;
	}
	if( $lang)
	{
		$language = $lang;
	}
	if( $lim)
	{
		$limit = $lim;
	}
	if( $filter_type)
	{
		$class_filter_type = $filter_type;
	}
	if( $filter_array)
	{
		$class_filter_array = $filter_array;
	}
	if( $sfilter_type)
	{
		$secondlevelclass_filter_type = $sfilter_type;
	}
	if( $sfilter_array)
	{
		$secondlevelclass_filter_array = $sfilter_array;
	}
	
	
	$first_level = $functionCollection -> fetchObjectTree( $parent_node_id, 
												false,
												false,
												$language,
												0,
												false,
												0,
												'gt',
												false,
												false,
												false,
												$class_filter_type,
												array( $class_filter_array ),
												false,
												false,
												false,
												false,
												true,
												false,
												true );

	///print_r( $first_level['result']);
	//die();
	foreach ($first_level['result'] as $first_level_result){
		$second_level = $functionCollection -> fetchObjectTree( $first_level_result->NodeID, 
												false,
												false,
												$language,
												0,
												false,
												0,
												'gt',
												false,
												false,
												false,
												$secondlevelclass_filter_type,
												array( $secondlevelclass_filter_array ),
												false,
												false,
												false,
												false,
												true,
												false,
												true );
		
		switch( $controller )
		{
			case 'node_id':	
				$controller_string = $first_level_result->NodeID;
				$categories_string.= '{"When":"'.$controller_string.'","Value":"","Text":"Todas"},';
			break;	
			case 'class_id':				
				$data_map = $first_level_result -> dataMap ();				
				$controller_string = $data_map[ 'id_class' ] -> toString();
			break;
		}
		
		
		foreach ($second_level[result] as $second_level_result){
		
			$brand_name=str_replace( "'","","$second_level_result->Name" );
			$categories_string.= '{"When":"'.$controller_string.'","Value":"'.$second_level_result->NodeID.'","Text":"'.$brand_name.'"},';
		}
	}
	$categories_string.= ']';
	echo $categories_string;

$Result = array();
$Result['pagelayout'] = false;
eZDB::checkTransactionCounter();
eZExecution::cleanExit(); 



?>