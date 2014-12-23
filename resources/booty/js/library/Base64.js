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
  * Base64 Class
  */

Booty.$.base64 = {

	/** 
	  * Private
	  */

	_keyStr:"ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",


	/**
	  * (encode)
	  */

	encode: function(e) {

		var n,r,i,s,o,u,a,f=0,t="";

		e = this._utf8_encode(e);

		while(f < e.length){
			n = e.charCodeAt(f++);
			r = e.charCodeAt(f++);
			i = e.charCodeAt(f++);
			s = n>>2;
			o = (n&3)<<4|r>>4;
			u = (r&15)<<2|i>>6;
			a = i&63;

			if(isNaN(r)){
				u = a = 64
			} else if(isNaN(i)){
				a =64
			}

			t = t + this._keyStr.charAt(s) + this._keyStr.charAt(o) + this._keyStr.charAt(u) + this._keyStr.charAt(a)
		}

		return t;
	},


	/** 
	  * (decode)
	  */

	decode: function(e) {

		var n,r,i,s,o,u,a,f=0,t="";

		e = e.replace(/[^A-Za-z0-9\+\/\=]/g,"");

		while(f < e.length) {
			s = this._keyStr.indexOf(e.charAt(f++));
			o = this._keyStr.indexOf(e.charAt(f++));
			u = this._keyStr.indexOf(e.charAt(f++));
			a = this._keyStr.indexOf(e.charAt(f++));
			n = s<<2|o>>4;
			r = (o&15)<<4|u>>2;
			i = (u&3)<<6|a;
			t = t+String.fromCharCode(n);

			if(u != 64){
				t = t+String.fromCharCode(r)
			}

			if(a != 64){
				t = t+String.fromCharCode(i)
			}
		}

		t = this._utf8_decode(t);
		return t
	},

	/** 
	  * Base64 UTF8 
	  */

	_utf8_encode: function(e) {
		var t="";

		e = e.replace(/\r\n/g,"\n");
		
		for(var n=0; n<e.length; n++){
			var r = e.charCodeAt(n);

			if(r < 128) {
				t += String.fromCharCode(r)
			} else if(r > 127 && r < 2048){
				t += String.fromCharCode(r>>6|192);
				t += String.fromCharCode(r&63|128)
			} else {
				t += String.fromCharCode(r>>12|224);
				t += String.fromCharCode(r>>6&63|128);
				t += String.fromCharCode(r&63|128)
			}
		}
		return t;
	},

	_utf8_decode: function(e){
		var t="";var n=0;var r=c1=c2=0;

		while( n< e.length) {
			r = e.charCodeAt(n);
			if(r<128){
				t += String.fromCharCode(r);
				n++;
			} else if( r > 191 && r < 224) {
				c2 = e.charCodeAt(n+1);
				t += String.fromCharCode((r&31)<<6|c2&63);
				n += 2;
			} else {
				c2=e.charCodeAt(n+1);
				c3=e.charCodeAt(n+2);
				t+=String.fromCharCode((r&15)<<12|(c2&63)<<6|c3&63);
				n+=3;
			}
		}

		return t
	},

	/** 
	  * (is) checks if a string is base64
	  */

	is: function(e) {
		var r = new RegExp("^(?:[A-Za-z0-9+/]{4})*(?:[A-Za-z0-9+/]{2}==|[A-Za-z0-9+/]{3}=|[A-Za-z0-9+/]{4})$");

		return r.test(e);
	}
}
