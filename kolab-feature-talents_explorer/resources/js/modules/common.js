var Common = {};


//--------------
// Variable
//--------------

Common.variable = '';
Common.actionsButtons = '.js-actions-buttons';


/**
 * Show floating element function
 * @return {void}
 */
Common.showFloatingEl = function(element) {
  element.fadeIn(300);
  $('body').addClass('overflow-hidden');

  if($('.scroll-content').length > 0) {
    $('.scroll-content').addClass('overflow-hidden');
  }
}

/**
 * Hide floating element function
 * @return {void}
 */
Common.hideFloatingEl = function(element) {
  element.fadeOut(300);
  $('body').removeClass('overflow-hidden');

  if($('.scroll-content').length > 0) {
    $('.scroll-content').removeClass('overflow-hidden');
  }
}


/**
 * Task Actions function
 * @return {void}
 */
Common.actionsButtonsInteration = function() {
	// on hover
	$('html, body').on('mouseover', '.js-action-button', function(){
		$(this).parent().addClass('is-hover');

		if(!$(this).is(':first-child')) {
			$(this).parent().addClass('is-second');
		}
	}).on('mouseout', '.js-action-button', function() {
		$(this).parent().removeClass('is-hover is-second');
	});
}

/**
 * Open/Close this Content function
 * @return {void}
 */
Common.toggleContent = function() {
  $('body').on('click', '.js-toggle-button', function(){
    var $thisContent = $(this).closest('.js-toggle-wrapper').find('.js-toggle-content');

    if(!$thisContent.is(':visible')){
      Common.hideFloatingEl($('.js-toggle-content'));
      $(this).closest('.js-toggle-wrapper').removeClass('is-active');

      setTimeout(() => {
        Common.showFloatingEl($thisContent);
        $(this).closest('.js-toggle-wrapper').addClass('is-active');
      }, 10);
    } else {
      Common.hideFloatingEl($('.js-toggle-content'));
      $(this).closest('.js-toggle-wrapper').removeClass('is-active');
    }
  });

  $(document).on('click', (e) => {
    if ($('.js-toggle-content').is(':visible') && !e.target.matches('.js-toggle-content, .js-toggle-content *')) {
      Common.hideFloatingEl($('.js-toggle-content'));
      $('.js-toggle-wrapper').removeClass('is-active');
    }
  });
}

/**
 * Toggle Class function
 * @return {void}
 */
Common.toggleClass = function() {
  $('body').on('click', '.js-toggleclass', function(){
    if($(this).closest('.js-toggleclass-wrapper').length > 0) {
      $(this).closest('.js-toggleclass-wrapper').toggleClass('is-active');
    } else {
      $(this).toggleClass('is-active');
    }
  });

  // Clic on planning task > go to detail (remove slide up)
  $(document).on('click', (e) => {
    if (!$(e.target).closest('.js-toggleclass-wrapper').hasClass('is-active') && e.target.matches('.is-task, .is-task *')) {
      $(e.target).closest('.js-toggleclass-wrapper').addClass('is-active');
    }
  });
}

/**
 * Content Position function
 * @return {void}
 */
Common.contentPosition = function() {
  $('body').on('click', '.js-position-button', function(){
    var scrollTop = $(window).scrollTop();
    var thisButtonTop = $(this).offset().top - scrollTop;
    var thisButtonLeft = $(this).offset().left + $(this).outerWidth()/2;
    var $thisContent = $(this).closest('.js-position-wrapper').find('.js-position-content');


    if(!$thisContent.is(':visible')){
      Common.hideFloatingEl($thisContent);

      setTimeout(() => {
        Common.showFloatingEl($thisContent);
      }, 10);
    } else {
      Common.hideFloatingEl($thisContent);
    }

    $thisContent.css({
      top: thisButtonTop + 20,
      left: thisButtonLeft
    })
  });

  $(document).on('click', (e) => {
    var $content = $('.js-position-content');
    var $contentButton = $('.js-close-note');

    if(($content.is(':visible') && !$content.is(e.target) && $content.has(e.target).length === 0) || $(e.target).hasClass('js-close-note')){
      Common.hideFloatingEl($content);
    }
  });

}


/**
 * Popup Position function
 * @return {void}
 */
Common.popupPosition = function() {
  $('body').on('click', '.js-popup-position-button', function(){
    var scrollTop = $(window).scrollTop();
    var thisButtonTop = $(this).offset().top - $(this).outerHeight() - scrollTop;
    var thisButtonLeft = $(this).offset().left + $(this).outerWidth() + 10;
    var dataPopup = $(this).attr('data-popup');
    var $thisPopup = $('.' + dataPopup);

    $thisPopup.css({
      top: thisButtonTop + scrollTop + 20,
      left: thisButtonLeft
    });
  });

  $(document).on('click', (e) => {
    var $container = $('.task-popin');
    var $button = $('.js-popup-position-button');

    if ($('.task-popin').is(':visible') && !e.target.matches('.task-popin, .task-popin *, .js-popup-position-button, .js-popup-position-button *')) {
   		Vue.prototype.$bus.$emit('ACTION_CHANGED', {action: null });
   		setTimeout(() => { $('.js-popup').css({ top: 0, left: 0 }); }, 100);
    }
  });

  $(window).on('resize', () => {
    Vue.prototype.$bus.$emit('ACTION_CHANGED', {action: null });
    setTimeout(() => { $('.js-popup').css({ top: 0, left: 0 }); }, 100);
  });
}



/**
 * Slide Toggle function
 * @return {void}
 */
Common.slideToggle = function() {
  $('body').on('click', '.js-slide-toggle', function(){
    var dataSlide = $(this).attr('data-slide');
    console.log(dataSlide);

    $(this).toggleClass('is-active');
    $('[data-target="'+ dataSlide +'"]').stop().slideToggle();
  });
}


//--------------
// Execution
//--------------

Common.init = function(){
	// Common functions called
}

Common.init();

//-- Task Actions buttons
Common.actionsButtonsInteration();

//-- Toggle this Content
Common.toggleContent();

//-- Toggle active Class
Common.toggleClass();

//-- Content Position
Common.contentPosition();

//-- Popup Position
Common.popupPosition();

//-- Slide Toggle
Common.slideToggle();


module.exports = Common;
