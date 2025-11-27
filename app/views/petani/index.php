<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2"><?= htmlspecialchars($title) ?></h1>
        <a href="<?= BASE_URL ?>petani/create" class="btn btn-sm btn-primary"><i class="bi bi-plus-circle"></i> Tambah Petani Baru</a>
    </div>

    <div class="card">
        <div class="card-body">
            <?php if (!empty($petani)): ?>
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>No. HP</th>
                            <th>Alamat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($petani as $p): ?>
                            <tr>
                                <td><?= htmlspecialchars($p['user_id']) ?></td>
                                <td><?= htmlspecialchars($p['nama']) ?></td>
                                <td><?= htmlspecialchars($p['email']) ?></td>
                                <td><?= htmlspecialchars($p['no_hp'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($p['alamat'] ?? '-') ?></td>
                                <td>
                                    <a href="<?= BASE_URL ?>petani/detail/<?= $p['user_id'] ?>" class="btn btn-sm btn-info" title="Detail"><i class="bi bi-eye"></i></a>
                                    <a href="<?= BASE_URL ?>petani/edit/<?= $p['user_id'] ?>" class="btn btn-sm btn-warning" title="Edit"><i class="bi bi-pencil"></i></a>
                                    <a href="<?= BASE_URL ?>petani/delete/<?= $p['user_id'] ?>" class="btn btn-sm btn-danger" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus petani ini?')"><i class="bi bi-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
                <div class="alert alert-info">Belum ada data petani. Silakan tambahkan petani baru.</div>
            <?php endif; ?>
        </div>
    </div>
</div>
