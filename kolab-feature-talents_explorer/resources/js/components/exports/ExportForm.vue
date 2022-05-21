<template>
  <div class="popup-wrapper">
    <div class="popup-intro text-center mb-30">
      <h2 class="popup-maintitle">
      	Ajouter des livrables
      </h2>
    </div>
    <form method="POST" v-on:submit.prevent="setExport()" data-url="" class="popup-form form__export" rel="form__export">
      <div class="popup-box is-large" id="export--items">
        <div v-for="(exxport, index) in exxports" v-if="exxports" :key="index" class="popup-form__item">
          <p class="mb-10"><strong>Livrable #{{ index + 1 }}</strong></p>
          <div class="row justify-content-between">
            <input type="hidden" v-model="exxport.id">

            <div class="form-box col-md-4">
              <label for="" class="form-label no-dot">Nom</label>
              <input type="text" placeholder="Nom du film" v-model="exxport.name" class="form-field" required>
            </div>

            <div class="form-box col-md-2">
              <label for="" class="form-label no-dot">Durée</label>
              <input type="text" placeholder="Ex : 60''" v-model="exxport.duration" class="form-field" required>
            </div>

            <div class="form-box col-md-2">
              <label for="" class="form-label no-dot">Résolution</label>
              <select type="text" v-model="exxport.resolution.id" v-select2 :data-key="index" class="form-field js-resolution-data" style="width: 120px;" required></select>
            </div>

            <!-- <div class="form-box col-md-2">
              <label for="" class="form-label no-dot">Sortie</label>
              <select type="text" v-model="exxport.format.id" v-select2 :data-key="index" class="form-field js-format-data" style="width: 120px;" required></select>
            </div> -->

            <div class="form-box col-md-2">
              <label for="" class="form-label no-dot">Sortie</label>
              <input type="text" placeholder="Ex : MP4" v-model="exxport.format_raw" class="form-field" required>
            </div>

            <div class="form-box col-md-2">
              <label for="" class="form-label no-dot">Langue</label>
              <select type="text" v-model="exxport.language.id" v-select2 :data-key="index" class="form-field js-language-data" style="width: 120px;" required></select>
            </div>
          </div>

          <button type="button" class="cross-button popup-form__item__delete" v-on:click="removeExport(index)">
          	<span class="icon icon-bold-cross"></span>
          </button>
        </div>
      </div>
      <button v-on:click.prevent="addExport()" class="button is-gradient popup-button on-top"><span class="icon icon-plus"></span> Ajouter un livrable</button>
      <button type="submit" class="button is-gradient popup-button">Ajouter les livrables</button>
    </form>
  </div>
</template>

