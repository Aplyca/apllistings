{def $current_user = fetch( 'user', 'current_user' )
	 $limit = ezini('MyProfile','LimitOrders','apllistings.ini') 
	 $orders_count = get_total_orders_by_user($current_user.contentobject_id)
	 $user_orders = get_orders_by_user($current_user.contentobject_id, $view_parameters.offset, $limit)
}

<div id="div_Orders">
    {if $user_orders}
        <div class="content_info">
            <div class="items_count">{'Total orders'|i18n("design/content/my_profile")}: {$orders_count}</div>
            <div class="refresh"><a class="refresh-link" onclick="populateTab({'myprofile/orders'|ezurl(single)});">{'Refresh'|i18n( 'hapd/profile' )}</a></div>
        </div>
        <div class="float-break"></div>
        <div id="page_navigator_top">
            {include    name=navigator
                uri='design:instantcontent/search/navigator.tpl'
                page_uri='myprofile/orders'
                item_count=$orders_count
                view_parameters=$view_parameters
                item_limit=$limit
                id=$current_user.contentobject_id}
        </div>
		{include uri="design:myprofile/user_orders.tpl" orders_count=$orders_count user_orders=$user_orders}		
        <div id="page_navigator_bottom"></div>
        {literal}
        <script>
	        $(function() {
	            $("#page_navigator_bottom").html($("#page_navigator_top").html());
	            $(".pagenavigator.block-{/literal}{$current_user.contentobject_id}{literal} a[rel]").each(function(){
	                $(this).click(function(event) {
	                    event.preventDefault();
	                    var offset=$(this).attr("rel");
	                    var offset_url='';
	                    if (offset)
	                    {
	                        offset_url='/(offset)/'+offset;
	                    }
	                    populateTab({/literal}{'myprofile/orders'|ezurl(single)}{literal}+offset_url);
	                });
	            });              
	        });
        </script>
        {/literal}
    {else}
        <p>{"There are no Orders."|i18n("design/content/my_profile")}</p>
    {/if}
</div>

{literal}
<script type="text/javascript">
$(function() {  
    $('.order-header').click(function(event){
        event.preventDefault();
        var order = $(this).parent();
        var detail=order.children('.order_detail');
        if (order.hasClass('selected'))
        {    
        	order.removeClass("selected");
        }
        else
        {
        	$('.user_order.selected').each(function(event){
        		$(this).children('.order_detail').toggle();
    			$(this).removeClass("selected");    			
    		});
            if (!(order.hasClass('cached'))) 
            {             
            	callback = function (res){
            	    if(res.s == 's')
            	    {
            	    	detail.html(res.m);
                        order.addClass('cached');
            	    }                
            	} 
            	executeAjax('GET', {/literal}{'customerorders/vieworder'|ezurl(single)}{literal}+'/'+order.attr('id')+ '/D', {}, callback); 
            }                                                
            order.addClass("selected");                
        }
        detail.toggle();        
    })
    $('.link').click(function(event){
        event.stopImmediatePropagation();
    });		
});			
</script>
{/literal}	