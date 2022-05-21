<template>
	<div>
    <div class="popup-intro text-center mb-30">
      <h2 class="popup-maintitle">
      	<template v-if="!talent.id">Créer un talent</template>
      	<template v-else>{{ talent.firstname }} {{ talent.lastname }}</template>
      </h2>
      <p class="popup-maintext" v-if="!talent.id">
      	Ce talent sera automatiquement créer dans la page <strong>Talents</strong>.<br>
      	Vous pourrez le modifier plus tard.
      </p>
    </div>
		<form method="POST" v-on:submit.prevent="setTalent" class="js-talent-form" rel="form__talent">
			<input type="hidden" v-model="talent.id">

      <!-- Step 1 -->
      <div v-show="step == 1" class="popup-box step-1">

        <div class="form-inner">
          <div class="text-center mb-20">
            <h2 class="main-title c-main-grey">Informations personnelles</h2>
          </div>

          <div class="form-box">
            <label for="" class="form-label">Nom <em>*</em></label>
            <input type="text" placeholder="Nom *" v-model="talent.lastname" class="form-field js-required">
          </div>

          <div class="form-box">
            <label for="" class="form-label">Prénom <em>*</em></label>
            <input type="text" placeholder="Prénom *" v-model="talent.firstname" class="form-field js-required">
          </div>

          <div class="form-box">
            <label for="" class="form-label">Contact <em>*</em></label>
            <input type="phone" placeholder="Numéro de téléphone" v-model="talent.phone" class="form-field">
            <input type="email" placeholder="Adresse e-mail *" v-model="talent.email" class="form-field js-required">
          </div>

          <div class="form-box">
            <label for="" class="form-label">Localisation</label>
            <input type="text" placeholder="Ville" v-model="talent.city" class="form-field">
            <input type="text" placeholder="Pays" v-model="talent.country" class="form-field">
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

        <div class="form-inner">
          <div class="text-center mb-20">
            <h2 class="main-title c-main-grey">Informations professionnelles</h2>
          </div>

          <div class="form-box">
            <label for="" class="form-label">Métier <em>*</em></label>
            <div class="form-field-wrapper">
              <span class="icon icon-job"></span>
              <select v-model="talent.job_id" v-select2 class="form-field js-job-data js-select2 js-required" id="job-data" name="job"></select>
            </div>
          </div>

          <div class="form-box">
            <label for="" class="form-label">Compétences <em>*</em></label>
            <div class="form-field-wrapper">
              <span class="icon icon-skills"></span>
              <select v-model="talent.skills_ids" v-select2 class="form-field js-skills-data js-select2 js-required" id="skills-data" name="skills[]" multiple="multiple"></select>
            </div>
          </div>

          <div class="form-box">
            <label for="" class="form-label">Statut <em>*</em></label>
            <!-- <input type="text" placeholder="Statut" v-model="talent.profile_type" class="form-field"> -->
            <div class="form-field-wrapper">
              <span class="icon icon-contract"></span>
              <select class="filter-field js-status-data js-required" name="status" v-select2 v-model="talent.profile_type">
              	<option value="Freelance">Freelance</option>
              	<option value="Intermittent">Intermittent</option>
              	<option value="En contrat">En contrat</option>
              </select>
            </div>
          </div>

          <div class="form-box">
            <label for="" class="form-label">Site web</label>
            <input type="social" placeholder="ex : https://nom-du-site.fr" v-model="talent.profile_url" class="form-field">
          </div>

          <div class="form-box">
            <label for="" class="form-label">Instagram</label>
            <input type="social" placeholder="https://www.instagram.com/user" v-model="talent.instagram_url" class="form-field">
          </div>

          <div class="form-box">
            <label for="" class="form-label">Vimeo</label>
            <input type="social" placeholder="https://vimeo.com/user" v-model="talent.vimeo_url" class="form-field">
          </div>

          <div class="form-box">
            <label for="" class="form-label">YouTube</label>
            <input type="social" placeholder="https://www.youtube.com/user/username" v-model="talent.youtube_url" class="form-field">
          </div>

          <div class="popup-pagination">
            <span class="popup-pagination__item is-active"></span>
            <span class="popup-pagination__item" v-bind:class="{ 'is-active' : step >= 2 }"></span>
          </div>

          <p v-if="error_msg" class="form-error">Veuillez remplir les champs obligatoires</p>
          <p class="form-required-mention"><em>*</em> Champs obligatoires</p>
        </div>
      </div>

      <button v-show="step == 2" type="button" v-on:click="prevStep()" class="action-link is-white popup-button is-left">
        <span class="icon icon-long-arrow"></span> Précédent
      </button>
			<button v-show="step == 2" type="submit" class="button is-gradient popup-button">
				<span v-if="!talent.id">Créer le talent</span>
				<span v-else>Enregistrer</span>
			</button>

		</form>
	</div>
