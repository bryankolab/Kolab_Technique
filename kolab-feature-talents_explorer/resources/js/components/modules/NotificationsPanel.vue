<!--<template>
	<div class="notifications js-notifications">
    <button type="button" class="notifications__close action-button js-close-notif">Fermer</button>
    <div class="notifications__item js-notifications-item" v-for="notification in notifications">
      <span class="notifications__type">{{ notification.element_type }}</span>
       <p class="notifications__user">{{ notification.user_id }}</p> 
      <p class="notifications__date">{{ Global._convertDate(notification.created_at) }}</p>
      <p class="notifications__content text c-main-grey">{{ notification.message }}</p>
    </div>
  </div>
</template>

<script>
    export default {
    		props: [],

    		data() {
          return {
          	notifications: []
          }
        },

        beforeMount() {
      	},

        mounted() {
        	this.getNotifications();
        },

        created () {
        },

        methods: {
        	getNotifications() {
	          axios.get('/api/notification',
	          {
	              params: {}
	          }).then(res => {
	          	console.log(res);
	              this.notifications = res.data.datas;
	          }).catch(error => console.log(error));
	        },
      	}
    }
</script>-->