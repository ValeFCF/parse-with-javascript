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

 	<script type="text/javascript"
      src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDnfdE7BKwMrZHgWcCyeDlCOCDzqNfG4mY&sensor=FALSE">//se puso en false por que no requiere la ubicación
    </script>
    <script type="text/javascript">
    //https://developer.chrome.com/devtools/docs/javascript-debugging

    var user;

    var mapOptions;   
	var map;

	var primeraVez;
	var coordInicial;
	//creamos un array
	var arrayLocations;
	var colorRandom;
	var Colors;

	var flightPath;

	var listaRutasSelect;

    function initialize () {
    	Parse.initialize("mq9CszjDmuCDV96zHWYphRY3eoCXGJw89VebGHWh", "vbBDF3x2r0DLoPGjlBg0aBiLIC5Z3CzOpqIVoH3T");

    	mapOptions = {
          center: new google.maps.LatLng(19.43742763718538, -99.21005666265191),
          zoom: 18,
          mapTypeId: google.maps.MapTypeId.ROADMAP
        };

        map = new google.maps.Map(document.getElementById("map_canvas"),
            mapOptions);

    	Colors = [
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

		listaRutasSelect = document.getElementById('listaRuta');



      	var query = new Parse.Query(Parse.User);
		query.equalTo("objectId", "IPLz80QX1D");
		query.find({
		  success: function(usuario) {
		    // Do stuff
		    for (var usuarioO in usuario){
		    	user = usuario[usuarioO];
				}
				consultarRutas();

 		 },
				  error: function(error) {
				    alert("Error: " + error.code + " " + error.message);
				  }
		});


    	
    }

    //http://ecapy.com/variables-globales-en-javascript/
    //https://developer.chrome.com/devtools
      function consultarRutas(argument) {

      	primeraVez=0;
      	arrayLocations = [];
      	colorRandom = 0;
		
		var Rutas = Parse.Object.extend("Rutas");
		var queryRutas = new Parse.Query(Rutas);
		queryRutas.equalTo("user",user);
		if (argument != null){
			queryRutas.equalTo("NombreRuta", argument);
		}else{
			queryRutas.notEqualTo("NombreRuta","SinNombre");
			queryRutas.descending("createdAt");
			queryRutas.limit(1000);
		}
		
		queryRutas.find({
		  success: function(resultsRutas) {

		    alert("Hay " + resultsRutas.length + " rutas.");

		    for (var numR in resultsRutas){
				if (argument==null && (resultsRutas.length>0) && (resultsRutas.length-(listaRutasSelect.options.length)!=0) ) {
					listaRutasSelect.options.add(new Option( resultsRutas[numR].get('NombreRuta') ));
				}//metemos la ruta en un arreglo de rutas para despues meterlo a un select
		      
		    	var Locations = Parse.Object.extend("Locations");
				var queryLocations = new Parse.Query(Locations);
				queryLocations.equalTo("ruta", resultsRutas[numR]);
				queryLocations.descending("numCoordenada");
				queryLocations.limit(1000);
				queryLocations.find({
				  success: function(resultsLocations) {
				    
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
			        
					flightPath = new google.maps.Polyline({
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

    <script>

    	function changeDate () {
    	 	
    	 	elemSelect = document.getElementById('listaRuta');
			valueSelect = elemSelect.options[elemSelect.selectedIndex].text;

			//alert('value select text: '+valueSelect);

			consultarRutas(valueSelect);
			
    	 } 

    </script>

</head>
  <body onload="initialize()">

	<form>
			<select name="Ruta" id="listaRuta" onchange="changeDate()">
			</select>

	</form>

	<form>
			<input type="button" 
			onclick="consultarRutas()" 
			value="Ver todas las rutas...">
	</form>

    <div id="map_canvas" style="width:100%; height:80%"></div>

  </body>
</html>