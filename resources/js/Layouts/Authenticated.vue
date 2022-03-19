<script setup>
import Menu from '@/Components/Menu.vue';
import { computed } from "vue";
import { usePage } from "@inertiajs/inertia-vue3";

const versionNumber = computed(() => usePage().props.value.versionNumber);
</script>

<template>
    <div>
        <Menu></Menu>
        <!-- Page Content -->
        <main>
            <slot />
        </main>
        <div class="text-center mb-3">
            {{ versionNumber }}
        </div>
    </div>
</template>
