<template>
    <div class="flex items-center dark:text-gray-500" v-if="tag">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" :fill="'#' + tag.color">
            <path d="M9.16667 2.5L16.6667 10C17.0911 10.4745 17.0911 11.1922 16.6667 11.6667L11.6667 16.6667C11.1922 17.0911 10.4745 17.0911 10 16.6667L2.5 9.16667V5.83333C2.5 3.99238 3.99238 2.5 5.83333 2.5H9.16667"></path>
        </svg>
        <p class="ml-1">{{ tag.name }}</p>
    </div>
</template>

<script setup>
    defineProps({
        tag: Object
    });
</script>
