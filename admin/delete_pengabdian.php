<?php
require_once 'auth_check.php';
require_once '../includes/config.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: manage_pengabdian.php');
    exit();
}

$id = intval($_GET['id']);

// Get pengabdian data first
$query = "SELECT gambar FROM pengabdian WHERE id = $1";
$result = pg_query_params($conn, $query, array($id));
$pengabdian = pg_fetch_assoc($result);

if (!$pengabdian) {
    header('Location: manage_pengabdian.php?error=notfound');
    exit();
}

// Delete from database
$delete_query = "DELETE FROM pengabdian WHERE id = $1";
$delete_result = pg_query_params($conn, $delete_query, array($id));

if ($delete_result) {
    // Delete image file if exists
    if ($pengabdian['gambar'] && file_exists('../public/uploads/pengabdian/' . $pengabdian['gambar'])) {
        unlink('../public/uploads/pengabdian/' . $pengabdian['gambar']);
    }
    header('Location: manage_pengabdian.php?success=delete');
} else {
    header('Location: manage_pengabdian.php?error=delete');
}

pg_close($conn);
exit();
?>
