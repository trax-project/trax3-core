<script setup>
import TextInput from "@starter/Components/Forms/Filters/TextInput.vue";
import { useStoreSelector } from "@starter/Composables/StoreSelector";

const { currentStore } = useStoreSelector();

const completionEndpoint = "/trax/api/gateway/front/stores/" + currentStore() + "/activity-ids";
const completionProp = "iri";
const completionParams = (search) => {
    return {
        sort: ["iri"],
        limit: 10,
        filters: {
            in_iri: search,
        },
    };
};
</script>

<template>
    <TextInput
        id="activity"
        placeholder="http://my.lms.com/courses/xapi-101"
        :completionEndpoint="completionEndpoint"
        :completionProp="completionProp"
        :completionParams="completionParams"
    />
</template>
