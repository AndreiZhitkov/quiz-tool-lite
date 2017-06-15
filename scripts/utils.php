<?PHP

define ("PHP_TRUE",  1);
define ("PHP_FALSE", 0); 


if (!class_exists('qtl_utils'))
{
	class qtl_utils
	{
	
		/** 
		 * Converts a DB date format to an array with variety of format options
		 * @param myDate  - A DB date
		 * @return - An array with a variety of date formats
		 */
		public static function formatDate($myDate)
		{
			$myDate = strtotime($myDate);			
			//Formats the Date
			$dateArray[0] = date('jS F Y', $myDate);
			$dateArray[1] = date('j/m/Y', $myDate);
			$dateArray[2] = date('jS M Y g:i a', $myDate);	
			$dateArray[3] = date('j/m/Y g:i a', $myDate);
			$dateArray[4] = date('jS M Y', $myDate);			
			
			return $dateArray;
		}
		
		
		
		public static function getCurrentDate()
		{
			$date = date("Y-m-d H:i:s");
			return $date;
		}
		/**
		Validate an email address.
		Provide email address (raw input)
		Returns true if the email address has the email 
		address format and the domain exists.
		*/
		public static function validEmail($email)
		{
		   $isValid = true;
		   $atIndex = strrpos($email, "@");
		   if (is_bool($atIndex) && !$atIndex)
		   {
			  $isValid = false;
		   }
		   else
		   {
			  $domain = substr($email, $atIndex+1);
			  $local = substr($email, 0, $atIndex);
			  $localLen = strlen($local);
			  $domainLen = strlen($domain);
			  if ($localLen < 1 || $localLen > 64)
			  {
				 // local part length exceeded
				 $isValid = false;
			  }
			  else if ($domainLen < 1 || $domainLen > 255)
			  {
				 // domain part length exceeded
				 $isValid = false;
			  }
			  else if ($local[0] == '.' || $local[$localLen-1] == '.')
			  {
				 // local part starts or ends with '.'
				 $isValid = false;
			  }
			  else if (preg_match('/\\.\\./', $local))
			  {
				 // local part has two consecutive dots
				 $isValid = false;
			  }
			  else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain))
			  {
				 // character not valid in domain part
				 $isValid = false;
			  }
			  else if (preg_match('/\\.\\./', $domain))
			  {
				 // domain part has two consecutive dots
				 $isValid = false;
			  }
			  else if
		(!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/',
						 str_replace("\\\\","",$local)))
			  {
				 // character not valid in local part unless 
				 // local part is quoted
				 if (!preg_match('/^"(\\\\"|[^"])+"$/',
					 str_replace("\\\\","",$local)))
				 {
					$isValid = false;
				 }
			  }
			  if ($isValid && !(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A")))
			  {
				 // domain not found in DNS
				 $isValid = false;
			  }
		   }
		   return $isValid;
		}
		
		public static function br2nl($text, $tags="br")
		{
			$tags = explode(" ", $tags);
			
				foreach($tags as $tag)
				{
					$text = eregi_replace("<" . $tag . "[^>]*>", "\n", $text);
					$text = eregi_replace("]*>", "\n", $text);
				}
			
			return($text);
		}
		
		
		public static function truncateText($string, $max = 20, $replacement = '')
		{
			if (strlen($string) <= $max)
			{
				return $string;
			}
			$leave = $max - strlen ($replacement);
			return substr_replace($string, $replacement, $leave);
		}
		
		public static function limitWords($str, $limit = 100, $end_char = '&#8230;') {
			
			if (trim($str) == '')
				return $str;
			
			preg_match('/\s*(?:\S*\s*){'. (int) $limit .'}/', $str, $matches);
		
			if (strlen($matches[0]) == strlen($str))
				$end_char = '';
		
			return rtrim($matches[0]) . $end_char;
		}
		
		
		
		
		public static function convertTextFromDB($input)
		{
			$input = stripslashes($input);
			$input = html_entity_decode($input, ENT_QUOTES, 'UTF-8');	
			
			return $input;
		}
		
		public static function dateDiff($startDate, $endDate)
		{
			$secondsSinceResponse = $endDate-$startDate;
			
			$daysResponse = $secondsSinceResponse / 86400;
			$daysResponse = number_format($daysResponse, 0);
			
			$daysResponse = $secondsSinceResponse / 86400;
			$daysResponse = floor($daysResponse);
			
			$temp_remainder = $secondsSinceResponse - ($daysResponse * 86400);
			$hours = floor($temp_remainder / 3600);
			
			$temp_remainder = $temp_remainder - ($hours * 3600);
			$minutes = floor($temp_remainder / 60);
			
			$seconds = $temp_remainder - ($minutes * 60);
			if($daysResponse==0)
			{
				if($hours==0)
				{
					if($minutes==0)
					{
						$dateDiff = $secondsSinceResponse.' seconds';
					}
					else
					{
						$dateDiff= $minutes.' minute(s), '.$seconds.' seconds';
					}
				}
				else
				{
					$dateDiff= $hours.' hour(s), '.$minutes.' minutes, '.$seconds.' seconds';			
				}
		
			}
			else
			{
				$dateDiff=$daysResponse.' day(s), '.$hours.' hour, '.$minutes.' minutes, '.$seconds.' seconds';
			}
			
			return $dateDiff;
		}
		
		/**
		 * Returns a random string of numbers and letters [0-9][A-Z]
		 * Note: This is not secure random
		 * @param $chars The number of characters in the random string
		 * @return String containing random numbers and letters
		 */
		public static function randomString ($chars)
		{
			$randStr = "";
			
			while ($chars > 0) {
				$ord = rand(48, 90);
				if (($ord >= 48 && $ord <=57) || ($ord >= 65 && $ord <=90)) {
					$randStr .= chr($ord);
					$chars -= 1;
				}
			}
		
			return $randStr;
		}
		
		
	
		public static function getCurrentUsername() {
			// Get the current user's info 
			$current_user = wp_get_current_user(); 
		
			if ( !($current_user instanceof WP_User) ) 
				return; 
		
			return $current_user->user_login; 
		}	
		
	
		/**
		 * Returns the file extension of the given filename
		 * E.g. 'Test.php' --> 'php'
		 * @param $fname The filename to chop
		 * @return String containing the file extension
		 */
		public static function getFileExtension ($fname)
		{
				//$i = strpos($fname, '.'); 
				//return (substr($fname, $i+1));	
		
				return strtolower(substr(strrchr($fname, "."), 1));
		}	
		
		/**
		 * Returns a string with http on the front if it does not exist. Used for redirection after quiz
		 * @param $url The string to parse
		 * @return String containing the correct http
		 */
		public static function addhttp($url)
		{
			if (!preg_match("~^(?:f|ht)tps?://~i", $url))
			{
				$url = "http://" . $url;
			}
			return $url;
		}	
		
		public static function loadDatatables()
		{
			wp_register_script( 'datatables', ( 'https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js' ), false, null, true );
			wp_enqueue_script( 'datatables' );
			
			//dataTables css
			wp_enqueue_style('datatables-style','https://cdn.datatables.net/1.10.13/css/dataTables.jqueryui.min.css');
				
			global $wp_scripts;
			// get the jquery ui object
			$queryui = $wp_scripts->query('jquery-ui-core');
			 
			// load the jquery ui theme
			$url = "https://ajax.googleapis.com/ajax/libs/jqueryui/".$queryui->ver."/themes/smoothness/jquery-ui.css";	
			wp_enqueue_style('jquery-ui-smoothness', $url, false, null);	
			
			wp_register_style( 'QTL_css_custom',  plugins_url('../css/qtl-styles.css',__FILE__) );
			wp_enqueue_style( 'QTL_css_custom' );			
		}
		
		
		
		// Uses the colorMeter below
		public static function generateRedGreenColourArray($stepCount)
		{
			
			if($stepCount==0)
			{
				$colourArray = 	array();
			}
			elseif($stepCount==1)
			{
				$colourArray = array("#00820B");
			}
			else		
			{
				$percentStep = 1/($stepCount-1); // Subtract 1 from ttola step count for some reason :( eeek 
				
				$colourArray = array();
				for ($i = 0.0; $i <= 1.0; $i += $percentStep)
				{
					$RGB = qtl_utils::colorMeter($i);
					$colourArray[] = '#'.$RGB;		
				}
			}
			return $colourArray;
		}
	
		// Returns an array of gradient colours based on starting colour and ending colour and steps between red and green
		public static function colorMeter($percent, $invert = false)
		{
			//$percent is in the range 0.0 <= percent <= 1.0
			//    integers are assumed to be 0% - 100%
					 // and are converted to a float 0.0 - 1.0
			//     0.0 = red, 0.5 = yellow, 1.0 = green
			//$invert will make the color scale reversed
			//     0.0 = green, 0.5 = yellow, 1.0 = red 
			
			//convert (int)% values to (float)
			if (is_int($percent)) $percent = $percent * 0.01;
			
			$R = min((2.0 * (1.0-$percent)), 1.0) * 210.0;
			$G = min((2.0 * $percent), 1.0) * 130.0;
			$B = 11;
			
			return (($invert) ? 
		sprintf("%02X%02X%02X",$G,$R,$B) 
		: sprintf("%02X%02X%02X",$R,$G,$B)); 
		} 		
		
	
		
		public static function getQTL_IconArray()
		{
		
			// Get the contents of the image dir
			$iconDir = AIQUIZ_DIR.'images/icons/correct/';
			
			$imageArray = array();
			//path to directory to scan
			$myIcons = scandir($iconDir);
			foreach($myIcons as $imageRef)
			{
				if($imageRef != "." && $imageRef != "..") 
				{
					$imageArray[]= $imageRef;
				}
			}	
			//$imageArray = asort($imageArray);
			return $imageArray;
		}	
	}
}
?>