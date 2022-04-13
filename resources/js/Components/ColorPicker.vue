<template>
    <div>
        <Chrome v-model="color" :disable-alpha="true" :disable-fields="true"> </Chrome>
    </div>
</template>

<script>
    import { Chrome } from '@ckpack/vue-color';
    export default {
        components: {
            Chrome,
        },
        props: {
            initialColor: {
                type: String,
                default: function () {
                    return (Math.random() * 0xFFFFFF<<0).toString(16)
                }
            }
        },

        data: function() {
           return {
               color: "#"+this.initialColor
           }
        },

        watch: {
            color: function() {
                this.$emit('ColorUpdated', this.color.hex)
            }
        },

        created() {
            this.$emit('ColorUpdated', this.color);
        }
    }
</script>
