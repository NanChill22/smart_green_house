<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Kontrol Smart Green House</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body { background: #f4f6f9; }
        .card { border-radius: 12px; }
        .mode-btn.active { background: #0d6efd; color: #fff; }
        .switch-btn.active { background: #198754; color: #fff; }
    </style>
</head>

<body>

<div class="container py-4">
    <h2 class="mb-4 text-center">Control Panel – Smart Green House</h2>

    <div class="row g-4">

        <!-- MODE SYSTEM -->
        <div class="col-md-4">
            <div class="card p-3 shadow">
                <h5 class="mb-3">Mode Sistem</h5>

                <button id="btnAuto" class="btn mode-btn w-100 mb-2 active">AUTO</button>
                <!-- <button id="btnManual" class="btn mode-btn w-100">MANUAL</button> -->
            </div>
        </div>

        <!-- BATAS SUHU -->
        <div class="col-md-4">
            <div class="card p-3 shadow">
                <h5>Batas Suhu (°C)</h5>
                <span id="suhuValue" class="fw-bold">-°C</span>
                <input type="range" class="form-range mt-2" id="batasSuhu" min="20" max="50" value="30">
            </div>
        </div>

        <!-- BATAS SOIL -->
        <div class="col-md-4">
            <div class="card p-3 shadow">
                <h5>Batas Soil (%)</h5>
                <span id="soilValue" class="fw-bold">-%</span>
                <input type="range" class="form-range mt-2" id="batasSoil" min="0" max="100" value="40">
            </div>
        </div>

    </div>

    <!-- <div class="row mt-4 g-4">

        <div class="col-md-6">
            <div class="card p-3 shadow text-center">
                <h5 class="mb-3">Kipas</h5>
                <button id="btnKipas" class="btn switch-btn w-100">OFF</button>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card p-3 shadow text-center">
                <h5 class="mb-3">Pompa</h5>
                <button id="btnPompa" class="btn switch-btn w-100">OFF</button>
            </div>
        </div>

    </div> -->
</div>


<script>
// ===================== LOAD STATUS DARI SERVER =========================
function loadStatus() {
    fetch("/smart_green_house/sensor/getControlStatus")
        .then(res => res.json())
        .then(data => {

            // MODE
            // if (data.mode === "auto") {
            //     document.getElementById("btnAuto").classList.add("active");
            //     document.getElementById("btnManual").classList.remove("active");
            // } else {
            //     document.getElementById("btnManual").classList.add("active");
            //     document.getElementById("btnAuto").classList.remove("active");
            // }

            // BATAS SUHU / SOIL
            document.getElementById("batasSuhu").value = data.batas_suhu;
            document.getElementById("batasSoil").value = data.batas_soil;
            document.getElementById("suhuValue").innerText = data.batas_suhu + "°C";
            document.getElementById("soilValue").innerText = data.batas_soil + "%";

            // KIPAS
            let btnKipas = document.getElementById("btnKipas");
            if (data.kipas == 1) {
                btnKipas.classList.add("active");
                btnKipas.innerText = "KIPAS ON";
            } else {
                btnKipas.classList.remove("active");
                btnKipas.innerText = "KIPAS OFF";
            }

            // POMPA
            let btnPompa = document.getElementById("btnPompa");
            if (data.pompa == 1) {
                btnPompa.classList.add("active");
                btnPompa.innerText = "POMPA ON";
            } else {
                btnPompa.classList.remove("active");
                btnPompa.innerText = "POMPA OFF";
            }
        });
}

// ===================== SEND UPDATE KE SERVER =========================
function sendUpdate() {
    let formData = new FormData();
    // formData.append("mode", document.getElementById("btnAuto").classList.contains("active") ? "auto" : "manual");
    formData.append("kipas", document.getElementById("btnKipas").classList.contains("active") ? 1 : 0);
    formData.append("pompa", document.getElementById("btnPompa").classList.contains("active") ? 1 : 0);
    formData.append("batas_suhu", document.getElementById("batasSuhu").value);
    formData.append("batas_soil", document.getElementById("batasSoil").value);

    fetch("/smart_green_house/sensor/setControlStatus", { method: "POST", body: formData });
}


// ===================== EVENT HANDLER =========================

// Mode Auto / Manual
// document.getElementById("btnAuto").onclick = function() {
//     this.classList.add("active");
//     document.getElementById("btnManual").classList.remove("active");
//     sendUpdate();
// };

// document.getElementById("btnManual").onclick = function() {
//     this.classList.add("active");
//     document.getElementById("btnAuto").classList.remove("active");
//     sendUpdate();
// };

// Slider batas suhu
document.getElementById("batasSuhu").oninput = function() {
    document.getElementById("suhuValue").innerText = this.value + "°C";
    sendUpdate();
};

// Slider batas soil
document.getElementById("batasSoil").oninput = function() {
    document.getElementById("soilValue").innerText = this.value + "%";
    sendUpdate();
};

// Tombol Kipas
document.getElementById("btnKipas").onclick = function() {
    this.classList.toggle("active");
    this.innerText = this.classList.contains("active") ? "KIPAS ON" : "KIPAS OFF";
    sendUpdate();
};

// Tombol Pompa
document.getElementById("btnPompa").onclick = function() {
    this.classList.toggle("active");
    this.innerText = this.classList.contains("active") ? "POMPA ON" : "POMPA OFF";
    sendUpdate();
};

// Load awal
loadStatus();

// Refresh otomatis tiap 2 detik
setInterval(loadStatus, 2000);

</script>

</body>
</html>
