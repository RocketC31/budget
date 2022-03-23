<script setup>
defineProps({
    classes: {
        type: String,
        default: ''
    },
    message: {
        type: String,
        default: ''
    }
});
</script>
<template>
    <div class="bg-red-50 border border-red-100 text-red-700 flex py-2 px-4 items-center rounded-md" :class="classes">
        <i class="far fa-times fa-sm"></i>
        <div class="flex-1 text-sm font-medium text-center">{{ message }}</div>
    </div>
</template>
