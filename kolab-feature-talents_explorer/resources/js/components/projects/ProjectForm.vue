<!--<template>
  <div>
    Header 
    <div class="popup-intro text-center mb-30">
      <h2 class="popup-maintitle">
      	<span v-if="!project.name">Créer un projet</span>
				<span v-else>{{ project.name }}</span>
      </h2>
      <p class="popup-maintext" v-if="!project.id">
      	Ce projet sera automatiquement créer dans la page <strong>Projets.</strong><br>
      	Une fois créé, il sera possible de le modifier plus tard.
    	</p>
    </div>
     ./Header 

     Form 
  	<form method="POST" v-on:submit.prevent="setProject" data-url="" class="form__project js-project-form" rel="form__project">
  		 Step 1 
  		<div v-show="step == 1" class="popup-box step-1">
        <input type="hidden" v-model="project.id">

        <div class="form-inner">
          <div class="form-box">
            <label for="" class="form-label">Projet <em>*</em></label>
            <input type="text" placeholder="Nom du projet *" v-model="project.name" class="form-field js-required">
          </div>
          <div class="form-box">
            <label for="" class="form-label">Production <em>*</em></label>
            <input type="text" placeholder="Nom de la production *" v-model="project.production" class="form-field js-required">
          </div>
          <div class="form-box">
            <label for="" class="form-label">Client <em>*</em></label>
            <select v-model="project.client_name" v-select2 id="form-clients-select2" class="form-field js-clients-data js-required" name="client_name"></select>
            <input type="phone" placeholder="Numéro de téléphone" v-model="project.client_phone" class="form-field">
            <input type="email" placeholder="Adresse mail" v-model="project.client_email" class="form-field">
          </div>
          <div class="form-box">
            <label for="" class="form-label">Responsable du projet</label>
            <input type="text" placeholder="Nom et prénom du responsable" v-model="project.responsable_name" class="form-field">
            <input type="phone" placeholder="Numéro de téléphone" v-model="project.responsable_phone" class="form-field">
            <input type="email" placeholder="Adresse mail" v-model="project.responsable_email" class="form-field">
          </div>
          <div class="form-box mb-0">
            <label for="" class="form-label mb-15">Catégorie du projet <em>*</em></label>
            <div class="js-checkbox-list js-required">
              <p class="form-error">Choisir une catégorie</p>
              <div class="wrapper-checkbox" v-for="projectCategory in projectCategories">
                <input type="radio" v-model="project.category_id" :name="Global._slugify(projectCategory.name)" :id="Global._slugify(projectCategory.id + '-' + projectCategory.name)" :value="projectCategory.id">
                <span class="radio"></span>
                <label :for="projectCategory.id + '-' + projectCategory.name" class="checkbox-label">{{ projectCategory.name }}</label>
              </div>
            </div>
          </div>

          <div class="popup-pagination">
            <span class="popup-pagination__item is-active"></span>
            <span class="popup-pagination__item" v-bind:class="{ 'is-active' : step >= 2 }" type="button" v-on:click.prevent="nextStep()" ></span>
          </div>

          <p v-if="error_msg" class="form-error">Veuillez remplir les champs obligatoires</p>
          <p class="form-required-mention"><em>*</em> Champs obligatoires</p>
        </div>

  		</div>
      <button v-show="step == 1" type="button" v-on:click.prevent="nextStep()" class="button is-gradient popup-button">Suivant</button>
       ./Step 1 

       Step 2 
  		<div v-show="step == 2" class="popup-box step-2">
        <div class="form-box">
            <label for="" class="form-label">Date de deadline <em>*</em></label>
            <v-date-picker :timezone="'Europe/Paris'" v-model="project.date_deadline" :popover="{ visibility: 'click' }" class="form-field" color="kpurple" />
        </div>

        <div class="form-box">
          <label for="" class="form-label">Assigner un / plusieurs talent(s)</label>
          <div class="form-field-wrapper">
            <span class="icon icon-search"></span>
            <select type="text" v-model="project.talents_id" v-select2 id="form-talents-select2" class="form-field js-talents-data" name="talents[]" multiple="multiple"></select>
          </div>
        </div>

        <div class="popup-pagination">
          <span class="popup-pagination__item "></span>
          <span class="popup-pagination__item is-active" v-bind:class="{ 'is-active' : step >= 2 }"></span>
        </div>

        <p class="form-required-mention"><em>*</em> Champs obligatoires</p>
  		</div>
  		./Step 2 

      <button v-show="step == 2" type="button" v-on:click="prevStep()" class="action-link is-white popup-button is-left">
      	<span class="icon icon-long-arrow"></span> Précédent
      </button>
      <button v-show="step == 2" type="submit" class="button is-gradient popup-button">
        <span v-if="!project.id">Créer</span>
        <span v-else>Enregistrer</span>
      </button>

  	</form>
  	./Form 

  </div>
