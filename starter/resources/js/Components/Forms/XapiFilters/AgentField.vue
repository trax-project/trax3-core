<script setup>
import FieldOptions from "@starter/Components/Forms/Filters/FieldOptions.vue";
import TextInput from "@starter/Components/Forms/Filters/TextInput.vue";
import { ref, computed, watch } from "vue";
import { useStoreSelector } from "@starter/Composables/StoreSelector";

const { currentStore } = useStoreSelector();

const props = defineProps({
    modelValue: {
        type: Object,
        required: false,
    },
    rounded: {
        type: Boolean,
        default: true,
    },
    error: {
        type: String,
        required: false,
    },
    showError: {
        type: Boolean,
        default: true,
    },
});

const emit = defineEmits(["update:modelValue", "update:error", "apply"]);

const typeOptions = [
    { id: "mbox", name: "Mbox" },
    { id: "openid", name: "OpenID" },
    { id: "account", name: "Account" },
];

const localType = ref("mbox");
const localAccountName = ref("");
const localAccountHomepage = ref("");

watch(
    () => props.modelValue,
    (val) => {
        if (val === null) {
            localAccountName.value = "";
            localAccountHomepage.value = "";
        }
    }
);

const type = computed({
    get() {
        if (!props.modelValue) {
            return localType.value;
        }
        if (props.modelValue.mbox) {
            localType.value = "mbox";
            return "mbox";
        }
        if (props.modelValue.openid) {
            localType.value = "openid";
            return "openid";
        }
        if (props.modelValue.account) {
            localType.value = "account";
            return "account";
        }
    },
    set(val) {
        if (localType.value == val) {
            return;
        }
        localType.value = val;
        localAccountName.value = "";
        localAccountHomepage.value = "";
        emit("update:modelValue", null);
    },
});

const mbox = computed({
    get() {
        return props.modelValue && props.modelValue.mbox
            ? props.modelValue.mbox.substring(7)
            : "";
    },
    set(val) {
        if (val.trim()) {
            emit("update:modelValue", { mbox: "mailto:" + val.trim() });
        } else {
            emit("update:modelValue", null);
        }
    },
});

const openid = computed({
    get() {
        return props.modelValue && props.modelValue.openid
            ? props.modelValue.openid
            : "";
    },
    set(val) {
        if (val.trim()) {
            emit("update:modelValue", { openid: val.trim() });
        } else {
            emit("update:modelValue", null);
        }
    },
});

const accountName = computed({
    get() {
        return props.modelValue && props.modelValue.account
            ? props.modelValue.account.name
            : localAccountName.value;
    },
    set(val) {
        localAccountName.value = val;
        if (val.trim() || localAccountHomepage.value.trim()) {
            emit("update:modelValue", {
                account: {
                    name: val.trim(),
                    homePage: localAccountHomepage.value.trim(),
                },
            });
        } else {
            emit("update:modelValue", null);
        }
    },
});

const accountHomepage = computed({
    get() {
        return props.modelValue && props.modelValue.account
            ? props.modelValue.account.homePage
            : localAccountHomepage.value;
    },
    set(val) {
        localAccountHomepage.value = val;
        if (val.trim() || localAccountName.value.trim()) {
            emit("update:modelValue", {
                account: {
                    name: localAccountName.value.trim(),
                    homePage: val.trim(),
                },
            });
        } else {
            emit("update:modelValue", null);
        }
    },
});

const completionEndpoint = "/trax/api/gateway/front/stores/" + currentStore() + "/agent-ids";
const completionParams = (search, id) => {
    if (id == "agent") {
        return {
            sort: ["sid_field_1"],
            limit: 10,
            filters: {
                sid_type: localType.value,
                in_sid_field_1: search,
            },
            distinct: ["sid_field_1"],
        };
    } else {
        return {
            sort: ["sid_field_2"],
            limit: 10,
            filters: {
                sid_type: localType.value,
                in_sid_field_2: search,
            },
            distinct: ["sid_field_2"],
        };
    }
};
const completionFormatProposal = (proposal, id) => {
    if (localType.value == "mbox") {
        return proposal.sid_field_1.split("mailto:")[1];
    }
    return id == "agent" ? proposal.sid_field_1 : proposal.sid_field_2;
};
const completionFormatSearch = (search) => {
    return search;
};
</script>

<template>
    <FieldOptions id="agent-type" :options="typeOptions" v-model="type" />

    <div class="relative flex flex-grow items-stretch">
        <TextInput
            v-if="type === 'mbox'"
            id="agent"
            prefix="mailto:"
            placeholder="agent@email.test"
            :class="[rounded ? 'rounded-none rounded-r' : 'rounded-none']"
            v-model="mbox"
            :error="error"
            :showError="showError"
            @update:error="$emit('update:error', $event)"
            @apply="$emit('apply')"
            :completionEndpoint="completionEndpoint"
            :completionParams="completionParams"
            :completionFormatProposal="completionFormatProposal"
        />
        <TextInput
            v-if="type === 'openid'"
            id="agent"
            placeholder="http://openid"
            :class="[rounded ? 'rounded-none rounded-r' : 'rounded-none']"
            v-model="openid"
            :error="error"
            :showError="showError"
            @update:error="$emit('update:error', $event)"
            @apply="$emit('apply')"
            :completionEndpoint="completionEndpoint"
            :completionParams="completionParams"
            :completionFormatProposal="completionFormatProposal"
        />
        <TextInput
            v-if="type === 'account'"
            id="agent"
            placeholder="name"
            class="rounded-none"
            v-model="accountName"
            :error="error"
            :showError="showError"
            @update:error="$emit('update:error', $event)"
            @apply="$emit('apply')"
            :completionEndpoint="completionEndpoint"
            :completionParams="completionParams"
            :completionFormatProposal="completionFormatProposal"
        />
        <TextInput
            v-if="type === 'account'"
            id="agent_homepage"
            placeholder="http://homepage"
            :class="[rounded ? 'rounded-none rounded-r' : 'rounded-none']"
            v-model="accountHomepage"
            :error="error"
            :showError="showError"
            @update:error="$emit('update:error', $event)"
            @apply="$emit('apply')"
            :completionEndpoint="completionEndpoint"
            :completionParams="completionParams"
            :completionFormatProposal="completionFormatProposal"
        />
    </div>
</template>
