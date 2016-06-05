<html>
<head>
<title>NU Staff Dormitory Service Project (NUSDS)</title>
<!--
 * Name: NU Staff Dormitory Service Project (NUSDS) Version 2.0
 * Purpose: GIST@NU (www.cgistln.nu.ac.th)
 * Date: 2015/04/20
 * Author: Chingchai Humhong (chingchaih@nu.ac.th)
 * Acknowledgement: Dr.Sittichai Choosumrong(sittichaic@nu.ac.th) and Dr.Sakda Homhuan(sakda.homhaun@gmail.com)
 !-->
<meta charset="utf-8">
<title>NU Staff Dormitory Service Project (NUSDS)</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="description" content="" />
<meta name="author" content="http://bootstraptaste.com" />
<!-- css -->
<link href="css/bootstrap.min.css" rel="stylesheet" />
<link href="css/fancybox/jquery.fancybox.css" rel="stylesheet">
<link href="css/jcarousel.css" rel="stylesheet" />
<link href="css/flexslider.css" rel="stylesheet" />
<link href="css/style.css" rel="stylesheet" />

 <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
  <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
  <!--script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"-->
  
  <script type="text/javascript" src="js/fsapi.js" onerror="alert('Error: failed to load ' + this.src)"></script>

</script>



<!-- Theme skin -->
<link href="skins/default.css" rel="stylesheet" />

<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
<!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->


