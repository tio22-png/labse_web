<?php
require_once 'auth_check.php';
require_once '../core/database.php';

$student_id = $_SESSION['student_id'];
$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul = trim($_POST['judul']);
    $keterangan = trim($_POST['keterangan']);
    $link_drive = trim($_POST['link_drive']);
    
    if (empty($judul)) {
        $error = 'Judul penelitian harus diisi!';
    } else {
        $file_path = null;
        
        // Handle file upload
        if (isset($_FILES['file_penelitian']) && $_FILES['file_penelitian']['error'] == 0) {
            $allowed = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
            $filename = $_FILES['file_penelitian']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if (in_array($ext, $allowed)) {
                $new_filename = 'penelitian_' . $student_id . '_' . time() . '.' . $ext;
                $upload_path = '../uploads/penelitian/' . $new_filename;
                
                if (!file_exists('../uploads/penelitian/')) {
                    mkdir('../uploads/penelitian/', 0777, true);
                }
                
                if (move_uploaded_file($_FILES['file_penelitian']['tmp_name'], $upload_path)) {
                    $file_path = $new_filename;
                } else {
                    $error = 'Gagal mengupload file!';
                }
            } else {
                $error = 'Format file tidak diizinkan! (Hanya PDF, Word, JPG, PNG)';
            }
        }
        
        if (empty($error)) {
            if (empty($file_path) && empty($link_drive)) {
                $error = 'Harap upload file ATAU masukkan link Google Drive!';
            } else {
                $query = "INSERT INTO penelitian (mahasiswa_id, judul, file_path, link_drive, keterangan, status, created_at) 
                          VALUES ($1, $2, $3, $4, $5, 'submitted', NOW())";
                $result = pg_query_params($conn, $query, array($student_id, $judul, $file_path, $link_drive, $keterangan));
                
                if ($result) {
                    $success = 'Hasil penelitian berhasil diupload!';
                } else {
                    $error = 'Gagal menyimpan data: ' . pg_last_error($conn);
                }
            }
        }
    }
}

// Get uploaded research
$query_list = "SELECT p.*, 
               (SELECT COUNT(*) FROM komentar_penelitian k WHERE k.penelitian_id = p.id) as total_komentar 
               FROM penelitian p 
               WHERE mahasiswa_id = $1 
               ORDER BY created_at DESC";
$result_list = pg_query_params($conn, $query_list, array($student_id));

$page_title = 'Hasil Penelitian';
include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="member-content">
    <div class="member-topbar">
        <div>
            <h4 class="mb-0">Hasil Penelitian</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Hasil Penelitian</li>
                </ol>
            </nav>
        </div>
        <div class="user-dropdown">
            <div class="text-end d-none d-md-block">
                <div class="fw-bold small"><?php echo htmlspecialchars($_SESSION['student_nama']); ?></div>
                <div class="text-muted small" style="font-size: 0.7rem;"><?php echo htmlspecialchars($_SESSION['student_nim'] ?? ''); ?></div>
            </div>
            <div class="user-avatar-placeholder">
                <i class="bi bi-person"></i>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Upload Form -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow border-0">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-primary">Upload Penelitian Baru</h6>
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

                    <form method="POST" action="" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">Judul Penelitian <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="judul" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">File (PDF/Word/Gambar)</label>
                            <input type="file" class="form-control" name="file_penelitian" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                            <small class="text-muted">Opsional jika menggunakan link</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Link Google Drive</label>
                            <input type="url" class="form-control" name="link_drive" placeholder="https://drive.google.com/...">
                            <small class="text-muted">Opsional jika sudah upload file</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Keterangan / Catatan</label>
                            <textarea class="form-control" name="keterangan" rows="3"></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-upload me-2"></i>Upload
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- List of Uploads -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Riwayat Upload</h6>
                </div>
                <div class="card-body">
                    <?php if (pg_num_rows($result_list) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Judul</th>
                                    <th>File/Link</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                    <th>Komentar</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = pg_fetch_assoc($result_list)): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($row['judul']); ?></strong>
                                        <?php if ($row['keterangan']): ?>
                                            <br><small class="text-muted"><?php echo htmlspecialchars($row['keterangan']); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($row['file_path']): ?>
                                            <a href="../uploads/penelitian/<?php echo htmlspecialchars($row['file_path']); ?>" target="_blank" class="badge bg-info text-decoration-none">
                                                <i class="bi bi-file-earmark-text me-1"></i>File
                                            </a>
                                        <?php endif; ?>
                                        <?php if ($row['link_drive']): ?>
                                            <a href="<?php echo htmlspecialchars($row['link_drive']); ?>" target="_blank" class="badge bg-warning text-dark text-decoration-none">
                                                <i class="bi bi-google me-1"></i>Drive
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php 
                                        $status_class = 'bg-secondary';
                                        if ($row['status'] == 'submitted') $status_class = 'bg-primary';
                                        elseif ($row['status'] == 'reviewed') $status_class = 'bg-info';
                                        elseif ($row['status'] == 'approved') $status_class = 'bg-success';
                                        elseif ($row['status'] == 'rejected') $status_class = 'bg-danger';
                                        ?>
                                        <span class="badge <?php echo $status_class; ?>"><?php echo ucfirst($row['status']); ?></span>
                                    </td>
                                    <td><?php echo date('d M Y H:i', strtotime($row['created_at'])); ?></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="showComments(<?php echo $row['id']; ?>)">
                                            <i class="bi bi-chat-dots me-1"></i>
                                            <?php echo $row['total_komentar']; ?>
                                        </button>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <p class="text-center text-muted my-4">Belum ada hasil penelitian yang diupload.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Comments Modal -->
<div class="modal fade" id="commentsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Komentar & Diskusi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="commentsList" class="mb-3" style="max-height: 400px; overflow-y: auto;">
                    <!-- Comments will be loaded here via AJAX -->
                    <div class="text-center"><div class="spinner-border text-primary"></div></div>
                </div>
                <form id="commentForm" onsubmit="submitComment(event)">
                    <input type="hidden" id="penelitian_id" name="penelitian_id">
                    <div class="input-group">
                        <input type="text" class="form-control" id="commentInput" placeholder="Tulis komentar..." required>
                        <button class="btn btn-primary" type="submit">Kirim</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function showComments(id) {
    document.getElementById('penelitian_id').value = id;
    var modal = new bootstrap.Modal(document.getElementById('commentsModal'));
    modal.show();
    loadComments(id);
}

function loadComments(id) {
    var commentsList = document.getElementById('commentsList');
    commentsList.innerHTML = '<div class="text-center"><div class="spinner-border text-primary"></div></div>';
    
    fetch('get_comments.php?id=' + id)
        .then(response => response.text())
        .then(html => {
            commentsList.innerHTML = html;
        });
}

function submitComment(e) {
    e.preventDefault();
    var id = document.getElementById('penelitian_id').value;
    var isi = document.getElementById('commentInput').value;
    
    var formData = new FormData();
    formData.append('penelitian_id', id);
    formData.append('isi', isi);
    
    fetch('add_comment.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('commentInput').value = '';
            loadComments(id);
        } else {
            alert('Gagal mengirim komentar');
        }
    });
}
</script>

<?php include 'includes/footer.php'; ?>
