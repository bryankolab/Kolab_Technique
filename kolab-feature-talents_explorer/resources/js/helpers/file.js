// File Helper

const File = {
  install(Vue, options) {
  	Vue.prototype.File = {};

    Vue.prototype.File._delete = (files, callback = null, args = []) => {
      Vue.prototype.$bus.$emit('CONFIRM', {
  			title: 'Attention',
  			text: 'Voulez-vous supprimer ce(s) fichier(s) ?',
  			confirmText: 'Alors ?',
  			button_1: {
  				title: 'Supprimer',
  				class: '',
  				method: Vue.prototype.File._deleteConfirm,
  				args: { files, callback, args }
  			},
  			button_2: {
  				title: 'Annuler',
  			}
  		});
    }

    Vue.prototype.File._deleteConfirm = (params = {}) => {
      axios.delete("/api/file/", {
      	data: {
      		files: params.files
      	}
      })
			.then(res => {
  			if(res.success === false){
      		// Todo : Error
      	} else {
      		if(params.callback){
      			params.callback(params.args.join(','));
      		}

      		Vue.prototype.$bus.$emit('FILE_ADD_OR_UPDATE');
      	}
      }).catch(error => console.log(error));
    }
  },
}

Vue.use(File);