document.addEventListener('DOMContentLoaded', function () {
    // Check if historicalData is available (passed from PHP)
    if (typeof historicalData !== 'undefined' && historicalData.length > 0) {
        const labels = historicalData.map(data => {
            const date = new Date(data.created_at);
            return date.toLocaleString('id-ID', { day: '2-digit', month: 'short', hour: '2-digit', minute: '2-digit' });
        });
        const suhuData = historicalData.map(data => data.suhu);
        const kelembabanUdaraData = historicalData.map(data => data.kelembaban);
        const kelembabanTanahData = historicalData.map(data => data.kelembaban_tanah);

        const ctx = document.getElementById('historicalChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Suhu (°C)',
                        data: suhuData,
                        borderColor: 'rgba(255, 99, 132, 1)',
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        fill: false,
                        tension: 0.1
                    },
                    {
                        label: 'Kelembaban Udara (%)',
                        data: kelembabanUdaraData,
                        borderColor: 'rgba(54, 162, 235, 1)',
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        fill: false,
                        tension: 0.1
                    },
                    {
                        label: 'Kelembaban Tanah (%)',
                        data: kelembabanTanahData,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        fill: false,
                        tension: 0.1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false, // Allow canvas to resize freely
                scales: {
                    x: {
                        display: true,
                        title: {
                            display: true,
                            text: 'Waktu'
                        }
                    },
                    y: {
                        display: true,
                        title: {
                            display: true,
                            text: 'Nilai Sensor'
                        }
                    }
                }
            }
        });
    } else {
        console.log("No historical data available for charting.");
    }
});
