<template>
    <BreezeAuthenticatedLayout>
        <div class="wrapper my-3">
            <div class="row md:flex-row flex-col">
                <div class="row__column mr-3 md:mr-3 mb-2 md:max-w-xs w-full">
                    <div class="box">
                        <div class="box__section box__section--header">{{ trans('pages.settings') }}</div>
                        <ul class="box__section">
                            <li><Link :href="route('settings.profile')"><i class="fas fa-user fa-sm"></i> {{ trans('general.profile') }}</Link></li>
                            <li><Link :href="route('settings.account')"><i class="fas fa-lock fa-sm"></i> {{ trans('general.account') }}</Link></li>
                            <li><Link :href="route('settings.preferences')"><i class="fas fa-sliders-h fa-sm"></i> {{ trans('general.preferences') }}</Link></li>
                            <li><Link :href="route('settings.dashboard')"><i class="fas fa-home fa-sm"></i> {{ trans('general.dashboard') }}</Link></li>
                            <li><Link :href="route('settings.spaces.index')"><i class="fas fa-rocket fa-sm"></i> {{ trans('models.spaces') }}</Link></li>
                        </ul>
                    </div>
                </div>
                <div class="row__column w-full">
                    <main>
                        <slot />
                    </main>
                </div>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>

<script setup>
    import { Link } from "@inertiajs/inertia-vue3";
    import { trans } from 'matice';
    import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
</script>
