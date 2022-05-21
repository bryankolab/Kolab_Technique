<template>
	<div class="page__talents" id="vue__talents">
		<ContextMenu ref="ContextMenu"></ContextMenu>

		<div class="main-container">
			<!--<AddButtons></AddButtons>-->

      <div class="filters">
  			<div class="row filters-row">
          <div class="search-col">
              <div class="filter-item has-picto">
                  <span class="filter-picto picto-search"></span>
  								<!-- <input type="text" v-model="filters.name" placeholder="Rechercher un talent" class="filter-field search-field" v-on:keyup="getTalents()" /> -->
  								<TalentAutocomplete></TalentAutocomplete>
  								<button type="button" class="filters-delete is-cross" :disabled='!filters.name' v-on:click="clear('name')"><span class="filters-delete__picto icon-cross"></span></button>
              </div>
          </div>

          <div class="filters-col">
              <p class="filters-text"><span class="filters-main-icon icon icon-filters"></span> Filtrer par</p>
              <div class="filter-col">
                  <div class="filter-item has-picto">
                      <span class="filter-picto picto-calendar"></span>
                      <v-date-picker v-model="filters.date" :popover="{ visibility: 'click' }" class="filter-field-date js-date-data" @dayclick="getTalents()" color="kpurple">
                        <input v-model="filters.date" required placeholder="Disponibilité" autocomplete="off" />
                      </v-date-picker>
                      <button type="button" class="filters-delete" :disabled='!filters.date' v-on:click="clear('date')"><span class="filters-delete__picto icon icon-refresh"></span></button>
                  </div>
              </div>
              <div class="filter-col">
                  <div class="filter-item filter-select has-picto">
                      <span class="filter-picto picto-job"></span>
      				        <select v-model="filters.jobs" v-select2 class="filter-field js-jobs-data" name="jobs[]" multiple="multiple"></select>
                      <button type="button" class="filters-delete" :disabled='filters.jobs.length == 0' v-on:click="clear('jobs')"><span class="filters-delete__picto icon icon-refresh"></span></button>
                      <!-- <button type="button" class="filters-delete" v-if="!filters.jobs.length == 0" v-on:click="clear('jobs')"><span aria-hidden="true">×</span></button> -->
                  </div>
              </div>
              <div class="filter-col">
                  <div class="filter-item filter-select has-picto">
                      <span class="filter-picto picto-skills"></span>
    				          <select v-model="filters.skills" v-select2 class="filter-field js-skills-data" name="skills[]" multiple="multiple"></select>
                      <button type="button" class="filters-delete" :disabled='filters.skills.length == 0' v-on:click="clear('skills')"><span class="filters-delete__picto icon icon-refresh"></span></button>
                      <!-- <button type="button" class="filters-delete" v-if="!filters.skills.length == 0" v-on:click="clear('skills')"><span aria-hidden="true">×</span></button> -->
                  </div>
              </div>
          </div>

  			</div>
      </div>

			<div class="block talents__block">
        <div class="alphabetical__list">
            <button type="button" class="alphabetical__item" v-bind:class="{ 'is-active': _letter == filters.alpha, 'is-disabled': !hasTalentsWithLetter(_letter) }" v-for="_letter in $data._letters" v-on:click="setActiveLetter(_letter);">{{ _letter }}</button>
        </div>

				<div class="table-head">
          <div class="table-row">
						<div class="table-data"><span>Artiste</span></div>
						<div class="table-data table-col-medium">Contact</div>
						<div class="table-data table-col-wide">Compétences</div>
						<div class="table-data table-col-small">Statut</div>
						<!-- <div class="table-data">Tarif journée</div> -->
						<div class="table-data">Références</div>
						<div class="table-data table-col-small">Ville</div>
            <div class="table-data col-actions"><div class="sr-only">Actions</div></div>
					</div>
        </div>

				<div class="table-body" style="height:42vh;overflow:auto;">
          <div class="table-row js-clickable js-clickable-talent" v-for="talent in talents">
            <div class="table-data">
                <!-- class is-available ou is-unavailable -->
                <span class="talents__status"></span>
                <p>{{ talent.name }}</p>
                <p class="c-grey">{{ talent.job ? talent.job.name : '' }}</p>
            </div>
            <div class="table-data table-col-medium">
              <a :href="'tel:' + talent.phone" class="tel">{{ Global._formatPhone(talent.phone) }}</a>
              <p class="c-grey">{{ talent.email }}</p>
            </div>
            <div class="table-data table-col-wide js-toggle-wrapper">
              <p v-if="talent.skills.length > 1"><small><strong>+ {{ talent.skills.length - 1 }} <template v-if="talent.skills.length == 2">compétence</template><template v-else>compétences</template></strong></small></p>
              <div class="table-data__list js-toggle-button">
                <div class="table-data__list-inner">
                  <span v-for="(skill, id) in talent.skills" :key="id" class="table-data__list-item">{{ skill.name }}</span>
                </div>
                <div v-if="talent.skills.length > 1" class="table-data__list-all-items js-toggle-content">
                  <span v-for="(skill, id) in talent.skills" :key="id" class="table-data__list-item">{{ skill.name }}</span>
                </div>
                <span v-if="talent.skills.length > 1" class="icon icon-down-arrow"></span>
              </div>
            </div>
            <div class="table-data table-col-small">
              <p>{{ talent.profile_type }}</p>
            </div>
            <!-- <div class="table-data"><p>{{ talent.price }}</p></div> -->
            <div class="table-data">
              <div class="talents__references">
              	<a :href="talent.profile_url" v-if="talent.profile_url" target="_blank" class="talents__reference c-grey"><span class="icon icon-web"></span></a>
              	<a :href="getInstaUrl(talent)" v-if="talent.instagram_url" target="_blank" class="talents__reference c-grey"><span class="icon icon-instagram"></span></a>
              	<a :href="getVimeoUrl(talent)" v-if="talent.vimeo_url" target="_blank" class="talents__reference c-grey"><span class="icon icon-vimeo"></span></a>
              	<a :href="getYoutubeUrl(talent)" v-if="talent.youtube_url" target="_blank" class="talents__reference c-grey"><span class="icon icon-youtube"></span></a>
              </div>
            </div>
            <div class="table-data table-col-small">
              <p>{{ talent.city }}</p>
              <p class="c-grey">{{ talent.country }}</p>
            </div>
            <div class="table-data col-actions js-toggle-wrapper">
              <button type="button" class="col-actions__button js-toggle-button"><span class="col-actions__button-dot"></span></button>
              <div class="context-menu col-actions__context-menu js-toggle-content">
                <ul class="context-menu--items">
                  <li class="context-menu--items-element" v-on:click.prevent="Talent._edit(talent)"><span class="icon icon-edit"></span> Modifier le talent</li>
                  <li class="context-menu--items-element" v-on:click.prevent="Talent._delete(talent, getTalents)"><span class="icon icon-delete"></span> Supprimer le talent</li>
                </ul>
              </div>
            </div>
          </div>
        </div>
			</div>

		</div>

	</div>
