<script setup>
import FiltersBox from "@starter/Components/Forms/Filters/FiltersBox.vue";
import AgentFilter from "@starter/Components/Forms/XapiFilters/AgentFilterInStatements.vue";
import VerbFilter from "@starter/Components/Forms/XapiFilters/VerbFilter.vue";
import ActivityFilter from "@starter/Components/Forms/XapiFilters/ActivityFilterInStatements.vue";
import ActivityTypeFilter from "@starter/Components/Forms/XapiFilters/ActivityTypeFilterInStatements.vue";
import ProfileFilter from "@starter/Components/Forms/XapiFilters/XapiProfileFilter.vue";
import TimeFilters from "@starter/Components/Forms/XapiFilters/TimeFilters.vue";
import Checkbox from "@starter/Components/Forms/Filters/Checkbox.vue";
import PrimaryButton from "@starter/Components/Buttons/PrimaryButton.vue";
import LinkButton from "@starter/Components/Buttons/LinkButton.vue";
import MoreLessButton from "@starter/Components/Buttons/MoreLessButton.vue";
import { useFilterItems } from "@starter/Composables/LoadItems/FilterItems";
import { watch } from "vue";

const emit = defineEmits(["apply"]);

const props = defineProps({
    filters: {
        type: Object,
        required: true,
    },
    options: {
        type: Object,
        required: true,
    },
    counter: {
        type: Number,
        required: true,
    },
    errors: {
        type: Object,
        required: true,
    },
});

const {
    resetingFilters,
    resetFilters,
    applyingFilters,
    applyFilters,
    showMoreFilters,
    recordFilters,
} = useFilterItems(props, {
    localStorage: "statement-filters",
    emit,
});

watch(showMoreFilters, (show) => {
    recordFilters();
    if (!show) {
        Object.assign(props.filters, {
            type: "",
            profile: "",
            since: "",
            until: "",
        });
    }
});
</script>

<template>
    <FiltersBox>
        <AgentFilter
            v-model="filters.agent"
            v-model:location="options.agent_location"
            v-model:error="errors.agent"
            @apply="applyFilters"
        />
        <VerbFilter
            v-model="filters.verb"
            v-model:error="errors.verb"
            @apply="applyFilters"
        />
        <ActivityFilter
            v-model="filters.activity"
            v-model:location="options.activity_location"
            v-model:error="errors.activity"
            @apply="applyFilters"
        />
        <ActivityTypeFilter
            v-if="showMoreFilters"
            v-model="filters.type"
            v-model:error="errors.type"
            @apply="applyFilters"
        />
        <ProfileFilter
            v-if="showMoreFilters"
            v-model="filters.profile"
            v-model:error="errors.profile"
            @apply="applyFilters"
        />
        <TimeFilters
            v-if="showMoreFilters"
            v-model:since="filters.since"
            v-model:until="filters.until"
            :errors="errors"
            @apply="applyFilters"
        />
        <Checkbox
            id="ascending"
            label="Chronological"
            v-model="options.ascending"
        />

        <div class="relative text-right space-x-5">
            <LinkButton :processing="resetingFilters" @click="resetFilters"
                >Reset</LinkButton
            >
            <PrimaryButton :processing="applyingFilters" @click="applyFilters"
                >Apply</PrimaryButton
            >
        </div>

        <div class="relative text-center md:col-span-2 -mt-5">
            <MoreLessButton
                v-model="showMoreFilters"
                moreLabel="More filters"
                lessLabel="Less filters"
            />
        </div>
    </FiltersBox>
</template>
