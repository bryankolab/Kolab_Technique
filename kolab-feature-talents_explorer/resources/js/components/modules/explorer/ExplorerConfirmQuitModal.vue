<template>
    <div class="modal-mask" style="top: 0; height: 75%; z-index: 99999">
        <div class="modal-wrapper" id="confirm-quit-modal-wrapper" style="vertical-align: middle" @click="handleCloseModal">
            <div class="modal-container" id="confirm-quit-modal-container">
                <div class="confirm-quit-modal-container">
                    <div class="confirm-quit-modal-header">
                        <h1>Êtes vous sur de vouloir quitter ?</h1>
                        <p>Les informations que vous avez remplies ne seront pas enregistrées</p>
                    </div>
                    <div class="confirm-quit-modal-body">
                        <button class="go-back-button" type="button" @click="emitGoBack">Revenir en arrière</button>
                        <button class="quit-button" type="button" @click="emitQuit">Quitter</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    methods: {
        emitGoBack() {
            console.log('emitGoBack')
            this.$emit('emit-go-back');
        },
        emitQuit() {
            console.log('emitQuit')

            this.$emit('emit-quit');
        },
        handleCloseModal() {
            console.log(event.target.id)
            if (event.target.id === "confirm-quit-modal-wrapper" || "confirm-quit-modal-container") {
                this.emitGoBack();
            }
        },
    }
}
</script>
