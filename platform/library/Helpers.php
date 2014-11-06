<?php
/**
 * Booty
 * @version: v1.0.0
 * @author: Andy Gulley
 *
 * Created by Andy Gulley. Please report any bug at http://github.com/flyandi/booty
 *
 * Copyright (c) 2014 Andy Gulley http://github.com/flyandi
 *
 * The MIT License (http://www.opensource.org/licenses/mit-license.php)
 *
 * Permission is hereby granted, free of charge, to any person
 * obtaining a copy of this software and associated documentation
 * files (the "Software"), to deal in the Software without
 * restriction, including without limitation the rights to use,
 * copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following
 * conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
 * OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
 * OTHER DEALINGS IN THE SOFTWARE.
 */


/** 
 * (Constants) 
 */
	
# General
define("TAG_NOVALUE", null);


/*** 
 **
 ** Helpers: Environment
 **
 **/

/** 
 * (macro) DefaultValue 
 * Checks the given value and returns an alternative if not passed
 *
 * @param value 			The value to check
 * @param default 			A default value
 */ 

function DefaultValue($value, $default = null){
	if (empty($value) || (is_string($value)&&strlen(trim($value))==0) || $value===null) return $default;
	// return value
	return $value;
}


/** 
 * (macro) GetVar 
 * returns a variable from the environment stack
 *
 * @param name 			 	The name of the variable
 * @param default 			A default value
 */ 

function GetVar($name, $default = null) {
	// cycle environment buckets
	foreach(array($_REQUEST, $_COOKIE, $_GET, $_POST, $GLOBALS) as $x=>$n) {
		if(isset($n[$name])) return $x == 2 ? urldecode($n[$name]) : $n[$name];
	}
	// nothing matched
	return $default;
}	


/** 
 * (macro) SetVar 
 * returns a variable from the environment stack
 *
 * @param name 			 	The name of the variable
 * @param value 			The value
 */ 

function SetVar($name, $value = null) {
	$GLOBALS[$name] = $value;
}		


/** 
 * (macro) GetVarEx
 * returns a variable from a variable stack and then environment
 *
 * @param name 			 	The name of the variable
 * @param variables 		A stack of variables
 * @param default 			A default value
 */ 

function GetVarEx($name, $variables = false, $default = null) {
	return $variables?DefaultValue(@$variables[$name], $default):GetVar($name, $default);
}


/** 
 * (macro) GetSecureVar 
 * reads a variable only from the globals which can't be modified from outside.
 *
 * @param name 			 	The name of the variable to read
 * @param default 			A default value
 */ 

function GetSecureVar($name, $default = "") {
	if (isset($GLOBALS[$name])) return @$GLOBALS[$name];
	return $default;
}		


/** 
 * (macro) GetDirVar 
 * reads the index name of the request URL
 *
 * @param index 			Index/Position of Location
 * @param default 			A default value
 * @param path 				An alternative path
 */ 
function GetDirVar($index=0, $default = null, $path = false) {
	// read defaults
	$path = $path ? $path : GetServerVar("REQUEST_URI");
	// verify
	if(strlen($path) != 0) {
		// prepare the path
		$r = explode("?", $path);
		// split
		$d = explode("/", $r[0]);
		// return value
		return @DefaultValue(strtolower(@$d[$index+1]), $default);
	}
	return $default;
}

/** 
 * (macro) GetRequest
 * returns the full current request
 *
 * @param start 		The beginning index
 * @param prepend 		If set to true, will add a slash before the path
 */ 

function GetRequest($index = 0, $prepend = true){
	// initialize
	$result = false;
	// parse request
	if($request = GetServerVar("REQUEST_URI", false)) {
		// adjust index
		$index += 1;
		// prepare
		$d = explode("/", $request, $index + 1); // < 1 ? 1 : $index);
		// check 
		if(is_array($d) && isset($d[$index])) {
			$result = $d[$index];
		}
	}
	// return result
	return ($prepend && substr($result, 0, 1) != "/" ? "/" : "") . $result;
}	


/** 
 * (macro) GetServerVar
 * returns a variable from the server variable stack
 *
 * @param name 			 	The name of the variable
 * @param default 			A default value
 */ 

function GetServerVar($name = REQUEST_URI, $default = null) {
	return isset($_SERVER[$name])?$_SERVER[$name]:$default;
}

/** 
 * (macro) SetServerVar
 * sets a variable in the server variable stack
 *
 * @param name 			 	The name of the variable
 * @param value 			The new value
 */

function SetServerVar($name, $value) {
	$_SERVER[$name] = $value;
}

