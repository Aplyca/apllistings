<h2>User information</h2>
<div class="full_user_image">
	{if $current_user.contentobject.data_map.image.has_content}	
		{attribute_view_gui image_class=perfil attribute=$current_user.contentobject.data_map.image}
	{else}
		<img width="100" height="75" title="{$current_user.contentobject.name}" alt="{$current_user.contentobject.name}" src={'no_image.jpg'|ezimage}/>
	{/if}			
	<a class="edit_link" href={concat('content/edit/',$current_user.contentobject_id)|ezurl}>(edit profile)</a>
</div>
<table border="0" cellpadding="0" cellspacing="0" class="full_user_info" width="100%">		
	{if $current_user.contentobject.published}
		<tr>
			<th>{"Member Since"|i18n("design/content/my_profile")}</th>
			<td>{$current_user.contentobject.published|datetime('custom','%d/%m/%Y')}</td>
		</tr>
	{/if}
	{if $current_user.contentobject.data_map.first_name.has_content}
		<tr>
			<th>{"Name"|i18n("design/content/my_profile")}</th>
			<td>{attribute_view_gui attribute=$current_user.contentobject.data_map.first_name} {attribute_view_gui attribute=$current_user.contentobject.data_map.last_name}</td>
		</tr>
	{/if}
	{if $current_user.contentobject.data_map.user_account.content.email}
		<tr>
			<th>{"Email"|i18n("design/content/my_profile")}</th>
			<td>{$current_user.contentobject.data_map.user_account.content.email}</td>
		</tr>
	{/if}
	{if $current_user.contentobject.data_map.user_account.content.login}
		<tr>
			<th>{"User name"|i18n("design/content/my_profile")}</th>
			<td>{$current_user.contentobject.data_map.user_account.content.login}</td>
		</tr>
	{/if}
	{if $current_user.contentobject.data_map.personal_phone_number.has_content}
		<tr>
			<th>{"Telephone Number"|i18n("design/content/my_profile")}</th>
			<td>{attribute_view_gui attribute=$current_user.contentobject.data_map.personal_phone_number}</td>
		</tr>
	{/if}
	{if $current_user.contentobject.data_map.personal_address_number.has_content}
		<tr>
			<th>{"Address Number"|i18n("design/content/my_profile")}</th>
			<td>{attribute_view_gui attribute=$current_user.contentobject.data_map.personal_address_number}</td>
		</tr>
	{/if}
	{if $current_user.contentobject.data_map.personal_street_name.has_content}
		<tr>
			<th>{"Street Name"|i18n("design/content/my_profile")}</th>
			<td>{attribute_view_gui attribute=$current_user.contentobject.data_map.personal_street_name}</td>
		</tr>
	{/if}
	{if $current_user.contentobject.data_map.personal_city.has_content}
		<tr>
			<th>{"City"|i18n("design/content/my_profile")}</th>
			<td>{attribute_view_gui attribute=$current_user.contentobject.data_map.personal_city}</td>
		</tr>
	{/if}
	{if $current_user.contentobject.data_map.personal_state_province.has_content}
		<tr>
			<th>{"State / Province"|i18n("design/content/my_profile")}</th>
			<td>{attribute_view_gui attribute=$current_user.contentobject.data_map.personal_state_province}</td>
		</tr>
	{/if}
	{if $current_user.contentobject.data_map.personal_zip_code.has_content}
		<tr>
			<th>{"Zip Code"|i18n("design/content/my_profile")}</th>
			<td>{attribute_view_gui attribute=$current_user.contentobject.data_map.personal_zip_code}</td>
		</tr>
	{/if}
	{if $current_user.contentobject.data_map.personal_country.has_content}
		<tr>
			<th>{"Country"|i18n("design/content/my_profile")}</th>
			<td>{attribute_view_gui attribute=$current_user.contentobject.data_map.personal_country}</td>
		</tr>
	{/if}
</table>