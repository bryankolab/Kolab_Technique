<template>
    <div class="modal-mask">
        <div class="modal-wrapper">
            <div class="modal-container">
                <div class="explorer-quit-modal">
                    <div class="modal-header text-center">
                        <h1 class="modal-title">Êtes vous sur de vouloir quitter ?</h1>
                        <p>Les informations que vous avez remplies ne seront pas enregistrées.</p>
                    </div>
                    <div class="explorer-modal-button-container">
                        <button class="explorer-modal-button" type="button" @click="emitCancel">Revenir en arrière
                        </button>
                        <button class="explorer-modal-button explorer-modal-quit-button" type="button" @click="emitValidate">Quitter</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: [],
    data() {
        return {}
    },

    mounted() {

    },
    computed: {},
    methods: {
        emitCloseModal() {
            this.$emit('close-modal');
        },
        emitValidate() {
            this.$emit('validate');
        },
        emitCancel() {
            this.$emit('cancel');
        }
    },
}
</script>
