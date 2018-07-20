


// This sets a global variable divName window object
// Needed for updating a unique div after update
var divName="";

jQuery.fn.isChildof = function(b){
    return (this.parents(b).length > 0);
};


<!-- layervis - generic toggler for show/hide on any divs by id-->
function toggleLayerVis(id){
if (document.getElementById) {
	if (this.document.getElementById(id).style.display=="none")
		(this.document.getElementById(id).style.display="block") ;
	else
		(this.document.getElementById(id).style.display="none") ;
	}
else if (document.all) {
	if (this.document.all[id].style.display=="none")
		(this.document.all[id].style.display="block") ;
	else
		(this.document.all[id].style.display="none") ;
	}
else if (document.layers) {
	if (this.document.layers[id].style.display=="none")
		(this.document.layers[id].style.display="block") ;
	else
		(this.document.layers[id].style.display="none") ;
	}
}

// A generic HIDE and SHOW stuff so you don't need to worry about toggles
function divDisplayShow(id)
{
	this.document.getElementById(id).style.display="block";
}

function divDisplayHide(id)
{
	this.document.getElementById(id).style.display="none";
}


// Start of Delete popup box code
function hideDiv(divName) { 
if (document.getElementById) { // DOM3 = IE5, NS6 
document.getElementById(divName).style.visibility = 'hidden'; 
} 
else { 
if (document.layers) { // Netscape 4 
document.divName.visibility = 'hidden'; 
} 
else { // IE 4 
document.all.divName.style.visibility = 'hidden'; 
} 
} 
}



function showDiv(divName) {
	if (document.getElementById)
	{ // DOM3 = IE5, NS6 
		document.getElementById(divName).style.visibility = 'visible'; 
	} 
	else
	{ 
		if (document.layers) { // Netscape 4 
		document.divName.visibility = 'visible'; 
		} 
		else { // IE 4 
		document.all.divName.style.visibility = 'visible'; 
		} 
	} 
	
} 