<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.min.js"></script>
<script type="text/javascript" src="http://dev.openlayers.org/releases/OpenLayers-2.13.1/OpenLayers.js"></script>
<!--script src="http://maps.google.com/maps/api/js?v=3.5&sensor=false"></script-->
<script src="http://maps.google.com/maps/api/js?v=3&amp;sensor=false"></script>
<script type="text/javascript">


	OpenLayers.ProxyHost = "geoproxy.php?url="
	var  buff,svarea,route,vill,controls;
	var map,layer, click, status;

	var map;
	var y=16.74233; //100.19643,16.74233
	var x=100.19643;
	var zoom=8;

	function init(){
	map = new OpenLayers.Map({
		div: "map",
		projection: "EPSG:3857",
		displayProjection: "EPSG:4326",
		maxResolution: 'auto',
	});

	map.addControl(new OpenLayers.Control.MousePosition());
	map.addControl(new OpenLayers.Control.LayerSwitcher());

	// Add Base Map
	//var mapnik = new OpenLayers.Layer.OSM();
	var gphy = new OpenLayers.Layer.Google(
		"Google Physical",
		{type:google.maps.MapTypeId.TERRAIN , numZoomLevels: 22}
	);
	var gmap = new OpenLayers.Layer.Google(
		"Google Streets", // the default
		{type:google.maps.MapTypeId.ROADMAP, numZoomLevels: 20}
	);
	var ghyb = new OpenLayers.Layer.Google(
		"Google Hybrid",
		{type:google.maps.MapTypeId.HYBRID, numZoomLevels: 20}
	);
	var gsat = new OpenLayers.Layer.Google(
		"Google Satellite",
		{type:google.maps.MapTypeId.SATELLITE, numZoomLevels: 22}
	);
	var lonlat = new OpenLayers.LonLat(x, y).transform(
		new OpenLayers.Projection("EPSG:4326"), // transform from WGS 1984
		new OpenLayers.Projection("EPSG:3857") // to Spherical Mercator
	);

	//Add Basemap layers
	map.addLayers([ghyb]);

	map.setCenter(new OpenLayers.LonLat(x, y).transform(
		new OpenLayers.Projection("EPSG:4326"),
		map.getProjectionObject()
	), zoom);

	// Add GeoJSON Layers

		var buffer	= new OpenLayers.Layer.Vector('Buffer',{
				strategies: [new OpenLayers.Strategy.Fixed()],
				protocol: new OpenLayers.Protocol.HTTP({
					url: 'buffer.php',
					format: new OpenLayers.Format.GeoJSON()
				}),
				styleMap: new OpenLayers.StyleMap({
					"default": new OpenLayers.Style(null, {
						rules: [new OpenLayers.Rule({

							title: 'buffer',
							symbolizer: {
								"Polygon": {
									fillColor: '#FF0000',
									strokeColor: '#FF0000',
									strokeWidth : 2,
									fillOpacity: 0.2,
									graphicZIndex: 2
								}
							}
						})]
					})
				}),
				projection: new OpenLayers.Projection("EPSG:3857"),
				visibility: false,
				//{layers: region, transparent: 'true'},
				featureInfoFormat: 'application/vnd.ogc.gml',
				transitionEffect: 'resize'
			})

		var servicearea	= new OpenLayers.Layer.Vector('Driving Distance',{
				strategies: [new OpenLayers.Strategy.Fixed()],
				protocol: new OpenLayers.Protocol.HTTP({
					url: 'servicearea.php',
					format: new OpenLayers.Format.GeoJSON()
				}),
				styleMap: new OpenLayers.StyleMap({
					"default": new OpenLayers.Style(null, {
						rules: [new OpenLayers.Rule({

							title: 'servicearea',
							symbolizer: {
								"Polygon": {
									fillColor: '#00FF00',
									strokeColor: '#088A08',
									strokeWidth : 2,
									fillOpacity: 0.2,
									graphicZIndex: 2
								}
							}
						})]
					})
				}),
				projection: new OpenLayers.Projection("EPSG:3857"),
				visibility: false,
				//{layers: region, transparent: 'true'},
				featureInfoFormat: 'application/vnd.ogc.gml',
				transitionEffect: 'resize'
			})

		var route_raw	= new OpenLayers.Layer.Vector('Routing',{
				strategies: [new OpenLayers.Strategy.Fixed()],
				protocol: new OpenLayers.Protocol.HTTP({
					url: 'route.php',
					format: new OpenLayers.Format.GeoJSON()
				}),
				styleMap: new OpenLayers.StyleMap({
					"default": new OpenLayers.Style(null, {
						rules: [new OpenLayers.Rule({

							title: 'route_raw',
							symbolizer: {
								"Line": {
									fillColor: '#00FF00',
									strokeColor: '#088A08',
									strokeWidth : 2,
									fillOpacity: 0.2,
									graphicZIndex: 2
								}
							}
						})]
					})
				}),
				projection: new OpenLayers.Projection("EPSG:3857"),
				visibility: false,
				//{layers: region, transparent: 'true'},
				featureInfoFormat: 'application/vnd.ogc.gml',
				transitionEffect: 'resize'
			})

		var nu_poi	= new OpenLayers.Layer.Vector('มหาวิทยาลัยนเรศวร',{
				strategies: [new OpenLayers.Strategy.Fixed()],
				protocol: new OpenLayers.Protocol.HTTP({
					url: 'nu_poi.php',
					format: new OpenLayers.Format.GeoJSON()
				}),
				styleMap: new OpenLayers.StyleMap({
					"default": new OpenLayers.Style(null, {
						rules: [new OpenLayers.Rule({
							title: 'nu_poi',
							symbolizer: {
								"Point": {
										externalGraphic: "img/nu-icon.png",
										graphicWidth :26,
										graphicHeight:26,
										graphicYOffset: -26,
										graphicOpacity: 1
								}
							}
						})]
					})
				}),
				projection: new OpenLayers.Projection("EPSG:3857"),
				visibility: true,
				//{layers: region, transparent: 'true'},
				featureInfoFormat: 'application/vnd.ogc.gml',
				transitionEffect: 'resize'
			})

		var village	= new OpenLayers.Layer.Vector('Village',{
				strategies: [new OpenLayers.Strategy.Fixed()],
				protocol: new OpenLayers.Protocol.HTTP({
					url: 'village.php',
					format: new OpenLayers.Format.GeoJSON()
				}),
				styleMap: new OpenLayers.StyleMap({
					"default": new OpenLayers.Style(null, {
						rules: [new OpenLayers.Rule({
							title: 'village',
							symbolizer: {
								"Point": {
										externalGraphic: "img/village2.png",
										graphicWidth :24,
										graphicHeight:24,
										graphicOpacity: 1
								}
							}
						})]
					})
				}),
				projection: new OpenLayers.Projection("EPSG:3857"),
				visibility: false,
				//{layers: region, transparent: 'true'},
				featureInfoFormat: 'application/vnd.ogc.gml',
				transitionEffect: 'resize'
			})

// Style Color Layers
    var buff_style = OpenLayers.Util.applyDefaults({
		fillColor: '#0000FF',
		strokeColor: '#0101DF',
		strokeWidth : 2,
		fillOpacity: 0.2,
		graphicZIndex: 2
    }, OpenLayers.Feature.Vector.style['default']);

    var svarea_style = OpenLayers.Util.applyDefaults({
		fillColor: '#00FF00',
		strokeColor: '#088A08',
		strokeWidth : 2,
		fillOpacity: 0.2,
		graphicZIndex: 2
    }, OpenLayers.Feature.Vector.style['default']);

    var vill_style = OpenLayers.Util.applyDefaults({
	externalGraphic: "img/Home-icon.png",
	graphicWidth: 26,
	graphicHeight: 26,
	graphicYOffset: -26,
	graphicOpacity: 1
    }, OpenLayers.Feature.Vector.style['default']);

    var route_style = OpenLayers.Util.applyDefaults({
	  strokeWidth: 7,
	  strokeColor: "#FF0040",
	  hoverFillOpacity: 0.7,
	  strokeOpacity: 0.7,
	  fillOpacity: 0.6
    }, OpenLayers.Feature.Vector.style['default']);

	vill		= new OpenLayers.Layer.Vector("หมู่บ้าน",{style: vill_style});
	route		= new OpenLayers.Layer.Vector("เส้นทางสัญจร",{style: route_style});
	buff      	= new OpenLayers.Layer.Vector("รัศมี 25 กม.",{style: buff_style});
	svarea  	= new OpenLayers.Layer.Vector("ระยะทางในรัศมี 25 กม.",{style: svarea_style});

	//map.addLayers([buffer,buff,svarea,servicearea,route,nu_poi,village,vill]);
	map.addLayers([buff,svarea,route,nu_poi,vill]);


	}




