<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2"><?= htmlspecialchars($title) ?></h1>
        <a href="<?= BASE_URL ?>user/create" class="btn btn-sm btn-primary"><i class="bi bi-plus-circle"></i> Tambah Pengguna Baru</a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Terdaftar Sejak</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= htmlspecialchars($user['id']) ?></td>
                                <td><?= htmlspecialchars($user['nama']) ?></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td>
                                    <span class="badge <?= $user['role'] === 'admin' ? 'bg-success' : 'bg-secondary' ?>">
                                        <?= htmlspecialchars($user['role']) ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars(date('d F Y', strtotime($user['created_at']))) ?></td>
                                <td>
                                    <a href="<?= BASE_URL ?>user/edit/<?= $user['id'] ?>" class="btn btn-sm btn-warning" title="Edit"><i class="bi bi-pencil"></i></a>
                                    <?php if ($user['id'] != $_SESSION['user_id']): // Jangan tampilkan tombol hapus untuk diri sendiri ?>
                                        <a href="<?= BASE_URL ?>user/delete/<?= $user['id'] ?>" class="btn btn-sm btn-danger" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?')"><i class="bi bi-trash"></i></a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
