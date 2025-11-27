<div class="container-fluid">
    <h1 class="h2 mb-4"><?= htmlspecialchars($title) ?></h1>

    <div id="alert-container">
    </div>

    <!-- Form Kontrol -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Kontrol Manual</h5>
        </div>
        <div class="card-body">
            <p>Gunakan tombol di bawah untuk menyalakan atau mematikan kipas dan pompa secara manual. Status saat ini dapat dilihat di samping.</p>
            <form id="control-form" action="#">
                <div class="row align-items-center">
                    <!-- Kontrol Pompa -->
                    <div class="col-md-6">
                        <div class="d-flex justify-content-between align-items-center border p-3 rounded">
                            <div>
                                <h6 class="mb-0">Pompa Air</h6>
                                <small class="text-muted">Mengatur irigasi tanaman</small>
                            </div>
                            <div class="form-check form-switch fs-4">
                                <input class="form-check-input" type="checkbox" role="switch" id="pompaSwitch" name="pompa" 
                                    <?= ($latestSensorData['pompa_status'] ?? 0) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="pompaSwitch"></label>
                            </div>
                        </div>
                    </div>
                    <!-- Kontrol Kipas -->
                    <div class="col-md-6">
                        <div class="d-flex justify-content-between align-items-center border p-3 rounded">
                            <div>
                                <h6 class="mb-0">Kipas Pendingin</h6>
                                <small class="text-muted">Mengatur sirkulasi udara</small>
                            </div>
                             <div class="form-check form-switch fs-4">
                                <input class="form-check-input" type="checkbox" role="switch" id="kipasSwitch" name="kipas" 
                                    <?= ($latestSensorData['kipas_status'] ?? 0) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="kipasSwitch"></label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="badge bg-warning text-dark">Mode Manual</span>
                    <small class="text-muted">Saat kontrol manual digunakan, sistem akan beralih ke mode "manual". Perangkat tidak akan merespons sensor hingga kembali ke mode "otomatis".</small>
                </div>
            </form>
        </div>
    </div>

    <!-- Riwayat Kontrol -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Riwayat Kontrol Manual (10 Terakhir)</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-striped">
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>Pengguna</th>
                            <th>Aksi</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody id="control-history-body">
                        <?php if (!empty($history)): ?>
                            <?php foreach ($history as $log): ?>
                            <tr>
                                <td><?= htmlspecialchars(date('d/m/y H:i:s', strtotime($log['created_at']))) ?></td>
                                <td><?= htmlspecialchars($log['nama'] ?? 'Sistem') ?></td>
                                <td>
                                    <span class="badge 
                                        <?= str_contains($log['aksi'], '_on') ? 'bg-success' : 'bg-danger' ?>">
                                        <?= htmlspecialchars($log['aksi']) ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($log['keterangan']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="text-center">Belum ada riwayat kontrol manual.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const baseUrl = '<?= BASE_URL ?>';
            const pompaSwitch = document.getElementById('pompaSwitch');
            const kipasSwitch = document.getElementById('kipasSwitch');
            const alertContainer = document.getElementById('alert-container');
            const controlHistoryBody = document.getElementById('control-history-body');

            // Function to display alerts
            function showAlert(message, type) {
                const alertHtml = `
                    <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                        ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;
                alertContainer.innerHTML = alertHtml;
                // Auto-close alert after 5 seconds
                setTimeout(() => {
                    const alertElement = alertContainer.querySelector('.alert');
                    if (alertElement) {
                        new bootstrap.Alert(alertElement).close();
                    }
                }, 5000);
            }

            // Function to fetch and render control history
            function fetchControlHistory() {
                fetch(baseUrl + 'sensor/getControlHistory')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.data) {
                            let historyHtml = '';
                            if (data.data.length > 0) {
                                data.data.forEach(log => {
                                    const actionType = log.aksi.includes('_on') ? 'bg-success' : 'bg-danger';
                                    const logDate = new Date(log.created_at);
                                    historyHtml += `
                                        <tr>
                                            <td>${logDate.toLocaleString('id-ID', {day: '2-digit', month: '2-digit', year: '2-digit', hour: '2-digit', minute: '2-digit', second: '2-digit'})}</td>
                                            <td>${log.nama || 'Sistem'}</td>
                                            <td><span class="badge ${actionType}">${log.aksi}</span></td>
                                            <td>${log.keterangan}</td>
                                        </tr>
                                    `;
                                });
                            } else {
                                historyHtml = `<tr><td colspan="4" class="text-center">Belum ada riwayat kontrol manual.</td></tr>`;
                            }
                            controlHistoryBody.innerHTML = historyHtml;
                        } else {
                            controlHistoryBody.innerHTML = `<tr><td colspan="4" class="text-center">Belum ada riwayat kontrol manual.</td></tr>`;
                        }
                    })
                    .catch(error => console.error('Error fetching control history:', error));
            }

            // Function to send control command
            async function sendControlCommand(device, status) {
                const formData = new FormData();
                formData.append('device', device);
                formData.append('status', status ? 1 : 0);

                try {
                    const response = await fetch(baseUrl + 'sensor/updateControl', {
                        method: 'POST',
                        body: formData
                    });
                    const data = await response.json();

                    if (data.success) {
                        showAlert(data.message, 'success');
                        fetchControlHistory(); // Refresh history after successful command
                    } else {
                        showAlert(data.message, 'danger');
                    }
                } catch (error) {
                    console.error('Error sending control command:', error);
                    showAlert('Terjadi kesalahan saat mengirim perintah kontrol.', 'danger');
                }
            }

            // Event listener for Pompa switch
            pompaSwitch.addEventListener('change', function() {
                sendControlCommand('pompa', this.checked);
            });

            // Event listener for Kipas switch
            kipasSwitch.addEventListener('change', function() {
                sendControlCommand('kipas', this.checked);
            });

            // Initial fetch of control history
            fetchControlHistory();
        });
    </script>
