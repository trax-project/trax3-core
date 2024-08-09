<script setup>
import StatementsLayout from "@starter/Layouts/StatementsLayout.vue";
import { Head } from "@inertiajs/vue3";
import StatementFilters from "@starter/Pages/Data/Filters/StatementFilters.vue";
import StatementResults from "@starter/Pages/Data/Results/StatementResults.vue";
import DataPanel from "@starter/Components/Misc/DataPanel.vue";
import Code from "@starter/Components/Misc/Code.vue";
import ScrollToTop from "@starter/Components/Misc/ScrollToTop.vue";
import { useLoadAndShowItems } from "@starter/Composables/LoadItems/LoadAndShowItems";
import { reactive } from "vue";
import { useStoreSelector } from "@starter/Composables/StoreSelector";

const { currentStore } = useStoreSelector();

const filters = reactive({
    agent: null,
    verb: "",
    activity: "",
    type: "",
    profile: "",
    since: "",
    until: "",
});

const options = reactive({
    agent_location: "everywhere",
    activity_location: "everywhere",
    ascending: false,
});

const {
    loadItems,
    loadMoreItems,
    loadingItems,
    loadedCounter,
    loadedItems,
    loadingErrors,
    hasMoreItems,
    showItem,
    itemPanelVisible,
    itemContent,
    itemContentType,
    itemIndex,
} = useLoadAndShowItems({
    url: "/trax/api/gateway/front/stores/" + currentStore() + "/statements",
    filters,
    baseParams: {
        filters: {
            voided: false,
        },
        options: {
            rearrange: true,
        },
    },
    settings: {
        sortProp: "stored",
        more: true,
    },
});

</script>

<template>
    <Head title="Statements" />

    <StatementsLayout side-menu-current="Statements">
        <DataPanel v-model:show="itemPanelVisible">
            <template v-slot:title> {{ itemIndex + 1 }} </template>
            <template v-slot:content>
                <Code :data="itemContent" :type="itemContentType" />
            </template>
        </DataPanel>

        <StatementFilters
            :filters="filters"
            :options="options"
            :counter="loadedCounter"
            :errors="loadingErrors"
            @apply="loadItems($event)"
        />

        <StatementResults
            :loadedItems="loadedItems"
            :hasMoreItems="hasMoreItems"
            :loadingItems="loadingItems"
            @loadMore="loadMoreItems"
            @show="showItem"
            class="mt-8"
            v-if="loadedItems.length"
        />

        <div class="text-gray-800 dark:text-white/90 text-center pt-12" v-else>
            No statement found!
        </div>

        <ScrollToTop />
    </StatementsLayout>
</template>
