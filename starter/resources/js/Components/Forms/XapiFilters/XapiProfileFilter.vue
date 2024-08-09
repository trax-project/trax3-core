<script setup>
import InputLabel from "@starter/Components/Forms/Filters/InputLabel.vue";
import TextInput from "@starter/Components/Forms/Filters/TextInput.vue";
import { useStoreSelector } from "@starter/Composables/StoreSelector";

const { currentStore } = useStoreSelector();

const props = defineProps({
    modelValue: {
        type: String,
        required: true,
    },
    error: {
        type: String,
        required: false,
    },
});

defineEmits(["update:modelValue", "update:error", "apply"]);

const completionEndpoint = "/trax/api/gateway/front/stores/" + currentStore() + "/activities";
const completionProp = "iri";
const completionParams = (search) => {
    return {
        sort: ["iri"],
        limit: 10,
        filters: {
            in_iri: search,
            is_category: 1,
        },
    };
};
</script>

<template>
    <div class="relative md:col-span-2 flex flex-row">
        <InputLabel for="profile" value="xAPI Profile" />
        <TextInput
            id="profile"
            placeholder="https://w3id.org/xapi/cmi5/context/categories/cmi5"
            :modelValue="modelValue"
            :error="error"
            @update:modelValue="$emit('update:modelValue', $event)"
            @update:error="$emit('update:error', $event)"
            :completionEndpoint="completionEndpoint"
            :completionProp="completionProp"
            :completionParams="completionParams"
        />
    </div>
</template>
