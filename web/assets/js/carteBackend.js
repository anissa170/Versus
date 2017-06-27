var data_hover = { alwaysOn:true, strokeColor:'000000', strokeWidth:'1', fillOpacity:'0.1' };

$(document).ready(function()
{
    $('area').data('maphilight', data_hover).trigger('alwaysOn.maphilight');


    //init plugins resize map
	$('.map').imageMapResize();
	
	//appliquation des zones sur la carte
    $('.image_map').maphilight();

});