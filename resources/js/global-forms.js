import $ from "jquery";

window.$ = $;
window.jQuery = $;

import select2 from "select2";
import "select2/dist/css/select2.min.css";

$.fn.select2 = select2;
window.Select2 = select2;

const Select2 = window.Select2;

function isDarkMode() {
    return document.documentElement.classList.contains('dark') || 
           document.body.classList.contains('dark') ||
           localStorage.getItem('theme') === 'dark' ||
           window.matchMedia('(prefers-color-scheme: dark)').matches;
}

function initSelect2() {
    const selects = document.querySelectorAll("select:not(.no-select2)");

    selects.forEach((select) => {
        if (
            select.classList.contains("select2-hidden-accessible") ||
            select.closest(".select2-container")
        ) {
            return;
        }

        const isMultiple = select.multiple;
        const allowClear =
            select.dataset.allowClear === "true" ||
            select.classList.contains("allow-clear");
        const placeholder = select.dataset.placeholder || "Select an option";
        const width = select.dataset.selectWidth || "100%";
        const minimumResultsForSearch = select.dataset.search === "false" ? -1 : 10;
        const dark = isDarkMode();

        try {
            $(select).select2({
                multiple: isMultiple,
                allowClear: allowClear,
                placeholder: placeholder,
                width: width,
                minimumResultsForSearch: minimumResultsForSearch,
                dropdownPosition: "below",
                escapeMarkup: function (markup) {
                    return markup;
                },
                templateResult: function (data) {
                    if (!data.id) return data.text;
                    return $(
                        '<span class="flex items-center py-1">' + data.text + "</span>"
                    );
                },
                templateSelection: function (data) {
                    if (!data.id) return data.text;
                    return $(
                        '<span class="flex items-center py-0.5">' + data.text + "</span>"
                    );
                },
            });
            
            $(select).next('.select2-container').find('.select2-selection').addClass('rounded-xl');
            
            if (dark) {
                $(select).next('.select2-container').find('.select2-selection').addClass('dark-mode');
                $(select).on('select2:open', function() {
                    document.querySelector('.select2-results__options')?.classList.add('dark-mode');
                });
            }
        } catch (e) {
            console.warn("Select2 initialization failed:", e);
        }
    });
}

function reinitSelect2() {
    const selects = document.querySelectorAll("select:not(.no-select2)");
    selects.forEach((select) => {
        if (
            select.classList.contains("select2-hidden-accessible") ||
            select.closest(".select2-container")
        ) {
            return;
        }
        
        const isMultiple = select.multiple;
        const allowClear =
            select.dataset.allowClear === "true" ||
            select.classList.contains("allow-clear");
        const placeholder = select.dataset.placeholder || "Select an option";
        const width = select.dataset.selectWidth || "100%";

        try {
            $(select).select2({
                multiple: isMultiple,
                allowClear: allowClear,
                placeholder: placeholder,
                width: width,
                minimumResultsForSearch: 10,
                dropdownPosition: "below",
            });
        } catch (e) {
            console.warn("Select2 initialization failed:", e);
        }
    });
}

window.initSelect2 = initSelect2;
window.reinitSelect2 = reinitSelect2;

export {
    initSelect2,
    reinitSelect2,
};
