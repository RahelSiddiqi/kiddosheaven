import Alpine from "alpinejs";
import ApexCharts from "apexcharts";
import "./bootstrap";
import {
    initSelect2,
    reinitSelect2,
} from "./global-forms";


// FullCalendar
import { Calendar } from "@fullcalendar/core";

window.Alpine = Alpine;
window.ApexCharts = ApexCharts;
window.FullCalendar = Calendar;

// Helper function to reload page after a delay
window.sleepReload = function (ms) {
    return new Promise((resolve) => setTimeout(resolve, ms)).then(() =>
        window.location.reload(),
    );
};

Alpine.start();

// Initialize components on DOM ready
document.addEventListener("DOMContentLoaded", () => {
    // Initialize Select2 for all select elements
    initSelect2();

    // Watch for dynamically added select elements
    const contentObserver = new MutationObserver((mutations) => {
        let shouldReinitSelect = false;

        mutations.forEach((mutation) => {
            mutation.addedNodes.forEach((node) => {
                if (node.nodeType === 1) { // Element node
                    if (node.tagName === 'SELECT' || (node.querySelector && node.querySelector('select:not(.no-select2)'))) {
                        shouldReinitSelect = true;
                    }
                }
            });
        });

        if (shouldReinitSelect) {
            setTimeout(() => initSelect2(), 100);
        }
    });

    contentObserver.observe(document.body, {
        childList: true,
        subtree: true
    });

    // Map imports
    if (document.querySelector("#mapOne")) {
        import("./components/map").then((module) => module.initMap());
    }

    // Chart imports
    if (document.querySelector("#chartOne")) {
        import("./components/chart/chart-1").then((module) =>
            module.initChartOne(),
        );
    }
    if (document.querySelector("#chartTwo")) {
        import("./components/chart/chart-2").then((module) =>
            module.initChartTwo(),
        );
    }
    if (document.querySelector("#chartThree")) {
        import("./components/chart/chart-3").then((module) =>
            module.initChartThree(),
        );
    }
    if (document.querySelector("#chartSix")) {
        import("./components/chart/chart-6").then((module) =>
            module.initChartSix(),
        );
    }
    if (document.querySelector("#chartEight")) {
        import("./components/chart/chart-8").then((module) =>
            module.initChartEight(),
        );
    }
    if (document.querySelector("#chartThirteen")) {
        import("./components/chart/chart-13").then((module) =>
            module.initChartThirteen(),
        );
    }

    // Calendar init
    if (document.querySelector("#calendar")) {
        import("./components/calendar-init").then((module) =>
            module.calendarInit(),
        );
    }
});

// Expose reinit functions globally for dynamic content
window.addEventListener("livewire:loaded", () => {
    initSelect2();
});

window.addEventListener("alpine:initialized", () => {
    initSelect2();
});
