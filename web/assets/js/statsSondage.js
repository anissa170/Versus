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
	       type : 'POST',
	       url : 'http://localhost/Versus/web/app_dev.php/sondage/1/ajax',
	       data : 'zone_id=' + data,
	       success: function (data) {
		       	myDoughnutChart.data.datasets[0].data = [];
		       	myDoughnutChart.data.labels = [];
		       	myDoughnutChart.data.datasets[0].backgroundColor = [];
		       	for(var i= 0; i < data.length; i++)
				{
			       	myDoughnutChart.data.datasets[0].data.push(data[i].reponses);
			       	myDoughnutChart.data.labels.push(data[i].proposition.label);
			       	myDoughnutChart.data.datasets[0].backgroundColor.push(data[i].proposition.couleur);
				}
		       	myDoughnutChart.update();
           },
	    });

	});

});
