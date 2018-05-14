<?php
header("Access-Control-Allow-Origin: *");
include("admin/conn.php");

$json_str = file_get_contents('php://input');
$data = json_decode($json_str, true);
//echo $data['username'];

$uid = $data['username'];
$pwd = $data['password'];
// $uid = "amrita";
// $pwd = "am";


$sql = "SELECT * FROM delvrt.users WHERE uname = '" . $uid . "' AND passwd = '" . $pwd . "' AND isActive = TRUE LIMIT 1";
$result = pg_query($conn, $sql);

if ($result) {
    $list = null;
    $json = null;
    while($row = pg_fetch_array($result)) {
        $json = array('uid' => $row['uid'], 'email' => $row['email'], 'fname' => $row['fname'], 'lname' => $row['lname'], 'token' => md5(uniqid($row['uname'], false)));
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