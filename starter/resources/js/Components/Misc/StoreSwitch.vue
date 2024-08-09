<script setup>
import Select from "@starter/Components/Forms/Filters/Select.vue";
import { useStoreSelector } from "@starter/Composables/StoreSelector";

const {
    currentStore,
    selectStore,
    storesChoice,
} = useStoreSelector();

</script>

<template>
    <div class="pb-5" style="max-width:200px" v-if="storesChoice().length > 1">
        <Select
            id="stores"
            :options="storesChoice()"
            :modelValue="currentStore()"
            :lines="true"
            @update:modelValue="selectStore($event)"
        />
    </div>
</template>
