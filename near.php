<?php
header("Access-Control-Allow-Origin: *");
include("admin/conn.php");

$json_str = file_get_contents('php://input');
$data = json_decode($json_str, true);
//echo $data['username'];

// 72.549512 23.064088
// 73.796834 18.486473
$lat = 18.486473;
$lng = 73.796834;

$sql = "with index_query as (
  select *, case
        when entitytypeid = 2 then (select storeName from delvrt.stores where idstore = entityid)
        when entitytypeid = 1 then (select fname from delvrt.users where uid = entityid)
        else '' end as storeName,
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
        $json[] = array('distance' => $row['distance'], 'geoJSON' => $row['geojson'], 'pointLatLng' => $row['pointlatlng'], 'place' => $row['place'], 'storeName' => $row['storename']);
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
?>