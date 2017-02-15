<h2>Bookmarks</h2>
{let $bookmark_list=fetch( content, bookmarks )}
	{if ne($bookmark_list|count,0)}		
		<form method="post" action="/content/bookmark" name="bookmarkaction">
			<div class="favoritos_box">							
				{section show=$bookmark_list}
				{section var=Bookmarks loop=$bookmark_list}
					{section show=ne( $ui_context, 'edit' )}
					<div class="favoritos_box_item">
					{section show=ne( $ui_context, 'browse')} 
						<div><input type="checkbox" title="Elegir favoritos para eliminar." value="{$Bookmarks.item.id}" name="DeleteIDArray[]"></div>
						<a href="#" onclick="ezpopmenu_showTopLevel( event, 'BookmarkMenu', ez_createAArray( new Array( '%nodeID%', '{$Bookmarks.item.node_id}', '%objectID%', '{$Bookmarks.item.contentobject_id}', '%bookmarkID%', '{$Bookmarks.item.id}', '%languages%', {$Bookmarks.item.node.object.language_js_array} ) ) , '{$Bookmarks.item.name|shorten(18)|wash(javascript)}'); return false;">{$Bookmarks.item.node.object.content_class.identifier|class_icon( small, '[%classname] Click on the icon to display a context-sensitive menu.'|i18n( 'design/admin/pagelayout',, hash( '%classname', $Bookmarks.item.node.object.content_class.name  ) ) )}</a>&nbsp;<a href={$Bookmarks.item.node.url_alias|ezurl}>{$Bookmarks.item.node.name|wash|downcase()|shorten(30)}</a></div>
					{section-else}
						{section show=$Bookmarks.item.node.object.content_class.is_container}
							{$Bookmarks.item.node.object.content_class.identifier|class_icon( small, $Bookmarks.item.node.object.content_class.name )}&nbsp;<a href={concat( '/content/browse/', $Bookmarks.item.node.node_id)|ezurl}>{$Bookmarks.item.node.name|wash}</a></div>
						{section-else}
							{$Bookmarks.item.node.object.content_class.identifier|class_icon( small, $Bookmarks.item.node.object.content_class.name )}&nbsp;{$Bookmarks.item.node.name|wash}</div>
						{/section}
					{/section}
					{section-else}
						<div>{$Bookmarks.item.node.object.content_class.identifier|class_icon( ghost, $Bookmarks.item.node.object.content_class.name )}&nbsp;<span class="disabled">{$Bookmarks.item.node.name|wash}</span></div>
					{/section}
					{/section}
				{/section}
			</div>	
			<div class="content_button_favorites">
				<input type="hidden" name="NeedRedirectBack" value="/Mi-Vanguardia" />
				<input type="submit" id="RemoveButton" name="RemoveButton" value="" class="button_delete_fav">
			</div>
		</form>	
	{else}
		<p>Empty</p> 
	{/if}
{/let}