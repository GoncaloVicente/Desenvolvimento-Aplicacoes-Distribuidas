<template>
    <div>
        <div class="col-md-12">
            <bar-chart
                :id="4"
                :data="categories"
                :text="'Busy Money per Category'"
            ></bar-chart>
        </div>
        <div class="col-md-12">
            <pie-chart
                :id="2"
                :data="type"
            ></pie-chart>
        </div>
        <div class="col-md-12">
            <user-chart
                :id="3"
                :data="usersPerType"
            ></user-chart>
        </div>
        <div class="col-md-12">
            <money-chart
                :id="5"
                :data="moneyForMonth"
                :text="'Busy Money per Month'"
            ></money-chart>
        </div>
        <div class="col-md-12">
            <year-chart
                :id="6"
                :data="moneyForYear"
                :text="'Busy Money per Year'"
            ></year-chart>
        </div>
    </div>
</template>
<script>

    import BarChart from "./BarChart.vue";
    import PieChart from "./PieChart.vue";
    import UserChart from "./totalUsersChart";
    import MoneyChart from "./staticsMoneyForMonth";
    import YearChart from "./staticsMoneyForYear";
    export default {
        data: function () {
            return {
                categories: null,
                type: null,
                usersPerType: null,
                moneyForMonth: null,
                moneyForYear:null,
            };
        },
        components: {
            'bar-chart': BarChart,
            'pie-chart': PieChart,
            'user-chart': UserChart,
            'money-chart': MoneyChart,
            'year-chart': YearChart
        },
        async created() {
            this.categories = (await axios.get("api/global/statics/categories")).data;
            this.type = (await axios.get("api/global/statics/type")).data;
            this.usersPerType = (await axios.get("api/global/statics/usersPerType")).data;
            this.moneyForMonth = (await axios.get("api/global/statics/moneyForMonth")).data;
            this.moneyForYear = (await axios.get("api/global/statics/moneyForYear")).data;
        },
    };
</script>
