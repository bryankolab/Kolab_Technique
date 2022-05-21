<template>
    <div class="page_explorer" id="vue__explorer">
        <div class="main-container" style="background: none;">
            <div v-if="isFreelance" class="explorer-highlight-project"
                 :style="{backgroundImage: 'linear-gradient( to left,rgba(0, 0, 0, 0.1), rgba(0, 0, 0, 0.8) ),url(' + highlightProject.mainMedia.path + ')'}">
                <h1>Bienvenue sur la première <br>
                    plateforme réservée aux meilleurs <br>
                    freelances de la <span>post-production</span>
                </h1>
                <div class="highlight-infos">
                    <span class="highlight-talent">{{ highlightProject.userName }}</span>
                    <span class="highlight-project">{{ highlightProject.name }}</span>
                </div>
            </div>
            <div v-else class="explorer-highlight-project"
                 :style="{backgroundImage: 'linear-gradient( to left,rgba(0, 0, 0, 0.1), rgba(0, 0, 0, 0.8) ),url(' + highlightProject.mainMedia.path + ')'}">
                <h1 style="height: 79px">
                    Plus de doutes, les meilleurs <br/> freelances de la post-prod sont ici.
                </h1>
                <p>Confiez leurs vos projets, on s’assure du reste.</p>

                <div class="highlight-infos">
                    <span class="highlight-talent">{{ highlightProject.userName }}</span>
                    <span class="highlight-project">{{ highlightProject.name }}</span>
                </div>
            </div>

            <div class="explorer-how-to">
                <h1>Les 4 étapes d’une mission<br>
                    freelance sur Explorer, en toute sécurité.</h1>

                <div class="step-container">
                    <div class="explorer-how-to-step" v-if="isFreelance">
                        <div class="step-icon-container">
                            <span class="picto-propose"/>
                        </div>
                        <p>Vous proposez une mission
                            à un freeelance</p>
                    </div>

                    <div class="explorer-how-to-step" v-else>
                        <div class="step-icon-container">
                            <span class="picto-propose"/>
                        </div>
                        <p>Vous proposez une mission
                            à un freeelance</p>
                    </div>

                    <span class="line dotted"/>

                    <div class="explorer-how-to-step" style="width: 196px; " v-if="isFreelance">
                        <div class="step-icon-container">
                            <span class="picto-paid"/>
                        </div>
                        <p>Vous êtes intéréssé par la mission,<br/>
                            vous envoyez alors votre devis</p>
                    </div>
                    <div class="explorer-how-to-step" style="width: 155px; " v-else>
                        <div class="step-icon-container">
                            <span class="picto-paid"/>
                        </div>
                        <p>Le freelance vous envoie son devis</p>
                    </div>

                    <span class="line dotted"/>

                    <div class="explorer-how-to-step" style="width: 244px;" v-if="isFreelance">
                        <div class="step-icon-container">
                            <span class="picto-sent"/>
                        </div>
                        <p>Une fois le devis payé, vous n’avez plus qu’à envoyer les livrables
                            dans le délai convenu</p>
                    </div>

                    <div class="explorer-how-to-step" style="width: 244px; " v-else>
                        <div class="step-icon-container">
                            <span class="picto-sent"/>
                        </div>
                        <p>Vous le réglez en toute sécurité avec notre partenaire de confiance MangoPay</p>
                    </div>

                    <span class="arrow dotted"/>

                    <div class="explorer-how-to-step" style="width: 155px;" v-if="isFreelance">
                        <div class="step-icon-container">
                            <span class="picto-validated"/>
                        </div>
                        <p v-if="isFreelance">Enfin, clôturez la mission
                            pour récupérer vos fonds</p>
                    </div>

                    <div class="explorer-how-to-step" style="width: 196px; " v-else>
                        <div class="step-icon-container">
                            <span class="picto-validated"/>
                        </div>
                        <p>Vous récupérez vos livrables et vous clôturez la mission</p>
                    </div>
                </div>
            </div>

            <div v-if="isFreelance" class="explorer-talents">
                <explorer-projects-list :portfolios="portfolios"/>
            </div>

            <div v-else class="explorer-talents">
                <explorer-talents-list/>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: ['user', 'user_role'],

    data() {
        return {
            highlightProject: {
                mainMedia: {path: "images/temp-img/explorer-main-proj.png"},
                userName: "FKA TWIGS",
                name: "Don’t judge me"
            }
        }
    },

    mounted() {
        console.log(this.isFreelance)
        $('body').toggleClass('theme-explorer');
    },

    methods: {},
    computed: {
        isFreelance: function () {
            return this.user.role.name === "talent";
        },
    }
};

