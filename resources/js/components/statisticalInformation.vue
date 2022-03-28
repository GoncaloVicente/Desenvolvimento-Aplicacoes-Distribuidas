<template>
    <div>
        <div class="col-md-12">
            <bar-chart
                :id="4"
                :data="categories"
                :text="'Money per Category'"
            ></bar-chart>
        </div>
        <div class="col-md-12">
            <pie-chart
                :id="2"
                :data="type"
            ></pie-chart>
        </div>
    </div>
</template>
<script>

    import BarChart from "./BarChart.vue";
    import PieChart from "./PieChart.vue";
    export default {
        data: function () {
            return {
                categories: null,
                type: null,
            };
        },
        components: {
            'bar-chart': BarChart,
            'pie-chart': PieChart,
        },
        async created() {
            this.categories = (await axios.get("api/statics/categories")).data;
            this.type = (await axios.get("api/statics/type")).data;
        },
    };
</script>
