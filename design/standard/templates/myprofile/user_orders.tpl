{def $user_orders = get_orders_by_user($current_user.contentobject_id)
	 $currency = fetch( 'shop', 'currency', hash( 'code', $Orders.item.productcollection.currency_code ) )
	 $locale = false()
	 $symbol = false()
}
	 
{if $currency}
    {set $locale = $currency.locale
         $symbol = $currency.symbol}
{else}
    {set $locale = false()
         $symbol = false()}
{/if}

<div class="content_user_orders">
	<div class="title_user_order">
		<div class="field_title_user_order_medium">{'ID'|i18n( 'design/admin/shop/orderlist' )}</div>
		<div class="field_title_user_order_medium">{'Total'|i18n( 'design/admin/shop/orderlist' )}</div>
		<div class="field_title_user_order_large">{'Time'|i18n( 'design/admin/shop/orderlist' )}</div>
		<div class="field_title_user_order_medium">{'Status'|i18n( 'design/admin/shop/orderlist' )}</div>
		<div class="float-break"></div>
	</div>
	<div class="float-break"></div>
	{foreach $user_orders as $user_order}
		<div class="user_order">
			<div class="field_user_order_medium">{$user_order.created |datetime( 'custom', '%Y%m%d' )}-{$user_order.id}</div>
			<div class="field_user_order_medium">{$user_order.total_inc_vat|l10n( 'currency', $locale, $symbol )}</div>
			<div class="field_user_order_large">{$user_order.created|l10n( shortdatetime )}</div>
			<div class="field_user_order_medium">{$user_order.status_name}</div>
		</div>
		<div class="float-break"></div>
		<div id="user_order_detail_{$user_order.id}" class="user_order_detail"></div>		
	{/foreach}
	<div class="float-break"></div>
</div>
