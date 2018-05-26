<?php
header("Access-Control-Allow-Origin: *");
include("admin/conn.php");

$json_str = file_get_contents('php://input');
$data = json_decode($json_str, true);

$sid = $data['sid'];
$auth = $data['token'];

$sql = "select l.place, storename, createdon, (select stype from delvrt.storetypemaster where idstoretypemaster = storetypeid) as storetype FROM delvrt.stores inner join delvrt.locations l on idstore = l.entityid WHERE idstore = $sid";

$result = pg_query($conn, $sql);

if ($result) {
    $list = null;
    $json = null;
    while($row = pg_fetch_array($result)) {
        $json[] = array('storeName' => ucwords($row['storename']), 'place' => ucwords($row['place']), 'storeType' => ucwords(preg_replace('/_/', ' ', $row['storetype'])), 'joinedOn' => $row['createdon']);
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
