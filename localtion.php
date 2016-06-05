<?php
    header("content-type: text/html; charset=utf-8");
    header ("Expires: Wed, 21 Aug 2013 13:13:13 GMT");
    header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    header ("Cache-Control: no-cache, must-revalidate");
    header ("Pragma: no-cache");

	   define("PG_DB"  , "dorm");
	   define("PG_HOST", "localhost");
	   define("PG_USER", "user");
	   define("PG_PORT", "5432");
	   define("PG_PASS", "user"); 

$con = pg_connect("dbname=".PG_DB." host=".PG_HOST." password=".PG_PASS." user=".PG_USER);
   // include "connect/connect_db.php";
    //conndb();

    $data = $_GET['data'];
    $val = $_GET['val'];

	//echo $data.$val;

        if ($data=='province') {
              echo "<select name='province' onChange=\"dochange('amphoe', this.value)\">";
              echo "<option value='0'>- เลือกจังหวัด -</option>\n";
              $result=pg_query("select * from province order by prov_nam_t");
              while($row = pg_fetch_array($result)){
                   echo "<option value='$row[prov_code]' >$row[prov_nam_t]</option>" ;
              }
        } else if ($data=='amphoe') {
              echo "<select name='amphoe' onChange=\"dochange('tambon', this.value)\">";
              echo "<option value='0'>- เลือกอำเภอ -</option>\n";
              $result=pg_query("SELECT * FROM amphoe WHERE prov_code= '$val' ORDER BY amp_nam_t");
              while($row = pg_fetch_array($result)){
                   echo "<option value=\"$row[amp_code]\" >$row[amp_nam_t]</option> " ;
              }
        } else if ($data=='tambon') {
              //echo "<select name='tambon'>\n";
			  echo "<select name='tambon' onChange=\"dochange('village', this.value)\">";
              echo "<option value='0'>- เลือกตำบล -</option>\n";
              $result=pg_query("SELECT * FROM tambon WHERE amp_code= '$val' ORDER BY tam_nam_t");
              while($row = pg_fetch_array($result)){
                   echo "<option value=\"$row[tam_code]\" >$row[tam_nam_t]</option> \n" ;
              }
		} else if ($data=='village') {
              echo "<select name='village'>\n";
              echo "<option value='0'>- เลือกหมู่บ้าน -</option>\n";
              $result=pg_query("SELECT * FROM village_new WHERE tam_code= '$val' ORDER BY vill_name");
              while($row = pg_fetch_array($result)){
                   echo "<option value=\"$row[dolacode]\" >$row[vill_name]</option> \n" ;
              }
         }
         echo "</select>\n";

        //echo pg_error();
        //closedb();
	 pg_close($con);
?>
