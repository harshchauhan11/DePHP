<?php
header("Access-Control-Allow-Origin: *");
include("admin/conn.php");

$json_str = file_get_contents('php://input');
$data = json_decode($json_str, true);
//echo $data['username'];

// 72.549512 23.064088
// 73.796834 18.486473
$lat = $data['lat'];
$lng = $data['lng'];

if($lat != null && $lng != null) {
	$sql = "with index_query as (
	  select *, case
		when entitytypeid = 2 then (select storeName from delvrt.stores where idstore = entityid)
		else '' end as storeName,
		case
		when entitytypeid = 2 then (select idstore from delvrt.stores where idstore = entityid)
		else 0 end as idstore,
	    st_distance(latlng, 'SRID=4326;POINT($lng $lat)') as distance,
	    ST_AsGeoJSON(latlng) as geoJSON, ST_AsText(latlng) as pointLatLng
	  from  delvrt.locations
		where entitytypeid = 2
	  order by latlng <-> 'SRID=4326;POINT($lng $lat)' limit 100
	)
	select * from index_query where distance <= 2000 order by distance limit 10;";

	$result = pg_query($conn, $sql);

	if ($result) {
	    $list = null;
	    $json = null;
	    while($row = pg_fetch_array($result)) {
		preg_match('#\((.*?)\)#', $row['pointlatlng'], $match);
		$ll = explode(" ", $match[1]);
		$ln = $ll[0];
		$lt = $ll[1];

		$json[] = array('distance' => (float)$row['distance'], 'longitude' => (float)$ln, 'latitude' => (float)$lt, 'geoJSON' => $row['geojson'], 'pointLatLng' => $row['pointlatlng'], 'place' => $row['place'], 'storeName' => $row['storename'], 'idSt' => $row['idstore']);
	    }
	    if(sizeof($json) > 0) {
		$list = array('status' => true, 'body' => $json);
	//        echo '<pre>'.json_encode($list, JSON_PRETTY_PRINT).'</pre>';
		echo json_encode($list);
	    } else {
		$list = array('status' => false, 'body' => []);
		echo json_encode($list);
	    }
	} else {
	    echo pg_last_error($conn);
	}
} else {
	$list = array('status' => false);
	echo json_encode($list);
}
?>
