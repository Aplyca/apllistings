function formResponse(responseText)
{			
	if (navigator.appName == 'Microsoft Internet Explorer')
	{
	    var options = {success: formResponse, iframe:false};
	    if(currentStep == 3)
		{
		    var options = {success: formResponse};
		}
	}		
	else
    {
        var options = {success: formResponse};
	}
	if(responseText === "1")
	{
		checkedSteps[currentStep-1] = 1;
		if(!saveAction)
		{
			currentStep++;
		}
		if(	currentStep > editStepsNumber)
		{
			// hotelType == 'saved' hotelState='published'
			window.location="/post/multiedit/" + objectID +  "/store";// cambiar a multiedit para completar validacion			
		}
		else
		{		
			loadStep(objectID, currentStep);
		}										
	}
	else
	{
		if (navigator.appName == 'Microsoft Internet Explorer')
		{
			// IE sucks 
			document.getElementById("editcontent").innerHTML = responseText;	
			loadStepBehavior(currentStep);
		}
		else
		{
			$("#editcontent").html(responseText);
			loadStepBehavior(currentStep);
		}		 			 						 	
		$('#editpartform').ajaxForm(options);
	}	           	           
} 	

function loadStep(objectID, step)
{		
	$.post('/post/editattributes/load/' + objectID + '/' + step, function(data) {		
		  $('#editcontent').html(data);
		  currentStep = step; 
		  if (navigator.appName == 'Microsoft Internet Explorer')
		  {
			  var options = {success: formResponse, iframe:false};
			  if(step == 3)
			  {
				  var options = {success: formResponse};
			  }			  
		  }		
		  else
		  {
			  var options = {success: formResponse};
		  }
		  
		  $('.editstepform').ajaxForm(options);
		  loadStepBehavior(step);
	});		
}


function createMenuSteps(objectID, step, menuSteps)
{			
	var html_ul = '';
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
			if (i == step && checkedSteps[i] == 1 || i == step && checkedSteps[i] == 1 && i == editStepsNumber )
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
			else if (i > step && checkedSteps[i+2] != 1 && i != editStepsNumber)
			{
				class_li = 'class="last_next_step"';
			}
			else if (i == (step + 1) && i != editStepsNumber)
			{
				class_li = 'class="last_next_step"';
			}
			else if (i == 5 && checkedSteps[i-1] == 1 && step != editStepsNumber)
			{
				class_li = 'class="relast_next_step"';
			}
			
			active_li = 1;			
		}
		
		if(active_li)
		{
			content_li = '<a href="#" onclick="loadStep(' + objectID + ',' + i + ');return false;">' + i + '. ' + menuSteps[i] + '</a>';
		}
		
		html_ul = html_ul + '<li '+ class_li +'>'+ content_li +'</li>';
	}
	
	$('.menu_steps').html(html_ul);	
}

function saveStep()
{	
	if(currentStep == 2)
	{
		$('input[id$="latitude"]').attr('value', myMarker.position.lat());
		$('input[id$="longitude"]').attr('value', myMarker.position.lng());
	}	
	saveAction = true;
	$('#editpartform').append("<input type='hidden' name='republish' value='1'>");
	$('#editpartform').submit();
}


