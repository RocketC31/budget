<template>
    <div class="row row--separate row--bottom mt-3 mb-1">
        <h3>{{ trans('models.attachments') }}</h3>
        <form @submit.prevent="submitFile">
            <input type="file" name="file" @input="form.file = $event.target.files[0]" @change="submitFile" />
        </form>
    </div>
    <div class="box">
        <div v-if="!element.attachments" class="box__section text-center">{{ trans('general.empty_attachments', { args: { resource: trans('models.transaction').toLowerCase() } }) }} </div>
        <template v-else>
            <div v-for="attachment in element.attachments" class="box__section row">
                <div>
                    <img v-if="attachment.file_type !== 'pdf'" :src="attachment.file_b64" style="max-width: 100%; max-height: 200px; border-radius: 5px; vertical-align: top;" />
                    <div v-else class="mb-1" style="display: flex; align-items: center; justify-content: center; width: 200px; height: 200px; border-radius: 5px; background: #EEE;">
                        <i class="fas fa-file-pdf"></i>
                    </div>
                    <a :href="route('attachments.download', { attachment: attachment.id })">{{ trans('actions.download') }}</a>
                </div>
                <div class="ml-2">
                    <form @submit.prevent="removeFile(attachment.id)">
                        <button class="button link">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </form>
                </div>
            </div>
        </template>
    </div>
</template>

<script>
    import { Head } from "@inertiajs/inertia-vue3";
    import { trans } from 'matice';
    import { Inertia } from "@inertiajs/inertia";
    import { Link, useForm } from "@inertiajs/inertia-vue3";

    export default {
        components: {
            Link,
            Head
        },
        props: {
            element: {
                type: Object,
                default: null
            }
        },

        data: function() {
           return {
               file: null
           }
        },

        setup(props) {
            const form = useForm({
                transaction_type: props.element.type,
                transaction_id: props.element.id,
                file: null
            })

            function submitFile() {
                form.post(route('attachments.create'), {
                    _method: 'put',
                    file: form.file,
                    forceFormData: true,
                    onSuccess: () => form.reset('file'),
                })
            }

            return { form, submitFile, trans }
        },

        methods: {
            removeFile(id) {
                Inertia.delete(route('attachments.delete', { id: id }));
            }
        }
    }
</script>
