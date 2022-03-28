<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Virtual Wallet</title>
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css"
          integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css"
          integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
</head>
<body>

<div class="container" id="app">
    <nav class="navbar navbar-default">
        <div class="container-fluid">
            <div class="navbar-header">
                <span class="navbar-brand">Virtual Wallet</span>
            </div>
            <ul class="nav navbar-nav">
                <li>
                    <router-link to="/">Home</router-link>
                </li>
                <li v-if="this.$store.getters.isLoggedIn && this.$store.getters.getType==='a'">
                    <router-link to="/users">Users</router-link>
                </li>
                <li v-if="this.$store.getters.isLoggedIn && this.$store.getters.getType==='a'">
                    <router-link to="/global/statistics">Global Statistics</router-link>
                </li>
                <li v-if="this.$store.getters.isLoggedIn && this.$store.getters.getType==='u'">
                    <router-link to="/regist/expense">Add Expense</router-link>
                </li>
                <li v-if="this.$store.getters.isLoggedIn && this.$store.getters.getType==='o'">
                    <router-link to="/regist/income">Add Income</router-link>
                </li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li v-if="!this.$store.getters.isLoggedIn">
                    <router-link to="/login">Login</router-link>
                </li>
                <li v-if="!this.$store.getters.isLoggedIn">
                    <router-link to="/register">Register</router-link>
                </li>
                <b-dropdown v-if="this.$store.getters.isLoggedIn && this.$store.state.user" id="dropdown" v-bind:text="this.$store.state.user.name" class="m-md-2">
                    <b-dropdown-item v-if="this.$store.getters.getType==='u'">
                        <a class="glyphicon glyphicon-user"></a>
                        <router-link to="/profile/edit">Profile</router-link>
                    </b-dropdown-item>
                    <b-dropdown-item v-if="this.$store.getters.getType==='u'">
                        <a class="glyphicon glyphicon-eur"></a>
                        <router-link to="/my/wallet">My Wallet</router-link>
                    </b-dropdown-item>
                    <b-dropdown-item  v-if="this.$store.getters.getType==='u'">
                        <a class="glyphicon glyphicon-stats"></a>
                        <router-link to="/statistics">Statistics</router-link>
                    </b-dropdown-item>
                    <b-dropdown-item>
                        <a class="glyphicon glyphicon-log-out"></a>
                        <router-link to="/" @click.native="logout()">Logout</router-link>
                    </b-dropdown-item>
                </b-dropdown>
            </ul>
        </div>
    </nav>

    <router-view></router-view>
</div>

<script src="js/vue.js"></script>
</body>
</html>
