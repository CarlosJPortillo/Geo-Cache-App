
<!DOCTYPE html>
<html>
	<head>
		 <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
		 <meta charset="utf-8">
		  <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
		 <style>
      #map {
            height: 300px;
			position: relative;
			margin-top: 20px;
			margin-left: 20px;
			margin-right: 20px;

     	 }
     #map_container {
	    	    position: relative;
	}

    </style>
		 <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
       		 <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
		 <script type = "text/javascript">
			//initialize value to some number outside of Lat/Lon system
			var LATITUDE = 1000;
			var LONGITUDE = 1000;
			var MAP = null;
			//create Markets array and image urls array
			var MARKERS = new Array();
			var IMG_URLS = new Array();
			var RECTANGLE = null;
			var CIRCLE = null;
			function evalSlider()
			{
				var sliderValue = document.getElementById('distance').value;
				document.getElementById('sliderVal').innerHTML = sliderValue;
				
			}
			//function to be called when page gets loaded 
			function Initialize()
			{
				GetLocation();
			}
			function GetLocation()
			{

				//check if geolocation is supported 
				if (navigator.geolocation) 
				{
					navigator.geolocation.getCurrentPosition(CreateStartPosition);
				} 	
				else 
				{
					//does not work with chrome
					alert("Geolocation is not supported by this browser.");
				}
			}
			function CreateStartPosition(position)
			{
				//get current location latitude and longitude and store them in variables 
				LATITUDE = position.coords.latitude;
				LONGITUDE = position.coords.longitude;
				//create map 
				MAP = new google.maps.Map(document.getElementById('map'),{
                                zoom: 9
                                });
                                MAP.setCenter(new google.maps.LatLng(LATITUDE, LONGITUDE));
                                //add listener for page resize to make page responsive
                                google.maps.event.addDomListener(window, "resize", function() {
                                        var center = MAP.getCenter();
                                        google.maps.event.trigger(MAP, "resize");
					MAP.setCenter(center);

                                });

			}
			function Main()
			{
			  if(LATITUDE!= 1000)
			  {
				startPosition = new google.maps.LatLng(LATITUDE, LONGITUDE);
				MAP.setCenter(startPosition);
				DeleteMarkers();
				DeleteShapes();
				var difficulty = parseInt(document.getElementById('difficulty').value);
				var distance = parseFloat(document.getElementById('distance').value);
				var cache_type = document.getElementById('cache_type').value;
				var radius = (1.609344 * distance)*1000;
				CIRCLE = new google.maps.Circle(
				{
					map: MAP,
					center: new google.maps.LatLng(LATITUDE, LONGITUDE),
					radius: radius
				
				});
				//get circle bounds
				var Rect = CIRCLE.getBounds();
				//create Recentangle with circles bounds 
				RECTANGLE =  new google.maps.Rectangle(
				{
					map: MAP,
					bounds: Rect
				});
				//convert the rectanglebounds to JSON 
				var JsonRec = Rect.toJSON();	
				// store the bounds into variables to send to php file
				var south = JsonRec["south"];	
				var north = JsonRec["north"];
				var west = JsonRec["west"];
				var east = JsonRec["east"];
				//JQUERY to use AJAX GET to pass data to search.php using 
				//anonymous method called in get request 
				$.get('search.php', {south: south, north: north,
				       west: west, east: east, difficulty: difficulty,
				       cache_type: cache_type}, function(data)
				{
					var marker;
					var lat;
                    var lon;
					var cache_ID;
					var r = JSON.parse(data);
					//if zero results returned from query 
					if(r.length == 0)
					{
						alert("No Geocaches found on matched criteria! \n Try adjusting different search settings");
					}
					var y = document.getElementById("hint");	
					var x = document.getElementById("T1");
                                                x.innerHTML = " <tr style =\"background-color:#4EE9FA;\">" + " <th><H3>Geocache ID</H3></th>"
                                                +" <th><H3>Latitude</H3></th>"
                                                +" <th><H3>Longtitude</H3></th>"
                                                +" </tr>";

					for(i = 0; i < r.length; i++)
					{ 
						lat = parseFloat(r[i].latitude);
						lon = parseFloat(r[i].longitude);
						cache_ID = r[i].cache_id;
						y.style.visibility = "visible";
				 		x.style.visibility = "visible";
						if(i%2 ==1)
                                                {
                                               	    x.innerHTML += "<tr style = \"background-color:#4EE9FA;\"><th>" + cache_ID + "</th><th>" + 
						    lat + "</th><th>" + lon + "</th></tr>";
						}                                             
						 else{
                                                    x.innerHTML += "<tr style = \"background-color:#E857EE;\"><th>" + cache_ID + "</th><th>" + lat +
						    "</th><th>" + lon + "</th></tr>";
						}
						MARKERS.push(new google.maps.Marker({
						position: {lat: lat, lng: lon},
						map: MAP,
							}));
						marker = MARKERS[i];
						MARKERS[i].addListener('click', function() {
								var infoWindow = new google.maps.InfoWindow();
								var contentString = "";
								mPosition = this.getPosition();
								var mLat = mPosition.lat();
								var mLon = mPosition.lng();
								var api_String = "https://api.flickr.com/services/rest/?method=flickr.photos.search&api_key=8d2969cc4eafe4382ea0273a364e8b8b&lat=" + mLat + "&lon=" + mLon + "&format=json&nojsoncallback=1";
                                                 $.get(api_String , function(data)
                                                 {
						   try{
                                                	
                                                        var imgUrl = "https://farm" + data.photos.photo[0].farm + ".staticflickr.com/"
                                                        + data.photos.photo[0].server + "/" + data.photos.photo[0].id + "_" +
                                                        data.photos.photo[0].secret + ".jpg";
							var imgUrl2 = "https://farm" + data.photos.photo[2].farm + ".staticflickr.com/"
                                                        + data.photos.photo[2].server + "/" + data.photos.photo[2].id + "_" +
                                                        data.photos.photo[2].secret + ".jpg";
							var imgUrl3 = "https://farm" + data.photos.photo[3].farm + ".staticflickr.com/"
                                                        + data.photos.photo[3].server + "/" + data.photos.photo[3].id + "_" +
                                                        data.photos.photo[3].secret + ".jpg";
							 var imgUrl4 = "https://farm" + data.photos.photo[1].farm + ".staticflickr.com/"
                                                        + data.photos.photo[1].server + "/" + data.photos.photo[1].id + "_" +
                                                        data.photos.photo[1].secret + ".jpg";
							 							
									
                                                        contentString = "<p><strong>Latitude:</strong> " +mLat + "<strong> Longitude:</strong> " + mLon +"</p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <img src =\"" +imgUrl + "\" height ='70' width = '70'> <img src =\"" +imgUrl2 + "\" height ='70' width = '70'> <img src =\"" +imgUrl3 + "\" height ='70' width = '70'><img src =\"" +imgUrl4 + "\" height ='70' width = '70'>";}
							catch(error)
							{
								alert("No photographs found at this location");
								contentString = "<p><strong>Latitude:</strong> " +mLat + "<strong> Longitude:</strong> " + mLon +"</p>"
							}
                                                        infoWindow.setContent(contentString);
                                                 });
								
                                                                infoWindow.open(MAP, this);
								mLat = mLat + .1;
                                                                newPosition = new google.maps.LatLng(mLat, mLon);
                                                                setTimeout(function(){MAP.setCenter(newPosition);}, 710);

                                                                 });

					}
					});
				 }
				 else
				 {
					alert("We don't have your location!");
				 }
			        }
			//delete markers 
			function DeleteMarkers()
			{
				for(i = 0; i < MARKERS.length; i++)
				{
					//clear each marker individually having it point to null 
					MARKERS[i].setMap(null);
				}
				//clear the markers array
				MARKERS = [];
			}
			function DeleteShapes()
			{
				if(CIRCLE != null)
				{
					CIRCLE.setMap(null);

					RECTANGLE.setMap(null);
				}
			}
		</script>
	</head>

	<body onload = "Initialize()">
		<div class = "container-fluid">
		<div class = "row">
		<div class = "col-md-4"><h3> Adjust distance from current location </h3></div>
		<div class = "col-md-4"><h3>Choose a difficulty</h3></div>
		<div class = "col-md-4"><h3>Choose Cache Type</h3></div>
		</div>
		<div class = "row">
		<div class = "col-md-4">
		<input type = "range" id = "distance" min = "5" step = "5" max = "200" value = "10"  onchange = "evalSlider()"/>
		<!-- main called when button clicked-->
		<button type = "button" id = "button1" onclick = "Main()"><strong>Find Geocaches!</strong></button></div>
		<div id = "sliderVal" class = "col-md-1"></div><div class = "col-md-3">
		<select id = "difficulty"> 
			<option>1</option>
			<option>2</option> 
			<option>3</option>
			<option>4</option>
			<option>5</option>
			<option>6</option>
			<option>7</option>
			<option>8</option>
			<option>9</option>
			<option>10</option>
		</select></div>
		<div class = "col-md-4">&nbsp;&nbsp;&nbsp;
		<select id = "cache_type">
			<option>Traditional</option>
			<option>Mystery/Puzzle</option>
			<option>Multi-Cache</option>
		</select>
	        </div>
		</div>
		<div id = "map_container"><div id = "map"> </div>
		<div class = row">
		<div class = "col-md-2" style = "visibility:hidden">This is hidden</div>
		<div id = "hint" class = "col-md-10" style = "visibility:hidden"><h2>&nbsp;Click on a marker to get information about that location</h2></div>
		</div>
		<div class = "row">
		<div class "col-md-4">
		<table id = "T1" class = "table table-bordered" style = "visibility:hidden;">
			<tr style = "background-color:#E857EE;">
			<th><H3>Geocache ID</H3></th>
			<th><H3>Latitude</H3></th>
			<th><H3>Longitude</H3></th>
			</tr> 
		</div>
		<div class "col-md-8"></div>
		</div>
		</div>	
		<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCiLV8WEOgJsHQGzh0tgiGZeXVaz5b7XQc"
			async defer></script>
	
	</body>
</html>
