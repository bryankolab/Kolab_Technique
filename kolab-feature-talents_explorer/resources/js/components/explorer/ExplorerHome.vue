<template>
    <div class="page_explorer_home" id="vue__explorer_home">
        <div class="main-content">
            <h1 v-if="isFreelance">Rejoignez le premier cercle<br>
                des <span>meilleurs freelances <br>monteurs</span> du monde.
            </h1>
            <h1 v-else>Plus de doutes.<br>
                <span>les meilleurs freelances de la <br>post-prod</span> sont ici.
            </h1>
            <div class="input-container">
                <input v-model="accessCode" class="password-input" type="text"
                       placeholder="Veuillez saisir un code d’accès."/>
                <button class="validate-button" @click="sendForm" type="button">Valider</button>
            </div>
            <p v-if="errorMessage!== null" style="color: red">{{ errorMessage }}</p>
            <a href="/explorer/membership" class="inscription-link">Obtenir un code d'accès</a>
        </div>

        <div class="gradient"/>
    </div>
</template>
<script>
export default {
    props: ['user'],
    data() {
        return {
            accessCode: '',
            errorMessage: null
        }
    },

    mounted() {
    },
    computed: {
        isFreelance: function () {
            return this.user.role.name === "talent";
        },
    },
    methods: {
        sendForm() {
            axios.post('/api/explorer/unlock-access', {
                params: {
                    access_code: this.accessCode
                }
            }).then(res => {
                window.location.href = "/explorer";
            }).catch(
                error => {
                    this.handleError(error);
                }
            );
        },
        handleError(error) {
            let response = error.response;
            if (response.status === 403) {
                if (response.data === 'Explorer Registration not validated') {
                    this.errorMessage = "Votre inscription en cours de validation, merci de patienter";
                } else if (response.data === "Explorer access code not valid") {
                    this.errorMessage = "Code invalide, veuillez réessayer"
                }
            }

            if (response.status === 500) {
                this.errorMessage = "Une erreur est survenue, veuillez nous contacter";
            }
        }
    },
}
</script>