</template>

<script>
  export default {
		props: ['_project'],

		data() {
      return {
      	dashboard: this.getComponent('Dashboard'),
      	step: 1,
      	project: {
      		id: null,
      		name: '',
          production:'',
          client:'',
          client_name:'',
          client_phone:'',
          client_email:'',
          responsable_name:'',
          responsable_phone:'',
          responsable_email:'',
          category_id :'',
          date_deadline: new Date(),
          talents: [],
          talents_id: []
        },
        projectCategories:[],
        error_msg: false,
      }
    },

    created() {
    	if(this._project) {
				this.project = this._project;

				this.project.client_name = this._project.client.name;

				var talents = [];
				this.project.talents.forEach(talent => {
					talents.push(talent.id);
				});
				this.project.talents_id = talents;

				this.project.date_deadline = new Date(this._project.date_deadline);
			}
    },

    mounted() {
		    this.getProjectCategory();

        this.setTalentSelect();
        this.setClientSelect();
        this.triggerSelect2();
    },

    methods: {
    	setProject(){

    		if(this.step == 1){
    			this.nextStep();
    		}

    		var errors = Vue.prototype.Global._checkForFields('.js-project-form');
        if(errors > 0) return false;

    		axios.post("/api/project", {
    			project: this.project
	      }).then(res => {
	      	if(res.success === false){
	      		// Error
	      	} else {
	      		this.$bus.$emit('PROJECT_ADD_OR_UPDATE', res.data); // Emit add or update project event
    				this.$bus.$emit('ACTION_CHANGED', {action: null}); // Close modal

    				this.$bus.$emit('ACTION_CHANGED', {
            	action: 'CONGRATS',
            	type: 'PROJECT',
            	project: res.data.datas, // created project
            	more: !this.project.id // more action if new project
            }); // Congrats modal
	      	}
	      }).catch(error => console.log(error));
    	},

      /**
       * Set Client select2
       */
      setClientSelect(){
        $(() => {
          $('.form__project .js-clients-data').select2({
            selectionCssClass: 'simple-field',
            dropdownCssClass: 'has-search',
            placeholder: "Nom du client *",
            tags: "true",
            language: {
			        searching: function() {
		            return "Chargement...";
			        }
				    },
            ajax: {
              url: '/api/client',
              processResults: function (data) {
                var data = $.map(data.datas, function (obj) {
                  obj.id = obj.name;
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
      },

      /**
       * Set Talent select2
       */
      setTalentSelect(){
        $(() => {
          $('.form__project .js-talents-data').select2({
            dropdownCssClass: 'multiple-choices',
            placeholder: "Rechercher un ou plusieurs talent(s)",
            language: {
			        searching: function() {
		            return "Chargement...";
			        }
				    },
            ajax: {
              url: '/api/talent',
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
      },

      /**
       * Fill select2
       * */
       triggerSelect2(){
       	  this.triggerClientSelect2();
       		this.triggerTalentsSelect2();
       },

      /**
       * Fill the job in the select2
       */
      triggerClientSelect2(){
      	$(() => {
       		let jobsSelect2 = $('#form-clients-select2');
 
          if(this.project.client){
         		let option = new Option(this.project.client.name, this.project.client.name, true, true);
         		jobsSelect2.append(option).trigger('change');
          }
       	});
      },

      /**
       * Fill the select2 with already selected Skills
       */
      triggerTalentsSelect2(){
       	let skillsSelect2 = $('#form-talents-select2');

       	this.project.talents.forEach(talent => {
       		let option = new Option(talent.name, talent.id, true, true);
       		skillsSelect2.append(option).trigger('change');
       	});
      },

      getProjectCategory(){
        axios.get("/api/project-category", {
            user  : this.$root.user,
        }).then(res => {
          if(res.success === false){
            // Error
          } else {
            this.projectCategories = res.data.datas;
          }
        }).catch(error => console.log(error));
      },

      prevStep(){
    		this.step --;
    	},

    	nextStep(){
    		var errors = Vue.prototype.Global._checkForFields('.js-project-form', this.step);
        if(errors > 0) {
          this.error_msg = true;
          return false;
        } else {
          this.error_msg = false;
          this.step ++;
        }

    	}
  	}
  }
</script>-->




<template>
  <div>
    <!-- Header -->
    <div class="popup-intro text-center mb-30">
      <h2 class="popup-maintitle">
      	<span v-if="!project.name">Créer un projet</span>
				<span v-else>{{ project.name }}</span>
      </h2>
      <p class="popup-maintext" v-if="!project.id">
      	Ce projet sera automatiquement créer dans la page <strong>Projets.</strong><br>
      	Une fois créé, il sera possible de le modifier plus tard.
    	</p>
    </div>
    <!-- ./Header -->

    <!-- Form -->
  	<form method="POST" v-on:submit.prevent="setProject" data-url="" class="form__project js-project-form" rel="form__project">
  		<!-- Step 1 -->
  		<div v-show="step == 1" class="popup-box step-1">
        <input type="hidden" v-model="project.id">

        <div class="form-inner">
          <div class="form-box">
            <label for="" class="form-label">Projet <em>*</em></label>
            <input type="text" placeholder="Nom du projet *" v-model="project.name" class="form-field js-required">
          </div>
          <div class="form-box">
            <label for="" class="form-label">Production <em>*</em></label>
            <input type="text" placeholder="Nom de la production *" v-model="project.production" class="form-field js-required">
          </div>
          <div class="form-box">
            <label for="" class="form-label">Client <em>*</em></label>
            <select v-model="project.client_name" v-select2 id="form-clients-select2" class="form-field js-clients-data js-required" name="client_name"></select>
            <input type="phone" placeholder="Numéro de téléphone" v-model="project.client_phone" class="form-field">
            <input type="email" placeholder="Adresse mail" v-model="project.client_email" class="form-field">
          </div>
          <div class="form-box">
            <label for="" class="form-label">Responsable du projet</label>
            <input type="text" placeholder="Nom et prénom du responsable" v-model="project.responsable_name" class="form-field">
            <input type="phone" placeholder="Numéro de téléphone" v-model="project.responsable_phone" class="form-field">
            <input type="email" placeholder="Adresse mail" v-model="project.responsable_email" class="form-field">
          </div>
          <div class="form-box mb-0">
            <label for="" class="form-label mb-15">Catégorie du projet <em>*</em></label>
            <div class="js-checkbox-list js-required">
              <p class="form-error">Choisir une catégorie</p>
              <div class="wrapper-checkbox" v-for="projectCategory in projectCategories">
                <input type="radio" v-model="project.category_id" :name="Global._slugify(projectCategory.name)" :id="Global._slugify(projectCategory.id + '-' + projectCategory.name)" :value="projectCategory.id">
                <span class="radio"></span>
                <label :for="projectCategory.id + '-' + projectCategory.name" class="checkbox-label">{{ projectCategory.name }}</label>
              </div>
            </div>
          </div>

          <div class="popup-pagination">
            <span class="popup-pagination__item is-active"></span>
            <span class="popup-pagination__item" v-bind:class="{ 'is-active' : step >= 2 }"></span>
          </div>

          <p v-if="error_msg" class="form-error">Veuillez remplir les champs obligatoires</p>
          <p class="form-required-mention"><em>*</em> Champs obligatoires</p>
        </div>

  		</div>
      <button v-show="step == 1" type="button" v-on:click.prevent="nextStep()" class="button is-gradient popup-button">Suivant</button>
      <!-- ./Step 1 -->

      <!-- Step 2 -->
  		<div v-show="step == 2" class="popup-box step-2">
        <div class="form-box">
            <label for="" class="form-label">Date de deadline <em>*</em></label>
            <v-date-picker :timezone="'Europe/Paris'" v-model="project.date_deadline" :popover="{ visibility: 'click' }" class="form-field" color="kpurple" />
        </div>

        <div class="form-box">
          <label for="" class="form-label">Assigner un / plusieurs talent(s)</label>
          <div class="form-field-wrapper">
            <span class="icon icon-search"></span>
            <select type="text" v-model="project.talents_id" v-select2 id="form-talents-select2" class="form-field js-talents-data" name="talents[]" multiple="multiple"></select>
          </div>
        </div>

        <div class="popup-pagination">
          <span class="popup-pagination__item is-active"></span>
          <span class="popup-pagination__item" v-bind:class="{ 'is-active' : step >= 2 }"></span>
        </div>

        <p class="form-required-mention"><em>*</em> Champs obligatoires</p>
  		</div>
  		<!-- ./Step 2 -->

      <button v-show="step == 2" type="button" v-on:click="prevStep()" class="action-link is-white popup-button is-left">
      	<span class="icon icon-long-arrow"></span> Précédent
      </button>
      <button v-show="step == 2" type="submit" class="button is-gradient popup-button">
        <span v-if="!project.id">Créer</span>
        <span v-else>Enregistrer</span>
      </button>

  	</form>
  	<!-- ./Form -->

  </div>
</template>

<script>
  export default {
		props: ['_project'],

		data() {
      return {
      	dashboard: this.getComponent('Dashboard'),
      	step: 1,
      	project: {
      		id: null,
      		name: '',
          production:'',
          client:'',
          client_name:'',
          client_phone:'',
          client_email:'',
          responsable_name:'',
          responsable_phone:'',
          responsable_email:'',
          category_id :'',
          date_deadline: new Date(),
          talents: [],
          talents_id: []
        },
        projectCategories:[],
        error_msg: false,
      }
    },

    created() {
    	if(this._project) {
				this.project = this._project;

				this.project.client_name = this._project.client.name;

				var talents = [];
				this.project.talents.forEach(talent => {
					talents.push(talent.id);
				});
				this.project.talents_id = talents;

				this.project.date_deadline = new Date(this._project.date_deadline);
			}
    },

    mounted() {
		    this.getProjectCategory();

        this.setTalentSelect();
        this.setClientSelect();
        this.triggerSelect2();
    },

    methods: {
    	setProject(){

    		if(this.step == 1){
    			this.nextStep();
    		}

    		var errors = Vue.prototype.Global._checkForFields('.js-project-form');
        if(errors > 0) return false;

    		axios.post("/api/project", {
    			project: this.project
	      }).then(res => {
	      	if(res.success === false){
	      		// Error
	      	} else {
	      		this.$bus.$emit('PROJECT_ADD_OR_UPDATE', res.data); // Emit add or update project event
    				this.$bus.$emit('ACTION_CHANGED', {action: null}); // Close modal

    				this.$bus.$emit('ACTION_CHANGED', {
            	action: 'CONGRATS',
            	type: 'PROJECT',
            	project: res.data.datas, // created project
            	more: !this.project.id // more action if new project
            }); // Congrats modal
	      	}
	      }).catch(error => console.log(error));
    	},

      /**
       * Set Client select2
       */
      setClientSelect(){
        $(() => {
          $('.form__project .js-clients-data').select2({
            selectionCssClass: 'simple-field',
            dropdownCssClass: 'has-search',
            placeholder: "Nom du client *",
            tags: "true",
            language: {
			        searching: function() {
		            return "Chargement...";
			        }
				    },
            ajax: {
              url: '/api/client',
              processResults: function (data) {
                var data = $.map(data.datas, function (obj) {
                  obj.id = obj.name;
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
      },

      /**
       * Set Talent select2
       */
      setTalentSelect(){
        $(() => {
          $('.form__project .js-talents-data').select2({
            dropdownCssClass: 'multiple-choices',
            placeholder: "Rechercher un ou plusieurs talent(s)",
            language: {
			        searching: function() {
		            return "Chargement...";
			        }
				    },
            ajax: {
              url: '/api/talent',
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
      },

      /**
       * Fill select2
       * */
       triggerSelect2(){
       	  this.triggerClientSelect2();
       		this.triggerTalentsSelect2();
       },

      /**
       * Fill the job in the select2
       */
      triggerClientSelect2(){
      	$(() => {
       		let jobsSelect2 = $('#form-clients-select2');
 
          if(this.project.client){
         		let option = new Option(this.project.client.name, this.project.client.name, true, true);
         		jobsSelect2.append(option).trigger('change');
          }
       	});
      },

      /**
       * Fill the select2 with already selected Skills
       */
      triggerTalentsSelect2(){
       	let skillsSelect2 = $('#form-talents-select2');

       	this.project.talents.forEach(talent => {
       		let option = new Option(talent.name, talent.id, true, true);
       		skillsSelect2.append(option).trigger('change');
       	});
      },

      getProjectCategory(){
        axios.get("/api/project-category", {
            user  : this.$root.user,
        }).then(res => {
          if(res.success === false){
            // Error
          } else {
            this.projectCategories = res.data.datas;
          }
        }).catch(error => console.log(error));
      },

      prevStep(){
    		this.step --;
    	},

    	nextStep(){
    		var errors = Vue.prototype.Global._checkForFields('.js-project-form', this.step);
        if(errors > 0) {
          this.error_msg = true;
          return false;
        } else {
          this.error_msg = false;
          this.step ++;
        }

    	}
  	}
  }
</script>


