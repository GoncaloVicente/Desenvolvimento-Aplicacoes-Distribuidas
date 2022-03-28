<template>
    <div>
        <div class="jumbotron" style="background-color: rgb(48,117,173);">
            <h1 style="text-align: center"><span style="color: white; ">{{title}}</span></h1>
        </div>

        <router-link to="/create/user" class="btn btn-sm btn-primary" style="font-size: medium;">Add User</router-link>
        <br><br>

        <user-list
            :users="users"
            :total="total"
            :wallets="wallets"
            @edit-click="editUser"
            @delete-click="deleteUser"
            @disable-click="disableUser"
            @active-click="activeUser"
            @message="childMessage"
            ref="usersListRef"
        ></user-list>

        <div class="alert alert-success" v-if="showSuccess">
            <button
                type="button"
                class="close-btn"
                v-on:click="showSuccess = false"
            >
                &times;
            </button>
            <strong>{{ successMessage }}</strong>
        </div>
<!--        <user-edit-->
<!--            :user="currentUser"-->
<!--            :departments="this.$store.state.departments"-->
<!--            @user-saved="savedUser"-->
<!--            @user-canceled="cancelEdit"-->
<!--            v-if="currentUser"-->
<!--        ></user-edit>-->
    </div>
</template>
<script type="text/javascript">
    import UserList from "./userList.vue";

    export default {
        components: {
            "user-list": UserList,
            // "user-edit": UserEdit
        },
        data: function () {
            return {
                title: "List Users",
                showSuccess: false,
                successMessage: "",
                currentUser: null,
                users: [],
                wallets: [],
                total:0,
            };
        },
        methods: {
            editUser: function (user) {
                this.currentUser = user;
                this.showSuccess = false;
            },
            activeUser: function (user) {
                axios.patch("api/users/active/" + user.id).then(response => {
                    this.showSuccess = true;
                    this.successMessage = "User Actived";
                    this.getUsers();
                });
            },
            disableUser: function (user) {
                axios.patch("api/users/disable/" + user.id).then(response => {
                    this.showSuccess = true;
                    this.successMessage = "User Disabled";
                    this.getUsers();
                });
            },
            deleteUser: function (user) {
                axios.delete("api/users/" + user.id).then(response => {
                    this.showSuccess = true;
                    this.successMessage = "User Deleted";
                    this.getUsers();
                });
            },
            savedUser: function () {
                this.currentUser = null;
                this.$refs.usersListRef.editingUser = null;
                this.showSuccess = true;
                this.successMessage = "User Saved";
            },
            cancelEdit: function () {
                this.currentUser = null;
                this.$refs.usersListRef.editingUser = null;
                this.showSuccess = false;
            },
            getUsers: function () {
                axios.get("api/users").then(response => {
                    this.users = response.data.data;
                    this.total = response.data.total;
                });
            },
            getWallets: function (){
                axios.get("api/wallets").then(response => {
                    this.wallets = response.data.data;
                });
            },
            childMessage: function (message) {
                this.showSuccess = true;
                this.successMessage = message;
            }
        },
        mounted() {
            this.getUsers();
            this.getWallets();
        }
    }
</script>

<style scoped></style>
