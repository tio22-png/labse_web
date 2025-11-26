<?php
require_once 'auth_check.php';
require_once '../core/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['penelitian_id']) && isset($_POST['isi'])) {
    $penelitian_id = $_POST['penelitian_id'];
    $isi = trim($_POST['isi']);
    $user_id = $_SESSION['user_id'];
    
    if (!empty($isi)) {
        $query = "INSERT INTO komentar_penelitian (penelitian_id, user_id, isi, created_at) VALUES ($1, $2, $3, NOW())";
        $result = pg_query_params($conn, $query, array($penelitian_id, $user_id, $isi));
        
        if ($result) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => pg_last_error($conn)]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Komentar tidak boleh kosong']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>
