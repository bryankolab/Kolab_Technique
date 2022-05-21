<template>
	<div class="project-detail__main-info">

    <!-- Header -->
    <div class="project-detail__main-info__box project-detail__main-info__header">
    	<a class="project-detail__main-info__button small-button is-violet" v-on:click="editProject(project)">
    		<span class="icon icon-edit"></span> Modifier le projet
    	</a>
      <h2 class="main-title"><span class="icon icon-project"></span> Projet : <br><span class="title-indent">{{ project.name }}</span></h2>
      <p class="text c-grey-b title-indent">Crée le {{ project.formated_created_at }}</p>
      
    </div>
    <!-- ./Header -->

    <!-- Infos + Exports -->
    <div class="row custom-row">

    	<!-- Infos -->
      <div class="col-md-4 col-custom-small">
        <div class="project-detail__main-info__box project-detail__main-info__aside">
          <div class="project-detail__main-info__aside-item">
            <h3>Production</h3>
            <p><strong>{{ project.production }}</strong></p>
          </div>
          <div class="project-detail__main-info__aside-item">
            <h3>Client</h3>
            <p><strong>{{ project.client.name }}</strong></p>
            <p class="text c-grey-b"><a :href="'mailto:' + project.client_email">{{ project.client_email }}</a></p>
            <p class="text c-grey-b">{{ project.client_phone }}</p>
          </div>
          <div class="project-detail__main-info__aside-item">
            <h3>Responsable</h3>
            <p><strong>{{ project.responsable_name }}</strong></p>
            <p class="text c-grey-b"><a :href="'mailto:' + project.responsable_email">{{ project.responsable_email }}</a></p>
            <p class="text c-grey-b">{{ project.responsable_phone }}</p>
          </div>
          <div v-if="project.category" class="project-detail__main-info__aside-item">
            <h3>Format</h3>
            <p><strong>{{ project.category.name }}</strong></p>
          </div>
          <div class="project-detail__main-info__aside-item">
            <h3>Deadline</h3>
            <p><strong>{{ project.formated_date_deadline }}</strong></p>
          </div>
        </div>
      </div>
      <!-- ./Infos -->

      <ProjectDetailExports :_project="project" />
    </div>
    <!-- ./Infos + Exports -->

  </div>
</template>

<script>
export default {
    props: ['_project'],

    data() {
      return {
      	project: this._project,
      }
    },

    created() {
    },

    mounted() {
    },

    methods: {
    	/**
    	 * Edit project
    	 * @param  Project projects
    	 * @return void
    	 */
    	editProject(project){
    	 	this.$bus.$emit('ACTION_CHANGED', {
    	 		action: 'SET_PROJECT',
    	 		project: project
    	 	});
    	}
    }
}
</script>