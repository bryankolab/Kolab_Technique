<template>
    <div class="popup-wrapper">
        <div class="popup-intro text-center mb-30">
          <h2 class="popup-maintitle">
          	<span v-if="!selectedProject">Création de tâche(s)</span>
          	<span v-if="selectedProject">{{ selectedProject.name }}</span>
          </h2>
          <p class="popup-maintext">Cette ou ces tâches seront automatiquement créée(s) dans la page <strong>Planning</strong> et sur le <strong>Dashboard</strong>.<br> Une fois créée(s), il sera possible de le(s) modifier plus tard.</p>
        </div>
        <form method="POST" v-on:submit.prevent="setTask" data-url="" class="form__task" rel="form__task">
            <div class="popup-box is-small text-center pre-task-form" v-show="step == 1">
            	<p class="text c-main-grey mb-30">Afin de créer une ou plusieurs tâche(s), vous devez <strong>choisir un projet. </strong><br> <strong>Avez-vous déjà créer le projet</strong> sur lequelle vous voulez ajouter cette/ces tâches ?</p>

              <div class="row mb-20">
                <div class="col-md-6">
              	 <div v-on:click="searchProject = true;" class="box-button js-toggleclass"><span class="picto picto-search"></span> Rechercher<br> <strong>un projet</strong></div>
                </div>
                <div class="col-md-6">
              	 <div v-on:click="createProject()" class="box-button"><span class="picto picto-projects"></span> Créer<br> <strong>un nouveau projet</strong></div>
                </div>
              </div>

            	<div v-show="searchProject">
                <div class="form-box text-left mb-0">
                  <label for="" class="form-label no-dot">Projet</label>
                  <div class="d-flex">
                    <div class="form-field-wrapper">
                      <span class="icon icon-search"></span>
                      <select v-select2 class="filter-field js-project-data" name="project"></select>
                    </div>
                    <button type="button" v-on:click="step = 2" class="pre-task-form__button button is-gradient" v-bind:class="{'is-disabled' : selectedProject == null }">Appliquer</button>
                  </div>
                </div>

            	</div>
            </div>

            <div class="popup-box is-large" id="task--items" v-show="step == 2">
                <div v-for="(task, index) in tasks" v-if="tasks" :key="index" class="popup-form__item">
                    <p class="mb-10"><strong>Tâche #{{ index + 1 }}</strong></p>
                    <div class="row justify-content-between">
                        <input type="hidden" v-model="task.id">

                        <div class="form-box col-md-3">
                            <label for="" class="form-label no-dot">Talent</label>
                            <select v-model="task.talent_id" v-select2 :data-key="index" class="form-field js-talent-data" required></select>
                        </div>

                        <div class="form-box col-md-3">
                            <label for="" class="form-label no-dot">Type de tâche</label>
                            <select v-model="task.task_type_id" v-select2 :data-key="index" class="form-field js-task-type-data" required></select>
                        </div>

                        <div class="form-box col-md-3 notes__form-box js-toggle-wrapper">
                            <label for="" class="form-label no-dot">Note</label>
                            <div class="form-field js-toggle-button notes__input"><span class="form-field-placeholder js-notes-placeholder">Ajouter une note</span> <span class="notes__field-value js-notes-value"></span></div>
                            <textarea type="text" placeholder="Ex : Travailler sur les retakes pour v3" v-model="task.note" class="form-field notes__textarea js-toggle-content js-notes-textarea no-caps" maxlength="300"></textarea>
                        </div>

                        <div class="form-box col-md-3">
                            <label for="" class="form-label no-dot">Dates début et fin</label>
                            <v-date-picker mode="range" :popover="{ visibility: 'click', placement: $screens({ default: 'bottom-end' }) }" v-model="task.date" class="form-field" color="kpurple" :columns="$screens({ default: 1, lg: 2 })"></v-date-picker>
                        </div>
                    </div>

                    <button type="button" class="cross-button popup-form__item__delete" v-on:click="removeTask(index)">
                    	<span class="icon icon-bold-cross"></span>
                    </button>
                </div>
            </div>

            <button v-if="_goback !== false" v-on:click.prevent="goBack()" v-show="step == 2" type="button" class="action-link is-white popup-button is-left">
              <span class="icon icon-long-arrow"></span> Précédent
            </button>

            <button v-on:click.prevent="addTask()" v-if="step == 2" class="button is-gradient popup-button on-top"><span class="icon icon-plus"></span> Ajouter une tâche</button>
            <button type="submit" v-if="step == 2" class="button is-gradient popup-button">Créer les tâches</button>
        </form>
    </div>
</template>

