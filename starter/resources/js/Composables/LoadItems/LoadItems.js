import { ref, reactive } from "vue";

export function useLoadItems(
    endpoint,       // url prop required. Filters, baseParams & settings optional.
    hooks = {},          // afterItemLoading & afterItemsLoading supported
) {
    // Manage loading.
    const loadedCounter = ref(0);
    const loadedItems = ref([]);
    const hasMoreItems = ref(false);
    const loadingItems = ref(false);

    const loadingErrors = endpoint.filters
        ? reactive({ ...endpoint.filters })
        : reactive({});

    const limit = endpoint.baseParams && endpoint.baseParams.limit
        ? endpoint.baseParams.limit
        : 20;

    let lastFiltering = { options: {}, filters: {} };

    const loadItems = (filtersAndOptions) => {
        if (filtersAndOptions !== undefined) {
            lastFiltering = filtersAndOptions;
        }
        privateLoadItems();
    };

    const loadMoreItems = () => {
        privateLoadItems(true);
    };

    const privateLoadItems = (more = false) => {
        loadingItems.value = true;

        let apiEndpoint =
            typeof endpoint.url === "function" ? endpoint.url() : endpoint.url;

        axios
            .get(apiEndpoint, {
                params: determineLoadingParams(more),
            })
            .then((resp) => {
                loadingItems.value = false;
                loadedCounter.value++;
                const lastItems = resp.data.data.map((item) => {
                    return hooks.afterItemLoading ? hooks.afterItemLoading(item) : item;
                });
                if (more) {
                    loadedItems.value.push(...lastItems);
                } else {
                    loadedItems.value = lastItems;
                }
                hasMoreItems.value = determineHasMoreItems(resp.data.data);
                if (hooks.afterItemsLoading) {
                    hooks.afterItemsLoading(loadedItems.value)
                }
            })
            .catch((error) => {
                loadingItems.value = false;
                loadedCounter.value++;
                if (error.response && error.response.status == 422) {
                    updateLoadingErrors(error.response.data);
                }
            });
    };

    const updateLoadingErrors = (feedback) => {
        Object.keys(feedback.errors).forEach((prop) => {
            loadingErrors[prop] = feedback.errors[prop][0];
        });
    };

    const determineLoadingParams = (forMore) => {
        // Base.
        // Keep it like this. Don't destructure the baseParams directly to avoid filters and options contamination.
        let params = {
            limit,
            filters: {},
            options: {},
        };
        if (endpoint.baseParams && endpoint.baseParams.filters) {
            params.filters = { ...endpoint.baseParams.filters };
        }
        if (endpoint.baseParams && endpoint.baseParams.options) {
            params.options = { ...endpoint.baseParams.options };
        }

        // Sorting.
        if (endpoint.settings && endpoint.settings.sortProp) {
            // By default, sorting is descending, except if the settings.ascending prop is set to true.
            params.sort = lastFiltering.options.ascending || endpoint.settings.ascending
                ? [endpoint.settings.sortProp]
                : ["-" + endpoint.settings.sortProp];
        }

        // More.
        if (forMore && endpoint.settings && endpoint.settings.more) {
            let moreParam = {};
            moreParam[endpoint.settings.sortProp] = loadedItems.value.slice(-1).pop()[
                endpoint.settings.sortProp
            ];
            // By default, sorting is descending, except if the settings.ascending prop is set to true.
            if (lastFiltering.options.ascending || endpoint.settings.ascending) {
                params.after = moreParam;
            } else {
                params.before = moreParam;
            }
        }

        // Assign filters and params.
        params.filters = Object.assign(params.filters, lastFiltering.filters);
        params.options = Object.assign(params.options, lastFiltering.options);
        delete params.options.ascending;
        return params;
    };

    const determineHasMoreItems = (newItems) => {
        return (
            endpoint.settings &&
            endpoint.settings.more &&
            (lastFiltering.options.ascending || newItems.length == limit)
        );
    };

    return {
        loadItems,
        loadMoreItems,
        loadedCounter,
        loadedItems,
        loadingErrors,
        hasMoreItems,
        loadingItems,
    };
}
