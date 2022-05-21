<template>
	<div class="context-menu" v-show="menuType">

		<template v-if="menuType == 'CMENU_PROJECT'">
		  <ul class="context-menu--items">
		    <li class="context-menu--items-element" v-on:click.prevent="Project._edit(project, callback, params)">
		    	<span class="icon icon-edit"></span> Modifier le projet
		    </li>
		    <hr>
		    <li class="context-menu--items-element" v-on:click.prevent="Project._delete(project, callback, params)">
		    	<span class="icon icon-delete"></span> Supprimer le projet
		    </li>
		  </ul>
		</template>

		<template v-if="menuType == 'CMENU_TASK'">
			<template v-if="task.accepted == null && task.talents[0].id == Global.user_id">
			  <ul class="context-menu--items">
					<li class="context-menu--items-element" v-on:click="Task._setAccepted(task, true, callback, params)">
						<span class="icon icon-check"></span> Accepter la tâche
					</li>
			    <li class="context-menu--items-element" v-on:click="Task._setAccepted(task, false, callback, params)">
			    	<span class="icon icon-cross"></span> Décliner la tâche
			    </li>
			  </ul>
			</template>
			<template v-else>
			  <ul class="context-menu--items">
			  	<template v-if="task.status != 'CLOSED'">
				    <li class="context-menu--items-element" v-on:click="Task._edit(task, callback, params)">
				    	<span class="icon icon-edit"></span> Modifier la tâche
				    </li>
				    <li class="context-menu--items-element" v-on:click="Task._close(task, callback, params)">
				    	<span class="icon icon-cross"></span> Clôturer la tâche
				    </li>
				  </template>
			    <li class="context-menu--items-element" v-on:click="Project._goTo(project)">
			    	<span class="icon icon-see"></span> Voir le projet
			    </li>
			    <hr>
			    <li class="context-menu--items-element" v-on:click="Task._delete(task, callback, params)">
			    	<span class="icon icon-delete"></span> Supprimer la tâche
			    </li>
			  </ul>
			</template>
		</template>

		<template v-if="menuType == 'CMENU_APPOINTMENT'">
		  <ul class="context-menu--items">
		    <li class="context-menu--items-element" v-on:click.prevent="Appointment._edit(appointment, callback, params)">
		    	<span class="icon icon-edit"></span> Modifier le RDV
		    </li>
		    <li class="context-menu--items-element" v-on:click.prevent="Appointment._delete(appointment, callback, params)">
		    	<span class="icon icon-delete"></span> Supprimer le RDV
		    </li>
		  </ul>
		</template>

		<template v-if="menuType == 'CMENU_TALENT'">
		  <ul class="context-menu--items">
		    <li class="context-menu--items-element" v-on:click.prevent="Talent._edit(talent, callback, params)">
		    	<span class="icon icon-edit"></span> Modifier le talent
		    </li>
		    <li class="context-menu--items-element" v-on:click.prevent="Talent._delete(talent, callback, params)">
		    	<span class="icon icon-delete"></span> Supprimer le talent
		    </li>
		  </ul>
		</template>

	</div>
</template>

<script>
    export default {
    		props: [],

    		data() {
          return {
          	menuType: null,

          	project: null,
          	task: null,
          	appointment: null,
          	talent: null,

          	callback: null,
          	params: []
          }
        },

        mounted() {
        	this.startFocusOut();
        },

        methods: {
        	show(e, type, datas, callback, params){
        		this.resetData();

        		$('.context-menu').css('left', e.clientX);
					  $('.context-menu').css('top', e.clientY);

				  	if(type == 'PROJECT'){
				  		this.menuType = 'CMENU_PROJECT';
				  		this.project = datas.project;
				  	} else if(type == 'TASK'){
				  		this.menuType = 'CMENU_TASK';
				  		this.project = datas.task.project_id;
				  		this.task = datas.task;
				  	} else if(type == 'APPOINTMENT'){
				  		this.menuType = 'CMENU_APPOINTMENT';
				  		this.appointment = datas.appointment;
				  	}  else if(type == 'TALENT'){
				  		this.menuType = 'CMENU_TALENT';
				  		this.talent = datas.talent;
				  	}

				  	if(callback){
				  		this.callback = callback;
				  	}

				  	if(params){
				  		this.params = params;
				  	}

				  	// Prevent scroll
				  	$('body').addClass('overflow-hidden');
				  	if($('.scroll-content').length > 0) {
				  		$('.scroll-content').addClass('overflow-hidden');
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
        		this.project = null;
				this.task = null;
				this.appointment = null;
				this.talent = null;
				this.callback = null;
        	},

      	}
    }
</script>