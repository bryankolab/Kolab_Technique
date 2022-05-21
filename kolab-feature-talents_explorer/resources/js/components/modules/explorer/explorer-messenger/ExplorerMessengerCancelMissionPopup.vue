<template>
    <div class="modal-mask">
        <div class="modal-wrapper">
            <div class="modal-container">
                <div class="explorer-quit-modal" style="width: 780px; height: 430px">
                    <div class="modal-header text-center">
                        <h1 class="modal-title">En annulant cette mission,<br/>
                            {{ missionProposition.freelance.firstname }} ne pourra plus vous envoyer de devis pour celle-ci.</h1>
                    </div>
                    <div class="explorer-modal-button-container">
                        <button class="explorer-modal-button" type="button" @click="cancelProposition">Oui, je confirme
                        </button>
                        <button class="explorer-modal-button explorer-modal-quit-button" type="button"
                                @click="cancelFullMission">Oui, et l'annuler pour les autres freelances contactés
                        </button>
                        <button class="explorer-modal-button explorer-modal-quit-button" type="button"
                                @click="emitCloseModal">Non, je ne veux pas annuler
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: ['missionProposition'],
    data() {
        return {}
    },
    mounted() {

    },
    computed: {},
    methods: {
        emitCloseModal() {
            this.$emit("modal-close");
        },

        cancelProposition() {
            axios.patch("/api/explorer/missions/propositions/" + this.missionProposition.id, {
                params: {
                    patch_type: 'mission_proposition_cancel'
                }
            }).then(res => {
                this.emitCloseModal();
            }).catch(error => console.log(error))
        },
        cancelFullMission() {
            axios.patch("/api/explorer/missions/propositions/" + this.missionProposition.id, {
                params: {
                    patch_type: 'mission_full_cancel'
                }
            }).then(res => {
                this.emitCloseModal();

            }).catch(error => console.log(error))
        }
    },
}
</script>
