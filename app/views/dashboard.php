<div class="container-fluid">
    <h3 class="mb-4">Halo, <?= htmlspecialchars($_SESSION['user_nama']) ?>!</h3>
    <h1 class="h2 mb-4"><?= htmlspecialchars($title) ?></h1>

    <!-- Status Saat Ini -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Status Saat Ini</h5>
        </div>
        <div class="card-body">
            <div class="row" id="sensor-data-container">
                <?php if ($latestSensorData): ?>
                    <div class="col-6 col-sm-6 col-md-2">
                        <div class="stat-card text-center p-3 border rounded mb-2">
                            <h4><i class="bi bi-thermometer-half text-danger"></i> Suhu</h4>
                            <p class="fs-3" id="suhu"><?= htmlspecialchars(number_format($latestSensorData['suhu'], 1)) ?> °C</p>
                        </div>
                    </div>
                    <div class="col-6 col-sm-6 col-md-2">
                        <div class="stat-card text-center p-3 border rounded mb-2">
                            <h4><i class="bi bi-moisture text-primary"></i> Kelembaban Udara</h4>
                            <p class="fs-3" id="kelembaban"><?= htmlspecialchars(number_format($latestSensorData['kelembaban'], 1)) ?> %</p>
                        </div>
                    </div>
                    <div class="col-6 col-sm-6 col-md-2">
                        <div class="stat-card text-center p-3 border rounded mb-2">
                            <h4><i class="bi bi-water text-success"></i> Kelembaban Tanah</h4>
                            <p class="fs-3" id="kelembaban-tanah"><?= htmlspecialchars(number_format($latestSensorData['kelembaban_tanah'], 1)) ?> %</p>
                        </div>
                    </div>
                    <div class="col-6 col-sm-6 col-md-2">
                        <div class="stat-card text-center p-3 border rounded mb-2">
                            <h4><i class="bi bi-fan" id="kipas-icon"></i> Kipas</h4>
                            <p class="fs-3" id="kipas-status"><?= isset($latestData['kipas_status']) ? ($latestData['kipas_status'] ? 'ON' : 'OFF') : 'OFF' ?></p>
                        </div>
                    </div>
                    <div class="col-6 col-sm-6 col-md-2">
                        <div class="stat-card text-center p-3 border rounded mb-2">
                           <h4><i class="bi bi-water" id="pompa-icon"></i> Pompa</h4>
                            <p class="fs-3" id="pompa-status"><?= isset($latestData['pompa_status']) ? ($latestData['pompa_status'] ? 'ON' : 'OFF') : 'OFF' ?></p>
                        </div>
                    </div>
                     <!-- <div class="col-6 col-sm-6 col-md-2">
                        <div class="stat-card text-center p-3 border rounded mb-2">
                           <h4><i class="bi bi-gear" id="mode-icon"></i> Mode</h4>
                            <p class="fs-3 text-capitalize" id="mode-status"><?= isset($mode) ? ( htmlspecialchars($mode) ? 'OTOMATIS' : 'MANUAL') : 'MANUAL' ?></p>
                        </div>
                    </div> -->
                <?php else: ?>
                    <div class="col" id="no-sensor-data">
                        <div class="alert alert-info">Belum ada data sensor.</div>
                    </div>
                <?php endif; ?>
            </div>
             <?php if ($latestSensorData): ?>
                 <small class="text-muted" id="last-updated">Data terakhir pada: <?= htmlspecialchars(date('d F Y H:i:s', strtotime($latestSensorData['created_at']))) ?></small>
            <?php else: ?>
                 <small class="text-muted" id="last-updated"></small>
            <?php endif; ?>
        </div>
    </div>

    <!-- Grafik Data Historis -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Grafik Data Historis (7 Hari Terakhir)</h5>
        </div>
        <div class="card-body">
            <div style="height: 400px;"><canvas id="historicalChart"></canvas></div>
        </div>
    </div>

    <script>
        // Data historis dari PHP untuk Chart.js
        const historicalData = <?= json_encode($historicalData); ?>;
        document.addEventListener('DOMContentLoaded', function () {
            const baseUrl = '<?= BASE_URL ?>';
            const sensorDataContainer = document.getElementById('sensor-data-container');
            const noSensorDataElement = document.getElementById('no-sensor-data');
            const lastUpdatedElement = document.getElementById('last-updated');

            let suhuElement = document.getElementById('suhu');
            let kelembabanElement = document.getElementById('kelembaban');
            let kelembabanTanahElement = document.getElementById('kelembaban-tanah'); // New element
            let kipasIcon = document.getElementById('kipas-icon');
            let kipasStatus = document.getElementById('kipas-status');
            let pompaIcon = document.getElementById('pompa-icon');
            let pompaStatus = document.getElementById('pompa-status');
            let modeIcon = document.getElementById('mode-icon');
            let modeStatus = document.getElementById('mode-status');

            function updateSensorData() {
                $.ajax({
                    url: baseUrl + 'sensor/getLatest',
                    method: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        if (data.success && data.data) {
                            const sd = data.data; // sensorData
                            
                            if (noSensorDataElement && sensorDataContainer.querySelector('#no-sensor-data-permanent')) { // If "No data" message is currently displayed
                                sensorDataContainer.innerHTML = `
                                    <div class="col-6 col-sm-6 col-md-2">
                                        <div class="stat-card text-center p-3 border rounded mb-2">
                                            <h4><i class="bi bi-thermometer-half text-danger"></i> Suhu</h4>
                                            <p class="fs-3" id="suhu">${parseFloat(sd.suhu).toFixed(1)} °C</p>
                                        </div>
                                    </div>
                                    <div class="col-6 col-sm-6 col-md-2">
                                        <div class="stat-card text-center p-3 border rounded mb-2">
                                            <h4><i class="bi bi-moisture text-primary"></i> Kelembaban Udara</h4>
                                            <p class="fs-3" id="kelembaban">${parseFloat(sd.kelembaban).toFixed(1)} %</p>
                                        </div>
                                    </div>
                                    <div class="col-6 col-sm-6 col-md-2">
                                        <div class="stat-card text-center p-3 border rounded mb-2">
                                            <h4><i class="bi bi-water text-success"></i> Kelembaban Tanah</h4>
                                            <p class="fs-3" id="kelembaban-tanah">${parseFloat(sd.kelembaban_tanah).toFixed(1)} %</p>
                                        </div>
                                    </div>
                                    <div class="col-6 col-sm-6 col-md-2">
                                        <div class="stat-card text-center p-3 border rounded mb-2">
                                            <h4><i class="bi bi-fan" id="kipas-icon"></i> Kipas</h4>
                                            <p class="fs-3" id="kipas-status">${sd.kipas_status ? 'ON' : 'OFF'}</p>
                                        </div>
                                    </div>
                                    <div class="col-6 col-sm-6 col-md-2">
                                        <div class="stat-card text-center p-3 border rounded mb-2">
                                           <h4><i class="bi bi-water" id="pompa-icon"></i> Pompa</h4>
                                            <p class="fs-3" id="pompa-status">${sd.pompa_status ? 'ON' : 'OFF'}</p>
                                        </div>
                                    </div>
                                     <div class="col-6 col-sm-6 col-md-2">
                                        <div class="stat-card text-center p-3 border rounded mb-2">
                                           <h4><i class="bi bi-gear" id="mode-icon"></i> Mode</h4>
                                            <p class="fs-3 text-capitalize" id="mode-status">${sd.mode.charAt(0).toUpperCase() + sd.mode.slice(1)}</p>
                                        </div>
                                    </div>
                                `;
                                // Re-get references to new elements if they were just injected
                                    suhuElement = document.getElementById('suhu');
                                    kelembabanElement = document.getElementById('kelembaban');
                                    kelembabanTanahElement = document.getElementById('kelembaban-tanah');
                                    kipasIcon = document.getElementById('kipas-icon');
                                    kipasStatus = document.getElementById('kipas-status');
                                    pompaIcon = document.getElementById('pompa-icon');
                                    pompaStatus = document.getElementById('pompa-status');
                                    modeIcon = document.getElementById('mode-icon');
                                    modeStatus = document.getElementById('mode-status');
                            } else {
                                if (suhuElement) suhuElement.textContent = parseFloat(sd.suhu).toFixed(1) + ' °C';
                                if (kelembabanElement) kelembabanElement.textContent = parseFloat(sd.kelembaban).toFixed(1) + ' %';
                                if (kelembabanTanahElement) kelembabanTanahElement.textContent = parseFloat(sd.kelembaban_tanah).toFixed(1) + ' %'; // New update

                                if (kipasIcon) {
                                    kipasIcon.classList.remove('text-success', 'text-muted');
                                    kipasIcon.classList.add(sd.kipas_status ? 'text-success' : 'text-muted');
                                }
                                if (kipasStatus) kipasStatus.textContent = sd.kipas_status ? 'ON' : 'OFF';

                                if (pompaIcon) {
                                    pompaIcon.classList.remove('text-success', 'text-muted');
                                    pompaIcon.classList.add(sd.pompa_status ? 'text-success' : 'text-muted');
                                }
                                if (pompaStatus) pompaStatus.textContent = sd.pompa_status ? 'ON' : 'OFF';

                                if (modeIcon) {
                                    modeIcon.classList.remove('text-success', 'text-warning');
                                    modeIcon.classList.add(sd.mode === 'otomatis' ? 'text-success' : 'text-warning');
                                }
                                if (modeStatus) modeStatus.textContent = sd.mode.charAt(0).toUpperCase() + sd.mode.slice(1);
                            }
                            
                            if (lastUpdatedElement) {
                                const date = new Date(sd.created_at);
                                lastUpdatedElement.textContent = `Data terakhir pada: ${date.toLocaleString('id-ID', {day: '2-digit', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit'})}`;
                            }
                        }
                        else {
                            if (sensorDataContainer && !document.getElementById('no-sensor-data-permanent')) {
                                sensorDataContainer.innerHTML = `<div class="col" id="no-sensor-data-permanent"><div class="alert alert-info">Belum ada data sensor.</div></div>`;
                                if (lastUpdatedElement) lastUpdatedElement.textContent = '';
                            }
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error('Error fetching sensor data:', textStatus, errorThrown);
                    }
                });
            }

            // Update every 5 seconds
            setInterval(updateSensorData, 5000);

            // Initial call
            updateSensorData();
        });
    </script>

