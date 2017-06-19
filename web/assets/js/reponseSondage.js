function popup() {
	var popID = "popup_map";

	$('#' + popID).fadeIn().css({
		'width': "auto"
	})

	var popMargTop = ($('#' + popID).height() + 80) / 2;
	var popMargLeft = ($('#' + popID).width() + 80) / 2;

	$('#' + popID).css({
		'margin-top' : -popMargTop,
		'margin-left' : -popMargLeft
	});

	$('body').append('<div id="fade"></div>');
	$('#fade').css({'filter' : 'alpha(opacity=80)'}).fadeIn();
}

$(document).ready(function()
{
	popup();

    var data = {};
    jQuery('#image_map').maphilight();
    data.alwaysOn = true; //je veux qu'il m'affiche constament une zone ...
    data.strokeColor = '000000'; //bleu foncé
    data.strokeWidth ='1'
    data.fillOpacity = '0';
    $('area').data('maphilight', data).trigger('alwaysOn.maphilight');

	$('area').mouseover(function(e) {
    	var data = {};
	    data.alwaysOn = true;
	    data.strokeColor = '000000';
    	data.fillOpacity = '0.1';
	    $(this).data('maphilight', data).trigger('alwaysOn.maphilight');  
	}).mouseout(function(e) {
    	var data = {};
	    data.alwaysOn = true;
	    data.strokeColor = '000000';
	    data.fillOpacity = '0';
	    $(this).data('maphilight', data).trigger('alwaysOn.maphilight');  
	}).click(function(e) { e.preventDefault(); });

	$('map').imageMapResize();

	$('area').click( function() {
		$('.region').html($(this).attr("title"));
		$('#fade , .popup_block').fadeOut(function() {
			$('#fade, a.close').remove();
		});
		return false;
	});

	$('.newpopup').click( function(e) {
		e.preventDefault();
		popup();
		return false;
	});

}); 

