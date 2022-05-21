<template>
	<!-- Wrapper -->
  <div class="planning" id="vue__planning">
		<ContextMenu ref="ContextMenu"></ContextMenu>

		<!-- Container -->
    <div class="main-container">
      <!--<AddButtons></AddButtons>-->

      <!-- View -->
      <div class="js-planning">

    		<!-- Filters -->
        <div class="filters form__task">
          <div class="row filters-row">
            <div class="search-col">
              <div class="filter-item has-picto">
                <span class="filter-picto picto-search"></span>
                <!-- <input type="text" v-model="filters.projects_id" placeholder="Rechercher un projet" class="filter-field search-field" v-on:keyup="getPlanning()" /> -->
                <!-- <select type="text" v-select2 v-model="filters.projects_id" class="js-project-data filter-field search-field"></select> -->
                <PlanningAutocomplete></PlanningAutocomplete>
                <button type="button" class="filters-delete is-cross" :disabled='!filters.project_name' v-on:click="clear('project_name')"><span class="filters-delete__picto icon-cross"></span></button>
              </div>
            </div>

            <div class="filters-col">
              <p class="filters-text"><span class="filters-main-icon icon icon-filters"></span> Filtrer par</p>
              <div class="filter-col">
                <div class="filter-item filter-select has-picto">
                  <span class="filter-picto picto-talent"></span>
                  <select type="text" v-model="filters.talents_id" v-select2 class="filter-field js-talents_id-data" multiple></select>
                  <button type="button" class="filters-delete" :disabled='filters.talents_id.length == 0' v-on:click="clear('talents_id')"><span class="filters-delete__picto icon icon-refresh"></span></button>
                </div>
              </div>
              <div class="filter-col">
                <div class="filter-item filter-select has-picto">
                  <span class="filter-picto picto-task"></span>
                  <select type="text" v-model="filters.task_types_id" v-select2 class="filter-field js-task_types_id-data" multiple></select>
                  <button type="button" class="filters-delete" :disabled='filters.task_types_id.length == 0' v-on:click="clear('task_types_id')"><span class="filters-delete__picto icon icon-refresh"></span></button>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- ./Filters -->

        <!-- Header -->
        <div class="planning__header">
          <!-- Date picker -->
          <div class="planning__header-col is-date">
            <button v-on:click="handleDateChange('DOWN')" class="nav-arrow is-prev">
            	<span class="sr-only">Précédent</span>
            </button>

            <p class="date"><strong>{{ date_month | monthLabel }}</strong> {{ date_year }}</p>

            <button v-on:click="handleDateChange('UP')" class="nav-arrow is-next">
            	<span class="sr-only">Suivant</span>
            </button>
          </div>

          <!-- Talent name -->
          <div class="planning__header-col-wrapper js-planning-header-wrapper">
              <button class="nav-arrow is-prev" v-on:click="handleSlide('PREV')"><span class="sr-only">Précédent</span></button>
            <div class="planning__header-col is-talents js-planning-row js-planning-row-talents">
              <template v-for="talent_id in selected_talents_id">
                <div class="planning__talent-col js-planning-cell">
                  <p v-if="selected_talents_name[talent_id]">
                    {{ selected_talents_name[talent_id][0].firstname }}
                    {{ selected_talents_name[talent_id][0].lastname | truncate(3, '.') }}
                  </p>
                </div>
              </template>
            </div>
            <button class="nav-arrow is-next" v-on:click="handleSlide('NEXT')"><span class="sr-only">Suivant</span></button>
          </div>

        </div>
        <!-- ./Header -->

        <!-- Planning -->
        <div class="block planning__block scroll-content js-planning-block">
          <div class="table-body planning-table">
            <div class="table-row js-planning-table-row" :data-day="moment(date).format('dddd')" v-for="(planningByTalent, date) in planning">
              <div class="table-data is-date js-planning-date" v-bind:class="{ 'is-current' : date == today }" v-on:click="highlightedRow($event)">{{ moment(date).format('dddd Do MMM YYYY') }}</div>


              <div class="table-row-wrapper">
                <div class="table-row js-planning-row">

                  <div class="table-data js-planning-cell" v-for="talent_id in selected_talents_id">
                		<div v-if="planningByTalent[talent_id] && planningByTalent[talent_id].events" class="table-cell-content">
                      <div class="js-planning-tasks-wrapper" v-bind:class="{ 'js-toggleclass-wrapper' : planningByTalent[talent_id].events.length > 1 }">
                        <p class="task__total-number">{{ planningByTalent[talent_id].events.length }} tâches</p>
                  			<div v-bind:class="{ 'js-toggleclass' : planningByTalent[talent_id].events.length > 1 }">
                          <span class="task__number" v-if="planningByTalent[talent_id].events.length > 1">
                          	<span class="task__number-inner">+{{ (planningByTalent[talent_id].events.length - 1) }}</span>
                            <span class="task__number-inner is-less">-</span>
                          </span>

                          <div class="task__wrapper">
                          	<template v-for="event in planningByTalent[talent_id].events">
                              <div v-if="event.type == 'TASK'" :class="'task is-task border-' + Global._slugify(event.task_type)" v-on:contextmenu.prevent="$refs.ContextMenu.show($event, 'TASK', {task: event}, getPlanning);" v-on:click="goToProject($event, event.project_id)">
                                <p class="task__type">{{ event.task_type }}</p>
                                <p class="task__name">{{ event.project_name }}</p>
                              </div>
                              <div v-if="event.type == 'APPOINTMENT'" class="task is-appointment" v-on:contextmenu.prevent="$refs.ContextMenu.show($event, 'APPOINTMENT', {appointment: event}, getPlanning);">
                                <p class="task__type">RDV - {{ event.firstname }} {{ event.lastname }}</p>
                                <p class="task__name">{{ event.job }}</p>
                            	</div>
                            </template>
                          </div>
                        </div>
                		  </div>
                    </div>
                  </div>

                </div>
              </div>

            </div>
          </div>
        </div>
        <!-- ./Planning -->

      </div>
      <!-- ./View -->
    </div>
    <!-- ./Container -->

  </div>
  <!-- ./Wrapper -->
