// Task Helper

const { before } = require("lodash");

const Task = {
  install(Vue, options) {
  	Vue.prototype.Task = {};

    Vue.prototype.Task._edit = (task) => {
      Vue.prototype.$bus.$emit('ACTION_CHANGED', {
  	 		action: 'SET_TASK_SINGLE',
  	 		task: task
  	 	});
    }

    Vue.prototype.Task._delete = (task, callback = null, args = []) => {
    	Vue.prototype.$bus.$emit('CONFIRM', {
  			title: 'Attention',
  			text: 'Voulez-vous supprimer cette tâche ?',
  			confirmText: 'Alors ?',
  			button_1: {
  				title: 'Supprimer',
  				class: '',
  				method: Vue.prototype.Task._deleteConfirm,
  				args: { task, callback, args }
  			},
  			button_2: {
  				title: 'Annuler',
  			}
  		});
    }

    Vue.prototype.Task._deleteConfirm = (params = {}) => {
    	axios.delete("/api/task/" + params.task.id)
			.then(res => {
  			if(res.success === false){
      		// Todo : Error
      	} else {
      		if(params.callback){
      			params.callback(params.args.join(','));
      		}
      		Vue.prototype.$bus.$emit('TASK_ADD_OR_UPDATE', res.data); // Emit add or update talent event
      	}
      }).catch(error => console.log(error));
		}

    Vue.prototype.Task._close = (task, callback = null, args = [] ) => {
      Vue.prototype.$bus.$emit('CONFIRM', {
  			title: 'Attention',
  			text: 'Voulez-vous clôturer cette tâche ?',
  			confirmText: 'Alors ?',
  			button_1: {
  				title: 'Clôturer',
  				class: '',
  				method: Vue.prototype.Task._confirmeClose,
  				args: { task:task, callback, args}
  			},
  			button_2: {
  				title: 'Annuler',
  			}
  		});
    }

    Vue.prototype.Task._confirmeClose = (params = {}) => {
		console.log(params);
		console.log(params["0"]);
    	axios.patch('/api/task/' + params["0"].id,
      {
		  action: 'set_closed',
      })
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

   Vue.prototype.Task._setAccepted = (task, value, callback = null, args = []) => {
	    axios.patch('/api/task/' + task.id,
      {
          action: 'set_acceptation',
          value: value
      })
      .then((res) => {
      	if(res.success === false){
      		// Todo : Error
      	} else {
      		if(callback){
      			callback(args.join(','));
      		}
      	}
      })
      .catch(error => console.log(error));
	  };

  },
}

Vue.use(Task);

