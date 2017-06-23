$(document).ready(function()
{
	function customerPopup(e, $elem) {

	// Prevent default anchor event
	e.preventDefault();

	// Set values for window
	intWidth = '500';
	intHeight = '400';
	strResize = 'yes';

	// Set title and open popup with focus on it
	var strTitle = ((typeof $elem.attr('title') !== 'undefined') ? $elem.attr('title') : 'Social Share'),
	    strParam = 'width=' + intWidth + ',height=' + intHeight + ',resizable=' + strResize,            
	    objWindow = window.open($elem.attr('href'), strTitle, strParam).focus();
	}

	/* ================================================== */

	$(document).ready(function ($) {
		$('.customer.share').on("click", function(e) {
			customerPopup(e, $(this));
		});
	});
});