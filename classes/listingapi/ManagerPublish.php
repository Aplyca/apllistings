<?php

class ManagerPublish
{

	public function ManagerPublish(){
	}

	static function fetchObjects( $parentNodeID, $classIdentifier, $attributeFilter )
	{	
		
		$functionCollection = new eZContentFunctionCollection();
		
		/*
			fetchObjectTree( $parentNodeID, $sortBy, $onlyTranslated, $language, $offset, $limit, $depth, $depthOperator,
			$classID, $attributeFilter, $extended_attribute_filter, $class_filter_type, $class_filter_array,
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
														$attributeFilter,
														false,
														'include',
														array($classIdentifier),
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