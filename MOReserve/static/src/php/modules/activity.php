<div class="grid grid-cols-3 gap-6">
    <section class="col-span-2 bg-gray-800 shadow rounded-lg p-6">
        <h4 class="text-lg font-medium text-gray-200">Monthly Spending</h4>
        <canvas id="monthlySpendingChart" class="w-full mt-6 rounded-lg"></canvas>
    </section>

    <!-- daily -->
    <section class="col-span-1 bg-gradient-to-r from-teal-500 via-cyan-600 to-cyan-700 shadow-lg rounded-lg p-6 text-white flex flex-col items-center justify-center">
        <h4 class="text-lg font-medium text-center">Daily Spending Average</h4>
        <div class="text-5xl font-bold bg-gradient-to-br from-white to-gray-300 bg-clip-text text-transparent mt-4">
            $54.32
        </div>
        <p class="text-sm mt-2 text-gray-300">Compared To <span class="font-medium">$62.10</span> Last Month</p>
    </section>

    <section class="col-span-2 bg-gray-800 shadow rounded-lg p-6">
        <h4 class="text-lg font-medium text-gray-200">Daily Spending</h4>
        <canvas id="dailySpendingChart" class="w-full mt-6"></canvas>
    </section>

    <!-- stats -->
    <section class="col-span-1 bg-gray-800 shadow-lg rounded-lg p-6 text-gray-200 flex items-center justify-center">
        <div class="mt-6 space-y-4">
            <div class="flex justify-between bg-gray-700 p-6 rounded-lg">
                <span class="mr-4">Total Transactions</span>
                <span class="font-semibold bg-gradient-to-bl from-teal-300 to-cyan-600 bg-clip-text text-transparent">$120.00</span>
            </div>
            <div class="flex justify-between bg-gray-700 p-6 rounded-lg">
                <span class="mr-4">Highest Spending</span>
                <span class="font-semibold bg-gradient-to-l from-teal-300 to-cyan-600 bg-clip-text text-transparent">$230.75</span>
            </div>
            <div class="flex justify-between bg-gray-700 p-6 rounded-lg">
                <span class="mr-4">Total Spendings</span>
                <span class="font-semibold bg-gradient-to-tl from-teal-300 to-cyan-600 bg-clip-text text-transparent">$5,432.00</span>
            </div>
        </div>
    </section>
</div>

<script>
    // monthly
    const monthlySpendingCtx = document.getElementById('monthlySpendingChart').getContext('2d');
    const monthlyGradient = monthlySpendingCtx.createLinearGradient(0, 0, 0, 300);
    monthlyGradient.addColorStop(0, 'rgba(94, 234, 212, 0.7)');
    monthlyGradient.addColorStop(1, 'rgba(94, 234, 212, 0.2)');

    new Chart(monthlySpendingCtx, {
        type: 'line',
        data: {
            labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
            datasets: [{
                label: 'Monthly Spending',
                data: [1200, 900, 1300, 1100],
                borderColor: 'rgba(45, 212, 191, 1)',
                backgroundColor: monthlyGradient,
                tension: 0.3,
                pointBackgroundColor: 'rgba(45, 212, 191, 1)',
                borderWidth: 2,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        font: { family: 'Monospace' },
                    },
                },
                x: {
                    ticks: {
                        font: { family: 'Monospace' },
                    },
                }
            }
        }
    });

    // daily
    const dailySpendingCtx = document.getElementById('dailySpendingChart').getContext('2d');
    new Chart(dailySpendingCtx, {
        type: 'bar',
        data: {
            labels: ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
            datasets: [{
                label: 'Daily Spending',
                data: [50, 70, 40, 60, 100, 90, 30],
                backgroundColor: 'rgba(94, 234, 212, 0.7)',
                borderColor: 'rgba(45, 212, 191, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: { enabled: true },
                datalabels: {
                    anchor: 'end',
                    align: 'top',
                    formatter: (value) => `$${value}`,
                    color: 'white',
                    font: { family: 'Monospace' },
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value;
                        },
                        font: { family: 'Monospace' },
                    },
                    suggestedMax: 120, // extra padding
                },
                x: {
                    ticks: {
                        font: { family: 'Monospace' },
                    },
                }
            }
        },
        plugins: [ChartDataLabels]
    });
</script>