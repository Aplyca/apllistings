<?php

require_once( "kernel/common/template.php" );
include_once('lib/ezfile/classes/ezfile.php');
$Module = $Params['Module'];
$http = eZHTTPTool::instance();

if ($Module->isCurrentAction('ExportCSV')) 
{ 
	$exportcsv_ini = eZINI::instance('exportcsv.ini');	
	$allow_classes = $exportcsv_ini->variable('ExportCSV','Classes');
	$ini_class_identifiers = $exportcsv_ini -> variable( 'ExportCSV', 'ClassIdentifier' );	
	$export_classes = array();
	foreach ($allow_classes as $action => $class)
	{		
		if ($Module->hasActionParameter($action))
		{
			$export_classes[] = $class;
		}		
	}
	
	if (!$export_classes)
	{
		$Module->redirectTo( '/classifiedexport/exportcsvprint' );
	}
	else
	{			
		$current_time = time();				
		$ini_state = $exportcsv_ini->variable('ExportCSV','State');		
		$file = $exportcsv_ini->variable('ExportCSV','File');
		$ini_fields = $exportcsv_ini->variable('ExportCSV','Fields');	
		$attribute_validater = $exportcsv_ini->variable('ExportCSV','ValidateIdentifier');	
		$total_classifieds = array();		
		
		foreach ($ini_class_identifiers as $class_identifier => $parentNodeID)
		{
			foreach ($export_classes as $class)
			{					
				if($class_identifier == $class)
				{
					$ini_packages = $exportcsv_ini -> variable( 'PackagesAnnouncements', 'Package-'.$class_identifier );
					$state = array('state', '=', $ini_state);					
					$packages = array($class_identifier . '/packages', 'in', $ini_packages);
					$internal_price = array($class_identifier . '/internal_price', '>', 0);	
					$start_date = array($class_identifier . '/start_date', '<=', $current_time);	
					$print_end_date = array($class_identifier . '/print_end_date', '>=', $current_time);						
					$attribute_filter =array( $internal_price, $state, $packages, $start_date, $print_end_date);
					$classifieds = ExportCsvPrint::fetchObjects($parentNodeID, $class_identifier, $attribute_filter );
					$total_classifieds = array_merge($total_classifieds, $classifieds);
					
				}
			}		
		}
		
		$array_strings = ExportCsvPrint::getClissifiedsCsv( $total_classifieds, $ini_fields, $exportcsv_ini, $attribute_validater );	
		//ExportCsvPrint::downloadCsv($file, $array_strings['csv_string']);
		ExportCsvPrint::downloadZip($file, $array_strings['csv_string'],$array_strings['img_array']);
		
	}
}
else
{
	$Module->redirectTo( '/classifiedexport/exportcsvprint' );
}

?>