/** 
 * (macro) GetHTTPVar
 * returns a variable from the incoming HTTP stack
 *
 * @param name 			 	The name of the variable
 * @param default 			A default value
 */

function GetHTTPVar($name, $default = null) {
	if(function_exists("getallheaders")) {
		$headers = getallheaders();
		return isset($headers[$name])?$headers[$name]:$default;	
	}
	return $default;
}

/** 
 * (macro) AppVar
 * returns a variable from the app stack
 *
 * @param name 			 	The name of the variable
 * @param default 			A default value
 */

function AppVar($name, $default = null) {
	return defined($name)?constant($name):$default;
}

/** 
 * (macro) IfAppVar
 * If condition to check for an app stack variable
 *
 * @param name 			 	The name of the variable
 * @param is  				Check against
 */

function IfAppVar($name, $is = null) {
	// get var
	$d = StringToBool(AppVar($name, false));
	// check
	return $d&&$d==$is;
}

/** 
 * (macro) ClearVar
 * Clears off a variable if possible
 *
 * @param name 			 	The name of the variable
 */

function ClearVar($name){
	unset($_COOKIE[$name]);
	unset($_GET[$name]);
	unset($_POST[$name]);
}	



/*** 
 **
 ** Helpers: Network/HTTP
 **
 **/


/** 
 * (macro) GetQueryString
 * Converts a string to it's boolean representation
 *
 * @param s 				A boolean string
 */ 

function GetQueryString($asarray = true, $withqm = false, $default = "", $fromstring = false) {
	$q = $fromstring!==false?$fromstring:GetServerVar("QUERY_STRING", false);
	if($q!==false&&strlen($q)>0) {
		if($asarray) {
			//return array_explodevalues(str_replace("&amp;", "&", $q), "&", "=");
		}
		return sprintf("%s%s", $withqm?"?":"", $q);
	}
	return $default;
}


/*** 
 **
 ** Helpers: JSON
 **
 **/

/** 
 * (macro) JSJSONDecode
 * Decodes a javascript like JSON string
 *
 * @param s 				Any JSON strin
 * @param assoc 			Set true to return a object
 */ 

function JSJSONDecode($json, $assoc = false) {
	return json_decode(preg_replace('/([{,])(\s*)([^"]+?)\s*:/','$1"$3":',str_replace(array("\n","\r"),"",$json)), $assoc);
}


/*** 
 **
 ** Helpers: Variables 
 **
 **/

/** 
 * (macro) FillVariableString
 * Replaces all a variable string with values
 *
 * @param string 			Any JSON strin
 * @param assoc 			Set true to return a object
 */ 

function FillVariableString($string, $data, $simplematch = false, $st = VARIABLE_FIELD_BEGIN, $et = VARIABLE_FIELD_END) {
	// cycle data
	foreach(is_array($data)?$data:Array() as $name=>$value) {
		if(is_string($value)) {
			// template field
			$string = str_replace($simplematch ? $name : sprintf("%s%s%s", $st, $name, $et), $value, $string);
		}
	}

	return $string;
}

/*** 
 **
 ** Helpers: Arrays 
 **
 **/


/** 
 * (macro) Extend
 * Extends an array like the jQuery $.extend function 
 *
 * @param <multiple>	As many arrays
 */ 

function Extend() {
	// initialize result
	$result = array();

	// cycle
	foreach(func_get_args() as $arr) {
		if(is_array($arr)) {
			$result = array_merge($result, $arr);
		}
	}

	// return result
	return $result;
}


/** 
 * (macro) TraverseArray
 * Traverses an array with filters
 *
 * @param input 			Any array
 * @param handler 			The handling function
 */ 

function TraverseArray($input, $handler) {	
	// prepre array
	if(is_object($input)) $input = (array) $input;
	// sanity check
	if(!is_array($input)) return false;

	// cycle
	foreach($input as $key=>$value) {
		// prepare
		switch(true) {
			case is_object($key) || is_array($key): 
				TraverseArray($key, $handler);
				break;
			case is_object($value) || is_array($value): 
				TraverseArray($value, $handler);
				break;
			default:
				$handler($key, $value);
				break;
		}
	}
}



/*** 
 **
 ** Helpers: Strings 
 **
 **/


/** 
 * (macro) Compare
 * compares two strings
 *
 * @param a 				The first string
 * @param b 				The second string
 * @param strict 			Needs to match exactly 
 */ 

function Compare($a, $b, $strict = false) {
	return $strict ? $a === $b : (strtolower($a) == strtolower($b));
}


/** 
 * (macro) StringToBool
 * Converts a string to it's boolean representation
 *
 * @param s 				A boolean string
 */ 

