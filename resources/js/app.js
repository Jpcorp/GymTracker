import { renderProgressChart } from './charts';

window.renderProgressChart = renderProgressChart;

function initProgressCharts() {
    document.querySelectorAll('[data-progress-chart]').forEach((el) => {
        const payload = JSON.parse(el.dataset.progressChart || 'null');
        renderProgressChart(el.id, payload);
    });
}

// ponytail: ApexCharts sizes itself once at mount against its container's current box, so a chart mounted
// while its Alpine x-show tab is display:none renders at 0 width forever — a plain window 'resize' event
// doesn't fix it (ApexCharts doesn't relayout off unrelated resize events). Re-running the same idempotent
// render (destroy + recreate) after a tab switch reads the container's now-real width instead.
window.reinitProgressCharts = initProgressCharts;

document.addEventListener('DOMContentLoaded', initProgressCharts);
document.addEventListener('livewire:navigated', initProgressCharts);