<script>
export default {
    props: ['_project'],

    data() {
      return {
      	selectedProject: null,
    		exxports: [],
        exxport: {
          id: null,
          name: '',
          resolution: { id: null, name: null },
          format: { id: null, name: null },
          format_raw: null,
          language: { id: null, name: null }
        }
      }
    },

    created() {
    	if(this._project){
    		this.selectedProject = this._project;
    	}
    },

    mounted() {
    	this.addExport();
    },

    methods: {
    	setExport() {
        axios.post("/api/export", {
        	project: this.selectedProject.id,
          exports: this.exxports
        }).then(res => {
          if (res.success === false) {
            // Error
          } else {
            this.$bus.$emit('EXPORT_ADD_OR_UPDATE', res.data); // Emit add or update talent event
            this.$bus.$emit('ACTION_CHANGED', {
            	action: 'CONGRATS',
            	type: 'EXPORT'
            }); // Congrats modal
          }
        }).catch(error => console.log(error));
      },

    	addExport(){
    		this.exxport.resolution = { id: null, name: null };
        this.exxport.format = { id: null, name: null };
        this.exxport.language = { id: null, name: null };
    		this.exxports.unshift(Object.assign({}, this.exxport));

    		this.setResolutionSelect();
	    	this.setFormatSelect();
	    	this.setLanguageSelect();

	    	setTimeout(() => {
	    		this.triggerResolutionSelect2();
	    		this.triggerFormatSelect2();
	    		this.triggerLanguageSelect2();
	    	}, 10);
    	},

    	removeExport(index){
      	this.exxports.splice(index, 1);
      },

    	/**
       * Set Resolution select2
       */
      setResolutionSelect(){
      	var $this = this;

        $(() => {
        	$('.form__export .js-resolution-data').select2({
            dropdownCssClass: 'has-search',
            placeholder: "Ex : 1920x1080",
            ajax: {
              url: '/api/resolution',
              processResults: function (data) {
                var data = $.map(data.datas, function (obj) {
                  obj.id = obj.id;
                  obj.text = obj.name;

                  return obj;
                });

                return {
                  results: data
                };
              }
            }
          });

          $('.js-resolution-data').on("select2:select", function(e){
	        	var key = $(this).attr('data-key');
	        	$this.exxports[key].resolution.id = e.params.data.id;
	      		$this.exxports[key].resolution.name = e.params.data.name;
	        });
	    	 	$('.js-resolution-data').on("select2:unselect", function(e){
	    	 		var key = $(this).attr('data-key');
	    	 		$this.exxports[key].resolution.id = null;
	      		$this.exxports[key].resolution.name = '';
	    	 	});
        });
      },

      /**
       * Set Format select2
       */
      setFormatSelect(){
      	var $this = this;

        $(() => {
        	$('.form__export .js-format-data').select2({
            dropdownCssClass: 'has-search',
            placeholder: "Ex : ProRes 422",
            ajax: {
              url: '/api/format',
              processResults: function (data) {
                var data = $.map(data.datas, function (obj) {
                  obj.id = obj.id;
                  obj.text = obj.name;

                  return obj;
                });

                return {
                  results: data
                };
              }
            }
          });

          $('.js-format-data').on("select2:select", function(e){
	        	var key = $(this).attr('data-key');
	        	$this.exxports[key].format.id = e.params.data.id;
	      		$this.exxports[key].format.name = e.params.data.name;
	        });
	    	 	$('.js-format-data').on("select2:unselect", function(e){
	    	 		var key = $(this).attr('data-key');
	    	 		$this.exxports[key].format.id = null;
	      		$this.exxports[key].format.name = '';
	    	 	});
        });
      },

      /**
       * Set Language select2
       */
      setLanguageSelect(){
      	var $this = this;

        $(() => {
        	$('.form__export .js-language-data').select2({
            dropdownCssClass: 'has-search',
            placeholder: "Ex : Anglais",
            ajax: {
              url: '/api/language',
              processResults: function (data) {
                var data = $.map(data.datas, function (obj) {
                  obj.id = obj.id;
                  obj.text = obj.name;

                  return obj;
                });

                return {
                  results: data
                };
              }
            }
          });

          $('.js-language-data').on("select2:select", function(e){
	        	var key = $(this).attr('data-key');
	        	$this.exxports[key].language.id = e.params.data.id;
        		$this.exxports[key].language.name = e.params.data.name;
	        });
	    	 	$('.js-language-data').on("select2:unselect", function(e){
	    	 		var key = $(this).attr('data-key');
	    	 		$this.exxports[key].language.id = null;
        		$this.exxports[key].language.name = '';
	    	 	});
        });
      },

      /**
       * Fill the resolution in the select2
       */
      triggerResolutionSelect2(){
      	var $this = this;
       	let resolutionSelect2 = $('.js-resolution-data');

       	resolutionSelect2.each(function(index, item){
       		console.log('Index', index);
       		console.log($this.exxports[index]);
       		console.log($this.exxports[index].resolution.id);
       		if($this.exxports[index].resolution.id){
     				let option = new Option($this.exxports[index].resolution.name, $this.exxports[index].resolution.id, true, true);
     				$(this).append(option).trigger('change');
     			}
     		});
      },

      /**
       * Fill the language in the select2
       */
      triggerFormatSelect2(){
      	var $this = this;
       	let formatSelect2 = $('.js-format-data');

       	formatSelect2.each(function(index, item){
       		if($this.exxports[index].format.id){
     				let option = new Option($this.exxports[index].format.name, $this.exxports[index].format.id, true, true);
     				$(this).append(option).trigger('change');
     			}
     		});
      },

      /**
       * Fill the language in the select2
       */
      triggerLanguageSelect2(){
      	var $this = this;
       	let languageSelect2 = $('.js-language-data');

       	languageSelect2.each(function(index, item){
       		if($this.exxports[index].language.id){
     				let option = new Option($this.exxports[index].language.name, $this.exxports[index].language.id, true, true);
     				$(this).append(option).trigger('change');
     			}
     		});
      },
    }
}
</script>
