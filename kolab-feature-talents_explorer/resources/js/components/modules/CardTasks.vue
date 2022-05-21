<template>
<!-- Card -->

<div class="card__task block dashboard__projects-block " v-bind:class="{ 'has-no-content': all_tasks.length == 0 }" style="background:#2B1C56">
  

  <!-- List, if project style="background: #2B1C56;"-->
  <div class="projects-table__content-wrapper" >
    <div style="margin-bottom:15px; display:flex;">
    <div style="    display: flex;
  background: #1E1438;
  width: 128px;
  align-items: center;
  border-radius: 10px;
  padding: 5px;
  justify-content: space-between;
  color: white;
  margin-top: 10px;padding-left: 24px;
  font-weight: bold;">Tâches
      <div class="tiny-button-section">
      <a href="javascript:;" class=" is-gradient" v-on:click="Global._setAction('SET_TASK', {project: project})">
        
      <span class="tiny-button icon-plus"> </span>
      </a>
    </div>
    </div>
    <div class="filter-select" style="display: flex;
  background: #1E1438;
  width: 81%;
  align-items: center;
  border-radius: 10px;
  padding: 5px;
  color: white;
  margin-left: 19px;
  margin-top: 10px;padding-left: 24px;
  font-weight: bold;"> 
  <button type="button" class="poopover-h c-1" v-on:click="test({status :'STATUS_PROGRESS'})">Aujourd'hui<div class="poopover" >{{ progress_tasks }}</div></button>
  <button type="button" class="c-1" v-on:click="test({status :'STATUS_WAITING'})">Demain<div class="poopover">{{ waiting_tasks}}</div> </button>
  <button class="c-1" v-on:click="test({status :'STATUS_CLOSED'})">Clôturé<div  class="poopover">{{ closed_tasks }}</div> </button>
  </div>
    </div>
    <div style="padding-top: 100px;
    margin: auto;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);" v-if="tasks.length == 0">
      
      <img src="images/nocontent.png" alt="" style="
    width: 63px;
    height: 47px;
    margin: auto;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    -webkit-transform: translate(-50%, -50%);
    transform: translate3d(-50%, -50%, 0);
    ">
    <div style="color: #7665A7;"> Aucune tâche pour aujourd'hui</div>
    </div>
    <div class="projects-table__content scroll-content" style="padding: 0px; padding-top: 7px;" v-if="tasks.length > 0" >
      <!-- Task -->
      <div class="project-card__col" v-for="task in tasks" v-on:contextmenu.prevent="contextMenu.show($event, 'TASK', {task: task, project: task.project}, contextMenuCallback, [true]);">
        <div class="project-card" v-bind:class="{ 'is-closed': task.status == 'CLOSED','is-waiting': !task.length > 0 && task.status !='CLOSED' }" ><!-- ,'is-waiting': task.accepted === null-->

          <!-- Category -->
          <div :class="'project__category is-' + task.project_category_color">{{ task.project_category }}</div>
          
           <span class="text-task main_new text-task-position" v-if="task.status !== 'CLOSED'">En cours</span>
          <div class="button project-detail__action-button-task js-toggle-button click" v-for="task in tasks" v-on:contextmenu.prevent="contextMenu.show($event, 'TASK', {task: task, project: task.project}, contextMenuCallback, [true]);" >
            <span class="dot-button-task" ></span>
            <span class="dot-button-task"></span>
            <span class="dot-button-task"></span>
          </div>
          <!-- Date 
          <p class="project-card__createdat" v-if="task.accepted == null">
            <template v-if="moment(task.created_at).format('YYYY-MM-DD') == moment().format('YYYY-MM-DD')">
              Aujourd'hui à {{ moment(task.created_at).format('HH:mm') }}
            </template>
            <template v-else>
              le {{ moment(task.created_at).format('DD/MM/YYYY') }} à {{ moment(task.created_at).format('HH:mm') }}
            </template>
          </p>-->

          <!-- Infos -->
          <div class="project-card__infos" >
            <p><strong>{{ task.task_type }}</strong></p>        
              <template v-if="task.status !== 'CLOSED'"><p v-on:click="goToProject($event, task.project_id)" >{{ task.project_name }}</p></template>
              <template v-else ><p>{{ task.project_name }}</p></template>
              <p v-if="task.status == 'CLOSED'">Clôturé le : {{ task.closed_at | dateFormat }}</p>
          </div>
          <div class="form-box new-box" >
            <label for="" class="form-label" style="color:#7665A7;">Tâches assignées par </label>
            <p style="background:#48367C; padding:10px; border-radius:5px;display:flex; align-items:center;"> 
			      <img :src="avatar" class="img-task-dash" alt="" width="25" height="25" >
		 {{task.created_by}}.<span> - Le {{ moment(task.created_at).format("D/MM/YY") }}</span></p>
          </div>
           <div class="form-box new-box-2 dash-car_padding " >
            <label for="" class="form-label" style="color:#7665A7;"> Date </label>
            <div class="date-picker" style="position">
            <span class="filter-picto picto-calendar"></span>
            <div class="date-picker_model" v-model="tasks.date" mode="range">Du {{ moment(task.start_date).format("D/MM/YY") }} au {{ moment(task.end_date).format("D/MM/YY") }}</div>
            </div>
          </div>


          <!-- Actions, Accept + Decline 
          <div v-if="task.accepted === null" class="text-center is-actions">
            <div class="project-card__actions-buttons js-actions-buttons">
              <button v-on:click="Task._setAccepted(task, true, contextMenuCallback, [true])" class="action-button js-action-button">
                Accepter
              </button>
              <button v-on:click="Task._setAccepted(task, false, contextMenuCallback, [true])" class="action-button js-action-button">
                Décliner
              </button>
            </div>
          </div>-->

          <!-- Actions + Note -->
          <div v-if="task.status !== 'CLOSED'" class="text-center js-position-wrapper is-actions" style="position: relative;bottom: 7px;">
            <button type="button" class="action-button js-position-button dash-note "  v-if="task.note" ><span class="icon icon-notes"></span> Voir note </button>
            <div class="notes js-position-content">
                <div class="notes__actions">
                    <button type="button" v-on:click.prevent="Task._edit(task, contextMenuCallback, [true])"><span class="icon icon-edit"></span></button>
                    <button type="button" class="js-close-note"><span class="icon icon-cross"></span></button>
                </div>
                <p class="word-wrap"><strong>Notes :</strong></p>
                <p class="text c-main-grey">{{ task.note }}</p>
            </div>

            <!-- Close action -->
            <!-- <button v-if="task.status !== 'CLOSED' && task.accepted !== null" v-on:click="Task._close(task, contextMenuCallback, [true])" class="action-button is-actions">Clôturer</button>-->

            <!-- Progress Bar -->
            <div class="project-card__progress" >
                <span class="project-card__progress-bar" :style="'width:' + getTaskProgressBar(task) + '%'"></span>
            </div>
          </div>
