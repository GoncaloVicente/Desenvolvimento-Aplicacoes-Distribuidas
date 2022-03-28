<template>
    <div class="container">
        <form>
            <div class="jumbotron" style="background-color: rgb(48,117,173);">
                <h1 style="text-align: center"><span style="color: white; ">{{title}}</span></h1>
            </div>
            <div class="col-md-12">
                <br>
                <label>Type of movement</label>
                <select name="type_movement" id="tMovement" class="form-control" v-model="$v.type_movement.$model">
                    <option disabled selected> -- Select an option --</option>
                    <option value="pee">Payment to externel entity</option>
                    <option value="t">Transfer</option>
                </select>
                <div style="color:red" v-if="!$v.type_movement.required && $v.type_movement.$anyDirty">Type of Movement is
                    required
                </div>
            </div>
            <div class="col-md-12">
                <br>
                <label>Value of expense</label>
                <input type="number" class="form-control" id="value" name="value"
                       placeholder="Value of expense" v-model="$v.value.$model">
                <div style="color:red" v-if="!$v.value.required && $v.value.$anyDirty">Value is required</div>
                <div style="color:red" v-if="!$v.value.minValue && $v.value.$anyDirty">Value must be greater than 0.01
                </div>
                <div style="color:red" v-if="!$v.value.maxValue && $v.value.$anyDirty">Value must be less than 5000.00
                </div>
            </div>
            <div class="col-md-12">
                <br>
                <label>Category</label>

                <vue-select @class="form-control" name="category" :filterable="false"
                            @search="fetchCategories" v-model="$v.category_id.$model"
                            placeholder="Search for category"
                            :options="categories">
                </vue-select>
                <div style="color:red" v-if="!$v.category_id.required && $v.category_id.$anyDirty">Category is required</div>
            </div>
            <div class="col-md-12">
                <br>
                <label>Description</label>
                <input type="text" v-model="description" class="form-control" id="description" name="description"
                       placeholder="Description">
            </div>

            <div class="col-md-12" v-if="type_movement=='pee'">
                <br>
                <label>Type of payment</label>
                <select name="type_payment" id="tPagamento" class="form-control" v-model="type_payment">
                    <option disabled selected> -- Select an option --</option>
                    <option value="bt">Bank Transfer</option>
                    <option value="mb">MB Payment</option>
                </select>
                <!--                <div style="color:red" v-if="!$v.type_payment.required && $v.type_payment.$anyDirty">Type of payment is required</div>-->
            </div>

            <div class="col-md-12" v-if="type_payment=='bt' && type_movement=='pee'">
                <br>
                <label>IBAN</label>
                <input type="text" class="form-control" id="iban" name="iban" placeholder="IBAN"
                       v-model="$v.iban.$model">
                <div style="color:red" v-if="!$v.iban.maxLength && $v.iban.$anyDirty">IBAN must be have 25 digits</div>
            </div>
            <div class="col-md-12" v-if="type_payment=='mb' && type_movement=='pee'">
                <br>
                <label>MB Entity</label>
                <input type="number" class="form-control" id="mb_entity_code" name="mb_entity_code"
                       placeholder="MB Entity" v-model="$v.mb_entity_code.$model">
                <!--                    <div style="color:red" v-if="!$v.iban.required && $v.iban.$anyDirty">IBAN is required</div>-->
                <div style="color:red" v-if="!$v.mb_entity_code.maxLength && $v.mb_entity_code.$anyDirty">MB Entity must be have 5
                    numbers
                </div>
                <div style="color:red" v-if="!$v.mb_entity_code.numeric && $v.mb_entity_code.$anyDirty">MB Entity must be
                    numeric
                </div>
            </div>
            <div class="col-md-12" v-if="type_payment=='mb' && type_movement=='pee'">
                <br>
                <label>MB Reference</label>
                <input type="number" class="form-control" id="mb_payment_reference" name="mb_payment_reference"
                       placeholder="MB Reference" v-model="$v.mb_payment_reference.$model">
                <div style="color:red" v-if="!$v.mb_payment_reference.maxLength && $v.mb_payment_reference.$anyDirty">MB Entity must be
                    have 9 numbers
                </div>
                <div style="color:red" v-if="!$v.mb_payment_reference.numeric && $v.mb_payment_reference.$anyDirty">MB Entity must be
                    numeric
                </div>
            </div>

            <div class="col-md-12" v-if="type_movement=='t'">
                <label>Email</label>
                <vue-select @class="form-control" name="email" :filterable="false" :options="emails"
                            @search="fetchEmails" v-model="$v.email.$model"
                            placeholder="Insert an email"></vue-select>
                <!--                    <div style="color:red" v-if="!$v.email.required && $v.email.$anyDirty">Email is required</div>-->
                <!--                    <div style="color:red" v-if="!$v.email.email && $v.email.$anyDirty">Invalid format of email</div>-->
            </div>

            <div class="col-md-12" v-if="type_movement=='t'">
                <br>
                <label>Source Description</label>
                <input type="text" v-model="source_description" class="form-control" id="source_description"
                       name="source_description" placeholder="Source Description">
            </div>

            <div class="col-md-12">
                <br>
                <button @click.prevent="!$v.$invalid ? registerMovement() : showErrors()" class="btn btn-primary">Add Expense</button>
            </div>
        </form>

    </div>
