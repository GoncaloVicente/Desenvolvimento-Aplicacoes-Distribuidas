<template>
    <div class="login-page">
        <div class="form">
            <form class="login-form" enctype="multipart/form-data">
                <input type="text" placeholder="Name" v-model="$v.name.$model"/>
                <div style="color:red" v-if="!$v.name.required && $v.name.$anyDirty">Name is required</div>
                <div style="color:red" v-if="!$v.name.spacesLetters && $v.name.$anyDirty">Name must only include spaces and letters</div>
                <input type="text" placeholder="Email" v-model.trim="$v.email.$model"/>
                <div style="color:red" v-if="!$v.email.required && $v.email.$anyDirty">Email is required</div>
                <div style="color:red" v-if="!$v.email.email && $v.email.$anyDirty">Invalid format of email</div>
                <input type="password" placeholder="Password" v-model="$v.password.$model"/>
                <div style="color:red" v-if="!$v.password.required && $v.password.$anyDirty">Password is required</div>
                <div style="color:red" v-if="!$v.password.minLength && $v.password.$anyDirty">Password must have 3 or more characters</div>
                <input type="text" placeholder="NIF" v-model.trim="$v.nif.$model" maxlength="9"/>
                <div style="color:red" v-if="!$v.nif.required && $v.nif.$anyDirty">NIF is required</div>
                <div style="color:red" v-if="!$v.nif.maxLength && $v.nif.$anyDirty">NIF must have 9 numbers</div>
                <div style="color:red" v-if="!$v.nif.numeric && $v.nif.$anyDirty">NIF must only include numbers</div>
                <div style="color:red" v-if="!$v.nif.minLength && $v.nif.$anyDirty">NIF must have 9 numbers</div>
                <input type="file" name="photo" @change="onFileChange($event)">
                <button @click.prevent="!$v.$invalid ? register() : showErrors()">Create account</button>
            </form>
            <br>
            <div style="color:red" v-if="errors">{{errorMsg}}</div>
        </div>
    </div>
</template>

<script>
    import {required,numeric,email,minLength,maxLength} from "vuelidate/src/validators";

    const spacesLetters = (value) => /^[A-Za-záàâãéèêíóôõúçÁÀÂÃÉÈÍÓÔÕÚÇ ]+$/.test(value);

    export default {
        data:function(){
            return{
                name:"",
                email:"",
                password:"",
                nif:"",
                photo:"",
                errors:false,
                errorMsg:""
            }
        },
        methods:{
            register(){
                const config = {
                    headers: { 'content-type': 'multipart/form-data' }
                };

                let formData = new FormData();
                formData.append('photo', this.photo);
                formData.append('name', this.name);
                formData.append('email', this.email);
                formData.append('password', this.password);
                formData.append('nif', this.nif);

                axios.post('/api/users',formData,config)
                    .then(response=> {
                        axios.post('/api/wallets',{id:response.data.id,email:this.email,balance:50})
                            .then(response=> {
                                this.$root.login(this.email,this.password);
                                this.$router.push('/');
                            })
                            .catch(error=> {
                            });
                    })
                    .catch(error=> {
                        if(error.response.data.errors.photo !== undefined){
                            this.errorMsg = error.response.data.errors.photo.toString().replace('.',"");
                            this.errors = true;
                        }else if(error.response.data.errors.email !== undefined){
                            this.errorMsg = error.response.data.errors.email.toString().replace('.',"");
                            this.errors = true;
                        }else if(error.response.data.errors.nif !== undefined){
                            this.errorMsg = error.response.data.errors.nif.toString().replace('.',"");
                            this.errors = true;
                        }else if(error.response.data.errors.name !== undefined){
                            this.errorMsg = error.response.data.errors.name.toString().replace('.',"");
                            this.errors = true;
                        }else if(error.response.data.errors.password !== undefined){
                            this.errorMsg = error.response.data.errors.password.toString().replace('.',"");
                            this.errors = true;
                        }
                    });
            },
            showErrors(){
                this.$v.$touch();
            },
            onFileChange(e){
                this.photo = e.target.files[0];
                console.log(this.photo);
            }
        },
        validations: {
            name: {
                required, spacesLetters
            },
            email: {
                required, email
            },
            password: {
                required, minLength:minLength(3)
            },
            nif: {
                required, maxLength:maxLength(9), numeric, minLength:minLength(9)
            }
        }
    }
</script>

<style scoped>
    .login-page {
        width: 360px;
        padding: 8% 0 0;
        margin: auto;
    }
    .form {
        position: relative;
        z-index: 1;
        background: #FFFFFF;
        max-width: 360px;
        margin: 0 auto 100px;
        padding: 45px;
        text-align: center;
        box-shadow: 0 0 20px 0 rgba(0, 0, 0, 0.2), 0 5px 5px 0 rgba(0, 0, 0, 0.24);
    }
    .form input {
        font-family: "Roboto", sans-serif;
        outline: 0;
        background: #f2f2f2;
        width: 100%;
        border: 0;
        margin: 0 0 15px;
        padding: 15px;
        box-sizing: border-box;
        font-size: 14px;
    }
    .form button {
        font-family: "Roboto", sans-serif;
        text-transform: uppercase;
        outline: 0;
        background: rgb(48,117,173);
        width: 100%;
        border: 0;
        padding: 15px;
        color: #FFFFFF;
        font-size: 14px;
        cursor: pointer;
    }
</style>
