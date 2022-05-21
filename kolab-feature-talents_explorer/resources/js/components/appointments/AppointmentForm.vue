<template>
	<div>
		<div class="popup-intro text-center mb-30">
      <h2 class="popup-maintitle">
      	<span v-if="!appointment.id">Création de rendez-vous</span>
				<span v-else>Modifier le rendez-vous</span>
      </h2>
      <p v-if="!appointment.id" class="popup-maintext">
      	Ce rendez-vous sera automatiquement créé dans la page <strong>Dashboard</strong> et <strong>Planning</strong>.<br>
      	Une fois créé, il sera possible de le modifier plus tard.
      </p>
    </div>
		<form method="POST" v-on:submit.prevent="setAppointment" class="form__appointment js-appointment-form" rel="form__appointment">
			<div v-show="step == 1" >
			<input type="hidden" v-model="appointment.id">

			<div class="popup-box">
				<div class="form-box">
	        		<label for="" class="form-label"> Titre du rendez-vous <em>*</em></label>
	        		<input type="text" v-model="appointment.firstname" placeholder="Titre du rendez-vous (exemple : Rendez-vous avec John Doe)" class="form-field js-required">
					<!--<input type="text" v-model="appointment.job" placeholder="Métier" class="form-field">-->
	      		</div>

				<div class="form-box">
	        		<label for="" class="form-label">Contact de l'interlocuteur<em>*</em></label>
					<input type="email" v-model="appointment.email" placeholder="Email *" class="form-field js-required">
					<input type="phone" v-model="appointment.phone" placeholder="Téléphone" class="form-field">
				</div>
				<div class="form-box">
          			<label for="" class="form-label">Assigner un / plusieurs talent(s)</label>
          			<div class="form-field-wrapper">
            			<span class="icon icon-search"></span>
            			<select type="text" v-select2 id="form-talents-select2" class="form-field js-talents-data" name="talents[]" multiple="multiple"></select>
          			</div>
        		</div>
				<!--<div class="form-box">
					<label for="" class="form-label">Talent(s) assigné(s) au rendez-vous</label>
					<div class="form-field-wrapper">
						<p id="select2-form-talents-select2-container-choice-wetm-1" class="select2-selection__choice__display"></p>
					</div>
				</div>-->
        		<p v-if="error_msg" class="form-error">Veuillez remplir les champs obligatoires</p>
       			<p class="form-required-mention"><em>*</em> Champs obligatoires</p>
			</div>
			<div class="popup-pagination">
          <span class="popup-pagination__item is-active"></span>
          <span class="popup-pagination__item" v-show="step == 1" type="button" v-on:click.prevent="nextStep()" > </span>
        </div>
		<button v-show="step == 1" type="button" v-on:click.prevent="nextStep()" class="button is-gradient popup-button">Suivant</button>
			</div>
		<div class="popup-box" id="task--items" v-show="step == 2">
				<div class="row">
					<div class="col-lg-8">
						<div class="form-box">
			        <label for="" class="form-label">Jour de rendez-vous <em>*</em></label>
			        <div class="form-field-wrapper">
				        <span class="icon icon-calendar"></span>
								<v-date-picker v-model="appointment.date" :popover="{ visibility: 'click', placement: $screens({ default: 'bottom-start' }) }" @dayclick="dayClick('appointment_date')" color="kpurple" :class="'w-100'">
							    <input v-model="appointment.convert_date" class="form-field js-datepicker js-required" autocomplete="off" placeholder="Date de RDV *" id="appointment_date" />
								</v-date-picker>
							</div>
						</div>
					</div>
				</div>


				<div class="form-box is-hours">
	        		<label for="" class="form-label">Heure de rendez-vous</label>
					<textarea type="number" v-model="appointment.hours" placeholder="__" minlength="2" maxlength="2" class="hour-field"></textarea>
					<!--<select v-model="appointment.hours" class="hour-field js-hour-data js-select-hours">
						<option v-for="index in 24">{{ (index-1).toString().padStart(2, '0') }}</option>
					</select>-->
					<textarea type="number" v-model="appointment.minutes" placeholder="__" class="hour-field" minlength="2" maxlength="2"></textarea>
					<!--<select v-model="appointment.minutes" class="hour-field js-hour-data js-select-minutes">
						<option v-for="index in 60" v-if="index == 1 || index%5 == 0 && index != 60">
							{{ (index).toString().padStart(2, '0').replace('01', '00') }}
						</option>
					</select>-->
				</div>
				<div class="form-box">
	        		<label for="" class="form-label">Lieu</label>
					<div class="form-field-wrapper">
						<span class="picto picto-location2"></span>
						<select v-model="appointment.location" v-select2 id="location-data" class="form-field js-location-data" name="location"> </select>
					</div>


				</div>
				<div class="form-box notes__form-box js-toggle-wrapper">
                            <label for="" class="form-label no-dot">Note</label>
                            <div class="form-field js-toggle-button notes__input"><span class="form-field-placeholder js-notes-placeholder">Ajouter une note</span> <span class="notes__field-value js-notes-value"></span></div>
                            <textarea type="text" placeholder="Ex : Travailler sur les retakes pour v3" v-model="appointment.note" class="form-field notes__textarea js-toggle-content js-notes-textarea no-caps" maxlength="300"></textarea>
                    </div>
				 <div class="popup-pagination">
          <span class="popup-pagination__item is-disable" v-show="step == 2" type="button"  v-on:click.prevent="prevStep()"></span>
          <span class="popup-pagination__item is-active"></span>
        </div>
		</div>
		 <button v-show="step == 1" type="button" v-on:click.prevent="nextStep()" class="button is-gradient popup-button">Suivant</button>
		 <button v-show="step == 2" type="button" v-on:click="prevStep()" class="action-link is-white popup-button is-left">
      	<span class="icon icon-long-arrow"></span> Précédent
      </button>

			<button v-show="step == 2" type="submit" class="button is-gradient popup-button">
				<span v-if="!appointment.id">Créer le rendez-vous</span>
				<span v-else>Modifier le rendez-vous</span>
			</button>
		</form>
	</div>
