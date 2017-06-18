
/**
 * jQuery Timer isn't natively a stopwatch, it just helps with
 * building one. Time must be incremented manually.
 *
 * The increment time is in milliseconds, so an input time of
 * 1000 equals 1 time per second. 
 *
 * The timer function converts the current time to a string
 * and outputs to the stopwatch element, $stopwatch.
 * 
 * demo:   http://jchavannes.com/jquery-timer/demo
 */

 

	var countdownTimer = new (function() {

    var $countdown;
    var $form;
    var incrementTime = 1000;
    
    //var currentTime = 300000; // 5 minutes (in milliseconds)
    var currentTime = startTime; // startTime is passed from php code (already in milliseconds)

    
    jQuery(function() {

        // Setup the timer
        $countdown = jQuery('#countdown');
        countdownTimer.Timer = jQuery.timer(updateTimer, incrementTime, true);

    });

    function updateTimer() {

		COUNTDOWN_GLOBAL = currentTime;
		
        // Output timer position
		var timeString = formatTime(currentTime);
        $countdown.html(timeString);

        // Increment timer position
        currentTime -= incrementTime;
        if (currentTime < 0) { 
			currentTime = 0;
			COUNTDOWN_GLOBAL = 0;
		}
    }

});


// Common functions
function pad(number, length) {
    var str = '' + number;
    while (str.length < length) {str = '0' + str;}
    return str;
}
function formatTime(time) {
    time = time / 10;
    var min = parseInt(time / 6000),
        sec = parseInt(time / 100) - (min * 60),
        hundredths = pad(time - (sec * 100) - (min * 6000), 2);
    //return (min > 0 ? pad(min, 2) : "00") + ":" + pad(sec, 2) + ":" + hundredths;
    return (min > 0 ? pad(min, 2) : "00") + ":" + pad(sec, 2) ;
}




function submitQTL_quiz()
{
	 document.forms["QTL-form"].submit();
	
}

