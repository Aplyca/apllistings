{include uri="design:post/postbar.tpl" steps=$steps id=$objectID}

{literal}
<script type="text/javascript">
	var packages = new Array(); 
</script> 
{/literal}

<p><a class="link_classified_preview" href="/post/preview/{$objectID}">Preview</a> </p>

<ul>
{foreach $packages as $package}
	<li>
	{include uri="design:post/package.tpl" package=$package object=$object modulename=$modulename}
	</li>			
{/foreach}
</ul>

<div style="display: none;">
	<a id="confirm_triger" href="#confirm"></a>
	<div id="confirm"></div>
</div>

{literal}
<script type="text/javascript"> 
       // wait for the DOM to be loaded 
       $(document).ready(function() { 
       	var options = {success: formResponse};  
           $('.packageform').ajaxForm(options);
           function formResponse(responseText)
           {
               $("#confirm").html(responseText);
           	$("#confirm_triger").click();
               
           }            
       }); 



	$(".link_classified_preview").fancybox({	
			'width'				: 750,
			'height'			: '80%',
			'autoScale'			: false,
			'transitionIn'		: 'none',
			'transitionOut'		: 'none',
			'type'				: 'iframe'
	});

          

   	$("#confirm_triger").fancybox({
		'titlePosition'		: 'inside',		
		'scrolling' 		: 'no',
		'autoScale' 		: 'true',		
	});



   	

	   // Controls quantity textfield behavior
	   
	   $('.package_feature_quantity').keypress(function(e) {
		   var str = $(this).attr("value").length;
    	   if( e.which!=8 && e.which!=0 && (e.which<48 || e.which>57))
    	   {        	   
    	     return false;
    	   }
    	   else
    	   {
        	   if(str > 1 &&   e.which!=8 && e.which!=0)
            	   return false;
    	   }	
	   });   
	   
       $('.package_feature_quantity').keyup(function(e) {
	       
			 var idArray = $(this).attr("id").split("_");
			 var id = idArray[1]; 
			 var quantity = $(this).attr("value");
			 featureIndex = getFeatureIndex(id);
			 packages[featureIndex.x].features[featureIndex.y].quantity = quantity;
			 if ( packages[featureIndex.x].features[featureIndex.y].checked )
			 {
				var totalPrice = getPackageTotalPrice(packages[featureIndex.x].id);	
				var packagePriceSel = "#package_price_" + packages[featureIndex.x].id;		
				$(packagePriceSel).text(totalPrice);   
			 }		    			   		     		   				      
		});


	$('.package_feature_checkbox').click(function() { 
		
		var checked = $(this).attr("checked"); 			             
		var id = $(this).attr("id");
		featureIndex = getFeatureIndex(id);
		packages[featureIndex.x].features[featureIndex.y].checked = checked		
		var totalPrice = getPackageTotalPrice(packages[featureIndex.x].id);	
		var packagePriceSel = "#package_price_" + packages[featureIndex.x].id;	
		$(packagePriceSel).text(totalPrice);   		                                           	
	});

	function getIdFromFeature(id)
	{
		for(i=0; i<packages.length; i++)
		{
			if(packages[i].features.length > 0)
			{
				for(j=0; packages[i].features; j++)
				{
					if(packages[i].features[j].id == id)
					{
						return packages[i].id;
					}
				}
			}
		}
		return 0;
	}

	function getFeatureIndex(id)
	{
		for(i=0; i<packages.length; i++)
		{
			if(packages[i].features.length > 0)
			{
				for(j=0; packages[i].features; j++)
				{
					if(packages[i].features[j].id == id)
					{
						var featureIndex = {x:i, y:j};
						return featureIndex;
					}
				}
			}
		}			
		return 0;
	}

	function getPackageTotalPrice(packageId)
	{
		var packagePrice = 0;
		for(i=0; i<packages.length; i++)
		{
			if(packages[i].id == packageId)
			{
				packagePrice += packages[i].price;
				if(packages[i].features.length > 0)
				{
					for(j=0; j < packages[i].features.length; j++)
					{						
						if(packages[i].features[j].has_quantity)
						{
							if(packages[i].features[j].quantity != "" && packages[i].features[j].checked)
							{								
								packagePrice += packages[i].features[j].price * packages[i].features[j].quantity;
							
							}							 
						}
						else
						{
							if(packages[i].features[j].checked)
							{
								packagePrice += packages[i].features[j].price;
							}								
						}				
					}
				}
			}
		}	
		return packagePrice; 		
	}


	
	 
</script> 
{/literal}