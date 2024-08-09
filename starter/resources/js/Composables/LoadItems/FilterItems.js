import { ref, watch, onMounted } from "vue";

export function useFilterItems(status, settings) {
    const applyingFilters = ref(false);
    const resetingFilters = ref(false);
    const showMoreFilters = ref(false);
    const defaultFilters = { ...status.filters };
    const defaultOptions = { ...status.options };

    onMounted(() => {
        initFilters();
    });

    const initFilters = () => {
        // Try to restore filters.
        if (settings.localStorage) {
            let savedFilter = localStorage.getItem(settings.localStorage);
            if (savedFilter) {
                savedFilter = JSON.parse(savedFilter);
                showMoreFilters.value = savedFilter.settings.showMoreFilters;
                Object.assign(status.filters, savedFilter.filters);
                Object.assign(status.options, savedFilter.options);
                applyFilters(false);
                return;
            }
        }
        // Assign default values.
        applyFilters(false);
    };

    const resetFilters = () => {
        if (settings.resetFilters !== undefined) {
            settings.resetFilters();
        } else {
            Object.assign(status.filters, defaultFilters);
            Object.assign(status.options, defaultOptions);
        }
        applyFilters();
        applyingFilters.value = false;
        resetingFilters.value = true;
    };

    const applyFilters = (record = true) => {

        // Sanitize.
        const sanitized = { filters: {}, options: {} };
        Object.keys(status.filters).forEach((prop) => {
            sanitize(status.filters, sanitized.filters, prop);
        });
        Object.keys(status.options).forEach((prop) => {
            sanitize(status.options, sanitized.options, prop);
        });

        // Record the filters.
        if (record) {
            recordFilters();
        }

        // Send the request.
        applyingFilters.value = true;
        
        if (settings.emit) {
            settings.emit("apply", sanitized);
        }
        
        if (settings.callback) {
            settings.callback(sanitized);
        }
    };

    const sanitize = (source, target, prop) => {
        let val = source[prop];
        if (val === undefined || val === null) {
            return;
        }
        if (typeof val === "string" && !val.trim()) {
            source[prop] = "";
        }
        if (typeof val === "string" && val.trim()) {
            target[prop] = val.trim();
        }
        if (typeof val === "boolean" || typeof val === "number") {
            target[prop] = val;
        }
        if (typeof val === "object") {
            target[prop] = JSON.stringify(val);
        }
    };

    const recordFilters = () => {
        if (settings.localStorage) {
            localStorage.setItem(
                settings.localStorage,
                JSON.stringify({
                    filters: status.filters,
                    options: status.options,
                    settings: { showMoreFilters: showMoreFilters.value },
                })
            );
        }
    };

    watch(
        () => status.counter,
        () => {
            applyingFilters.value = false;
            resetingFilters.value = false;
        }
    );

    return {
        resetingFilters,
        resetFilters,
        applyingFilters,
        applyFilters,
        showMoreFilters,
        recordFilters,
    };
}
