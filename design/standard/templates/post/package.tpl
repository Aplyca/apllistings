{literal}
<script type="text/javascript">
	var packageData = {
		id : {/literal}{$package.id}{literal},
		price : {/literal}{$package.price}{literal},
		features : new Array()		
	};										
	
</script> 
{/literal}
<div class="package">
<form action="/post/getconfirmdata/" class="packageform radius" id="{$package.id}" method="get">	
	<input type="hidden" id="cte_package_price_{$package.id}" value="{$package.price}">
	<div class="attribute-header">
    	<h2 class="long">{$package.name}</h2>
	</div>
    <div class="full">
    	<div class="small fr tr">
   			<b>Price:</b><span class="package_price"> $</span> <span class="package_price" id="package_price_{$package.id}"> {$package.price}</span><br/>   			
			<b>Duration: </b>{$package.duration} days
		</div>
        <p class="medium"><i>{$package.description}</i></p> 
	</div>
    <div class="break"></div>
    <input type="hidden"  name="packageId" value="{$package.id}"/>	
	{if $package.features}	
		<h3>Improve your publication</h3>
        <table class="featurelist full">
        <tr>
           	<th class="tl">Improve with</th>
            <th class="small tc">Quantity</th>
			<th class="small tc">Check</th>
        </tr>
        {foreach $package.features as $feature}	
	        {literal}
					<script type="text/javascript">
						var feature = {
							title : "{/literal}{$feature.title}{literal}",
							price : {/literal}{$feature.price}{literal},
							id:	{/literal}{$feature.id}{literal},
							has_quantity : {/literal}{$feature.has_quantity}{literal},	
							checked : false,
							quantity : ""									
						}
						packageData.features.push(feature);
					</script> 
				{/literal}	
        		
			<tr>	
            	<td class="tl"><strong>{$feature.title}</strong> <i>(${$feature.price} {if $feature.has_quantity} / additional unit {/if})</i></td>
                <td class="tc">
                	{if $feature.has_quantity}
						<input class="package_feature_quantity" id="quantity_{$feature.id}" type="text"  name="improve_options[{$feature.identifier}][quantity]" value="">
					{/if}
                </td>
                <td class="tc"> 
                 <input class="package_feature_checkbox" id="{$feature.id}" type="checkbox"  name="improve_options[{$feature.identifier}][checked]" />
				<input class="package_feature_id" type="hidden"  name="improve_options[{$feature.identifier}][id]" value="{$feature.id}"/>
				<input type="hidden"  name="module" value="{$modulename}"/>
				<input type="hidden"  name="objectId" value="{$objectID}"/>				
                </td>
			</tr>				
		{/foreach}
		</table>
														
	{/if}
	<input type="submit" value="select this"/>
</form>
</div>

{literal}
<script type="text/javascript">
	packages.push(packageData);	
</script> 
{/literal}