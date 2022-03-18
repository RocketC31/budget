<script setup>
import Menu from '@/Components/Menu.vue';
</script>

<template>
    <div>
        <Menu></Menu>
        <!-- Page Content -->
        <main>
            <slot />
        </main>
    </div>
</template>
