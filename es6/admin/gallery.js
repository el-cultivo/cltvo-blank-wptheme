
//Galería Meta - Agregar Item
jQuery(window).load(function($){
	jQuery('#table__galeria').on('click','.add__imagen_JS',function() {
		// console.log(123);
		var meta_name = jQuery(this).attr('meta-name');
		var lastkey = 0;
		var nextKey = 0;
		jQuery('#tbody__imagen_JS').find('tr').each(function(){
			var actualKey = parseInt( jQuery(this).attr('meta-key') );
			if (lastkey <= actualKey ){
				lastkey = actualKey;
			}
		});
		nextKey = lastkey + 1;
		var clone = jQuery("#template_clone_JS").clone()
												.appendTo("#tbody__imagen_JS")
												.css("visibility", "visible")
												.attr( "meta-key", nextKey )
												.removeAttr('id');	
		clone.find('*').attr('disabled', false );

		clone.find('td').attr('id',meta_name+'_'+nextKey);
		clone.find('td:nth-child(1) > input').attr('name',meta_name+'['+nextKey+'][imagen]')
		clone.find('td:nth-child(1) > input').attr('id',meta_name+'_'+nextKey+'_imagen');
		clone.attr('id',meta_name+'_'+nextKey);
	});
});

//Galería Meta - Resetear Item
jQuery(window).load(function($){
	jQuery('#table__galeria').on('click','.reset',function(e) {
		e.preventDefault();
	    var meta_name = jQuery(this).attr('meta-name');
	    var num_ele = jQuery('#tbody__imagen_JS').children("tr").length;
	    if( num_ele > 0) { // Verifica que haya al menos un elemento con esa clase__input
	        var row = jQuery(this).parent().parent().parent().attr('meta-key'); // Obtiene la llave del elemento a eliminar
	        jQuery('#table__galeria tr[meta-key='+row+']').remove(); // Elimina los elementos con esa llave
	    }
	    var counter=0;
	    jQuery('#tbody__imagen_JS').find('tr').each(function(){
	        jQuery(this).attr('meta-key',counter);
	        jQuery(this).find('td:nth-child(1) > input').attr('name',meta_name+'['+counter+'][imagen]');

	        jQuery(this).attr('id',meta_name+'_'+counter); 
	        jQuery(this).find('td:nth-child(1) > input').attr('id',meta_name+'_'+counter+'_imagen');
	        counter++;
	    });
	});
});

//Galería Meta - Eliminar Item
jQuery(window).load(function($){
	jQuery('#table__galeria').on('click','.delete__imagen_JS',function(e) {
		e.preventDefault();
	    var meta_name = jQuery(this).attr('meta-name');
	    var num_ele = jQuery('#tbody__imagen_JS').children("tr").length;
	    console.log("num_ele", num_ele);
	    if( num_ele > 0) { // Verifica que haya al menos un elemento con esa clase__input
	        var row = jQuery(this).parent().parent().attr('meta-key'); // Obtiene la llave del elemento a eliminar
	        jQuery('#table__galeria tr[meta-key='+row+']').remove(); // Elimina los elementos con esa llave
	    }
	    var counter=0;
	    jQuery('#tbody__imagen_JS').find('tr').each(function(){
	        jQuery(this).attr('meta-key',counter);
	        jQuery(this).find('td:nth-child(1) > input').attr('name',meta_name+'['+counter+'][imagen]');

	        jQuery(this).attr('id',meta_name+'_'+counter); 
	        jQuery(this).find('td:nth-child(1) > input').attr('id',meta_name+'_'+counter+'_imagen');
	        counter++;
	    });
	});
});


//Galería Meta - Desplegar Imágenes
jQuery(window).load(function($){
	
	function inputCheck() {
		if ( jQuery(this).children('input').attr('value') !== '' ) {
			jQuery(this).children('button').hide();
			jQuery(this).children('#thumbnail').show();
		} else {
			jQuery(this).children('button').show();
		}
	}
	
	jQuery('td.thumbnail').each(inputCheck);
	
});

//Galería Meta - Escoger Imagen de Item
jQuery(window).load(function($){
	
    var meta_image_frame;

    jQuery('#table__galeria').on('click','.media-button',function(e){
	    
	    var the_id = jQuery(this).parent().attr('id');
	    
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
			    jQuery('#table__galeria #'+the_id+' .reset').show();
			    jQuery('#table__galeria #'+the_id+' .media-input').val(id);
			    jQuery('#table__galeria #'+the_id+' .media-input').hide();
			    jQuery('#table__galeria #'+the_id+' .media-button').hide();
			    jQuery('#table__galeria #'+the_id+' .thumbnail_holder').show();
			    jQuery('#table__galeria #'+the_id+' .thumbnail_holder').html('<div class="reset">&#10005;</div><img width="100" src="'+thumbnail+'">');
			    jQuery('#table__galeria #'+the_id+' .delete__imagen_JS').hide();
			});
		};
		
		wp.media.frames.meta_image_frame.on('close', media_set_image); // Closing event for media manger
		wp.media.frames.meta_image_frame.on('select', media_set_image); // Image selection event
		wp.media.frames.meta_image_frame.open(); // Showing media manager

    });
});


//Galería Meta - Sortear Items
jQuery(window).load(function($){
	let gallery = jQuery('#table__galeria')
	
	function start_function() {
		jQuery('body').css('cursor','move');
		jQuery('#table__galeria .tr_sortable').addClass('shadow');
	}
	
	function stop_function() {
		jQuery('body').css('cursor','default');
		jQuery('#table__galeria').find('.tr_sortable').removeClass('shadow');
	}

	function update_function() { }
	
	if (gallery.sortable) {
		gallery.sortable({ 
			items: '.tr_sortable',
			cancel:'input',
			start: start_function,
			stop: stop_function,
			update: update_function
		});			
	}

});


