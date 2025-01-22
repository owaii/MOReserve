var urlParam = new URLSearchParams(window.location.search);
var id = urlParam.get("id");

document.addEventListener('alpine:init', () => {
    Alpine.data('moneyFlow', () => ({
        view: 'Week',
        chart: null,
        chartConfig: null,
        fetchDahsData: [],
        fetchDayData: [],

        async init() {
            await this.fillDayDashboardData();
            await this.fillDayData();
            this.renderChart();
        },

        async fillDayDashboardData() {
            try {
                const response = await fetch(`static/src/php/week.php?id=${encodeURIComponent(id)}`, {
                    headers: { "Content-Type": "application/json" },
                });

                if (!response.ok) throw new Error(`Failed to fetch week data: ${response.status}`);

                const data = await response.json();

                if (data.status) {
                    const tempData = [];
                    for (let i = 0; i < data.day.length; i++) {
                        tempData[i] = data.day[i] || 0;
                    }
                    this.fetchDahsData = tempData;
                } else {
                    console.error("Server returned an error:", data.message);
                }
            } catch (error) {
                console.error("Error fetching week data:", error);
            }
        },

        async fillDayData() {
            try {
                const response = await fetch(`static/src/php/day.php?id=${encodeURIComponent(id)}`, {
                    headers: { "Content-Type": "application/json" },
                });

                if (!response.ok) throw new Error(`Failed to fetch day data: ${response.status}`);

                const data = await response.json();

                if (data.status) {
                    const tempData = new Array(7).fill(0);
                    for (let i = 0; i < data.number.length; i++) {
                        tempData[data.number[i] - 1] = parseInt(data.val[i]);
                    }
                    this.fetchDayData = tempData;
                } else {
                    console.error("Server returned an error:", data.message);
                }
            } catch (error) {
                console.error("Error fetching day data:", error);
            }
        },

        chartData() {
            return {
                Week: {
                    labels: ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
                    data: this.fetchDahsData,
                    type: 'line',
                },
                Day: {
                    labels: ['12 AM', '4 AM', '8 AM', '12 PM', '4 PM', '8 PM', '12 AM'],
                    data: this.fetchDayData,
                    type: 'bar',
                },
            };
        },

        renderChart() {
            if (this.chart) this.chart.destroy();
            const ctx = document.getElementById('moneyFlowChart').getContext('2d');
            const { labels, data, type } = this.chartData()[this.view];
            this.chartConfig = this.getChartConfig(labels, data, type);
            this.chart = new Chart(ctx, this.chartConfig);
        },

        toggleView() {
            this.view = this.view === 'Week' ? 'Day' : 'Week';
            this.renderChart();
        },

        getChartConfig(labels, data, type) {
            return {
                type: type,
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Savings Flow',
                        data: data,
                        borderColor: 'rgba(45, 212, 191, 1)',
                        backgroundColor: type === 'line'
                            ? 'rgba(94, 234, 212, 0.2)'
                            : 'rgba(94, 234, 212, 0.7)',
                        tension: type === 'line' ? 0.3 : 0,
                        fill: type === 'line',
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { ticks: { color: '#9CA3AF' }, grid: { color: '#374151' } },
                        y: { ticks: { color: '#9CA3AF' }, grid: { color: '#374151' } },
                    },
                },
            };
        },
    }));
});
