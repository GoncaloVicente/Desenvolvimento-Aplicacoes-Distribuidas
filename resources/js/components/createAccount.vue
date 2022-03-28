<template>
    <div class="container">
        <form>
            <div class="jumbotron" style="background-color: rgb(48,117,173);">
                <h1 style="text-align: center"><span style="color: white; ">{{title}}</span></h1>
            </div>
            <div class="col-md-4">
                <label>Photo</label>
                <br/><br/>
                <input type="file" name="photo" @change="onFileChange($event)">
            </div>
            <div class="col-md-8">
                <div class="col-md-12">
                    <label>Type of User</label>
                    <select name="type" id="type" class="form-control" v-model="$v.type.$model">
                        <option disabled selected> -- Select an option --</option>
                        <option value="a">Administrator</option>
                        <option value="o">Operator</option>
                    </select>
                    <div style="color:red" v-if="!$v.type.required && $v.type.$anyDirty">Type is required</div>
                </div>
                <div class="col-md-12">
                    <label>Name</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="Name"
                           v-model="$v.name.$model">
                    <div style="color:red" v-if="!$v.name.required && $v.name.$anyDirty">Name is required</div>
                    <div style="color:red" v-if="!$v.name.spacesLetters && $v.name.$anyDirty">Name must only include
                        spaces and letters
                    </div>
                </div>
                <div class="col-md-12">
                    <label>Password</label>
                    <input type="text" class="form-control" id="password" name="password" placeholder="Password"
                           v-model="$v.password.$model">
                    <div style="color:red" v-if="!$v.password.required && $v.password.$anyDirty">Password is required
                    </div>
                    <div style="color:red" v-if="!$v.password.minLength && $v.minLength.$anyDirty">Password must be
                        greater than 3
                    </div>
                </div>
                <div class="col-md-12">
                    <label>Email</label>
                    <input type="text" class="form-control" id="email" name="email" placeholder="Email"
                           v-model="$v.email.$model">
                    <div style="color:red" v-if="!$v.email.required && $v.email.$anyDirty">Email is required</div>
                    <div style="color:red" v-if="!$v.email.email && $v.email.$anyDirty">Email format is invalid</div>
                </div>
            </div>
            <div class="col-md-12">
                <br>
                <button @click.prevent="!$v.$invalid ? createUser() : showErrors()" class="btn btn-primary">Add
                    Registry
                </button>
            </div>
        </form>
    </div>
</template>

<script>
    import {required, email, minLength} from "vuelidate/src/validators";

    const spacesLetters = (value) => /^[A-Za-záàâãéèêíóôõúçÁÀÂÃÉÈÍÓÔÕÚÇ ]+$/.test(value);

    export default {
        name: "createAcount",
        data: function () {
            return {
                title: 'Create User',
                type: "",
                name: "",
                photo: "",
                password: "",
                email: "",
            };
        }, methods: {
            createUser() {
                const config = {
                    headers: { 'content-type': 'multipart/form-data' }
                };

                let formData = new FormData();
                formData.append('photo', this.photo);
                formData.append('type', this.type);
                formData.append('name', this.name);
                formData.append('password', this.password);
                formData.append('email', this.email);

                axios.post('/api/user/create', formData, config)
                    //{
                    // type: this.type,
                    // name: this.name,
                    // password: this.password,
                    // email: this.email,
                    //})
                    .then(response => {
                        this.$router.push('/');
                    })
                    .catch(error => {
                        console.log(error);
                    });
            },
            showErrors() {
                this.$v.$touch();
            },
            onFileChange(e){
                this.photo = e.target.files[0];
            }
        }
        , validations: {
            email: {
                required, email
            },
            name: {
                required, spacesLetters
            },
            password: {
                required, minLength: minLength(3)
            },
            type: {
                required
            },
        }
    }
</script>

<style scoped>

</style>
