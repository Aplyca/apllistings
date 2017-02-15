{set-block scope=root variable=cache_ttl}0{/set-block}

{def $current_user = fetch( 'user', 'current_user' )
	 $listing_states = $group.states
}

{if $current_user.is_logged_in}
	<div class="container_profile_dashboard float-break">
		<h1>{"My Account"|i18n("design/content/my_profile")}</h1>
        <div class="container_user_info">
			{include uri="design:myprofile/user_info.tpl" current_user=$current_user}
	    </div>
		<!--<div class="left_content">
		</div>
		<div class="right_content">
			<div class="container_bookmark_list">
				{*{include uri="design:myprofile/user_bookmark_list.tpl" current_user=$current_user}*}
			</div>
		</div>-->
		<div class="float-break"></div>
		<div class="full_content">
			<div class="general-tabs">
				<ul>
					{foreach $listing_states as $key => $item}
						<li><a href={concat('myprofile/listings/', $key)|ezurl}>{$item}</a></li>
					{/foreach}	
				</ul>
			</div>
		</div>
		
		<div id="dialog_preview" title="{"Listing Preview"|i18n("design/content/post")}">
			<div id="preview"></div>	
		</div>
		
		{include uri="design:myprofile/dashboard_script.tpl"}
	</div>
{else}
	{include uri='design:user/login.tpl'}	
{/if}

{ezcss_require( 'fancybox/jquery.fancybox-1.3.4.css' )}
{ezcss_require( 'pikachoose/bottom.css' )}
{ezscript_require( 'pikachoose/lib/jquery.pikachoose.js' )}
{ezscript_require( 'fancybox/jquery.fancybox-1.3.4.js' )}
{ezscript_require( 'jquery/jquery.form.js' )}