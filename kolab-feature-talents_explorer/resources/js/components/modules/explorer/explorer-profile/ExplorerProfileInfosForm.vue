<template>
    <div v-if="isFreelance" class="explorer-form explorer-profile-infos-form row">
        <div class="infos-form col-12">
            <button class="explorer-edit-form-button" @click="handleFormUnlockButton"><span
                style="width: 16.8px; height: 16.8px;" class="picto-explorer-pencil"/></button>
            <form v-on:submit.prevent="patchUser">
                <div class="infos-form-group">
                    <div class="explorer-form-main-label">
                        <div class="icon-container"><span class="picto-explorer-user"/></div>
                        <h2>Informations personnelles*</h2>
                    </div>
                    <div class="input-container">
                        <input :disabled="isInputDisabled" :class="{'disabled-input': isInputDisabled}"
                               v-model="formUser.firstname" placeholder="Prénom" type="text"> </input>
                        <input :disabled="isInputDisabled" :class="{'disabled-input': isInputDisabled}"
                               v-model="formUser.lastname" placeholder="Nom" type="text">
                    </div>
                </div>

                <div class="infos-form-group">
                    <div class="explorer-form-main-label">
                        <div class="icon-container"><span class="picto-explorer-user"/></div>
                        <h2>Coordonnées*</h2>
                    </div>
                    <div class="input-container">
                        <input :disabled="isInputDisabled" :class="{'disabled-input': isInputDisabled}"
                               v-model="formUser.email" placeholder="Votre mail" type="email">
                        <input :disabled="isInputDisabled" :class="{'disabled-input': isInputDisabled}"
                               v-model="formUser.phone" placeholder="Votre numéro" type="tel">
                    </div>
                </div>
                <div class="infos-form-group">
                    <div class="explorer-form-main-label">
                        <div class="icon-container"><span class="picto-explorer-job"/></div>
                        <h2>Portfolio & Compétences*</h2>
                    </div>
                    <div class="input-container">
                        <div style="width: 49.5%; height: 100%">
                            <select :disabled="isInputDisabled" v-model="formUser.job_id" v-select2
                                    class="js-job-data js-select2 js-required"
                                    id="job-data" name="job"></select>
                        </div>
                        <input :disabled="isInputDisabled" :class="{'disabled-input': isInputDisabled}"
                               v-model="formUser.website" placeholder="Site internet" type="text">
                    </div>
                    <div class="input-container">
                        <select :disabled="isInputDisabled" v-model="formUser.skills_ids" v-select2
                                class="js-skills-data js-select2 js-required" id="skills-data"
                                name="skills[]" multiple="multiple"></select>
                    </div>
                </div>
                <div class="infos-form-group">
                    <div class="explorer-form-main-label">
                        <div class="icon-container"><span class="picto-explorer-user"/></div>
                        <h2>Description</h2>
                    </div>
                    <div class="input-container">
                        <textarea :disabled="isInputDisabled" :class="{'disabled-input': isInputDisabled}"
                               v-model="formUser.description" placeholder="Décrivez-vous" type="text"
                               style="width: 100%"></textarea>
                    </div>
                </div>

                <button :disabled="isInputDisabled" class="submit-button" type="submit" v-if="!formSended">Enregistrer
                </button>
                <button disabled class="submit-button button-sended" v-else-if="formSended">Enregistré</button>
            </form>
        </div>
    </div>

    <div v-else class="explorer-form explorer-profile-infos-form row" style="padding-left: 15px;">
        <div class="infos-form col-12">
            <button class="explorer-edit-form-button" @click="handleFormUnlockButton"><span
                style="width: 16.8px; height: 16.8px;" class="picto-explorer-pencil"/></button>
            <form v-on:submit.prevent="patchUser">
                <div class="infos-form-group">
                    <div class="explorer-form-main-label">
                        <div class="icon-container"><span class="picto-explorer-user"/></div>
                        <h2>Informations personnelles*</h2>
                    </div>
                    <div class="input-container">
                        <input :disabled="isInputDisabled" :class="{'disabled-input': isInputDisabled}"
                               v-model="formUser.firstname" placeholder="Prénom"
                               type="text"> </input>
                        <input :disabled="isInputDisabled" :class="{'disabled-input': isInputDisabled}"
                               v-model="formUser.lastname" placeholder="Nom" type="text">
                    </div>
                </div>

                <div class="infos-form-group">
                    <div class="explorer-form-main-label">
                        <div class="icon-container"><span class="picto-explorer-user"/></div>
                        <h2>Coordonnées*</h2>
                    </div>
                    <div class="input-container">
                        <input :disabled="isInputDisabled" :class="{'disabled-input': isInputDisabled}"
                               v-model="formUser.email" placeholder="Votre mail"
                               type="email">
                        <input :disabled="isInputDisabled" :class="{'disabled-input': isInputDisabled}"
                               v-model="formUser.phone" placeholder="Votre numéro"
                               type="tel">
                    </div>
                </div>

                <div class="infos-form-group">
                    <div class="explorer-form-main-label">
                        <div class="icon-container"><span class="picto-explorer-job"/></div>
                        <h2>Métier & Société</h2>
                    </div>
                    <div class="input-container">
                        <input :disabled="isInputDisabled" :class="{'disabled-input': isInputDisabled}"
                               v-model="formUser.client_job" placeholder="Métier" type="text">
                        <input :disabled="isInputDisabled" :class="{'disabled-input': isInputDisabled}"
                               v-model="formUser.company" placeholder="Société" type="text">
                    </div>
                </div>

                <button :disabled="isInputDisabled" class="submit-button" type="submit" v-if="!formSended">Enregistrer
                </button>
                <button disabled class="submit-button button-sended" v-else-if="formSended">Enregistré</button>
            </form>
        </div>
    </div>
</template>
<script>
export default {
    props: ['user', 'isFreelance', 'isSelf'],

    data() {
        return {
            formUser: {...this.user},
            formSended: false,
            isInputDisabled: true
        }
    },
    mounted() {
        this.triggerSelect2();
        this.setSelect2();

        setTimeout(() => {
        }, 100);
    },

    methods: {
        unlockForm() {
            this.isInputDisabled = false;
            this.formSended = false
        },
        lockForm() {
            this.isInputDisabled = true;
        },
        handleFormUnlockButton() {
            this.isInputDisabled ? this.unlockForm() : this.lockForm();
        },
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

        setSelect2() {
            if (this.isFreelance) {
                this.setSkillsSelect();
            }
            this.setJobsSelect();
        },
        /**
         * Fill select2
         * */
        triggerSelect2() {
            if (this.isFreelance) {
                this.triggerSkillsSelect2();
            }
            this.triggerJobsSelect2();
        },

        /**
         * Fill the job in the select2
         */
        triggerJobsSelect2() {
            let jobsSelect2 = $('#client-job-data');

            if (this.isFreelance) {
                jobsSelect2 = $('#job-data');
            }

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

        patchUser() {
            axios.patch('/api/user', {
                user: this.formUser
            }).then(res => {
                this.formSended = true;
                this.errorMessage = null;
                this.lockForm();
                this.emitFormUpdated();
            }).catch(error => {
                this.errorMessage = error.response.data.message
            });
        },

        emitFormUpdated() {
            this.$emit('form-updated')
        }
    }
}
</script>
