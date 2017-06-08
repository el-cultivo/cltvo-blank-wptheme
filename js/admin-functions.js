/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId])
/******/ 			return installedModules[moduleId].exports;
/******/
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			exports: {},
/******/ 			id: moduleId,
/******/ 			loaded: false
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.loaded = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "./js/";
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(0);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
/***/ (function(module, exports, __webpack_require__) {

	'use strict';
	
	__webpack_require__(1);
	
	__webpack_require__(2);
	
	__webpack_require__(3);
	
	__webpack_require__(4);
	
	var _fileUpload = __webpack_require__(5);
	
	(0, _fileUpload.defaultFileUploadConfig)();

/***/ }),
/* 1 */
/***/ (function(module, exports) {

	'use strict';
	
	//Galería Meta - Agregar Item
	jQuery(window).load(function ($) {
		jQuery('#table__galeria').on('click', '.add__imagen_JS', function () {
			// console.log(123);
			var meta_name = jQuery(this).attr('meta-name');
			var lastkey = 0;
			var nextKey = 0;
			jQuery('#tbody__imagen_JS').find('tr').each(function () {
				var actualKey = parseInt(jQuery(this).attr('meta-key'));
				if (lastkey <= actualKey) {
					lastkey = actualKey;
				}
			});
			nextKey = lastkey + 1;
			var clone = jQuery("#template_clone_JS").clone().appendTo("#tbody__imagen_JS").css("visibility", "visible").attr("meta-key", nextKey).removeAttr('id');
			clone.find('*').attr('disabled', false);
	
			clone.find('td').attr('id', meta_name + '_' + nextKey);
			clone.find('td:nth-child(1) > input').attr('name', meta_name + '[' + nextKey + '][imagen]');
			clone.find('td:nth-child(1) > input').attr('id', meta_name + '_' + nextKey + '_imagen');
			clone.attr('id', meta_name + '_' + nextKey);
		});
	});
	
	//Galería Meta - Resetear Item
	jQuery(window).load(function ($) {
		jQuery('#table__galeria').on('click', '.reset', function (e) {
			e.preventDefault();
			var meta_name = jQuery(this).attr('meta-name');
			var num_ele = jQuery('#tbody__imagen_JS').children("tr").length;
			if (num_ele > 0) {
				// Verifica que haya al menos un elemento con esa clase__input
				var row = jQuery(this).parent().parent().parent().attr('meta-key'); // Obtiene la llave del elemento a eliminar
				jQuery('#table__galeria tr[meta-key=' + row + ']').remove(); // Elimina los elementos con esa llave
			}
			var counter = 0;
			jQuery('#tbody__imagen_JS').find('tr').each(function () {
				jQuery(this).attr('meta-key', counter);
				jQuery(this).find('td:nth-child(1) > input').attr('name', meta_name + '[' + counter + '][imagen]');
	
				jQuery(this).attr('id', meta_name + '_' + counter);
				jQuery(this).find('td:nth-child(1) > input').attr('id', meta_name + '_' + counter + '_imagen');
				counter++;
			});
		});
	});
	
	//Galería Meta - Eliminar Item
	jQuery(window).load(function ($) {
		jQuery('#table__galeria').on('click', '.delete__imagen_JS', function (e) {
			e.preventDefault();
			var meta_name = jQuery(this).attr('meta-name');
			var num_ele = jQuery('#tbody__imagen_JS').children("tr").length;
			console.log("num_ele", num_ele);
			if (num_ele > 0) {
				// Verifica que haya al menos un elemento con esa clase__input
				var row = jQuery(this).parent().parent().attr('meta-key'); // Obtiene la llave del elemento a eliminar
				jQuery('#table__galeria tr[meta-key=' + row + ']').remove(); // Elimina los elementos con esa llave
			}
			var counter = 0;
			jQuery('#tbody__imagen_JS').find('tr').each(function () {
				jQuery(this).attr('meta-key', counter);
				jQuery(this).find('td:nth-child(1) > input').attr('name', meta_name + '[' + counter + '][imagen]');
	
				jQuery(this).attr('id', meta_name + '_' + counter);
				jQuery(this).find('td:nth-child(1) > input').attr('id', meta_name + '_' + counter + '_imagen');
				counter++;
			});
		});
	});
	
	//Galería Meta - Desplegar Imágenes
	jQuery(window).load(function ($) {
	
		function inputCheck() {
			if (jQuery(this).children('input').attr('value') !== '') {
				jQuery(this).children('button').hide();
				jQuery(this).children('#thumbnail').show();
			} else {
				jQuery(this).children('button').show();
			}
		}
	
		jQuery('td.thumbnail').each(inputCheck);
	});
	
	//Galería Meta - Escoger Imagen de Item
	jQuery(window).load(function ($) {
	
		var meta_image_frame;
	
		jQuery('#table__galeria').on('click', '.media-button', function (e) {
	
			var the_id = jQuery(this).parent().attr('id');
	
			e.preventDefault();
	
			if (meta_image_frame) {
				meta_image_frame.open();
				return;
			}
	
			// Sets up the media library frame
			var meta_image_frame = wp.media.frames.meta_image_frame = wp.media({
				title: "Agregar Imagen",
				multiple: false,
				library: { type: 'image' }
			});
	
			var media_set_image = function media_set_image() {
				var selection = wp.media.frames.meta_image_frame.state().get('selection');
	
				if (!selection) {
					return;
				} // No selection
	
				// Iterate through selected elements
				selection.each(function (attachment) {
					var id = attachment.attributes.id;
					var thumbnail = attachment.attributes.sizes.thumbnail.url;
					jQuery('#table__galeria #' + the_id + ' .reset').show();
					jQuery('#table__galeria #' + the_id + ' .media-input').val(id);
					jQuery('#table__galeria #' + the_id + ' .media-input').hide();
					jQuery('#table__galeria #' + the_id + ' .media-button').hide();
					jQuery('#table__galeria #' + the_id + ' .thumbnail_holder').show();
					jQuery('#table__galeria #' + the_id + ' .thumbnail_holder').html('<div class="reset">&#10005;</div><img width="100" src="' + thumbnail + '">');
					jQuery('#table__galeria #' + the_id + ' .delete__imagen_JS').hide();
				});
			};
	
			wp.media.frames.meta_image_frame.on('close', media_set_image); // Closing event for media manger
			wp.media.frames.meta_image_frame.on('select', media_set_image); // Image selection event
			wp.media.frames.meta_image_frame.open(); // Showing media manager
		});
	});
	
	//Galería Meta - Sortear Items
	jQuery(window).load(function ($) {
		var gallery = jQuery('#table__galeria');
	
		function start_function() {
			jQuery('body').css('cursor', 'move');
			jQuery('#table__galeria .tr_sortable').addClass('shadow');
		}
	
		function stop_function() {
			jQuery('body').css('cursor', 'default');
			jQuery('#table__galeria').find('.tr_sortable').removeClass('shadow');
		}
	
		function update_function() {}
	
		if (gallery.sortable) {
			gallery.sortable({
				items: '.tr_sortable',
				cancel: 'input',
				start: start_function,
				stop: stop_function,
				update: update_function
			});
		}
	});

/***/ }),
/* 2 */
/***/ (function(module, exports) {

	'use strict';
	
	var $ = jQuery;
	var hexColorSampleChanger = function hexColorSampleChanger() {
		//TODO validate that the input value is a hex number
		var input = $('.hex-color-text-input_JS');
		if (input.lenght < 0) {
			return;
		}
		input.on('change', function () {
			var $this = $(this),
			    sample = $this.siblings('.hex-color-text-sample__container').find('.hex-color-text-sample_JS'),
			    color = $this.val() === '' ? 'transparent' : '#' + $this.val();
			sample.css('backgroundColor', color);
		});
	};
	
	$(window).load(function () {
		hexColorSampleChanger();
	});

/***/ }),
/* 3 */
/***/ (function(module, exports) {

	'use strict';
	
	var $ = jQuery;
	var sortableTables = function sortableTables() {
		//Cltvo_sortableInputs Meta - Agregar Item
	
		var initSortable = function initSortable() {
	
			var sortableContainer = $('body').find('.cltvo_sortable_table__container_JS');
			if (sortableContainer.hasClass('ui-sortable')) {
				sortableContainer.sortable("destroy");
			}
	
			function start_function() {
				$('body').css('cursor', 'move');
				$('.cltvo_sortable_table__container_JS .tr_sortable_JS').addClass('shadow');
			}
	
			function stop_function() {
				$('body').css('cursor', 'default');
				sortableContainer.find('.tr_sortable_JS').removeClass('shadow');
			}
	
			function update_function() {}
	
			sortableContainer.sortable({
				items: '.tr_sortable_JS',
				cancel: '.input__sortable_JS',
				start: start_function,
				stop: stop_function,
				update: update_function
			});
		};
	
		jQuery(document).ready(function agregarItem($) {
			$('.cltvo_sortable_table__container_JS').on('click', '.add__sortable_JS', function () {
				var meta_key = jQuery(this).data('meta-key');
				var lastkey = 0;
				var nextKey = 0;
				jQuery('#tbody__sortable_JS').find('tr').each(function () {
					var actualKey = parseInt(jQuery(this).attr('meta-key'));
					if (lastkey <= actualKey) {
						lastkey = actualKey;
					}
				});
				nextKey = lastkey + 1;
				var tr = jQuery("#sortable_clone_JS").clone().appendTo("#tbody__sortable_JS").css("visibility", "visible").attr("meta-key", nextKey).removeAttr('id');
	
				tr.find('*').attr('disabled', false);
				tr.attr('id', meta_key + '_' + nextKey);
	
				tr.find('td').attr('id', meta_key + '_' + nextKey);
				tr.find('td input, td textarea').each(function (index, el) {
					var $el = $(el),
					    key = $el.data('key');
					$el.attr('name', meta_key + '[' + nextKey + '][' + key + ']');
					$el.attr('id', meta_key + '_' + nextKey + '_' + key);
				});
	
				initSortable();
			});
		});
	
		//Cltvo_sortableInputs Meta - Elimnar Item
		jQuery(document).ready(function eliminarItem($) {
			$('.cltvo_sortable_table__container_JS').on('click', '.delete__sortable_JS', function (e) {
				e.preventDefault();
				var meta_name = jQuery(this).data('meta-key');
				var num_ele = jQuery('#tbody__sortable_JS').children("tr").length;
				if (num_ele > 1) {
					// Verifica que haya al menos un elemento con esa clase__input
					var row = jQuery(this).parent().parent().attr('meta-key'); // Obtiene la llave del elemento a eliminar
					jQuery('.cltvo_sortable_table__container_JS tr[meta-key=' + row + ']').remove(); // Elimina los elementos con esa llave
				}
				initSortable();
			});
		});
	
		//Cltvo_sortableInputs Meta - Sort Gallery Rows
		jQuery(document).ready(function sortGalleryRows($) {
			$('.move').click(function (e) {
				e.preventDefault();
			});
			initSortable();
		});
	};
	
	jQuery(window).load(function () {
		sortableTables();
	});

/***/ }),
/* 4 */
/***/ (function(module, exports) {

	'use strict';
	
	var $ = jQuery;
	// Banners - Eliminar Item
	jQuery(document).ready(function ($) {
		$('#table__banners').on('click', '.reset', function (e) {
			var the_id = $(this).closest('tr').attr('id');
			$('#table__banners #' + the_id + ' .media-button').css('display', 'block');
			$('#table__banners #' + the_id + ' img').hide();
			$(this).hide();
		});
	});
	
	// Banners - Desplegar Imágenes
	jQuery(document).ready(function ($) {
		function inputCheck() {
			if ($(this).find('input').val() !== '') {
				$(this).find('button').css('display', 'none');
			} else {
				$(this).find('button').css('display', 'block');
			}
		}
		$('tr.banner_row').each(inputCheck);
	});
	// Featured Image - Escoger Imagen de Item
	jQuery(document).ready(function ($) {
		var meta_image_frame;
	
		$('#table__banners').on('click', '.media-button', function (e) {
	
			var the_id = $(this).closest('tr').attr('id');
			// console.log(the_id);
	
			e.preventDefault();
	
			if (meta_image_frame) {
				meta_image_frame.open();
				return;
			}
	
			// Sets up the media library frame
			var meta_image_frame = wp.media.frames.meta_image_frame = wp.media({
				title: "Agregar Imagen",
				multiple: false,
				library: { type: 'image' }
			});
	
			var media_set_image = function media_set_image() {
				var selection = wp.media.frames.meta_image_frame.state().get('selection');
	
				if (!selection) {
					return;
				} // No selection
	
				// Iterate through selected elements
				selection.each(function (attachment) {
					var id = attachment.attributes.id;
					var thumbnail = attachment.attributes.sizes.thumbnail.url;
					$('#table__banners #' + the_id + ' .media-input').val(id);
					$('#table__banners #' + the_id + ' .media-button').css('display', 'none');
					$('#table__banners #' + the_id + ' .thumbnail_holder').html('<div class="reset">&#10005;</div><img width="100" src="' + thumbnail + '"><button class="button media-button" style="display:none;">Elegir Imagen</button>');
				});
			};
	
			wp.media.frames.meta_image_frame.on('close', media_set_image); // Closing event for media manger
			wp.media.frames.meta_image_frame.on('select', media_set_image); // Image selection event
			wp.media.frames.meta_image_frame.open(); // Showing media manager
		});
	});

/***/ }),
/* 5 */
/***/ (function(module, exports) {

	'use strict';
	
	Object.defineProperty(exports, "__esModule", {
		value: true
	});
	var $ = jQuery;
	//metaboxUploadInterface:: Obj config {
	//		WPLibraryOpts {String title, Bool multiple, Obj library}, //con defaults
	//		upload_button_selector,
	//		onSelectionForEach(attachment),
	//		afterSelectionForEach(selection), 
	//		onRemoveHandler(selection)
	//	} -> I/O
	//
	// OJO: onRemoveHandler corre dos veces (init y después de la seleccion) así que hay que cuidar de no hacer demasiados demasiados listeners... usar $(selector).off('click') antes del $(selector).on('click' para devincular y no duplicar listeners.
	var metaboxUploadInterface = exports.metaboxUploadInterface = function metaboxUploadInterface(config) {
	
		var library_opts = config.WPLibraryOpts || {};
	
		//setup
		var upload_button = $(config.upload_button_selector);
	
		var meta_image_frame = wp.media.frames.meta_image_frame = wp.media({
			title: library_opts.title || "Agregar Archivo",
			multiple: library_opts.multiple || false,
			library: library_opts.library || {}
		});
	
		var onSelection = function onSelection() {
			var selection = wp.media.frames.meta_image_frame.state().get('selection');
			if (!selection) {
				return;
			}
			selection.forEach(function (attachment) {
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
			upload_button.on('click', function () {
				meta_image_frame.open();
			});
	
			if (typeof config.onRemoveHandler === 'function') {
				config.onRemoveHandler();
			}
		}
	
		//register WPLibrary onSelection event handler
		wp.media.frames.meta_image_frame.on('select', onSelection); // Image selection event
	};
	
	var fileUploadConfig = exports.fileUploadConfig = {
		upload_button_selector: '.cltvo_upload_JS',
	
		onSelectionForEach: function onSelectionForEach(attachment) {
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
	
		onRemoveHandler: function onRemoveHandler() {
			var remove_button = $('.cltvo_remove_upload_JS');
			remove_button.off('click');
			remove_button.on('click', function () {
				remove_button.hide();
				$('.cltvo_file_id_input_JS').val('');
				$('.cltvo_filename_input_JS').val('');
				$('.fileUpload__success_JS').hide();
				$('.cltvo_upload_JS').show();
			});
		}
	};
	
	var defaultFileUploadConfig = exports.defaultFileUploadConfig = function defaultFileUploadConfig() {
		jQuery(window).load(function () {
			metaboxUploadInterface(fileUploadConfig);
		});
	};

/***/ })
/******/ ]);
//# sourceMappingURL=admin-functions.js.map