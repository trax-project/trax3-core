<script setup>
import Prism from "prismjs";
import { computed, onMounted } from "vue";

const props = defineProps({
    data: {
        type: [String, Object, Array],
        required: true,
    },
    type: {
        type: String,
        default: "application/json",
    },
    language: {
        type: String,
        default: "javascript",
    },
});

onMounted(() => {
    window.Prism = window.Prism || {};
    window.Prism.manual = true;
    Prism.highlightAll();
});

const jsonContent = computed(() => JSON.stringify(props.data, null, 4));

const textContent = computed(() =>
    props.data.replace(/(?:\r\n|\r|\n)/g, "<br>")
);
</script>

<template>
    <pre
        v-if="type == 'application/json'"
    ><code :class="'language-'+language" v-html="jsonContent"></code></pre>
    <code
        v-else-if="type == 'text/plain'"
        class="text-gray-600 dark:text-gray-300 text-sm"
        v-html="textContent"
    />
    <code class="text-gray-600 dark:text-gray-300 text-sm" v-else>
        {{ data }}
    </code>
</template>

<style scoped>
code[class*="language-"],
pre[class*="language-"] {
    color: black;
    background: none;
    font-family: Consolas, Monaco, "Andale Mono", "Ubuntu Mono", monospace;
    font-size: 0.95em;
    text-align: left;
    white-space: pre;
    word-spacing: normal;
    word-break: normal;
    word-wrap: normal;
    line-height: 1.5;

    -moz-tab-size: 4;
    -o-tab-size: 4;
    tab-size: 4;

    -webkit-hyphens: none;
    -moz-hyphens: none;
    -ms-hyphens: none;
    hyphens: none;
}

code :deep(.token.punctuation) {
    color: #9ca3af;
}

code :deep(.token.property) {
    color: #000;
}

code :deep(.token.tag),
code :deep(.token.boolean),
code :deep(.token.number),
code :deep(.token.constant),
code :deep(.token.symbol),
code :deep(.token.keyword),
code :deep(.token.deleted) {
    color: #d946ef;
}

code :deep(.token.selector),
code :deep(.token.attr-name),
code :deep(.token.string),
code :deep(.token.char),
code :deep(.token.builtin),
code :deep(.token.inserted) {
    color: #06b6d4;
}

code :deep(.token.operator),
code :deep(.token.entity),
code :deep(.token.url),
code :deep(.language-css .token.string),
code :deep(.style .token.string) {
    color: #fb923c;
}

.dark code :deep(.token.punctuation) {
    color: #6b7280;
}

.dark code :deep(.token.property) {
    color: #fff;
}

.dark code :deep(.token.tag),
.dark code :deep(.token.boolean),
.dark code :deep(.token.number),
.dark code :deep(.token.constant),
.dark code :deep(.token.symbol),
.dark code :deep(.token.deleted) {
    color: #e879f9;
}

.dark code :deep(.token.selector),
.dark code :deep(.token.attr-name),
.dark code :deep(.token.string),
.dark code :deep(.token.char),
.dark code :deep(.token.builtin),
.dark code :deep(.token.inserted) {
    color: #22d3ee;
}
</style>
