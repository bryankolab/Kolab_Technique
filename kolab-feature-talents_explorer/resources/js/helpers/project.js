// Project Helper

const Project = {
  install(Vue, options) {
  	Vue.prototype.Project = {};

  	Vue.prototype.Project._goTo = (project_id) => {
      window.location.href = '/projects/' + project_id;
    }

    Vue.prototype.Project._edit = (project) => {
      Vue.prototype.$bus.$emit('ACTION_CHANGED', {
  	 		action: 'SET_PROJECT',
  	 		project: project
  	 	});
    }

    Vue.prototype.Project._delete = (project, callback = null, args = []) => {
      Vue.prototype.$bus.$emit('CONFIRM', {
  			title: "Attention",
  			text: "Voulez-vous supprimer ce projet ?",
  			confirmText: 'Alors ?',
  			button_1: {
  				title: "Supprimer",
  				class: '',
  				method: Vue.prototype.Project._deleteConfirm,
  				args: { project, callback, args }
  			},
  			button_2: {
  				title: "Annuler",
  			}
  		});
    },

    Vue.prototype.Project._deleteConfirm = (params = {}) => {
    	axios.delete('/api/project/' + params.project.id)
			.then(res => {
  			if(res.success === false){
      		// Todo : Error
      	} else {
      		if(params.callback){
      			params.callback(params.args.join(','));
      		}
      	}
      }).catch(error => console.log(error));
    }
  },
}

Vue.use(Project);