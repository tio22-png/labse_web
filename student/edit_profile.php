<?php
require_once 'auth_check.php';
require_once '../core/database.php';

$student_id = $_SESSION['student_id'];
$error = '';
$success = '';

// Get student data
$query = "SELECT * FROM mahasiswa WHERE id = $1";
$result = pg_query_params($conn, $query, array($student_id));
$student = pg_fetch_assoc($result);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = trim($_POST['nama']);
    $nim = trim($_POST['nim']);
    $jurusan = trim($_POST['jurusan']);
    $email = trim($_POST['email']);
    $alasan = trim($_POST['alasan']);
    $password_lama = trim($_POST['password_lama']);
    $password_baru = trim($_POST['password_baru']);
    $password_konfirmasi = trim($_POST['password_konfirmasi']);
    
    if (empty($nama) || empty($email) || empty($nim) || empty($jurusan)) {
        $error = 'Nama, NIM, Jurusan, dan Email harus diisi!';
    } else {
        // Check email uniqueness (exclude current student)
        $check_email = "SELECT id FROM mahasiswa WHERE email = $1 AND id != $2";
        $result_check = pg_query_params($conn, $check_email, array($email, $student_id));
        
        // Check NIM uniqueness
        $check_nim = "SELECT id FROM mahasiswa WHERE nim = $1 AND id != $2";
        $result_check_nim = pg_query_params($conn, $check_nim, array($nim, $student_id));
        
        if (pg_num_rows($result_check) > 0) {
            $error = 'Email sudah digunakan oleh mahasiswa lain!';
        } elseif (pg_num_rows($result_check_nim) > 0) {
            $error = 'NIM sudah digunakan oleh mahasiswa lain!';
        } else {
            
            // Ambil data user terkait dari tabel users (role=mahasiswa)
            $user_query = "SELECT id, password FROM users WHERE role = 'mahasiswa' AND reference_id = $1";
            $user_result = pg_query_params($conn, $user_query, array($student_id));
            $user = pg_fetch_assoc($user_result);
            $user_id = $user ? $user['id'] : null;
            
            // Handle password change (hanya di tabel users)
            $password_hash = $user ? $user['password'] : null;
            $password_changed = false;
            if (!empty($password_lama) && !empty($password_baru)) {
                if ($password_baru !== $password_konfirmasi) {
                    $error = 'Password baru dan konfirmasi password tidak sama!';
                } elseif (strlen($password_baru) < 6) {
                    $error = 'Password baru minimal 6 karakter!';
                } elseif (!$user || empty($user['password']) || !password_verify($password_lama, $user['password'])) {
                    $error = 'Password lama tidak sesuai!';
                } else {
                    $password_hash = password_hash($password_baru, PASSWORD_DEFAULT);
                    $password_changed = true;
                }
            }
            
            if (empty($error)) {
                // Update mahasiswa data
                $update_query = "UPDATE mahasiswa 
                                SET nama = $1, nim = $2, jurusan = $3, email = $4, alasan = $5
                                WHERE id = $6";
                $update_result = pg_query_params($conn, $update_query, 
                                                array($nama, $nim, $jurusan, $email, $alasan, $student_id));
                
                if ($update_result) {
                    // Update data user terkait di tabel users
                    if ($user_id) {
                        // Update email di users
                        $update_user_email = "UPDATE users SET email = $1, username = $2 WHERE id = $3";
                        // Note: Updating username might be risky if it conflicts, but let's try to keep it synced or just update email
                        // Let's just update email for now to be safe, or maybe username too if we want.
                        // For now, only email.
                        $update_user_email = "UPDATE users SET email = $1 WHERE id = $2";
                        pg_query_params($conn, $update_user_email, array($email, $user_id));
                        
                        // Update password jika diubah
                        if ($password_changed && $password_hash) {
                            $update_user_pwd = "UPDATE users SET password = $1 WHERE id = $2";
                            pg_query_params($conn, $update_user_pwd, array($password_hash, $user_id));
                        }
                    }
                    
                    // Update session data
                    $_SESSION['student_nama'] = $nama;
                    $_SESSION['student_email'] = $email;
                    $_SESSION['student_nim'] = $nim;
                    
                    $success = 'Profil berhasil diperbarui!';
                    
                    // Refresh student data
                    $result = pg_query_params($conn, $query, array($student_id));
                    $student = pg_fetch_assoc($result);
                } else {
                    $error = 'Gagal memperbarui profil. Silakan coba lagi.';
                }
            }
        }
    }
}

$page_title = 'Edit Profil';
include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="member-content">
    <div class="member-topbar">
        <div>
            <h4 class="mb-0">Edit Profil</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit Profil</li>
                </ol>
            </nav>
        </div>
        <div class="user-dropdown">
            <div class="text-end d-none d-md-block">
                <div class="fw-bold small"><?php echo htmlspecialchars($student['nama']); ?></div>
                <div class="text-muted small" style="font-size: 0.7rem;"><?php echo htmlspecialchars($student['nim']); ?></div>
            </div>
            <div class="user-avatar-placeholder">
                <i class="bi bi-person"></i>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Pribadi</h6>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo $error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>

                    <?php if ($success): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $success; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" name="nama" value="<?php echo htmlspecialchars($student['nama']); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">NIM</label>
                                <input type="text" class="form-control" name="nim" value="<?php echo htmlspecialchars($student['nim']); ?>" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jurusan</label>
                                <select class="form-select" name="jurusan" required>
                                    <option value="Teknik Informatika" <?php echo $student['jurusan'] == 'Teknik Informatika' ? 'selected' : ''; ?>>Teknik Informatika</option>
                                    <option value="Sistem Informasi" <?php echo $student['jurusan'] == 'Sistem Informasi' ? 'selected' : ''; ?>>Sistem Informasi</option>
                                    <option value="Teknik Komputer" <?php echo $student['jurusan'] == 'Teknik Komputer' ? 'selected' : ''; ?>>Teknik Komputer</option>
                                    <option value="Ilmu Komputer" <?php echo $student['jurusan'] == 'Ilmu Komputer' ? 'selected' : ''; ?>>Ilmu Komputer</option>
                                    <option value="Teknologi Informasi" <?php echo $student['jurusan'] == 'Teknologi Informasi' ? 'selected' : ''; ?>>Teknologi Informasi</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($student['email']); ?>" required>
                            </div>
                        </div>

                        <hr class="my-4">
                        <h6 class="mb-3 font-weight-bold text-primary">Ubah Password (Opsional)</h6>

                        <div class="mb-3">
                            <label class="form-label">Password Lama</label>
                            <input type="password" class="form-control" name="password_lama">
                            <small class="text-muted">Isi jika ingin mengubah password</small>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Password Baru</label>
                                <input type="password" class="form-control" name="password_baru">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Konfirmasi Password Baru</label>
                                <input type="password" class="form-control" name="password_konfirmasi">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
