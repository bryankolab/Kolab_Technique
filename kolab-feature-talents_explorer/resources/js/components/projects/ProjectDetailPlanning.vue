<template>
  <div class="project-detail__planning block">

		<!-- Planning header -->
    <div class="row">
      <div class="col-md-7">
        <div class="mb-30">
          <h2 class="main-title mb-05"><span class="icon icon-calendar"></span> Planning</h2>
          <p class="c-main-grey title-indent" v-if="nb_days > 0">{{ nb_days }} jours planifiés</p>
          <p class="c-main-grey title-indent" v-else>{{ nb_days }} jour planifié</p>
        </div>
      </div>
      <div class="col-md-5 text-right">
        <a href="javascript:;" class="small-button is-gradient" v-on:click="Global._setAction('SET_TASK', {project: project})"><span class="icon icon-plus"></span> Ajouter une tâche</a>
      </div>
    </div>
    <!-- ./Planning header -->

    <!-- Planning calendar -->
    <div class="row">
      <div class="col-md-8">
        <div class="simple-table">
          <div class="simple-table__header">
            <div class="simple-table__row">
              <div class="simple-table__td"><p>Date</p></div>
              <div class="simple-table__td"><p>Tâches</p></div>
            </div>
          </div>
          <div class="simple-table__body">
            <div v-for="(tasksByDate, date) in planning.project_tasks" class="simple-table__row">
              <div class="simple-table__td is-date" v-bind:class="{ 'is-active' : date == today }">
              	<p>{{ moment(date).format('dddd Do MMM YYYY') }}</p>
              </div>
              <div class="simple-table__td no-padding" v-bind:class="{ 'has-tasks' : tasksByDate.length > 0 }">
                <div v-for="task in tasksByDate" :class="'project-task js-col-actions task-' + Global._slugify(task.taskTypes[0].name)">
                  <p>{{ task.taskTypes[0].name }}</p>
                  <button type="button" class="col-actions__button is-small is-white js-popup-position-button" data-popup="js-popup" v-on:click="Global._setAction('SET_TASK_SINGLE', {task: task, project: project, class: 'task-popin'});">
                    <span class="col-actions__button-dot"></span>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Planning legend -->
      <div class="col-md-4">
        <div class="simple-table">
          <div class="simple-table__header">
            <div class="simple-table__row">
              <div class="simple-table__td"><p>Jours planifiés</p></div>
            </div>
          </div>
          <div class="simple-table__body">
            <div v-for="task in planning.project_tasks_summary" class="simple-table__row">
              <div class="simple-table__td">
                <div class="project-detail__summary-item text-right">
                  <span :class="'project-detail__summary-legend task-' + Global._slugify(task.task_type)"></span>
                  <p class="c-main-grey">{{ task.task_type }}</p>
                  <p class="project-detail__summary-days"><strong>{{ task.nb_days }} jours</strong></p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- ./Planning calendar -->

  </div>
</template>

<script>
export default {
    props: ['_project'],

    data() {
      return {
        project: this._project,
        planning: {},
        nb_days: 0,
        today: moment().format('YYYY-MM-DD')
      }
    },

    created() {
    },

    mounted() {
    	this.getPlanning();

      this.$bus.$on('TASK_ADD_OR_UPDATE', () => {
      	this.getPlanning();
      });
    },

    methods: {
      getPlanning(){
        axios.get('/api/project/' + this.project.id + '/planning').then(res => {
          if (res.success === false) {
              // Todo : Error
          } else {
              this.planning = res.data.datas; // Update project task list
              this.getNbDays();
          }
        }).catch(error => console.log(error));
      },

      getNbDays(){
      	if(this.planning.project_tasks){
	      	setTimeout(() => {
	      		this.nb_days = $('.has-tasks').length; // Only days with task(s)
	      	}, 10);
	      }
      }
    }
}
</script>