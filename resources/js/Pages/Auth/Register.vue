<template>
    <BreezeGuestLayout>
        <Head :title="trans('auth.register')" />

        <BreezeValidationErrors class="mb-4" />

        <form @submit.prevent="submit">
            <div>
                <BreezeLabel for="name" :value="trans('fields.name')" />
                <BreezeInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" required autofocus autocomplete="name" />
            </div>

            <div class="mt-4">
                <BreezeLabel for="email" :value="trans('fields.email')" />
                <BreezeInput id="email" type="email" class="mt-1 block w-full" v-model="form.email" required autocomplete="username" />
            </div>

            <div class="mt-4">
                <BreezeLabel for="password" :value="trans('fields.password')" />
                <BreezeInput id="password" type="password" class="mt-1 block w-full" v-model="form.password" required autocomplete="new-password" />
            </div>

            <div class="mt-4">
                <BreezeLabel for="password_confirmation" :value="trans('auth.verify_password')" />
                <BreezeInput id="password_confirmation" type="password" class="mt-1 block w-full" v-model="form.password_confirmation" required autocomplete="new-password" />
            </div>

            <div class="mt-4">
                <BreezeLabel for="currency" :value="trans('fields.currency')" />
                <select class="w-full px-3 py-2 text-sm border rounded-md appearance-none" name="currency" v-model="form.currency">
                    <option v-for="currency in currencies" :value="currency.id">{{ currency.name }} (<span v-html="currency.symbol"></span>)</option>
                </select>
            </div>

            <div class="flex items-center justify-end mt-4">
                <Link :href="route('login')" class="underline text-sm text-gray-600 hover:text-gray-900">
                    {{ trans('auth.already_using' )}}
                </Link>

                <BreezeButton class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    {{ trans('auth.register') }}
                </BreezeButton>
            </div>
        </form>
    </BreezeGuestLayout>
</template>

<script setup>
    import BreezeButton from '@/Components/Button.vue';
    import BreezeGuestLayout from '@/Layouts/Guest.vue';
    import BreezeInput from '@/Components/Input.vue';
    import BreezeLabel from '@/Components/Label.vue';
    import BreezeValidationErrors from '@/Components/ValidationErrors.vue';
    import { Head, Link, useForm } from '@inertiajs/inertia-vue3';
    import { trans } from 'matice';

    defineProps({
        currencies: Array,
    })

    const form = useForm({
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        currency: 1, //€
        terms: false,
    });

    const submit = () => {
        form.post(route('register'), {
            onFinish: () => form.reset('password', 'password_confirmation'),
        });
    };
</script>
