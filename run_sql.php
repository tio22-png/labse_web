<?php
require_once 'core/database.php';

$sql = file_get_contents('database/create_landing_page_content.sql');

if (!$sql) {
    die("Could not read SQL file");
}

$result = pg_query($conn, $sql);

if ($result) {
    echo "Successfully created and seeded landing_page_content table.\n";
} else {
    echo "Error: " . pg_last_error($conn) . "\n";
}
?>
