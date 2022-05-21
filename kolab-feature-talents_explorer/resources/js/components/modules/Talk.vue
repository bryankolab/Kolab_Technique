<template v-if="talk.length == 0">
    <div class="block has-bg overflow-hidden has-no-content">
        <div class="block__inner">
            <img src="/images/talk-block.png" alt="" class="img-fluid is-theme-light">
            <img src="/images/talk-block-dark.png" alt="" class="img-fluid is-theme-dark">
        </div>

        <div class="no-content__block">
            
            	<div style="padding-top: 100px;
    margin: auto;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);">
                      <img src="images/calendar-nocontent.png" alt="" style="
    width: 63px;
    height: 47px;
    margin: auto;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    -webkit-transform: translate(-50%, -50%);
    transform: translate3d(-50%, -50%, 0);
    ">
    <div style="color: #7665A7;"> Rien de prévu sur votre planning du jour</div>
                </div>
            
        </div>
    </div>
</template>

<script>
import Planning from '../planning/Planning.vue'
export default {
  components: { Planning },
    data() {
        return {}
    },

    beforeMount() {
    },

    mounted() {
    },

    methods: {}
}
</script>
