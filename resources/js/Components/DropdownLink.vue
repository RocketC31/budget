<template>
    <Link class="block w-full px-4 py-2 text-left text-sm leading-5 text-gray-400 dark:text-white hover:bg-blue-50 dark:hover:bg-gray-800 hover:text-blue-600 dark:hover:text-white rounded focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out">
        <slot />
    </Link>
</template>

<script setup>
    import { Link } from '@inertiajs/inertia-vue3';
</script>
