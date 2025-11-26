<?php
require_once __DIR__ . '/core/database.php';

$query = "SELECT id, kategori, judul FROM lab_profile ORDER BY kategori, id";
$result = pg_query($conn, $query);

if (!$result) {
    echo "Query failed: " . pg_last_error($conn) . "\n";
    exit;
}

echo "ID | Kategori | Judul\n";
echo "---|---|---\n";
while ($row = pg_fetch_assoc($result)) {
    echo $row['id'] . " | " . $row['kategori'] . " | " . $row['judul'] . "\n";
}
?>
