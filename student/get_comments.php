<?php
require_once 'auth_check.php';
require_once '../core/database.php';

if (!isset($_GET['id'])) exit('Invalid request');

$penelitian_id = $_GET['id'];

$query = "SELECT k.*, u.username, u.role,
          CASE 
            WHEN u.role = 'personil' THEN (SELECT nama FROM personil WHERE id = u.reference_id)
            WHEN u.role = 'mahasiswa' THEN (SELECT nama FROM mahasiswa WHERE id = u.reference_id)
            ELSE u.username 
          END as nama_pengirim
          FROM komentar_penelitian k 
          JOIN users u ON k.user_id = u.id 
          WHERE k.penelitian_id = $1 
          ORDER BY k.created_at ASC";

$result = pg_query_params($conn, $query, array($penelitian_id));

if (pg_num_rows($result) > 0) {
    while ($row = pg_fetch_assoc($result)) {
        $is_me = ($row['user_id'] == $_SESSION['user_id']);
        $align = $is_me ? 'text-end' : 'text-start';
        $bg = $is_me ? 'bg-primary text-white' : 'bg-light';
        
        echo '<div class="mb-3 ' . $align . '">';
        echo '<small class="text-muted">' . htmlspecialchars($row['nama_pengirim']) . ' (' . ucfirst($row['role']) . ')</small>';
        echo '<div class="p-2 rounded ' . $bg . '" style="display: inline-block; max-width: 80%;">';
        echo htmlspecialchars($row['isi']);
        echo '</div>';
        echo '<div class="small text-muted" style="font-size: 0.7rem;">' . date('d M Y H:i', strtotime($row['created_at'])) . '</div>';
        echo '</div>';
    }
} else {
    echo '<p class="text-center text-muted">Belum ada komentar.</p>';
}
?>
