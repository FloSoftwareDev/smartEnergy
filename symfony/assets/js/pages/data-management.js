import { api } from '../utils/api';
import { wrapApiCall } from '../utils/errorHandling';

class DataManagement {
    constructor() {
        this.bindEvents();
        this.loadInitialData();
    }

    async loadInitialData() {
        await wrapApiCall(async () => {
            const response = await api.get('/api/data');
            this.displayData(response.data);
        });
    }

    bindEvents() {
        const form = document.querySelector('#dataForm');
        if (form) {
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                await this.handleFormSubmit(form);
            });
        }
    }

    async handleFormSubmit(form) {
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());

        await wrapApiCall(async () => {
            const response = await api.post('/api/data', data);
            this.displayData(response.data);
            form.reset();
        });
    }

    displayData(data) {
        const container = document.querySelector('#dataContainer');
        if (!container) return;

        // Example of updating the UI with the received data
        container.innerHTML = data.map(item => `
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">${item.title}</h5>
                    <p class="card-text">${item.description}</p>
                </div>
            </div>
        `).join('');
    }
}

// Initialize when the DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    new DataManagement();
}); 