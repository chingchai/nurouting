<?php

   // Database connection settings
	   define("PG_DB"  , "dorm");
	   define("PG_HOST", "localhost");
	   define("PG_USER", "user");
	   define("PG_PORT", "5432");
	   define("PG_PASS", "user"); 
	   define("TABLE",   "_service_area");


   // Retrieve start point




   // FUNCTION findNearestEdge
        // Connect to database
	$con = pg_connect("dbname=".PG_DB." host=".PG_HOST." port=".PG_PORT." password=".PG_PASS." user=".PG_USER);

		//$sql = "select * from ".TABLE." where e_date <= c_date";

		$sql = "select ST_AsGeoJSON(the_geom) AS geojson from ".TABLE." ";


     /* $sql = "SELECT gid, source, target, geom,
		          ST_Distance(geom, ST_GeometryFromText(
	                   'POINT(".$lonlat[0]." ".$lonlat[1].")', 3857)) AS dist
	             FROM ".TABLE."

	             ORDER BY dist LIMIT 1"; */

				// echo "<br>";
				// echo $sql;
      $query = pg_query($con,$sql);
/*
      $edge['gid']      = pg_fetch_result($query, 0, 0);
      $edge['source']   = pg_fetch_result($query, 0, 1);
      $edge['target']   = pg_fetch_result($query, 0, 2);
      $edge['geom'] = pg_fetch_result($query, 0, 3);
*/
      // Close database connection
      pg_close($con);


// Connect to database
	$con = pg_connect("dbname=".PG_DB." host=".PG_HOST." port=".PG_PORT." password=".PG_PASS." user=".PG_USER);

   // Perform database query
   $query = pg_query($con,$sql);

   //echo $sql;

 	  // Return route as GeoJSON
   $geojson = array(
      'type'      => 'FeatureCollection',
      'features'  => array()
   );

   // Add geom to GeoJSON array
   while($edge=pg_fetch_assoc($query)) {

      $feature = array(
         'type' => 'Feature',
         'geometry' => json_decode($edge['geojson'], true),
         'crs' => array(
            'type' => 'EPSG',
            'properties' => array('code' => '3857')
         ),
         'properties' => array(

         )
      );

      // Add feature array to feature collection array
      array_push($geojson['features'], $feature);
   }


   // Close database connection
   pg_close($con);

   // Return routing result
    header('Content-type: application/json',true);
  echo json_encode($geojson);
?>
