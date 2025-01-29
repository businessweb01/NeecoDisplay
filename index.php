<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excel to Chart.js</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Annotation Plugin -->
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-annotation@1.0.0"></script>
    <style>
        /* Make the layout flexible and responsive */
        .canvases {
            display: flex;
            flex-direction: column;
            gap: 10px;
            justify-content: center;
            align-items: center;
            width: 100%;  /* Full width */
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
            width: 100%;  /* Make canvas take full width of the container */
            max-width: 100%;
            max-height: 200px;  /* Set a maximum height */
            height:100%;  /* Set a fixed height */
        }

        p {
            margin: 5px;
            font-size: 14px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <form id="uploadForm" method="post" enctype="multipart/form-data">
        <input type="file" id="file" name="file" accept=".xlsx, .xls" />
        <button type="submit">Submit</button>
    </form>

    <!-- Multiple canvas elements with max-height 200px -->
    <div class="canvases">
        <div class="canvas-wrapper">
            <canvas id="chart1"></canvas>
            <div class="indicator">
                <p id="MQ">Present Month MQ</p>
                <p id="Mqupordown"></p>
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
        $('#uploadForm').on('submit', function(e) {
            e.preventDefault(); // Prevent default form submission

            const formData = new FormData(this); // Get form data

            $.ajax({
                url: 'backend/backend.php', // Path to the PHP script
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.error) {
                        alert(response.error);
                        return;
                    }

                    console.log(response); // Log the response for debugging

                    // Use all the data from the server
                    const allData1 = response.dataset3;
                    const allData2 = response.dataset4;
                    const allData3 = response.dataset5;
                     // Function to determine if the value increased or decreased
                const compareValues = (data) => {
                    if (data.length < 30) return "Insufficient Data";
                    return data[29] > data[28] ? "Tumaas" : "Bumaba";
                };

                // Update indicator text
                $('#MQ').text('Present Month MQ: ' + allData1[allData1.length - 1].toFixed(2));
                $('#APRI').text('APRI: ' + allData2[allData2.length - 1].toFixed(2));
                $('#Php').text('Php/kwhr per Day: ' + allData3[allData3.length - 1].toFixed(2));
                
                $('#MQupordown').text(compareValues(allData1)); // Update upordown indicator
                $('#APRIupordown').text(compareValues(allData2)); // Update upordown indicator
                $('#Phpupordown').text(compareValues(allData3)); // Update upordown indicator

                    // Data for each chart
                    const data1 = {
                        labels: response.labels,  // Load labels from the response
                        datasets: [
                            {
                                label: 'Present Month MQ',
                                data: allData1,  // Show all data
                                fill: false,
                                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                borderColor: 'rgba(75, 192, 192, 1)',
                                borderWidth: 2,
                                tension: 0,  // Ensure consistent spacing between points
                                pointRadius: 4,
                                pointHoverRadius: 8,
                                pointHitRadius: 10,
                                spanGaps: false,
                                id: 'dataset1',
                            },
                        ],
                    };

                    const data2 = {
                        labels: response.labels,  // Load labels from the response
                        datasets: [
                            {
                                label: 'APRI',
                                data: allData2,  // Show all data
                                fill: false,
                                backgroundColor: 'rgba(255, 159, 64, 0.5)',
                                borderColor: 'rgba(255, 159, 64, 1)',
                                borderWidth: 3,
                                tension: 0,  // Ensure consistent spacing between points
                                pointRadius: 6,
                                pointHoverRadius: 10,
                                pointHitRadius: 12,
                                spanGaps: false,
                                id: 'dataset2',
                            },
                        ],
                    };

                    const data3 = {
                        labels: response.labels,  // Load labels from the response
                        datasets: [
                            {
                                label: 'Php/kwhr per Day',
                                data: allData3,  // Show all data
                                fill: true,
                                backgroundColor: 'rgba(4, 251, 0, 0.2)',
                                borderColor: 'rgb(115, 255, 102)',
                                borderWidth: 3,
                                tension: 0,  // Ensure consistent spacing between points
                                pointRadius: 6,
                                pointHoverRadius: 10,
                                pointHitRadius: 12,
                                spanGaps: false,
                                id: 'dataset3',
                            },
                        ],
                    };

                    // Chart configuration
                    const config = {
                        type: 'line',
                        options: {
                            interaction: {
                                mode: 'nearest',  // Show tooltips for the nearest data point
                                intersect: false,  // Show tooltips even if hovering on empty space
                            },
                            responsive: true,
                            hover: {
                                mode: 'nearest',  // Show tooltips for the nearest data point
                                intersect: false,
                            },
                            animation: {
                                duration: 0,  // Disable chart animation completely
                            },
                            scales: {
                                x: {
                                    display: false,  // Hide the x-axis labels and grid
                                    grid: {
                                        display: false,  // Hide the x-axis grid lines
                                    },
                                },
                                y: {
                                    display: false,  // Hide the y-axis labels and grid
                                    grid: {
                                        display: false,  // Hide the y-axis grid lines
                                    },
                                },
                            },
                            plugins: {
                                legend: {
                                    display: false,  // Hide the legend with color boxes and labels
                                },
                                tooltip: {
                                    enabled: true,  // Enable tooltips
                                    mode: 'nearest',  // Show the tooltip for the closest data point
                                    intersect: false,  // Allow tooltips even if not hovering directly on a point
                                    position: 'average',  // Position the tooltip at the average point for all datasets
                                    callbacks: {
                                        label: function(tooltipItem) {
                                            return tooltipItem.dataset.label + ': ' + tooltipItem.raw.toFixed(2);
                                        },
                                    },
                                },
                            },
                        },
                    };

                    // Create the chart instances for each dataset on a different canvas
                    const chart1Ctx = document.getElementById('chart1').getContext('2d');
                    const chart1 = new Chart(chart1Ctx, { ...config, data: data1 });

                    const chart2Ctx = document.getElementById('chart2').getContext('2d');
                    const chart2 = new Chart(chart2Ctx, { ...config, data: data2 });

                    const chart3Ctx = document.getElementById('chart3').getContext('2d');
                    const chart3 = new Chart(chart3Ctx, { ...config, data: data3 });

                    // Create a function to sync tooltips between all charts
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
                error: function(xhr, status, error) {
                    alert('An error occurred while processing the file: ' + xhr.responseText);
                }
            });
        });
    </script>
</body>
</html>
