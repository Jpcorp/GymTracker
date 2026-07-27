import { renderProgressChart } from './charts';

window.renderProgressChart = renderProgressChart;

function initProgressCharts() {
    document.querySelectorAll('[data-progress-chart]').forEach((el) => {
        const payload = JSON.parse(el.dataset.progressChart || 'null');
        renderProgressChart(el.id, payload);
    });
}

document.addEventListener('DOMContentLoaded', initProgressCharts);
document.addEventListener('livewire:navigated', initProgressCharts);
