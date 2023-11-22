<template>
    <button :type="type"
        class="inline-flex items-center px-4 py-2 dark:bg-gray-800 border dark:border-transparent rounded-md font-semibold text-xs dark:text-white uppercase tracking-widest dark:hover:bg-gray-700 hover:bg-gray-100 dark:active:bg-gray-700 focus:outline-none dark:focus:border-gray-900 focus:shadow-outline-gray transition ease-in-out duration-150"
        :class="{'dark:bg-gray-700 bg-gray-100': active}"
    >
        <slot />
    </button>
</template>

<script setup>
    defineProps({
        type: {
            type: String,
            default: 'submit',
        },
        active: {
            type: Boolean,
            default: false
        }
    });
</script>
