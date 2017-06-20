/**
 * Gallery para el Cultivo V.2.0.0
 */

import R from 'ramda'
const $ = jQuery

const selectImage = (gallery_container, gallery_name) => function(e) {
	e.preventDefault()
	const meta_image_frame = wp.media.frames.meta_image_frame = wp.media({
		title: "Agregar Imagen",
		multiple: false,
		library: {type: 'image'}
	})
	const media_set_image = function() {
		let selection = wp.media.frames.meta_image_frame.state().get('selection');

		if (!selection) { return;} // No selection
		
		// Iterate through selected elements
		selection.each(function(attachment) {
			let id = attachment.attributes.id;
			let thumbnail = attachment.attributes.sizes.thumbnail.url;
			gallery_container.append(image_template(gallery_name, id, thumbnail))
		});

		//reinicializamos el handler del remove
		let  remove_button = $('.remove-image-from-gallery_JS')
		remove_button.off()
		remove_button.on('click', remove);
	};

	wp.media.frames.meta_image_frame.open(); // Showing media manager
	wp.media.frames.meta_image_frame.on('select', media_set_image); // Image selection event
}

const remove =  e => {//debe reinicializarse después de que se agrega o remueve una imágen
	e.preventDefault()
	$(e.target).parent().remove()
}

const initSortable = function(){
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
}

const image_template = (gallery_name, id, thumbnail_url) =>
	 `<div id="${gallery_name}_${id}" class="tr_sortable gallery__image-container">
		<div class="gallery__image" style="background-image: url('${thumbnail_url}')"></div>
		<button class="button remove-image-from-gallery_JS">Remove</button>
		<input type="hidden" value="${id}" name="${gallery_name}[][imagen]">
	</div>`

const printGalleryImages = (gallery_name, images) => {
	let only_real_images = images.filter(image=> image.imagen !== "")//porque el array viene inicializado con un objeto default e inuil, tenemos que quitar el objeto default
	let html =  only_real_images.map(image => 	image_template(gallery_name, image.imagen, image.url)).join('')
	$(`#${gallery_name}`).append(html)
}

window.initGallery = (gallery_name, images) => {
    const images_array = R.values(images)//porque wordpress o php hace cosas raras y a veces manda un objeto con objetos en lugar de un array con objetos, lo forzamos a que sea un array
	$(window).load(function() {
		const add_button = $('.add-image-to-gallery_JS')
		const gallery_container = $(`#${gallery_name}`)
		
		printGalleryImages(gallery_name, images_array)
		initSortable()
		//handlers
		add_button.on('click', selectImage(gallery_container, gallery_name));

		const remove_button = $('.remove-image-from-gallery_JS')//tenemos que inicializar el remove button después de que se impriman las imagenes
		remove_button.off();	
		remove_button.on('click', remove);	
	})
}
	