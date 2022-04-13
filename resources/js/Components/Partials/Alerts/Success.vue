<template>
    <div class="row" :class="classes" style="
    padding: 15px;
    color: #FFF;
    background: #AAD26F;
    border-radius: 5px;
">
        <div class="row__column row__column--compact">
            <i class="fas fa-check"></i>
        </div>
        <div class="row__column text-center" style="font-weight: 600;">{{ message }}</div>
    </div>
</template>

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