function checkExampleQuestionExampleAnswer(questionID, qType, correctResponse, IDstring, correctFeedbackArray, incorrectFeedbackArray)
{
	
	
	divDisplayHide("exampleQuestionAnswerCorrect"+questionID);
	divDisplayHide("exampleQuestionAnswerInCorrect"+questionID);
	
	var isCorrect=false; // Assume its false for now
	
	if(qType=="reflection" || qType=="reflectionText") // If its reflection ONLY show the correct answer as there is no incorrect answer
	{
		isCorrect=true;
	}
	else
	{
		
		//var correctResponse = JSON.parse(correctResponse);
		
		switch (qType)
		{
			case "radio":
			
				// Get all response and stick into an array
				var optionIDArray = IDstring.split(',');
				
				// get the radio response
				if(document.getElementById("option"+correctResponse).checked)
				{
					isCorrect=true;
					
					// If its correct and radio button type ALL are true so show correct for all
					for (var i = 0; i < optionIDArray.length; i++)
					{	
						currentOptionID = optionIDArray[i];	
						divDisplayShow("correctFeedback"+currentOptionID); // Show the correct feedback
						divDisplayHide("incorrectFeedback"+currentOptionID); // Hide the incorrect feedback
		
					}				
				}
				else // They are ALL wrong
				{
					for (var i = 0; i < optionIDArray.length; i++)
					{	
						currentOptionID = optionIDArray[i];	
						divDisplayShow("incorrectFeedback"+currentOptionID); // Show the incorrect feedback
						divDisplayHide("correctFeedback"+currentOptionID); // Hide the correct feedback
		
					}		
				}			
			
			break;
			
			case "check":
				//Turn the IDstring into an array and go through the array of values
				var optionIDArray = IDstring.split(',');
				var correctIDArray = correctResponse.split(',');			
				var responseArrayStr="";
				var responseArrayLocation="";
				for (var i = 0; i < optionIDArray.length; i++)
				{
					currentOptionID = optionIDArray[i];				
					responseArrayLocation = correctResponse.indexOf(currentOptionID);
	
					if(document.getElementById("option"+optionIDArray[i]).checked)
					{
						if(responseArrayLocation>=0) // It is a correct answer AND is ticked
						{
							// Ticked and correct
							divDisplayShow("correctFeedback"+currentOptionID);
							divDisplayHide("incorrectFeedback"+currentOptionID);
						}
						else
						{
							// ticked and incorrect
							divDisplayShow("incorrectFeedback"+currentOptionID);
							divDisplayHide("correctFeedback"+currentOptionID);						
						}
						
						responseArrayStr=responseArrayStr+optionIDArray[i]+",";					
					}
					else
					{
						if(responseArrayLocation>=0) // Its a correct answer AND is ticked
						{
							// not ticked and incorrect
							divDisplayShow("incorrectFeedback"+currentOptionID);	
							divDisplayHide("correctFeedback"+currentOptionID);											
						}
						else
						{
							// not ticked and correct
							divDisplayShow("correctFeedback"+currentOptionID);	
							divDisplayHide("incorrectFeedback"+currentOptionID);
						}					
					}
				}
				
				// Now remove the last comma
				responseArrayStr = responseArrayStr.slice(0,-1);		
				//alert ("Correct = "+correctResponse+" : Response = "+responseArrayStr);
				
				if(correctResponse==responseArrayStr)
				{
					isCorrect=true;
				}			
			
			break;
			
			case "text":
				//Turn the IDstring into an array and see if the input is in the array
				var optionValueArray = IDstring.split(',');
				var myResponse = document.getElementById("textBoxID"+questionID).value;
				myResponse = myResponse.toLowerCase();
				if(optionValueArray.indexOf(myResponse) > -1)
				{
					isCorrect=true;
				}			
			
			break;
			
			case "blank":
			
				// Start off saying that its actually correct
				isCorrect=true;
				
				var blank_feedback=""; // The main string for returning ALL the feedback to the div
				
				// Get the correct answers and put them into array
				var correctAnswerArray = correctResponse.split('|');
				
				// Get the correct feedback and put it into an array
				var correctFeedbackArray = decodeURI(correctFeedbackArray).split('|');
				
				// Get the incorrect feedback and put it into an array
				var incorrectFeedbackArray = decodeURI(incorrectFeedbackArray).split('|');				
				
				// Get the possible options - the ID str is number of options
				var blankCount = IDstring;
				var blankCorrectTotal = 0;
				
				for (i = 1; i <= blankCount; i++)
				{ 
					var elementName = "blank_"+questionID+"_"+i;
					var myResponse = document.getElementById("blank_"+questionID+"_"+i).value;
					
					if(myResponse==""){isCorrect=false;} // If its blank then they haven't got it all correct

					blank_feedback+="<div style='margin:5px 0px 5px 0px; padding:5px; border:1px solid #ccc'><b>Blank "+i+" : Possible Correct Answers</b><br/>";

					// Lookup the possible answers for this. 
					var correctArray = correctAnswerArray[i-1].split(',');
					
					var correctAnswerString = "<ol>";
					
					// Get the correct words as a string for feedback
					for (var j=0; j < correctArray.length; j++)
					{
						correctAnswerString+="<li>"+correctArray[j]+"</li>";

					}
					correctAnswerString+= "</ol>";

					blank_feedback+=correctAnswerString;
					
					if(!myResponse){myResponseText="None given";}else{myResponseText = myResponse;}
					blank_feedback+= "Your Response : <b>"+myResponseText+"</b><br/>";
					
					if(correctArray.indexOf(myResponse.toLowerCase()) > -1)
					{
						blank_feedback+="<span class='correct'>Correct</span>";
						var thisCorrectFeedack = correctFeedbackArray[i-1];
						//alert("Feedback = "+thisCorrectFeedack);
						if(thisCorrectFeedack)
						{
							blank_feedback+="<div class='correctFeedbackDiv'>"+correctFeedbackArray[i-1]+"</div>";
						}
						blankCorrectTotal++; // increment total blank correct by one
					}
					else
					{
						isCorrect=false;
						blank_feedback+= "<span class='incorrect'>Incorrect</span>";
						var thisIncorrectFeedack = incorrectFeedbackArray[i-1];
						if(thisIncorrectFeedack)
						{					
							blank_feedback+="<div class='incorrectFeedbackDiv'>"+incorrectFeedbackArray[i-1]+"</div>";
						}
						
					}
					
					blank_feedback+="</div>"; // //Close the answer div for this blank
					
				}
				
				blank_feedback = "You got "+blankCorrectTotal+"/"+blankCount+" blanks correct<hr/>"+blank_feedback;

				var div = document.getElementById('blank_feedback_'+questionID);	
				//div.innerHTML = "You got "+blank_feedback+"/"+blank_count+" correct"+blank_feedback;				
				div.innerHTML = blank_feedback;								

			
			break;
			
		}
	}
	
	
	if(qType!="blank")
	{
		if(isCorrect==true)
		{
			divDisplayShow("exampleQuestionAnswerCorrect"+questionID);
		}
		else
		{
			divDisplayShow("exampleQuestionAnswerInCorrect"+questionID);
		}
	}
	
	
	
}



//function ajaxQuestionResponseUpdate(elementID, questionID, currentUser)
function ajaxQuestionResponseUpdate(elementID, questionID, IDStr, qType, currentUser)
{
	var userResponse = '';
	//if is refection question with text input, save it to the userResponse string for update
	if (qType=='reflectionText'){
		userResponse = document.getElementById(elementID).value;
	}else{
		//if is single or multi response question, save the selected optionID(s) to the userResponse string for update
		var optionIDArray = IDStr.split(',');

		for (var i = 0; i < optionIDArray.length; i++)
		{		
			//currentOptionID = optionIDArray[i];	
			if(document.getElementById("option"+optionIDArray[i]).checked){
				
				userResponse = userResponse + optionIDArray[i] + ',';
			}
		}		
	}
	
	//alert('test');		
//	alert (userResponse);
	
	// We need question ID AND the logged in user AND the value passed to the beneath query	
	jQuery.ajax({
		type: 'POST',
		url: ajaxurl,
		data: {			
			"action": "addResponseToDatabase",
			"userResponse": userResponse,
			"currentUser": currentUser,
			"questionID": questionID
		},
		success: function(data){}
	});
	
	return false;		
	
}

 // used with the tabs to determine the initial page based on ?tab=1 query string
function getParam(name) {
    var query = location.search.substring(1);
    if (query.length) {
        var parts = query.split('&');
        for (var i = 0; i < parts.length; i++) {
            var pos = parts[i].indexOf('=');
            if (parts[i].substring(0,pos) == name) {
                return parts[i].substring(pos+1);
            }
        }
    }
    return 1;
}

// extend text box as you type

function resizeInput() {
    jQuery(this).attr('size', jQuery(this).val().length);
}

jQuery('input[type="text"]')
    // event handler
    .keyup(resizeInput)
    // resize on page load
    .each(resizeInput);