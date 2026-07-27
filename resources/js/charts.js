import ApexCharts from 'apexcharts';

const instances = {};

/**
 * Render (or re-render) a line chart in the element with the given id.
 * payload: { labels: string[], series: { name: string, data: (number|null)[] }[] }
 */
export function renderProgressChart(containerId, payload) {
    const el = document.getElementById(containerId);
    if (!el || !payload || !payload.series || payload.series.length === 0) {
        return;
    }

    if (instances[containerId]) {
        instances[containerId].destroy();
        delete instances[containerId];
    }

    const chart = new ApexCharts(el, {
        chart: { type: 'line', height: 320, toolbar: { show: false } },
        series: payload.series,
        xaxis: { categories: payload.labels },
        stroke: { curve: 'smooth', width: 2 },
        dataLabels: { enabled: false },
        legend: { position: 'top' },
    });

    chart.render();
    instances[containerId] = chart;
}
