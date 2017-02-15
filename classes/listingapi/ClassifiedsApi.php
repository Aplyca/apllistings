<?php

class ClassifiedsApi{

	public function ClassifiedsApi(){
	}

	static function fetchNodes( $parentNodeID, $class_identifier, $attribute_filter, $limit )
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
														$limit,
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

	static function getContentAttributes( $nodes, $attributes, $attributes_filter )
	{	
		
		foreach ($nodes as $node)
		{	
			$attribute_filter_array = array();
			$attribute_array = array();
			$dataMap = $node->dataMap();
			$contentObjectID = $node->ContentObjectID;
						
			if($dataMap[$attributes_filter]->content())
			{			
				$attribute_filter_array[] = $contentObjectID; 
							
				foreach ($attributes as $attribute)
				{
					$content_attribute = $dataMap[$attribute];				
					
					if (is_object($content_attribute))
					{
						$attribute_filter_array[] = ClassifiedsApi::getValueClassified( $content_attribute );
					}		
				}	
			
				$classifieds_filter_array[] = implode('|', $attribute_filter_array);
			
			}
			else
			{		
				$attribute_array[] = $contentObjectID; 
							
				foreach ($attributes as $attribute)
				{
					$content_attribute = $dataMap[$attribute];				
					
					if (is_object($content_attribute))
					{
						$attribute_array[] = ClassifiedsApi::getValueClassified( $content_attribute );
					}		
				}	
			
				$classifieds_array[] = implode('|', $attribute_array);
				
			}
													
		}	
		
		return $classifieds_filter_array + $classifieds_array;
		
	}
	
	static function getValueClassified( $content_attribute )
	{	
		switch ($content_attribute->dataType()->DataTypeString) 
		{						
			case 'ezstring':
				$attribute_value = $content_attribute->content(); 
			break;					
			case 'ezinteger':
				$attribute_value = $content_attribute->content(); 
			break;
			case 'ezobjectrelation':
				$attribute_value = $content_attribute->content()->Name;
			break;
			case 'ezimage':
				if ($content_attribute->hasContent()){	
					$property_image = $content_attribute->content()->imageAlias('small');
					$attribute_value = $property_image['url'];			
				} 
				else{
					$attribute_value = '';	
				} 							
			break;
			default:
				$attribute_value = $content_attribute->toString(); 
			break;
		}			
		
		return $attribute_value;
		
	}

	static function getXml( $classifieds, $classified_type, $attributes )
	{	
		
		/*
		<?xml version="1.0" ?>
		<VehicleAnnouncement>
			<Announcement>
				<id>****</id>
				<title>****</title>
				<price>****</price>
				<year>****</year>
				<km>****</km>
				<location>****</location>
				<advertisers_comments>****</advertisers_comments>
				<main_image>****</main_image>
			</Announcement>
		</VehicleAnnouncement>
		*/
		
		
		$dom = new DomDocument('1.0'); 
		$announcements = $dom->appendChild($dom->createElement($classified_type));
		
		foreach ($classifieds as $classified)
		{
			$value_announcement = explode('|', $classified);
			$announcement = $announcements->appendChild($dom->createElement('Announcement')); 	
			
			foreach ($attributes as $index => $attribute)
			{				
				$field = $announcement->appendChild($dom->createElement($attribute)); 
				$field->appendChild($dom->createTextNode($value_announcement[$index])); 
			}
		}	
				
		$dom->formatOutput = true; 
		$xml_string = $dom->saveXML();
		
		return $xml_string;
		
	}
	
}

?>