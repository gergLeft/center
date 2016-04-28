/**
* password_strength_plugin.js
* Copyright (c) 20010 myPocket technologies (www.mypocket-technologies.com)
* Permission is hereby granted, free of charge, to any person obtaining a copy
* of this software and associated documentation files (the "Software"), to deal
* in the Software without restriction, including without limitation the rights
* to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
* copies of the Software, and to permit persons to whom the Software is
* furnished to do so, subject to the following conditions:
* The above copyright notice and this permission notice shall be included in
* all copies or substantial portions of the Software.
* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
* IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
* FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
* AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
* LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
* OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
* THE SOFTWARE.
*
* @author Darren Mason (djmason9@gmail.com)
* @date 3/13/2009
* @projectDescription Password Strength Meter is a jQuery plug-in provide you smart algorithm to detect a password strength. Based on Firas Kassem orginal plugin - http://phiras.wordpress.com/2007/04/08/password-strength-meter-a-jquery-plugin/
* @version 1.0.1
*
* @requires jquery.js (tested with 1.3.2)
* @param shortPass: "shortPass", //optional
* @param badPass: "badPass", //optional
* @param goodPass: "goodPass", //optional
* @param strongPass: "strongPass", //optional
* @param baseStyle: "testresult", //optional
* @param userid: "", //required override
* @param messageloc: 1 //before == 0 or after == 1
*
*/
(function($){
$.fn.shortPass = 'Too short';
$.fn.badPass = 'Weak';
$.fn.goodPass = 'Good';
$.fn.strongPass = 'Strong';
$.fn.samePassword = 'Username and Password identical.';
$.fn.resultStyle = "";
$.fn.passStrength = function(options) {
var defaults = {
shortPass: "shortPass", //optional
badPass: "badPass", //optional
goodPass: "goodPass", //optional
strongPass: "strongPass", //optional
baseStyle: "testresult", //optional
userid: "", //required override
messageloc: 1 //before == 0 or after == 1
};
var opts = $.extend(defaults, options);
return this.each(function() {
var obj = $(this);
$(obj).unbind().keyup(function()
{
var results = $.fn.teststrength($(this).val(),$(opts.userid).val(),opts);
if(opts.messageloc === 1)
{
$(this).next("." + opts.baseStyle).remove();
$(this).after("<span class=\""+opts.baseStyle+"\"><span></span></span>");
$(this).next("." + opts.baseStyle).addClass($(this).resultStyle).find("span").text(results);
}
else
{
$(this).prev("." + opts.baseStyle).remove();
