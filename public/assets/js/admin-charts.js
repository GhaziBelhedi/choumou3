/**
 * admin-charts.js — initialise les graphiques Chart.js du dashboard admin
 * à partir de window.dashboardData (injecté en inline script par la vue).
 */

document.addEventListener('DOMContentLoaded', function () {
    if (typeof Chart === 'undefined' || !window.dashboardData) return;

    var palette = {
        primary: '#E63946',
        primaryLight: 'rgba(230, 57, 70, 0.12)',
        gold: '#FFB703',
        accent: '#06B6A8',
        ink: '#1A1523',
        inkFaint: '#8B8598',
        border: '#E9E1D3',
    };

    Chart.defaults.font.family = "'Inter', -apple-system, sans-serif";
    Chart.defaults.color = palette.inkFaint;

    initRevenueChart(palette);
    initStatusChart(palette);
    initTypeChart(palette);
});

function initRevenueChart(palette) {
    var canvas = document.getElementById('revenueChart');
    if (!canvas) return;

    var data = window.dashboardData.revenueByDay || [];

    new Chart(canvas, {
        type: 'line',
        data: {
            labels: data.map(function (d) { return d.label; }),
            datasets: [{
                label: 'Chiffre d\'affaires (DT)',
                data: data.map(function (d) { return d.value; }),
                borderColor: palette.primary,
                backgroundColor: palette.primaryLight,
                borderWidth: 3,
                fill: true,
                tension: 0.35,
                pointRadius: 3,
                pointBackgroundColor: palette.primary,
                pointHoverRadius: 6,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: palette.ink,
                    padding: 10,
                    cornerRadius: 8,
                    callbacks: {
                        label: function (ctx) { return ctx.parsed.y.toFixed(2) + ' DT'; }
                    }
                }
            },
            scales: {
                y: { beginAtZero: true, grid: { color: palette.border }, ticks: { callback: function (v) { return v + ' DT'; } } },
                x: { grid: { display: false } }
            }
        }
    });
}

function initStatusChart(palette) {
    var canvas = document.getElementById('statusChart');
    if (!canvas) return;

    var data = window.dashboardData.ordersByStatus || [];
    var colors = [palette.gold, palette.primary, palette.accent, '#3B82F6', palette.ink, '#94A3B8'];

    if (!data.length) {
        canvas.parentElement.innerHTML = '<p class="text-faint" style="text-align:center;padding:var(--space-8) 0">Aucune commande pour l\'instant.</p>';
        return;
    }

    new Chart(canvas, {
        type: 'doughnut',
        data: {
            labels: data.map(function (d) { return d.label; }),
            datasets: [{
                data: data.map(function (d) { return d.value; }),
                backgroundColor: colors,
                borderColor: '#fff',
                borderWidth: 2,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: {
                legend: { position: 'bottom', labels: { boxWidth: 10, padding: 12, font: { size: 11 } } }
            }
        }
    });
}

function initTypeChart(palette) {
    var canvas = document.getElementById('typeChart');
    if (!canvas) return;

    var raw = window.dashboardData.revenueByType || { livre: 0, fourniture: 0 };

    if (!raw.livre && !raw.fourniture) {
        canvas.parentElement.innerHTML = '<p class="text-faint" style="text-align:center;padding:var(--space-8) 0">Pas encore de ventes.</p>';
        return;
    }

    new Chart(canvas, {
        type: 'doughnut',
        data: {
            labels: ['Livres', 'Fournitures scolaires'],
            datasets: [{
                data: [raw.livre, raw.fourniture],
                backgroundColor: [palette.primary, palette.accent],
                borderColor: '#fff',
                borderWidth: 2,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: {
                legend: { position: 'bottom', labels: { boxWidth: 10, padding: 12 } },
                tooltip: {
                    callbacks: {
                        label: function (ctx) { return ctx.label + ' : ' + ctx.parsed.toFixed(2) + ' DT'; }
                    }
                }
            }
        }
    });
}
