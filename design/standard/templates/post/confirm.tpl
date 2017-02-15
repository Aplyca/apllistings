  <div class="plan_preview_order">
  	 <div class="confirm_generic_order">
  		<div class="confirm_package_order">
	       <div class="confirm_package_label">Please confirm your posting</div>
				<div class="confirm_package_name"><strong>Package:</strong> {$object.data_map.package.value.name}</div><br/><br/>
			 
					<form  action="/post/confirm/" method="post">
						<input type="submit" name="publish" id="PublishButton" value="Publish"/>
						{*<input type="submit" name="store" id="StoreButton" value="Store"/>*}	
						<input type="submit" name="purchase" value="Purchase" title="" alt="" class="button_buy"/>
					</form>
			
		<div class="confirm_package_price"><strong>Total Price:</strong> &pound;{$object.data_map.listing_price.data_float}</div><br/>	
			 
			<div class="plan_preview">
			<p><a class="fancy_plan_preview" href="/post/preview/{$object.id}"><img class="link_plan_preview" src={'../images/preview_icon.jpg'|ezimage} width="118" height="132"></a></p>
			</div>
			
			  	
			
		</div>	
	 </div>
  </div>
	
	
		{literal}
		<script type="text/javascript">
			$(document).ready(function() {
				$(".fancy_plan_preview").fancybox({	
						'width'				: 750,
						'height'			: '80%',
						'autoScale'			: false,
						'transitionIn'		: 'none',
						'transitionOut'		: 'none',
						'type'				: 'ajax'
				});
			});
		</script>
		{/literal}	