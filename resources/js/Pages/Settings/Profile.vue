<template>
    <Head :title="trans('general.profile')" />

    <Layout>
        <form @submit.prevent="submit">
            <div class="box">
                <div class="box__section">
                    <div class="input input--small">
                        <label>{{ trans('fields.avatar') }}</label>
                        <img class="mb-2" :src="user.avatar" style="width: 200px; height: 200px; border-radius: 5px; object-fit: cover;" />
                        <input type="file" name="avatar" @input="form.avatar = $event.target.files[0]" />
                        <ValidationError :message="errors.avatar"></ValidationError>
                    </div>
                    <div class="input input--small">
                        <label>{{ trans('fields.name') }}</label>
                        <input type="text" name="name" v-model="form.name" />
                    </div>
                    <button class="button">{{ trans('actions.save') }}</button>
                </div>
            </div>
        </form>
    </Layout>
</template>

<script setup>
    import { Head, usePage, useForm } from "@inertiajs/inertia-vue3";
    import { trans } from 'matice';
    import Layout from '@/Pages/Settings/Layout.vue';
    import { computed, onMounted } from "vue";
    import ValidationError from "@/Components/ValidationError.vue";

    function submit() {
        form.post(route('settings.profile'), {
            _method: 'put',
            file: form.avatar,
            forceFormData: true
        })
    }

    const user = computed(() => usePage().props.value.auth.user);
    const errors = computed(() => usePage().props.value.errors);

    const form = useForm({
        name: null,
        avatar: null
    });

    onMounted(() => {
        form.name = user.value.name;
    });
</script>