/**
 * Dashboard components
 */

Vue.component('explorer-projects-list', {
    props: [],
    template: `
        <div class="explorer-talents">
        <div class="projects-list-title">
            <span class="picto-explorer-fire" style="height: 22px; width: 16px; margin-right: 18px;"/>
            <h1>Les projets du moment</h1>
        </div>
        <span class="separator"/>

        <div class="explorer-talents-list">
            <button v-for="project in portfolios" class="explorer-talent-card"
                    v-bind:style="{ backgroundImage: 'linear-gradient(rgba(0, 0, 0, 0), rgba(0, 0, 0, 1) ),url(' + project.mainMedia.path + ')'}"
                    style="background-size: cover; background-position: center;" type="button"
                    @click="showPortfolio(project)">
                <div class="card-infos">
                    <span class="talent-name">{{ project.userName }}</span>
                    <span class="talent-job">{{ project.name }}</span>
                </div>
            </button>
        </div>
        <explorer-profile-project-detail v-if="showPortfolioDetail" :project="portfolioToShow"
                                         v-on:close-modal="closePortfolioDetailModal"/>
        </div>`,
    data: function () {
        return {
            portfolios: [],
            portfolioToShow: null,
            showPortfolioDetail: false
        }
    },
    beforeMount() {
        this.getProjects();
    },
    methods: {
        /**
         * Get portfolio projects
         */
        /**
         * getTalents
         * @return void
         */
        getProjects() {
            let params = {};

            setTimeout(() => {
                axios.get("/api/explorer/portfolios", {
                    params: params
                }).then(res => {
                    if (res.success === false) {
                        // Todo : Error
                    } else {
                        this.portfolios = res.data; // Update talents list
                    }
                }).catch(error => console.log(error));
            }, 10);
        },
        showPortfolio(portfolio) {
            $('body').toggleClass('body-modal');
            this.portfolioToShow = portfolio;
            this.showPortfolioDetail = true;
        },
        closePortfolioDetailModal() {
            $('body').toggleClass('body-modal');
            this.showPortfolioDetail = false;
        }
    }
});

