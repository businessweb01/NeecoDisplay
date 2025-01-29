<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excel to Chart.js</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-annotation@1.0.0"></script>
    <style>
        .canvases {
            display: flex;
            flex-direction: column;
            gap: 10px;
            justify-content: center;
            align-items: center;
            width: 100%;
        }

        .canvas-wrapper {
            display: flex;
            flex-direction: row;
            justify-content: center;
            align-items: center;
            max-width: 100%;
            width: 100%;
        }

        canvas {
            width: 100%;
            max-width: 100%;
            max-height: 200px;
            height: 100%;
        }

        p {
            margin: 5px;
            font-size: 14px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="canvases">
        <div class="canvas-wrapper">
            <canvas id="chart1"></canvas>
            <div class="indicator">
                <p id="MQ">Present Month MQ</p>
                <p id="MQupordown"></p>
            </div>
        </div>
        <div class="canvas-wrapper">
            <canvas id="chart2"></canvas>
            <div class="indicator">
                <p id="APRI">APRI</p>
                <p id="APRIupordown"></p>
            </div>
        </div>
        <div class="canvas-wrapper">
            <canvas id="chart3"></canvas>
            <div class="indicator">
                <p id="Php">Php/kwhr per Day</p>
                <p id="Phpupordown"></p>
            </div>
        </div>
    </div>

    <script>
$(document).ready(function() {
    // Declare chart variables globally
    let chart1, chart2, chart3;

    function loadChartData() {
        $.ajax({
            url: 'backend/consumer_backend.php',
            type: 'GET',
            success: function(response) {
                if (response.error) {
                    alert(response.error);
                    return;
                }

                console.log(response);

                const allData1 = response.dataset3;
                const allData2 = response.dataset4;
                const allData3 = response.dataset5;

                const compareValues = (data) => {
                    if (data.length < 30) return "Insufficient Data";
                    return data[29] > data[28] ? "Tumaas" : "Bumaba";
                };

                $('#MQ').text('Present Month MQ: ' + allData1[allData1.length - 1].toFixed(2));
                $('#APRI').text('APRI: ' + allData2[allData2.length - 1].toFixed(2));
                $('#Php').text('Php/kwhr per Day: ' + allData3[allData3.length - 1].toFixed(2));
                
                $('#MQupordown').text(compareValues(allData1));
                $('#APRIupordown').text(compareValues(allData2));
                $('#Phpupordown').text(compareValues(allData3));

                const createChart = (canvasId, label, data, color) => {
                    const ctx = document.getElementById(canvasId).getContext('2d');
                    return new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: response.labels,
                            datasets: [{
                                label: label,
                                data: data,
                                fill: false,
                                backgroundColor: color,
                                borderColor: color,
                                borderWidth: 2,
                                tension: 0,
                                pointRadius: 4,
                                pointHoverRadius: 8,
                                pointHitRadius: 10,
                                spanGaps: false,
                            }]
                        },
                        options: {
                            interaction: { mode: 'nearest', intersect: false },
                            responsive: true,
                            scales: { x: { display: false }, y: { display: false } },
                            plugins: { legend: { display: false }, tooltip: { enabled: true } }
                        }
                    });
                };

                // Assign chart instances to global variables
                chart1 = createChart('chart1', 'Present Month MQ', allData1, 'rgba(75, 192, 192, 1)');
                chart2 = createChart('chart2', 'APRI', allData2, 'rgba(255, 159, 64, 1)');
                chart3 = createChart('chart3', 'Php/kwhr per Day', allData3, 'rgb(115, 255, 102)');

                // Sync tooltips between charts
                const syncTooltips = (chart, event) => {
                    const activePoints = chart.getElementsAtEventForMode(event, 'nearest', { intersect: false }, true);
                    const index = activePoints[0]?.index;

                    if (index !== undefined) {
                        [chart1, chart2, chart3].forEach((c) => {
                            const tooltip = c.tooltip;
                            tooltip.setActiveElements([{
                                datasetIndex: 0,
                                index: index
                            }]);
                            c.update();
                        });
                    }
                };

                // Attach the event listeners to sync tooltips across all charts
                chart1.canvas.addEventListener('mousemove', (e) => syncTooltips(chart1, e));
                chart2.canvas.addEventListener('mousemove', (e) => syncTooltips(chart2, e));
                chart3.canvas.addEventListener('mousemove', (e) => syncTooltips(chart3, e));

                // Function to update the <p> tags with formatted data
                const updateDisplay = () => {
                    for (let i = 0; i < allData1.length; i++) {
                        // Update the p tags with formatted values
                        $('#MQ').text('Present Month MQ: ' + allData1[i].toFixed(2));
                        $('#APRI').text('APRI: ' + allData2[i].toFixed(2));
                        $('#Php').text('Php/kwhr per Day: ' + allData3[i].toFixed(2));
                    }
                };

                // Call the updateDisplay function to update the tags
                updateDisplay();
            },
            error: function(xhr) {
                alert('An error occurred while fetching the data: ' + xhr.responseText);
            }
        });
    }

    loadChartData(); // Automatically fetch data on page load
    setInterval(loadChartData, 60000); // Auto-refresh every 60 seconds
});

    </script>
</body>
</html>
