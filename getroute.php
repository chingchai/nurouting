<?php
	   define("PG_DB"  , "dorm");
	   define("PG_HOST", "localhost");
	   define("PG_USER", "user");
	   define("PG_PORT", "5432");
	   define("PG_PASS", "user");

		$con = pg_connect("dbname=".PG_DB." host=".PG_HOST." password=".PG_PASS." user=".PG_USER);

    $province_id = $_POST['province'];
    $amphur_id = $_POST['amphoe'];
    $district_id = $_POST['tambon'];
    $vill_id = $_POST['village'];
		$distance = $_POST['distance'];

    $sql_1 = "SELECT * FROM province WHERE prov_code = '$province_id' ";
    $result_1 = pg_query($sql_1);
    $row_1 = pg_fetch_array($result_1);
    $province_name = $row_1['prov_nam_t'];

    $sql_2 = "SELECT * FROM amphoe WHERE amp_code = '$amphur_id' ";
    $result_2 = pg_query($sql_2);
    $row_2 = pg_fetch_array($result_2);
    $amphur_name = $row_2['amp_nam_t'];

    $sql_3 = "SELECT * FROM tambon WHERE tam_code = '$district_id' ";
    $result_3 = pg_query($sql_3);
    $row_3 = pg_fetch_array($result_3);
    $district_name = $row_3['tam_nam_t'];

		$sql_4 = "SELECT * FROM village_new WHERE dolacode = '$vill_id' ";
    $result_4 = pg_query($sql_4);
    $row_4 = pg_fetch_array($result_4);
    $vill_name = $row_4['vill_name'];
		$vill_x = $row_4['x'];
		$vill_y = $row_4['y'];


switch($_REQUEST['method']) {

		case 'B' : // ST_Buffer

		   $sql = "

			DROP VIEW IF EXISTS _village_sel;
			CREATE VIEW _village_sel AS SELECT gid,dolacode,vill_name,geom FROM village_new WHERE dolacode = '".$vill_id."' ;

			DROP VIEW IF EXISTS _temp_route;
			CREATE VIEW _temp_route AS SELECT * FROM pgr_fromAtoB('trans',11154122.023,1890923.738,".$vill_x.",".$vill_y.");

			DROP VIEW IF EXISTS _nu_buffer25km;
			CREATE VIEW _nu_buffer25km AS
			SELECT gid, name, x, y, st_buffer(geom ,".$distance.",'quad_segs=50 endcap=round join=round mitre_limit=5') AS geom FROM nu_poi;


			";
			$sql_5 = "SELECT ST_Within(a.geom, b.geom) AS CHECK FROM _village_sel a, _nu_buffer25km b;";
			$result_5 = pg_query($sql_5);
			$row_5 = pg_fetch_array($result_5);
			$check = $row_5['check'];
		   break;


		case 'D' : // prg_Driving Distance

		$sql = "
			DROP VIEW IF EXISTS _village_sel;
			CREATE VIEW _village_sel AS SELECT gid,dolacode,vill_name,geom FROM village_new WHERE dolacode = '".$vill_id."' ;

			DROP VIEW IF EXISTS _temp_route;
			CREATE VIEW _temp_route AS SELECT * FROM pgr_fromAtoB('trans',11152747.312,1892027.468,".$vill_x.",".$vill_y.");

			DROP TABLE IF EXISTS _service_node;
			DROP TABLE IF EXISTS _service_area;

			CREATE TABLE _service_node AS
				SELECT * FROM public.trans_vertices_pgr n JOIN
					(SELECT seq, id1 AS node, cost FROM pgr_drivingdistance('
						SELECT gid AS id,
							source::integer,
							target::integer,
							length::double precision AS cost
						FROM public.trans',
							31627,".$distance.",false,false))AS route ON n.id = route.node;
			CREATE TABLE _service_area AS
				SELECT ST_SetSRID(ST_MakePolygon(ST_AddPoint(foo.openline, ST_StartPoint(foo.openline)))::geometry,3857)as the_geom
					FROM(SELECT ST_Makeline(points order by id) AS openline
					FROM(SELECT ST_Makepoint(X,Y) AS points ,row_number() over() AS id
					FROM pgr_alphAShape('SELECT id::integer, ST_X(the_geom)::float AS X, ST_Y(the_geom)::float AS Y  FROM _service_node')) AS a) AS foo;



 	            ";
 	           // echo $sql;
			$sql_6 = "SELECT ST_Within(a.geom, b.the_geom) AS CHECK FROM _village_sel a, _service_area b;";
			$result_6 = pg_query($sql_6);
			$row_6 = pg_fetch_array($result_6);
			$check = $row_6['check'];
			
		   break;


 } // close switch

