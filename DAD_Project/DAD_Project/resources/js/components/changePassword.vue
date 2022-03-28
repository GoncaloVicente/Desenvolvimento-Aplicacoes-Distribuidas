<template>

</template>

<script>
    export default {
        name: "changePassword",
        methods: {
            changePassword: function(){
                this.$emit('change-password')
            }
        }
    }
</script>
