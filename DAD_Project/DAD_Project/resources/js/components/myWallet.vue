<template>
    <div>
        <div class="jumbotron" style="background-color: rgb(48,117,173);">
            <h1 style="text-align: center"><span style="color: white; ">{{title}}</span></h1>
        </div>
        <div class="col-md-12">
            <label>Current Balance</label>
            <input readonly="readonly" type="number" class="form-control" id="balance" name="balance" v-model="balance">
            <br>
        </div>

        <div class="col-md-12">
            <edit v-if="editingMovement" :movement="selectedMovement" :categories="categories" @save-movement="saveMovement" @cancel-edit="cancelEdit"> </edit>
        </div>

        <div class="col-md-12">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th> Id </th>
                        <th> Type </th>
                        <th> Transfer e-mail </th>
                        <th> Type of payment </th>
                        <th> Category </th>
                        <th> Date </th>
                        <th> Start balance End Balance </th>
                        <th> Value </th>
                        <th> Actions </th>
                    </tr>
                    <tr>
                        <td>
                            <input v-model="filter.id_movement" type="text" class="search-input form-control" placeholder="Search ID"/>
                        </td>
                        <td>
                            <select v-model="filter.type" class="search-select form-control">
                                <option></option>
                                <option value="e">Expense</option>
                                <option value="i">Income</option>
                            </select>
                        </td>
                        <td>
                            <input v-model="filter.transfer_email" type="text" class="search-input form-control" placeholder="Search Transfer Email"/>
                        </td>
                        <td>
                            <select v-model="filter.type_payment" class="search-select form-control">
                                <option></option>
                                <option value="c">Cash</option>
                                <option value="bt">Bank Transfer</option>
                                <option value="mb">MB Payment</option>
                            </select>
                        </td>
                        <td>
                            <input v-model="filter.category" type="text" class="search-input form-control" placeholder="Search Category"/>
                        </td>
                        <th>
                            <input v-model="filter.inicial_date" type="date" class="search-input form-control" placeholder="Search Start Date"/>
                            <input v-model="filter.final_date" type="date" class="search-input form-control" placeholder="Search Finally Date"/>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="movement in myMovements" :key="movement.id">
                        <td> {{ movement.id }} </td>

                        <td v-if="movement.type === 'e'"> Expense </td>
                        <td v-if="movement.type === 'i'"> Income </td>
                        <td v-if="movement.transfer === 0"> --- </td>

                        <td v-if="movement.transfer === 1"> {{ movement.transfer_email }} </td>

                        <td v-if="movement.type_payment === 'mb'"> ATM </td>
                        <td v-if="movement.type_payment === 'bt'"> Bank Transfer </td>
                        <td v-if="movement.type_payment === 'c'"> Cash </td>
                        <td v-if="movement.type_payment === null"> --- </td>

                        <td class="capitalize" v-if="movement.category != null"> {{ movement.category }} </td>

                        <td v-if="movement.category === null"> --- </td>

                        <td> {{ movement.date }} </td>
                        <td> "{{ movement.start_balance }} => {{ movement.end_balance }}" </td>
                        <td> {{ movement.value }} </td>

                        <td>
                            <a class="btn btn-sm btn-primary" v-on:click.prevent="editMovement(movement)"> Edit </a>
                            <a class="btn btn-sm btn-info" v-on:click="selectMovement(movement)"> Detail </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div>
            <my-movement :myMovement="selectedMovement"></my-movement>
        </div>
        <div class="col-md-12">
            <paginate
                :pageCount="pageCount"
                :containerClass="'pagination'"
                :clickHandler="clickCallback">
            </paginate>
        </div>
    </div>
</template>

<script>
    import myMovement from "./myMovementList";
    import EditMovement from "./editMovement";

    export default {
        components: {
            "edit": EditMovement,
            "my-movement": myMovement,
        },
        data: function () {
            return {
                title: "My Wallet",
                balance: 0,
                categories: [],
                myMovements: [],
                total: 0,
                filter: {
                    id_movement: '',
                    type: '',
                    transfer_email: '',
                    type_payment: '',
                    category: '',
                    inicial_date: '',
                    final_date: '',
                    dest_email: ''
                },
                selectedMovement: null,
                editingMovement: false,
            }
        },
        methods: {
            selectMovement: function(movement){
                this.selectedMovement=movement;
            },
            editMovement: function (movement) {
                this.selectedMovement=movement;
                this.editingMovement = movement;
                this.showSuccess = false;
            },
            saveMovement: function(movement){
                this.editingMovement = false;
                axios.put('api/movements/'+ movement.id, movement)
                    .then(response=>{
                        this.showSuccess = true;
                        this.successMessage = 'Movement Saved';

                        Object.assign(this.selectedMovement, response.data.data);
                        this.selectedMovement = null;
                        this.editingMovement = false;
                    });
            },
            cancelEdit: function(){
                this.showSuccess = false;
                this.editingMovement = false;
            },
            clickCallback: function (page) {

                let variables = '&id=' + this.filter.id_movement + '&type=' + this.filter.type + '&transfer_email=' + this.filter.transfer_email + '&type_payment=' + this.filter.type_payment + '&category=' + this.filter.category + '&dateInicial=' + this.filter.inicial_date + '&dateFinal=' + this.filter.final_date + '&dest_email=' + this.filter.dest_email;

                axios.get("/api/my/movements/?page=" + page + variables).then(response => {
                    this.myMovements = response.data.data;
                    this.total = response.data.total;
                });
            }
        },
            created() {
                axios.get('/api/wallets/id/' + this.$store.state.user.email)
                    .then(response => {
                        this.balance = response.data[0].balance;
                    });

                axios.get('/api/my/movements/')
                    .then(response => {
                        this.myMovements = response.data.data;
                        this.total = response.data.total;
                    });

                axios.get('/api/movements/categories')
                    .then(response => {
                        this.categories = response.data;
                    })
            },
        computed: {
            pageCount: function () {
                if (this.total) {
                    let pagecount = Math.round(this.total / 15);
                    return pagecount;
                } else {
                    return 0;
                }
            }
        },
        watch: {
            filter: {
                deep: true,
                // We have to move our method to a handler field
                handler() {

                    let variables = '&id=' + this.filter.id_movement + '&type=' + this.filter.type + '&transfer_email=' + this.filter.transfer_email + '&type_payment=' + this.filter.type_payment + '&category=' + this.filter.category + '&dateInicial=' + this.filter.inicial_date + '&dateFinal=' + this.filter.final_date + '&dest_email=' + this.filter.dest_email;

                    axios.get("/api/my/movements?" + variables).then(response => {
                        this.myMovements = response.data.data;
                        this.total = response.data.total;
                    });
                }
            }
        },
        sockets: {
            balance(msg) {
                this.balance = msg;
                axios.get('/api/my/movements/')
                    .then(response => {
                        this.myMovements = response.data.data;
                        this.total = response.data.total;
                    });
            }
        }
    }
</script>

<style scoped>
    tr.activerow {
        background: #123456 !important;
        color: #fff !important;
    }

    td.capitalize {
        text-transform: capitalize;
    }

    .table tr {
        height: 25px;
    }
    .btn {
        margin-top: 0;
        margin-bottom: 0;
    }
</style>
