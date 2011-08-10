<!DOCTYPE html>
<head>
    <title></title>
    <script type="application/javascript" src="https://raw.github.com/billyist/doodle-js/master/build/doodle.js">
    </script>
    <script type="application/javascript">
        
        /* Examples from "Foundation ActionScript 3.0 Animation: Making Things Move",
         * Written by Keith Peters, Copyright 2007.
         * http://www.amazon.com/gp/product/1590597915?tag=de05f-20
         *
         * Ported to JavaScript by Billy Lamberta using the Doodle.js library
         * available at http://github.com/billyist/doodle-js
         */
        //http://lamberta.org/doodle-js/
        //http://lamberta.org/doodle-js/doc/api/graphics.html
        //http://lamberta.org/doodle-js/doc/examples/making-things-move/
        
        //important! http://www.efeion.com/canvastest/test.html see how he got teh canvas and drawing with raw js, try to combine with zepto.
        
		var colourList = [];

        function init_scene(){
        
            var display = doodle.createDisplay("#display");
            display.frameRate = 30;
            var layer0 = display.createLayer();
            var easing = 0.2, targetX, targetY;
            
            targetX = display.width / 2;
            targetY = 0;//display.height/2;
            var list = [];
            
            for (var i = 0; i < 5; i++) {
                var strip = doodle.createSprite();
                layer0.addChild(strip);
                
                strip.graphics.beginFill(colourList[i]);
                strip.graphics.rect(0, 0, display.width / 5, display.height);
                strip.graphics.endFill();
                strip.x = (display.width / 5) * i;
                strip.y = display.height;
                
                strip.addListener(doodle.events.Event.ENTER_FRAME, onEnterFrame);
                list.push(strip);
            }
            
            function onEnterFrame(event){
                for (var m = 1; m < 6; m++) {
                    //list[m].x += 1*m+1;
                    //ball.x += 1;
                    
                    var vx = (targetX - list[m - 1].x) * (easing * m), vy = (targetY - list[m - 1].y) * (easing * m);
                    //list[m].x += vx;
                    list[m - 1].y += vy;
                }
            }
        }
    </script>
    <script type="application/javascript">
        //https://developer.mozilla.org/en/drawing_graphics_with_canvas
        //https://developer.mozilla.org/en/canvas_tutorial
        //http://microjs.com/#canvas
        function draw(){
            var canvas = document.getElementById("canvas");
            
            for (var i = 0; i < 5; i++) {
                var ctx = canvas.getContext("2d");
                
                ctx.fillStyle = "rgb(200,0,0)";
                ctx.fillRect(10 * i, 10 * i, 55, 50);
                
                ctx.fillStyle = "rgba(0, 0, 200, 0.5)";
                ctx.fillRect(30 * i, 30 * i, 55, 50);
            }
            
        }

        //http://raphaeljs.com/reference.html#animate
    </script>
</head>
<!--<body onload="init_scene();">-->
<body>
	    <div id="display" style="background-color: #fff; width: 800px; height: 600px;"></div>

   			<?php      
            
            error_reporting(E_ALL);
			ini_set('display_errors', '1');

//Your API Key is ******************
//Your secret is ******************
//http://learn.adobe.com/wiki/display/kulerdev/B.+Feeds
//http://www.last.fm/api/rest
//http://blog.sherifmansour.com/?p=302	
	
	
	
	if ( isset( $_GET['u'] ) ) {
		$user = $_GET['u'];
	} else {
		$user = 'Obvio';
	}
	
	$base = 'http://ws.audioscrobbler.com/2.0/';
/*

To get your own key visit http://kuler.adobe.com/api and then uncomment this

	$key = '************';
	$secret = '************';
	$kuler = '*********';

*/

	//$user = $tempUser;

	
	//get recent tracks
	$recentTracks = new SimpleXMLElement( file_get_contents( $base.'?method=user.getrecenttracks&user='.urlencode($user).'&'.$key ) );
	$artist = $recentTracks->recenttracks->track[0]->artist;
	
	//get tags for artist
	$topTags = new SimpleXMLElement( file_get_contents( $base.'?method=artist.getTopTags&artist='.urlencode($artist).'&'.$key ) );
	$tag = $topTags->toptags->tag[0]->name;
	
	//get scheme for tag
	$colours = new SimpleXMLElement( file_get_contents( 'http://kuler-api.adobe.com/rss/search.cfm?searchQuery=tag:'.urlencode($tag).'&key='.$kuler ) );
	
	if ( $colours->channel->recordCount == 0 ){
		echo 'No matches for tag '.$tag;
		$colours = new SimpleXMLElement( file_get_contents( 'http://kuler-api.adobe.com/rss/search.cfm?searchQuery='.urlencode($tag).'&key='.$kuler ) );
	} else {
		//
	}
	$namespace = $colours->channel->item[0]->getNameSpaces( true );
	$ns = $colours->channel->item[0]->children( $namespace['kuler'] ); //"http://kuler.adobe.com/kuler/API/rss/"
	
	
	
	echo '<script type="text/javascript">';
	
	foreach ( $ns->themeItem->themeSwatches as $swatch ) {
		//var_dump($swatch);
		foreach ( $swatch as $colour ){
			//var_dump($colour->swatchHexColor);
			
			echo "colourList.push(0x$colour->swatchHexColor);\n";
			
			//echo "<div style='background:#$colour->swatchHexColor; width:50px; height:50px'>$colour->swatchHexColor</div>";
		}
	}
	echo 'init_scene();</script>';
	
	
	//var_dump($colours);
		
	//var_dump($colours->channel->item[0]->title);
	//var_dump($colours->channel->item[0]->enclosure->link->url);
	//var_dump($colours->channel->item[0]->description);

	?>
		
    <canvas id="canvas" width="300" height="300">
    </canvas>
    <img src="<?php echo $colours->channel->item[0]->enclosure->link->url ?>" />
</body>
</html>