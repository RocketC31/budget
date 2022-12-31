<template>
    <Head :title="trans('actions.create') + ' ' + trans('models.transaction')" />

    <BreezeAuthenticatedLayout>
        <div class="wrapper my-3">
            <h2 class="mb-3">{{ trans('actions.create') }} {{ trans('models.transaction') }}</h2>
            <div>
                <div class="bg mb-2">
                    <button
                        class="bg__button"
                        :class="{ 'bg__button--active': type === 'earning' }"
                        @click="switchType('earning')">{{ trans('models.earning') }}</button>
                    <button
                        class="bg__button"
                        :class="{ 'bg__button--active': type === 'spending' }"
                        @click="switchType('spending')">{{ trans('models.spending') }}</button>
                </div>
                <div class="input">
                    <label>{{ trans('models.tag') }}</label>
                    <Searchable
                        name="tag"
                        :items="tags"
                        @SelectUpdated="tagUpdated"></Searchable>
                    <ValidationError v-if="errors.tag_id" :message="errors.tag_id"></ValidationError>
                </div>
                <div class="input">
                    <label>{{ trans('fields.date') }}</label>
                    <DatePicker
                        :first-day-of-week="firstDayOfWeek"
                        @DateUpdated="onDateUpdate"></DatePicker>
                    <div class="hint mt-05">YYYY-MM-DD</div>
                    <ValidationError v-if="errors.date" :message="errors.date"></ValidationError>
                    <ValidationError v-if="errors.day" :message="errors.day"></ValidationError>
                </div>
                <div class="input">
                    <label>{{ trans('fields.description') }}</label>
                    <input type="text" v-model="description" :placeholder="type === 'earning' ? trans('models.placeholder_earning') : trans('models.placeholder_spending')" />
                    <ValidationError v-if="errors.description" :message="errors.description"></ValidationError>
                </div>
                <div class="input">
                    <label>{{ trans('fields.amount') }} (<span v-html="currencies.find(c => c.id === defaultCurrencyId).symbol"></span>)</label>
                    <div class="row">
                        <div class="row__column row__column--compact mr-1" v-if="currencies.length > 1">
                            <select v-model="selectedCurrencyId">
                                <option v-for="currency in currencies" :key="'currencies-' + currency.id" :value="currency.id">
                                    <span v-html="currency.symbol"></span>
                                </option>
                            </select>
                        </div>
                        <div class="row__column row__column--double">
                            <input type="text" v-model="amount" />
                        </div>
                    </div>
                    <div class="hint mt-05" v-if="selectedCurrencyId !== defaultCurrencyId">{{ currencies.find(c => c.id === selectedCurrencyId).name }} {{ trans('general.will_convert' )}} {{ currencies.find(c => c.id === defaultCurrencyId).name }}</div>
                    <ValidationError v-if="errors.amount" :message="errors.amount"></ValidationError>
                </div>
                <div>
                    <div class="input row">
                        <div class="row__column row__column--compact mr-1">
                            <input type="checkbox" id="test" v-model="isRecurring" />
                        </div>
                        <div class="row__column">
                            <label for="test">{{ trans('models.recurring_transaction') }} &mdash; {{ trans('actions.create_for_me_future') }}</label>
                        </div>
                    </div>
                    <div v-if="isRecurring">
                        <div class="bg mb-2">
                            <button
                                v-for="interval in recurringsIntervals"
                                :key="'recurringsIntervals-' + interval"
                                class="bg__button"
                                :class="{ 'bg__button--active': recurringInterval === interval }"
                                @click="switchRecurringInterval(interval)">{{ capitalize(trans('calendar.intervals.'+interval)) }}</button>
                        </div>
                        <div class="input">
                            <label>{{ trans('general.how_long_spending') }}</label>
                            <div class="row">
                                <div class="row__column row__column--compact mr-1">
                                    <input id="noEnd" type="radio" v-model="recurringEnd" value="forever" />
                                </div>
                                <div class="row__column">
                                    <label for="noEnd">{{ trans('general.forever') }}</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="row__column row__column--compact mr-1">
                                    <input id="fixedEnd" type="radio" v-model="recurringEnd" value="fixed" />
                                </div>
                                <div class="row__column">
                                    <label for="fixedEnd">{{ trans('general.until') }}</label>
                                    <DatePicker
                                        name="end"
                                        :start-date="recurringEndDate"
                                        :first-day-of-week="firstDayOfWeek"
                                        @DateUpdated="onEndUpdate"></DatePicker>
                                    <div class="hint mt-05">YYYY-MM-DD</div>
                                    <ValidationError v-if="errors.end" :message="errors.end"></ValidationError>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <button
                    class="button"
                    @click="createEarning">
                    <span v-if="loading">{{ trans('actions.loading') }}</span>
                    <span v-if="!loading">{{ trans('actions.create') }}</span>
                </button>
                <div
                    v-if="success"
                    class="mt-2"
                    style="color: green;"
                >{{ trans('activities.transaction.success_created') }}</div>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>

