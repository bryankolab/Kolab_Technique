// Appointment Helper
const { before } = require("lodash");

const Appointment = {
  install(Vue, options) {
  	Vue.prototype.Appointment = {};

    Vue.prototype.Appointment._edit = (appointment) => {
      Vue.prototype.$bus.$emit('ACTION_CHANGED', {
  	 		action: 'SET_APPOINTMENT',
  	 		appointment: appointment
  	 	});
    }

    Vue.prototype.Appointment._delete = (appointment, callback = null, args = []) => {
      Vue.prototype.$bus.$emit('CONFIRM', {
  			title: 'Attention',
  			text: 'Voulez-vous supprimer ce rendez-vous ?',
  			confirmText: 'Alors ?',
  			button_1: {
  				title: 'Supprimer',
  				class: '',
  				method: Vue.prototype.Appointment._deleteConfirm,
  				args: { appointment, callback, args }
  			},
  			button_2: {
  				title: 'Annuler',
  			}
  		});
    }

    Vue.prototype.Appointment._deleteConfirm = (params = {}) => {
    	console.log(params);
    	axios.delete("/api/appointment/" + params.appointment.id)
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

Vue.use(Appointment);