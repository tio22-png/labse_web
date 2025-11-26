<?php
// Disable OPcache for this specific file
if (function_exists('opcache_invalidate')) {
    opcache_invalidate(__FILE__, true);
}

require_once 'auth_check.php';
require_once '../includes/config.php';

if (!isset($_GET['id'])) {
    header('Location: review_penelitian.php');
    exit();
}

$penelitian_id = intval($_GET['id']);
$member_id = $_SESSION['member_id'];

// Handle status update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $new_status = trim($_POST['status']);
    $query_update = "UPDATE penelitian SET status = $1 WHERE id = $2";
    $result_update = pg_query_params($conn, $query_update, array($new_status, $penelitian_id));
    
    if ($result_update) {
        $success = "Status berhasil diperbarui";
    }
}

// Get research details
$query = "SELECT p.*, m.nama as nama_mahasiswa, m.nim, m.jurusan, m.email
          FROM penelitian p 
          JOIN mahasiswa m ON p.mahasiswa_id = m.id 
          WHERE p.id = $1 AND m.dosen_pembimbing_id = $2";
$result = pg_query_params($conn, $query, array($penelitian_id, $member_id));
$penelitian = pg_fetch_assoc($result);

if (!$penelitian) {
    header('Location: review_penelitian.php');
    exit();
}

$page_title = 'Detail Penelitian';
include 'includes/member_header.php';
include 'includes/member_sidebar.php';
?>

<div class="member-content">
    <div class="member-topbar">
        <div>
            <h4 class="mb-0">Detail Penelitian</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="review_penelitian.php">Review Penelitian</a></li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </nav>
        </div>
    </div>

    <?php if (isset($success)): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?php echo $success; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h5 class="card-title mb-4"><?php echo htmlspecialchars($penelitian['judul']); ?></h5>
                    
                    <div class="mb-4">
                        <label class="text-muted small">Mahasiswa</label>
                        <div class="fw-bold"><?php echo htmlspecialchars($penelitian['nama_mahasiswa']); ?> (<?php echo htmlspecialchars($penelitian['nim']); ?>)</div>
                        <div><?php echo htmlspecialchars($penelitian['jurusan']); ?></div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="text-muted small">Keterangan</label>
                        <p><?php echo nl2br(htmlspecialchars($penelitian['keterangan'] ?? '-')); ?></p>
                    </div>
                    
                    <div class="mb-4">
                        <label class="text-muted small d-block mb-2">File / Link</label>
                        <div class="d-flex gap-2">
                            <?php if (!empty($penelitian['file_path'])): ?>
                                <a href="../uploads/penelitian/<?php echo htmlspecialchars($penelitian['file_path']); ?>" target="_blank" class="btn btn-outline-primary">
                                    <i class="bi bi-file-earmark-text me-2"></i>Download File
                                </a>
                            <?php endif; ?>
                            
                            <?php if (!empty($penelitian['link_drive'])): ?>
                                <a href="<?php echo htmlspecialchars($penelitian['link_drive']); ?>" target="_blank" class="btn btn-outline-success">
                                    <i class="bi bi-google me-2"></i>Buka Link Drive
                                </a>
                            <?php endif; ?>
                            
                            <?php if (empty($penelitian['file_path']) && empty($penelitian['link_drive'])): ?>
                                <span class="text-muted fst-italic">Tidak ada file atau link</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <h6 class="mb-3">Diskusi & Komentar</h6>
                    <div id="commentsList" class="bg-light p-3 rounded mb-3" style="max-height: 400px; overflow-y: auto;">
                        <div class="text-center"><div class="spinner-border text-primary spinner-border-sm"></div></div>
                    </div>
                    
                    <form id="commentForm" onsubmit="submitComment(event)">
                        <input type="hidden" id="penelitian_id" value="<?php echo $penelitian_id; ?>">
                        <div class="input-group">
                            <input type="text" class="form-control" id="commentInput" placeholder="Tulis komentar..." required>
                            <button class="btn btn-primary" type="submit">
                                <i class="bi bi-send"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0">Status Penelitian</h6>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="update_status" value="1">
                        <div class="mb-3">
                            <label class="form-label">Status Saat Ini</label>
                            <select class="form-select" name="status">
                                <option value="submitted" <?php echo $penelitian['status'] == 'submitted' ? 'selected' : ''; ?>>Submitted</option>
                                <option value="reviewed" <?php echo $penelitian['status'] == 'reviewed' ? 'selected' : ''; ?>>Reviewed</option>
                                <option value="approved" <?php echo $penelitian['status'] == 'approved' ? 'selected' : ''; ?>>Approved</option>
                                <option value="rejected" <?php echo $penelitian['status'] == 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Update Status</button>
                    </form>
                </div>
            </div>
            
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0">Info Mahasiswa</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="bg-secondary rounded-circle d-inline-flex align-items-center justify-content-center text-white" style="width: 60px; height: 60px;">
                            <i class="bi bi-person-fill h3 mb-0"></i>
                        </div>
                    </div>
                    <h6 class="text-center mb-1"><?php echo htmlspecialchars($penelitian['nama_mahasiswa']); ?></h6>
                    <p class="text-center text-muted small mb-3"><?php echo htmlspecialchars($penelitian['email']); ?></p>
                    
                    <div class="d-grid">
                        <a href="mailto:<?php echo htmlspecialchars($penelitian['email']); ?>" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-envelope me-2"></i>Kirim Email
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    loadComments();
});

function loadComments() {
    var id = document.getElementById('penelitian_id').value;
    var commentsList = document.getElementById('commentsList');
    
    fetch('../student/get_comments.php?id=' + id)
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
    
    fetch('../student/add_comment.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('commentInput').value = '';
            loadComments();
        } else {
            alert('Gagal mengirim komentar');
        }
    });
}
</script>

<?php include 'includes/member_footer.php'; ?>
