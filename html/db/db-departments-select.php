<?php
include __DIR__ . '/db-config.php';

$sql = "SELECT 
            dept.id AS department_id,
            dept.name AS department_name,
            dept.workgroup_id AS workgroup_id,
            wg.name AS workgroup_name
        FROM department AS dept
        LEFT JOIN workgroups AS wg ON dept.workgroup_id = wg.id
        WHERE dept.id != '000'
        ORDER BY dept.id ASC";

$result = mysqli_query($conn, $sql);
$departments = [];

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $departments[] = $row;
    }
} mysqli_close($conn);

//  // debug
//  echo "<pre>";
//  print_r($departments);
//  echo "</pre>";

return $departments;    
?>