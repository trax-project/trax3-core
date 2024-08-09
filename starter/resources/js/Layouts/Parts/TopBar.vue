<script setup>
import DarkModeButton from "@starter/Layouts/Parts/DarkModeButton.vue";
import ApplicationLogo from "@starter/Layouts/Parts/ApplicationLogo.vue";
import {
    UserCircleIcon,
} from "@heroicons/vue/24/outline";
import {
    Disclosure,
    Menu,
    MenuButton,
    MenuItem,
    MenuItems,
} from "@headlessui/vue";

const props = defineProps({
    current: {
        type: String,
        required: false,
    },
});
const navigation = [
    { name: "Statements", href: route("statements") },
    { name: "Access", href: route("access") },
];
const userNavigation = [
    { name: "Sign Out", href: route("logout") },
];
</script>

<template>
    <Disclosure as="nav" class="bg-gray-800">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="flex h-16 justify-between">
                <ApplicationLogo :compact="true" :dark="true" />
                <div class="pb-0 -my-px ml-6 flex space-x-4">
                    <span
                        v-for="item in navigation"
                        :key="item.name"
                        :href="item.href"
                        :class="[
                            item.name == current
                                ? 'border-fuchsia-500 text-white'
                                : 'border-transparent text-white/50 hover:text-white',
                            'box-border inline-flex items-center border-b-4 text-sm font-medium',
                        ]"
                        :aria-current="
                            item.name == current ? 'page' : undefined
                        "
                    >
                        <a
                            :href="item.href"
                            :class="[
                                'rounded-md px-3 py-2 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white/80',
                            ]"
                            >{{ item.name }}</a
                        >
                    </span>
                </div>
                <div class="ml-6 flex items-center">
                    <DarkModeButton :dark="true"></DarkModeButton>

                    <!-- Profile dropdown -->
                    <Menu as="div" class="relative ml-4">
                        <div>
                            <MenuButton
                                class="flex max-w-xs items-center rounded-full bg-transparent text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white"
                            >
                                <span class="sr-only">Open user menu</span>
                                <UserCircleIcon
                                    class="h-7 w-7 text-gray-400 hover:text-gray-200"
                                />
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
                                class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                            >
                                <MenuItem
                                    v-for="item in userNavigation"
                                    :key="item.name"
                                    v-slot="{ active }"
                                >
                                    <a
                                        :href="item.href"
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
                </div>
            </div>
        </div>
    </Disclosure>
</template>
