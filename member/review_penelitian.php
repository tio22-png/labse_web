<?php
require_once 'auth_check.php';
require_once '../includes/config.php';

$member_id = $_SESSION['member_id'];

// Get research submissions from supervised students
$query = "SELECT p.*, m.nama as nama_mahasiswa, m.nim, m.jurusan,
          (SELECT COUNT(*) FROM komentar_penelitian k WHERE k.penelitian_id = p.id) as total_komentar 
          FROM penelitian p 
          JOIN mahasiswa m ON p.mahasiswa_id = m.id 
          WHERE m.dosen_pembimbing_id = $1 
          ORDER BY p.created_at DESC";
$result = pg_query_params($conn, $query, array($member_id));

$page_title = 'Review Penelitian';
include 'includes/member_header.php';
include 'includes/member_sidebar.php';
?>

<div class="member-content">
    <div class="member-topbar">
        <div>
            <h4 class="mb-0">Review Penelitian Mahasiswa</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">Review Penelitian</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <?php if (pg_num_rows($result) > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th>Mahasiswa</th>
                            <th>Judul Penelitian</th>
                            <th>Tanggal Upload</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = pg_fetch_assoc($result)): ?>
                        <tr>
                            <td>
                                <div class="fw-bold"><?php echo htmlspecialchars($row['nama_mahasiswa']); ?></div>
                                <small class="text-muted"><?php echo htmlspecialchars($row['nim']); ?></small>
                            </td>
                            <td>
                                <div class="fw-bold"><?php echo htmlspecialchars($row['judul']); ?></div>
                                <?php if ($row['file_path']): ?>
                                    <span class="badge bg-light text-dark border me-1"><i class="bi bi-file-earmark-text"></i> File</span>
                                <?php endif; ?>
                                <?php if ($row['link_drive']): ?>
                                    <span class="badge bg-light text-dark border"><i class="bi bi-google"></i> Drive</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo date('d M Y H:i', strtotime($row['created_at'])); ?></td>
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
                            <td>
                                <a href="detail_penelitian_standalone.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary">
                                    <i class="bi bi-eye me-1"></i> Review
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="text-center py-5">
                <img src="../assets/img/empty.svg" alt="Empty" style="width: 150px; opacity: 0.5;" class="mb-3">
                <p class="text-muted">Belum ada penelitian yang diupload oleh mahasiswa bimbingan Anda.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/member_footer.php'; ?>
