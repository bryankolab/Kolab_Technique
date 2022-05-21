<template>
	<!-- Wrapper -->
	<div class="projects-wrapper" id="vue__projects">
		<ContextMenu ref="ContextMenu"></ContextMenu>

		<!-- Container -->
		<div class="main-container">
      <div class="projects__view-container">
        <div class="projects__view-wrapper">

        	<!-- View -->
    			<div class="js-projects-list projects__view">
    			<!--	<AddButtons></AddButtons>-->

    				<!-- Filters -->
            <div class="filters">
              <div class="row filters-row">
                <div class="search-col">
                  <div class="filter-item has-picto">
                    <span class="filter-picto picto-search"></span>
                    <ProjectAutocomplete></ProjectAutocomplete>
                    <button type="button" class="filters-delete is-cross" :disabled='!filters.name' v-on:click="clear('name')"><span class="filters-delete__picto icon-cross"></span></button>
                  </div>
                </div>

                <div class="filters-col">
                  <!--<p class="filters-text"><span class="filters-main-icon icon icon-filters"></span> Filtrer par</p>-->
                  <div class="filter-col">
                    <div class="filter-item filter-select has-picto">
                      <span class="filter-picto picto-categories"></span>
                      <select type="text" v-model="filters.categories" v-select2 class="filter-field js-categories-data" name="categories[]" multiple="multiple"></select>
                      <button type="button" class="filters-delete" :disabled='filters.categories.length == 0' v-on:click="clear('categories')"><span class="filters-delete__picto icon icon-refresh"></span></button>
                    </div>
                  </div>

                  <div class="filter-col">
                    <div class="filter-item filter-select has-picto">
                      <span class="filter-picto picto-order"></span>
                      <select type="text" v-model="filters.sortby" v-select2 class="filter-field js-sortby-data" name="sortby">
                      	<option value="SORT_RECENT_TO_OLDER">Du plus récent au plus ancien</option>
                      	<option value="SORT_OLDER_TO_RECENT">Du plus ancien au plus récent</option>
                      </select>
                      <button type="button" class="filters-delete" :disabled='!filters.sortby' v-on:click="clear('sortby', '')"><span class="filters-delete__picto icon icon-refresh"></span></button>
                    </div>
                  </div>

                  <div class="filter-col">
                    <div class="filter-item filter-select has-picto">
                      <span class="filter-picto picto-status"></span>
                      <select type="text" v-model="filters.status" v-select2 class="filter-field js-status-data" name="status">
                      	<option value="STATUS_ALL">Tous</option>
                      	<option value="STATUS_PROGRESS">En cours</option>
                      	<option value="STATUS_CLOSED">Terminé</option>
                      </select>
                      <button type="button" class="filters-delete" :disabled='!filters.status' v-on:click="clear('status', '')"><span class="filters-delete__picto icon icon-refresh"></span></button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- ./Filters -->

            <!-- List -->
    				<div class="block projects__block scroll-content">
              <div class="projects-table" v-for="(projectsByYear, year) in projects">
                <div class="table-row" v-for="(projectsByMonth, month) in projectsByYear">

                	<!-- Date -->
                  <div class="projects-table__title-cell">
                      <p class="projects-table__date">{{ $data._months[month] + ' ' + year}}</p>
                  </div>
                  <!-- ./Date -->

                  <!-- Projects -->
                  <div class="projects-table__content">

                  	<!-- Project card -->
                    <div class="project-card__col" v-for="(project, index) in projectsByMonth" v-on:click="Project._goTo(project.id)" v-on:contextmenu.prevent="$refs.ContextMenu.show($event, 'PROJECT', {project: project}, getProjects);" :key="index">
                      <div class="project-card">
                        <div :class="'project__category is-' + project.category.color" v-if="project.category">
                        	{{ project.category.name }}
                        </div>
                        <div class="project-card__infos">
                          <p><strong>{{ project.name }}</strong></p>
                          <p>Production : {{ project.production }}</p>
                          <p>Client : {{ project.client.name }}</p>
                          <p>
                          	<span v-if="getProjectProgressBar(project) < 100">Deadline</span>
                          	<span v-else>Terminé le</span>
                          	: {{ project.date_deadline | dateFormat }}
                          </p>
                        </div>
                        <div class="project-card__progress">
                          <span class="project-card__progress-bar" :style="'width:' + getProjectProgressBar(project) + '%'" v-bind:class="{ 'is-full' : getProjectProgressBar(project) == 100 }"></span>
                        </div>
                      </div>
                    </div>
                    <!-- ./Project card -->

                  </div>
                  <!-- ./Projects -->

                </div>
              </div>
    				</div>
    				<!-- ./List -->

    			</div>
    			<!-- ./View -->

        </div>
      </div>
		</div>
		<!-- ./Container -->

	</div>
	<!-- Wrapper -->
