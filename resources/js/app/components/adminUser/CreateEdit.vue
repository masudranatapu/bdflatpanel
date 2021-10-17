<template>
    <div>
        <base-modal :show="show" @close="close">
            <!--The elements that we wanted to be shown or used.-->
            <form>
                <div class="form-group">
                    <label for="recipient-name" class="col-form-label">Recipient:</label>
                    <input type="text" class="form-control" id="recipient-name">
                </div>

                <div class="form-group">
                    <label for="recipient-name" class="col-form-label">ID: {{id}}</label>
                    <label for="recipient-name" class="col-form-label">Recipient:</label>
                    <input type="text" class="form-control" id="recipient-names">
                </div>

                <div class="form-group">
                    <label for="message-text" class="col-form-label">Message:</label>
                    <textarea class="form-control" id="message-text"></textarea>
                </div>
            </form>
        </base-modal>
    </div>
</template>

<script>
    import BaseModal from '../shared/BaseModal.vue'
    export default {
        props: ['show', 'id'],
        components: { BaseModal },
        mounted() {

        },
        methods: {
            close: function() {
                this.$emit('close')
            }
        }
    }
</script>