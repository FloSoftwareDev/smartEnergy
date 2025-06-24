import { api } from './utils/api.js';
import { wrapApiCall, showError } from './utils/errorHandling.js';

class EnergyDashboard {
    constructor() {
        this.chart = null;
        this.init();
    }

    async init() {
        await this.initializeChart();
        await this.loadDashboardData();
        this.startAutoRefresh();
    }

    async initializeChart() {
        const ctx = document.getElementById('energyChart');
        if (!ctx) return;

        this.chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'Verbruik (kWh)',
                    data: [],
                    borderColor: '#e74c3c',
                    backgroundColor: 'rgba(231, 76, 60, 0.1)',
                    tension: 0.4
                }, {
                    label: 'Productie (kWh)',
                    data: [],
                    borderColor: '#27ae60',
                    backgroundColor: 'rgba(39, 174, 96, 0.1)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Energie Verbruik vs Productie'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'kWh'
                        }
                    }
                }
            }
        });
    }

    async loadDashboardData() {
        await wrapApiCall(async () => {
            const response = await api.get('/api/dashboard-data');
            this.updateDashboard(response.data);
        });
    }

    updateDashboard(data) {
        // Update metrics
        document.getElementById('totalConsumption').textContent = 
            parseFloat(data.totalConsumption).toFixed(1);
        document.getElementById('totalProduction').textContent = 
            parseFloat(data.totalProduction).toFixed(1);
        document.getElementById('totalCost').textContent = 
            '€' + parseFloat(data.totalCost).toFixed(2);
        document.getElementById('efficiency').textContent = 
            parseFloat(data.efficiency).toFixed(1) + '%';
        document.getElementById('netConsumption').textContent = 
            parseFloat(data.netConsumption).toFixed(1);
        document.getElementById('savings').textContent = 
            '€' + parseFloat(data.savings).toFixed(2);

        // Update recent activity
        this.updateRecentActivity(data.latestData);

        // Update chart
        this.updateChart(data.latestData);
    }

    updateRecentActivity(data) {
        const container = document.getElementById('recentActivity');
        if (!container) return;

        container.innerHTML = data.map(item => `
            <div class="d-flex justify-content-between align-items-center mb-2 p-2 border-bottom">
                <div>
                    <strong>${item.source}</strong><br>
                    <small class="text-muted">${new Date(item.timestamp).toLocaleTimeString('nl-NL', {hour: '2-digit', minute:'2-digit'})}</small>
                </div>
                <div class="text-end">
                    <div class="text-success">${parseFloat(item.production).toFixed(1)} kWh</div>
                    <div class="text-danger">${parseFloat(item.consumption).toFixed(1)} kWh</div>
                </div>
            </div>
        `).join('');
    }

    updateChart(data) {
        if (!this.chart) return;

        const labels = data.map(item => 
            new Date(item.timestamp).toLocaleTimeString('nl-NL', {hour: '2-digit', minute:'2-digit'})
        ).reverse();

        const consumptionData = data.map(item => parseFloat(item.consumption)).reverse();
        const productionData = data.map(item => parseFloat(item.production)).reverse();

        this.chart.data.labels = labels;
        this.chart.data.datasets[0].data = consumptionData;
        this.chart.data.datasets[1].data = productionData;
        this.chart.update();
    }

    startAutoRefresh() {
        // Refresh data every 30 seconds
        setInterval(() => {
            this.loadDashboardData();
        }, 30000);
    }
}

// Global functions for button clicks
window.refreshData = async function() {
    const dashboard = new EnergyDashboard();
    await dashboard.loadDashboardData();
};

window.addSampleData = async function() {
    await wrapApiCall(async () => {
        const sampleData = {
            timestamp: new Date().toISOString(),
            consumption: Math.random() * 5 + 2,
            production: Math.random() * 4,
            cost: (Math.random() * 5 + 2) * 0.25,
            source: ['Solar', 'Wind', 'Grid', 'Battery'][Math.floor(Math.random() * 4)],
            efficiency: Math.random() * 100
        };

        await api.post('/api/energy-data', sampleData);
        
        // Refresh dashboard after adding data
        await refreshData();
    });
};

// Initialize dashboard when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    new EnergyDashboard();
}); 