<template>
    <div class="modal-mask">
        <div class="modal-wrapper">
            <div v-if="!isPropositionSended" v-on:click="emitCloseModal" class="modal-container">
                <div class="explorer-form-modal explorer-project-propose-modal">
                    <div class="modal-header text-center">
                        <h1 class="modal-title">Proposer une nouvelle mission</h1>
                        <p>Vous pouvez proposer une nouvelle mission ou bien reproposer vos anciennes missions.</p>
                    </div>

                    <form method="post" v-on:submit.prevent="postMissionProposition"
                          class="explorer-form explorer-form-project-propose">
                        <div class="form-section">
                            <div class="form-title-container">
                                <div class="picto-container"><span class="picto-explorer-pile-white"></span></div>
                                <h2>Titre de votre projet*</h2>
                            </div>
                            <div class="form-section-content">
                                <select v-model="mission.name" v-select2 class="filter-field js-mission-data"
                                        name="mission-name"></select>
                            </div>
                        </div>

                        <div class="form-section">
                            <div class="form-title-container">
                                <div class="picto-container"><span class="picto-explorer-pile-white"></span></div>
                                <h2>Budget et deadline*</h2>
                            </div>
                            <div class="form-section-content">
                                <select required v-model="mission.budget">
                                    <option value="" disabled>Votre budget</option>
                                    <option>Moins de 100€</option>
                                    <option>Entre 100€ et 500€</option>
                                    <option>Entre 500€ et 1000€</option>
                                    <option>Plus de 1000€</option>
                                </select>

                                <select required v-model="mission.deadline">
                                    <option value="" disabled>Votre deadline</option>
                                    <option>Dès maintenant</option>
                                    <option>Dans la semaine</option>
                                    <option>Dans 2 semaines</option>
                                    <option>Dans le mois</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-section">
                            <div class="form-title-container">
                                <div class="picto-container"><span class="picto-explorer-pile-white"></span></div>
                                <h2>Détail de la mission</h2>
                            </div>
                            <div class="form-section-content">
                                <select required v-model="mission.type">
                                    <option value="" disabled>Type de mission</option>
                                    <option>VFX</option>
                                    <option>Montage</option>
                                    <option>Motion Design</option>
                                    <option>Etalonnage</option>
                                </select>
                                <textarea required v-model="mission.description"
                                          placeholder="Donnez plus de détails sur la mission à effectuer..."/>
                            </div>
                        </div>

                        <button type="submit" class="form-validation-btn">Envoyer</button>
                    </form>
                </div>
            </div>
            <div v-else class="explorer-mission-modal-validate">
                <img src="/images/explorer/mission-propose-validate.png" class="mission-modal-validate-icon"
                     alt="Validation icon"/>
                <h1 class="mission-modal-validate-text">Votre demande de mission
                    a bien été envoyée !</h1>
                <button class="mission-modal-validate-button" type="button" @click="redirectToMessenger">Afficher la
                    conversation
                </button>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: ['userToPropose'],

    data() {
        return {
            mission: {
                name: '',
                budget: '',
                deadline: '',
                type: '',
                description: ''
            },
            proposeToUserId: this.userToPropose.id,
            isPropositionSended: false,
            clientProposedMission: null
        }
    },
    created() {
    },
    mounted() {
        this.setMissionSelect();
    },
    methods: {
        postMissionProposition() {
            axios.post('/api/explorer/missions/propositions', {
                    params: {
                        mission_proposition: {
                            mission: this.mission,
                            propose_to_user_id: this.proposeToUserId
                        }
                    }
                }
            ).then(res => {
                if (res.success === false) {
                    // Error
                } else {
                    this.isPropositionSended = true;
                }
            }).catch(error => {
                console.log(error)
            });
        },
        emitCloseModal() {
            if (event.target.className === "modal-container") {
                if (this.isPropositionSended) {
                    this.redirectToExplorer();
                } else {
                    this.$emit('close-modal');
                }
            }
        },
        redirectToMessenger() {
            window.location.replace("/explorer/messenger");
        },
        redirectToExplorer() {
            window.location.replace("/explorer");
        },

        getClientProposedMission() {
            let params = {};

            setTimeout(() => {
                axios.get("/api/explorer/missions", {
                    params: params
                }).then(res => {
                    this.clientProposedMission = res.data; // Update talents list
                }).catch(error => console.log(error));
            }, 10);
        },
        setMissionSelect() {
            $(() => {
                $('.js-mission-data').select2({
                    placeholder: "Mission",
                    tags: true,
                    createTag: function (params) {
                        var term = $.trim(params.term);

                        if (term === '') {
                            //return null;
                        }

                        return {
                            id: term,
                            text: term,
                            newTag: true // add additional parameters
                        }
                    },
                    debug: true,
                    language: {
                        searching: function () {
                            return "Chargement...";
                        }
                    },
                    ajax: {
                        url: '/api/explorer/missions',
                        processResults: function (data) {
                            let datas = $.map(data, function (obj) {
                                obj.id = obj.name;
                                obj.text = obj.name;

                                return obj;
                            });

                            return {
                                results: datas
                            };
                        }
                    }
                });

                $('.js-mission-data').on("select2:select", (e) => {
                    console.log(e)
                    //this.getTalents();
                });
                $('.js-mission-data').on("select2:unselect", (e) => {
                    //this.getTalents();
                });
            });
        },
    },
}
</script>
