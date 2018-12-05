<?php 
session_start();
if (!$_SESSION){
	    header('Location: ../error.php'); 
		exit();
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset='utf-8' />
<title>Display a map</title>
<meta name='viewport' content='initial-scale=1,maximum-scale=1,user-scalable=no' />
<link href='https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,700' rel='stylesheet'>
<script src='https://api.tiles.mapbox.com/mapbox-gl-js/v0.49.0/mapbox-gl.js'></script>
<script src='sweetgreen.geojson'></script>
<link href='https://api.tiles.mapbox.com/mapbox-gl-js/v0.49.0/mapbox-gl.css' rel='stylesheet' />
      <style>

        body {
          color:#404040;
          font:400 15px/22px 'Source Sans Pro', 'Helvetica Neue', Sans-serif;
          margin:0;
          padding:0;
          -webkit-font-smoothing:antialiased;
        }

        * {
          -webkit-box-sizing:border-box;
          -moz-box-sizing:border-box;
          box-sizing:border-box;
        }

        .sidebar {
          position:absolute;
          width:33.3333%;
          height:100%;
          top:0;left:0;
          overflow:hidden;
          border-right:1px solid rgba(0,0,0,0.25);
        }
        .pad2 {
          padding:20px;
        }

        .map {
          position:absolute;
          left:33.3333%;
          width:66.6666%;
          top:0;bottom:0;
        }

        h1 {
          font-size:22px;
          margin:0;
          font-weight:400;
          line-height: 20px;
          padding: 20px 2px;
        }

        a {
          color:#404040;
          text-decoration:none;
        }

        a:hover {
          color:#101010;
        }

        .heading {
          background:#fff;
          border-bottom:1px solid #eee;
          min-height:60px;
          line-height:60px;
          padding:0 10px;
          background-color: #00853e;
          color: #fff;
        }

        .listings {
          height:100%;
          overflow:auto;
          padding-bottom:60px;
        }

        .listings .item {
          display:block;
          border-bottom:1px solid #eee;
          padding:10px;
          text-decoration:none;
        }

        .listings .item:last-child { border-bottom:none; }
        .listings .item .title {
          display:block;
          color:#00853e;
          font-weight:700;
        }

        .listings .item .title small { font-weight:400; }
        .listings .item.active .title,
        .listings .item .title:hover { color:#8cc63f; }
        .listings .item.active {
          background-color:#f8f8f8;
        }
        ::-webkit-scrollbar {
          width:3px;
          height:3px;
          border-left:0;
          background:rgba(0,0,0,0.1);
        }
        ::-webkit-scrollbar-track {
          background:none;
        }
        ::-webkit-scrollbar-thumb {
          background:#00853e;
          border-radius:0;
        }

        .marker {
          border: none;
          cursor: pointer;
          height: 56px;
          width: 56px;
          background-image: url(marker2.png);
		  
          background-color: rgba(0, 0, 0, 0);
        }

        .clearfix { display:block; }
        .clearfix:after {
          content:'.';
          display:block;
          height:0;
          clear:both;
          visibility:hidden;
        }

        /* Marker tweaks */
        .mapboxgl-popup {
          padding-bottom: 50px;
        }

        .mapboxgl-popup-close-button {
          display:none;
        }
        .mapboxgl-popup-content {
          font:400 15px/22px 'Source Sans Pro', 'Helvetica Neue', Sans-serif;
          padding:0;
          width:180px;
        }
        .mapboxgl-popup-content-wrapper {
          padding:1%;
        }
        .mapboxgl-popup-content h3 {
          background:#91c949;
          color:#fff;
          margin:0;
          display:block;
          padding:10px;
          border-radius:3px 3px 0 0;
          font-weight:700;
          margin-top:-15px;
        }

        .mapboxgl-popup-content h4 {
          margin:0;
          display:block;
          padding: 10px 10px 10px 10px;
          font-weight:400;
        }

        .mapboxgl-popup-content div {
          padding:10px;
        }

        .mapboxgl-container .leaflet-marker-icon {
          cursor:pointer;
        }

        .mapboxgl-popup-anchor-top > .mapboxgl-popup-content {
          margin-top: 15px;
        }

        .mapboxgl-popup-anchor-top > .mapboxgl-popup-tip {
          border-bottom-color: #91c949;
        }
		

	.pulse {
	  background rgba(0,0,0,0.2)
	  border-radius 50%
	  height 14px
	  width 14px
	  position absolute
	  left 50%
	  top 50%
	  margin 11px 0px 0px -12px
	  transform rotateX(55deg)
	  z-index -2
	  &:after
		content ""
		border-radius 50%
		height 40px
		width 40px
		position absolute
		margin -13px 0 0 -13px
		animation pulsate 2.5s ease-out
		animation-iteration-count infinite
		opacity 0.0
		background rgba(94, 190, 255, 0.5)
		box-shadow 0 0 1px 2px #2D99D3 
		animation-delay 1.1s
	}
	@keyframes pulsate {
	  0%
		transform scale(0.1, 0.1)
		opacity 0.0
	  50%
		opacity 1.0
	  100%
		transform scale(1.2, 1.2)
		opacity 0
	}
			
      </style>
</head>
<body>
 
     <div class='sidebar'>
      <div class='heading'>
        <h1>Our locations</h1>
      </div>
    <div id='listings' class='listings'></div>
    </div>
    <div id='map' class='map'> </div>

<script>

  // This will let you use the .remove() function later on
  if (!('remove' in Element.prototype)) {
    Element.prototype.remove = function() {
      if (this.parentNode) {
          this.parentNode.removeChild(this);
      }
    };
  }


	mapboxgl.accessToken = 'apikey';
	const map = new mapboxgl.Map({
	container: 'map',
	style: 'mapbox://styles/greykrav/cjoggl3u81rwy2so07z7np943',
		// initial position in [long, lat] format
		center: [-87.719905, 42.027887],
		// initial zoom
		zoom: 13,
		scrollZoom: true
	});
	
	
	// This adds the data to the map
  map.on('load', function (e) {
    // This is where your '.addLayer()' used to be, instead add only the source without styling a layer
    map.addSource("places", {
      "type": "geojson",
      "data": stores
    });
	//try to fix how you deal w the raster image
	
	  map.addLayer({
    id: 'greykrav.24jx3bmu',
    type: 'raster',
    source: {
      type: 'raster',
      tiles: ['https://api.mapbox.com/v4/greykrav.24jx3bmu/{z}/{x}/{y}.png?access_token='],
    }
	});

	
    // Initialize the list
    buildLocationList(stores);

  });

  // This is where your interactions with the symbol layer used to be
  // Now you have interactions with DOM markers instead
  stores.features.forEach(function(marker, i) {
    // Create an img element for the marker
    var el = document.createElement('div');
    el.id = "marker-" + i;
    el.className = 'marker';
    // Add markers to the map at all points
    new mapboxgl.Marker(el, {offset: [0, -23]})
        .setLngLat(marker.geometry.coordinates)
        .addTo(map);

	
		
    el.addEventListener('click', function(e){
        // 1. Fly to the point
        flyToStore(marker);

        // 2. Close all other popups and display popup for clicked store
        createPopUp(marker);

        // 3. Highlight listing in sidebar (and remove highlight for all other listings)
        var activeItem = document.getElementsByClassName('active');

        e.stopPropagation();
        if (activeItem[0]) {
           activeItem[0].classList.remove('active');
        }

        var listing = document.getElementById('listing-' + i);
        listing.classList.add('active');

    });
  });

  function flyToStore(currentFeature) {
    map.flyTo({
        center: currentFeature.geometry.coordinates,
        zoom: 15,
		speed: .2,
		curve: 1,
		pitch: 45
      });
  }


  <!-- function flyToStore(currentFeature) { -->
      <!-- for (i = 0; i < stores.features.length; i++) { -->
     <!-- var currentFeature = stores.features[i]; -->
	<!-- if (currentFeature){ -->
    <!-- map.flyTo({ -->
        <!-- center: currentFeature.geometry.coordinates, -->
        <!-- zoom: 20, -->
		<!-- speed: .2, -->
		<!-- curve: 1, -->
		<!-- pitch: 100 -->
      <!-- }); -->
	  <!-- } else { -->
	      <!-- map.flyTo({ -->
        <!-- center: currentFeature.geometry.coordinates, -->
        <!-- zoom: 14, -->
		<!-- speed: .2, -->
		<!-- curve: 1, -->
		<!-- pitch: 45 -->
      <!-- }); -->
	  
	  <!-- } -->
  <!-- } -->
  <!-- } -->


  function createPopUp(currentFeature) {
    var popUps = document.getElementsByClassName('mapboxgl-popup');
    if (popUps[0]) popUps[0].remove();


    var popup = new mapboxgl.Popup({closeOnClick: false})
          .setLngLat(currentFeature.geometry.coordinates)
          .setHTML('<h3>Greystone Associates</h3>' +
            '<h4>' + currentFeature.properties.address + '</h4>')
          .addTo(map);
  }


  function buildLocationList(data) {
    for (i = 0; i < data.features.length; i++) {
      var currentFeature = data.features[i];
      var prop = currentFeature.properties;

      var listings = document.getElementById('listings');
      var listing = listings.appendChild(document.createElement('div'));
      listing.className = 'item';
      listing.id = "listing-" + i;

      var link = listing.appendChild(document.createElement('a'));
      link.href = '#';
      link.className = 'title';
      link.dataPosition = i;
      link.innerHTML = prop.address;

      var details = listing.appendChild(document.createElement('div'));
      details.innerHTML = prop.city;
      if (prop.phone) {
        details.innerHTML += ' &middot; ' + prop.phoneFormatted;
      }



      link.addEventListener('click', function(e){
        // Update the currentFeature to the store associated with the clicked link
        var clickedListing = data.features[this.dataPosition];

        // 1. Fly to the point
        flyToStore(clickedListing);

        // 2. Close all other popups and display popup for clicked store
        createPopUp(clickedListing);

        // 3. Highlight listing in sidebar (and remove highlight for all other listings)
        var activeItem = document.getElementsByClassName('active');

        if (activeItem[0]) {
           activeItem[0].classList.remove('active');
        }
        this.parentNode.classList.add('active');

      });
    }
  }
    </script>
  </body>
</html>