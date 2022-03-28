<template>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>Description</th>
                        <th>Source Description</th>
                        <th>IBAN</th>
                        <th>MB Entity Code</th>
                        <th>MB Payment Reference</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-if="myMovement">
                        <td>{{myMovement.description}}</td>
                        <td>{{myMovement.source_description}}</td>
                        <td>{{myMovement.iban}}</td>
                        <td>{{myMovement.mb_entity_code}}</td>
                        <td>{{myMovement.mb_payment_reference}}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "myMovementList",
        props: ['myMovement'],
    }
</script>

<style scoped>

</style>
