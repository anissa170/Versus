var data_hover = { alwaysOn:true, strokeColor:'000000', strokeWidth:'1', fillOpacity:'0.1' };

$(document).ready(function()
{
    $('area').data('maphilight', data_hover).trigger('alwaysOn.maphilight');


    //init plugins resize map
	$('.map').imageMapResize();
	
	//appliquation des zones sur la carte
    $('.image_map').maphilight();

    

	function changeCanvas() {
		var c=document.getElementById("map_point");
		var ctx=c.getContext("2d");

        c.height = $('#preview').height();
        c.width = $('#preview').width();

	    zoneList = new Array();
    	firstPoint = null;
    	currentZone = null;

	}

    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            
            reader.onload = function (e) {
                $('#preview').attr('src', e.target.result);
                setTimeout(changeCanvas, 100);
            }

            reader.readAsDataURL(input.files[0]);
        }
    }
    
    $("#imgInp").change(function(){
        readURL(this);
    });

    class Point {
	  constructor(posX, posY) {
	    this.x = posX;
	    this.y = posY;
	  }
	}

    var zoneList = new Array();
	var firstPoint = null;
	var currentZone = null;

	function drawLine(previewPoint, currentPoint) {
		var c=document.getElementById("map_point");
		var ctx=c.getContext("2d");

		ctx.beginPath(); 
		ctx.lineWidth="1";
		ctx.strokeStyle="black"; 
		ctx.moveTo(previewPoint.x,previewPoint.y);
		ctx.lineTo(currentPoint.x,currentPoint.y);
		ctx.stroke();
	}

	function drawPoint(point) {
		var c=document.getElementById("map_point");
		var ctx=c.getContext("2d");

		ctx.strokeRect(point.x,point.y,1,1);
	}

    function createFirstPoint(point) {
    	firstPoint = point;
    	currentZone = new Object();
    	currentZone['name'] = null;
    	currentZone['points'] = new Array();
    	currentZone['points'].push(firstPoint);
    	drawPoint(point);
    }

    function addPoint(point) {
    	var previewPoint = currentZone['points'][currentZone['points'].length - 1];
    	currentZone['points'].push(point);
    	drawPoint(point);
    	drawLine(previewPoint, point);
    }

    function finishZone() {
    	var previewPoint = currentZone['points'][currentZone['points'].length - 1];
    	currentZone['name'] = prompt("Entrez le nom de la zone", "Paris");
    	zoneList.push(currentZone);
    	drawLine(previewPoint, firstPoint);
    	firstPoint = null;
    	currentZone = null;
    	$('#obj').val(JSON.stringify(zoneList));
    }

    function isFirstPoint(point){
    	var nbPixelEnable = 5;
    	if( point.x >= (firstPoint.x - nbPixelEnable) && (firstPoint.x + nbPixelEnable) >= point.x) {
			if( point.y >= (firstPoint.y - nbPixelEnable) && (firstPoint.y + nbPixelEnable) >= point.y) {
				return true;
	    	}
    	}
    	return false;
    }

    $('#map_point').click(function(e) {
        var posX = e.pageX - $(this).position().left;
        var posY = e.pageY - $(this).position().top;
        var point = new Point(posX,posY);
        if (firstPoint == null) {
        	createFirstPoint(point);
        }
        else if (point == firstPoint || isFirstPoint(point)) {
        	finishZone();
        }
        else {
        	addPoint(point);
        }
    });

});