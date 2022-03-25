<script setup>
import { Head } from '@inertiajs/inertia-vue3';
import { trans } from 'matice';
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import { Inertia } from "@inertiajs/inertia";

const props = defineProps({
    invite: Object
})

function accept() {
    Inertia.post(route('space_invites.accept', { space: props.invite.space.id, invite: props.invite.id }));
}

function deny() {
    Inertia.post(route('space_invites.deny', { space: props.invite.space.id, invite: props.invite.id }));
}
</script>

<template>
    <Head :title="trans('general.invite')" />

    <BreezeAuthenticatedLayout>
        <div class="wrapper wrapper--narrow my-3">
            <h2>{{ trans('general.invite') }}</h2>
            <div class="box mt-3">
                <div class="box__section">
                    <h3 class="color-dark mb-1">{{ trans('general.invited_to') }} "{{ invite.space.name }}"</h3>
                    <div>{{ trans('general.sent_by') }} {{ invite.inviter.name }}.</div>
                    <div class="row row--middle mt-2">
                        <button @click="accept" class="button">{{ trans('actions.accept') }}</button>
                        <button @click="deny" class="button button--secondary ml-2">{{ trans('actions.deny') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>
