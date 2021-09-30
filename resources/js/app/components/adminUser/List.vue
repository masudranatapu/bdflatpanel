<template>
    <div>
        <div class="card">
            <div class="card-header"> Page title
                <div class="float-sm-right">
                    <div>
                        <create-admin-user
                                :show="showModal(id)"
                                @close="toggleModal(id)" :id="id" />
                        <a class="text-sm" href="#" @click.stop="toggleModal(id)">Create New</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Name</th>
                        <th scope="col">Email</th>
                        <th scope="col">Mobile</th>
                        <th scope="col">Group</th>
                        <th scope="col">Role</th>
                        <th scope="col">Status</th>
                        <th scope="col">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="user in users" :key="user.user_id">
                        <th scope="row">{{ user.user_id }}</th>
                        <td>{{ user.first_name }} {{ user.last_name }}</td>
                        <td>{{ user.email }}</td>
                        <td>{{ user.mobile_no }}</td>
                        <td>{{ user.role_group }}</td>
                        <td>{{ user.role_name }}</td>
                        <td> {{ user.status }}</td>
                        <td>
                            <div class="table-button-container">
                                <button class="btn btn-warning btn-sm" @click.stop="toggleModal(user.user_id)">
                                    <span class="glyphicon glyphicon-pencil"> </span> Edit </button>&nbsp;&nbsp;
                                <button class="btn btn-danger btn-sm">
                                    <span class="glyphicon glyphicon-trash"> </span> Delete</button>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <div class="card-footer">
                Footer
            </div>
        </div>
    </div>
</template>

<script>
    import CreateAdminUser from './CreateEdit.vue'
    import {getPaginatedList} from '../../services/admin-user'

    export default {
        components: { CreateAdminUser },
        data() {
            return {
                users: null,
                activeModal: 0,
                pending: true,
                error: null,
                id: 1
            }
        },

        mounted() {
            this.getData();
        },

        methods: {
            showModal: function(id) {
                return this.activeModal === id
            },

            toggleModal: function (id) {
                this.id = id
                if(this.activeModal !== 0) {
                    this.activeModal = 0
                    return false
                }
                this.activeModal = id
            },

            edit: function (id) {
                this.id = id
                this.toggleModal(id)
            },

            async getData() {
                let loader = Vue.$loading.show();

                const res = await getPaginatedList().catch(err => { console.log('User: ' + err)});
                setTimeout(() => loader.hide(), 1 * 1000);
                if (res.success) {
                    let result = res.data;
                    if (result.hasOwnProperty('data')) {
                        result = result.data;
                    }
                    this.users = result;
                } else {
                    this.users = null;
                }
            }
        }
    }
</script>