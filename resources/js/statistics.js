import Chart from 'chart.js/auto';

// Brand palette: IRSO yellow first, then colors that stay distinguishable side by side.
const COLORS = ['#f59e0b', '#6366f1', '#10b981', '#f43f5e', '#0ea5e9', '#8b5cf6', '#14b8a6', '#f97316'];

// Text, grid and surface colors for each panel theme.
const THEMES = {
    light: { text: '#6b7280', grid: '#f3f4f6', surface: '#ffffff', tooltip: '#111827' },
    dark: { text: '#9ca3af', grid: 'rgba(255, 255, 255, 0.06)', surface: '#111827', tooltip: '#374151' },
};

const charts = [];

Chart.defaults.font.family = "Figtree, ui-sans-serif, system-ui, sans-serif";
Chart.defaults.plugins.tooltip.padding = 10;
Chart.defaults.plugins.tooltip.cornerRadius = 8;
Chart.defaults.plugins.tooltip.displayColors = false;

function currentTheme() {
    return document.documentElement.classList.contains('dark') ? THEMES.dark : THEMES.light;
}

/**
 * Reads the chart data stored in the canvas `data-labels` and `data-values` JSON attributes.
 *
 * @param {HTMLCanvasElement} canvas
 * @returns {{labels: string[], values: number[]}}
 */
function readData(canvas) {
    return {
        labels: JSON.parse(canvas.dataset.labels),
        values: JSON.parse(canvas.dataset.values),
    };
}

/**
 * Axis options shared by the bar and line charts.
 *
 * @param {boolean} horizontal Whether the values run along the x axis.
 */
function axes(horizontal = false) {
    const values = { beginAtZero: true, ticks: { precision: 0 }, grid: {}, border: { display: false } };
    const labels = { grid: { display: false }, border: { display: false } };

    return horizontal ? { x: values, y: labels } : { x: labels, y: values };
}

/**
 * Paints a chart with the colors of the current theme.
 *
 * @param {Chart} chart
 */
function applyTheme(chart) {
    const theme = currentTheme();

    chart.options.color = theme.text;
    chart.options.plugins.tooltip.backgroundColor = theme.tooltip;
    Object.values(chart.options.scales ?? {}).forEach((scale) => {
        scale.ticks.color = theme.text;
        if (scale.grid.display !== false) scale.grid.color = theme.grid;
    });
    if (chart.config.type === 'doughnut') {
        chart.data.datasets[0].borderColor = theme.surface;
    }
    if (chart.config.type === 'line') {
        chart.data.datasets[0].pointBackgroundColor = theme.surface;
    }
    chart.update('none');
}

function register(chart) {
    charts.push(chart);
    applyTheme(chart);
}

function renderMonthly(canvas) {
    const { labels, values } = readData(canvas);
    const gradient = canvas.getContext('2d').createLinearGradient(0, 0, 0, canvas.clientHeight);
    gradient.addColorStop(0, 'rgba(245, 158, 11, 0.25)');
    gradient.addColorStop(1, 'rgba(245, 158, 11, 0)');

    register(new Chart(canvas, {
        type: 'line',
        data: {
            labels,
            datasets: [{
                label: 'Publicaciones',
                data: values,
                borderColor: COLORS[0],
                backgroundColor: gradient,
                fill: true,
                tension: 0.4,
                borderWidth: 2.5,
                pointBorderColor: COLORS[0],
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6,
            }],
        },
        options: { maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: axes() },
    }));
}

function renderBar(canvas, horizontal = false) {
    const { labels, values } = readData(canvas);

    register(new Chart(canvas, {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: horizontal ? 'Publicaciones' : 'Usuarios',
                data: values,
                backgroundColor: horizontal ? COLORS[0] : labels.map((_, index) => COLORS[index % COLORS.length]),
                borderRadius: 6,
                borderSkipped: false,
                maxBarThickness: horizontal ? 18 : 36,
            }],
        },
        options: {
            indexAxis: horizontal ? 'y' : 'x',
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: axes(horizontal),
        },
    }));
}

function renderDoughnut(canvas) {
    const { labels, values } = readData(canvas);

    register(new Chart(canvas, {
        type: 'doughnut',
        data: {
            labels,
            datasets: [{
                data: values,
                backgroundColor: labels.map((_, index) => COLORS[index % COLORS.length]),
                borderWidth: 3,
                hoverOffset: 6,
            }],
        },
        options: {
            maintainAspectRatio: false,
            cutout: '68%',
            plugins: {
                legend: { position: 'bottom', labels: { usePointStyle: true, pointStyle: 'circle', boxWidth: 8, padding: 16 } },
            },
        },
    }));
}

renderMonthly(document.getElementById('publications-per-month'));
renderDoughnut(document.getElementById('users-per-role-doughnut'));
renderBar(document.getElementById('users-per-role-bar'));
renderBar(document.getElementById('publications-per-category'), true);

// The theme switch in the user menu announces changes so the charts can repaint.
window.addEventListener('theme-changed', () => charts.forEach(applyTheme));
