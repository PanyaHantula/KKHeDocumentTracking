<?php
include __DIR__ . '/db-config.php';
// Fetch all departments from the database
$sql = "SELECT * 
FROM workgroups           
ORDER BY id ASC ";

$result = mysqli_query($conn, $sql);
$workgroup = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $workgroup[] = $row;
    }
} mysqli_close($conn);

// echo "<pre>";
// print_r($workgroup);
// echo "</pre>";
return $workgroup;    
?>