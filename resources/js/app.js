import './components/image-viewer.js';
import '../css/dashboard.css';

const formatNumber = (value) =>
    new Intl.NumberFormat('es-CL', {
        maximumFractionDigits: 0,
    }).format(value ?? 0);

const findEquiposEstadoChart = () => {
    if (!Array.isArray(window.__dashboardCharts)) {
        return null;
    }

    return window.__dashboardCharts.find((chart) => chart?.canvas?.id === 'chart-equipos-estado') ?? null;
};

const updateEquiposEstadoChart = (payload, attempts = 0) => {
    const chart = findEquiposEstadoChart();
    if (!chart) {
        if (attempts < 5) {
            setTimeout(() => updateEquiposEstadoChart(payload, attempts + 1), 200);
        }
        return;
    }

    chart.data.labels = payload.labels;
    chart.data.datasets[0].data = payload.values;
    chart.data.datasets[0].backgroundColor = payload.colors;
    chart.update();
};

const bindEquiposEstadoFilter = () => {
    const card = document.getElementById('equipos-estado-card');
    if (!card || card.dataset.chartBound === 'true') {
        return;
    }

    const endpoint = card.dataset.chartUrl;
    const select = card.querySelector('[data-chart-filter-select]');
    const resetButton = card.querySelector('#equipos-estado-reset');
    const totalEl = card.querySelector('#equipos-estado-total');
    const container = card.querySelector('[data-chart-container]');
    const emptyMessage = card.querySelector('#equipos-estado-empty');
    const clienteText = card.querySelector('#equipos-estado-cliente-text');
    const estadosList = card.querySelectorAll('#equipos-estado-lista [data-estado]');

    if (!endpoint || !select || !totalEl || !container || !emptyMessage) {
        return;
    }

    const setResetVisibility = () => {
        if (!resetButton) {
            return;
        }
        resetButton.classList.toggle('hidden', !select.value);
    };

    const toggleLoading = (loading) => {
        card.classList.toggle('opacity-60', loading);
        card.classList.toggle('pointer-events-none', loading);
    };

    const updateDetails = (payload) => {
        totalEl.textContent = formatNumber(payload.total ?? 0);
        estadosList.forEach((item) => {
            const estado = item.dataset.estado;
            const index = payload.keys.indexOf(estado);
            const value = index !== -1 ? payload.values[index] : 0;
            const target = item.querySelector('.equipos-estado-valor');
            if (target) {
                target.textContent = formatNumber(value);
            }
        });

        const tieneDatos = (payload.total ?? 0) > 0;
        container.classList.toggle('hidden', !tieneDatos);
        emptyMessage.classList.toggle('hidden', tieneDatos);

        if (clienteText) {
            clienteText.textContent = payload.cliente
                ? `Cliente seleccionado: ${payload.cliente}`
                : 'Mostrando todos los clientes';
        }
    };

    const fetchData = async () => {
        toggleLoading(true);
        try {
            const params = new URLSearchParams();
            if (select.value) {
                params.set('cliente_id', select.value);
            }

            const url = params.toString() ? `${endpoint}?${params}` : endpoint;
            const response = await fetch(url, {
                headers: {
                    Accept: 'application/json',
                },
            });

            if (!response.ok) {
                throw new Error('No se pudo actualizar el gráfico.');
            }

            const payload = await response.json();
            updateEquiposEstadoChart(payload);
            updateDetails(payload);
        } catch (error) {
            console.error(error);
        } finally {
            toggleLoading(false);
        }
    };

    select.addEventListener('change', () => {
        setResetVisibility();
        fetchData();
    });

    if (resetButton) {
        resetButton.addEventListener('click', () => {
            if (!select.value) {
                return;
            }
            select.value = '';
            setResetVisibility();
            fetchData();
        });
    }

    setResetVisibility();
    card.dataset.chartBound = 'true';
};

const bootDashboardEnhancements = () => {
    bindEquiposEstadoFilter();
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', bootDashboardEnhancements);
} else {
    bootDashboardEnhancements();
}

document.addEventListener('livewire:navigated', () => {
    setTimeout(bootDashboardEnhancements, 0);
});