</template>

<script>
export default {
    props: ['user'],

    data() {
        let date = new Date();
        return {
            planning: {},
            selected_talents_id: [],
            selected_talents_name: [],
            filters: {
            	talents_id: [],
            	projects_id: null,
            	project_name: '',
            	task_types_id: [],
            },
            date_month: date.getMonth() + 1,
            date_year: date.getFullYear(),
            today: moment().format('YYYY-MM-DD'),
            counter_slide: 0,
            translation: 0,
            cell_width: 250
        }
    },

    beforeMount() {
    },

    mounted() {
  		// Get from storage - Dates
  		// if(localStorage.getItem('date_month')){
  		// 	this.date_month = localStorage.getItem('date_month');
  		// }

  		// if(localStorage.getItem('date_year')){
  		// 	this.date_year = localStorage.getItem('date_year');
  		// }

  		// Get from storage - Talents
  		if(localStorage.getItem('filter_talents_id')){
  			this.filters.talents_id = JSON.parse(localStorage.getItem('filter_talents_id'));
  		}

  		if(localStorage.getItem('selected_talents_id')){
  			this.selected_talents_id = JSON.parse(localStorage.getItem('selected_talents_id'));
  		}

  		if(localStorage.getItem('selected_talents_name')){
  			this.selected_talents_name = JSON.parse(localStorage.getItem('selected_talents_name'));
  		}

  		// Get from storage - Tasks
  		if(localStorage.getItem('filter_task_types_id')){
  			this.filters.task_types_id = JSON.parse(localStorage.getItem('filter_task_types_id'));
  		}

  		if(localStorage.getItem('selected_task_types_id')){
  			this.selected_task_types_id = JSON.parse(localStorage.getItem('selected_task_types_id'));
  		}

  		if(localStorage.getItem('selected_task_types_name')){
  			this.selected_task_types_name = JSON.parse(localStorage.getItem('selected_task_types_name'));
  		}

  		// Get from storage - Tasks
  		// if(localStorage.getItem('filter_task_types_id')){
  		// 	this.filters.task_types_id = JSON.parse(localStorage.getItem('filter_task_types_id'));
  		// }

  		// if(localStorage.getItem('selected_task_types_id')){
  		// 	this.selected_task_types_id = JSON.parse(localStorage.getItem('selected_task_types_id'));
  		// }

  		// if(localStorage.getItem('selected_task_type_name')){
  		// 	this.selected_task_type_name = JSON.parse(localStorage.getItem('selected_task_type_name'));
  		// }

  		if(!localStorage.getItem('first_config')){
      	// Get talents
      	axios.get("/api/talent")
      	.then(res => {
      		res.data.datas.forEach((talent, index) => {
      			if($.inArray(talent.id, this.filters.talents_id) < 0){
      				this.filters.talents_id.push(talent.id);
      			}
      		});

      		this.getPlanning();
      	}).catch(error => console.log(error));

      	localStorage.setItem('first_config', true);
      } else {
      	this.getPlanning();
    	}


      this.setProjectSelect('project');

      this.setTalentsSelect('talents_id');
      this.triggerTalentsSelect2('talents_id');

      this.setTaskTypesSelect('task_types_id');
      this.triggerTaskTypesSelect2('task_types_id');

      this.$bus.$on('TASK_ADD_OR_UPDATE', () => {
      	this.getPlanning();
      });

      this.$bus.$on('APPOINTMENT_ADD_OR_UPDATE', () => {
      	this.getPlanning();
      });

      this.isMounted = true;

      $(document).on('click', (element) => {
        if (!element.target.matches('.is-highlighted *')) {
          $('.js-planning-table-row').removeClass('is-highlighted unhighlighted');
        }
      });
    },

    destroy(){
			$(document).off('click');
    },

    computed: {
    },

    methods: {
      getPlanning() {
        // Check if the current user id is the array
        var userId = this.user.id.toString();
        this.filters.talents_id = this.filters.talents_id.filter(item => ![this.user.id].includes(item));
        this.filters.talents_id = this.filters.talents_id.filter(item => ![userId].includes(item));
        this.filters.talents_id.unshift(this.user.id);

        this.selected_talents_id = this.filters.talents_id;
        this.selected_task_types_id = this.filters.task_types_id;
        this.tableRow();

        axios.get("/api/planning", {
          params: {
            filter_talents_id: this.filters.talents_id,
            filter_projects_id: this.filters.projects_id,
            filter_task_types_id: this.filters.task_types_id,
            date_month: this.date_month,
            date_year: this.date_year
          }
        }).then(res => {
          if (res.success === false) {
              // Todo : Error
          } else {
            let datas = res.data.datas;
           	this.planning = datas.planning; // Update project task list
            this.selected_talents_name = datas.talent_name; // Talent name list
            this.selected_task_types_name = datas.task_name; // Task name list

         		localStorage.setItem('date_month', this.date_month);
      			localStorage.setItem('date_year', this.date_year);

      			// Save talents filter for refresh
      			localStorage.setItem('filter_talents_id', JSON.stringify(this.filters.talents_id));
      			localStorage.setItem('selected_talents_id', JSON.stringify(this.selected_talents_id));
      			localStorage.setItem('selected_talents_name', JSON.stringify(this.selected_talents_name));

      			// Save task filter for refresh
      			localStorage.setItem('filter_task_types_id', JSON.stringify(this.filters.task_types_id));
      			localStorage.setItem('selected_task_types_id', JSON.stringify(this.selected_task_types_id));
      			localStorage.setItem('selected_task_types_name', JSON.stringify(this.selected_task_types_name));

            if(this.isMounted){
              this.tableRow(null);

              //-- Planning scroll position

              // Init scroll position to 0
              $('.js-planning-block').scrollTop(0);
              // $('.js-planning-block').css({'opacity': 0});
              setTimeout(() => {
                var startDay = startDay;

                // Scroll to Monday of current day's week
                if($('.js-planning-date.is-current').length > 0) {
                  var $currentDay = $('.js-planning-date.is-current');
                  var $currentDayRow = $currentDay.closest('.js-planning-table-row');
                  var $thisMonday = $currentDayRow.prevAll('.js-planning-table-row[data-day="'+ startDay +'"]:first');

                  if($currentDay.attr('data-day') == startDay) {
                    // console.log($currentDayRow.attr('data-day'));
                    $thisMonday = $currentDayRow;
                  }

                  $('.js-planning-block').animate({ scrollTop : $thisMonday.offset().top - $('.js-planning-block').offset().top }, 0);
                  $('.js-planning-block').css({'opacity': 1});
                }

                // Scroll to Monday of first task
                else if($('.task__wrapper').length > 0) {
                  console.log('ELSE');
                  // Scroll to first task
                  var $firstTask = null;

                  $('.js-planning-table-row').each(function(index, item){
                    if($(item).find('.task__wrapper').length > 0 && $firstTask == null) {
                      $firstTask = $(item);
                    }
                  });

                  // If the month doesn't begin with Monday
                  if($firstTask.prevAll('.js-planning-table-row[data-day="'+ startDay +'"]').length < 1) {
                    $('.js-planning-block').scrollTop(0);
                  } else {
                    var $monday = $firstTask.prevAll('.js-planning-table-row[data-day="'+ startDay +'"]:first');
                    if($firstTask.attr('data-day') == startDay) {
                      $monday = $firstTask;
                    }

                    $('.js-planning-block').animate({ scrollTop : $monday.offset().top - $('.js-planning-block').offset().top }, 0);
                  }
                }

              }, 100);
            }

            // Reset talents select2
            //this.setTalentsSelect('talents_id');
      			//this.triggerTalentsSelect2('talents_id');

      			// Reset task select2
      			//this.setTaskTypesSelect('task_types_id');
      			//this.triggerTaskTypesSelect2('task_types_id');
          }
        }).catch(error => console.log(error));

      },

      /**
       * Set Talent select2
       */
      setTalentsSelect(name) {
        $(() => {
          $('.form__task .js-'+name+'-data').select2({
            dropdownCssClass: 'multiple-choices filters-dropdown',
            placeholder: "Talent(s)",
            closeOnSelect: false,
            language: {
			        searching: function() {
		            return "Chargement...";
			        }
				    },
            ajax: {
              url: '/api/talent?exclude= ' + this.user.id,
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

					$('.form__task .js-'+name+'-data').on("select2:select", (e) => { this.getPlanning(); });
      	 	$('.form__task .js-'+name+'-data').on("select2:unselect", (e) => { this.getPlanning(); });
        });
      },

      /**
       * Fill the select2 with already selected Skills
       */
      triggerTalentsSelect2(name){
       		let talentsSelect2 = $('.form__task .js-'+name+'-data');
       		talentsSelect2.html('');

       		this.selected_talents_id.forEach(talentId => {
       			if(talentId == this.user.id){
							var name = this.user.firstname + ' ' + this.user.lastname;
       			} else {
       				var name = this.selected_talents_name[talentId][0].firstname + ' ' + this.selected_talents_name[talentId][0].lastname;
       			}

       			let option = new Option(name, talentId, true, true);
       			talentsSelect2.append(option).trigger('change');
       		});
      },

      /**
       * Set TaskType select2
       */
      setTaskTypesSelect(name) {
        $(() => {
          $('.form__task .js-'+name+'-data').select2({
            dropdownCssClass: 'multiple-choices filters-dropdown',
            placeholder: "Tâche(s)",
            closeOnSelect: false,
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

					$('.form__task .js-'+name+'-data').on("select2:select", (e) => { this.getPlanning(); });
      	 	$('.form__task .js-'+name+'-data').on("select2:unselect", (e) => { this.getPlanning(); });
        });
      },

      /**
       * Fill the select2 with already selected Skills
       */
      triggerTaskTypesSelect2(name){
       		let taskTypeSelect2 = $('.form__task .js-'+name+'-data');
       		taskTypeSelect2.html('');

       		this.selected_task_types_id.forEach(taskId => {
       			var name = this.selected_task_types_name[taskId][0].name;

       			let option = new Option(name, taskId, true, true);
       			taskTypeSelect2.append(option).trigger('change');
       		});
      },

      /**
       * Set Project select2
       */
      setProjectSelect(name) {
        $(() => {
          $('.form__task .js-'+name+'-data').select2({
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

					$('.form__task .js-'+name+'-data').on("select2:select", (e) => { this.getPlanning(); });
      	 	$('.form__task .js-'+name+'-data').on("select2:unselect", (e) => { this.getPlanning(); });
        });
      },

      /**
       * Handle date change.
       */
      handleDateChange(type) {
        switch (type) {
          case 'UP':
            this.date_month++;
            if (this.date_month > 12) {
              this.date_year++;
              this.date_month = 1;
            }
            break;
          case 'DOWN':
            this.date_month--;
            if (this.date_month < 1) {
              this.date_year--;
              this.date_month = 12;
            }
            break;
        }

        this.getPlanning();
      },

      handleSlide(type){
        var rightPos = $('.js-planning-header-wrapper').offset().left + $('.js-planning-header-wrapper').outerWidth();
        var rowRightPos = $('.js-planning-row-talents').offset().left + $('.js-planning-row-talents').outerWidth();

        switch(type){
          case 'PREV':
            if(this.counter_slide > 0){
              this.counter_slide--;
              this.translation = this.translation + this.cell_width;
            }
            break;
          case 'NEXT':
            if(rowRightPos > rightPos) {
              this.counter_slide++;
              this.translation = this.translation - this.cell_width;
            }
            break;
        }

        $('.js-planning-row').stop().css({"transform":"translate("+this.translation+"px, 0)"});
      },

      /**
       * Clear field
       */
      clear(type){
        if(typeof this.filters[type] == 'object'){
            this.filters[type] = [];
        } else {
            this.filters[type] = null;

            if(type == 'project_name'){
            	this.filters['projects_id'] = null;
            }
        }

        if($('.js-'+type+'-data')){
        	$('.js-'+type+'-data').val('').trigger('change');
      	}

        if(type == 'talents_id'){
          $('.js-planning-row').css({"transform":""});
        }

        this.getPlanning();
      },

      /**
       * Table row width
       */
      tableRow(mounted = true) {
        var $this = this;

        setTimeout(() => {
          $('.js-planning-row').each(function(){
            var rowWidth = $this.selected_talents_id.length * $this.cell_width;

            $(this).width(rowWidth);
            $this.isMounted = mounted;
          });
        }, 10);
      },

      /**
       * Highlight Table row on click
       */
      highlightedRow(e) {
      	var target = $(e.target).closest('.js-planning-table-row');
      	

      	if(!target.hasClass('is-highlighted')){
          $('.js-planning-table-row').removeClass('is-highlighted unhighlighted');
          $('.js-planning-table-row').addClass('unhighlighted');
      	  target.removeClass('unhighlighted').addClass('is-highlighted');
      	} else {
          $('.js-planning-table-row').removeClass('is-highlighted unhighlighted');
        }
      },

      goToProject(event, project_id){
    		if($(event.target).closest('.js-planning-tasks-wrapper').hasClass('is-active') || !$(event.target).closest('.js-planning-tasks-wrapper').hasClass('js-toggleclass-wrapper')){
    			Vue.prototype.Project._goTo(project_id);
    		}
    	}

    }
}
</script>
