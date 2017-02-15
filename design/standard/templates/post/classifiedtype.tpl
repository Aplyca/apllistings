
{def $areas = fetch( content, list, hash( parent_node_id, 2,
									class_filter_type,include,
									class_filter_array, array( 'area' )))
					 
                                    				   									
}


{include uri="design:post/postbar.tpl" steps=$steps}

<ul>
{foreach $areas as $index => $area}
<li>
	<a href="/post/type/{$area.data_map.identifier.data_text}">{$area.name}</a>
</li>	 
{/foreach}
</ul>