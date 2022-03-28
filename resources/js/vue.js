require('./bootstrap');

window.Vue = require('vue');

import VueRouter from 'vue-router';

import BootstrapVue from 'bootstrap-vue';

Vue.use(BootstrapVue);
import Login from './components/loginPage';
import InitialPage from './components/initialPage';
import registarMovimento from "./components/registarMovimento";
import EditProfile from './components/editProfile';
import Register from './components/registerPage';
import CreateAccount from './components/createAccount';
import UserComponent from "./components/user.vue";
import MyWallet from "./components/myWallet";
import StatisticalInformation from "./components/statisticalInformation";
import GlobalStatics from "./components/globalStatics.vue";
import RegistExpense from "./components/registExpense";

import Paginate from 'vuejs-paginate';
Vue.use(VueRouter);

import Vuelidate from 'vuelidate';

Vue.use(Vuelidate);

import Vuex from 'vuex';
Vue.component('paginate', Paginate);
Vue.use(Vuex);

import VueSocketIO from "vue-socket.io";

Vue.use(new VueSocketIO({
    debug:true,
    connection:'http://localhost:8081'
}));

const store = new Vuex.Store({
    state: {
        token: sessionStorage.getItem('token') || null,
        user: JSON.parse(sessionStorage.getItem('user')) || null
    },
    mutations: {
        defineToken(state, token) {
            state.token = token;
            sessionStorage.setItem('token', token);
            axios.defaults.headers.common.Authorization = "Bearer " + token;
        },
        destroyToken(state) {
            sessionStorage.removeItem('token');
            state.token = null;
            axios.defaults.headers.common.Authorization = undefined;
        },
        defineUser(state, user) {
            state.user = user;
            sessionStorage.setItem('user', JSON.stringify(user));
        },
        destroyUser(state) {
            sessionStorage.removeItem('user');
            state.user = null;
        },
        loadHeader(state) {
            if (state.token) {
                axios.defaults.headers.common.Authorization = "Bearer " + state.token;
            } else {
                axios.defaults.headers.common.Authorization = undefined;
            }
        }
    },
    getters: {
        isLoggedIn(state) {
            return state.token != null;
        },
        getType(state){
            return state.user.type;
        }
    }
});

const isAuth = function (next) {
    if (!store.getters.isLoggedIn) {
        next('/login');
    }
    else{
        next();
    }
};

const isOperator = function (next) {
    if (!store.getters.isLoggedIn) {
        next('/login');
    }
    else{
        if(store.state.user.type=='o'){
            next();
        }else{
            next(false);
        }
    }
};

const isUser = function (next) {
    if (!store.getters.isLoggedIn) {
        next('/login');
    }
    else{
        if(store.state.user.type=='u'){
            next();
        }else{
            next(false);
        }
    }
};

const isAdmin = function (next) {
    if (!store.getters.isLoggedIn) {
        next('/login');
    }
    else{
        if(store.state.user.type=='a'){
            next();
        }else{
            next(false);
        }
    }
};

const isGuest = function (next) {
    if (store.getters.isLoggedIn) {
        next('/');
    }
    else{
        next();
    }
};

const routes = [
    {path: '/', component: InitialPage},
    {path: '/regist/income', component: registarMovimento, beforeEnter: (to, from, next) => isOperator(next)},
    {path: '/login', component: Login, beforeEnter: (to, from, next) => isGuest(next)},
    {path: '/profile/edit', component: EditProfile, beforeEnter: (to, from, next) => isAuth(next)},
    {path: '/user/edit', component: EditProfile, beforeEnter: (to, from, next) => isAuth(next)},
    {path: '/register', component: Register, beforeEnter: (to, from, next) => isGuest(next)},
    {path: '/create/user', component: CreateAccount, beforeEnter: (to, from, next) => isAdmin(next)},
    {path: '/users', component: UserComponent, beforeEnter: (to, from, next) => isAdmin(next)},
    {path: '/my/wallet', component: MyWallet, beforeEnter: (to, from, next) => isUser(next)},
    {path: '/statistics', component: StatisticalInformation, beforeEnter: (to, from, next) => isUser(next)},
    {path: '/global/statistics', component: GlobalStatics, beforeEnter: (to, from, next) => isAdmin(next)},
    {path: '/regist/expense', component: RegistExpense, beforeEnter: (to, from, next) => isUser(next)},
];

const router = new VueRouter({
    routes
});

const app = new Vue({
    el: '#app',
    store,
    router,
    methods: {
        logout() {
            if (this.$store.getters.isLoggedIn) {
                axios.post("api/logout")
                    .then(response => {
                        this.$socket.emit('logout', this.$store.state.user);
                        this.$store.commit('destroyToken');
                        this.$store.commit('destroyUser');
                        if (this.$route.path != '/') {
                            this.$router.push('/');
                        }
                    })
                    .catch(error => {
                        this.$store.commit('destroyToken');
                        this.$store.commit('destroyUser');
                        if (this.$route.path != '/') {
                            this.$router.push('/');
                        }
                    });
            }
        },
        login(email, password) {
            return axios.post('/api/login', {email: email, password: password})
                .then((response) => {
                    this.$store.commit('defineToken', response.data.access_token);
                    axios.get("api/users/me")
                        .then(response => {
                            this.$store.commit("defineUser", response.data.data);
                            this.$socket.emit('login', response.data.data);
                        })
                        .catch(error => {
                            this.$store.commit("destroyToken");
                            this.$store.commit("destroyUser");
                        });
                    return true;
                })
                .catch(error => {
                    console.log(error);
                    return false;
                });
        }
    },
    created() {
        this.$store.commit("loadHeader");
        if(this.$store.state.user){
            this.$socket.emit('login',this.$store.state.user);
        }
    }
});
