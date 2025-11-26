<?php
require_once 'core/database.php';

$query = "ALTER TABLE lab_profile ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP";
$result = pg_query($conn, $query);

if ($result) {
    echo "Successfully added updated_at column to lab_profile.\n";
} else {
    echo "Error: " . pg_last_error($conn) . "\n";
}
?>
