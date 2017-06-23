var data = { alwaysOn:true, strokeColor:'000000', strokeWidth:'1', fillOpacity:'0' };
var data_hover = { alwaysOn:true, strokeColor:'000000', strokeWidth:'1', fillOpacity:'0.1' };

$(document).ready(function()
{
    $('area').data('maphilight', data).trigger('alwaysOn.maphilight');

	$('area').mouseover(function(e) {
	    $(this).data('maphilight', data_hover).trigger('alwaysOn.maphilight');  
	}).mouseout(function(e) {
	    $(this).data('maphilight', data).trigger('alwaysOn.maphilight');  
	}).click(function(e) { e.preventDefault(); });


    //init plugins resize map
	$('#map').imageMapResize();
	
	//appliquation des zones sur la carte
    $('#image_map').maphilight();

	//display popup
	popup();

	$('area').click( function() {
		$('.region').html($(this).attr("title"));
		$('.zone_id').val($(this).data("id"));
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

function popup() {
	var popID = "popup_map";

	$('#' + popID).fadeIn().css({
		'width': "auto",
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
