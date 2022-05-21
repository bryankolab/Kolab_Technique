<template>
	<div class="popup js-popup confirm-popup" v-if="action && action != 'CLOSE'">
        <div class="popup-content">
            <div class="popup-box">
        		<p class="mb-15"><span class="icon icon-alert"></span> <strong>{{ title }}</strong></p>
        		<p class="text c-main-grey mb-15">{{ text }}</p>
        		<p class="text c-main-grey mb-15">{{ confirmText }}</p>
                <template v-if="button_2">
                    <button v-on:click="doIt(button_2.method, button_2.args)" :class="button_2.class" class="confirm-popup__button button is-gradient">{{ button_2.title }}</button>
                </template>
                
        		<template v-if="button_1">
        			<button v-on:click="doIt(button_1.method, button_1.args)" :class="button_1.class" class="confirm-popup__button button is-grey is-light">{{ button_1.title }}</button>
        		</template>
        	</div>
        </div>
    </div>
</template>

<script>
export default {
    props: [],

    data(){
    	return {
    		action: null,
    		title: null,
    		text: null,
    		confirmText: null,
    		button_1: {},
    		button_2: {}
      }
    },

    beforeMount(){
    },

    mounted(){
    	this.$bus.$on('CONFIRM', (datas) => {
	      this.title = datas.title ? datas.title : null;
	      this.text = datas.text ? datas.text : null;
	      this.confirmText = datas.confirmText ? datas.confirmText : null;

	      // Actions
	      this.button_1 = datas.button_1 ? datas.button_1 : null;
	      this.button_2 = datas.button_2 ? datas.button_2 : null;

	      this.action = datas.action ? datas.action : 'OPEN';

	      // Add body class
	      this.setBodyClass();
	    });
    },

    methods: {
    	doIt(method, args){
    		if(method){
    			method(args);
    		}

    		this.close();
    	},

    	close(){
    		this.action = 'CLOSE';

    		// Remove body class
    		this.setBodyClass();
    	},

    	setBodyClass(){
    		if(this.action == 'CLOSE'){
    			$('body').removeClass('alert-is-open');
    	    } else {
    	      	$('body').addClass('alert-is-open');
    	    }
    	}
    }
}
</script>
