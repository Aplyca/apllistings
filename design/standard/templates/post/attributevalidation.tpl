<div class="block"> 
	<span {if $attribute.has_validation_error}class="message-error float-break"{/if}>
		<td>
			<strong>{$attribute.contentclass_attribute_name}</strong>
			{if $attribute.is_required}<span class="required_field"> *</td></span>{/if}
		</td>
	</span>
	<td>
		{if $text_info}<span class="text_info">{$text_info}</span>{/if}
		{attribute_edit_gui attribute=$attribute}
	</td>
	{if $attribute.has_validation_error}<div class="messageError  float-break">	<i>{$attribute.validation_error}</i></div>{/if}
    
    {if eq($attribute.contentclass_attribute_identifier, 'fema_no')}
    <div class="linksText">
    <a href="http://www.usfa.dhs.gov/applications/hotel/" target="_blank" title="Click here to find your FEMA number.">Click here</a> to find your FEMA number.
    </div>
    {/if}
    
    {if eq($attribute.contentclass_attribute_identifier, 'prices_from')}
        <div class="linksText linksSec">
        <a href="http://www.gsa.gov/portal/category/21287" target="_blank" title="Click here to find your state per diem rate if required." >Click here</a> to find your state per diem rate if required.
    </div>

    {/if}
</div>