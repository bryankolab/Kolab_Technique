//--------------
// Imports
//--------------
require('./bootstrap');
window.Vue = require('vue');

//--------------
// Variables
//--------------
window.Main = {};
var Main 			= window.Main;
		Main.mode 	= document.querySelector('body').getAttribute('data-mode');

//--------------
// Vue JS Setup
//--------------

//-- Load components
const files = require.context('./', true, /\.vue$/i);
files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));

// Utils code
require('./utils');

// Helpers
require('./helpers/global');
require('./helpers/alert');
require('./helpers/project');
require('./helpers/task');
require('./helpers/appointment');
require('./helpers/talent');
require('./helpers/exxport');
require('./helpers/file');

//-- Create a fresh Vue application
window.app = new Vue({
  el: '#app'
});

window.changeWhenPopupIsOpen = false;

window.eventWhenPopupIsOpen = () => {
	$('body').on('select2:select', '.select2-hidden-accessible', (e) => {
  	window.changeWhenPopupIsOpen = true;
  });
 	$('body').on('select2:unselect', '.select2-hidden-accessible', (e) => {
 		window.changeWhenPopupIsOpen = true;
 	});
 	$('body').on('keyup', 'input, textarea', (e) => {
 		window.changeWhenPopupIsOpen = true;
 	});
 	$('body').on('keyup', 'input[type="text"], textarea, input[type="search"]', function(e) {
    if(!$(this).hasClass('no-caps')) {
   		var parts = this.value.split(" ");
  		for(var i = 0; i < parts.length; i++){
  			var j = parts[i].charAt(0).toUpperCase();
  			parts[i] = j + parts[i].substr(1);
  		}
      this.value = parts.join(" ");
    }
 	});
}


//--------------
// Classic methods
//--------------

/**
 * Init all scripts
 * @return {void}
 */
Main.init = function(){

	//----------------------
	// Common
	//----------------------
  var Common = require('./modules/common.js');
  window.eventWhenPopupIsOpen();

  //----------------------
  // Login
  //----------------------
  var $login = $('[data-module="login"]');
  if ($login.length > 0) {
    var Login = require('./modules/login.js');
  }

  //----------------------
  // User ID - Display "Hello" Screen
  //----------------------
  if(window.userId) {
    if(!localStorage.getItem('first_login')) {
      localStorage.setItem('first_login', window.userId);
      $('.js-welcome-screen').fadeIn();

      setTimeout(function(){
        $('.js-welcome-screen').fadeOut();
        $('body').addClass('is-ready');
      }, 2000);
    } else {
      $('body').addClass('is-ready');
    }
  } else {
    localStorage.removeItem('first_login');
    $('body').addClass('is-ready');
  }

  //----------------------
  // Dark mode
  //----------------------
  // var Darkmode = require('./modules/darkmode.js');


  //----------------------
  // Notifications
  //----------------------
  $('body').on('click', '.js-open-notif', function(){
    $('.js-notifications').addClass('is-open');
  });
  $('body').on('click', '.js-close-notif', function(){
    $('.js-notifications').removeClass('is-open');
  });
  $(document).on('click', (e) => {
    if ($('.js-notifications').hasClass('is-open') && !e.target.matches('.js-notifications, .js-notifications *, .js-open-notif')) {
      $('.js-notifications').removeClass('is-open');
    }
  });

  //-- Notification nb
  setTimeout(function(){
    var nbNotif = $('.js-notifications .js-notifications-item').length;
    
    if(nbNotif > 0) {
      $('.js-notifications').remove();
    } else {
      $('.js-open-notif').html(nbNotif);
      $('.js-notifications').fadeIn();
      $('.js-open-notif').fadeIn();
    }
  }, 1000);

}


//--------------
// Classic execution
//--------------

$(function(){
	Main.init();
});
