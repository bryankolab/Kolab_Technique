<template>
	<div class="project-detail__documents block">
		<div class="progress-bar">
			<span class="progress-bar__inner"></span>
			<span class="progress-bar__background"></span>
		</div>
		<div class="mb-30 js-toggle-wrapper">
	  	<h2 class="main-title mb-05"><span class="icon icon-download-cloud"></span> Éléments partagés</h2>
	  	<p class="text c-main-grey title-indent">{{ files.length }}
	  		<template v-if="files.length < 2">élément partagé</template>
	  		<template v-else>éléments partagés</template></p>

	  	<div v-if="state == 'DELETE'" class="delete-actions">
        <button type="button" class="action-link" v-on:click="state = null">Annuler</button>
        <button :disabled="deletedItems.length == 0" v-on:click="File._delete(deletedItems)" class="button icon-button">
        	<span class="icon icon-delete"></span>
        </button>
      </div>

	  	<button type="button" class="button project-detail__action-button js-toggle-button">
        <span class="dot-button"></span>
        <span class="dot-button"></span>
        <span class="dot-button"></span>
      </button>

      <div class="actions-box js-toggle-content">
      	<ul class="context-menu--items">
      		<li class="context-menu--items-element" v-on:click="setState(null)">
      			<span class="icon icon-plus"></span> Ajouter un ou des fichier(s)
      			<input type="file" id="fileupload" ref="files" class="fileupload" multiple />
      		</li>
      		<li class="context-menu--items-element" v-on:click="setState('DELETE')"><span class="icon icon-delete"></span> Supprimer un ou des fichier(s)</li>
      	</ul>
      </div>

  	</div>

  	<div class="row">
  		<!-- <div class="col-md-3">
  			<div class="project-detail__documents-folders">
		  		<p class="mb-05"><strong>Tous</strong></p>
		  		<ul class="folders-list">
			  		<li class="folders-item"><span class="icon icon-folder"></span> Brief</li>
			  		<li class="folders-item"><span class="icon icon-folder"></span> Fonts</li>
			  		<li class="folders-item"><span class="icon icon-folder"></span> Charte graphique</li>
			  		<li class="folders-item"><span class="icon icon-folder"></span> Logos</li>
			  		<li class="folders-item"><span class="icon icon-folder"></span> Musiques</li>
		  		</ul>
	  		</div>
  		</div> -->

	  	<div class="col-md-12">
	  		<div class="simple-table">
          <div class="simple-table__header">
            <div class="simple-table__row">
              <div class="simple-table__td td-50"><p>Nom</p></div>
              <div class="simple-table__td td-25"><p>Date d'ajout</p></div>
              <div class="simple-table__td td-25"><p>Type de fichier</p></div>
            </div>
          </div>
          <div class="simple-table__body">
			  		<div v-for="file in files" class="simple-table__row">
			  			<div class="wrapper-checkbox" v-if="state == 'DELETE'">
	              <input type="checkbox" v-model="deletedItems" :value="file.id" :id="'document-' + file.id" />
	              <span class="checkbox"></span>
	            </div>
			  			<div class="simple-table__td td-50 name">
			  				<label :for="'document-' + file.id" v-if="state == 'DELETE'">{{ Global._removeTime(file.name) }}</label>
			  				<p v-else><a :href="file.url_view" download>{{ Global._removeTime(file.name) }}</a></p>
			  			</div>
			  			<div class="simple-table__td td-25 justify-content-center">
			  				<p>{{ moment(file.created_at).format("DD/MM/YYYY") }}</p>
			  			</div>
			  			<div class="simple-table__td td-25 justify-content-center">
			  				<p>{{ file.extension }}</p>
			  			</div>
			  		</div>
			  	</div>
			  </div>
	  	</div>
  	</div>

  </div>
</template>

<script>
export default {
    props: ['_project'],

    data() {
      return {
      	project: this._project,
      	files: [],
      	state: null,
      	deletedItems: []
      }
    },

    created() {
    },

    mounted() {
    	this.getFiles();

    	this.$bus.$on('FILE_ADD_OR_UPDATE', () => {
      	this.getFiles();
      	this.state = null;
      });

    	$(() => {
	    	$('#fileupload').fileupload({
		      url: '/transfert/index.php?folder=project',
		      dataType: 'json',
		      autoUpload: true,
		      acceptFileTypes: /(\.|\/)(gif|jpe?g|png|pdf|doc)$/i,
		      // maxFileSize: 99999999999,
		      // maxChunkSize: 99999999999,
		      // disableImageResize: true,
		      // previewMaxWidth: 200,
		      // previewMaxHeight: 200,
		      // previewCrop: true
		      submit: (e, data) => {
		      	data.files[0].uploadName = Date.now() + '_' + Vue.prototype.Global._slugify(data.files[0].name);
		      }
			  }).on('fileuploadadd', (e, data) => {
		      $('.progress-bar').fadeIn();
			  }).on('fileuploadprocessalways', (e, data) => {
			  }).on('fileuploadprogressall', (e, data) => {
		      var progress = parseInt(data.loaded / data.total * 100, 10);
      		$('.progress-bar__inner').css('width', progress + '%');
			  }).on('fileuploaddone', (e, data) => {
		      var files = data.result.files;
		      axios.post('/api/project/'+ this.project.id +'/file', {
	    			project: this.project.id,
	    			files: files
		      }).then(res => {
		      	if(res.success === false){
		      		// Error
		      	} else {
		      		this.files = res.data.datas;
		      	}
		      }).catch(error => console.log(error));

		      setTimeout(() => {
			      $('.progress-bar').fadeOut();
			    }, 800);
		      setTimeout(() => {
			      $('.progress-bar__inner').css('width', '0%');
			    }, 1000);
			  }).on('fileuploadfail', function (e, data) {
			    console.log('-- UPLOAD FAIL');
			    console.error(e);
			    setTimeout(() => {
				    $('.progress-bar').fadeOut();
				    $('.progress-bar__inner').css('width', '0%');
			    }, 1000);
			  });
			});
    },

    methods: {
    	getFiles(){
    		axios.get('/api/project/'+ this.project.id +'/file')
    			.then(res => {
		      	if(res.success === false){
		      		// Error
		      	} else {
		      		this.files = res.data.datas;
		      	}
		      }).catch(error => console.log(error));
    	},

    	setState(value){
    		this.state = value;
    		$('.js-toggle-content').fadeOut();
    	}
    }
}
</script>