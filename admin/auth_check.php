<?php
// File untuk cek apakah user sudah login
// Include file ini di setiap halaman admin yang perlu proteksi

session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // Jika belum login, redirect ke halaman login
    header('Location: login.php');
    exit();
}

// Fungsi untuk logout
function admin_logout() {
    session_start();
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit();
}
?>
