<template>
    <transition name="base-modal">
        <div class="modal-mask" v-show="show">
            <div class="modal-container">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">New message</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" @click="close" >&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <slot></slot>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary">Send message</button>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>

<script>
    export default {
        props: ['show'],

        mounted: function () {
            document.addEventListener("keydown", (e) => {
                if (this.show && e.keyCode == 27) {
                    this.close()
                }
            })
        },

        methods: {
            close: function() {
                this.$emit('close')
            }
        }
    }
</script>

<style scoped>
    * {
        box-sizing: border-box;
    }
    .modal-mask {
        position: fixed;
        z-index: 9998;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, .5);
        transition: opacity .3s ease;
        overflow-x: auto;
    }
    .modal-container {
        width: 75%;
        height: 75%;
        margin: 0px auto;
        padding: 20px 30px;
        background-color: #eee;
        border-radius: 2px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, .33);
        transition: all .3s ease;
    }
    .modal-body {
        margin: 20px 0;
    }
    .modal-enter {
        opacity: 0;
    }
    .modal-leave-active {
        opacity: 0;
    }
    .modal-enter .modal-container,
    .modal-leave-active .modal-container {
        -webkit-transform: scale(1.1);
        transform: scale(1.1);
    }
</style>
