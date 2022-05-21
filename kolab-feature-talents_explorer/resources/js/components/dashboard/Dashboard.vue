<template>
  <div class="dashboard" id="vue__dashboard">
    <div class="js-dashboard-inner">
    	<ContextMenu ref="ContextMenu"></ContextMenu>

      <div class="main-container" style="background: none;">
          <!--<AddButtons></AddButtons>-->

         <!-- <button type="button" class="notifications__btn js-open-notif">10</button>-->

          <div class="dashboard__container">
              <div class="row" style="background: #2B1C56;
    margin-bottom: 60px;
    /* padding-left: 30px; */
    /* padding-right: 30px; */
    min-height: 618px;
    max-width: 100%;
    border-radius: 20px;
    flex: 0 0 100%;">
                 <!-- <div class="col-w30 col-custom block__col">
                      <card-welcome
                      	:user="user"
                      ></card-welcome>
                  </div> -->

                  <div class="col-w70 col-custom block__col" style="display: flex;
    width: 100%;flex: 0 0 100%;
    max-width: 100%;padding:0; margin:0;">
                  	<!-- layout/CardTasks -->
                    <div class="col-w30 col-custom " style="flex: 0 0 26%;
    max-width: 26%;margin:0; padding:0;">
                      <card-welcome
                      	:user="user"
                      ></card-welcome>
                  </div>
                   
                  	<card-tasks
                    	:all_tasks="all_tasks"
                    	:tasks="tasks"
                    	:progress_tasks="progress_tasks"
                      :closed_tasks="closed_tasks"
                      :waiting_tasks="waiting_tasks"
                    	:contextMenu="$refs.ContextMenu"
                    	:contextMenuCallback="getDashboard"
                    ></card-tasks>
                  </div>
              </div>

              <div class="row">
                  <div class="col-md-6 col-custom block__col">
                      <talk></talk>
                      
                  </div>

                  <div class="col-md-6 col-custom block__col">
                  		<!-- layout/CardRdv -->
                      <card-rdv
                      	:appointments="appointments"
                      	:contextMenu="$refs.ContextMenu"
                      	:contextMenuCallback="getDashboard"
                        :user="user"
                      ></card-rdv>
                  </div>
              </div>
          </div>
      </div>

      <!-- layout/NotificationsPanel -->
     <!-- <NotificationsPanel></NotificationsPanel>-->
    </div>

    <WelcomeScreen :user="user"></WelcomeScreen>
  </div>

</template>

<script>
export default {
    props: ['user'],

    data() {
        return {
        	all_tasks: [],
          tasks: [],
          appointments: [],
          progress_tasks: 0,
          closed_tasks: 0,
          waiting_tasks: 0,
          
        }
    },

    mounted() {
        this.getDashboard(true);

        // Catch event when element is added or updated
    	this.$bus.$on('APPOINTMENT_ADD_OR_UPDATE', () => {
	      this.getDashboard(); // Call talents with filters
	    });

	    this.$bus.$on('TASK_ADD_OR_UPDATE', () => {
	      this.getDashboard(true); // Call talents with filters
	    });
    },

    methods: {
        getDashboard(all, filters = {}) {
        	var params = {};

      		if(filters.status) params.filter_status = filters.status;

          axios.post('/api/dashboard/' + this.user.id, {
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
        }
    }
}
</script>
