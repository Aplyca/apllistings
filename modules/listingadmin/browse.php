<?php

$tpl = eZTemplate::factory();
$module = $Params['Module'];
//$objectId = $Params['ObjectID'];



$Result['content'] = $tpl->fetch("design:listingadmin/browse.tpl");
$Result['path'] = array( array('url' => 'listingadmin/browse', 'text' => 'Upgrade Confirmation'));



?>


