<template>
    <div class="block welcome-card__block has-bg" style="background-image: url(/images/kolab-dashboard-hello.gif);
    background-repeat: no-repeat;
    background-size: cover;
">
      <div class="block__inner">
        <div class="d-flex justify-content-between">
        	<p class="is-upp c-grey">{{ date }}</p>
        	<p class="welcome-card__time" style="background: rgba(0, 0, 0, 0.5);">{{ time }}</p>
        </div>
      	<h2 class="big-title">Hello !</h2>
  			<p class="text">{{ user.firstname }}.</p>
      </div>
      <div class="block__bck-image">
        <!-- <img src="/images/kolab-dashboard-hello.png" class="img-fluid block__image" alt="" width="" height=""> -->
      </div>
		</div>
</template>

<script>
    export default {
    		props: ['user'],

    		data() {
          return {
          	date: this.getDate(),
          	time: this.getTime()
          }
        },

        beforeMount() {
      	},

        mounted() {
        },

        created () {
            this.scheduleUpdateTime();
        },

        methods: {
        	updateTime() {
              this.date = this.getDate();
              this.time = this.getTime();
              this.scheduleUpdateTime();
          },
          scheduleUpdateTime() {
              setTimeout(this.updateTime, 1000);
          },

          getDate(){
          	const options = { weekday: 'long', month: 'long', day: 'numeric' };
          	return new Date().toLocaleDateString('fr-FR', options);
          },
          getTime(){
          	var hours 	= new Date().getHours().toString().padStart(2, '0');
          	var minutes = new Date().getMinutes().toString().padStart(2, '0');

          	return hours + ':' + minutes;
          }
      	}
    }
</script>
