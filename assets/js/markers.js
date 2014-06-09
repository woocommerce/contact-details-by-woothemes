function createMarker(map,point,root,the_link,the_title,color,callout,plugin_path) {

	var baseIcon = root + "assets/icons/shadow.png";
	var blueIcon = root + "assets/icons/blue-dot.png";
	var redIcon = root + "assets/icons/red-dot.png";
	var greenIcon = root + "assets/icons/green-dot.png";
	var yellowIcon = root + "assets/icons/yellow-dot.png";
	var tealIcon = root + "assets/icons/teal-dot.png";
	var blackIcon = root + "assets/icons/black-dot.png";
	var whiteIcon = root + "assets/icons/white-dot.png";
	var purpleIcon = root + "assets/icons/purple-dot.png";
	var pinkIcon = root + "assets/icons/pink-dot.png";
	var customIcon = color;

	var image = root + "assets/icons/red-dot.png";

	if(color === 'blue')			{ image = blueIcon; }
	else if(color === 'red')		{ image = redIcon; }
	else if(color === 'green')		{ image = greenIcon; }
	else if(color === 'yellow')		{ image = yellowIcon; }
	else if(color === 'teal')		{ image = tealIcon; }
	else if(color === 'black')		{ image = blackIcon; }
	else if(color === 'white')		{ image = whiteIcon; }
	else if(color === 'purple')		{ image = purpleIcon; }
	else if(color === 'pink')		{ image = pinkIcon; }
	else { image = customIcon; }

	var marker = new google.maps.Marker({
    	map:map,
   		draggable:false,
    	animation: google.maps.Animation.DROP,
    	position: point,
    	icon: image,
    	title: the_title
  	});

  	var infowindow = new google.maps.InfoWindow({
        content: callout
    });

  	google.maps.event.addListener(marker, 'click', function() {
  		if ( callout === '' ) {
  			window.location = the_link;
  		} else {
  			infowindow.open(map,marker);
  		}
  	});

  	return marker;

}