{def $menu_steps = ezini('MenuSteps','NameMenuSteps','apllistings.ini')}


<div id='editcontent'></div>


<script type="text/javascript">

var editStepsNumber = {$editstepsnumber};
var currentStep = {$initialeditstep};
var checkedSteps = new Array(editStepsNumber);


{literal}

	loadStep(currentStep);					
	function formResponse(responseText)
	{			
		var options = {success: formResponse}; 
		if(responseText === "1")
		{
			checkedSteps[currentStep-1] = 1;
			currentStep++;
			if(	currentStep > editStepsNumber)
			{
				window.location="/post/multiedit/store";// cambiar a multiedit para completar validacion
			}
			else
			{				
				loadStep(currentStep);
			}										
		}
		else
		{
			if (navigator.appName == 'Microsoft Internet Explorer')
			{
				// IE sucks 
				document.getElementById("editcontent").innerHTML = responseText;	  
			}
			else
			{
				$("#editcontent").html(responseText);
				loadStepBehavior(currentStep);
			}		 			 						 	
			$('#editpartform').ajaxForm(options);
		}	           	           
	} 	

	function loadStep(step)
	{		
		$.post('/post/editattributes/load/' + step, function(data) {
			  $('#editcontent').html(data);
			  currentStep = step; 		  
			  var options = {success: formResponse}; 
			  $('.editstepform').ajaxForm(options);
			  loadStepBehavior(step);
		});
		
	}

	function loadStepBehavior(step)
	{		

		/*if(step == 1)
		{			
				// place here events you need to enable after loading dom asyncrhonically
		}
		else if(step ==2 ) */
	}

	function createMenuSteps(step)
	{	
		var menuSteps = new Array(5);	
	
		{/literal}{foreach $menu_steps as $key => $value}{literal}
			menuSteps[{/literal}{$key}{literal}] = '{/literal}{$value}{literal}'; 
		{/literal}{/foreach}{literal}
		
		var htmn_ul = '';
		var class_li = '';
		var content_li = '';
		var active_li = 0;		
		
		for ( i=1; i<=editStepsNumber ;i++ )
		{		
			class_li = '';			
			content_li = '<span>' + i + '. ' + menuSteps[i] + '</span>';						
			active_li = 0;				
			
			if(i == step && i != editStepsNumber)
			{
				class_li = 'class="current"';
				active_li = 1;				
			}
			else if (i == editStepsNumber)
			{
				class_li = 'class="no_background"';	
				active_li = 0;					
			}
			else if (i == (step - 1) && i != editStepsNumber)
			{
				class_li = 'class="last_step_back"';
				active_li = 1;				
			}	
			else if (i < (step - 1) && i != editStepsNumber)
			{
				class_li = 'class="step_back"';
				active_li = 1;				
			}	
			
			if (i == step && i == editStepsNumber)
			{
				class_li = 'class="last_step"';
				active_li = 1;				
			}		
			
			if(checkedSteps[i-1] == 1)
			{	
				if (i == step && checkedSteps[i] == 1)
				{
					class_li = 'class="current_back"';
				}
				else if (i > step && checkedSteps[i] != 1 && i != editStepsNumber)
				{
					class_li = 'class="next_step"';
				}
				else if (i > step && checkedSteps[i+1] != 1 && i != editStepsNumber)
				{
					class_li = 'class="last_next_step"';
				}
				else if (i == (step + 1) && i != editStepsNumber)
				{
					class_li = 'class="last_next_step"';
				}
				
				active_li = 1;			
			}
			
			if(active_li)
			{
				content_li = '<a href="#" onclick="loadStep('+ i +');return false;">' + i + '. ' + menuSteps[i] +'</a>';
			}
			
			htmn_ul = htmn_ul + '<li '+ class_li +'>'+ content_li +'</li>';
		}
		
		$('.menu_steps').html(htmn_ul);	
	}
	



</script> 
{/literal}	