<div style="position: absolute;
  bottom: 0px;
  width: 100%;
  height: 100%;
  max-height: 40px;
  background: rgb(30, 20, 56);
  border-radius: 0px 0px 8px;display: inline-grid;
  color: white;text-align:center;align-items:center;text-decoration: none;
    outline: none;
    border: none;
    font-family: 'Proxima Nova';
    font-weight: bold;" id="countdown" class="count"  v-if="task.status !== 'CLOSED'" v-on:click="countdown()"> Clôturer</div>
      </div>
        </div>

      <!-- ./Task -->

    </div>

    <!-- Gradient hiding more tasks 
    <div class="projects-table__content-b-gradient" v-if="all_tasks.length > 6" v-bind:class="{ 'is-hidden' : all_tasks.length > 6 && all_tasks.length <= 8 }"></div>
  </div>-->
  <!-- ./List -->

  <!-- Fake, if no project
  <FakeTasks v-if="all_tasks.length == 0"></FakeTasks>-->
    <!--./Fake Summary 
  <div class="dashboard__projects-resume">
    <div class="projects-resume__title-box">
      <h2 class="main-title">{{ progress_tasks }}
        <template v-if="progress_tasks > 1">tâches</template>
        <template v-else>tâche</template>
      </h2>
      <p class="text c-main-grey">en cours</p>
    </div>-->

   <!-- <div class="filters-col">
      <p class="filters-text"><span class="filters-main-icon icon icon-filters"></span> Filtrer par :</p>
      <div class="filter-col">
        <div class="filter-item filter-select has-picto">
          <span class="filter-picto picto-status"></span>
          <select class="filter-field js-status-data" name="status" v-select2 v-model="filters.status">
            <option value="STATUS_ALL">Toutes les tâches</option>
            <option value="STATUS_WAITING">Tâches en attente</option>
            <option value="STATUS_PROGRESS">Tâches en cours</option>
            <option value="STATUS_CLOSED">Tâches clôturées</option>
          </select>
        </div>
      </div>
    </div>   -->

    <!--<div class="scroll-arrows" v-if="all_tasks.length > 6" v-bind:class="{ 'is-hidden' : all_tasks.length > 6 && all_tasks.length <= 8 }"></div>-->
  </div>
    <!--/Summary -
    <div class="tiny-button-section">
      <a href="javascript:;" class=" is-gradient" v-on:click="Global._setAction('SET_TASK', {project: project})">
        
      <span class="tiny-button icon-plus"> </span>
      </a>
    </div>-->

</div>



<!-- ./Card -->
</template>

