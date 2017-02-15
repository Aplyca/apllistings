{def $content_features = fetch( content, list, hash( parent_node_id, 83,
      											     class_filter_type, 'include',
                                             	     class_filter_array, array('content_feature'),
                                                     sort_by, array(priority,true()) )) 
	 $hotel_packages = fetch( content, list, hash( parent_node_id, 75,
      											   class_filter_type, 'include',
                                             	   class_filter_array, array('package'),
                                                   sort_by, array(priority,true()) ))
     $sortArray = hash(74, $packages[74], 75, $packages[75], 76, $packages[76], 77, $packages[77])  
     $feature_id = ''
     $ni = 0
	 $np = 3
     $c1 = ''
	 $c2 = ''
	 $selected_radio = ''  
} 
{set $packages = $sortArray}

<div class="content_post">
	<h1>Choose a Listing Package</h1>
	<div id="view_packages" class="listing_packagaes">
		{foreach $packages as $index => $package reverse}
			<h3><a href="#">{$package.name}</a></h3>
			<div>
				<p>
					Mauris mauris ante, blandit et, ultrices a, suscipit eget, quam. Integer ut neque. Vivamus nisi metus, molestie vel, gravida in, condimentum sit amet, nunc. Nam a nibh. Donec suscipit eros. Nam mi. Proin viverra leo ut odio. Curabitur malesuada. Vestibulum a velit eu ante scelerisque vulputate.
				</p>
				<form class="form_select_package" id="formSelectPackage_{$package.id}" name="formSelectPackage_{$package.id}" method="post" action={concat('post/listingplan/',$package.id)|ezurl} >
					<input class="button_select_package" type="submit" value="Select package {$package.name}" name="ButtonSelectPackage_{$package.id}" id="ButtonSelectPackage_{$package.id}">
				</form>
				<div class="package_price">
					<strong>Price {attribute_view_gui attribute=$hotel_packages.$np.data_map.price}</strong>
				</div>							
			</div>
			{set $np = dec($np)}
		{/foreach}
	</div>
	
	<div class="content_complete_packages">
		<p><a href="/" class="view_complete_packages" id="viewPackages" >View all Packages</a></p>	
		<div id="packages" title="Listing Packages">
			<table style="width: 520px;">
			   <tr>
				 <td class="title"> <font size="2" face="calibri" ><strong>Features</strong></font></td>
				 {foreach $packages as $index =>$package}
				 <td class="title"> <font size="2" face="calibri"><strong>{$package.name}</strong></font></td>
				 {set $ni=inc($ni)}
				 {/foreach}
				 {set $ni=0}
			   </tr>
			   <tr>
				 <td bgcolor = "EFF5FB"> <font size="2" face="calibri" color= "3399FF"><strong>Price</strong></font></td>
				 {foreach $packages as $index =>$package}
				 <td bgcolor = "EFF5FB"> <font size="2" face="calibri" color= "3399FF"><strong>{attribute_view_gui attribute=$hotel_packages.$ni.data_map.price}</strong></font></td>
				 {set $ni=inc($ni)}
				 {/foreach}
				 {set $ni=0}
			   </tr>		   
			   {foreach $content_features as $cols => $content}
			   {set $c1=$c1|inc}
			   {set $c2=$c1}
			   {set $feature_id = $content.data_map.identifier.content}
				   {foreach $packages as $package}
				   {if and(is_set($package.content_restrictions.$feature_id),eq($c1,$c2))}
						<tr>
						<td bgcolor = "EFF5FB"> <font size="2" face="calibri" color="3399FF"><strong>{$content.name}</strong></font></td>
							{foreach $packages as $index =>$package}
								{if is_set($package.content_restrictions.$feature_id)}
									{if eq($package.content_restrictions.$feature_id,0)}
									<td><font size="2" face="calibri" color="red"><img src={'cancel-icon.png'|ezimage()} width="15px";height="15px"></font></td>
									{else}
									<td><font size="2" face="calibri" color="grey"><strong>{$package.content_restrictions.$feature_id}</strong></font></td>
									{/if}
								{else}
									<td><font size="2" face="calibri" color="grey"><img src={'ok-icon.png'|ezimage()} width="15px";height="15px"></font></td>	
								{/if}							
								
							{/foreach}
						</tr>
						{set $c2 = $c1|inc}
					{/if}					   
					{/foreach}
				{/foreach}			   
			   
			   {foreach $content_features as $cols_feat => $content_feat}
			   {set $c1=$c1|inc}
			   {set $c2=$c1}
			   {set $feature_id = $content_feat.data_map.identifier.content}
				   {foreach $packages as $package}
				   {if and(is_set($package.available_features.$feature_id),eq($c1,$c2))}
						<tr>
						<td bgcolor = "EFF5FB"> <font size="2" face="calibri" color="3399FF"><strong>{$content_feat.name}</strong></font></td>
							{foreach $packages as $index =>$package}
								{if is_set($package.available_features.$feature_id)}
									{if ne($package.available_features.$feature_id,'')}
									<td><font size="2" face="calibri" color="grey"><strong>{$package.available_features.$feature_id}</strong></font></td>
									{else}
									<td><font size="2" face="calibri" color="grey"><i>Unlimited</i></font></td>
									{/if}
								{else}
									<td><font size="2" face="calibri" color="red"><img src={'cancel-icon.png'|ezimage()} width="15px";height="15px"></font></td>	
								{/if}							
								
							{/foreach}
						</tr>
						{set $c2 = $c1|inc}
					{/if}			
				   {/foreach}		   
			   {/foreach}
			</table>
		</div>		
	</div>

</div>

{*
<div class="content_post">
	<h1>Choose a Listing Package</h1>
	<div class="listing_package">		 
		{foreach $packages as $index => $package reverse}
			<div class="listing_pakage_selected" id="{$package.id}" name="{$package.id}">
				<span>{$package.name}</span>
				{attribute_view_gui attribute=$hotel_packages.$np.data_map.price}
			</div>
			{set $np = dec($np)}
		{/foreach}	
		<p><a href="/" class="view_packages" id="viewPackages" >See complete packages</a></p>
	</div>
</div>
*}


{literal}
	<script type="text/javascript">
		
		$(document).ready(function() {	
		
			$(".listing_pakage_selected").click(function () { 
				window.location='/post/listingplan/'+$(this).attr('id');
			});			
			
			$(function() {
				$( "#packages" ).dialog({
					autoOpen: false,
					resizable: false,
					draggable: false,
					height: 'auto',
					width: '550',
					modal: true
				});
		
				$( ".view_complete_packages" ).click(function() {
					$("#packages").dialog( "open" );
					$("#packages").dialog( "option", "buttons", {  "Ok": function() {	
																				$( this ).dialog( "close" );																												
																		   }															  
																		}
					);		
					return false;
				});
			});
			
			$(function() {
				$( "#view_packages" ).accordion();
			});
			
		});				
	</script>
{/literal}	
