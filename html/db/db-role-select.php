<?php
include __DIR__ . '/db-config.php';

$sql = "SELECT *
        FROM role
        ORDER BY id ASC";

$resulte = mysqli_query($conn,$sql);
$role = [];

if ($resulte) {
    while ($row = mysqli_fetch_assoc($resulte)){
        $role[] = $row;
    }
}

mysqli_close($conn);

// echo "<pre>";
// print_r($role);
// echo "</pre>";
return $role;

?>