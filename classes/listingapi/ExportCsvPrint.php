<?php

class ExportCsvPrint{

	public function ExportCsvPrint(){
	}

	static function fetchObjects( $parentNodeID, $class_identifier, $attribute_filter ){	
		
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
	
	static function getClissifiedsCsv ( $nodes, $fields, $exportcsv_ini, $attribute_validater )
	{
		
		$array_strings = array();
		$csv_array = array(implode('|',$fields));
		$img_array = array();
		$timestamp = time();		
		$contentObjectFree = eZINI::instance('createclassifeds.ini');	
		$objectIDFree = $contentObjectFree->variable('ContentObject','PackageFree');
		
		foreach ($nodes as $node)
		{				
			$class_identifier = $node->ClassIdentifier;
			$field_to_identifier = $exportcsv_ini->variable('FieldMapping-'.$class_identifier,'FieldToIdentifier');					
			
			$attributes_identifiers = array();
			
			foreach ($fields as $field) 
			{
				$attributes_identifiers[$field]=$field_to_identifier[$field];
			}
						
			$csv_attribute_array = array();
			$img_attribute_array = array();
			$dataMap = $node->dataMap();
			$start_date = $dataMap[$attribute_validater['start_date']]->DataInt;
			$end_date = $dataMap[$attribute_validater['print_end_date']]->DataInt;
			$packages = $dataMap[$attribute_validater['packages']];
			
			if ($packages->DataInt)
			{	
				$packages_id = $packages->content()->ID;
				if ($packages_id <> $objectIDFree['ObjectID'])
				{				
					if ($timestamp >= $start_date && $timestamp <= $end_date)
					{	
						foreach ($attributes_identifiers as $attribute_identifier)
						{	
										
							$content_attribute = $dataMap[$attribute_identifier];
							
							if (is_object($content_attribute))
							{								
								switch ($content_attribute->dataType()->DataTypeString) 
								{
									case 'ezimage':
										if ($content_attribute->hasContent()){							
											$csv_attribute_array[] = 'Tiene foto';
											$property_image = $content_attribute->content()->imageAlias('small');
											$img_attribute_array[] = $property_image['url'];
										} 
										else{
											$csv_attribute_array[] = 'Sin foto';	
										} 
									break;
									case 'ezobjectrelation':
										$csv_attribute_array[] = utf8_encode($content_attribute->content()->Name); 
									break;
									case 'eztext':
										if ($content_attribute->ContentClassAttributeIdentifier == 'specifications' || $content_attribute->ContentClassAttributeIdentifier == 'advertisers_comments' || $content_attribute->ContentClassAttributeIdentifier == 'description' ){
											$charactersToReplace = array("\r\n", "\n", "\r");
											$charactersToAdd = " ";
											if ($dataMap['title']->hasContent()){
												$csv_attribute_array[] = str_replace($charactersToReplace, $charactersToAdd, $dataMap['title']->content().', '.$content_attribute->content().' contacto: '.$dataMap['contact_phone']->content()); 
											}
											else
												$csv_attribute_array[] = $content_attribute->content(); 
										}
										else
											$csv_attribute_array[] = $content_attribute->content(); 
									break;
									case 'ezboolean':
										if ($content_attribute->ContentClassAttributeIdentifier == 'printed_framed'){													
											if ($content_attribute->Value()){
												$csv_attribute_array[] = 'Tiene marco';	
											} 
											else{
												$csv_attribute_array[] = 'Sin marco';	
											} 
										}
										elseif ($content_attribute->ContentClassAttributeIdentifier == 'bold_framed'){													
											if ($content_attribute->Value()){
												$csv_attribute_array[] = 'Tiene negrita';	
											} 
											else{
												$csv_attribute_array[] = 'Sin negrita';	
											} 
										}
									break;
									default:
										$csv_attribute_array[] = $content_attribute->toString(); 
									break;
								}
							}
							elseif ($attribute_identifier == 'ContentObjectID')
							{
								$csv_attribute_array[] = $node->ContentObjectID;
								$img_attribute_array[] = $node->ContentObjectID; 
							}
							elseif ($attribute_identifier == 'ClassName')
							{
								$csv_attribute_array[] = $node->ClassName;
							}
							else
							{
								$csv_attribute_array[] = '';	
							}
							
						}	
								
						$csv_array[] = implode('|', $csv_attribute_array);							
						$img_array[] = implode('|', $img_attribute_array);
						
					}
				
				}
				
			}
										
		}	
		
		$csv_string = implode("\n", $csv_array);	
		$array_strings['csv_string']= $csv_string;
		$array_strings['img_array']= $img_array;
		
		return $array_strings;
		
	}
	
	static function downloadCsv ( $file, $csv_string )
	{
		header( 'X-Powered-By: eZ Publish' );
		header( 'Content-Type: ' . 'application/csv' );
		header( "Pragma: " );
		header( "Cache-Control: " );
		header( "Expires: ". gmdate('D, d M Y H:i:s', time() + 600) . ' GMT' );
		header( 'Content-Disposition: attachment; filename=' . $file );
		header( 'Content-Transfer-Encoding: text' );
		header( 'Accept-Ranges: bytes' );

		echo "$csv_string";

		eZExecution::cleanExit();
	}
	
	static function downloadZip ( $file, $csv_string, $img_array)
	{
		$zip = new ZipArchive();
		$filepath = "extension/classifieds/var/files";
		$filename = "download".time().".zip";
		if ($zip->open($filepath.'/'.$filename, ZIPARCHIVE::CREATE)!==TRUE) {
			exit("cannot open <$filename>\n");
		}
		$zip->addFromString($file, $csv_string);
		if(count($img_array) > 0)
		{
			foreach($img_array as $item_img){
				$img = explode('|', $item_img);
				$ext = explode('.', $img[1]);
				$zip->addFile($img[1], $img[0].'.'.$ext[1]);
			}		
		}		
		$zip->close();
		
		$file = $filepath.'/'.$filename;  
		header("Content-Description: File Transfer");  
		header('Content-Type: application/zip');
		header('Content-Transfer-Encoding: binary'); 
		header("Content-Length: ".filesize($file));
		header("Content-Disposition: attachment; filename=\"".basename($file)."\"");
		readfile($file);
		
	}
		
}

?>