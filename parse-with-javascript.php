<!DOCTYPE html>
<html>
  <head>
  	<script src="//www.parsecdn.com/js/parse-1.3.0.min.js"></script>

	    <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
	    <style type="text/css">
	      html { height: 100% }
	      body { height: 100%; margin: 0; padding: 0 }
	      #map_canvas { height: 100% }
	    </style>
    

<?php
// $currentUser = ParseUser::getCurrentUser();

/*


require 'vendor/autoload.php';
 
use Parse\ParseClient;
use Parse\ParseObject;
use Parse\ParseQuery;
use Parse\ParseUser;
use Parse\ParseInstallation;
use Parse\ParseException;
use Parse\ParseAnalytics;
use Parse\ParseGeoPoint;
 
ParseClient::initialize('mq9CszjDmuCDV96zHWYphRY3eoCXGJw89VebGHWh', 'pfyN8fvLbhli6mmtioNQa1N9jtBtUCDg6NUy5wTc', 'tB9xXgOgwLPa97xMvMyYOh1vISbZtZca2L4KHOTa');
$numeroElementosRetornados = 0;


$geoPoint1 = new ParseGeoPoint(19.43742763718538, -99.21005666265191);
$geoPoint2 = new ParseGeoPoint(19.43720203826151, -99.21004777783455);
$geoPoint3 = new ParseGeoPoint(19.43706524560175, -99.21000444339515);

$queryRutas = new ParseQuery("Rutas");

//una sola ruta
$queryRutas->equalTo("NombreRuta", "prueba al mercado version 6.0");


$queryRutas->limit(1000);
$resultsRutas = $queryRutas->find();
if (count($resultsRutas)>0) {
  echo "Hay " . count($resultsRutas) . " rutas. <br>";

	foreach ($resultsRutas as $ruta) {
	  	$queryLocations = new ParseQuery("Locations");
		$queryLocations->equalTo("ruta", $ruta);
		// greaterThan
		$queryLocations->descending("numCoordenada");
		$queryLocations->limit(1000);
		$resultsLocations = $queryLocations->find();

		if (count($resultsLocations)>0) {

			$numeroLocations = count($resultsLocations);

			//el primero de locations sacarlo para pasarlo
			$objectFirstLocations = $queryLocations->first();
			//Hacer un geoPoint para obtener valorres
			$geoPoint = new ParseGeoPoint($objectFirstLocations->get('geoPoint')->getLatitude(),$objectFirstLocations->get('geoPoint')->getLongitude());
			//obtener y guardar lat y lng para pasarlos a javascript
			$firstLatitude = $geoPoint->getLatitude();
			$firstLongitiude = $geoPoint->getLongitude();

			echo "Hay " . $numeroLocations . " Locations of " .  $ruta->get('NombreRuta') . ".<br>";

				foreach ($resultsLocations as $location) {
					# code...
					$geoLocationLat = $location->get('geoPoint')->getLatitude();
					$geoLocationLng = $location->get('geoPoint')->getLongitude();
					//echo "Lat " . $geoLocationLat . " Lng " .  $geoLocationLng . ".<br>";
				}
		}
	}

}else {
	echo "No hay rutas ";
}

*/

?>


 	<script type="text/javascript"
      src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDnfdE7BKwMrZHgWcCyeDlCOCDzqNfG4mY&sensor=FALSE">//se puso en false por que no requiere la ubicación
    </script>
    <script type="text/javascript">
      function initialize() {

      	var mapOptions = {
          center: new google.maps.LatLng(19.43742763718538, -99.21005666265191),
          zoom: 18,
          mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        
		var map = new google.maps.Map(document.getElementById("map_canvas"),
            mapOptions);

		Parse.initialize("mq9CszjDmuCDV96zHWYphRY3eoCXGJw89VebGHWh", "vbBDF3x2r0DLoPGjlBg0aBiLIC5Z3CzOpqIVoH3T");

		var primeraVez=0;
		var coordInicial;
		//creamos un array
	    var arrayLocations = [];
	    var colorRandom = 0;
	    var Colors = [
		    "#FF0000", 
		    "#00FF00", 
		    "#0000FF", 
		    "#FFFFFF", 
		    "#000000", 
		    "#FFFF00", 
		    "#00FFFF", 
		    "#FF00FF",
		    "#7FFFD4", 
		    "#8A2BE2", 
		    "#A52A2A", 
		    "#5F9EA0", 
		    "#D2691E", 
		    "#6495ED", 
		    "#008B8B", 
		    "#B8860B",
		    "#8B008B", 
		    "#FF8C00", 
		    "#FF1493", 
		    "#ADFF2F", 
		    "#4B0082", 
		    "#90EE90", 
		    "#9370DB", 
		    "#FF4500",
		    "#FA8072"
		];

		var Rutas = Parse.Object.extend("Rutas");
		var queryRutas = new Parse.Query(Rutas);
		//queryRutas.equalTo("NombreRuta", "prueba al mercado version 6.0");
		queryRutas.limit(1000);
		queryRutas.find({
		  success: function(resultsRutas) {
		    alert("Hay " + resultsRutas.length + " rutas.");

		    for (var numR in resultsRutas){
		      // 00000 alert(numR + ' - ' + resultsRutas[numR].get('NombreRuta'));
		      
		    	var Locations = Parse.Object.extend("Locations");
				var queryLocations = new Parse.Query(Locations);
				queryLocations.equalTo("ruta", resultsRutas[numR]);
				queryLocations.descending("numCoordenada");
				queryLocations.limit(1000);
				queryLocations.find({
				  success: function(resultsLocations) {
				    //alert("Hay " + resultsLocations.length + " Locations en la ruta: " + resultsRutas[numR].get('NombreRuta'));
				    

				    for (var numL in resultsLocations){

				    	if (numL==0 && primeraVez==0) {
				    		coordInicial = new google.maps.LatLng(resultsLocations[numL].get('geoPoint').latitude, 
				    												resultsLocations[numL].get('geoPoint').longitude);

				    		mapOptions = {
					          center: coordInicial,
					          zoom: 18,
					          mapTypeId: google.maps.MapTypeId.ROADMAP
					        };

					        map = new google.maps.Map(document.getElementById("map_canvas"),
			           		mapOptions);

				    		primeraVez++;
				    	}
				    	if (numL==0) {
				    		var marker = new google.maps.Marker({
						      position: new google.maps.LatLng(resultsLocations[numL].get('geoPoint').latitude, 
				    												resultsLocations[numL].get('geoPoint').longitude),
						      map: map,
						      title: 'Hello World!'
						  	});
				    	}
				    	
				    	arrayLocations.push(new google.maps.LatLng(resultsLocations[numL].get('geoPoint').latitude,
				    												resultsLocations[numL].get('geoPoint').longitude));
				    	


				    }
			        
					var flightPath = new google.maps.Polyline({
						    path: arrayLocations,
						    geodesic: true,
						    strokeColor: Colors[colorRandom],
						    strokeOpacity: 1.0,
						    strokeWeight: 2
						  });
					

					flightPath.setMap(map);

					arrayLocations = [];
					colorRandom = Math.floor((Math.random() * 24) + 1);


				  },
				  error: function(error) {
				    alert("Error: " + error.code + " " + error.message);
				  }
				});

		    }
		    
		  },
		  error: function(error) {
		    alert("Error: " + error.code + " " + error.message);
		  }
		});

        


      }
    </script>

</head>
  <body onload="initialize()">
    <div id="map_canvas" style="width:100%; height:80%"></div>
  </body>
</html>