<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2"><?= htmlspecialchars($title) ?></h1>
        <div>
            <a href="<?= BASE_URL ?>scripts/export.php" class="btn btn-sm btn-success"><i class="bi bi-file-earmark-excel"></i> Export ke CSV</a>
            <a href="<?= BASE_URL ?>scripts/export_pdf.php" class="btn btn-sm btn-danger"><i class="bi bi-file-earmark-pdf"></i> Export ke PDF</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <?php if (!empty($reports)): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Suhu Rata-rata (°C)</th>
                            <th>Kelembaban Rata-rata (%)</th>
                            <th>Durasi Pompa (menit)</th>
                            <th>Durasi Kipas (menit)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reports as $report): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars(date('d F Y', strtotime($report['tanggal']))) ?></strong></td>
                                <td class="text-center"><?= htmlspecialchars(number_format($report['suhu_rata'], 1)) ?></td>
                                <td class="text-center"><?= htmlspecialchars(number_format($report['kelembaban_rata'], 1)) ?></td>
                                <td class="text-center"><?= htmlspecialchars(round($report['pompa_durasi'])) ?></td>
                                <td class="text-center"><?= htmlspecialchars(round($report['kipas_durasi'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
                <div class="alert alert-info">Belum ada laporan harian yang tersedia.</div>
            <?php endif; ?>
        </div>
    </div>
</div>
