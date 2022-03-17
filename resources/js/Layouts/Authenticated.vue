<script setup>
import Menu from '@/Components/Menu.vue';
</script>

<template>
    <div>
        <Menu></Menu>
        <div class="min-h-screen bg-gray-100">
            <!-- Page Content -->
            <main>
                <slot />
            </main>
        </div>
    </div>
</template>
