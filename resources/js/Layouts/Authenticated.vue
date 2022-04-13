<template>
    <div>
        <Menu></Menu>
        <!-- Page Content -->
        <main>
            <div v-if="user.verification_token" class="text-center" style="
                    padding: 15px;
                    color: #FFF;
                    background: #F86380;
                ">
                <span v-html="trans('general.verify_account')"></span>
                ({{ trans('general.or') }} <button @click="submit" class="button link">{{ trans('actions.resent').toLowerCase() }}</button>)
            </div>
            <div class="wrapper mt-3">
                <Success v-if="flash === 'success'" :message="trans('auth.verification_sent')"></Success>
                <Danger v-if="flash === 'already_verified'" :message="trans('auth.verification_already_verified')"></Danger>
                <Danger v-if="flash === 'rate_limited'" :message="trans('auth.verification_wait_next_send')"></Danger>
            </div>
            <slot />
        </main>
        <div class="text-center mb-3">
            {{ versionNumber }}
        </div>
    </div>
</template>

<script setup>
    import Menu from '@/Components/Menu.vue';
    import { trans } from 'matice';
    import { computed } from "vue";
    import { usePage } from "@inertiajs/inertia-vue3";
    import Success from "@/Components/Partials/Alerts/Success";
    import Danger from "@/Components/Partials/Alerts/Danger";
    import { Inertia } from "@inertiajs/inertia";

    const versionNumber = computed(() => usePage().props.value.versionNumber);

    const user = computed(() => usePage().props.value.auth.user);
    const flash = computed(() => usePage().props.value.flash.message);

    function submit() {
        Inertia.post(route('resend_verification_mail'));
    }
</script>
