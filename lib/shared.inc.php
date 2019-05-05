<?php

// part of FREMO Yellow Pages @ g-zi.de/FYP

if (isset($_GET['tpa_id']))
{	// SSO definition and database connection
	require_once('sso/FypTypo3Sso.php');
	require_once('sso/Autologin.php');

	if (SsoSignatureVerifier::ssoSignatureIsValid(
	$_GET['version'], $_GET['user'], $_GET['tpa_id'], $_GET['expires'], $_GET['action'],
	$_GET['flags'], $_GET['userdata'], $_GET['signature']))
	{
		$userdata = SsoSignatureVerifier::UnpackUserData($_GET['userdata']);
		$username = $_GET['user'];
		$loggedInUser = Autologin::GetLoggedInExistingOrNewUserFromSsoData($username, $userdata['email']);
        $_SESSION["sso"] = 1;
	}
	else
	{
		echo "Can't trust authenticity of claims provider";
		die();
	}
}


function escape($string) {
    return preg_replace('~[\x00\x0A\x0D\x1A\x22\x25\x27\x5C]~u', '\\\$0', $string); // \x5F = _
}


function sql_error()
{
  if ($dbtype='mysqli')
    return @mysqli_error($db);
  else
    return @mysql_error();
}


function getVariableFromString($key)
{	// filter special characters
	$value = str_replace('\r\n', "\n", htmlspecialchars_decode(escape(stripslashes(htmlspecialchars($key)))));
	return $value;
}


function getVariableFromQueryStringOrSession($key)
{	// Retrieve the URL variables (using PHP).

	if(!isset($_SESSION)) session_start();

	$value = null;
	if(isset($_GET[$key])) {
		$value = str_replace('\r\n', "\n", htmlspecialchars_decode(escape(stripslashes(htmlspecialchars($_GET[$key])))));
		$_SESSION[$key] = $value;
	} 
	elseif(isset($_POST[$key])) {
		$value = str_replace('\r\n', "\n", htmlspecialchars_decode(escape(stripslashes(htmlspecialchars($_POST[$key])))));
		$_SESSION[$key] = $value;
	} 
	elseif(isset($_SESSION[$key])) {
		$value = $_SESSION[$key];
	}
	if ($value!=$key) return $value;
}


function countLines($Text, $Width)
{
	$Text = wordwrap($Text, $Width);
	$lines = count(explode("\r\n", $Text));
	if($lines <= '3') $lines = '3';
	return $lines;
}


function readCell($objPHPExcel, $adr)
{
	$cell = escape(stripslashes(htmlspecialchars($objPHPExcel->getActiveSheet()->getCell($adr)->getCalculatedValue())));
	return $cell;
}


function sendMail($email,$subject,$msg)
{
//	global $websiteName,$emailAddress;
		
	$header = 'From: fyp@g-zi.de' . "\r\n" .
    'Reply-To: g.zi@gmx.de' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();	

	return mail($email,$subject,$msg,$header);
}


function HexToRGB($hex)
{
	$hex = preg_replace("/#/", "", $hex);
	$color = array();
	
	if(strlen($hex) == 3) {
		$color['r'] = hexdec(substr($hex, 0, 1) . $r);
		$color['g'] = hexdec(substr($hex, 1, 1) . $g);
		$color['b'] = hexdec(substr($hex, 2, 1) . $b);
	}
	else if(strlen($hex) == 6) {
		$color['r'] = hexdec(substr($hex, 0, 2));
		$color['g'] = hexdec(substr($hex, 2, 2));
		$color['b'] = hexdec(substr($hex, 4, 2));
	}
	return $color;
}


function RGBToHex($r, $g, $b) 
{
	$hex = "#";
	$hex.= str_pad(dechex($r), 2, "0", STR_PAD_LEFT);
	$hex.= str_pad(dechex($g), 2, "0", STR_PAD_LEFT);
	$hex.= str_pad(dechex($b), 2, "0", STR_PAD_LEFT);
	
	return $hex;
}


function isIE()
{	// Returns the version of Internet Explorer or false
    $isIE = preg_match("/MSIE ([0-9]{1,}[\.0-9]{0,})/",$_SERVER['HTTP_USER_AGENT'],$version);
    if($isIE){
        return $version[1];
    }
    return $isIE;
}

function isMozilla()
{	// Returns the version of Internet Explorer or false
    $isMozilla = preg_match("/Mozilla ([0-9]{1,}[\.0-9]{0,})/",$_SERVER['HTTP_USER_AGENT'],$version);
    if($isMozilla){
        return $version[1];
    }
    return $isMozilla;
}

