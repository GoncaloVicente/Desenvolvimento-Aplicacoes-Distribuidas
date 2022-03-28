<template>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>Photo</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Balance</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <th>
                            <input type="text" class="search-input form-control" v-model="filter.name" placeholder="Search Name"/>
                        </th>
                        <th>
                            <input type="text" class="search-input form-control" v-model="filter.email" placeholder="Search Email"/>
                        </th>
                        <th>
                            <select v-model="filter.balance"  class="search-select form-control">
                                <option></option>
                                <option>Empty</option>
                                <option>Has Money</option>
                            </select>
                        </th>
                        <th>
                            <select v-model="filter.type"  class="search-select form-control">
                                <option></option>
                                <option value="a" >Administrator</option>
                                <option value="o" >Operator</option>
                                <option value="u" >User</option>
                            </select>
                        </th>
                        <th>
                            <select v-model="filter.status"  class="search-select form-control">
                                <option></option>
                                <option value="1">Active</option>
                                <option value="0">Disable</option>
                            </select>
                        </th>
                        <td>&nbsp;</td>
                    </tr>
                    </thead>
                    <tbody>
                    <tr
                        v-for="user in internalUser"
                        :key="user.id"
                        :class="{activerow: editingUser === user}"
                    >
                        <td v-if="user.photo"><img v-bind:src="'/storage/fotos/'+user.photo" class="rounded-circle" height=35px widht=35px/></td>
                        <td v-else><img v-bind:src="'/storage/fotos/user-default.png'" class="rounded-circle" height=35px widht=35px/></td>
                        <td>{{ user.name }}</td>
                        <td>{{ user.email }}</td>
                        <td v-if="user.balance">{{user.balance.balance >0 ? 'Has Money' : "Empty"}}</td>
                        <td v-else-if="user.balance==null"></td>
                        <td v-if="user.type == 'a'">Administrator</td>
                        <td v-if="user.type == 'o'">Operator</td>
                        <td v-if="user.type == 'u'">User</td>
                        <td v-if="user.active == 0">Disable</td>
                        <td v-if="user.active == 1">Active</td>
                        <td>
                            <a v-if="user.type == 'a' ||user.type == 'o'"
                               class="btn btn-sm btn-danger"
                               v-on:click.prevent="deleteUser(user)"
                            >Delete</a>
                            <a v-if="user.type == 'u' && user.active == 1"
                               class="btn btn-sm btn-danger"
                               v-on:click.prevent="disableUser(user)"
                            >Disable</a>
                            <a v-if="user.type == 'u' && user.active == 0"
                               class="btn btn-sm btn-danger"
                               v-on:click.prevent="activeUser(user)"
                            >Active</a>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-12">
                <paginate
                    :pageCount="pageCount"
                    :containerClass="'pagination'"
                    :clickHandler="clickCallback">
                </paginate>
            </div>
        </div>

    </div>

</template>

<script>
    export default {
        name: "userList",
        props: ['users','wallets','total'],
        data: function () {
            return {
                editingUser: null,
                internalUser : this.users,
                totalRecords : this.total,
                filter: { name: '', email:'', balance: '', type: '', status: '' },
            }
        },
        methods: {
            editUser: function (user) {
                this.editingUser = user;
                this.$emit('edit-click', user);
            },
            disableUser: function (user) {
                this.editingUser = null;
                this.$emit('disable-click', user);
            },
            activeUser: function (user) {
                this.editingUser = null;
                this.$emit('active-click', user);
            },
            deleteUser: function (user) {
                this.editingUser = null;
                this.$emit('delete-click', user);
            },
            clickCallback: function(page) {

                let variables = '&name='+this.filter.name+'&email='+this.filter.email+'&balance='+this.filter.balance+'&type='+this.filter.type+'&status='+this.filter.status;

                axios.get("api/users?page="+page + variables).then(response => {
                    this.internalUser = response.data.data;
                    this.totalRecords = response.data.total;
                });
            }
        },
        computed:{
            pageCount: function() {
                if(this.total){
                    let pagecount= Math.ceil(this.totalRecords/15);
                    return pagecount;
                }else{
                    return 0;
                }
            }
        },
        watch:{
            users: function (val) {
                this.internalUser = val;
            },
            total: function (val) {
                this.totalRecords = val;
            },
            filter: {
                deep: true,
                // We have to move our method to a handler field
                handler() {

                    let variables = 'name='+this.filter.name+'&email='+this.filter.email+'&balance='+this.filter.balance+'&type='+this.filter.type+'&status='+this.filter.status;

                    axios.get("api/users?"+variables).then(response => {
                        this.internalUser = response.data.data;
                        this.totalRecords = response.data.total;
                    });
                }
            },
        }
    }
</script>

<style scoped>
    tr.activerow {
        background: #123456 !important;
        color: #fff !important;
    }
</style>
