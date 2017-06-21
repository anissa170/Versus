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

	$('area').click( function() {
		var	data = $(this).data("id");
		$.ajax({
	       url : 'http://localhost/Versus/web/app_dev.php/sondage/1/ajax',
	       data : 'zone_id=' + data,
	       type : 'POST',
	       dataType : 'html',
	       success : function(code_html, statut){
	           
	       },

	       error : function(resultat, statut, erreur){

	       }

	    });

	});

});