function genbuffer() {
		var url = 'buffer.php';
		$.ajax({
		  url: url,
		  success: function(data){
			  var GeoJSON = new OpenLayers.Format.GeoJSON();
			  var features = GeoJSON.read(data);
			  buff.removeFeatures(buff.features);
			  buff.addFeatures(features);
		  }
		} );

		/* var controls = {
				  buff:  new OpenLayers.Control.DrawFeature(buff)
			  }
			  for (var key in controls) {
				  map.addControl(controls[key]);
			  } */
	};

// add funtion genservicearea
function genservicearea() {
		var url = 'servicearea.php';
		$.ajax({
		  url: url,
		  success: function(data){
			  var GeoJSON = new OpenLayers.Format.GeoJSON();
			  var features = GeoJSON.read(data);
			  svarea.removeFeatures(svarea.features);
			  svarea.addFeatures(features);
		  }

		});
	}	;
// add funtion genvillage
function genvill() {
		var url = 'village.php';
		$.ajax({
		  url: url,
		  success: function(data){
			  var GeoJSON = new OpenLayers.Format.GeoJSON();
			  var features = GeoJSON.read(data);
			  vill.removeFeatures(vill.features);
			  vill.addFeatures(features);
		  }

		});
	}	;

// add funtion genroute
function genroute() {
		var url = 'route.php';
		$.ajax({
		  url: url,
		  success: function(data){
			  var GeoJSON = new OpenLayers.Format.GeoJSON();
			  var features = GeoJSON.read(data);
			  route.removeFeatures(route.features);
			  route.addFeatures(features);
		  }

		});
	};



 function toggleControl(element) {
            for (key in controls) {
				if (element.value == key && element.checked) {
                    controls[key].activate();
                } 	else {
                    controls[key].deactivate();
					}
            }
    }

</script>

								<style> 
								select{
								    width: 100%;
								    height: 35px;
								    box-sizing: border-box;
								    border-radius: 4px;
								    background-repeat: no-repeat;
								    padding: 0px 0px 0px 50px;
								    text-align: center;

								    

								}
								</style>


   <script type="text/javascript">
        function Inint_AJAX() {
           try { return new ActiveXObject("Msxml2.XMLHTTP");  } catch(e) {} //IE
           try { return new ActiveXObject("Microsoft.XMLHTTP"); } catch(e) {} //IE
           try { return new XMLHttpRequest();          } catch(e) {} //Native Javascript
           alert("XMLHttpRequest not supported");
           return null;
        };

        function dochange(src, val) {
             var req = Inint_AJAX();
             req.onreadystatechange = function () {
                  if (req.readyState==4) {
                       if (req.status==200) {
                            document.getElementById(src).innerHTML=req.responseText; //รับค่ากลับมา
                       }
                  }
             };
             req.open("GET", "localtion.php?data="+src+"&val="+val); //สร้าง connection
             req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded;charset=utf-8"); // set Header
             req.send(null); //ส่งค่า
        }

        window.onLoad=dochange('province', -1);
    </script>
    
    <script type="text/javascript">
		FireShotAPI.AutoInstall = true;

   	function openScreenshot(mode)
	{
		var w = window.open('','View captured image','width=800,height=600,toolbar=1,scrollbars=1');
		w.document.write('<p><b>This is a captured image embedded into HTML by JavaScript.</b></p>');
		var img = w.document.createElement("IMG");
		img.src = "data:image/png;base64," +  FireShotAPI.base64EncodePage(mode);
		w.document.body.appendChild(img);
	}
    </script>	

</head>
<header>
        <div class="navbar navbar-default navbar-static-top">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <div class="container-fluid" style="margin-top:0px" >
                    <img src="img/logo.png"  >
                    </div>
                </div>
            <div class="navbar-collapse collapse ">                    
  </header>

