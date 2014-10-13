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

    //http://ecapy.com/variables-globales-en-javascript/
      function initialize(mes,dia,hora,mesFin,diaFin,horaFin) {

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

		//http://www.w3schools.com/jsref/jsref_utc.asp
		//http://www.w3schools.com/jsref/jsref_obj_date.asp
		// los ultimos 4 se pueden dejar en blanco
		//new Date(year, month, day, hours, minutes, seconds, milliseconds) 

		if (!mes || !dia || !hora || !mesFin || !diaFin || !horaFin) {
			//alert("entro a null");
			var dateToCompare = new Date(2014,9,8,6); // 5 horas menos para que respete la zona horaria prueba con 11:00
			var dateToCompareFin = new Date(2014,9,8,7); // prueba con 12:00
		}else{	
			
			var dateToCompare = new Date(2014,mes,dia,hora); // 5 horas menos para que respete la zona horaria prueba con 11:00
			var dateToCompareFin = new Date(2014,mesFin,diaFin,horaFin); // prueba con 12:00

			/*alert('mes='+mes+'-dia='+dia+'-hora='+hora);
			alert("Date UTC = " + (dateToCompare).toUTCString());
			alert('mesFin='+mesFin+'-diaFin='+diaFin+'-horaFin='+horaFin);
			alert("DateFin UTC = " + (dateToCompareFin).toUTCString());*/
		}

		

		var Rutas = Parse.Object.extend("Rutas");
		var queryRutas = new Parse.Query(Rutas);
		//queryRutas.equalTo("NombreRuta", "prueba al mercado version 6.0");
		queryRutas.notEqualTo("NombreRuta","SinNombre");
		queryRutas.ascending("createdAt");
		// si dateToCompare y dateToCompareFin se cambiaron
		queryRutas.greaterThanOrEqualTo("createdAt", dateToCompare);
		queryRutas.lessThanOrEqualTo("createdAt", dateToCompareFin);

		queryRutas.limit(1000);
		queryRutas.find({
		  success: function(resultsRutas) {
			
		    alert("Hay " + resultsRutas.length + " rutas.");

		    var holaa = 1;

		    for (var numR in resultsRutas){
		    	if (holaa == 1){
					alert(numR + ' - ' + resultsRutas[numR].get('NombreRuta'));
					//alert("dato devuelto => "+resultsRutas[numR].createdAt); //Parse 6:00
					//alert("dato devuelto UTC => "+(resultsRutas[numR].createdAt).toUTCString()); //Parse UTC 11:00
					holaa ++;
				}
		      
		      
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

    <script>

    	var mes = 9;
    	var dia = 1;
    	var hora = 20;
    	var mesFin = 9;
    	var diaFin = 1;
    	var horaFin = 20;
    	var elemSelect;
    	var valueSelect;

    	function changeDate (argument, args2) {
    	 	
    	 	elemSelect = document.getElementById(argument);
			valueSelect = elemSelect.options[elemSelect.selectedIndex].value;

			if (args2 == 2 || args2 == 5) {
				
				switch (parseInt(valueSelect)) {
					case 0:
				        valueSelect = 19;
				        break;
				    case 1:
				        valueSelect = 20;
				        break;
				    case 2:
				        valueSelect = 21;
				        break;
				    case 3:
				        valueSelect = 22;
				        break;
				    case 4:
				        valueSelect = 23;
				        break;
				    case 5:
				        valueSelect = 0;
				        break;
				    case 6:
				    case 7:
				    case 8:
				    case 9:
				    case 10:
				    case 11:
				    case 12:
				    case 13:
				    case 14:
				    case 15:
				    case 16:
				    case 17:
				    case 18:
				    case 19:
				    case 20:
				    case 21:
				    case 22:
				    case 23:
				    	valueSelect = (valueSelect-5);
				        break;
				}

			}

			switch (args2) {
			    case 0:
			        mes = valueSelect;
			        break;
			    case 1:
			        dia = valueSelect;
			        break;
			    case 2:
			        hora = valueSelect;
			        break;
			    case 3:
			        mesFin = valueSelect;
			        break;
			    case 4:
			        diaFin = valueSelect;
			        break;
			    case 5:
			        horaFin = valueSelect;
			        break;
			}

    	 } 

    	 function buscarMapa (argument) {
    	 	initialize(mes,dia,hora,mesFin,diaFin,horaFin);
    	 }

    </script>

</head>
  <body onload="initialize()">

	<form>
			<select name="mes" id="mes" onchange="changeDate('mes',0)">
				<option value="9">Octubre</option>
				<option value="10">Noviembre</option>
			</select>

			<select name="dia" id="dia" onchange="changeDate('dia',1)">
				<option value="1">1</option>
				<option value="2">2</option>
				<option value="3">3</option>
				<option value="4">4</option>
				<option value="5">5</option>
				<option value="6">6</option>
				<option value="7">7</option>
				<option value="8">8</option>
				<option value="9">9</option>
				<option value="10">10</option>
				<option value="11">11</option>
				<option value="12">12</option>
				<option value="13">13</option>
				<option value="14">14</option>
				<option value="15">15</option>
				<option value="16">16</option>
				<option value="17">17</option>
				<option value="18">18</option>
				<option value="19">19</option>
				<option value="20">20</option>
				<option value="21">21</option>
				<option value="22">22</option>
				<option value="23">23</option>
				<option value="24">24</option>
				<option value="25">25</option>
				<option value="26">26</option>
				<option value="27">27</option>
				<option value="28">28</option>
				<option value="29">29</option>
				<option value="30">30</option>
				<option value="31">31</option>
			</select>

			<select name="hora" id="hora" onchange="changeDate('hora',2)">
				<option value="1">1 am</option>
				<option value="2">2 am</option>
				<option value="3">3 am</option>
				<option value="4">4 am</option>
				<option value="5">5 am</option>
				<option value="6">6 am</option>
				<option value="7">7 am</option>
				<option value="8">8 am</option>
				<option value="9">9 am</option>
				<option value="10">10 am</option>
				<option value="11">11 am</option>
				<option value="12">12 pm</option>
				<option value="13">1 pm</option>
				<option value="14">2 pm</option>
				<option value="15">3 pm</option>
				<option value="16">4 pm</option>
				<option value="17">5 pm</option>
				<option value="18">6 pm</option>
				<option value="19">7 pm</option>
				<option value="20">8 pm</option>
				<option value="21">9 pm</option>
				<option value="22">10 pm</option>
				<option value="23">11 pm</option>
				<option value="0">12 am</option>
			</select>
	</form>

	<form>
			<select name="mesFin" id="mesFin" onchange="changeDate('mesFin',3)">
				<option value="9">Octubre</option>
				<option value="10">Noviembre</option>
			</select>

			<select name="diaFin" id="diaFin" onchange="changeDate('diaFin',4)">
				<option value="1">1</option>
				<option value="2">2</option>
				<option value="3">3</option>
				<option value="4">4</option>
				<option value="5">5</option>
				<option value="6">6</option>
				<option value="7">7</option>
				<option value="8">8</option>
				<option value="9">9</option>
				<option value="10">10</option>
				<option value="11">11</option>
				<option value="12">12</option>
				<option value="13">13</option>
				<option value="14">14</option>
				<option value="15">15</option>
				<option value="16">16</option>
				<option value="17">17</option>
				<option value="18">18</option>
				<option value="19">19</option>
				<option value="20">20</option>
				<option value="21">21</option>
				<option value="22">22</option>
				<option value="23">23</option>
				<option value="24">24</option>
				<option value="25">25</option>
				<option value="26">26</option>
				<option value="27">27</option>
				<option value="28">28</option>
				<option value="29">29</option>
				<option value="30">30</option>
				<option value="31">31</option>
			</select>

			<select name="horaFin" id="horaFin" onchange="changeDate('horaFin',5)">
				<option value="1">1 am</option>
				<option value="2">2 am</option>
				<option value="3">3 am</option>
				<option value="4">4 am</option>
				<option value="5">5 am</option>
				<option value="6">6 am</option>
				<option value="7">7 am</option>
				<option value="8">8 am</option>
				<option value="9">9 am</option>
				<option value="10">10 am</option>
				<option value="11">11 am</option>
				<option value="12">12 pm</option>
				<option value="13">1 pm</option>
				<option value="14">2 pm</option>
				<option value="15">3 pm</option>
				<option value="16">4 pm</option>
				<option value="17">5 pm</option>
				<option value="18">6 pm</option>
				<option value="19">7 pm</option>
				<option value="20">8 pm</option>
				<option value="21">9 pm</option>
				<option value="22">10 pm</option>
				<option value="23">11 pm</option>
				<option value="0">12 am</option>
			</select>
	</form>

	<form>
			<input type="button" 
			onclick="buscarMapa()" 
			value="Buscar...">
	</form>


    <div id="map_canvas" style="width:100%; height:80%"></div>

  </body>
</html>