function getagent() 
{ 
  if (strstr($_SERVER['HTTP_USER_AGENT'],'Opera'))    {     
   
     $brows=ereg_replace(".+\(.+\) (Opera |v){0,1}([0-9,\.]+)[^0-9]*","Opera \\2",$_SERVER['HTTP_USER_AGENT']); 
     if(ereg('^Opera/.*',$_SERVER['HTTP_USER_AGENT'])){ 
     $brows=ereg_replace("Opera/([0-9,\.]+).*","Opera \\1",$_SERVER['HTTP_USER_AGENT']);    }} 
  elseif (strstr($_SERVER['HTTP_USER_AGENT'],'MSIE')) 
     $brows=ereg_replace(".+\(.+MSIE ([0-9,\.]+).+","Internet Explorer \\1",$_SERVER['HTTP_USER_AGENT']); 
  elseif (strstr($_SERVER['HTTP_USER_AGENT'],'Firefox')) 
     $brows=ereg_replace(".+\(.+rv:.+\).+Firefox/(.*)","Firefox \\1",$_SERVER['HTTP_USER_AGENT']); 
  elseif (strstr($_SERVER['HTTP_USER_AGENT'],'Mozilla')) 
     $brows=ereg_replace(".+\(.+rv:([0-9,\.]+).+","Mozilla \\1",$_SERVER['HTTP_USER_AGENT']); 
  else 
     $brows=$_SERVER['HTTP_USER_AGENT']; 
  return $brows; 
}  


/**
 * Calculate relative luminance in sRGB colour space for use in WCAG 2.0 compliance
 * @link http://www.w3.org/TR/WCAG20/#relativeluminancedef
 * @param string $col A 3 or 6-digit hex colour string
 * @return float
 * @author Marcus Bointon <marcus@synchromedia.co.uk>
 */
function relativeluminance($col) 
{
    //Remove any leading #
    $col = trim($col, '#');
    //Convert 3-digit to 6-digit
    if (strlen($col) == 3) {
        $col = $col[0] . $col[0] . $col[1] . $col[1] . $col[2] . $col[2];
    }
    //Convert hex to 0-1 scale
    $components = array(
        'r' => hexdec(substr($col, 0, 2)) / 255,
        'g' => hexdec(substr($col, 2, 2)) / 255,
        'b' => hexdec(substr($col, 4, 2)) / 255
    );
    //Correct for sRGB
    foreach($components as $c => $v) 
    {
        if ($v <= 0.03928) $components[$c] = $v / 12.92;
        else $components[$c] = pow((($v + 0.055) / 1.055), 2.4);
    }
    //Calculate relative luminance using ITU-R BT. 709 coefficients
    return ($components['r'] * 0.2126) + ($components['g'] * 0.7152) + ($components['b'] * 0.0722);
}


/**
 * Calculate contrast ratio acording to WCAG 2.0 formula
 * Will return a value between 1 (no contrast) and 21 (max contrast)
 * @link http://www.w3.org/TR/WCAG20/#contrast-ratiodef
 * @param string $c1 A 3 or 6-digit hex colour string
 * @param string $c2 A 3 or 6-digit hex colour string
 * @return float
 * @author Marcus Bointon <marcus@synchromedia.co.uk>
 */
function contrastratio($c1, $c2) {
    $y1 = relativeluminance($c1);
    $y2 = relativeluminance($c2);
    //Arrange so $y1 is lightest
    if ($y1 < $y2) {
        $y3 = $y1;
        $y1 = $y2;
        $y2 = $y3;
    }
    return ($y1 + 0.05) / ($y2 + 0.05);
}


/*	Google-Translate-PHP
	====================
	
	Google Translate API free PHP class. Translates totally free of charge.
	
	## Usage
	
	Instantiate GoogleTranslate object
	```php
	$tr = new GoogleTranslate("en", "ka");
	```
	or set/change languages later
	```php
	$tr = new GoogleTranslate();
	$tr->setLangFrom("en");
	$tr->setLangTo("ka");
	```
	translate sentences
	```php
	echo $tr->translate("Hello World!");
	```
	Also, you can use shorter syntax:
	```php
	echo $tr->setLangFrom("en")->setLangTo("ru")->translate("Goodbye");
	```
	Or call a static method
	```php
	echo GoogleTranslate::staticTranslate("Hello again", "en", "ka");
	```
*/
/**
 * Google Translate PHP class
 *
 * @author      Levan Velijanashvili <me@stichoza.com>
 * @link        http://stichoza.com/
 * @version     1.3.0
 * @access      public
 */
class GoogleTranslate {
    
    /**
     * Last translation
     * @var string
     * @access private
     */
    public $lastResult = "";
    
    /**
     * Language translating from
     * @var string
     * @access private
     */
    private $langFrom;
    
    /**
     * Language translating to
     * @var string
     * @access private
     */
    private $langTo;
    
    /**
     * Google Translate URL format
     * @var string
     * @access private
     */
    private static $urlFormat = "http://translate.google.com/translate_a/t?client=t&text=%s&hl=en&sl=%s&tl=%s&ie=UTF-8&oe=UTF-8&multires=1&otf=1&pc=1&trs=1&ssel=3&tsel=6&sc=1";

    /**
     * Class constructor
     * 
     * @param string $from Language translating from (Optional)
     * @param string $to Language translating to (Optional)
     * @access public
     */
    public function __construct($from = "en", $to = "ka") {
        $this->setLangFrom($from)->setLangTo($to);
    }

