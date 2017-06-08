const $ = jQuery
//metaboxUploadInterface:: Obj config {
	//		WPLibraryOpts {String title, Bool multiple, Obj library}, //con defaults
	//		upload_button_selector,
	//		onSelectionForEach(attachment),
	//		afterSelectionForEach(selection), 
	//		onRemoveHandler(selection)
	//	} -> I/O
	//
	// OJO: onRemoveHandler corre dos veces (init y después de la seleccion) así que hay que cuidar de no hacer demasiados demasiados listeners... usar $(selector).off('click') antes del $(selector).on('click' para devincular y no duplicar listeners.
export var metaboxUploadInterface = function(config) {
		
			var library_opts = config.WPLibraryOpts || {};
			
			//setup
			var upload_button = $(config.upload_button_selector);
			
			var meta_image_frame = wp.media.frames.meta_image_frame = wp.media({
				title: library_opts.title || "Agregar Archivo",
				multiple: library_opts.multiple  || false,
				library: library_opts.library || {}
			});

			var onSelection = function() {
				var selection = wp.media.frames.meta_image_frame.state().get('selection');
				if (!selection) { return;} 
				selection.forEach(function(attachment) {
					config.onSelectionForEach(attachment);
				});
			
				if (typeof config.afterSelectionForEach === 'function') {
					config.afterSelectionForEach(selection);
				}
				if (typeof config.onRemoveHandler === 'function') {
					config.onRemoveHandler(selection);
				}
			};

			//register click handlers
			if (upload_button.length > 0 && meta_image_frame) {
				upload_button.on('click', function() {
					meta_image_frame.open();
				});

				if (typeof config.onRemoveHandler === 'function') {
					config.onRemoveHandler();
				}

			}
			
			//register WPLibrary onSelection event handler
			wp.media.frames.meta_image_frame.on('select', onSelection); // Image selection event

	};
	
export var fileUploadConfig = {
		upload_button_selector: '.cltvo_upload_JS',

		onSelectionForEach: function(attachment) {
			var filename = attachment.attributes.filename;
			var file_id = attachment.id;
			var id_input = $('.cltvo_file_id_input_JS');
			var filename_input = $('.cltvo_filename_input_JS');
			var upload_button = $('.cltvo_upload_JS');
			var notification = $('.fileUpload__success_JS');
			
			var removeButton = $('.cltvo_remove_upload_JS');

			upload_button.hide();
			id_input.val(file_id);
			filename_input.val(filename);
			notification.text(filename).show();
			removeButton.show();
		},

		onRemoveHandler: function() {
			var remove_button = $('.cltvo_remove_upload_JS');
			remove_button.off('click');
			remove_button.on('click', function() {
				remove_button.hide();
				$('.cltvo_file_id_input_JS').val('');
				$('.cltvo_filename_input_JS').val('');
				$('.fileUpload__success_JS').hide();
				$('.cltvo_upload_JS').show();
			});
		}
	};


export const  defaultFileUploadConfig = () => {
	jQuery(window).load(() => {
		metaboxUploadInterface(fileUploadConfig)
	})
}

