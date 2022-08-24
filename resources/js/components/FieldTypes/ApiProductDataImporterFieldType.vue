<template>
    <div class="w-full flex flex-wrap -mx-2">
        <p>field here</p>
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
