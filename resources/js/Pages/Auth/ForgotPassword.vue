<script setup>
import BreezeButton from '@/Components/Button.vue';
import BreezeGuestLayout from '@/Layouts/Guest.vue';
import BreezeInput from '@/Components/Input.vue';
import BreezeLabel from '@/Components/Label.vue';
import BreezeValidationErrors from '@/Components/ValidationErrors.vue';
import {Head, useForm, usePage} from '@inertiajs/inertia-vue3';
import { trans } from 'matice';
import { computed } from "vue";
import Success from "@/Components/Partials/Alerts/Success";
import {Inertia} from "@inertiajs/inertia";

const props = defineProps({
    status: String,
    token: {
        default: null
    }
});

const form = useForm({
    email: null,
    password: null,
    password_confirmation: null
});

const submit = () => {
    const data = {};
    if (props.token !== null) {
        data['token'] = props.token;
        data['password'] = form.password;
        data['password_confirmation'] = form.password_confirmation;
    } else {
        data['email'] = form.email;
    }

    console.log(data);

    Inertia.post(route('password.email'), data);
};

const flash = computed(() => usePage().props.value.flash.message);
</script>

<template>
    <BreezeGuestLayout>
        <Head :title="trans('auth.forgot_your_password')" />

        <div class="wrapper mt-3">
            <Success v-if="flash === 'success'" :message="trans('auth.reset_password_sent')"></Success>
        </div>

        <div class="wrapper wrapper--narrow my-3">
            <div class="box">
                <div class="box__section">
                    <form @submit.prevent="submit">
                        <div v-if="token">
                            <BreezeLabel for="password" :value="trans('fields.password')" />
                            <BreezeInput id="password" type="password" class="mt-1 block w-full" v-model="form.password" required autofocus />
                            <BreezeLabel for="password_confirmation" :value="trans('auth.verify_password')" />
                            <BreezeInput id="password_confirmation" type="password" class="mt-1 block w-full" v-model="form.password_confirmation" required autofocus />
                        </div>
                        <div v-else>
                            <BreezeLabel for="email" value="Email" />
                            <BreezeInput id="email" type="email" class="mt-1 block w-full" v-model="form.email" required autofocus autocomplete="username" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <BreezeButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                {{ trans('actions.reset') }}
                            </BreezeButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="mb-4 text-sm text-gray-600">
            {{ trans('auth.forgot_your_password_desc') }}
        </div>

        <BreezeValidationErrors class="mb-4" />


    </BreezeGuestLayout>
</template>