function StringToBool($s) {
	return in_array(strtolower($s), array("1", "true", "on", "+")) ? true : false;
}


/** 
 * (macro) StripWhitespace
 * Removes all whitespace from the file
 *
 * @param source 			The source text
 * @param stripbreaklines	Set true to remove breaklines as well
 * @param stripcomments		Set true to remove comments as well
 */ 
function StripWhitespace($source, $stripbreaklines = true, $stripcomments = false) {
	// replace
	foreach(array(
		"/\" \"(?=[^\]]*?(?:\"|$))/",
		$stripbreaklines ? "/\r\n\t/" : false,
		$stripcomments ? "!/\*[^*]*\*+([^/][^*]*\*+)*/!" : false,
	) as $pr) {
		if($pr) 
			$source = preg_replace($pr, "", $source);

	}
	// return
	return $source;
}


/** 
 * (macro) IsLowerCase
 * Checks if the string is all lower case
 *
 * @param s 				The source string
 */ 
function IsLowerCase($s) {
	return strtolower($s)===$s;
}





# -------------------------------------------------------------------------------------------------------------------
# GetPageRequest, returns the page that is requested
function GetPageRequest() {
	$r = false;
	if($request = GetServerVar("REQUEST_URI", false)) {
		$r = explode("?", $request);
	}
	return is_array($r)&&isset($r[0])?$r[0]:false;
}

# -------------------------------------------------------------------------------------------------------------------
# GetRequestVar, returns the request string after a dir-var
function GetRequestVar($i=0, $default = false, $nq = false, $rt = false){
	$result = false;
	if($request = GetServerVar("REQUEST_URI", false)) {
		$r=""; $d=explode("/", @$_SERVER['REQUEST_URI'], $i);
		if($i < 1) $i = 1;
		for($x=$i-1;$x<count($d);$x++){ $r.=sprintf("%s%s", $rt?"":"/", $d[$x]);}
		$result = strlen($r)!=0?$r:$default;
		if($nq) {
			$result = explode("?", $result);
			$result = $result[0];
		}
	}
	return $result;
}	

# -------------------------------------------------------------------------------------------------------------------
# RemoveSlashs
function RemoveSlashs($s, $fromback = true, $c = "/") {
	$s = $fromback?strrev($s):$s;
	while(substr($s, 0, 1)=="/") {
		$s = substr($s, 1);
	}
	return $fromback?strrev($s):$s;
}

# -------------------------------------------------------------------------------------------------------------------
# GetRequestString, returns the current request
function GetRequestString($notrail = false, $noparameters = false, $onlyparameters = false, $querymarker = false) {
	$v = GetServerVar("REQUEST_URI");
	if($noparameters || $onlyparameters) {
		$v = explode("?", $v);
		$v = $v[0];
		// check onlyparameters
		if($onlyparameters) {
			return isset($v[1])?sprintf("%s%s", $querymarker?"?":"", $v[1]):"";
		}
	}
	if($notrail&&substr($v, -1)=="/") $v = RemoveSlashs($v);
	return $v;
}



# -------------------------------------------------------------------------------------------------------------------
# CreateUserId
function CreateUserId() {
	return CreateGUID();
}

# -------------------------------------------------------------------------------------------------------------------
# CreateGUID
function CreateGUID() {
	return md5(uniqid(rand(), true));
}	

# -------------------------------------------------------------------------------------------------------------------
# CreateDigitId	
function CreateDigitId($digits = 5) {
	$id = "";
	for ($i=0;$i<$digits;$i++) $id .= rand(0, 9);
	return $id;
}

# -------------------------------------------------------------------------------------------------------------------
# CreateRandomPassword
function CreateRandomPassword($length = 8) {
	// initialize
	$result = "";
	$possible = "2346789bcdfghjkmnpqrtvwxyzBCDFGHJKLMNPQRTVWXYZ";
	$maxlength = strlen($possible);
	// buffer overflow protection
	if ($length > $maxlength) {
		$length = $maxlength;
	}
	// cycle
	$i = 0; 
	while ($i < $length) { 
		// pick a random character from the possible ones
		$char = substr($possible, mt_rand(0, $maxlength-1), 1);
		// check
		if (!strstr($result, $char)) { 
			$result .= $char;
		}
		$i++;
	}
	// return
	return $result;
}


# -------------------------------------------------------------------------------------------------------------------
# ValidateEMail
function ValidateEMail($email) {
	return filter_var($email, FILTER_VALIDATE_EMAIL);
}

# -------------------------------------------------------------------------------------------------------------------
# IfIsString	
function IfIsString($a, $b) {
	if (!(strpos($a, $b) === false)) return true;
	return false;
}	


