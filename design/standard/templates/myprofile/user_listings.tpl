<div class="listing_actions">
	{if $state|eq(3)}
		<form class="form_actions" id="form_{$listing.contentobject_id}" name="form_{$listing.contentobject_id}" action={'listingactions/action'|ezurl} method="post">
			<input type="hidden" value="{$listing.contentobject_id}" id="ObjectID" name="ObjectID" />
			<input type="submit" value="Publish" id="PublishButton" name="PublishButton" class="button_publish" />
			<input type="submit" value="Edit" id="EditButton" name="EditButton" class="button_edit" />
			<input type="submit" value="Remove" id="RemoveButton" name="RemoveButton" class="button_remove" />
		</form>
	{elseif and( $state|eq(4), $listing.data_map.succesful.value|eq(0) )}
		<form class="form_actions" id="form_{$listing.contentobject_id}" name="form_{$listing.contentobject_id}">
			<input type="hidden" value="{$listing.contentobject_id}" id="ObjectID" name="ObjectID" />
			<input type="submit" value="Close" id="CloseButton" name="CloseButton" class="button_close" />
		</form>
	{/if}
</div>
{node_view_gui content_node=$listing view='ads_line'}