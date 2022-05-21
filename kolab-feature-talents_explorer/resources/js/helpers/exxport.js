// Export Helper

const Exxport = {
  install(Vue, options) {
  	Vue.prototype.Exxport = {};

    Vue.prototype.Exxport._delete = (exxports, callback = null, args = []) => {
      Vue.prototype.$bus.$emit('CONFIRM', {
  			title: 'Attention',
  			text: 'Voulez-vous supprimer ce(s) livrable(s) ?',
  			confirmText: 'Alors ?',
  			button_1: {
  				title: 'Supprimer',
  				class: '',
  				method: Vue.prototype.Exxport._deleteConfirm,
  				args: { exxports, callback, args }
  			},
  			button_2: {
  				title: 'Annuler',
  			}
  		});
    }

    /**
     * [description]
     * @param  {[type]}   params.talent   [description]
     * @param  {Function} params.callback [description]
     * @param  {Array}    params.args   [description]
     * @return {[type]}            [description]
     */
    Vue.prototype.Exxport._deleteConfirm = (params = {}) => {
      axios.delete("/api/export/", {
      	data: {
      		exports: params.exxports
      	}
      })
			.then(res => {
  			if(res.success === false){
      		// Todo : Error
      	} else {
      		if(params.callback){
      			params.callback(params.args.join(','));
      		}

      		Vue.prototype.$bus.$emit('EXPORT_ADD_OR_UPDATE');
      	}
      }).catch(error => console.log(error));
    }
  },
}

Vue.use(Exxport);