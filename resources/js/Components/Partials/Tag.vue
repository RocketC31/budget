<template>
    <span :style="'background:#'+tag.color"><i class="fas fa-tag fa-xs"></i> {{ tag.name }}</span>
</template>

<script setup>
    defineProps({
        tag: Object
    });
</script>
<style scoped>
    span {
        border-radius: 5px;
        color: #FFF;
        padding: 5px 10px;
        font-size: 14px;
        font-weight: 600;
    }
</style>
