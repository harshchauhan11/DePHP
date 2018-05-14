<?php
header("Access-Control-Allow-Origin: *");
include("admin/conn.php");

$json_str = file_get_contents('php://input');
$data = json_decode($json_str, true);

$uid = $data['uid'];
$etype = $data['eType'];
$token = $data['token'];
$lat = $data['lat'];
$lng = $data['lng'];
$place = $data['place'];

if($uid > 0 && $etype > 0 && $lat != null && $lng != null) {
	$sql = "INSERT INTO delvrt.locations (entityTypeId, entityId, latlng, place) VALUES ($etype, $uid, 'SRID=4326;POINT($lng $lat)', '$place')";
	$result = pg_query($conn, $sql);

	if ($result) {
	        $list = array('status' => true);
	        echo json_encode($list);
	} else {
	        $list = array('status' => false);
	        echo json_encode($list);
	}
} else {
        $list = array('status' => false);
        echo json_encode($list);
}
?>