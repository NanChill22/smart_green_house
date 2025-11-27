<div class="container-fluid">
    <a href="<?= BASE_URL ?>petani" class="btn btn-sm btn-outline-secondary mb-3"><i class="bi bi-arrow-left"></i> Kembali ke Daftar Petani</a>
    <h1 class="h2 mb-4"><?= htmlspecialchars($title) ?>: <?= htmlspecialchars($petani['nama']) ?></h1>

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <table class="table table-borderless">
                        <tr>
                            <th style="width: 150px;">ID Pengguna</th>
                            <td>: <?= htmlspecialchars($petani['user_id']) ?></td>
                        </tr>
                        <tr>
                            <th>Nama</th>
                            <td>: <?= htmlspecialchars($petani['nama']) ?></td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>: <?= htmlspecialchars($petani['email']) ?></td>
                        </tr>
                        <tr>
                            <th>No. HP</th>
                            <td>: <?= htmlspecialchars($petani['no_hp'] ?? '-') ?></td>
                        </tr>
                        <tr>
                            <th>Alamat</th>
                            <td>: <?= nl2br(htmlspecialchars($petani['alamat'] ?? '-')) ?></td>
                        </tr>
                        <tr>
                            <th>Role</th>
                            <td>: <span class="badge bg-info"><?= htmlspecialchars($petani['role']) ?></span></td>
                        </tr>
                        <tr>
                            <th>Terdaftar pada</th>
                            <td>: <?= htmlspecialchars(date('d F Y H:i', strtotime($petani['created_at']))) ?></td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <hr>
            <a href="<?= BASE_URL ?>petani/edit/<?= $petani['user_id'] ?>" class="btn btn-warning"><i class="bi bi-pencil"></i> Edit Data</a>
            <a href="<?= BASE_URL ?>petani/delete/<?= $petani['user_id'] ?>" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus petani ini?')"><i class="bi bi-trash"></i> Hapus Petani</a>

        </div>
    </div>
</div>