</template>

<script>
	export default {
		props: [],

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
        open_options: null,
			}
		},

		mounted() {
    	// On mounted, get all talents list
    	this.getTalents(true);

    	// Catch event when talent is added or updated
    	this.$bus.$on('TALENT_ADD_OR_UPDATE', () => {
	      this.getTalents(); // Call talents with filters
	    });

    	this.setSkillsSelect();
    	this.setJobsSelect();
    },

    computed: {
    },

    methods: {

    	/**
    	 * getTalents
    	 * @return void
    	 */
    	getTalents(all){
    			var params = {};

    			setTimeout(() => {
	      		if(this.filters.alpha) params.filter_alpha = this.filters.alpha;
	      		if(this.filters.name) params.filter_name = this.filters.name;
	      		if(this.filters.date) params.filter_date = moment(this.filters.date).format('YYYY-MM-DD');
	      		if(this.filters.jobs.length > 0) params.filter_jobs = this.filters.jobs;
	      		if(this.filters.skills.length > 0) params.filter_skills = this.filters.skills;
	      		if(this.filters.date) this.filters.date = moment(this.filters.date).format('DD/MM/YYYY');

	      		axios.get("/api/talent", {
	      			params: params
	      		}).then(res => {
	      			if(res.success === false){
	  	      		// Todo : Error
	  	      	} else {
	  	      		this.talents = res.data.datas; // Update talents list
	  	      		if(all) this.all_talents = res.data.datas; // Set all talents for first time
	  	      	}
	  	      }).catch(error => console.log(error));
	      	}, 10);
		},
		

    	/**
    	 * Set skills select2
    	 */
    	setSkillsSelect(){
    	 	$(() => {
    	 		$('.js-skills-data').select2({
            dropdownCssClass: 'multiple-choices filters-dropdown',
    	 			placeholder: "Compétence(s)",
            closeOnSelect: false,
            debug: true,
            language: {
			        searching: function() {
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

    	 		$('.js-skills-data').on("select2:select", (e) => { this.getTalents(); });
    	 		$('.js-skills-data').on("select2:unselect", (e) => { this.getTalents(); });
    	 	});
    	},

    	/**
    	 * Set jobs select2
    	 */
    	setJobsSelect(){
    	 	$(() => {
    	 		$('.js-jobs-data').select2({
            dropdownCssClass: 'multiple-choices filters-dropdown',
    	 			placeholder: "Métier(s)",
    	 			//minimumInputLength: 1,
            closeOnSelect: false,
    	 			language: {
			        searching: function() { return "Chargement..."; }
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

					$('.js-jobs-data').on("select2:select", (e) => { this.getTalents(); });
    	 		$('.js-jobs-data').on("select2:unselect", (e) => { this.getTalents(); });
    	 	});
    	},

    	/**
    	 * Enable/Disable active letter
    	 */
    	setActiveLetter(letter){
    		if(this.filters.alpha == letter){
    			this.filters.alpha = null;
    		} else {
    			this.filters.alpha = letter;
    		}

    		this.getTalents();
    	},

    	hasTalentsWithLetter(letter){
    		const startsWith = this.all_talents.filter((talent) => {
    			return talent.firstname.toLowerCase().startsWith(letter.toLowerCase());
    		});

    		return startsWith.length > 0;
    	},

      /**
       * Clear field
       */
      clear(type){
        if(typeof this.filters[type] == 'object'){
            this.filters[type] = [];
        } else {
            this.filters[type] = null;
        }

        if($('.js-'+type+'-data')){
        	$('.js-'+type+'-data').val('').trigger('change');
      	}

        this.getTalents();
      },

      convert(talent){
      	return btoa(talent.id);
      },

      getInstaUrl(talent){
      	var url = talent.instagram_url.replace('https://instagram.com/', '');
      			url = talent.instagram_url.replace('http://instagram.com/', '');
      			url = talent.instagram_url.replace('https://www.instagram.com/', '');
      			// url = talent.instagram_url.replace('http://www.instagram.com/', '');
            console.log(url);

      	return 'https://www.instagram.com/' + url;
      },

      getVimeoUrl(talent){
      	var url = talent.vimeo_url.replace('https://vimeo.com/', '');
      			url = talent.vimeo_url.replace('http://vimeo.com/', '');
      			url = talent.vimeo_url.replace('https://www.vimeo.com/', '');
      			url = talent.vimeo_url.replace('http://www.vimeo.com/', '');

      	return 'https://www.vimeo.com/' + url;
      },

      getYoutubeUrl(talent){
      	var url = talent.youtube_url.replace('https://youtube.com/', '');
      			url = talent.youtube_url.replace('http://youtube.com/', '');
      			url = talent.youtube_url.replace('https://www.youtube.com/', '');
      			url = talent.youtube_url.replace('http://www.youtube.com/', '');

      	return 'https://www.youtube.com/' + url;
      }
    }

	}
</script>
