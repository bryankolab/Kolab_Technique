<template>
    <div class="explorer-profile-projects-list">
        <div v-if="showAddProjectInList" class="project-empty" v-on:click="portfolioAddModalToggle">
            <span>Ajouter un projet</span>
        </div>
        <div v-for="project in user.portfolios" class="project" v-on:click="portfolioProjectDetailOpen(project)"
             v-bind:style="{ backgroundImage:'linear-gradient(rgba(0, 0, 0, 0.35), rgb(0, 0, 0)), url(' + project.mainMedia.path + ')' }">
            <span>{{ project.name }}</span>
        </div>
        <explorer-profile-portfolio-add v-if="showAddPortfolioModal" v-on:close-modal="portfolioAddModalToggle"/>
        <explorer-profile-project-detail v-if="showProjectDetailModal" :project="selectedProjectDetail"
                                         v-on:close-modal="portfolioProjectDetailClose"/>
    </div>
</template>
<script>
export default {
    props: ['user', 'isSelf'],

    data() {
        return {
            currentPage: null,
            showAddPortfolioModal: false,
            showProjectDetailModal: false,
            selectedProjectDetail: null,
        }
    },
    created() {
        if (this.currentPage == null) {
            this.currentPage = "projects";
        }
    },
    mounted() {
        this.emitNbProjectsToParent();
    },
    computed: {
        showAddProjectInList() {
            return this.isSelf && this.user.portfolios.length <= 0;
        }
    },
    methods: {
        setCurrentPage(current) {
            this.currentPage = current;
        },
        emitNbProjectsToParent() {
            this.$emit('update-nb-projects', this.user.portfolios.length)
        },
        portfolioAddModalToggle() {
            this.showAddPortfolioModal = !this.showAddPortfolioModal;
        },
        portfolioProjectDetailOpen(project) {
            this.selectedProjectDetail = project;
            this.showProjectDetailModal = true;
            $('body').toggleClass('body-modal');
        },
        portfolioProjectDetailClose() {
            this.showProjectDetailModal = false;
            $('body').toggleClass('body-modal');
        }
    },
}
</script>
