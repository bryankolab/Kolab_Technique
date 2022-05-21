// Global Helper

const Global = {
  install(Vue, options) {
  	Vue.prototype.Global = {};

  	Vue.prototype.moment = moment;

    Vue.prototype.Global._setAction = (action, datas) => {
    	//Vue.prototype.$bus.$emit('ACTION_CHANGED', {action: null });
      Vue.prototype.$bus.$emit('ACTION_CHANGED', {action: action, ...datas });
    };

    Vue.prototype.Global._slugify = (str) => {
		  str = str.replace(/^\s+|\s+$/g, ''); // trim
		  str = str.toLowerCase();

		  // remove accents, swap ñ for n, etc
		  var from = "ãàáäâẽèéëêìíïîõòóöôùúüûñç·/_,:;";
		  var to   = "aaaaaeeeeeiiiiooooouuuunc------";
		  for (var i = 0, l = from.length; i < l; i++) {
		    str = str.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
		  }

		  str = str.replace(/[^a-z0-9 .-]/g, '') // remove invalid chars
		    .replace(/\s+/g, '-') // collapse whitespace and replace by -
		    .replace(/-+/g, '-'); // collapse dashes

		  return str;
		};

		Vue.prototype.Global._removeTime = (value) => {
      const split = value.split('_');
      split.splice(0, 1);

      return split.join('_');
    };

		Vue.prototype.Global._convertDate = (date) => {
      return new Date(date).toLocaleDateString('fr-FR');
    };

    Vue.prototype.Global._formatPhone = (phone) => {
    	if(phone){
      	return phone.replace(/[^\dA-Z]/g, '').replace(/(.{2})/g, '$1 ').trim();
    	}
    };

    Vue.prototype.Global._showAccount = (user) => {
    	Vue.prototype.$bus.$emit('ACTION_CHANGED', {
  	 		action: 'ACCOUNT',
  	 		user: user
  	 	});
    };

    Vue.prototype.Global._saveNotification = (message, user_id, element_type, element_id) => {
    	axios.post("/api/notification", {
    		'message': message,
    		'user_id': user_id,
    		'element_type': element_type,
    		'element_id': element_id
    	})
			.then(res => {
  			if(res.success === false){
      		// Todo : Error
      	} else {
      		console.log('-- SAVE NOTIFICATION -- ');
      		console.log(message);
      	}
      }).catch(error => console.log(error));
    }

    Vue.prototype.Global._validateEmail = (email) => {
      var re = /\S+@\S+\.\S+/;
      return re.test(email);
    }

    var email_has_error = false;

    Vue.prototype.Global._checkForFields = (form, step) => {
    	var errors = 0;
      var currentForm = form;

      if (step !== undefined) {
        currentForm = form + ' .step-'+ step;
      }

    	var requiredFields = $(currentForm + ' input.js-required');
    	requiredFields.each((index, item) => {
    		$(item).removeClass('field-error');
    		if($(item).val() == ''){
    			$(item).addClass('field-error');
    			errors++;
    		}
    	});

      // Select
      var requiredSelects = $(currentForm + ' select.js-required');
      requiredSelects.each((index, item) => {
        var multiple = $(item).attr('multiple');

        $(item).next('.select2').removeClass('field-error');

        if (typeof multiple !== typeof undefined && multiple !== false) {
          if($(item).find('option:selected').length == 0){
            $(item).next('.select2').addClass('field-error');
            errors++;
          }
        } else {
          if($(item).select2('val') == null){
            $(item).next('.select2').addClass('field-error');
            errors++;
          }
        }
      });

      // Radio
      var requiredRadio = $(currentForm + ' .js-checkbox-list.js-required');
      requiredRadio.each((index, item) => {
        $(item).removeClass('field-error');

        if($(item).find('input:checked').length < 1){
          $(item).addClass('field-error');
          errors++;
        }
      });

      // Email
      var email = $(currentForm + ' input[type="email"].js-required');
      email.each((index, item) => {
        var emailVal = $(item).val();
        if (emailVal !== '' && !Vue.prototype.Global._validateEmail(emailVal)) {
          $(item).addClass('field-error');
          if(email_has_error == false) {
            $('<p class="error-msg js-email-error">Veuillez entrer une adresse e-mail valide</p>').insertAfter($(item));
            email_has_error = true;
          }
        } else {
          $(item).next('.js-email-error').remove();
          email_has_error = false;
        }
      });

    	return errors;
    }

  },
}

Vue.use(Global);