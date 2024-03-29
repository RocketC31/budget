<template>
    <div v-click-outside="hide">
        <input type="hidden" :name="name" :value="inputValue" />
        <div class="searchable__input" @click="toggleShown">
            <span v-if="!selected">{{ trans('actions.select') }}</span>
            <span v-else v-html="selected.label"></span>
        </div>
        <div class="searchable__container">
            <div class="searchable__list" v-if="shown && queriedItems">
                <input type="search" v-model="query" :placeholder="trans('actions.search')" ref="query" />
                <ul :style="{ 'max-height': size * 50 + 'px' }">
                    <li
                        class="row"
                        v-for="item in queriedItems" :key="item"
                        @click="select(item)">
                        <div
                            class="row__column row__column--compact"
                            style="width: 20px;">
                            <i
                                class="fas fa-check fa-xs"
                                v-if="isSelected(item)"></i>
                        </div>
                        <div
                            class="row__column"
                            v-html="item.label"></div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>

<script>
    import { trans } from "matice";

    export default {
        props: {
            name: String,
            size: { type: Number, default: 4 },
            items: Array,
            initial: String,
            distinctKey: {
                type: String,
                default: 'key'
            },
            canUnselectLast: { type: Boolean, default: true }
        },

        setup(props) {
            return { trans }
        },

        data() {
            return {
                shown: false,
                selected: this.initial ? this.getItemByKey(this.initial) : null,
                query: ''
            }
        },

        computed: {
            inputValue() {
                if (this.selected) {
                    return this.selected.key
                }

                return ''
            },

            queriedItems() {
                return this.items.filter(item => {
                    if (this.query.length < 1 || item.label.toUpperCase().indexOf(this.query.toUpperCase()) > -1) {
                        return item
                    }
                });
            }
        },

        methods: {
            getItemByKey(key) {
                for (let i = 0; i < this.items.length; i ++) {
                    if (this.items[i][this.distinctKey] === key) {
                        return this.items[i]
                    }
                }
            },

            isSelected(item) {
                return this.selected && this.selected === item;
            },

            toggleShown() {
                this.shown = !this.shown

                this.$nextTick(() => {
                    if (this.$refs.query) {
                        this.$refs.query.focus()
                    }
                })
            },

            show() {
                this.shown = true
            },

            hide() {
                this.shown = false
            },

            select(payload) {
                //If it's the currently selected where we re click. Considere it's to unset it !
                if (this.isSelected(payload) && this.canUnselectLast) {
                    payload = null;
                }
                this.selected = payload

                this.hide()

                // Update parent
                this.$emit('SelectUpdated', this.selected)
            }
        }
    }
</script>
