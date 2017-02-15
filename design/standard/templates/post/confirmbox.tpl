<div class="confirm_box">
	<h1>Confirm your Posting</h1>
	<div class="nombreCredito">
		<p>You are about to publish <strong>{$classifiedName}</strong> </p>
		<p>from <b>{$publicationData.startDate|datetime('custom', '%d/%m/%Y')}</b> to  <b>{$publicationData.endDate|datetime('custom', '%d/%m/%Y')}</b></p>
		<br/>		
		<br/>
		<ul>
		{foreach $selectedImproves as $improve}
			<li>
				<b>{$improve.identifier}</b><br/>
				Price: {$improve.price} <br/>
				Quantity: {$improve.price} <br/>
			</li>
		{/foreach}
		</ul>
		<br/>
		<hr >
		<h4>Total: <span><b> $ {$totalPrice}</b></span></h4>
	</div>
	<br />	
	<div class="buttonblock">
	{if $modulename|eq('listingactions')}
		<form  action="/listingactions/publish/{$objectId}" method="post">
		<input type="submit" name="publish" id="PublishButton" value="Publish"/>		
	{else}
		<form  action="/post/package/" method="post">
		<input type="submit" name="publish" id="PublishButton" value="Publish"/>
		<input type="submit" name="store" id="StoreButton" value="Store"/>		
	{/if}	
		
		<a href="#" id="confirm_close" class="close_fancyB" onclick="return false;">Close</a>	
	</form>		
	</div>
</div>


{literal}
<script type="text/javascript"> 

/*$('#confirm_close').click(function() { 	
	$(this).parent().fancybox.close;
	return false;
});*/	

$('#confirm_close').click(parent.$.fancybox.close);

</script> 
{/literal}