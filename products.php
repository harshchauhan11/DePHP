<?php
header("Access-Control-Allow-Origin: *");
include "admin/conn.php";

$json_str = file_get_contents('php://input');
$data = json_decode($json_str, true);

// $lat = $data['lat'];

// if ($lat != null && $lng != null) {
    $sql1 = "select * from delvrt.productscategorymaster";
    $result1 = pg_query($conn, $sql1);

    if ($result1) {
        $list = null;
        $json = null;
        while ($row1 = pg_fetch_array($result1)) {
			
			$sql2 = "select * from delvrt.productssubcategorymaster where categorymasterid = " . $row1['idproductscategorymaster'] . "";
			$result2 = pg_query($conn, $sql2);
			$json2 = null;
			while ($row2 = pg_fetch_array($result2)) {
				$sql3 = "select * from delvrt.products where subcategorymasterid = " . $row2['idproductssubcategorymaster'] . "";
				$result3 = pg_query($conn, $sql3);
				$json3 = null;
				while ($row3 = pg_fetch_array($result3)) {
					$json3[] = array('idChild' => (float) $row3['idproducts'], 'type' => $row3['productname']);
				}
				if($json3 != null)
					$json2[] = array('idParent' => (float) $row2['idproductssubcategorymaster'], 'type' => $row2['subcategorytype'], 'child' => $json3);
				else
					$json2[] = array('idParent' => (float) $row2['idproductssubcategorymaster'], 'type' => $row2['subcategorytype']);
			}
			$json[] = array('idGrandParent' => (float) $row1['idproductscategorymaster'], 'type' => $row1['categorytype'], 'child' => $json2);
        }
        if (sizeof($json) > 0) {
            $list = array('status' => true, 'body' => $json);
                   echo '<pre>'.json_encode($list, JSON_PRETTY_PRINT).'</pre>';
            // echo json_encode($list);
        } else {
            $list = array('status' => false, 'body' => []);
            echo json_encode($list);
        }
    } else {
        echo pg_last_error($conn);
    }
// } else {
//     $list = array('status' => false);
//     echo json_encode($list);
// }
