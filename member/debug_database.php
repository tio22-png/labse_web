<?php
require_once 'auth_check.php';
require_once '../includes/config.php';

// Check penelitian data
$query = "SELECT id, judul, LENGTH(judul) as judul_len FROM penelitian ORDER BY id";
$result = pg_query($conn, $query);

echo "<!DOCTYPE html><html><head><title>Debug Database</title></head><body>";
echo "<h1>Database Check - Penelitian Table</h1>";
echo "<table border='1' cellpadding='10'>";
echo "<tr><th>ID</th><th>Judul Length</th><th>Judul (Raw)</th></tr>";

while ($row = pg_fetch_assoc($result)) {
    echo "<tr>";
    echo "<td>" . $row['id'] . "</td>";
    echo "<td>" . $row['judul_len'] . "</td>";
    echo "<td><pre>" . htmlspecialchars($row['judul']) . "</pre></td>";
    echo "</tr>";
}

echo "</table>";
echo "</body></html>";
?>
