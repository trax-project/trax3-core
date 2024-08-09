<script setup>
import { Menu, MenuButton, MenuItem, MenuItems } from "@headlessui/vue";

const props = defineProps({
    options: {
        type: Array,
        required: true,
    },
});

const emit = defineEmits(["click"]);
</script>

<template>
    <Menu as="div" class="relative">
        <div>
            <MenuButton>
                <slot />
            </MenuButton>
        </div>
        <transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="transform opacity-0 scale-95"
            enter-to-class="transform opacity-100 scale-100"
            leave-active-class="transition ease-in duration-75"
            leave-from-class="transform opacity-100 scale-100"
            leave-to-class="transform opacity-0 scale-95"
        >
            <MenuItems
                class="absolute top-auto right-0 z-10 w-48 mt-2 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
            >
                <MenuItem
                    v-for="item in options"
                    :key="item.id"
                    v-slot="{ active }"
                    @click="$emit('click', item.id)"
                >
                    <a
                        href="#"
                        :class="[
                            active
                                ? 'text-gray-900 bg-gray-200 dark:text-black dark:bg-gray-300'
                                : 'text-gray-700',
                            'block px-4 py-2 text-sm',
                        ]"
                        >{{ item.name }}</a
                    >
                </MenuItem>
            </MenuItems>
        </transition>
    </Menu>
</template>