Vue.component('explorer-talents-list', {
    template: `
        <div class="explorer-talents">
        <div class="explorer-talents-searchbar">
            <div class="talents-search">
                <span class="picto picto-explorer-search"/>
                <input v-on:input="getTalents" v-model="filters.name" type="text"
                       placeholder="Rechercher un monteur, artiste vfx, graphiste..."/>
            </div>
            <div class="talents-search talents-filters">
                <span class="filter-text">Filtrer par :</span>

                <div class="filters-container">
                    <div class="filter-container">
                        <div class="filter-item filter-select has-picto">
                            <span class="filter-picto picto-explorer-filter-work"></span>
                            <select v-model="filters.jobs" v-select2 class="filter-field js-jobs-data"
                                    name="jobs[]"></select>
                        </div>
                    </div>
                    <div class="filter-container">
                        <div class="filter-item filter-select has-picto">
                            <span class="filter-picto picto-explorer-filter-competence"></span>
                            <select v-model="filters.skills" v-select2 class="filter-field js-skills-data"
                                    name="skills[]" multiple="multiple"></select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="talents.length === 0 " class="explorer-talents-list"
             style="display: flex; align-items: center; justify-content: center">
            <h1>Aucun résultat</h1>
        </div>
        <div v-else class="explorer-talents-list">
            <a v-for="talent in talents" class="explorer-talent-card" :href="'/explorer/profile/'+talent.id">
                <div v-if="talent.lastPortfolioMainMedia == null" class="talent-initials-container">
                    <span class="talent-initials">{{ getTalentInitials(talent) }}</span>
                </div>
                <div v-else class="explorer-talent-card"
                     v-bind:style="{ backgroundImage: 'linear-gradient(rgba(0, 0, 0, 0), rgba(0, 0, 0, 1) ),url(' + talent.lastPortfolioMainMedia.path + ')'}"
                     style="background-size: cover; background-position: center; width: 100%;">

                </div>
                <div class="card-infos">
                    <span class="talent-name">{{ talent.name }}</span>
                    <span class="talent-job">{{ talent.job.name }}</span>
                </div>
            </a>
        </div>
        </div>`,
    data() {
        return {
            all_talents: [],
            talents: [],
            filters: {
                alpha: null,
                name: null,
                date: null,
                jobs: [],
                skills: []
            },
        }
    },
    mounted() {
        this.getTalents(true);

        this.setSkillsSelect();
        this.setJobsSelect();
    },
    methods: {
        getTalentInitials(talent) {
            return talent.firstname.charAt(0) + talent.lastname.charAt(0);
        },
        /**
         * getTalents
         * @return void
         */
        getTalents(all) {
            let params = {};

            setTimeout(() => {
                if (this.filters.alpha) params.filter_alpha = this.filters.alpha;
                if (this.filters.name) params.filter_name = this.filters.name;
                if (this.filters.date) params.filter_date = moment(this.filters.date).format('YYYY-MM-DD');
                if (this.filters.jobs.length > 0) params.filter_jobs = [this.filters.jobs];
                if (this.filters.skills.length > 0) params.filter_skills = this.filters.skills;
                if (this.filters.date) this.filters.date = moment(this.filters.date).format('DD/MM/YYYY');

                params.filter_is_explorer = 1;

                axios.get("/api/talent", {
                    params: params
                }).then(res => {
                    if (res.success === false) {
                        // Todo : Error
                    } else {
                        this.talents = res.data.datas; // Update talents list
                        if (all) this.all_talents = res.data.datas; // Set all talents for first time
                    }
                }).catch(error => console.log(error));
            }, 10);
        },

        /**
         * Set skills select2
         */
        setSkillsSelect() {
            $(() => {
                $('.js-skills-data').select2({
                    dropdownCssClass: 'multiple-choices filters-dropdown',
                    placeholder: "Compétence(s)",
                    closeOnSelect: false,
                    debug: true,
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

                $('.js-skills-data').on("select2:select", (e) => {
                    this.getTalents();
                });
                $('.js-skills-data').on("select2:unselect", (e) => {
                    this.getTalents();
                });
            });
        },

        /**
         * Set jobs select2
         */
        setJobsSelect() {
            $(() => {
                $('.js-jobs-data').select2({
                    placeholder: "Métier(s)",
                    minimumResultsForSearch: -1,
                    //tags: "true",
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

                $('.js-jobs-data').on("select2:select", (e) => {
                    this.getTalents();
                });
                $('.js-jobs-data').on("select2:unselect", (e) => {
                    this.getTalents();
                });
            });
        },

        /**
         * Clear field
         */
        clear(type) {
            if (typeof this.filters[type] == 'object') {
                this.filters[type] = [];
            } else {
                this.filters[type] = null;
            }

            if ($('.js-' + type + '-data')) {
                $('.js-' + type + '-data').val('').trigger('change');
            }

            this.getTalents();
        },
    }
});
</script>
