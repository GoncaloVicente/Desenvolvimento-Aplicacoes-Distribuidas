<template>
    <div>
        <div class="jumbotron" style="background-color: rgb(48,117,173);">
            <h1 style="text-align: center"><span style="color: white; ">Edit User</span></h1>
        </div>

        <form>
            <div>
                <div v-if="has_errors">
                    <div v-for="error in errors" v-bind:key="error.key" class="alert alert-danger">
                        <span>{{ error }}</span>
                    </div>
                </div>
                <div class="col-md-5" align="middle">
                    <img v-if="this.$store.state.user.photo" v-bind:src="photo" alt="User Photo" style="border:1px solid black;" width="50%">
                    <img v-else="" v-bind:src="defaultPhoto" alt="User Photo" style="border:1px solid black;" width="50%">

                    <br><br>
                    <button onclick="document.getElementById('photo').click()" style="width: 25%;"> New Photo </button>
                    <button v-if="this.photo !== this.defaultPhoto" v-on:click.prevent="removePhoto"> Remove Photo </button>

                    <input id="photo" accept=".jpg" type="file" style="display:none" @change="uploadNewPhoto($event)">
                </div>

                <div class="col-md-5">
                    <br>
                    <br>

                    <div>
                        <label> Name </label>
                        <input id="inputName" type="text" class="form-control" placeholder="Name" v-model="name"/>
                        <div class="error" v-if="!$v.name.required"> Name is required </div>
                    </div>

                    <br>

                    <div v-if="this.$store.getters.isLoggedIn && this.$store.state.user.type !== 'a'">
                        <label for="inputNIF">NIF</label>
                        <input id="inputNIF" type="text" class="form-control" placeholder="NIF" v-model="nif"/>
                        <div class="error" v-if="!$v.nif.minLength || !$v.nif.maxLength || !$v.nif.numeric"> NIF must have 9 numbers </div>
                    </div>
                    <br>
                    <div align="right">
                        <a href="" v-on:click.prevent="changePassword()"> {{ passwordLinkText }} </a>
                    </div>


                    <div v-if="changing">
                        <div>
                            <label for="inputOldPassword"> Old Password </label>
                            <input id="inputOldPassword" type="password" class="form-control" placeholder="Old Password" v-model="oldPassword"/>
                            <div class="error" v-if="!$v.oldPassword.required"> Old Password is required </div>
                        </div>
                        <br>
                        <div>
                            <label for="inputNewPassword"> New Password </label>
                            <input id="inputNewPassword" type="password" class="form-control" placeholder="New Password" v-model="newPassword"/>
                            <div class="error" v-if="!$v.newPassword.required"> New Password is required </div>
                            <div class="error" v-if="!$v.newPassword.minLength"> New Password must be at least 3 characters </div>
                        </div>
                        <br>
                        <div>
                            <label for="inputConfPassword"> Confirm Password </label>
                            <input id="inputConfPassword" type="password" class="form-control" placeholder="Confirm Password" v-model="confPassword"/>
                            <div class="error" v-if="!$v.confPassword.required"> Confirmation Password is required </div>
                            <div class="error" v-if="!$v.confPassword.sameAsNewPassword"> Confirm Password must match with New Password </div>
                        </div>
                        <br>
                    </div>

                    <div>
                        <button @click.prevent="!$v.$invalid ? updateUser() : showErrors()" class="btn btn-primary"> Save </button>
                        <button @click.prevent="reset()" class="btn btn-info"> Reset </button>
                        <button v-on:click.prevent="cancel()" class="btn btn-light" > Cancel </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
    import {required, minLength, maxLength, numeric, sameAs, requiredIf} from "vuelidate/src/validators";

    const spacesLetters = (value) => /^[A-Za-záàâãéèêíóôõúçÁÀÂÃÉÈÍÓÔÕÚÇ]+$/.test(value);

    export default {
        data: function () {
            return {
                id: this.$store.state.user.id,
                name: this.$store.state.user.name,
                photo: '/storage/fotos/' + this.$store.state.user.photo,
                newPhoto: this.$store.state.user.photo,
                defaultPhoto: 'user-default.png',
                nif: this.$store.state.user.nif,
                passwordLinkText: "Change password",
                changing: false,
                oldPassword: "",
                newPassword: "",
                confPassword: "",
                errors: [],
                has_errors: false
            }
        },
        methods:{
            changePassword: function(){
                if(this.changing){
                    this.changing = false;
                    this.passwordLinkText = "Change password";
                } else{
                    this.changing = true;
                    this.passwordLinkText = "Don't change password";
                }
                this.oldPassword = "";
                this.newPassword = "";
                this.confPassword = "";
            },
            uploadNewPhoto(event) {
                // this.photo = event.target.files[0];
                this.newPhoto = event.target.files[0];
                console.log(this.newPhoto);
            },
            removePhoto() {
                this.photo = '/storage/fotos/' + this.defaultPhoto;
                this.newPhoto = this.defaultPhoto;
            },
            updateUser() {
                axios.put(`/api/user/${this.id}`, {
                    name: this.name,
                    nif: this.nif,
                    photo: this.newPhoto,
                    newPassword: this.newPassword,
                    oldPassword: this.oldPassword
                }, {
                    headers: {
                        Authorization: "Bearer " + this.$store.state.access_token
                    }
                })
                    .then(response => {
                        this.$store.commit("defineUser", response.data.data);
                        this.$router.push('/profile');
                    })
                    .catch(error => {
                        this.has_errors = true;
                        this.errors = error.response.data.errors;
                    });
            },
            showErrors() {
                this.$v.$touch();
            },
            reset() {
                this.name = this.$store.state.user.name;
                this.photo = '/storage/fotos/' + this.$store.state.user.photo;
                this.newPhoto = '/storage/fotos/' + this.$store.state.user.photo;
                this.nif = this.$store.state.user.nif;
                this.oldPassword = "";
                this.newPassword = "";
                this.confPassword = "";

            },
            cancel() {
                this.$router.push('/profile');
            }
        },
        validations: {
            name: {
                required
            },
            nif: {
                minLength: minLength(9),
                maxLength: maxLength(9),
                numeric
            },
            oldPassword: {
                required: requiredIf(function() {
                    return this.changing;
                }),
            },
            newPassword: {
                required: requiredIf(function() {
                    return this.changing;
                }),
                minLength: minLength(3)
            },
            confPassword: {
                required: requiredIf(function() {
                    return this.changing;
                }),
                sameAsNewPassword: sameAs("newPassword")
            }
        }
    };
</script>

<style>
    .btn {
        margin-bottom: 24px;
        margin-top: 10px;
    }
    .error {
        color: red;
        font-style: italic;
    }
</style>
