const $ = jQuery
var sortableTables = function() {
	//Cltvo_sortableInputs Meta - Agregar Item

	var initSortable = function () {

		var sortableContainer = $('body').find('.cltvo_sortable_table__container_JS');
		if (sortableContainer.hasClass('ui-sortable')) {
			sortableContainer.sortable("destroy");
		}




		function start_function() {
			$('body').css('cursor','move');
			$('.cltvo_sortable_table__container_JS .tr_sortable_JS').addClass('shadow');
		}
		
		function stop_function() {
			$('body').css('cursor','default');
			sortableContainer.find('.tr_sortable_JS').removeClass('shadow');
		}

		function update_function() { }

		sortableContainer.sortable({ 
			items: '.tr_sortable_JS',
			cancel:'.input__sortable_JS',
			start: start_function,
			stop: stop_function,
			update: update_function
		});	
	}

	jQuery(document).ready(function agregarItem($){
		$('.cltvo_sortable_table__container_JS').on('click','.add__sortable_JS', function() {
			var meta_key = jQuery(this).data('meta-key');
			var lastkey = 0;
			var nextKey = 0;
			jQuery('#tbody__sortable_JS').find('tr').each(function(){
				var actualKey = parseInt( jQuery(this).attr('meta-key') );
				if (lastkey <= actualKey ){
					lastkey = actualKey;
				}
			});
			nextKey = lastkey + 1;
			var tr = jQuery("#sortable_clone_JS")
				.clone()
				.appendTo("#tbody__sortable_JS")
				.css("visibility", "visible")
				.attr( "meta-key", nextKey )
				.removeAttr('id');

			tr.find('*').attr('disabled', false );
	 		tr.attr('id',meta_key+'_'+nextKey);
	 		
			tr.find('td').attr('id',meta_key+'_'+nextKey);
			tr.find('td input, td textarea').each(function(index, el) {
				var $el = $(el),
					key = $el.data('key');
				$el.attr('name', meta_key+'['+nextKey+']['+key+']');
				$el.attr('id', meta_key+'_'+nextKey+'_'+key);
			});
			
			initSortable();
	   });
	});

	//Cltvo_sortableInputs Meta - Elimnar Item
	jQuery(document).ready(function eliminarItem($){
		$('.cltvo_sortable_table__container_JS').on('click','.delete__sortable_JS',function(e) {
			e.preventDefault();
			var meta_name = jQuery(this).data('meta-key');
			var num_ele = jQuery('#tbody__sortable_JS').children("tr").length;
			if( num_ele > 1) { // Verifica que haya al menos un elemento con esa clase__input
				var row = jQuery(this).parent().parent().attr('meta-key'); // Obtiene la llave del elemento a eliminar
				jQuery('.cltvo_sortable_table__container_JS tr[meta-key='+row+']').remove(); // Elimina los elementos con esa llave
			}
			initSortable();
		});
	});

	//Cltvo_sortableInputs Meta - Sort Gallery Rows
	jQuery(document).ready( function sortGalleryRows($){
		$('.move').click( function (e) { e.preventDefault();});
		initSortable();
	});
	
};


jQuery(window).load(function() {
	sortableTables()
})
