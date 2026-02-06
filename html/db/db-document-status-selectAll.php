<?php
include __DIR__ . '/db-config.php';
// Fetch all departments from the database
$sql = "SELECT * 
FROM document_status           
ORDER BY id ASC ";

$result = mysqli_query($conn, $sql);
$DocumentStatus = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $DocumentStatus[] = $row;
    }
} mysqli_close($conn);

// echo "<pre>";
// print_r($DocumentStatus);
// echo "</pre>";

return $DocumentStatus;    

?>