    /**
     * Set language we are transleting from
     * 
     * @param string $from Language code
     * @return GoogleTranslate
     * @access public
     */
    public function setLangFrom($lang) {
        $this->langFrom = $lang;
        return $this;
    }
    
    /**
     * Set language we are transleting to
     * 
     * @param string $to Language code
     * @return GoogleTranslate
     * @access public
     */
    public function setLangTo($lang) {
        $this->langTo = $lang;
        return $this;
    }
    
    /**
     * Simplified curl method
     * @param string $url URL
     * @param array $params Parameter array
     * @param boolean $cookieSet
     * @return string
     * @access public
     */
    public static final function makeCurl($url, array $params = array(), $cookieSet = false) {
        if (!$cookieSet) {
            $cookie = tempnam(sys_get_temp_dir(), "CURLCOOKIE");
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $output = curl_exec($ch);

            // Clean up temporary file
            unset($ch);
            unlink($cookie);

            return $output;
        }
        
        $queryString = http_build_query($params);

        $curl = curl_init($url . "?" . $queryString);
        curl_setopt($curl, CURLOPT_COOKIEFILE, $cookie);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($curl);
        
        return $output;
    }

    /**
     * Translate text
     * 
     * @param string $string Text to translate
     * @return string/boolean Translated text
     * @access public
     */
    public function translate($string) {
        return $this->lastResult = self::staticTranslate($string, $this->langFrom, $this->langTo);
    }

    /**
     * Static method for translating text
     * 
     * @param string $string Text to translate
     * @param string $from Language code
     * @param string $to Language code
     * @return string/boolean Translated text
     * @access public
     */
    public static function staticTranslate($string, $from, $to) {
        $url = sprintf(self::$urlFormat, rawurlencode($string), $from, $to);
        $result = preg_replace('!,+!', ',', self::makeCurl($url)); // remove repeated commas (causing JSON syntax error)
        $resultArray = json_decode($result, true);
        $finalResult = "";
        if (!empty($resultArray[0])) {
            foreach ($resultArray[0] as $results) {
                $finalResult .= $results[0];
            }
            return $finalResult;
        }
        return false;
    }
}


function translates($string, $langIn, $langOut)  
{	// prerequisit for google translation
	if($langOut=="") $langOut = 'en';
	$tr = new GoogleTranslate($langIn, $langOut);

	$string = rawurldecode($string);
	$string = preg_replace("/&nbsp;/", " ",$string);
	$string = preg_replace("/&amp;/", "&",$string);

	if(strlen($string)>1200) 
	{
		$string = explode('.',$string);
		foreach($string as $value)
		{		
			$str = $str."<br>".$tr->translate($value).".";
		}
		return $str;
	}
	else $tr->translate($string);
	
	return $tr->lastResult;
}

function translategoogle($string, $langIn, $langOut)  
{	// prerequisit for google translation


	if($langOut=="") $langOut = 'en';
	$tr = new GoogleTranslate($langIn, $langOut);

	$string = rawurldecode($string);
	$string = preg_replace("/&nbsp;/", " ",$string);
	$string = preg_replace("/&amp;/", "&",$string);

	if(strlen($string)>1200) 
	{
		$string = explode('.',$string);
		foreach($string as $value)
		{		
			$str = $str."<br>".$tr->translate($value).".";
		}
		return $str;
	}
	else $tr->translate($string);
//echo $langIn.$langOut.$string;

	return $tr->lastResult;
}


function translatepl($string, $langIn, $langOut)  
{	// this is a temporary solution for translating 
	if($langOut=="") $langOut = 'en';

	//$url = 'http://mania.hekko.pl/demos/translator/?fl=auto&tl=en&text=Nábytek+dřevěný&submit=Translate';	
	$url = 'http://mania.hekko.pl/demos/translator/?fl='.$langIn.'&tl='.$langOut.'&text='.$string.'.&submit=Translate';

	$ch = curl_init( $url );
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
	$response = curl_exec( $ch );
	curl_close($ch); 
	$filterResponse = substr( $response, strpos($response, '<div style="width: auto; float: left; font-size: 2em;">')+60 );
	$translation = trim(substr( $filterResponse, 0, strpos($filterResponse, '</div>')-1));
	
	return $translation;
}

function translatetest($string, $langIn, $langOut)  
{	// this is a temporary solution for translating 
	if($langOut=="") $langOut = 'en';

	//$url = 'http://mania.hekko.pl/demos/translator/?fl=auto&tl=en&text=Nábytek+dřevěný&submit=Translate';	
	$url = 'https://translate.google.com/#'.$langIn.'/'.$langOut.'/'.$string;

	$ch = curl_init( $url );
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
	$response = curl_exec( $ch );
	curl_close($ch); 
	$filterResponse = substr( $response, strpos($response, '<div style="width: auto; float: left; font-size: 2em;">')+60 );
	$translation = trim(substr( $filterResponse, 0, strpos($filterResponse, '</div>')-1));
	
	return $translation;
}

