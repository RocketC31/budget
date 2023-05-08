<template>
    <Head :title=" trans('actions.edit') + ' ' + trans('models.space')" />

    <BreezeAuthenticatedLayout>
        <div class="wrapper my-3">
            <h2 class="mb-3">{{ trans('actions.edit') }} {{ trans('models.space') }}</h2>
            <form @submit.prevent="submitSpace" autocomplete="off">
                <div class="box">
                    <div class="box__section row">
                        <div class="row__column">
                            <h2>{{ trans('general.general') }}</h2>
                        </div>
                        <div class="row__column row__column--double">
                            <div class="input input--small">
                                <label>{{ trans('fields.name') }}</label>
                                <input type="text" name="name" v-model="space.name"/>
                            </div>
                            <div class="input input--small mb-0">
                                <label>{{ trans('fields.currency') }}</label>
                                <select class="p-2.5" disabled>
                                    <option>{{ space.currency.name }}</option>
                                </select>
                                <div class="hint mt-05">{{ trans('general.cant_edit_currency') }}</div>
                            </div>
                            <template v-if="syncBankActive">
                                <div class="input input--small mt-4 mb-0" >
                                    <Toggle @UpdateActive="onActiveSyncUpdate" :label="trans('fields.sync_bank_activate')" :checked="space.sync_active"></Toggle>
                                </div>
                                <div class="input input--small mt-4 mb-0" v-if="banks.length > 0">
                                    <Searchable :distinct-key="'name'" :name="'bank'" :items="getBanksName()" :initial="getCurrentBankName()" @SelectUpdated="bankUpdated"></Searchable>
                                </div>
                                <a v-if="linkBank" :href="linkBank">{{ trans('actions.connect_bank') }}</a>
                                <div v-if="space.bank && space.bank.account_id && space.sync_active" class="hint mt-05">{{ trans('general.configured_bank') }} {{ space.bank.name }}</div>
                            </template>
                        </div>
                    </div>
                </div>
                <div class="row row--right mt-2">
                    <Link class="button button--secondary mr-2" :href="route('settings.spaces.index')">{{ trans('actions.cancel') }}</Link>
                    <button class="button">{{ trans('actions.save') }}</button>
                </div>
            </form>
            <div class="box mt-3">
                <div class="box__section row">
                    <div class="row__column">
                        <h2>{{ trans('general.members') }}</h2>
                    </div>
                    <div class="row__column row__column--double">
                        <div v-for="(user, i) in space.users" :class="{ 'mt-2': i > 0 }">
                            <div class="color-dark mb-1">{{ user.name }}</div>
                            <div class="fs-sm">{{ capitalize(user.pivot.role) }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box mt-3">
                <div class="box__section row">
                    <div class="row__column">
                        <h2>{{ trans('general.invites') }}</h2>
                    </div>
                    <div class="row__column row__column--double">
                        <span v-if="space.invites.length === 0">{{ trans('activities.invites.no_invites')}}</span>
                        <div v-for="(invite, i) in space.invites" :class="{ 'mt-2': i > 0 }">
                            <div class="color-dark mb-1" >{{ invite.invitee.name }}</div>
                            <div class="fs-sm">{{ trans('general.invited_by') }} {{ invite.inviter.name }} &middot; {{ invite.status }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box mt-3">
                <div class="box__section row">
                    <div class="row__column">
                        <h2>{{ trans('general.invite_someone') }}</h2>
                    </div>
                    <div class="row__column row__column--double">
                        <Success v-if="flash === 'inviteStatus'" :message="trans('activities.invites.invite_sent')"></Success>
                        <Danger v-if="flash === 'present'" :message="trans('activities.invites.already_present')"></Danger>
                        <Danger v-if="flash === 'exists'" :message="trans('activities.invites.invites')"></Danger>
                        <form @submit.prevent="submitInvite" autocomplete="off">
                            <div class="input input--small">
                                <label>{{ trans('fields.email') }}</label>
                                <input type="email" name="email" v-model="invite.email"/>
                                <ValidationError v-if="errors.email" :message="errors.email"></ValidationError>
                            </div>
                            <div class="input input--small">
                                <label>{{ trans('fields.role') }}</label>
                                <select class="p-2.5" name="role" v-model="invite.role">
                                    <option value="admin">Admin</option>
                                    <option value="regular" selected>Regular</option>
                                </select>
                                <ValidationError v-if="errors.role" :message="errors.role"></ValidationError>
                            </div>
                            <button class="button">{{ trans('actions.invite') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>

<script setup>
    import { trans } from 'matice';
    import { Head, usePage, Link } from '@inertiajs/inertia-vue3';
    import { ref, computed } from "vue";
    import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
    import ValidationError from "@/Components/ValidationError.vue";
    import { Inertia } from "@inertiajs/inertia";
    import { capitalize } from '@/tools';
    import Danger from "@/Components/Partials/Alerts/Danger.vue";
    import Success from "@/Components/Partials/Alerts/Success.vue";
    import Toggle from "@/Components/Toggle.vue";
    import Searchable from "@/Components/Searchable.vue";

    const props = defineProps({
        space: Object,
        banks: Array
    });

    const space = ref(props.space);
    const invite = ref({
        email: null,
        role: 'admin'
    });

    function getBanksName() {
        return props.banks.map(bank => {
            bank.label = bank.name;
            return bank;
        });
    }

    function getCurrentBankName() {
        return props.space.bank && props.space.bank.name ? props.space.bank.name : ''
    }

    function submitSpace() {
        if (patchMethodAvailable.value)
            Inertia.patch(route('spaces.update', { space: space.value.id }), space.value);
        else
            Inertia.put(route('spaces.update', { space: space.value.id }), space.value);
    }

    function submitInvite() {
        Inertia.post(route('spaces.invite', { space: space.value.id }), invite.value);
    }

    function onActiveSyncUpdate(isActive) {
        space.value.sync_active = (isActive) ? 1 : 0;
    }

    function bankUpdated(bank) {
        space.value.bank = bank;
        space.value.bank.link = null;
    }

    const errors = computed(() => usePage().props.value.errors);
    const linkBank = computed(() => {
        if (props.space.bank) {
            return props.space.bank.link;
        }
        return null;
    });
    const flash = computed(() => usePage().props.value.flash.message);
    const patchMethodAvailable = computed(() => usePage().props.value.patchMethodAvailable);
    const syncBankActive = computed(() => usePage().props.value.bank_sync_available);
</script>
