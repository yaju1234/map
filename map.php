<head>
  <style>
	
	.labels {
    margin-top:-3px;
    padding: 7px;
    position: absolute;
    visibility: visible;
    z-index: 1030;
}
.labels .arrow{
    border-top-color: #000000;
    border-right-color: rgba(0,0,0,0);
    border-bottom-color: rgba(0,0,0,0);
    border-left-color: rgba(0,0,0,0);
    border-width: 7px 10px 0;
    bottom: 0;
    left: 50%;
    margin-left: -5px;
    border-style: solid;
    height: 0;
    position: absolute;
    width: 0;
}
.labels .inner{
    background-color: #000000;
    border-radius: 4px;
	font-size:17px;
    color: #FFFFFF;
    //max-width: 200px;
    padding: 7px 7px;
    text-align: center;
    text-decoration: none;  
}
	.modal-open {
		overflow: scroll;
	}
	.modal {
		overflow-y: scroll !important;
	}
	</style>

  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBC9z8BAJqI5ocYHEMgawvbxhvrxxfLOdM&libraries=geometry,places&ext=.js"></script>
<script src="https://cdn.rawgit.com/googlemaps/v3-utility-library/master/markerwithlabel/src/markerwithlabel.js"></script>
</head>
<p id="start" hidden><?php echo $_GET['start'] ?></p>
<div id="map_canvas" style="height: 100%; width: 100%;"></div>
<div class="modal fade bs-example-modal-lg tovwinvlddata" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-lg">  
    <div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Some addresses are invalid.</h4>
      </div>
    <a href="javascript:void(0);" onClick="viewInvalidAddress();">Click to view.</a>
    </div>
  </div>
</div>

<div class="modal fade vwinvlddata" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog">  
    <div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">List of invalid adresses.</h4>
    </div>
    <div class="list" style="text-align:left; margin-left:10px;"></div>
    </div>
  </div>
</div>
<script>
var latLng = new google.maps.LatLng(45.9432, 24.9668),
	b = false,
	infowindow = null,
	invalidAddress = [];
	colorsObj = {};
	map = new google.maps.Map(document.getElementById('map_canvas'), {
		zoom: 9,
		center: latLng,
		mapTypeId: google.maps.MapTypeId.ROADMAP
	});
	var colors = [];
	/*while (colors.length < 100) {
		do {
			var color = Math.floor((Math.random()*10)+1);
		} while (colors.indexOf(color) >= 0);
		colors.push("#" + ("000000" + color.toString(16)).slice(-6));
	}*/
	function random_rgba() {
		return 'rgba(' +Math.floor(Math.random() * 255) + ',' + Math.floor(Math.random() * 255) + ',' +Math.floor(Math.random() * 255) + ',1)';
	}
	while (colors.length < 100) {
		do {
			var color = random_rgba();
		} while (colors.indexOf(color) >= 0);
		colors.push(color);
	}
	
function initMap() {
	var start  = $("#start").text();
	
	$.ajax({
		type: 'GET',
		url: "http://floridaconstruct.eu/comenzi/programaripezi.php?start="+start,
		dataType: 'json',
		success: function(json) {
			for (var i = 0; i < json.length; i++) {
				var labelContent = json[i].item1;
				var sellerName = json[i].item4;
				var lastCount = false;
				if(i == json.length-1){
					lastCount = true;
				}
				var labelColor = 'yellow';
				var markerColor = 'Green';
				if(colorsObj.hasOwnProperty(sellerName)){
					labelColor = colorsObj[sellerName].labelColor;
					markerColor = colorsObj[sellerName].markerColor;
				}else{
					var randNm1 = Math.floor((Math.random() * 100) + 1);
					var randNm2 = Math.floor((Math.random() * 100) + 1);
					colorsObj[json[i].item4] = {'labelColor' : colors[randNm1], 'markerColor' : colors[randNm2]};
				}
				renderMarkers(json[i].item3,labelContent,lastCount,colorsObj[sellerName].labelColor,colorsObj[sellerName].markerColor,sellerName);
			}
		},
		failure: function(e) {
		   console.log(e);
		}
	});
}

function renderMarkers(address,labelContent,lastCount,labelColor,markerColor,sellerName){
	$.ajax({
		type: 'GET',
		url: "https://maps.google.com/maps/api/geocode/json?address=" + address + "&sensor=false&key=AIzaSyDqneUplTRHo8Ac6GC2ZdEN7ciX9fSLL74",
		dataType: 'json',
		success: function(json1) {
			if(json1.results.length>0){
				if(!b){
					b = true;
					map.setCenter(json1.results[0].geometry.location);
				}
				var marker = new MarkerWithLabel({
					position: json1.results[0].geometry.location,								
					map: map,
					shadow: 'none',
					draggable: false,
					raiseOnDrag: false,
					//labelContent: labelContent,
					/*label: {
						color: 'red',
						fontWeight: 'bold',
						text: labelContent
					},*/
					labelContent: "<div class='arrow' style='border-top-color: "+markerColor+";'></div><div class='inner' style='background-color:"+markerColor+"'>"+labelContent+"</div>",
					labelAnchor: new google.maps.Point(0, 0),
					labelClass: "labels",
					labelInBackground: false,
					icon:"aa.png"
					//icon: pinSymbol(markerColor)
				});
				var iw = new google.maps.InfoWindow({
					content: '<p>Client Name: '+labelContent+'</p><p>Address : '+address+'</p><p>Seller Name : '+sellerName+'</p>'
				});
				google.maps.event.addListener(marker, "click", function(e) {
					if (infowindow) {
						infowindow.close();
					}
					infowindow = iw.open(map, this);
				});
			}else{
				let invalidAddrData = {'cName' : labelContent, 'address' : address};
				invalidAddress.push(invalidAddrData);
			}
			if(lastCount){
				/*if(confirm('View invalid addresses!')){
					$('.vwinvlddata').modal();
				}*/
				$('.tovwinvlddata').modal();
			}
		},
		failure: function(e) {
		   console.log(e);
		}
	});
}

function pinSymbol(color) {
  return {
    path: 'M 0,0 C -2,-20 -10,-22 -10,-30 A 10,10 0 1,1 10,-30 C 10,-22 2,-20 0,0 z',
    fillColor: color,
    fillOpacity: 0,
    strokeColor: '#000',
    strokeWeight: 2,
	display: 'none',
    scale: 2
  };
}

function viewInvalidAddress(){
	var invp = "";
	console.log(invalidAddress);
	if(invalidAddress.length>0){
		for(var i=0; i<invalidAddress.length; i++){
			invp += '<p>Client Name : '+invalidAddress[i].cName+'</p><p>Address : '+invalidAddress[i].address+'</p><br />';
		}
		$('.list').append(invp);
		$('.vwinvlddata').modal();
		$('.tovwinvlddata').modal('hide');
	}
}

google.maps.event.addDomListener(window, 'load', initMap);
</script>