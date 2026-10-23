import Chart from 'chart.js/auto';

const COLORS = ['255, 99, 132', '54, 162, 235', '255, 206, 86', '75, 192, 192', '153, 102, 255'];

/**
 * Draws a chart of users per role inside the given canvas.
 *
 * @param {HTMLCanvasElement} canvas Canvas with `data-labels` and `data-values` JSON attributes.
 * @param {'bar'|'doughnut'} type Chart.js chart type.
 */
function renderRoleChart(canvas, type) {
    const labels = JSON.parse(canvas.dataset.labels);
    const values = JSON.parse(canvas.dataset.values);
    const colors = labels.map((_, index) => COLORS[index % COLORS.length]);

    new Chart(canvas, {
        type,
        data: {
            labels,
            datasets: [{
                label: 'Cantidad de usuarios',
                data: values,
                backgroundColor: colors.map((rgb) => `rgba(${rgb}, 0.2)`),
                borderColor: colors.map((rgb) => `rgba(${rgb}, 1)`),
                borderWidth: 1,
            }],
        },
        options: {
            maintainAspectRatio: false,
        },
    });
}

renderRoleChart(document.getElementById('users-per-role-bar'), 'bar');
renderRoleChart(document.getElementById('users-per-role-doughnut'), 'doughnut');
