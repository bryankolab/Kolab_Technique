<template>
    <div class="page__project_detail projects__view" id="vue__project_detail">
	   	<ContextMenu ref="ContextMenu"></ContextMenu>

      <div class="main-container">
        <a href="/projects" class="action-link mb-20">
        	<span class="icon icon-long-arrow"></span> Retour vers Projets
        </a>

        <div class="row">
          <div class="col-xl-6">
            <ProjectDetailInfos :_project="project" />
            <ProjectDetailDocuments :_project="project" />
          </div>
          <div class="col-xl-6">
            <ProjectDetailPlanning :_project="project" />
          </div>
        </div>
      </div>

    </div>
</template>

<script>
export default {
    props: ['_project'],

    data() {
      return {
        project: null,
      }
    },

    created() {
      if (this._project) {
        this.project = this._project;
        this.project.formated_created_at = Vue.prototype.Global._convertDate(this.project.created_at);
        this.project.formated_date_deadline = Vue.prototype.Global._convertDate(this.project.date_deadline);
      }
    },
    mounted() {
    },

    methods: {
    }
}
</script>
