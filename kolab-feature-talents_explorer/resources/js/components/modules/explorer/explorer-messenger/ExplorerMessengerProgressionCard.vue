<template>
    <div class="explorer-messenger-card explorer-messenger-progression-card">
        <button type="button" @click="toggleCard" class="card-header">
            <div class="picto-container"><span class="picto-explorer-shield"/></div>
            <h1 class="card-title">Etapes d’une mission Kolab</h1>
            <div class="card-dropdown-button">
                <span v-if="isCardOpen" class="picto-explorer-dropdown-up"/>
                <span v-else class="picto-explorer-dropdown-down"/>
            </div>
        </button>
        <div v-if="isFreelance" v-show="isCardOpen" class="card-body">
            <div class="progression-item" :class="{ active: activeStep===1 }">
                <span class="step-number">1</span>
                <span class="step-description">
                    Envoyez votre devis<br/> dès maintenant
                </span>
            </div>
            <div class="progression-item" :class="{ active: activeStep===2 }">
                <span class="step-number">2</span>
                <span class="step-description">
                    Une fois payé, envoyez les livrables <br/>dans le délai convenu
                </span>
            </div>
            <div class="progression-item" :class="{ active: activeStep===3 }">
                <span class="step-number">3</span>
                <span class="step-description">
                    Clôturez la mission et récupérez vos<br/> fonds lorsque le client la clôture aussi
                </span>
            </div>
        </div>
        <div v-else v-show="isCardOpen" class="card-body">
            <div class="progression-item" :class="{ active: activeStep===1 }">
                <span class="step-number">1</span>
                <span class="step-description">
                    Le freelance vous envoie<br/>
                    son devis
                </span>
            </div>
            <div class="progression-item" :class="{ active: activeStep===2 }">
                <span class="step-number">2</span>
                <span class="step-description">
                    Vous le réglez sur MangoPay, notre<br/>
                    partenaire de confiance.
                </span>
            </div>
            <div class="progression-item" :class="{ active: activeStep===3 }">
                <span class="step-number">3</span>
                <span class="step-description">
                    Le freelance vous envoie les livrables<br/> dans le délai convenu
                </span>
            </div>
            <button v-if="activeStep===4" type="button" @click="closeMission" class="progression-item" :class="{ active: activeStep===4 }">
                <span class="step-number">4</span>
                <span class="step-description">
                    Vous clôturez la mission, pour<br/> déclencher le paiement.
                </span>
            </button>
            <div v-else class="progression-item" :class="{ active: activeStep===4 }">
                <span class="step-number">4</span>
                <span class="step-description">
                    Vous clôturez la mission, pour<br/> déclencher le paiement.
                </span>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: ['user', 'missionStatus', 'isFreelance','missionProposition'],
    data() {
        return {
            isCardOpen: true
        }
    },

    mounted() {

    },
    computed: {
        activeStep: function () {
            switch (this.missionStatus) {
                case 'waiting_quote' :
                    return 1;
                case 'waiting_payment' :
                    return 2;
                case 'waiting_work' :
                    return this.isFreelance ? 2 : 3;
                case 'waiting_closing' :
                    return this.isFreelance ? 3 : 4;
                default :
                    return 0;
            }
        }
    },
    methods: {
        toggleCard() {
            this.isCardOpen = !this.isCardOpen;
        },
        closeMission() {
            axios.patch("/api/explorer/missions/propositions/" + this.missionProposition.id, {
                params: {
                    patch_type: 'mission_proposition_close'
                }
            }).then(res => {

            }).catch(error => console.log(error))
        }
    },
}
</script>
