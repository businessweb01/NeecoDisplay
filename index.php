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


    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,400;0,700&display=swap"
        rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            font-family: "Poppins", sans-serif, Arial;
        }

        /* Make the layout flexible and responsive */
        .canvases {
            display: flex;
            flex-direction: column;
            gap: 10px;
            justify-content: center;
            align-items: center;
            width: 100%;
            /* Full width */
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
            /* Make canvas take full width of the container */
            max-width: 100%;
            max-height: 150px;
            /* Set a maximum height */
            height: 100%;
            /* Set a fixed height */
        }

        @keyframes marquee {
            0% {
                transform: translateX(100%);
            }

            100% {
                transform: translateX(-100%);
            }
        }

        .animate-marquee {
            display: inline-block;
            white-space: nowrap;
            animation: marquee 30s linear infinite;
        }
    </style>
</head>

<body>
    <!-- navigation bar -->
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <!-- Large screen logo -->
            <img src="images/logo.png" alt="Logo" class="h-20 d-none d-lg-block">

            <!-- Small screen logo -->
            <img src="images/small_logo.png" alt="Small Logo" class="h-20 d-block d-lg-none">
        </div>
    </nav>

    <!-- animation -->
    <div class="bg-green-400 h-10 flex items-center overflow-hidden whitespace-nowrap relative">
        <!-- Fixed Advisory Label -->
        <span class="uppercase font-bold text-white bg-green-800 px-2 lg:px-10 py-2 rounded-e-lg shadow-md flex-shrink-0 relative z-10">
            System Advisory
        </span>

        <!-- Scrolling Text -->
        <p class="absolute animate-marquee text-white font-semibold uppercase ml-4 pl-3 z-0">
            Lorem ipsum dolor, sit amet consectetur adipisicing elit. Sunt asperiores dolorem id corrupti, optio officia consequatur. Dolore deleniti fugit porro, dignissimos nulla sed amet placeat accusamus rem, error doloribus culpa.
        </p>
    </div>

    <!-- body -->
    <div class="container row">
        <div class="col-md-12 col-lg-3 col-sm-12">
            <div class="row p-2">
                <div class="col col-md-4 col-sm-4 col-lg-12">
                    <div class="card mb-2 shadow">
                        <div class="card-body">
                            <h4 class="card-title fw-bold">Present Month MQ</h4>
                            <p id="MQ" class="fs-3 fw-semibold"></p>
                            <p id="Mqupordown"></p>
                        </div>
                    </div>
                </div>
                <div class="col col-md-4 col-sm-4 col-lg-12">
                    <div class="card mb-2 shadow">
                        <div class="card-body">
                            <h4 class="card-title fw-bold">APRI</h4>
                            <p id="APRI" class="fs-3 fw-semibold"></p>
                            <p id="APRIupordown"></p>
                        </div>
                    </div>
                </div>
                <div class="col col-md-4 col-sm-4 col-lg-12">
                    <div class="card mb-2 shadow">
                        <div class="card-body">
                            <h4 class="card-title fw-bold">Php/kwhr per Day</h4>
                            <p id="Php" class="fs-3 fw-semibold"></p>
                            <p id="Phpupordown"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12 col-lg-6 col-sm-12 p-2">
            <form id="uploadForm" method="post" enctype="multipart/form-data">
                <div class="flex mb-2">
                    <input type="file" id="file" name="file" accept=".xlsx, .xls" class="form-control" />
                    <button type="submit" class="btn btn-success ms-2">Submit</button>
                </div>
            </form>

            <!-- Multiple canvas elements with max-height 200px -->
            <div class="canvases border-2 rounded shadow">
                <div class="canvas-wrapper">
                    <canvas id="chart1"></canvas>
                </div>
                <div class="canvas-wrapper">
                    <canvas id="chart2"></canvas>
                </div>
                <div class="canvas-wrapper">
                    <canvas id="chart3"></canvas>
                </div>
            </div>

        </div>

        <div class="col-md-12 col-lg-3 col-sm-12">
            <div class="p-2">
                <div class="bg-white d-flex justify-content-center align-items-center shadow rounded">
                    <img src="images/Neeco2Area1Map.png" alt="Philippine Map" class="img-fluid rounded">
                </div>
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
                        console.log("Comparing values:", data[29], data[28]); // Check the values being compared

                        const result = data[29] > data[28] ? "Tumaas" : "Bumaba";
                        console.log("Comparison result:", result); // Log the result of comparison
                        return result;
                    };

                    const updateTextColor = (elementId, result) => {
                        const element = $("#" + elementId);

                        if (result === "Tumaas") {
                            element.addClass("text-green-500").removeClass("text-red-700");
                        } else if (result === "Bumaba") {
                            element.addClass("text-red-700").removeClass("text-green-500");
                        }
                    };

                    // Update indicator text
                    $('#MQ').text('Present Month MQ: ' + allData1[allData1.length - 1].toFixed(2));
                    $('#APRI').text('APRI: ' + allData2[allData2.length - 1].toFixed(2));
                    $('#Php').text('Php/kwhr per Day: ' + allData3[allData3.length - 1].toFixed(2));

                    const apriResult = compareValues(allData2);
                    const phpResult = compareValues(allData3);

                    // Update the comparison text and color
                    $('#MQupordown').text(compareValues(allData1));
                    $('#APRIupordown').text(apriResult);
                    $('#Phpupordown').text(phpResult);

                    updateTextColor("APRI", apriResult);
                    updateTextColor("APRIupordown", apriResult);

                    updateTextColor("Php", phpResult);
                    updateTextColor("Phpupordown", phpResult);

                    // Data for each chart
                    const data1 = {
                        labels: response.labels, // Load labels from the response
                        datasets: [{
                            label: 'Present Month MQ',
                            data: allData1, // Show all data
                            fill: false,
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 2,
                            tension: 0, // Ensure consistent spacing between points
                            pointRadius: 4,
                            pointHoverRadius: 8,
                            pointHitRadius: 10,
                            spanGaps: false,
                            id: 'dataset1',
                        }, ],
                    };

                    const data2 = {
                        labels: response.labels, // Load labels from the response
                        datasets: [{
                            label: 'APRI',
                            data: allData2, // Show all data
                            fill: false,
                            backgroundColor: 'rgba(255, 159, 64, 0.5)',
                            borderColor: 'rgba(255, 159, 64, 1)',
                            borderWidth: 3,
                            tension: 0, // Ensure consistent spacing between points
                            pointRadius: 6,
                            pointHoverRadius: 10,
                            pointHitRadius: 12,
                            spanGaps: false,
                            id: 'dataset2',
                        }, ],
                    };

                    const data3 = {
                        labels: response.labels, // Load labels from the response
                        datasets: [{
                            label: 'Php/kwhr per Day',
                            data: allData3, // Show all data
                            fill: true,
                            backgroundColor: 'rgba(4, 251, 0, 0.2)',
                            borderColor: 'rgb(115, 255, 102)',
                            borderWidth: 3,
                            tension: 0, // Ensure consistent spacing between points
                            pointRadius: 6,
                            pointHoverRadius: 10,
                            pointHitRadius: 12,
                            spanGaps: false,
                            id: 'dataset3',
                        }, ],
                    };

                    // Chart configuration
                    const config = {
                        type: 'line',
                        options: {
                            interaction: {
                                mode: 'nearest', // Show tooltips for the nearest data point
                                intersect: false, // Show tooltips even if hovering on empty space
                            },
                            responsive: true,
                            hover: {
                                mode: 'nearest', // Show tooltips for the nearest data point
                                intersect: false,
                            },
                            animation: {
                                duration: 0, // Disable chart animation completely
                            },
                            scales: {
                                x: {
                                    display: false, // Hide the x-axis labels and grid
                                    grid: {
                                        display: false, // Hide the x-axis grid lines
                                    },
                                },
                                y: {
                                    display: false, // Hide the y-axis labels and grid
                                    grid: {
                                        display: false, // Hide the y-axis grid lines
                                    },
                                },
                            },
                            plugins: {
                                legend: {
                                    display: false, // Hide the legend with color boxes and labels
                                },
                                tooltip: {
                                    enabled: true, // Enable tooltips
                                    mode: 'nearest', // Show the tooltip for the closest data point
                                    intersect: false, // Allow tooltips even if not hovering directly on a point
                                    position: 'average', // Position the tooltip at the average point for all datasets
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
                    const chart1 = new Chart(chart1Ctx, {
                        ...config,
                        data: data1
                    });

                    const chart2Ctx = document.getElementById('chart2').getContext('2d');
                    const chart2 = new Chart(chart2Ctx, {
                        ...config,
                        data: data2
                    });

                    const chart3Ctx = document.getElementById('chart3').getContext('2d');
                    const chart3 = new Chart(chart3Ctx, {
                        ...config,
                        data: data3
                    });

                    // Create a function to sync tooltips between all charts
                    const syncTooltips = (chart, event) => {
                        const activePoints = chart.getElementsAtEventForMode(event, 'nearest', {
                            intersect: false
                        }, true);
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
                            // $('#MQ').text('Present Month MQ: ' + allData1[i].toFixed(2));
                            // $('#APRI').text('APRI: ' + allData2[i].toFixed(2));
                            // $('#Php').text('Php/kwhr per Day: ' + allData3[i].toFixed(2));

                            // no text

                            $('#MQ').text(allData1[i].toFixed(2));
                            $('#APRI').text(allData2[i].toFixed(2));
                            $('#Php').text(allData3[i].toFixed(2));
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