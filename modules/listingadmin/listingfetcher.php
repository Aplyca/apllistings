<?php

$module = $Params['Module'];
$offset = $Params['offset'];


$count = 0;
if($module->isCurrentAction('Search'))
{	
	$params = array();
	$params['CreationType'] = $module->actionParameter('CreationType');
	$params['ListingStatus'] = $module->actionParameter('ListingStatus');	
	$params['ExpirationFrom'] = $module->actionParameter('ExpirationFrom');	
	$params['ExpirationTo'] = $module->actionParameter('ExpirationTo');
	$params['Package'] = $module->actionParameter('Package');
	//$params['Offset'] = $module->actionParameter('Offset');
	$params['Offset'] = $offset;
	$params['SortBy'] = $module->actionParameter('SortBy');

	$listingFetcher = ListingFetcher::instance();	
	if($listingFetcher)
	{
		$result = $listingFetcher->fetch($params); 	
		$listings =  $result['list'];
		$count = $result['count'];		
	}
	else echo "error";
}


$navParams = array('offset' => $params['Offset']);


$tpl = eZTemplate::factory();
$tpl->setVariable( 'limit', ListingFetcher::LIMIT );
$tpl->setVariable( 'navparams', $navParams );
$tpl->setVariable( 'count', $count );
$tpl->setVariable( 'listings', $listings );

$Result = array();
$Result['content'] = $tpl->fetch( "design:listingadmin/listingfetch.tpl" );
$Result['path'] = array ( array ('url' => 'listingadmin/listingfetcher', 'text' => "full") );

$Result['pagelayout'] = false;
eZDebug::updateSettings(array("debug-enabled" => false, "debug-by-ip" => false));

/*$response['m']=$Result['content'];
//$response['total_search']=$tpl->variable('total_search');
$response['s']='s';
print_r(json_encode($response));*/

/*eZDB::checkTransactionCounter();
eZExecution::cleanExit();*/

?>
