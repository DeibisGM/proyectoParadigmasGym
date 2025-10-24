(function () {
    'use strict';

    const weeklyOverview = {
        front: {
            hombros: { label: 'Hombros', percentage: 35, intensity: 'medium' },
            pecho: { label: 'Pecho', percentage: 62, intensity: 'high' },
            abdomen: { label: 'Abdomen', percentage: 48, intensity: 'medium' },
            brazos: { label: 'Brazos', percentage: 54, intensity: 'high' },
            piernas: { label: 'Piernas', percentage: 40, intensity: 'medium' }
        },
        back: {
            trapecio: { label: 'Trapecio', percentage: 30, intensity: 'medium' },
            espalda: { label: 'Espalda alta', percentage: 55, intensity: 'high' },
            lumbar: { label: 'Zona lumbar', percentage: 28, intensity: 'low' },
            gluteos: { label: 'Glúteos', percentage: 46, intensity: 'medium' },
            isquiotibiales: { label: 'Isquiotibiales', percentage: 38, intensity: 'medium' },
            gemelos: { label: 'Gemelos', percentage: 22, intensity: 'low' }
        }
    };

    const intensityClasses = ['zone-intensity-low', 'zone-intensity-medium', 'zone-intensity-high'];

    function ready(callback) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', callback);
        } else {
            callback();
        }
    }

    function updateZoneElement(zoneElement, zoneData) {
        intensityClasses.forEach((cls) => zoneElement.classList.remove(cls));

        if (!zoneData) {
            zoneElement.setAttribute('aria-label', `${zoneElement.dataset.zone}`);
            zoneElement.dataset.percentage = '--';
            return;
        }

        zoneElement.dataset.percentage = zoneData.percentage;
        zoneElement.setAttribute('aria-label', `${zoneData.label}: ${zoneData.percentage}%`);
        zoneElement.classList.add(`zone-intensity-${zoneData.intensity}`);
    }

    function updateLegendLists(legendLists, view, data) {
        legendLists.forEach((list) => {
            const isCurrent = list.dataset.view === view;
            list.hidden = !isCurrent;

            list.querySelectorAll('[data-zone-value]').forEach((valueEl) => {
                intensityClasses.forEach((cls) => valueEl.classList.remove(cls));

                const zoneKey = valueEl.dataset.zoneValue;
                const zoneData = data[zoneKey];
                if (!zoneData) {
                    valueEl.textContent = '--%';
                    return;
                }

                valueEl.textContent = `${zoneData.percentage}%`;
                valueEl.classList.add(`zone-intensity-${zoneData.intensity}`);
            });
        });
    }

    function positionTooltip(tooltip, zoneElement) {
        const rect = zoneElement.getBoundingClientRect();
        const scrollY = window.scrollY || window.pageYOffset;
        const scrollX = window.scrollX || window.pageXOffset;

        tooltip.style.top = `${rect.top + scrollY - 12}px`;
        tooltip.style.left = `${rect.left + scrollX + rect.width / 2}px`;
    }

    ready(() => {
        const overview = document.querySelector('.body-overview');
        if (!overview) {
            return;
        }

        const content = overview.querySelector('.body-overview__content');
        const tooltip = overview.querySelector('.body-overview__tooltip');
        const toggleButtons = overview.querySelectorAll('.body-overview__toggle button');
        const legendLists = overview.querySelectorAll('.body-overview__legend-list');
        const zoneElements = overview.querySelectorAll('.body-overview__zone');

        function applyView(view) {
            const data = weeklyOverview[view];
            if (!data) {
                return;
            }

            content.dataset.currentView = view;

            zoneElements.forEach((zone) => {
                if (zone.dataset.view !== view) {
                    zone.setAttribute('aria-hidden', 'true');
                    return;
                }

                zone.removeAttribute('aria-hidden');
                updateZoneElement(zone, data[zone.dataset.zone]);
            });

            updateLegendLists(legendLists, view, data);
        }

        function showTooltip(zoneElement) {
            const view = zoneElement.dataset.view;
            const zoneKey = zoneElement.dataset.zone;
            const zoneData = weeklyOverview[view] ? weeklyOverview[view][zoneKey] : null;

            const label = zoneData ? zoneData.label : zoneKey;
            const percentage = zoneData ? `${zoneData.percentage}%` : '--%';
            tooltip.textContent = `${label}: ${percentage}`;
            positionTooltip(tooltip, zoneElement);
            tooltip.classList.add('is-visible');
        }

        function hideTooltip() {
            tooltip.classList.remove('is-visible');
        }

        toggleButtons.forEach((button) => {
            button.addEventListener('click', () => {
                const view = button.dataset.viewTarget;
                if (!view || content.dataset.currentView === view) {
                    return;
                }

                toggleButtons.forEach((btn) => btn.classList.toggle('is-active', btn === button));
                tooltip.classList.remove('is-visible');
                applyView(view);
            });
        });

        zoneElements.forEach((zone) => {
            zone.addEventListener('mouseenter', () => showTooltip(zone));
            zone.addEventListener('focus', () => showTooltip(zone));
            zone.addEventListener('mouseleave', hideTooltip);
            zone.addEventListener('blur', hideTooltip);
        });

        applyView(content.dataset.currentView || 'front');
    });
})();