</template>

<script>
	export default {
		props: ['appointments','_appointment','users'],

		data() {
			return {
				step: 1,
				searchTalent: false,
        		selectedTalent: [],
				appointment: {
					id: null,
					firstname: null,
					lastname: null,
					phone: null,
					job:	null,
					email: null,
					date: null,
					hours: null,
					minutes: null,
					room: null,
					room_id: null,
					location: null,
					convert_date: null,
					talents: [],
					note: null,
					talent_id: [],
					talent_name: '',

				},
        error_msg: false,
			}
		},

		created() {
			if(this._talent && this._appointment.id) {
				this.appointment = this._appointment;
				this.appointment.hours = this.getHours(this.appointment.datetime).padStart(2, '0');
				this.appointment.minutes = this.getMinutes(this.appointment.datetime).padStart(2, '0');
				this.appointment.date = moment(this.appointment.appointment_date).format('YYYY-MM-DD');
				this.appointment.convert_date = moment(this.appointment.appointment_date).format('Do MMMM YYYY');
				this.step = 2;
			}
		},

		mounted() {
			this.setHoursSelect();
			//this.setRoomSelect();
			//this.triggerRoomSelect2();
			this.setTalentSelect();
			this.setLocationSelect();
			this.triggerLocationSelect2();
			this.getNotes();
		},

		methods: {

			dayClick(inputId){
				setTimeout(() => {
					this.appointment.date = moment(this.appointment.date).format('YYYY-MM-DD');
					this.appointment.convert_date = moment(this.appointment.date).format('Do MMMM YYYY');
					$('.js-datepicker').val(this.appointment.convert_date);
				}, 10);

				// Add 'has-value' class (display black icon)
				$('#' + inputId).closest('.form-field-wrapper').addClass('has-value');
			},

			triggerTalentsSelect2(){
       	let skillsSelect2 = $('#form-talents-select2');

       	this.project.talents.forEach(talent => {
       		let option = new Option(talent.name, talent.id, true, true);
       		skillsSelect2.append(option).trigger('change');
       	});
      },

			/**
    	 * [setAppointment description]
    	 */
    	 setAppointment(){

    	 	var errors = Vue.prototype.Global._checkForFields('.js-appointment-form');
    	 	if(errors > 0) {
          this.error_msg = true;
          return false;
        } else {
          this.error_msg = false;
        }

    	 	this.appointment.date = moment(this.appointment.date).format('YYYY-MM-DD');

    	 	axios.post("/api/appointment", {
				 appointment: this.appointment,
				 talents: this.selectedTalent.id

    	 	}).then(res => {
    	 		if(res.data.success === false){
	      		console.log(res.data.datas.message); // Todo : popup error
	      	} else {
	      		this.$bus.$emit('APPOINTMENT_ADD_OR_UPDATE', res.data); // Emit add or update appointment event
	      		this.$bus.$emit('ACTION_CHANGED', {
            	action: 'CONGRATS',
            	type: 'APPOINTMENT'
            }); // Congrats modal
	      	}
	      }).catch(error => console.log(error));
		 },
		 triggerSelect2(){
       	  this.triggerClientSelect2();
       		this.triggerTalentsSelect2();
	   },
	   triggerTalentsSelect2(){
       	let skillsSelect2 = $('#form-talents-select2');

       	this.appointment.talents.forEach(talent => {
       		let option = new Option(talent.name, talent.id, true, true);
       		skillsSelect2.append(option).trigger('change');
       	});
      },
	setTalentSelect(){
        $(() => {
          $('.form__appointment .js-talents-data').select2({
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
		$('.js-talent-data').on("select2:select", function(e){
	        	var key = $(this).attr('data-key');
        		$this.appointment[key].talent_name = e.params.data.name;
	        });
	    	 	$('.js-talent-data').on("select2:unselect", function(e){
	    	 		var key = $(this).attr('data-key');
        		$this.appointment[key].talent_name = '';
	    	 	});
      },
    	 /**
	       * Set Hours select2
	       */
	      setHoursSelect(){
          $(() => {
            $('.form__appointment .js-hour-data').select2({
            	minimumResultsForSearch: -1,
          		dropdownCssClass: 'is-hours',
            });

            $('.js-select-hours').on("select2:select", (e) => {
		        	this.appointment.hours = e.params.data.text;
		        });
		    	 	$('.js-select-minutes').on("select2:select", (e) => {
		        	this.appointment.minutes = e.params.data.text;
		        });
          });
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

		},
		/**
       * Set location select2
       */
      setLocationSelect(){
        $(() => {
          $('.form__appointment .js-location-data').select2({
          	selectionCssClass: 'simple-field',
            dropdownCssClass: 'has-search',
            placeholder: "Lieu",
            tags: "true",
            language: {
			        searching: function() {
		            return "Chargement...";
			        }
				    },
            ajax: {
              url: '/api/appointment/location',
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
       * Fill the select2 with location
       */
      triggerLocationSelect2(){
       	let locationSelect2 = $('#location-data');

       	if(this.appointment.location){
       		let option = new Option(this.appointment.location, this.appointment.location, true, true);
       		locationSelect2.append(option).trigger('change');
       	}
      },

		getNotes() {
        $(document).on('click', (e) => {
          $('.js-notes-textarea').each(function(){

            if ($(this).is(':visible') && !e.target.matches('.js-toggle-content, .js-toggle-content *')) {
              var $notesField = $(this).parent().find('.js-notes-value');

              if($(this).val() !== '') {
                var notes = $(this).val();

                $notesField.parent().addClass('has-value');
                $notesField.html(notes);

                if($(this).val().length > 36) {
                  $notesField.addClass('is-cut');
                } else {
                  $notesField.removeClass('is-cut');
                }
              } else {
                $notesField.parent().removeClass('has-value');
                $notesField.html('');
              }
            }

          });
        });
	  },


      setNotes(){
      	$('.js-notes-textarea').each(function(){
	      		var $notesField = $(this).parent().find('.js-notes-value');

	      		if($(this).val() !== '') {
	            var notes = $(this).val();

	            $notesField.parent().addClass('has-value');
	            $notesField.html(notes);

	            if($(this).val().length > 36) {
	              $notesField.addClass('is-cut');
	            } else {
	              $notesField.removeClass('is-cut');
	            }
	          } else {
	            $notesField.parent().removeClass('has-value');
	            $notesField.html('');
	          }
      	});
      },

	      /**
	       * Set Room select2

	      setRoomSelect(){
          $(() => {
            $('.form__appointment .js-room-data').select2({
            	minimumResultsForSearch: -1,
              placeholder: "Choix de la salle",
              tags:"true",
              ajax: {
                url: '/api/room',
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
			**/
	    /**
       * Fill the job in the select2

      triggerRoomSelect2(){
       	let roomSelect2 = $('#room-data');

       	if(this.appointment.room){
     			let option = new Option(this.appointment.room.name, this.appointment.room.id, true, true);
     			roomSelect2.append(option).trigger('change');
     		}
      },**/


       // Set location select2

      setLocationSelect(){
        $(() => {
          $('.form__appointment .js-location-data').select2({
          	selectionCssClass: 'simple-field',
            dropdownCssClass: 'has-search',
            placeholder: "Lieu",
            tags: "true",
            language: {
			        searching: function() {
		            return "Chargement...";
			        }
				    },
            ajax: {
              url: '/api/appointment/location',
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


       // Fill the select2 with location

      triggerLocationSelect2(){
       	let locationSelect2 = $('#location-data');

       	if(this.appointment.location){
       		let option = new Option(this.appointment.location, this.appointment.location, true, true);
       		locationSelect2.append(option).trigger('change');
       	}
	  }

    }
  }
 </script>


<!--<template>
	<div>
		<div class="popup-intro text-center mb-30">
      <h2 class="popup-maintitle">
      	<span v-if="!appointment.id">Création de rendez-vous</span>
				<span v-else>Modifier le rendez-vous</span>
      </h2>
      <p v-if="!appointment.id" class="popup-maintext">
      	Ce rendez-vous sera automatiquement créé dans la page <strong>Dashboard</strong> et <strong>Planning</strong>.<br>
      	Une fois créé, il sera possible de le modifier plus tard.
      </p>
    </div>
		<form method="POST" v-on:submit.prevent="setAppointment" class="form__appointment js-appointment-form" rel="form__appointment">
			<input type="hidden" v-model="appointment.id">

			<div class="popup-box">
				<div class="form-box">
	        <label for="" class="form-label">Rendez-vous <em>*</em></label>
	        <input type="text" v-model="appointment.lastname" placeholder="Nom *" class="form-field js-required">
					<input type="text" v-model="appointment.firstname" placeholder="Prénom" class="form-field">
					<input type="text" v-model="appointment.job" placeholder="Métier" class="form-field">
	      </div>

				<div class="form-box">
	        <label for="" class="form-label">Contact <em>*</em></label>
					<input type="email" v-model="appointment.email" placeholder="Email *" class="form-field js-required">
					<input type="phone" v-model="appointment.phone" placeholder="Téléphone" class="form-field">
				</div>

				<div class="row">
					<div class="col-lg-8">
						<div class="form-box">
			        <label for="" class="form-label">Jour de rendez-vous <em>*</em></label>
			        <div class="form-field-wrapper">
				        <span class="icon icon-calendar"></span>
								<v-date-picker v-model="appointment.date" :popover="{ visibility: 'click', placement: $screens({ default: 'bottom-start' }) }" @dayclick="dayClick('appointment_date')" color="kpurple" :class="'w-100'">
							    <input v-model="appointment.convert_date" class="form-field js-datepicker js-required" autocomplete="off" placeholder="Date de RDV *" id="appointment_date" />
								</v-date-picker>
							</div>
						</div>
					</div>
				</div>-->


			<!--	<div class="form-box is-hours">
	        <label for="" class="form-label">Heure de rendez-vous</label>-->
					<!-- <input type="number" v-model="appointment.hours" placeholder="00" minlength="2" maxlength="2" class="hour-field" /> -->
					<!--<select v-model="appointment.hours" class="hour-field js-hour-data js-select-hours">
						<option v-for="index in 24">{{ (index-1).toString().padStart(2, '0') }}</option>
					</select>-->
					<!-- <input type="number" v-model="appointment.minutes" placeholder="00" class="hour-field" minlength="2" maxlength="2" /> -->
					<!--<select v-model="appointment.minutes" class="hour-field js-hour-data js-select-minutes">
						<option v-for="index in 60" v-if="index == 1 || index%5 == 0 && index != 60">
							{{ (index).toString().padStart(2, '0').replace('01', '00') }}
						</option>
					</select>
				</div>-->

				<!-- <div class="form-box">
	        <label for="" class="form-label">Salle</label>
					<select v-model="appointment.room_id" v-select2 id="room-data" class="form-field js-room-data"></select>
				</div> -->

				<!--<div class="form-box">
	        <label for="" class="form-label">Lieu</label>
					<select v-model="appointment.location" v-select2 id="location-data" class="form-field js-location-data" name="location"></select>
				</div>

        <p v-if="error_msg" class="form-error">Veuillez remplir les champs obligatoires</p>
        <p class="form-required-mention"><em>*</em> Champs obligatoires</p>
			</div>

			<button type="submit" class="button is-gradient popup-button">
				<span v-if="!appointment.id">Créer le rendez-vous</span>
				<span v-else>Modifier le rendez-vous</span>
			</button>
		</form>
	</div>
</template>

<script>
	export default {
		props: ['_appointment'],

		data() {
			return {
				appointment: {
					id: null,
					lastname: null,
					firstname: null,
					job: null,
					phone: null,
					email: null,
					date: null,
					hours: null,
					minutes: null,
					room: null,
					room_id: null,
					location: null,
					convert_date: null,
				},
        error_msg: false,
			}
		},

		created() {
			if(this._appointment && this._appointment.id) {
				this.appointment = this._appointment;
				this.appointment.hours = this.getHours(this.appointment.datetime).padStart(2, '0');
				this.appointment.minutes = this.getMinutes(this.appointment.datetime).padStart(2, '0');
				this.appointment.date = moment(this.appointment.appointment_date).format('YYYY-MM-DD');
				this.appointment.convert_date = moment(this.appointment.appointment_date).format('Do MMMM YYYY');
			}
		},

		mounted() {
			this.setHoursSelect();
			//this.setRoomSelect();
			//this.triggerRoomSelect2();
			this.setLocationSelect();
			this.triggerLocationSelect2();
		},

		methods: {

			dayClick(inputId){
				setTimeout(() => {
					this.appointment.date = moment(this.appointment.date).format('YYYY-MM-DD');
					this.appointment.convert_date = moment(this.appointment.date).format('Do MMMM YYYY');
					$('.js-datepicker').val(this.appointment.convert_date);
				}, 10);

				// Add 'has-value' class (display black icon)
				$('#' + inputId).closest('.form-field-wrapper').addClass('has-value');
			},

			/**
    	 * [setAppointment description]
    	 */
    	 setAppointment(){

    	 	var errors = Vue.prototype.Global._checkForFields('.js-appointment-form');
    	 	if(errors > 0) {
          this.error_msg = true;
          return false;
        } else {
          this.error_msg = false;
        }

    	 	this.appointment.date = moment(this.appointment.date).format('YYYY-MM-DD');

    	 	axios.post("/api/appointment", {
    	 		appointment 	: this.appointment
    	 	}).then(res => {
    	 		if(res.data.success === false){
	      		console.log(res.data.datas.message); // Todo : popup error
	      	} else {
	      		this.$bus.$emit('APPOINTMENT_ADD_OR_UPDATE', res.data); // Emit add or update appointment event
	      		this.$bus.$emit('ACTION_CHANGED', {
            	action: 'CONGRATS',
            	type: 'APPOINTMENT'
            }); // Congrats modal
	      	}
	      }).catch(error => console.log(error));
    	 },

    	 /**
	       * Set Hours select2
	       */
	      setHoursSelect(){
          $(() => {
            $('.form__appointment .js-hour-data').select2({
            	minimumResultsForSearch: -1,
          		dropdownCssClass: 'is-hours',
            });

            $('.js-select-hours').on("select2:select", (e) => {
		        	this.appointment.hours = e.params.data.text;
		        });
		    	 	$('.js-select-minutes').on("select2:select", (e) => {
		        	this.appointment.minutes = e.params.data.text;
		        });
          });
	      },

	      /**
	       * Set Room select2
	       */
	      setRoomSelect(){
          $(() => {
            $('.form__appointment .js-room-data').select2({
            	minimumResultsForSearch: -1,
              placeholder: "Choix de la salle",
              tags:"true",
              ajax: {
                url: '/api/room',
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
       * Fill the job in the select2
       */
      triggerRoomSelect2(){
       	let roomSelect2 = $('#room-data');

       	if(this.appointment.room){
     			let option = new Option(this.appointment.room.name, this.appointment.room.id, true, true);
     			roomSelect2.append(option).trigger('change');
     		}
      },

      /**
       * Set location select2
       */
      setLocationSelect(){
        $(() => {
          $('.form__appointment .js-location-data').select2({
          	selectionCssClass: 'simple-field',
            dropdownCssClass: 'has-search',
            placeholder: "Lieu",
            tags: "true",
            language: {
			        searching: function() {
		            return "Chargement...";
			        }
				    },
            ajax: {
              url: '/api/appointment/location',
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
       * Fill the select2 with location
       */
      triggerLocationSelect2(){
       	let locationSelect2 = $('#location-data');

       	if(this.appointment.location){
       		let option = new Option(this.appointment.location, this.appointment.location, true, true);
       		locationSelect2.append(option).trigger('change');
       	}
      },

    }
  }
 </script>-->