</template>

<script>
	export default {
		props: ['_talent'],

		data() {
			return {
        step: 1,
				talent: {
					id: null,
					firstname: '',
					lastname: '',
					email: '',
					phone : '',
					job : '',
					job_id: '',
					price : '',
					city : '',
					country : '',
					profile_type : '',
					profile_url : '',
					skills : [],
					skills_ids : [],
          instagram_url: '',
          vimeo_url: '',
          youtube_url: '',
				},
        error_msg: false,
			}
		},

		created() {
			if(this._talent) {
				this.talent = this._talent;

				var skills = [];
				this.talent.skills.forEach(skill => {
					skills.push(skill.id);
				});
				this.talent.skills_ids = skills;
			}
		},

		mounted() {
			this.setSkillsSelect();
			this.setJobsSelect();
			this.setStatusSelect();
			this.triggerSelect2();
      this.selectOnChange();
		},

		methods: {

    	/**
    	 * [setTalent description]
    	 */
    	 setTalent(){

    	 	if(this.step == 1){
    	 		this.nextStep();
    	 	}

        var errors = Vue.prototype.Global._checkForFields('.js-talent-form');
        if(errors > 0) return false;

    	 	axios.post("/api/talent", {
    	 		talent 	: this.talent,
    	 		// user : Vue.prototype.$loggedUser.id
    	 	}).then(res => {
    	 		if(res.data.success === false){
	      		console.log(res.data.datas.message); // Todo : popup error
	      	} else {
	      		this.$bus.$emit('TALENT_ADD_OR_UPDATE', res.data); // Emit add or update talent event
	      		this.$bus.$emit('ACTION_CHANGED', {
            	action: 'CONGRATS',
            	type: 'TALENT'
            }); // Congrats modal
	      	}
	      }).catch(error => console.log(error));
    	 },

    	/**
    	 * Set skills select2
    	 */
    	setSkillsSelect(){
    	 	$('.js-skills-data').select2({
          language: "fr",
          dropdownCssClass: 'multiple-choices',
  	 			placeholder: "Choisir une ou plusieurs compétence(s)",
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
    	},

      /**
    	 * Set jobs select2
    	 */
    	setJobsSelect(){
  	 		$('.js-job-data').select2({
          minimumResultsForSearch: -1,
  	 			tags: "true",
  	 			placeholder: "Choisir un métier",
  	 			language: {
		        searching: function() {
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
    	 * Set status select2
    	 */
    	setStatusSelect(){
    	 	$(() => {
    	 		$('.js-status-data').select2({
    	 			minimumResultsForSearch: Infinity,
    	 			placeholder: "Statut",
    	 		});
    	 	});
    	},

      /**
       * Fill select2
       * */
       triggerSelect2(){
       	this.triggerJobsSelect2();
       	this.triggerSkillsSelect2();
       },

      /**
       * Fill the job in the select2
       */
       triggerJobsSelect2(){
       	let jobsSelect2 = $('#job-data');

       	if(this.talent.job){
     			let option = new Option(this.talent.job.name, this.talent.job.id, true, true);
     			jobsSelect2.append(option).trigger('change');
     		}
       },

      /**
       * Fill the select2 with already selected Skills
       */
       triggerSkillsSelect2(){
       	let skillsSelect2 = $('#skills-data');

       	this.talent.skills.forEach(skill => {
       		let option = new Option(skill.name, skill.id, true, true);
       		skillsSelect2.append(option).trigger('change');
       	});
       },

        prevStep(){
          this.step --;
        },

        nextStep(){
          var errors = Vue.prototype.Global._checkForFields('.js-talent-form', this.step);
          if(errors > 0) {
            this.error_msg = true;
            return false;
          } else {
            this.error_msg = false;
            this.step ++;
          }
        },

        selectOnChange() {
          $('.js-select2').each(function(){
            $(this).on('change', function() {
              if ($(this).val() != ""){
                $(this).parent().addClass('has-value');
              } else {
                $(this).parent().removeClass('has-value');
              }
            });
          });
        }
      }
    }
 </script>
