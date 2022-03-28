<template>
    <div class="container">
        <div class="jumbotron" style="background-color: rgb(48,117,173);">
            <h1 style="text-align: center"><span style="color: white; ">{{title}}</span></h1>
        </div>
        <div class="alert alert-success" role="alert">
            <h4 class="alert-heading">Welcome</h4>
            <hr>
            <p class="mb-0">To start using the website as navigation bar tools</p>
            <p>Right now we have: <b>{{numeroCarteiras}}</b> wallets!</p>
            <p>A soma dos valores - "{{somaValue}}" - de todos os movimentos</p>
        </div>
    </div>
</template>

<script>
    export default {
    data: function () {
            return {
                title: 'Virtual Wallet',
                numeroCarteiras: 0,
                somaValue: 0,
            };
        }, async created() {
            this.numeroCarteiras = (await axios.get('/api/wallets/num')).data,
            this.somaValue = (await axios.get('/api/sum')).data[0].sum
        },
        name: "initialPage.vue"
    }
</script>
    <style scoped>
</style>
