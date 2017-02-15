{def $limit = ezini('MyProfile','LimitListings','apllistings.ini') 
     $listings = fetch('content', 'tree', hash( 'parent_node_id', fetch('user', 'current_user').contentobject.main_node_id,
                                                'sort_by', array( 'published', false() ),
                                                'class_filter_type',  'include',
                                                'limit', $limit,
                                                'offset', $view_parameters.offset,
                                                'class_filter_array', ezini('MyProfile','ListingsClasses','apllistings.ini'),
                                                'attribute_filter', array( 'and',array('state', "=", $state_id))))
	$listings_count = fetch('content', 'tree_count', hash( 'parent_node_id', fetch('user', 'current_user').contentobject.main_node_id,
                                                           'class_filter_type',  'include',
                                                           'class_filter_array', ezini('MyProfile','ListingsClasses','apllistings.ini'),
                                                           'attribute_filter', array( 'and',array('state', "=", $state_id))))                                                              
}
<div id="listings"> 
    <div class="content_listings_line"> 
    {if $listings}
        <div class="content_info">
            <div class="items_count">{'Total hotels'|i18n("design/content/my_profile")}: {$listings_count}</div>
            <div class="refresh"><div class="spinner"></div><a class="refresh-link" onclick="populateTab({concat('myprofile/listings/', $state_id)|ezurl(single)});">{'Refresh'|i18n( 'dcshoes/preview' )}</a></div>
        </div>
        <div class="float-break"></div>
        <div id="page_navigator_top">
            {include    name=navigator
                uri='design:instantcontent/search/navigator.tpl'
                page_uri=concat('myprofile/listings/', $state_id)
                item_count=$listings_count
                view_parameters=$view_parameters
                item_limit=$limit
                id=$state_id}
        </div>
        {foreach $listings as $listing}
            {include uri="design:myprofile/user_listings.tpl" listing=$listing state=$state_id}      
        {/foreach}
        <div id="page_navigator_bottom"></div>
        {literal}
        <script>
	        $(function() {
	            $("#page_navigator_bottom").html($("#page_navigator_top").html());
	            $(".pagenavigator.block-{/literal}{$state_id}{literal} a[rel]").each(function(){
	                $(this).click(function(event) {
	                    event.preventDefault();
	                    var offset=$(this).attr("rel");
	                    var offset_url='';
	                    if (offset)
	                    {
	                        offset_url='/(offset)/'+offset;
	                    }
	                    populateTab({/literal}{concat('myprofile/listings/', $state_id)|ezurl(single)}{literal}+offset_url);
	                });
	            });              
	        });
        </script>
        {/literal}
    {else}
        <p>{"There are no listings."|i18n("design/content/my_profile")}</p>
    {/if}
    </div>
</div>

{literal}
<script type="text/javascript">
$(function() {		
	$( "#dialog_preview" ).dialog({
		autoOpen: false,
		width: 750,
		height: 'auto',
		modal: true,
		buttons: {
					"Done": function() {
						$( this ).dialog( "close" );
					}
				 },
		close: function() {
			$('#preview').html('');
		}
	});

	$( ".listing_preview, .btn_preview" ).click(function() {
		$.get({/literal}{'post/preview'|ezurl(single)}{literal}+'/'+$(this).attr('id'), function(data) {
			$('#preview').html(data);
			$( "#dialog_preview" ).dialog( "open" );				  
		});
		return false;
	});
	
});

$(".button_remove").click(function(){
	if (confirm("Are you sure you want to remove this listing?")) {
		return true;
	}
	else
		return false;
});

$('.button_publish[title], .button_edit[title], .button_remove[title]').qtip({
	position: {
    	my: 'bottom',
    	at: 'top center'
    },
	style: {
		width : 200,
		tip: 'bottom left'
	},
	hide:{delay:200}	
});
</script>
{/literal}