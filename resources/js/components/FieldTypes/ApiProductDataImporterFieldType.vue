<template>
    <div class="w-full flex flex-wrap p-2">
        <p class="font-bold">API Product field here</p>
    </div>
</template>

<script>
export default {
    mixins: [ Fieldtype ],

    props: ['config'],

    data: function() {
        return {
            information: [],
        };
    },

    mounted() {},

    methods: {}
}
</script>