<script>
    import { trans } from 'matice';
    import { Head, usePage } from '@inertiajs/inertia-vue3';
    import { ref, computed } from "vue";
    import { capitalize } from "@/tools";
    import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
    import Searchable from "@/Components/Searchable";
    import ValidationError from "@/Components/ValidationError";
    import DatePicker from "@/Components/DatePicker";
    import { Inertia } from "@inertiajs/inertia";

    export default {
        components: {
            Head,
            BreezeAuthenticatedLayout,
            Searchable,
            ValidationError,
            DatePicker,
            Inertia
        },

        props: {
            tags: Array,
            currencies: Array,
            defaultTransactionType: String,
            firstDayOfWeek: String,
            defaultCurrencyId: Number,
            recurringsIntervals: Array
        },

        data() {
            return {
                tag: null,
                date: this.getTodaysDate(),
                description: '',
                amount: '10.00',
                isRecurring: false,
                recurringInterval: 'monthly',
                recurringEnd: 'forever',
                recurringEndDate: this.get100DaysFutureDate(),
                success: false,
                loading: false
            }
        },

        setup(props) {
            let type = ref(props.defaultTransactionType);
            let selectedCurrencyId = ref(props.defaultCurrencyId);
            const errors = computed(() => usePage().props.value.errors)
            return { type, selectedCurrencyId, trans, capitalize, errors }
        },

        methods: {
            onDateUpdate(date) {
                this.date = date
            },

            onEndUpdate(date) {
                this.recurringEndDate = date
            },

            switchType(type) {
                this.type = type;
                this.success = false
            },

            tagUpdated(payload) {
                this.tag = null;
                if (payload && payload.key) {
                    this.tag = payload.key
                }
            },

            getTodaysDate() {
                return new Date().toISOString().slice(0, 10)
            },

            get100DaysFutureDate() {
                let now = new Date()

                return (now.getFullYear() + 1) + '-' + ('0' + (now.getMonth() + 1)).slice(-2) + '-' + ('0' + now.getDate()).slice(-2)
            },

            switchRecurringInterval(interval) {
                this.recurringInterval = interval;
            },

            createEarning() {
                if (!this.loading) {
                    this.loading = true

                    let body = {
                        amount: this.amount,
                        currency_id: this.selectedCurrencyId,
                        description: this.description,
                        type: this.type
                    }

                    if (this.tag) {
                        body["tag"] = this.tag;
                    }

                    if (this.isRecurring) { // It's a recurring
                        body["type"] = this.type;
                        body["interval"] = this.recurringInterval;
                        body["day"] = this.date.slice(-2);
                        body["start"] = this.date;

                        if (this.recurringEnd === 'fixed') {
                            body["end"] = this.recurringEndDate
                        }

                        Inertia.post('/recurrings', body, {
                            onFinish: () => this.loading = false
                        });
                    } else {
                        body["date"] = this.date;

                        Inertia.post('/transactions', body, {
                            onFinish: () => this.loading = false
                        });
                    }
                }
            }
        }
    }
</script>
