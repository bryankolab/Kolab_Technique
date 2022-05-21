<template>
	<div class="col-md-8 col-custom-small">

		<!-- Header -->
    <div class="project-detail__main-info__box is-light project-detail__export-box-header js-toggle-wrapper">
      <h2 class="main-title mb-05">
      	<span class="icon icon-player"></span> Livrables
      </h2>
      <p class="text c-grey-b title-indent">{{ exxports.length }} <template v-if="exxports.length < 2">livrable</template> <template v-else>livrables</template></p>

      <div v-if="state == 'DELETE'" class="delete-actions">
        <button type="button" class="action-link" v-on:click="state = null">Annuler</button>
        <button :disabled="deletedItems.length == 0" v-on:click="Exxport._delete(deletedItems)" class="button icon-button">
        	<span class="icon icon-delete"></span>
        </button>
      </div>

      <button type="button" class="button project-detail__action-button is-violet js-toggle-button">
        <span class="dot-button"></span>
        <span class="dot-button"></span>
        <span class="dot-button"></span>
      </button>

      <div class="actions-box js-toggle-content">
        <ul class="context-menu--items">
          <li class="context-menu--items-element" v-on:click="Global._setAction('SET_EXPORT', {project: project})"><span class="icon icon-plus"></span> Ajouter un livrable</li>
          <li class="context-menu--items-element" v-on:click="setState('DELETE')"><span class="icon icon-delete"></span> Supprimer un ou des livrable(s)</li>
        </ul>
      </div>

    </div>
    <!-- ./Header -->

    <!-- Exports -->
    <div class="project-detail__main-info__box is-light project-detail__export-box">
      <div class="simple-table dark-bckg is-small">
        <div class="simple-table__header">
          <div class="simple-table__row">
            <div class="simple-table__td td-40"><p>Film</p></div>
            <div class="simple-table__td td-10"><p>Durée</p></div>
            <div class="simple-table__td td-20"><p>Résolution</p></div>
            <div class="simple-table__td td-20"><p>Sortie</p></div>
            <div class="simple-table__td td-10"><p><span class="icon icon-language"></span></p></div>
          </div>
        </div>
        <div class="simple-table__body">
          <div class="simple-table__row" v-for="exxport in exxports">
          	<div class="wrapper-checkbox" v-if="state == 'DELETE'">
              <input type="checkbox" v-model="deletedItems" :value="exxport.id" :id="'export-' + exxport.id" />
              <span class="checkbox"></span>
            </div>
            <div class="simple-table__td td-40 name">
              <label :for="'export-' + exxport.id" v-if="state == 'DELETE'">{{ exxport.name }}</label>
              <p v-else>{{ exxport.name }}</p>
            </div>
            <div class="simple-table__td td-10"><p>{{ exxport.duration }}</p></div>
            <div class="simple-table__td td-20"><p>{{ exxport.resolution.name }}</p></div>
            <div class="simple-table__td td-20"><p>{{ exxport.format_raw }}</p></div>
            <div class="simple-table__td td-10"><p>{{ exxport.language.name }}</p></div>
          </div>
        </div>
      </div>
    </div>
    <!-- ./Exports -->

  </div>
</template>

<script>
export default {
    props: ['_project'],

    data() {
      return {
      	project: this._project,
      	exxports: [],
      	state: null,
      	deletedItems: []
      }
    },

    created() {
    },

    mounted() {
    	this.getExports();

      this.$bus.$on('EXPORT_ADD_OR_UPDATE', () => {
      	this.getExports();
      	this.state = null;
      });
    },

    methods: {
    	getExports(){
      	axios.get('/api/export/?project_id=' + this.project.id)
      	.then(res => {
          if (res.success === false) {
              // Todo : Error
          } else {
              this.exxports = res.data.datas;
          }
        }).catch(error => console.log(error));
      },

      setState(value){
      	this.state = value;
      	$('.js-toggle-content').fadeOut();
      }
    }
}
</script>