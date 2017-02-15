<?php

class AdminNotification{

	public function AdminNotification(){
	}

	static function fetchObjects( $parentNodeID, $class_identifier, $attribute_filter )
	{	
		
		$functionCollection = new eZContentFunctionCollection();
		
		/*
			fetchObjectTree( $parentNodeID, $sortBy, $onlyTranslated, $language, $offset, $limit, $depth, $depthOperator,
			$classID, $attribute_filter, $extended_attribute_filter, $class_filter_type, $class_filter_array,
			$groupBy, $mainNodeOnly, $ignoreVisibility, $limitation, $asObject, $objectNameFilter, $loadDataMap = true )
		*/
				
		$result=$functionCollection -> fetchObjectTree( $parentNodeID, 
														false,
														false,
														false,
														0,
														false,
														0,
														'gt',
														false,
														$attribute_filter,
														false,
														'include',
														array($class_identifier),
														false,
														false,
														true,
														false,
														true,
														false,
														true);
														
		return $result[ 'result' ];
		
	}
	
}

?>