</template>

<script>
	export default {
		props: [],

		data() {
			return {
				selectedProject: null,
        projects: [],
				filters: {
					name: null,
					projects_id: null,
					sortby: '',
					status: '',
					categories: []
				},
        isProjectView: false
			}
		},

		mounted() {
			this.setProjectSelect('project');
		  this.setCategoriesSelect();
		  this.setSortbySelect();
		  this.setStatusSelect();

    	// On mounted, get all projects list
    	this.getProjects();

    	// Catch event when project is added or updated
    	this.$bus.$on('PROJECT_ADD_OR_UPDATE', () => {
	      this.getProjects(); // Call projects with filters
	    });
    },

    computed: {
    },

    methods: {
    	/**
    	 * getProjects
    	 * @return void
    	 */
    	getProjects(){

    		// Adapt if filters are defined ?
    		// Juste un test, ca peut être autrement !!
    		var params = {};
    		if(this.filters.name) params.filter_name = this.filters.name;
    		if(this.filters.sortby) params.filter_sortby = this.filters.sortby;
    		if(this.filters.status) params.filter_status = this.filters.status;
    		if(this.filters.projects_id) params.filter_projects_id = this.filters.projects_id;
    		if(this.filters.categories.length > 0) params.filter_categories = this.filters.categories;

    		axios.get("/api/project", {
    			params: params
    		}).then(res => {
    			if(res.success === false){
	      		// Todo : Error
	      	} else {
	      		this.projects = res.data.datas; // Update projects list
	      	}
	      }).catch(error => console.log(error));
    	},

    	/**
       * Set Project select2
       */
      setProjectSelect(name) {
        $(() => {
          $('.js-'+name+'-data').select2({
            placeholder: "Rechercher un projet",
            language: {
			        searching: function() {
		            return "Chargement...";
			        }
				    },
            ajax: {
              url: '/api/project',
              data: function (params) {
                return {
                  mode: 'search',
                  term: params.term
                }
              },
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

					$('.js-'+name+'-data').on("select2:select", (e) => { this.getProjects(); });
      	 	$('.js-'+name+'-data').on("select2:unselect", (e) => { this.getProjects(); });
        });
      },

      /**
       * Set Categories select2
       */
      setCategoriesSelect(){
        $(() => {
          $('.js-categories-data').select2({
            dropdownCssClass: 'multiple-choices filters-dropdown',
            placeholder: "Catégorie(s)",
            closeOnSelect: false,
            language: {
			        searching: function() {
		            return "Chargement...";
			        }
				    },
            ajax: {
              url: '/api/project-category',
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
        });

        $('.js-categories-data').on("select2:select", (e) => { this.getProjects(); });
        $('.js-categories-data').on("select2:unselect", (e) => { this.getProjects(); });
      },

      /**
       * Set Sortby select2
       */
      setSortbySelect(){
        $(() => {
          $('.js-sortby-data').select2({
            dropdownCssClass: 'filters-dropdown',
          	minimumResultsForSearch: Infinity,
            placeholder: "Deadline"
          });
        });

        $('.js-sortby-data').on("select2:select", (e) => { this.getProjects(); });
        $('.js-sortby-data').on("select2:unselect", (e) => { this.getProjects(); });
      },

      /**
       * Set Status select2
       */
      setStatusSelect(){
        $(() => {
          $('.js-status-data').select2({
            dropdownCssClass: 'filters-dropdown',
          	minimumResultsForSearch: Infinity,
            placeholder: "Statut"
          });
        });

        $('.js-status-data').on("select2:select", (e) => { this.getProjects(); });
        $('.js-status-data').on("select2:unselect", (e) => { this.getProjects(); });
      },

      /**
       * Clear field
       */
      clear(type, value = null){
        if(typeof this.filters[type] == 'object'){
            this.filters[type] = [];
        } else {
            this.filters[type] = value;
        }

        if($('.js-'+type+'-data')){
        	$('.js-'+type+'-data').val('').trigger('change');
        }

        this.getProjects();
      },
    }
	}
</script>
