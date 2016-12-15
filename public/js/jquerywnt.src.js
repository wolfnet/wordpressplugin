window.jQueryWnt = jQuery.noConflict(true);
if (typeof window.jQuery === 'undefined') {
	// If query was not defined before, create a global variable which will be available for plugins
	window.jQuery = jQueryWnt;
	window.$ = jQueryWnt;
}
