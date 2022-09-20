<template>
    <div class="w-full flex flex-wrap ">
        <template v-for="field in config.fields">
            <div class="w-full flex flex-wrap px-2 py-4" v-if="field.handle === 'product_variants'">
                <h3 class="w-full flex flex-wrap px-2 mb-2">Product Variant Fields</h3>
                <div class="w-1/2 px-2 mb-2" v-for="customField in field.option_fields">
                    <label class="block capitalize mb-1">{{ customField.display || customField.handle }}</label>
                    <v-select
                        class="w-full"
                        :options="config.keys"
                        :value="customMapping[customField.handle]"
                        @input="(value) => setCustomFieldMapping(customField.handle, value)"
                    />
                    <input type="hidden" :name="'custom_field_mapping[' + customField.handle + ']'"
                           :value="customMapping[customField.handle]">
                </div>
            </div>
            <div class="w-1/2 px-2 mb-2" v-else>
                <label class="block capitalize mb-1">{{ field.display || field.handle }}</label>
                <v-select
                    class="w-full"
                    :options="config.keys"
                    :value="mapping[field.handle]"
                    @input="(value) => setMapping(field.handle, value)"
                />
                <input type="hidden" :name="'mapping[' + field.handle + ']'" :value="mapping[field.handle]">
                <p v-if="['title', 'sku'].includes(field.handle)" class="text-xs text-red text-danger">This field is required</p>
            </div>
        </template>
    </div>
</template>

<script>
export default {
    mixins: [Fieldtype],

    props: ['config'],

    data: function () {
        return {
            mapping: {},
            customMapping: {}
        };
    },

    mounted() {
        this.config.keys.forEach(key => {
            const field = this.config.fields.filter(field => field.handle === key)[0]

            if (field) {
                this.$set(this.mapping, field.handle, key)
                this.$emit('mapping', this.mapping)
                this.$set(this.customMapping, field.handle, key)
                this.$emit('custommapping', this.customMapping)
            }
        })
    },

    methods: {
        setMapping: function (handle, value) {
            this.$set(this.mapping, handle, value)
            this.$emit('mapping', this.mapping)
        },
        setCustomFieldMapping: function (handle, value) {
            this.$set(this.customMapping, handle, value)
            this.$emit('custommapping', this.customMapping)
        },
    }
}
</script>
