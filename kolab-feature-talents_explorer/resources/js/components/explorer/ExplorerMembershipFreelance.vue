<template>
    <div class="page_explorer_membership" id="vue__explorer_membership_client">
        <div class="main-container" style="background: none;">
            <div class="bg-image-left"/>
            <div class="form-container">
                <h1>Demande de code d’accès Explorer</h1>
                <p>{{ user.firstname }}, ces infos sont-elles toujours valables ?</p>

                <form method="POST" v-on:submit.prevent="postRegistration" class="membership-sub-form">
                    <div class="form-input-group">
                        <div class="input-container">
                            <label for="user-firstname">Prénom*</label>
                            <input class="membership-input" v-model="formUser.firstname" id="user-firstname"
                                   type="text" required/>
                        </div>
                        <div class="input-container">
                            <label for="user-lastname">Nom de famille*</label>
                            <input class="membership-input" v-model="formUser.lastname" id="user-lastname" type="text" required/>
                        </div>
                    </div>

                    <div class="form-input-group">
                        <div class="input-container">
                            <label for="user-email">Adresse mail*</label>
                            <input class="membership-input" v-model="formUser.email" id="user-email" type="email" required/>
                        </div>
                        <div class="input-container">
                            <label for="user-phone">Numéro de téléphone*</label>
                            <input class="membership-input" v-model="formUser.phone" id="user-phone" type="tel" required/>
                        </div>
                    </div>
                    <div class="form-input-group">
                        <div class="input-container">
                            <label for="job-data">Metier</label>
                            <select v-model="formUser.job" v-select2 class="js-job-data js-select2 js-required"
                                    id="job-data" name="job"></select>
                        </div>
                        <div class="input-container">
                            <label for="user-portfolio">Portfolio</label>
                            <input class="membership-input" v-model="formUser.website" id="user-portfolio" type="text" placeholder="Votre site web"/>
                        </div>

                    </div>

                    <div class="form-input-group">
                        <div class="input-container" style="width: 100%">
                            <label for="skills-data" style="top: -5px;position: relative;">Vos compétences</label>
                            <select v-model="formUser.skills" v-select2
                                    class="js-skills-data js-select2 js-required" id="skills-data"
                                    name="formUser.selectedSkills[]" multiple="multiple"></select>
                        </div>
                    </div>
                    <div class="form-input-group">
                        <div class="input-container" style="width: 100%">
                            <label for="user-description">Description</label>
                            <textarea v-model="formUser.description" id="user-description" type="text" placeholder="Description"/>
                        </div>
                    </div>

                    <button class="submit-button" v-if="!isSended">Envoyer la demande</button>
                    <button disabled class="submit-button button-sended" v-else-if="isSended">Demande envoyée</button>
                </form>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: ['user'],

    data() {
        return {
            formUser: {...this.user},
            isSended: false
        }
    },
    mounted() {
        $('body').toggleClass('theme-explorer');

        this.triggerSelect2();

        setTimeout(() => {
            this.setSkillsSelect();
            this.setJobsSelect();
        }, 1000)

    },
    methods: {
        /**
         * Set jobs select2
         */
        setJobsSelect() {
            $('.js-job-data').select2({
                minimumResultsForSearch: -1,
                tags: "true",
                placeholder: "Choisir un métier",
                language: {
                    searching: function () {
                        return "Chargement...";
                    }
                },
                ajax: {
                    url: '/api/job',
                    processResults: function (data) {
                        var data = $.map(data.datas, function (obj) {
                            obj.id = obj.id;
                            obj.text = obj.name;

                            return obj;
                        });

                        return {
                            results: data
                        };
                    }
                }
            });
        },
        /**
         * Set skills select2
         */
        setSkillsSelect() {
            $('.js-skills-data').select2({
                dropdownCssClass: 'multiple-choices',
                placeholder: "Choisir une ou plusieurs compétence(s)",
                language: {
                    searching: function () {
                        return "Chargement...";
                    }
                },
                ajax: {
                    url: '/api/skill',
                    processResults: function (data) {
                        var data = $.map(data.datas, function (obj) {
                            obj.id = obj.id;
                            obj.text = obj.name;

                            return obj;
                        });

                        return {
                            results: data
                        };
                    }
                }
            });
        },

        /**
         * Fill select2
         * */
        triggerSelect2() {
            this.triggerJobsSelect2();
            this.triggerSkillsSelect2();
        },

        /**
         * Fill the job in the select2
         */
        triggerJobsSelect2() {
            let jobsSelect2 = $('#job-data');

            if (this.formUser.job) {
                let option = new Option(this.formUser.job.name, this.formUser.job.id, true, true);
                jobsSelect2.append(option).trigger('change');
            }
        },

        /**
         * Fill the select2 with already selected Skills
         */
        triggerSkillsSelect2() {
            let skillsSelect2 = $('#skills-data');

            this.formUser.skills.forEach(skill => {
                let option = new Option(skill.name, skill.id, true, true);
                skillsSelect2.append(option).trigger('change');
            });
        },

        postRegistration() {
            axios.post('/api/explorer/register', {
                user: this.formUser
            }).then(res => {
                if (res.success === false) {
                    // Error
                } else {
                    this.isSended = true;
                }
            });
        }
    },
}
</script>