</template>

<script>
    import vueSelect from 'vue-select';
    import 'vue-select/dist/vue-select.css';
    import {
        required,
        numeric,
        email,
        minLength,
        maxLength,
        minValue,
        maxValue,
    } from "vuelidate/src/validators";

    export default {
        name: "registExpense",
        components: {
            vueSelect,
        },
        data: function () {
            return {
                title: 'Regist Expense',
                emails: [],
                categories: [],
                category_id: null,
                email: null,
                value: "",
                type_payment: "",
                type_movement: "",
                source_description: "",
                description: "",
                iban: "",
                mb_entity_code: "",
                mb_payment_reference: "",

            };
        },
        methods: {
            registerMovement() {
                axios.get('/api/wallets/id/' + this.$store.state.user.email)
                    .then(response => {
                        axios.post('/api/movements/expense', {
                            email: this.email,
                            value: this.value,
                            type_payment: this.type_payment,
                            source_description: this.source_description,
                            iban: this.iban,
                            start_balance: response.data[0].balance,
                            category_id: this.category_id.value,
                            type_movement: this.type_movement,
                            description: this.description,
                            mb_entity_code: this.mb_entity_code,
                            mb_payment_reference: this.mb_payment_reference
                            //end_balance: Number(response.data[0].balance)+Number(this.value), //Não é seguro estar aqui
                        })
                        .then(async (response) => {
                            await axios.get('/api/wallets/id/' + this.email)
                            .then(response => {
                                this.$socket.emit('balance-user',response.data[0].balance, response.data[0].id);
                            })
                            .catch(error => {
                                console.log(error);
                            });
                            this.$router.push('/');
                        })
                        .catch(error => {
                            console.log(error);
                        });
                    }).catch(error => {
                    console.log(error);
                });


            },
            showErrors() {
                this.$v.$touch();
            },
            fetchCategories(search, loading) {
                let self = this;
                loading(true);
                if(search){
                    axios.get('/api/movements/category/' + search)
                        .then(function (response) {
                            self.categories = [];
                            response.data.forEach((category) => self.categories.push({label:category[1],value:category[0]}));
                        })
                        .catch(function (error) {
                            console.log(error);
                        })
                        .finally(function () {
                            loading(false);
                        });
                }
            },
            fetchEmails(search, loading) {
                let self = this;
                loading(true);
                if(search) {
                    axios.get('/api/wallets/email/' + search)
                        .then(function (response) {
                            self.emails = response.data;
                        })
                        .catch(function (error) {
                            console.log(error);
                        })
                        .finally(function () {
                            loading(false);
                        });
                }
            },
        },
        validations: {
            email: {
                email
            },
            iban: {
                minLength: minLength(25), maxLength: maxLength(25)
            },
            value: {
                required, minValue: minValue(0.01), maxValue: maxValue(5000.00)
            },
            // type_payment: {
            //     required
            // },
            type_movement: {
                required
            },
            category_id: {
                required
            },
            mb_entity_code: {
                numeric, maxLength: maxLength(5), minLength: minLength(5)
            },
            mb_payment_reference: {
                numeric, maxLength: maxLength(9), minLength: minLength(9)
            }
        }
    }
</script>

<style scoped>

</style>