# -------------------------------------------------------------------------------------------------------------------
# GetRemoteAddress
function GetRemoteAddress(){
	$rm=Array("HTTP_CLIENT_IP", "HTTP_X_FORWARDED", "HTTP_FORWARDED_FOR", "HTTP_X_FORWARDED_FOR", "HTTP_X_CLUSTER_CLIENT_IP");
	foreach($rm as $r){if(isset($_SERVER[$r])){return @$_SERVER[$r];}}
	return @$_SERVER["REMOTE_ADDR"];
}

# -------------------------------------------------------------------------------------------------------------------
# ReverseDNSLookup
function ReverseDNSLookup($ip) {
	// get hostname and ip
	$hostname = GetHostByAddr($ip);
	$hostip = GetHostByName($hostname);
	// return result
	return Array(
		"hostname"=>$hostname,
		"hostip"=>$hostip,
		"sourceip"=>$ip,
		"match"=>$ip==$hostip
	);
}

# -------------------------------------------------------------------------------------------------------------------
# GetSession
function GetSession(){
	return GetVar("session", false);
}	

# -------------------------------------------------------------------------------------------------------------------
# Redirect, creates a 302 redirect
function Redirect($url){header("location: $url"); exit;}

# -------------------------------------------------------------------------------------------------------------------
# LeftString, compares the left string
function LeftString($haystack, $needle) {
	return strtolower(substr($haystack, 0, strlen($needle))) == strtolower($needle);
}

# -------------------------------------------------------------------------------------------------------------------
# Debug
function Debug($p){
	echo "<pre>".$p."\n</pre>";
}

# -------------------------------------------------------------------------------------------------------------------
# (function) DebugWrite, writes to the debug file
function DebugWrite($mixed) {
	file_put_contents(DEBUG_LOG, print_r($mixed, true)."\n", FILE_APPEND);
}	
# -------------------------------------------------------------------------------------------------------------------
# DieDebug
function DieDebug($s) {
	echo "<Pre>";
	var_dump($s);
	exit;
}

# -------------------------------------------------------------------------------------------------------------------
# Base64URLEncode / Decode
function Base64URLEncode($input) {
	return strtr(base64_encode($input), '+/=', '-_,');
}

function Base64URLDecode($input) {
	return base64_decode(strtr($input, '-_,', '+/='));
}	

# -------------------------------------------------------------------------------------------------------------------
# Array
function is_assoc($array) {
	return (bool)count(array_filter(array_keys($array), 'is_string'));
}	

# explodeNth
function explodeNth($delimiter, $string, $n) {
	$arr = explode($delimiter, $string);
	$arr2 = array_chunk($arr, $n);
	$out = array();
	for ($i = 0, $t = count($arr2); $i < $t; $i++) {
		$out[] = implode($delimiter, $arr2[$i]);
	}
	return $out;
}

# explodeVar 
function explodeVar($string, $delimiter = "=", $break = "\n") {
	$result = Array();
	foreach(explode($break, $string) as $index=>$line) {
		$line = trim($line);
		if(strlen($line)!=0) {
			$line = explode($delimiter, $line, 2);
			$result[count($line)==2?$line[0]:$index] = count($line)==2?$line[1]:$line[0];
		}
	}
	return $result;
}	

# islowercase



function Div($class, $id = "", $content = "", $style = "") {
	return Tag("div", Array("id"=>$id, "class"=>$class, "style"=>$style), $content);
}

# -------------------------------------------------------------------------------------------------------------------
# Tag, creates a html tag element <></>
function Tag($tagname, $tagargs, $content="", $opentag = false) {
	$cn = "";
	if (is_array($tagargs)) {
		foreach($tagargs as $key=>$value) {
			if($value!==false) {
				if($value===TAG_NOVALUE){
					$cn .= $key." ";
				} else if(strlen($value)>0) {
					$cn .= sprintf("%s=\"%s\" ", $key, $value);
				}
			}
		}
	} else {
		$cn = $tagargs;
	}
	$cn = trim($cn);
	$ret = trim(sprintf("<%s %s", $tagname, strlen($cn)!=0?$cn:""));
	if ($opentag) return $ret."/>";
	return $ret.">".$content."</".$tagname.">";
}	

# -------------------------------------------------------------------------------------------------------------------
# TagSelect, creates a select/option field
function TagSelect($tagargs, $options, $selected=null) {
	$cn = "";
	foreach($options as $value=>$name) {
		$cn .= Tag("option", Array("value"=>$value, (($selected==$value)?"selected":"")=>TAG_NOVALUE), ($name=="")?$value:$name);
	}
	return Tag("select", $tagargs, $cn);
}


		
