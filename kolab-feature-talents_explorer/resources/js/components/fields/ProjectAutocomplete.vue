<template>
	<input type="text"
		v-model="$parent.filters.name"
		placeholder="Rechercher un projet"
		class="filter-field search-field js-search-projects"
	/>
</template>


<script>
export default {
    props: [],

    data(){
    	return {
      }
    },

    beforeMount(){
    },

    mounted(){
    	$(() => {
	    	$(".js-search-projects").autocomplete({
				  source: (request, response) => {
				    $.get('/api/project', {
							term: request.term,
							mode: 'search'
						})
				    .done((data) => {
				    	response(data.datas);
				    });
				  },
				  create: function() {
            $(this).data('ui-autocomplete')._renderItem = (ul, item) => {
            	return $('<li>')
								.data('item.autocomplete', item)
								.append('<a>' + item.name + '</a>')
								.appendTo(ul);
            };
        	},
				  minLength: 2,
				  select: (event, ui) => {
				  	this.$parent.filters.name = ui.item.name;
				  	setTimeout(() => { this.$parent.getProjects(); }, 10);
				  },
				  open: (event, ui) => {
				  	console.log('OPEN');
				  	$('.ui-autocomplete').css({
				  		width: $('.ui-autocomplete-input').outerWidth(),
				  	});
				  },
				  close: (event, ui) => {
				  	console.log('CLOSE');
				  }
				});
	    });
    },

    methods: {
    }
}
</script>