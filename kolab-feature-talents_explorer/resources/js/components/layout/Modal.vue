<template>
	<div class="popup js-popup" v-show="action" :class="pClass">
		<div class="popup-overlay" v-on:click="action == 'CONGRATS' ? close() : Alert._defaultClose(close)"></div>
		<div class="popup-content">

			<!-- <button type="button" class="popup__close small-icon-button js-close-popup" v-on:click="action = null">
				<IconCross></IconCross> Close
			</button> -->

			<ProjectForm v-if="action == 'SET_PROJECT'" :_project="project"></ProjectForm>
			<TaskForm v-if="action == 'SET_TASK'" :_project="project" :_goback="goback"></TaskForm>
			<TaskSingleForm v-if="action == 'SET_TASK_SINGLE'" :_task="task" :_project="project"></TaskSingleForm>
			<ExportForm v-if="action == 'SET_EXPORT'" :_project="project"></ExportForm>
			<AppointmentForm v-if="action == 'SET_APPOINTMENTFORM'" :_appointment="appointment"></AppointmentForm>
			<Appointment v-if="action == 'SET_APPOINTMENT'" :_appointment="appointment"></Appointment>
			<TalentForm v-if="action == 'SET_TALENT'" :_talent="talent"></TalentForm>
			<Congrats v-if="action == 'CONGRATS'" :_type="type" :_element="element" :_more="more"></Congrats>
      		<AccountManagement v-if="action == 'ACCOUNT'" :_user="user"></AccountManagement>

		</div>
	</div>
</template>

<script>
    export default {
    		props: [],

    		data() {
          return {
          	action: null,
          	type: null,
          	more: null,
          	element: null,
          	pClass: null,

          	talent: null,
          	project: null,
          	task: null,
          	appointment: null,
            user: null,
            goback: null
          }
        },

        mounted() {
        	this.$bus.$on('ACTION_CHANGED', (datas) => {
            setTimeout(() => {
              this.action = datas.action;
              this.type = datas.type ? datas.type : false;
              this.more = datas.more ? datas.more : false;
              this.pClass = datas.class ? datas.class : false;
            }, 10);

			      // Reset datas
			      this.talent = null;
			      this.project = null;
			      this.appointment = null;
			      this.task = null;
            this.user = null;
            this.element = null;
            this.goback = datas.goback;

		        if(datas.talent) { this.talent = datas.talent; this.element = datas.talent; }
		        if(datas.project) { this.project = datas.project; this.element = datas.project; }
		        if(datas.appointment) { this.appointment = datas.appointment; this.element = datas.appointment; }
		        if(datas.task) { this.task = datas.task; this.element = datas.task; }
            if(datas.user) { this.user = datas.user; this.element = datas.user; }

			      // Add body class
			      this.setBodyClass();
			    });
        },

        beforeDestroy(){
        	 this.$bus.$off('ACTION_CHANGED');
        },

        methods: {
        	close(){
        		this.action = null;

        		// Remove body class
        		this.setBodyClass();

        		// Reset watcher for changes on popup
        		window.changeWhenPopupIsOpen = false;
        	},

        	setBodyClass(){
            setTimeout(() => {
          		if(this.action == null || this.pClass == 'task-popin'){
          			$('body').removeClass('popup-is-open');
  			      } else {
  			      	$('body').addClass('popup-is-open');
  			      }
            },10);
        	}
      	}
    }
</script>
