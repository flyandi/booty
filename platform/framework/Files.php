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


namespace Booty\Framework;

/**
  * (constants)
  */


/**
  * (enumerations)
  */

interface FilesFilters {
	const all = 0;
	const onlydirs = 1;
	const onlyfiles = 2;
}

interface FilesMimeType { 

	// Application types
	const gz = "application/x-gzip";
	const json = "application/json";
	const xml = "application/xml";
	const doc = "application/msword";
	const docx = "application/msword";
	const xls = "application/vnd.ms-excel";
	const xlsx = "application/vnd.ms-excel";
	const ppt = "application/vnd.ms-powerpoint";
	const pptx = "application/vnd.ms-powerpoint";
	const rtf = "application/rtf";
	const pdf = "application/pdf";
	const zip = "application/zip";
	const tar = "application/x-tar";
	const swf = "application/x-shockwave-flash";

	// Image types
	const jpg = "image/jpg";
	const jpeg = "image/jpg";
	const jpe = "image/jpg";
	const png = "image/png";
	const gif = "image/gif";
	const bmp = "image/bmp";
	const svg = "image/svg+xml";

	// Text types
	const plain = "text/plain";
	const js = "text/javascript";
	const css = "text/css";
	const html = "text/html";
	const htm = "text/html";
	const php = "text/html";
	const txt = "text/plain";

	// Media types
	const mpeg = "video/mpeg";
	const mpg = "video/mpeg";
	const mp4 = "video/mpeg";
	const avi = "video/msvideo";
	const wmv = "video/x-ms-wmv";
	const mov = "video/quicktime";
	const mp3 = "audio/mpeg";
	const wav = "audio/wav";

	// Font types
	const ttf = "font/truetype";
	const otf = "font/opentype";
	const eot = "application/vnd.ms-fontobject";
	const woff = "application/x-font-woff";
}


/** 
  * (class) Files
  * Provides easy access to the file system
  */

class Files extends Primitive {


	/** 
	 * (__construct) 
	 *
     * @param source 	Specify the source of the configration 
     * @param format 	Format of the source, otherwise autodetect
	*/

	public function __construct() {

	}

	/** 
	 * (get) Reads the location and returns all paths
	 *
     * @param location 	Returns the paths for the location
     * @param files 	If set to true, includes also files
     * @param recursive If set to true, also walks through the sub paths
	*/

	public function get($location, $filter = FilesFilters::all, $recursive = false, $sort = true) {
		// initialize
		$results = array();
		// sanity check
		if(!is_dir($location)) return $results;		
		// add trailing slash
		if(substr($location, -1) != "/") $location .= "/";
		// run open dir
		if ($handle = opendir($location)) {
			while (false !== ($file = readdir($handle))) {
				if(!in_array($file, array(".", ".."))) {
					// prepare filename
					$fn = $location.$file;
					// include
					if($filter == FilesFilters::all || (is_dir($fn) && $filter == FilesFilters::onlydirs) || (is_file($fn) && $filter == FilesFilters::onlyfiles)) {
						$results[] = $fn;	
					}
					// path
					if(is_dir($fn) && $recursive) 
						$results = array_merge($results, $this->get($fn, $filter, $recursive, $sort));
				}
			}
		}
		// close handle
		closedir($handle);
		// sortable
		if($sort) sort($results);
		// return
		return $results;
	}


	/** 
	 * (Mime) Returns the mime type of the file
	 *
     * @param source 	The source filename
     * @param lookup 	Forces an lookup to the mime.db database [NI]
	*/

	static public function Mime($source, $lookup = false) {

		// get suffix
		$suffix = pathinfo($source, PATHINFO_EXTENSION);

		// get class
		$mimes = new \ReflectionClass("\Booty\Framework\FilesMimeType");

		// get map
		$map = $mimes->getConstants();

		// check
		return isset($map[$suffix]) ? $map[$suffix] : "Unknown/" . $suffix;
	}


}

