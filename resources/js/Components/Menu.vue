<script setup>
import { ref, computed } from 'vue';
import { Link } from "@inertiajs/inertia-vue3";
import { usePage } from '@inertiajs/inertia-vue3';
import { changeTheme } from '@/tools';

import BreezeDropdown from '@/Components/Dropdown.vue';
import BreezeDropdownLink from '@/Components/DropdownLink.vue';
import { trans } from 'matice';

const showingNavigationDropdown = ref(false);
const spaces = computed(() => usePage().props.value.spaces);
const avatar = computed(() => usePage().props.value.auth.user.avatar);
const currentSpace = computed(() => usePage().props.value.current_space);
const openMobileMenu = ref(false);

function resetTheme() {
    changeTheme("light");
}

</script>

<template>
    <div class="navigation">
        <div class="wrapper items-center">
            <ul class="hidden w-full lg:flex">
                <li class="flex w-1/5">
                    <Link :class="{active :route().current('dashboard')}" class="m-auto flex items-center" :href="route('index')"><i class="fas fa-home fa-sm color-blue"></i> <span class="md:flex ml-05">{{ trans('general.dashboard') }}</span></Link>
                </li>
                <li class="flex w-1/5">
                    <Link :class="{active :route().current('transactions')}" class="m-auto flex items-center" :href="route('transactions.index')"><i class="fas fa-exchange-alt fa-sm color-green"></i> <span class="md:flex ml-05">{{ trans('models.transactions') }}</span></Link>
                </li>
                <li class="flex w-1/5">
                    <Link :class="{active :route().current('budgets')}" class="m-auto flex items-center" :href="route('budgets.index')"><i class="fas fa-wallet fa-sm color-red"></i> <span class="md:flex ml-05">{{ trans('models.budgets') }}</span></Link>
                </li>
                <li class="flex w-1/5">
                    <Link :class="{active :route().current('tags')}" class="m-auto flex items-center" :href="route('tags.index')"><i class="fas fa-tag fa-sm color-blue"></i> <span class="md:flex ml-05">{{ trans('models.tags') }}</span></Link>
                </li>
                <li class="flex w-1/5">
                    <Link :class="{active :route().current('reports')}" class="m-auto flex items-center" :href="route('reports.index')"><i class="fas fa-chart-line fa-sm color-green"></i> <span class="md:flex ml-05">{{ trans('pages.reports') }}</span></Link>
                </li>
            </ul>
            <div class="flex lg:hidden cursor-pointer" id="menu__mobile-btn" @click.stop="openMobileMenu = true">
                <div class="space-y-2">
                    <span class="block w-8 h-0.5 bg-gray-600"></span>
                    <span class="block w-8 h-0.5 bg-gray-600"></span>
                    <span class="block w-8 h-0.5 bg-gray-600"></span>
                </div>
            </div>
            <div class="navbar-backdrop fixed inset-0 bg-gray-800" id="menu__mobile_backdrop" :class="{active: openMobileMenu }" @click.stop="openMobileMenu = false"></div>
            <div class="dark:bg-neutral-700 navbar-menu z-50 fixed top-0 left-0 bottom-0 flex flex-col sm:w-2/6 py-6 px-6 bg-white overflow-y-auto w-full" :class="{active: openMobileMenu}" id="menu__mobile-panel">
                <nav>
                    <div class="flex items-center mb-8 cursor-pointer" id="menu__mobile-panel-btn" @click.stop="openMobileMenu = false">
                        <button class="p-4 navbar-close text-2xl">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div>
                        <ul>
                            <li class="mb-1">
                                <Link :class="{active: route().current('dashboard')}" class="block p-4 text-gray-400 dark:text-white hover:bg-blue-50 dark:hover:bg-gray-800 hover:text-blue-600 rounded" :href="route('index')">{{ trans('general.dashboard') }}</Link>
                            </li>
                            <li class="mb-1">
                                <Link :class="{active: route().current('transactions')}" class="block p-4 text-gray-400 hover:bg-blue-50 dark:hover:bg-gray-800 hover:text-blue-600 rounded" :href="route('transactions.index')">{{ trans('models.transactions') }}</Link>
                            </li>
                            <li class="mb-1">
                                <Link :class="{active: route().current('budgets')}" class="block p-4 text-gray-400 hover:bg-blue-50 dark:hover:bg-gray-800 hover:text-blue-600 rounded" :href="route('budgets.index')">{{ trans('models.budgets') }}</Link>
                            </li>
                            <li class="mb-1">
                                <Link :class="{active: route().current('tags')}" class="block p-4 text-gray-400 hover:bg-blue-50 dark:hover:bg-gray-800 hover:text-blue-600 rounded" :href="route('tags.index')">{{ trans('models.tags') }}</Link>
                            </li>
                            <li class="mb-1">
                                <Link :class="{active: route().current('reports')}" class="block p-4 text-gray-400 hover:bg-blue-50 dark:hover:bg-gray-800 hover:text-blue-600 rounded" :href="route('reports.index')">{{ trans('pages.reports') }}</Link>
                            </li>
                        </ul>
                    </div>
                </nav>
            </div>
            <BreezeDropdown align="right" width="48">
                <template #trigger>
                    <span class="inline-flex rounded-md w-full">
                        <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 focus:outline-none transition ease-in-out duration-150">
                            <img :src="avatar" class="avatar mr-05" />

                            <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </span>
                </template>

                <template #content>
                    <BreezeDropdownLink :href="route('activities.index')" method="get" as="button">
                        {{ trans('pages.activities') }}
                    </BreezeDropdownLink>
                    <BreezeDropdownLink :href="route('imports.index')" method="get" as="button">
                        {{ trans('models.imports') }}
                    </BreezeDropdownLink>
                    <BreezeDropdownLink :href="route('settings.index')" method="get" as="button">
                        {{ trans('pages.settings') }}
                    </BreezeDropdownLink>
                    <BreezeDropdownLink @click="resetTheme" :href="route('logout')" method="post" as="button">
                        {{ trans('pages.log_out') }}
                    </BreezeDropdownLink>
                    <BreezeDropdown :align="'left'" width="48" v-if="spaces.length > 1">
                        <template #trigger>
                            <span class="inline-flex rounded-md w-full">
                                <button type="button" class="flex w-full px-4 py-2 text-left text-sm leading-5 text-gray-400 dark:text-white hover:bg-blue-50 dark:hover:bg-gray-800 dark:hover:text-white hover:text-blue-600 rounded focus:outline-none transition duration-150 ease-in-out">
                                    {{ currentSpace.name }}

                                    <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </span>
                        </template>
                        <template #content>
                            <BreezeDropdownLink v-for="space in spaces" :href="route('spaces.show', { 'space' : space.id })" method="get" as="button">
                                {{ space.name }}
                            </BreezeDropdownLink>
                        </template>
                    </BreezeDropdown>
                </template>
            </BreezeDropdown>
        </div>
    </div>
</template>
