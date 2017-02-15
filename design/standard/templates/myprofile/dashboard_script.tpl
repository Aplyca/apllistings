{literal}
<script type="text/javascript">

	$('#container_profile_listings').tabs();
		
	jQuery(document).ready(function(){
	
		$(".button_close").bind("click", function(e){
			e.preventDefault();
		});
	
		/*$(".button_remove").click(function(){
			var form = $(this).parent();
			var inputs = $(form).children('input');
			var values = new Array();
			var i = 0;
			var tipoAnuncio;
			var contentobjectID;
			inputs.each(function(index) {
				values[i] =  $(this).val();
				i++;
			});		
			contentobjectID = values[0];					
			if (confirm("Are you sure you want to remove this ad?")) {
				$.get('/listingactions/remove/'+contentobjectID,function(data){
					$(window.location).attr('href', '/myprofile/dashboard');
				});
			}
		});	*/

		$(".button_close").click(function(){			
			var form = $(this).parent();	
			var inputs = $(form).children('input');
			var values = new Array(); 
			var i = 0;
			var tipoAnuncio;
			var contentobjectID;				
			inputs.each(function(index) {
				values[i] =  $(this).val();
				i++;				
			});
			contentobjectID = values[0];			
			if (confirm("Are you sure you want to close this ad?")) {				
				$.get('/listingactions/close/'+contentobjectID,function(data){
					$('#form_'+contentobjectID).hide();
				});
			}
		});	
	
	});			
	
</script>
{/literal}