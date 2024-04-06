/**
 * ulrich_products - jQuery.inView.js
 * 
 * @author: Mathias Bruckmoser <mathias.bruckmoser(at)abavo.de>
 * @since: 28.05.2018 - 08:51:35
 * 
 * @copyright: since 2018 - abavo GmbH <dev(at)abavo.de>
 * @license: Proprietary
 */

/**
 *  In View jQuery method
 *
 * @return {boolean}
 * */
$.fn.inView = function () {
	//Window Object
	var win = $(window);
	//Object to Check
	obj = $(this);
	//the top Scroll Position in the page
	var scrollPosition = win.scrollTop();
	//the end of the visible area in the page, starting from the scroll position
	var visibleArea = win.scrollTop() + win.height();
	//the end of the object to check
	var objEndPos = (obj.offset().top + obj.outerHeight());
	return(visibleArea >= objEndPos && scrollPosition <= objEndPos ? true : false)
};