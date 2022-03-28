<template>
    <div>
        <br>
        <div class="jumbotron">
            <div>
                <div>
                    <label> Category </label>
                    <select v-model="movement.category" class="search-select form-control">
                        <option v-for="category in categories" v-bind:value="category.name">
                            {{ category.name }}
                        </option>
                    </select>
                </div>

                <br>

                <div>
                    <label> Description </label>
                    <input type="text" class="form-control" placeholder="Description" v-model="movement.description"/>
                </div>
            </div>

            <br>

            <div class="form-group">
                <a class="btn btn-primary" v-on:click.prevent="saveMovement()">Save</a>
                <a class="btn btn-light" v-on:click.prevent="cancelEdit()">Cancel</a>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['movement', 'categories'],
        methods: {
            saveMovement() {
                this.$emit('save-movement', this.movement)
            },
            cancelEdit() {
                this.$emit('cancel-edit')
            }
        }
    }
</script>

<style scoped>
    .jumbotron {
        padding: 24px 30px 0;
    }
</style>