<script>
export default {
    props: ['_project', '_goback'],

    data() {
      return {
    		step: 1,
    		searchProject: false,
    		selectedProject: null,
    		tasks: [],
        task: {
          id: null,
          name: '',
          talent_id: null,
          talent_name: '',
          task_type_id: null,
          task_type_name: '',
          note: null,
          date: {
              start: new Date(),
              end: new Date()
          },
        }
      }
    },

    created() {
    	if(this._project){
    		this.selectedProject = this._project;
    		this.step = 2;
    	}
    },

    mounted() {
    	// For creation (Different pour l'update)
      this.addTask();
      this.setProjectSelect();
      this.getNotes();
    },

    // computed: {
    //     isDisabled: function(){
    //       var noProject = this.selectedProject.length == 0;

    //       var isEmpty = noProject;

    //       // if(isEmpty){
    //       //   this.getTalents();
    //       // }

    //      return isEmpty;
    //     }
    // },

    methods: {
      setTask() {
        axios.post("/api/task", {
        	project: this.selectedProject.id,
          tasks: this.tasks
        }).then(res => {
          if (res.success === false) {
            // Error
          } else {
            this.$bus.$emit('TASK_ADD_OR_UPDATE', res.data); // Emit add or update talent event
            this.$bus.$emit('ACTION_CHANGED', {
            	action: 'CONGRATS',
            	type: 'TASK'
            }); // Congrats modal
          }
        }).catch(error => console.log(error));
      },

      createProject(){
      	this.$bus.$emit('ACTION_CHANGED', {action: 'SET_PROJECT'}); // Close modal
      },

      addTask(){
      	this.tasks.unshift(Object.assign({}, this.task));
      	this.setTalentSelect();
      	this.setTaskTypeSelect();

      	setTimeout(() => {
      		this.triggerTalentSelect2();
      		this.triggerTaskTypeSelect2();
      		this.setNotes();
      	}, 10);
      },

      removeTask(index){
      	this.tasks.splice(index, 1);
      },

      /**
       * Set Talent select2
       */
      setTalentSelect(){
      	var $this = this;

        $(() => {
        	$('.form__task .js-talent-data').select2({
            dropdownCssClass: 'has-search task-dropdown',
            placeholder: "Rechercher un talent",
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

	        $('.js-talent-data').on("select2:select", function(e){
	        	var key = $(this).attr('data-key');
        		$this.tasks[key].talent_name = e.params.data.name;
	        });
	    	 	$('.js-talent-data').on("select2:unselect", function(e){
	    	 		var key = $(this).attr('data-key');
        		$this.tasks[key].talent_name = '';
	    	 	});
        });
      },

      /**
       * Fill the talent in the select2
       */
      triggerTalentSelect2(){
      	var $this = this;
       	let talentSelect2 = $('.js-talent-data');

       	talentSelect2.each(function(index, item){
       		if($this.tasks[index].talent_id){
     				let option = new Option($this.tasks[index].talent_name, $this.tasks[index].talent_id, true, true);
     				$(this).append(option).trigger('change');
     			}
     		});
      },

      /**
       * Set TaskType select2
       */
      setTaskTypeSelect(){
      	var $this = this;

        $(() => {
        	$('.form__task .js-task-type-data').select2({
            dropdownCssClass: 'has-search task-dropdown',
            placeholder: "Choisir une tâche",
            language: {
			        searching: function() {
		            return "Chargement...";
			        }
				    },
            ajax: {
              url: '/api/task-type',
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

        	$('.js-task-type-data').on("select2:select", function(e){
	        	var key = $(this).attr('data-key');
        		$this.tasks[key].task_type_name = e.params.data.name;
	        });
	    	 	$('.js-task-type-data').on("select2:unselect", function(e){
	    	 		var key = $(this).attr('data-key');
        		$this.tasks[key].task_type_name = '';
	    	 	});
        });
      },

      /**
       * Fill the task type in the select2
       */
      triggerTaskTypeSelect2(){
      	var $this = this;
       	let taskTypeSelect2 = $('.js-task-type-data');

       	taskTypeSelect2.each(function(index, item){
       		if($this.tasks[index].task_type_id){
     				let option = new Option($this.tasks[index].task_type_name, $this.tasks[index].task_type_id, true, true);
     				$(this).append(option).trigger('change');
     			}
     		});
      },

      /**
       * Set Project select2
       */
      setProjectSelect(){
        $(() => {
          $('.form__task .js-project-data').select2({
            dropdownCssClass: 'has-search',
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
        });

        $('.js-project-data').on("select2:select", (e) => {
        	this.selectedProject = e.params.data;
        });
    	 	$('.js-project-data').on("select2:unselect", (e) => {
    	 		this.selectedProject = null;
    	 	});
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

      goBack(){
      	this.tasks = [];
      	this.addTask();
      	this.step = 1;
      }
    }
}
</script>
