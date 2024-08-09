import { ref } from "vue";
import { useLoadItems } from "@starter/Composables/LoadItems/LoadItems";

export function useLoadAndShowItems(endpoint) {
    
    // Manage item display panel.
    const itemPanelVisible = ref(false);
    const itemContent = ref(null);
    const itemContentType = ref(null);
    const itemIndex = ref(null);
    const itemProps = ref(null);

    const showItem = (item) => {
        itemPanelVisible.value = true;
        itemContent.value = item.content;
        itemContentType.value = item.content_type;
        itemIndex.value = item.index;
        itemProps.value = item.props;
    };

    // Manage loading.
    const {
        loadItems,
        loadMoreItems,
        loadedCounter,
        loadedItems,
        loadingErrors,
        hasMoreItems,
        loadingItems,
    } = useLoadItems(endpoint);

    return {
        showItem,
        itemPanelVisible,
        itemContent,
        itemContentType,
        itemIndex,
        itemProps,

        loadItems,
        loadMoreItems,
        loadedCounter,
        loadedItems,
        loadingErrors,
        hasMoreItems,
        loadingItems,
    };
}
