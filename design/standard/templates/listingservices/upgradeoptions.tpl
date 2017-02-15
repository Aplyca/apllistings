<h1>Upgrade your plan!</h1>
<br/>
<h2>Your Current Plan</h2>

<b>{$current_package.name} ({$remain_days} days left)</b>
		<p>{$current_package.description}</p>

<h2>Upgrade Options</h2>
{foreach $upgrade_packages as $key => $package}
		<a href="/listingservices/upgradeconfirm/{$object_id}/{$package.id}">{$package.name} (${$package.upgrade_price})</a> 
		<p>{$package.description}</p>
{/foreach}