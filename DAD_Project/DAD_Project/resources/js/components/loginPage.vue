<template>
    <div class="login-page">
        <div class="form">
            <form class="login-form">
                <input type="text" placeholder="Email" v-model.trim="$v.email.$model"/>
                <div style="color:red" v-if="!$v.email.required && $v.email.$anyDirty">Email is required</div>
                <input type="password" placeholder="Password" v-model="$v.password.$model"/>
                <div style="color:red" v-if="!$v.password.required && $v.password.$anyDirty">Password is required</div>
                <button @click.prevent="!$v.$invalid ? login() : showErrors()">login</button>
            </form>
            <br>
            <div style="color:red" v-if="!userExist">User credentials are invalid</div>
        </div>
    </div>
</template>

<script>
    //$v refers to Vuelidate's special object used for storing validation states
    import {required} from "vuelidate/src/validators";

    export default {
        data:function(){
            return{
                email:"",
                password:"",
                userExist:true
            }
        },
        methods:{
            async login(){
                this.userExist = await this.$root.login(this.email,this.password);
                if(this.userExist){
                    this.$router.push('/');
                }
            },
            showErrors(){
                this.$v.$touch();
            }
        },
        validations: {
            email: {
                required
            },
            password: {
                required
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
