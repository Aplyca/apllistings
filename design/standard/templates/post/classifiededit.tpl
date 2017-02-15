{def $object_id = ezhttp( 'classified_obj_id', 'session' )
	$object=fetch( 'content', 'object', hash( 'object_id', $object_id ) )
	$attributes = array() 
}

{set $attributes = $obj_attributes}
{include uri="design:post/postbar.tpl" steps=$steps modulename=$modulename id=$objectId}


{if $modulename|eq('listingactions')}
	<form class="user-payworks-form" method="post" action={'listingactions/edit/'|append($objectId)|ezurl} enctype="multipart/form-data" >
{else}
	<form class="user-payworks-form" method="post" action={'post/edit'|ezurl} enctype="multipart/form-data" >
{/if}

	<div class="attribute-header">
		<h2 class="long">Announcement Information</h2>
	</div>
	<table border="0" id="blue_table">
	{foreach $attributes as $attribute}		
		<tr>{include uri="design:post/attributevalidation.tpl" attribute=$attribute}</tr>	
	{/foreach}
	</table>
		
	<div class="buttonblock">
	{if $modulename|eq('listingactions')}
		<input type="submit" name="save" value="Save" class="button_next"/>
	{else}
		<input type="submit" name="nextstep" value="Next" class="button_next"/>
	{/if}			
	</div>
</form>


<div class="block" id="block_upload"> 
	<p class="text_upload">Upload more images <a href="/multiupload/upload/{$images_cache_node_id}" id="additional_images">here</a>.</p>
</div> 

{include uri="design:tooltip.tpl"}

{literal}
<script language="javascript" type="text/javascript">
	$(document).ready(function(){
		//$('#block_upload').hide();
			
		$("#additional_images").fancybox({
			'width'				: 720,
			'height'				: 400,
			'transitionIn'		: 'fade',
			'transitionOut'	: 'elastic',
			'type'				: 'iframe'
		});
		
	});
</script>
{/literal}


