<template>
	<div class="congrats">
		<!-- <span class="congrats__overlay" v-on:click="close()"></span> -->
		<div class="popup-intro text-center mb-30" v-if="_element">
      <h2 class="popup-maintitle">{{ _element.name }}</h2>
    </div>

		<div class="block has-bg">
			<!-- <div class="block__bck-image">
	        <img src="/images/project-created.jpg" class="img-fluid block__image" alt="" width="" height="">
	    </div> -->
	    <div class="congrats__content js-congrats-content text-center">
	    	<h3 class="medium-title mb-30">
					<template v-if="_type == 'PROJECT'">
						<span v-if="_element && _element.id">Le projet a été modifié avec succès !</span>
						<span v-else>Le projet a été créé avec succès !</span>
					</template>
					<template v-if="_type == 'TASK'">
						Vos tâches ont été créées avec succès !
					</template>
					<template v-if="_type == 'APPOINTMENT'">
						<span v-if="_element && _element.id">Votre rendez-vous a été modifié avec succès !</span>
						<span v-else>Votre rendez-vous a été créé avec succès !</span>
					</template>
					<template v-if="_type == 'TALENT'">
						<span v-if="_element && _element.id">Ce talent a été modifié avec succès !</span>
						<span v-else>Ce talent a été créé avec succès !</span>
					</template>
					<template v-if="_type == 'EXPORT'">
						Vos exports ont été créés avec succès !
					</template>
				</h3>

				<p class="congrats__text text c-main-grey">
					<template v-if="_type == 'PROJECT'">Vous trouverez le projet dans la page <strong>Projet</strong>.</template>
					<template v-if="_type == 'TASK'">Ces tâches ont été créées dans la page <strong>Dashboard</strong> et dans la page <strong>Planning</strong>.</template>
					<template v-if="_type == 'APPOINTMENT'">
						<span v-if="_element && _element.id">Le rendez-vous a été modifié dans la page <strong>Dashboard</strong>.</span>
						<span v-else>Le rendez-vous a été créé dans la page <strong>Dashboard</strong>.</span>
					</template>
					<template v-if="_type == 'TALENT'">
						<span v-if="_element && _element.id">Ce talent a bien été modifié dans la page <strong>Talents</strong>.</span>
						<span v-else>Ce talent a bien été créé dans la page <strong>Talents</strong>.</span>
					</template>
					<template v-if="_type == 'EXPORT'">Vous trouverez vos exports dans le <strong>détail de votre projet</strong>.</template>
				</p>

				<span class="picto picto-big-check"></span>
			</div>

			<div v-if="_more" class="create-more text-center is-hidden">
				<p class="text c-main-grey mb-30">Vous pouvez aussi créer <strong>un rendez-vous, une ou plusieurs tâche(s)</strong><br> et <strong>ajouter un ou plusieurs export(s)</strong> pour ce projet.</p>
				<div class="row">
					<div class="col-md-6 mb-15">
						<div v-on:click="addAppointment()" class="box-button"><span class="picto picto-calendar"></span>Créer<br> <strong>un rendez-vous</strong></div>
					</div>
					<div class="col-md-6 mb-15">
						<div v-on:click="addTask()" class="box-button"><span class="picto picto-task"></span>Créer<br> <strong>une ou plusieurs tâche(s)</strong></div>
					</div>
					<div class="col-md-6 mb-15">
						<div v-on:click="addExport()" class="box-button"><span class="picto picto-play"></span>Ajouter<br> <strong>un ou plusieurs livrable(s)</strong></div>
					</div>
					<div class="col-md-6 mb-15">
						<div v-on:click="addProject()" class="box-button"><span class="picto picto-projects"></span>Créer<br> <strong>un nouveau projet</strong></div>
					</div>
				</div>

				<button v-on:click="close()" class="action-link is-white popup-button is-next">Passer et retourner sur la page <span class="icon icon-long-arrow"></span></button>
			</div>
		</div>

		<!-- <button v-on:click="close()">Fermer</button> -->
	</div>
</template>

<script>
	export default {
		props: ['_type', '_element', '_more'],

		data() {
			return {
			}
		},

		created() {
		},

		mounted() {
			if(this._type == 'PROJECT') {
				setTimeout(() => {
					if(this._element.id){
						this.$bus.$emit('ACTION_CHANGED', {action: null});
					} else {
						$('.js-congrats-content').hide();
						$('.create-more').fadeIn('300');
					}
				}, 2000);
			} else {
				setTimeout(() => {
					this.$bus.$emit('ACTION_CHANGED', {action: null});
				}, 2000);
			}
		},

		methods: {
			addAppointment(){
				this.$bus.$emit('ACTION_CHANGED', {action: 'SET_APPOINTMENT'});
			},

			addTask(){
				this.$bus.$emit('ACTION_CHANGED', {action: 'SET_TASK', project: this._element, goback: false});
			},

			addExport(){
				this.$bus.$emit('ACTION_CHANGED', {action: 'SET_EXPORT', project: this._element});
			},

			addProject(){
				this.$bus.$emit('ACTION_CHANGED', {action: 'SET_PROJECT'});
			},

			close(){
				this.$bus.$emit('ACTION_CHANGED', {action: null}); // Close modal
			}
    }
  }
 </script>
