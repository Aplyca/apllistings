{*$order*}{*$object*}

{def $eventType = explode_by_capital_letter($eventData.EventTypeName)
	$billingIni = ezini('cctype','data','cybersourcebillinginput.ini')
}

{foreach $object.assigned_nodes as $assigned_node}
  			{if $assigned_node.node_id|ne($object.main_node_id)}
	   			{def $url_alias = $assigned_node.url_alias|ezurl}
	  		{/if}
 {/foreach}


<div class="content_billing">
	<div class="form_content_post">			
		<div class="title_content_post">
			<h2>{"Published Hotel Confirmation"|i18n("design/content/billing")}</h2>
		</div>
		<div class="messages_content_post">
			<h1 style="font-size:23px;">Congratulations!</h1>
			<h3>{"Dear customer, Your Hotel announcement  was succesfully published"|i18n("design/content/billing")}</h3>
			<h4>{"We will contact you for the billing process"|i18n("design/content/billing")}</h4>
			<p>Go to your <a href={$url_alias} target="_blank">{attribute_view_gui attribute=$object.data_map.hotel_name}</a> announcement.</p>
			<p><strong>Customer Information</strong></p>
			<ul>
				<li><strong>First Name:</strong> {$user.firstName}</li>
				<li><strong>Last Name:</strong> {$user.last_name}</li>
				<li><strong>Email Address:</strong> {$user.email}</li>
				{if ne($user.address,'')}
				<li><strong>Address:</strong> {$user.address}</li>
				{/if}
				{if ne($user.city,'')}
				<li><strong>City/Country:</strong> {$user.city}/{$ordermanager.account_information.country}</li>
				{/if}
				{if ne($user.phone_number,'')}
				<li><strong>Phone Number:</strong> {$user.phone_number}</li>
				{/if}
				
			</ul>
			<ul class="back_profile">
				<li><a href="{'/myprofile/dashboard'|ezurl('no')}" title={"back to my profile"|i18n('extension/xrowecommerce')}>{"back to my profile"|i18n('extension/xrowecommerce')}</a></li>
			</ul>
			
			<br/>
			
			
			{if or(ne($ordermanager.id,''),ne($ordermanager.product_total_inc_vat,''),ne($ordermanager.status_name,''))}
			<p><strong>Billing Information</strong></p>
			   {if ne($ordermanager.id,'')}<li><strong>Order ID:</strong>{$ordermanager.id}</li>{/if}
			   {if ne($ordermanager.product_total_inc_vat,'')}<li><strong>Total Amount:</strong> ${$ordermanager.product_total_inc_vat} USD </li>{/if}
			   {if ne($ordermanager.status_name,'')}<li><strong>Status:</strong> {$ordermanager.status_name}</li>{/if}
			</ul>
			{/if}
			
			
			
		</div>
	</div>
</div>
