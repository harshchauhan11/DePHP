<?php
header("Access-Control-Allow-Origin: *");
include("admin/conn.php");

$json_str = file_get_contents('php://input');
$data = json_decode($json_str, true);
//echo $data['username'];

$uid = $data['uid'];
$etype = $data['eType'];
// $uid = "amrita";
// $pwd = "am";


$sql = "SELECT *, delvrt.ST_AsText(latlng) as am FROM delvrt.locationshistory WHERE entityId = '" . $uid . "' order by idlocationshistory desc limit 1";
$result = pg_query($conn, $sql);

if ($result) {
    $list = null;
    $json = null;
    while($row = pg_fetch_array($result)) {
        preg_match('#\((.*?)\)#', $row['am'], $match);
	$ll = explode(" ", $match[1]);
	$ln = $ll[0];
	$lt = $ll[1];

        $json[] = array('longitude' => (float)$ln, 'latitude' => (float)$lt, 'pointLatLng' => $row['am'], 'place' => $row['place']);
    }
    if(sizeof($json) > 0) {
        $list = array('status' => true, 'body' => $json);
        echo json_encode($list);
    } else {
        $list = array('status' => false, 'body' => []);
        echo json_encode($list);
    }
} else {
    echo pg_last_error($conn);
}
?>
