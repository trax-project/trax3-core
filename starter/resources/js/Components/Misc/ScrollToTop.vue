<script setup>
import { ChevronUpIcon } from "@heroicons/vue/20/solid";
import { ref, onMounted } from "vue";

const showButton = ref(false);

const scrollToTop = () => {
    window.scrollTo({
        top: 0,
        behavior: "smooth",
    });
};

onMounted(() => {
    window.onscroll = () => {
        showButton.value = document.documentElement.scrollTop > 150;
    };
});
</script>

<template>
    <button
        type="button"
        :class="[
            showButton ? 'visible' : 'invisible',
            'fixed bottom-5 right-5 rounded-full focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 bg-indigo-600 p-2 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline-indigo-600',
        ]"
        @click="scrollToTop"
    >
        <ChevronUpIcon class="h-5 w-5" aria-hidden="true" />
    </button>
</template>