<script>
export default {
    props: ['all_tasks', 'tasks','task', 'progress_tasks','closed_tasks','waiting_tasks','contextMenu', 'contextMenuCallback', 'project','_task','_project','task_talent','user'],
  
    data() {
      let date = new Date();
      let timeleft = 5 ;
      var intervalId = null;
      return {

        timeleft: 5,
        avatar: '',

        date: {
            start: new Date(),
            end: new Date()
        },
        
      	filters: {
          status: null
        },
        state: null,
        today: moment().format('YYYY-MM-DD'),
        callback: null,
        
    }
    },

    beforeMount() {
    },

    mounted() {
      this.setStatusSelect();
      
      
    },


    methods: {

      // Countdown before closing a task

      countdown(){
        
        // let timeleft = 10;
        let t = setInterval(() => {
          //  if(document.getElementsByClassName('count').onclick == true){
          //    document.getElementById('countdown').onclick = function(){
          //      clearInterval(t);
          //      clearTimeout(t);
          //      document.getElementById('countdown').innerHTML = "Clôturé";
          //     this.timeleft = 5;
          //    }
               
          //  }
          this.timeleft--;
          document.getElementById('countdown').innerHTML = 'Tâche clôturé dans ' + this.timeleft + "\'";
        if(this.timeleft == 0){
          clearInterval(t);
          this.timeleft = 5;
          Vue.prototype.Task._confirmeClose(this.tasks,this.contextMenuCallback,[true]);
          document.getElementById('countdown').innerHTML = "Clôturé";
          this.setState(this.value);

        }
        }, 1000);
      
      },

      setBodyClass(){
    		if(this.action == 'set_closed'){
    			$('body').remove(this.tasks);
    	    } else {
    	      	$('body').add(this.tasks);
    	    }
      },
      
      // function display filter
      test(filters){
        // const y = (test) =>{
          this.contextMenuCallback(false, filters); 
        // }
      },


      close(event){

    		if(!event.target.matches('.is-actions, .is-actions *')){
    			Vue.prototype.Task._close(task);
    		}else{
          clearInterval(countdown())
        }
      },
  

      hasTalentsWithLetter(daystatus){
    		const startsWith = this.all_tasks.filter((tasks) => {
    			return this.tasks.status.startsWith(daystatus);
    		});

    		return startsWith.length > 0;
    	},


    	setBodyClass(){
    		if(this.action == 'CLOSE'){
    			$('body').removeClass('alert-is-open');
    	    } else {
    	      	$('body').addClass('alert-is-open');
    	    }
      },

          startFocusOut(){
        		$(document).on('click', () => {
        			if(this.menuType !== null)
        				this.hideContextMenu();
        		});
        		// $(document).on('scroll', () => { this.hideContextMenu(); });
        		// $('.planning__block').on('scroll', () => { this.hideContextMenu(); });
        	},

        	hideContextMenu(){
        		this.menuType = null;

        		// Remove prevent scroll
        		$('body').removeClass('overflow-hidden');
				  	if($('.scroll-content').length > 0) {
				  		$('.scroll-content').removeClass('overflow-hidden');
				  	}
        	},

        	resetData(){
        		this.menuType = null;
            this.task = null;
            this.appointment = null;
            this.talent = null;
            this.callback = null;
        	},
   

      /**
    	 * Set status select2
    	 */
    	setStatusSelect(){
    	 	$(() => {
    	 		$('.card__task .js-status-data').select2({
             
    	 			 minimumResultsForSearch: Infinity,
             dropdownCssClass: "dashboard-filters",
    	 			 placeholder: "Toutes les tâches",
    	 		});

    	 		$('.card__task .js-status-data').on("select2:select", () => { this.contextMenuCallback(false, this.filters); });
    	 		$('.card__task .js-status-data').on("select2:unselect", () => { this.contextMenuCallback(false, this.filters); });
    	 	});
      },
      
      getTasks(){
        console.log(filter)
      },

      getDashboard(all, filters = {}) {
        	var params = {};

      		if(filters.status) params.filter_status = filters.status;

          axios.post('/api/dashboard/', {
          	params: params
          })
          .then(res => {
              this.tasks = res.data.datas.tasks.length ? res.data.datas.tasks : [];
              this.appointments = res.data.datas.appointments.length ? res.data.datas.appointments : [];
              this.progress_tasks = res.data.datas.progress_tasks;
              this.closed_tasks = res.data.datas.closed_tasks;
              this.waiting_tasks = res.data.datas.waiting_tasks;

              if(all){
              	this.all_tasks = res.data.datas.tasks.length ? res.data.datas.tasks : [];
              }
          })
          .catch(error => console.log(error));
        },

      setState(value){
    		this.state = value;
    		$('.js-toggle-content').fadeOut();
    	},

    	goToProject(event, project_id){
    		if(!event.target.matches('.is-actions, .is-actions *')){
    			Vue.prototype.Project._goTo(project_id);
    		}
    	}
    }
}
</script>

