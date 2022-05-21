<template>
    <div class="block has-bg rdv__block block-g" v-bind:class="{ 'has-no-content' : appointments.length == 0 }">
        <div style="margin-bottom:15px; display:flex;">
    <div class="main-appointment" >Rendez-vous
      <div class="tiny-button-section">
      <a href="javascript:;" class=" is-gradient" v-on:click="Global._setAction('SET_APPOINTMENT', {appointment: appointment})">
        
      <span class="tiny-button icon-plus"> </span>
      </a>
    </div>
    </div>
    <div class="filter-select appointment-filters_notes" > 
  <div class="poopover-h "  v-on:click="" > Aujourd'hui  <div class="poopover" v-if="appointments.length > 0">{{ appointments.length }}</div></div>
  <div class="c-1" v-on:click=""> À venir <div class="poopover">{{ appointments.length }}</div> </div>
  </div>
    </div>
    <div class="rdv__card-inner js-slide-toggle" :data-slide="'slide-'"> <label for="" class="form-label" style="color:#7665A7;" v-if="appointments.length > 0">Aujourd'hui</label>
    <span class="rdv__arrow icon icon-down-arrow" v-if="appointments.length > 0" ></span> </div>
               
                    <div class="row-card" :data-target="'slide-'" v-if="appointments.length > 0" >
                        <div v-for="appointment in appointments" class="rdv__col col-md-13" v-on:contextmenu.prevent="contextMenu.show($event, 'APPOINTMENT', {appointment: appointment}, contextMenuCallback);">
                            <div class="rdv__card">
                           
                                 <div class="rdv-car_inf">
                                     
                                    <!-- <span class="rdv__time">
                                    	<span class="rdv__time-title">RDV</span>
                                    </span>      -->
                                    	
                                        
                                        	
                                        <!--<button type="button" class="button project-detail__action-button-task js-toggle-button">
                                            <span class="dot-button-task"></span>
                                            <span class="dot-button-task"></span>
                                            <span class="dot-button-task"></span>

                                        </button>
                                        <div class="actions-box js-toggle-content">
      	                                    <ul class="context-menu--items">
      		                                    <li class="context-menu--items-element" v-on:click.prevent="Appointment._edit(appointment)">
                                                    <span class="icon icon-plus"></span> Modifier le rendez-vous
      			                                    <input type="file" id="fileupload" ref="files" class="fileupload" multiple />
      		                                    </li>
      		                                    <li class="context-menu--items-element" v-on:click.prevent="Appointment._delete(appointment)"><span class="icon icon-delete"></span> Supprimer le rendez</li>
      	                                    </ul>
                                        </div> --> 

                                    <!--<span class="rdv__room">{{ appointment.room ? appointment.room.name : '' }}</span>-->
                                        
                                    <div class="rdv-marg"><span class="rdv__clock"><span class="picto picto-time"></span> {{ getHours(appointment.datetime) }}:{{ getMinutes(appointment.datetime) }}</span>
                                        <div class="breaking-word">
                                            <p><strong>{{ appointment.firstname }}</strong></p>
                                        </div>
                                    </div>
                                    
                                    <div style="display:flex;align-items:center;">
                                        <div class="appointment-nav">
                                            <div>

                                                <div type="button" class="button js-toggle-button appointment-edit">
                                            <span class="dot-button-task" ></span>
                                            <span class="dot-button-task" ></span>
                                            <span class="dot-button-task" ></span>

                                        </div>
                                        <div class="actions-box js-toggle-content">
      	                                    <ul class="context-menu--items">
      		                                    <li class="context-menu--items-element" v-on:click.prevent="Appointment._edit(appointment)">
                                                    <span class="icon icon-plus"></span> Modifier le rendez-vous
      			                                    <input type="file" id="fileupload" ref="files" class="fileupload" multiple />
      		                                    </li>
      		                                    <li class="context-menu--items-element" v-on:click.prevent="Appointment._delete(appointment)"><span class="icon icon-delete"></span> Supprimer le rendez</li>
      	                                    </ul>
                                        </div>
                                                </div>
                                                <div >

                                                <button type="button" class="action-button js-position-button dash-note appointment-notes_show" v-if="appointment.note" ><span class="icon icon-notes icon-center"></span></button>

                                                <div class="notes js-position-content">

                                                    <div class="notes__actions">
                                                        <button type="button" v-on:click.prevent="Appointment._edit(appointment, contextMenuCallback, [true])"><span class="icon icon-edit"></span></button>
                                                        <button type="button" class="js-close-note"><span class="icon icon-cross"></span></button>
                                                    </div>
                                                    <p class="word-wrap"><strong>Notes :</strong></p>
                                                    <p class="text c-main-grey">{{ appointment.note }}</p>

                                                </div>
                                                </div>
                                                
                                        </div>
                                                <div class="rdv__contact" >
                                                    

                                        <!-- <a :href="'mailto:' + appointment.email" class="text c-main-grey test" ></a>--><span class=" text test icon icon-location wrapper-js_query move" ><a :href="'location:' + appointment.location" class="content" >{{appointment.location}}</a></span><br /> 
                                                    
                                        <span class=" text  test icon icon-phone" ><a :href="'tel:' + appointment.phone" class="content" >{{appointment.phone}}</a></span> <br /> 
                                        <span class=" text  test icon icon icon-email-full" ><a :href="'mailto:' + appointment.email" class="content" >{{appointment.email}}</a></span> <br />  
                                        <!-- <a :href="'mailto:' + appointment.email" class="text c-main-grey test" ><span class="icon icon-email-full"></span><span>{{appointment.email}}</span> </a><br>
                                        <a :href="'tel:' + appointment.phone" v-if="appointment.phone != null" class="text c-main-grey test" ><span class="icon icon-phone" ></span><span>{{appointment.email}}</span>  </a><br />
                                        <a :href="'location:' + appointment.location" v-if="appointment.location" class="text c-main-grey test" ><span class="picto picto-location" ></span><span>{{appointment.email}}</span>  </a><br /> -->
                                    </div>  
                                    </div>
                                 <!-- <div class="rdv__card-inner js-slide-toggle" :data-slide="'slide-' + appointment.id">
                                    
                                    <p class="text c-main-grey">{{ appointment.job }}</p>
                                    <span class="rdv__arrow icon icon-down-arrow"></span> 
                                </div>-->
                                    
                                    </div>
                                </div>
                            
                        </div>
                    </div>
                    
               
           
        

            <template v-if="appointments.length == 0">
            	<div style="padding-top: 100px;
    margin: auto;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);">
                      <img src="images/work.png" alt="" style="
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
    <div style="color: #7665A7;"> Aucun rendez-vous pour aujourd'hui</div>
                </div>
            </template>

            <!-- <div class="rdv__maintitle">
                <h2 class="main-title">{{ appointments.length }} Rendez-vous</h2>
                <p class="c-grey">À venir</p>
            </div> -->
       
        
         <!-- <div class="no-content__block" v-if="appointments.length == 0">
            
            <div class="no-content__inner">
                <div class="text-center">
                    <span class="picto picto-calendar"></span>
                </div>
                <h2 class="main-title">Pas de RDV en cours</h2>
                <p class="text c-main-grey">Les rendez-vous en attente s'afficheront ici</p>
            </div>
        </div>  -->
        
    </div>
    
</template>

<script>
export default {
    props: ['appointments', 'contextMenu', 'contextMenuCallback','appointment','_talent','users', 'appointment'],

    data() {

        let date = new Date();

        return {
            visible: true,
            talent: {
					id: null,
					firstname: '',
					lastname: '',
					email: '',
					phone : '',
					job : '',
					job_id: '',
					price : '',
					city : '',
					country : '',
					profile_type : '',
                    profile_url : '',
					skills : [],
					skills_ids : [],
                    instagram_url: '',
                    vimeo_url: '',
                    youtube_url: '',
                    filters: {
                        status: null
                },
                },
        today: moment().format('YYYY-MM-DD'),
        error_msg: false,
        }
    },

    beforeMount() {
    },

    mounted() {
    },

    methods: {
       

        getAppointments(){

            this.contextMenuCallback(false, filters); 
        },

        wrappertoogle(){
            $('.content').click(function(e) {
  $(e.target).closest('.wrapper-js_query').toggleClass('move');
});
        }

    }
}
</script>