<body onload="init()">
<div class="container-fluid" style="margin-top:5px">
    <div class="container" >
      <div class="row" >

      	<div class="col-md-4">
<div class="form-group">

<form action="getroute.php" method="post" name="form" target="iframe_target" role="form"><br>

<div class="form-group">    
<fieldset>

<legend><h5>โปรดเลือกที่อยู่ของท่าน</h5></legend>
 
	จังหวัด : 
	
		<span id="province">
			<select >
				<option value="0">- เลือกจังหวัด -</option>
			</select>
		</span>






	อำเภอ :

		<span id="amphoe">
				<select >
				<option value="0">- เลือกอำเภอ -</option>
			</select>


		</span>



	ตำบล :
	
		<span   id="tambon">
			<select >
				<option value="0">- เลือกตำบล -</option>
			</select>
		</span>
	
	หมู่บ้าน :
	
		<span   id="village">
		<select >	
				<option value="0">- เลือกหมู่บ้าน -</option>
			</select>
		</span>


</fieldset>
</div>

<fieldset>
<legend><h5>โปรดเลือกระยะทาง</h5></legend>
รูปแบบการค้นหา :
<input type="radio" name="method" value="B" checked="checked" /> แบบรัศมี
<input type="radio" name="method" value="D" /> แบบเส้นทางคมนาคม
<br/>

ระยะทาง :
	<span id="distance">
		<select name="distance">
			<option >- เลือกระยะทาง -</option>
			<option value=5000>5 km.</option>
			<option value=10000>10 km.</option>
			<option value=15000>15 km.</option>
			<option value=20000>20 km.</option>
			<option value=25000 selected="selected" >25 km.</option>
			<option value=30000>30 km.</option>
			<option value=40000>40 km.</option>
			<option value=50000>50 km.</option>
		</select>
	</span>
</p>

<p align="right">


<button type="submit" value="ตกลง" onclick= "genbuffer(); genvill(); genroute(); genservicearea();" class="btn btn-primary">ตกลง </button>
<button type="reset" value="ยกเลิก" class="btn btn-primary">ยกเลิก </button>

</p>

</fieldset>

<fieldset>
	<legend><h5>ผลการสืบค้น</h5></legend>
	<iframe id="iframe_target" name="iframe_target" src="index.php" style="width:0;height:0;border:0px solid #fff;"></iframe>
	<script language="JavaScript">
		function showResult(result,km,vn)
		{
			if(result==1)
			{
				document.getElementById("divResult").innerHTML = "<font color=red>สถานะ: ไม่สามารถขอหอพักได้</font><br>จาก "+vn+" เดินทางมายังมหาวิทยาลัยนเรศวร <br> เป็นระยะทางทั้งหมด "+km+" กิโลเมตร";
			}
			else
			{
				document.getElementById("divResult").innerHTML = "<font color=green>สถานะ: สามารถขอหอพักได้</font><br>จาก "+vn+"  เดินทางมายังมหาวิทยาลัยนเรศวร <br> เป็นระยะทางทั้งหมด "+km+" กิโลเมตร";
			}
		}
	</script>
	<div id="divResult"></div>
	<!--<p> <input class="btn btn-primary pull-right" onclick="javascript:window.print()" type="button" value="สั่งพิมพ์" name="print1"></p>-->
	<p> <input class="btn btn-primary pull-right" onclick="FireShotAPI.printPage(true)" type="button" value="สั่งพิมพ์" name="print1"></p>

</fieldset>
<!--
<fieldset>
<legend>คำอธิบายสัญลักษณ์</legend>
<p align="center">

</p>
</fieldset> -->

</form>

<!-- <h3> Result </h3> -->


</div>
</div>


<div class="col-md-8">
 <div class="form-group">

	<div id="map" style="width: 110%; height: 80%;"></div>
	
 </div>
</div>
</div>
</div>
<hr>
<center><p>&copy; สถานภูมิภาคเทคโนโลยีอวกาศและภูมิสารสนเทศ ภาคเหนือตอนล่าง มหาวิทยาลัยนเรศวร อำเภอเมือง จังหวัดพิษณุโลก 65000</p></center>

</body>


<script src="js/jquery.js"></script>
<script src="js/jquery.easing.1.3.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jquery.fancybox.pack.js"></script>
<script src="js/jquery.fancybox-media.js"></script>
<script src="js/google-code-prettify/prettify.js"></script>
<script src="js/portfolio/jquery.quicksand.js"></script>
<script src="js/portfolio/setting.js"></script>
<script src="js/jquery.flexslider.js"></script>
<script src="js/animate.js"></script>
<script src="js/custom.js"></script>
</body>

</html>

