const $ = jQuery
// Banners - Eliminar Item
jQuery(document).ready(function($) {
	$('#table__banners').on('click','.reset',function(e){
		var the_id = $(this).closest('tr').attr('id');
		$('#table__banners #'+the_id+' .media-button').css('display','block');
		$('#table__banners #'+the_id+' img').hide();
		$(this).hide();
	});
});

// Banners - Desplegar Imágenes
jQuery(document).ready(function($){
	function inputCheck() {
		if ( $(this).find('input').val() !== '' ) {
			$(this).find('button').css('display','none');
		} else {
			$(this).find('button').css('display','block');
		}
	}
	$('tr.banner_row').each(inputCheck);
});
// Featured Image - Escoger Imagen de Item
jQuery(document).ready(function($){
	var meta_image_frame;

	$('#table__banners').on('click','.media-button',function(e){

		var the_id = $(this).closest('tr').attr('id');
		// console.log(the_id);

		e.preventDefault();

		if ( meta_image_frame ) {
			meta_image_frame.open();
			return;
		}
		
		// Sets up the media library frame
		var meta_image_frame = wp.media.frames.meta_image_frame = wp.media({
			title: "Agregar Imagen",
			multiple: false,
			library: {type: 'image'}
		});

		var media_set_image = function() {
			var selection = wp.media.frames.meta_image_frame.state().get('selection');
			
			if (!selection) { return;} // No selection
			
			// Iterate through selected elements
			selection.each(function(attachment) {
				var id = attachment.attributes.id;
				var thumbnail = attachment.attributes.sizes.thumbnail.url;
				$('#table__banners #'+the_id+' .media-input').val(id);
				$('#table__banners #'+the_id+' .media-button').css('display','none');
				$('#table__banners #'+the_id+' .thumbnail_holder').html('<div class="reset">&#10005;</div><img width="100" src="'+thumbnail+'"><button class="button media-button" style="display:none;">Elegir Imagen</button>');
			});
		};
		
		wp.media.frames.meta_image_frame.on('close', media_set_image); // Closing event for media manger
		wp.media.frames.meta_image_frame.on('select', media_set_image); // Image selection event
		wp.media.frames.meta_image_frame.open(); // Showing media manager

	});
});