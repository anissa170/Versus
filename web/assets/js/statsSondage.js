var data = { alwaysOn:true, strokeColor:'000000', strokeWidth:'1', fillOpacity:'0' };
var data_hover = { alwaysOn:true, strokeColor:'000000', strokeWidth:'1', fillOpacity:'0.1' };

function removeHover($element) {
	$element.data('maphilight', data).trigger('alwaysOn.maphilight');
}

$(document).ready(function()
{
    $('area').data('maphilight', data).trigger('alwaysOn.maphilight');

	$('area').mouseover(function(e) {
	    $(this).data('maphilight', data_hover).trigger('alwaysOn.maphilight');  
	}).mouseout(function(e) {
		if (!$(this).hasClass('selected')) {
	    	removeHover($(this));
		}
	}).click(function(e) { e.preventDefault(); });

	$('area.selected').data('maphilight', data_hover).trigger('alwaysOn.maphilight');  


    //init plugins resize map
	$('#map').imageMapResize();
	
	//appliquation des zones sur la carte
    $('#image_map').maphilight();
	$('area').click( function() {
		removeHover($('area.selected'));
		$('area.selected').removeClass('selected');

		$(this).addClass('selected');
		$('.regionLabel span').text($(this).attr("title"));
		var	data = $(this).data("id");
		var url = $(this).data("url");
		$.ajax({
	       type : 'POST',
	       url : url,
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