// Connect to database
   $con = pg_connect("dbname=".PG_DB." host=".PG_HOST." password=".PG_PASS." user=".PG_USER);

   // Perform database query
   $query = pg_query($con,$sql);
/*
		//Select village from user
		// vill
		pg_query("DROP VIEW IF EXISTS _village_sel;");
		pg_query("CREATE VIEW _village_sel AS SELECT gid,dolacode,vill_name,geom FROM village_new WHERE dolacode = '".$vill_id."' ;");

		// routing
		pg_query("DROP VIEW IF EXISTS _temp_route;");
		pg_query("CREATE VIEW _temp_route AS SELECT * FROM pgr_fromAtoB('trans',11153388.429,1890846.578,".$vill_x.",".$vill_y.");");

		// service area
		pg_query("DROP TABLE IF EXISTS _service_node; DROP TABLE IF EXISTS _service_area;");
		pg_query("
						CREATE TABLE _service_node AS
						SELECT * FROM public.trans_vertices_pgr n JOIN
						(SELECT seq, id1 AS node, cost FROM pgr_drivingdistance('
							  SELECT gid AS id,
								  source::integer,
								  target::integer,
								  length::double precision AS cost
							  FROM public.trans',
							  31627,".$distance.",false,false))AS route ON n.id = route.node;
						CREATE TABLE _service_area AS
						SELECT ST_SetSRID(ST_MakePolygon(ST_AddPoint(foo.openline, ST_StartPoint(foo.openline)))::geometry,3857)as the_geom
							FROM(SELECT ST_Makeline(points order by id) AS openline
							FROM(SELECT ST_Makepoint(X,Y) AS points ,row_number() over() AS id
							FROM pgr_alphAShape('SELECT id::integer, ST_X(the_geom)::float AS X, ST_Y(the_geom)::float AS Y  FROM _service_node')) AS a) AS foo;"
				);
	*/
		// check
		//$sql_5 = "SELECT ST_Within(a.geom, b.the_geom) AS CHECK FROM _village_sel a, _service_area b;";
/*		$sql_5 = "SELECT ST_Within(a.geom, b.geom) AS CHECK FROM _village_sel a, _nu_buffer25km b;";
		$result_5 = pg_query($sql_5);
		$row_5 = pg_fetch_array($result_5);
		$check = $row_5['check'];
*/
		//sum cost
		$sql_7 = "select ROUND(sum(cost)/1000) as sumcost from _temp_route";
		$result_7 = pg_query($sql_7);
		$row_7 = pg_fetch_array($result_7);
		$sumcost = $row_7['sumcost'];



		if((string)$check == "t"){
			echo "<script>window.top.window.showResult('1',".$sumcost.",'".$vill_name."');</script>";
			//echo '<script>alert('.$sumcost.');</script>';
			//echo "</br>จาก ".$vill_name." เดินทางมายังมหาวิทยาลัยนเรศวร </br>เป็นระยะทางทั้งหมด ".$sumcost." กิโลเมตร  <br>สถานะ: ไม่สามารถขอหอพักได้ </br>";
		}else{
			echo "<script>window.top.window.showResult('2',".$sumcost.",'".$vill_name."');</script>";
			//echo '<script>alert('.$sumcost.');</script>';
			//echo "</br>จาก ".$vill_name." เดินทางมายังมหาวิทยาลัยนเรศวร </br>เป็นระยะทางทั้งหมด ".$sumcost." กิโลเมตร <br>สถานะ: สามารถขอหอพักได้ </br>";
		};

		pg_close($con);
		?>
