import ApexCharts from 'apexcharts';

const instances = {};

// Matches the app's existing accent colors (cyan/amber/emerald/rose/violet badges elsewhere in the UI).
const PALETTE = ['#22d3ee', '#fbbf24', '#34d399', '#fb7185', '#a78bfa'];

/**
 * Render (or re-render) a chart in the element with the given id.
 * payload: { labels: string[], series: { name: string, data: (number|null)[] }[], type?: 'area'|'bar'|'radar' }
 * `type` defaults to 'area' (the original line/gradient progress chart); 'bar' and 'radar' reuse the
 * same dark theme/palette/tooltip but skip the gradient fill (meaningless for those chart types).
 */
export function renderProgressChart(containerId, payload) {
    const el = document.getElementById(containerId);
    if (!el || !payload || !payload.series || payload.series.length === 0) {
        return;
    }

    // ponytail: a chart's tab can be display:none (Alpine x-show) at mount time, giving a 0-width
    // container. ApexCharts bakes that width into its DOM on first render and never recovers cleanly
    // even after a later destroy+recreate on the same node — so skip entirely while hidden and let the
    // tab-click re-init (see app.js) do the *first* real render once the container has actual width.
    if (!instances[containerId] && el.offsetWidth === 0) {
        return;
    }

    if (instances[containerId]) {
        instances[containerId].destroy();
        delete instances[containerId];
    }

    const type = payload.type || 'area';
    const isPlot = type === 'area'; // line/gradient chart with a date x-axis; bar and radar don't use these

    const options = {
        chart: {
            type,
            height: 320,
            toolbar: { show: isPlot, tools: { download: false } },
            zoom: { enabled: isPlot },
            background: 'transparent',
            fontFamily: 'inherit',
        },
        theme: { mode: 'dark' },
        colors: PALETTE,
        series: payload.series,
        grid: { borderColor: 'rgba(148, 163, 184, 0.12)', strokeDashArray: 3 },
        stroke: { curve: 'smooth', width: 2.5 },
        fill: isPlot
            ? { type: 'gradient', gradient: { opacityFrom: 0.35, opacityTo: 0.02, shadeIntensity: 1 } }
            : { opacity: type === 'radar' ? 0.25 : 1 },
        markers: { size: 3, hover: { size: 6 }, strokeWidth: 0 },
        dataLabels: { enabled: false },
        legend: { position: 'top', labels: { colors: '#cbd5e1' }, markers: { radius: 4 } },
        tooltip: { theme: 'dark' },
    };

    if (type === 'radar') {
        options.xaxis = { categories: payload.labels, labels: { style: { colors: '#cbd5e1', fontSize: '11px' } } };
    } else {
        options.xaxis = {
            categories: payload.labels,
            labels: { style: { colors: '#94a3b8', fontSize: '11px' } },
            axisBorder: { color: '#1e293b' },
            axisTicks: { color: '#1e293b' },
        };
        options.yaxis = { labels: { style: { colors: '#94a3b8', fontSize: '11px' } } };
    }

    if (isPlot) {
        options.tooltip.x = { format: 'dd MMM yyyy' };
    }

    if (type === 'bar') {
        options.plotOptions = { bar: { borderRadius: 4, columnWidth: '55%' } };
    }

    const chart = new ApexCharts(el, options);

    chart.render();
    instances[containerId] = chart;
}
