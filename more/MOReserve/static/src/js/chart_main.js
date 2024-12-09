console.log(this.chartConfig);
document.addEventListener('alpine:init', () => {
    Alpine.data('moneyFlow', () => ({
        view: 'Week',
        chart: null,
        chartConfig: null,

        chartData: {
            Week: {
                labels: ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
                data: [300, 500, 400, 450, 700, 600, 750],
                type: 'line',
            },
            Day: {
                labels: ['12 AM', '4 AM', '8 AM', '12 PM', '4 PM', '8 PM', '12 AM'],
                data: [50, 100, 200, 300, 400, 350, 450],
                type: 'bar',
            },
        },

        init() {
            const ctx = document.getElementById('moneyFlowChart').getContext('2d');
            this.chartConfig = this.getChartConfig(this.view);
            this.chart = new Chart(ctx, this.chartConfig);
        },

        toggleView() {
            this.view = this.view === 'Week' ? 'Day' : 'Week';
            this.updateChart();
        },

        getChartConfig(view) {
            const { labels, data, type } = this.chartData[view];
            return {
                type: type,
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Savings Flow',
                        data: data,
                        borderColor: 'rgba(45, 212, 191, 1)',
                        backgroundColor: view === 'Week'
                            ? 'rgba(94, 234, 212, 0.2)'
                            : 'rgba(94, 234, 212, 0.7)',
                        tension: view === 'Week' ? 0.3 : 0,
                        pointBackgroundColor: view === 'Week'
                            ? 'rgba(45, 212, 191, 1)'
                            : null,
                        pointRadius: view === 'Week' ? 5 : 0,
                        fill: view === 'Week',
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        x: {
                            ticks: {
                                color: '#9CA3AF',
                                font: { family: 'Monospace' },
                            },
                            grid: { color: '#374151' },
                        },
                        y: {
                            ticks: {
                                color: '#9CA3AF',
                                font: { family: 'Monospace' },
                            },
                            grid: { color: '#374151' },
                        },
                    },
                },
            };
        },

        updateChart() {
            if (this.chart) {
                this.chart.destroy();
            }
            const ctx = document.getElementById('moneyFlowChart').getContext('2d');
            this.chartConfig = this.getChartConfig(this.view);
            this.chart = new Chart(ctx, this.chartConfig);
        },
    }));
});
