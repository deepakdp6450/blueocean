/**
 * Swatchly Frontend JS
 */
;( function ( $ ) {
	"use strict";

	if ( typeof swatchly_params === 'undefined' ) {
		return false;
	}

	/* Builder Compatibility Utilities
	======================================================= */
	var builderCompat = {
		isOxygen: function() {
			return Boolean(Number(swatchly_params.is_oxygen_builder));
		},

		findProductContainer: function($element) {
			// Try standard .product first
			var $container = $element.closest('.product');
			if ($container.length) {
				return $container;
			}

			// Oxygen Builder: look for ct-div-block with product-related class
			if (this.isOxygen()) {
				$container = $element.closest('.ct-div-block[class*="roduct"]');
				if ($container.length) {
					return $container;
				}
				$container = $element.closest('.ct-div-block');
				if ($container.length) {
					return $container;
				}
			}

			// Final fallback
			return $element.closest('.product');
		}
	};

	/* Tooltip JS
	======================================================= */
	function addTooltip(){
		var image_src = $(this).data('tooltip_image'),
			text = $(this).data('tooltip_text');

		if(image_src && text){
			$(this).append(`<div class="swatchly-tooltip"><span class="swatchly-tooltip-text">${text}</span><img src="${image_src}" /></div>`);
		} else if(image_src){
			$(this).append(`<div class="swatchly-tooltip"><img src="${image_src}" /></div>`);
		} else if(text){
			$(this).append(`<div class="swatchly-tooltip"><span class="swatchly-tooltip-text">${text}</span></div>`);
		}
	}

	function removeTooltip(e){
		$('.swatchly-tooltip').remove();
	}

	$(document).ready(function() {
		$(document).on( 'mouseover', 'div.swatchly-swatch,a.swatchly-swatch', addTooltip );
		$(document).on('mouseleave', 'div.swatchly-swatch,a.swatchly-swatch', removeTooltip);
	});
	
	/* For both Single & Product loop
	======================================================= */
	$(document).ready(function(){
		$('.swatchly-hide-this-variation-row').closest('tr').addClass('swatchly_d_none');
	});

	/* Product Loop JS
	======================================================= */
	var product_loop = {
	    /* 
		 * Some thems make the full product loop item linked to the product details. 
		 * Preventing the user from clicking on the product loop item so the swatches will be clickable.
		 */
		prevent_click: function(){
			$('.swatchly_loop_variation_form').on( 'click', function(e){
				if( $(e.target).is('.swatchly-more-button') || $(e.target).closest('.swatchly-more-button').length ){
					return;
				}
				
				e.preventDefault();
				e.stopPropagation();
				e.stopImmediatePropagation();
			});
		},
		init_variation_form: function(){
			var enable_swatches = Boolean(Number(swatchly_params.enable_swatches));

		    if(enable_swatches){
		        $( '.swatchly_loop_variation_form' ).swatchly_loop_variation_form();
		    }
		}
	}

	$(document).ready(function(){
		$('.swatchly_loop_variation_form').addClass('swatchly_loaded_on_ready');
		product_loop.prevent_click();
	});

	// Reset the changes
	$.fn.reset = function (event){
		$(this).find('.swatchly_ajax_add_to_cart').removeClass('alt disabled wc-variation-is-unavailable swatchly_found_variation added');

		if(event === 'click'){
			var $button_text = $(this).find('.swatchly_ajax_add_to_cart').data('select_options_text')
			$(this).find('.swatchly_ajax_add_to_cart').text($button_text);
		}

		// remove veiw cart button
		$(this).find('.added_to_cart.wc-forward').remove();
		$(this).remove_out_of_stock();

		// hide reset button
		$(this).find('.reset_variations').attr('style', 'display: none !important');
	};

	// Reset to the initial price
	$.fn.reset_to_default_price = function(){
		$(this).find('.price, .wc-block-components-product-price').first().removeClass('swatchly_d_none');
		$(this).find('.swatchly_price').remove();
	};

	// Don't show the out of stock element
	$.fn.remove_out_of_stock = function (){
		if($(this).find('.swatchly_pl.swatchly_out_of_stock')){
			$(this).find('.swatchly_pl.swatchly_out_of_stock').remove();
		}
	}

	// Find the product image selector
	$.fn.get_product_image_selector = function(){
		var $product_thumbnail = '',
			product_thumbnail_selector = swatchly_params.product_thumbnail_selector,
			$productContainer = builderCompat.findProductContainer($(this));

		// custom selector priority first
		if(product_thumbnail_selector){
			$product_thumbnail = $(this).find(product_thumbnail_selector);

			if( !$product_thumbnail.length && $productContainer.find(product_thumbnail_selector).length ){
				$product_thumbnail = $productContainer.find(product_thumbnail_selector);
			}

			return $product_thumbnail;
		}

		// Check if product has slider
		var has_slider = false;
		if($(this).find('.gallery-image').length){
			has_slider = true;
			return;
		}

		// look for default wc image selector
		$product_thumbnail = $(this).find('img.attachment-woocommerce_thumbnail');

		// ocean theme support
		if($product_thumbnail.length === 0){
			$product_thumbnail = $(this).find('img.woo-entry-image-main');

			// look for other default seletors
			if($product_thumbnail.length === 0){
				$product_thumbnail = $(this).find('img.attachment-woocommerce_thumbnail');

				if($product_thumbnail.length === 0){
					$product_thumbnail = $(this).find('img.wp-post-image');

					// Oxygen builder support
					if($product_thumbnail.length === 0 && builderCompat.isOxygen()){
						$product_thumbnail = $productContainer.find('.ct-image');
					}

					if($product_thumbnail.length === 0){
						$product_thumbnail = $productContainer.find('img').first();
					}
				}
			}
		}

		// Support WL Universal grid image slider
		if($(this).find('.ht-product-image-slider').length !== 0 ){
			$product_thumbnail = $(this).find('.slick-slide.ht-slider-first-item img');
		}

		return $product_thumbnail;
	};

	// Backup product image properties
	// Needed when reset variation & show the default image
	$.fn.backup_product_image = function(){
		var $product_thumbnail = $(this).get_product_image_selector();

		// If the product thumbail does not exists, do nothing
		if(!$product_thumbnail){
			return;   
		}

		// Clone & set default image's properties
		var backup_attributes = {
			"data-backup_alt": $product_thumbnail.attr('alt'),
			"data-backup_src": $product_thumbnail.attr('src'),
			"data-backup_width": $product_thumbnail.attr('width'),
			"data-backup_height": $product_thumbnail.attr('height')
		}

		if( $product_thumbnail.attr('srcset') ) {
		    backup_attributes["data-backup_srcset"] = $product_thumbnail.attr( 'srcset' );
		    backup_attributes["data-backup_sizes"] = $product_thumbnail.attr( 'sizes' );
		}

		$product_thumbnail.attr(backup_attributes);
	};

	// Change the product image when variation found
	$.fn.change_image = function(variation){
		// image selector
		var $product_thumbnail = $(this).get_product_image_selector();

		// If the product thumbail does not exists, do nothing
		if(!$product_thumbnail){
			return;   
		}

		var attributes = {
		    alt: variation.image.alt,
		    src: variation.image.thumb_src,
		    width: variation.image.thumb_src_w,
		    height: variation.image.thumb_src_h
		};

		if( $product_thumbnail.attr('srcset') ) {
		    attributes.srcset = variation.image.srcset;
		    attributes.sizes = variation.image.sizes;
		}

		// Finally change/update image
		$product_thumbnail.attr(attributes);

		// Support WL Universal grid image slider
		if( $(this).find('.ht-product-image-slider').length !== 0 ){
			$(this).find('.ht-product-image-slider').slick('slickGoTo', 0);
		}
	};

	// Reset to the default image when click on "reset" button
	$.fn.reset_to_default_image = function(){
		// Image selector
		var $product_thumbnail = $(this).get_product_image_selector();

		// Get backup attributes before reset
		var backup_attributes = {
			alt: $product_thumbnail.attr('data-backup_alt'),
			src: $product_thumbnail.attr('data-backup_src'),
			width: $product_thumbnail.attr('data-backup_width'),
			height: $product_thumbnail.attr('data-backup_height')
		}

		if( $product_thumbnail.attr('srcset') ) {
			backup_attributes["srcset"] = $product_thumbnail.attr( 'data-backup_srcset' );
			backup_attributes["sizes"]  = $product_thumbnail.attr( 'data-backup_sizes' );
		}

		// Finally reset the image
		$product_thumbnail.attr(backup_attributes);

		// Support WL Universal grid image slider
		if( $(this).find('.ht-product-image-slider').length !== 0 ){
			$(this).find('.ht-product-image-slider').slick('slickGoTo', 0);
		}
	};

	// Change add to cart button text
	$.fn.change_add_to_cart_text = function(){
		var original_cart_text = $(this).text(),
			new_cart_text = $(this).data('add_to_cart_text');

		$(this).html($(this).html().replace(original_cart_text, new_cart_text));
	};

	// Function for each loop variation form
	$.fn.swatchly_loop_variation_form = function(){
		// Classic themes use `.price`; block themes' Product Price block uses `.wc-block-components-product-price`.
		var $price_selector = '.price, .wc-block-components-product-price';

		return this.each( function(){
			var $el_variation_form = $( this ),
				$el_product = builderCompat.findProductContainer($el_variation_form),
				$el_ajax_add_to_cart = $el_product.find('.swatchly_ajax_add_to_cart');

			$el_product.backup_product_image();

			// hide reset button by default
			$el_product.find('.reset_variations').attr('style', 'display: none !important');

			$el_variation_form.on('found_variation', function(e, variation){
				$el_product.reset();

				var availability_html = variation.availability_html,
					is_in_stock = variation.is_in_stock;

				// If out of stcok
				if(!is_in_stock){
					$(this).append(`<div class="swatchly_pl swatchly_out_of_stock">${availability_html}</div>`);
				} else {
					$el_product.remove_out_of_stock();
				}

				// show reset buton once a vaiation found
				$el_product.find('.reset_variations').attr('style', '');

				// Update price
				if( !$el_product.find('.swatchly_price').length ){
					if($(variation.price_html).length){
						$el_product.find($price_selector).addClass('swatchly_d_none').after( $(variation.price_html).addClass('swatchly_price') );
					}
				} else {
					$el_product.find('.swatchly_price').remove();
					$el_product.find($price_selector).addClass('swatchly_d_none').after($(variation.price_html).addClass('swatchly_price'));
				}

				// Update cart text
				if( $el_ajax_add_to_cart.length ){
					$el_ajax_add_to_cart.change_add_to_cart_text('found_variation');
				}

				// Update Image
				$el_product.change_image(variation);

				// For ajax add to cart
				// Manually generate selected variation attributes
				var selected_variation = {},
					variations = $(this).find( 'select[name^=attribute]' );
				if ( !variations.length) {
				    variations = $(this).find( '[name^=attribute]:checked' );
				}
				if ( !variations.length) {
				    variations = $(this).find( 'input[name^=attribute]' );
				}

				variations.each( function() {
				    var $this_item = $( this ),
				        attribute_name = $this_item.attr( 'name' ),
				        attribute_value = $this_item.val(),
				        index,
				        attribute_tax_name;
				        $this_item.removeClass( 'error' );
				    if ( attribute_value.length === 0 ) {
				        index = attribute_name.lastIndexOf( '_' );
				        attribute_tax_name = attribute_name.substring( index + 1 );
				        $this_item.addClass( 'required error' );
				    } else {
				        selected_variation[attribute_name] = attribute_value;
				    }
				});

				if($el_ajax_add_to_cart.length){
					$el_ajax_add_to_cart.addClass('swatchly_found_variation');
				}
				$el_ajax_add_to_cart.attr('data-variation_id', variation.variation_id);
				$el_ajax_add_to_cart.attr('data-variation', JSON.stringify(selected_variation));

				if($el_ajax_add_to_cart.hasClass('added')){
					$el_ajax_add_to_cart.removeClass('added');
				}

			}).on('click','.reset_variations', function(e){
				$el_product.reset('click');
				$el_product.remove_out_of_stock();
				$el_product.reset_to_default_price();
				$el_product.reset_to_default_image();

			});
		});
	};

	// Product loop init
	$(window).on('load', function(){
	    product_loop.init_variation_form();
	} );

	/*=====  End of Product Loop JS  ======*/
	

	/* Single product page JS
	======================================================= */
	var single_product = {
	    init: function(){
			var enable_swatches           	  = Boolean(Number(swatchly_params.enable_swatches)),
				sp_override_global            = Boolean(Number(swatchly_params.sp_override_global)),
				pl_override_global            = Boolean(Number(swatchly_params.pl_override_global)),
				enable_variation_url          = Boolean(Number(swatchly_params.enable_variation_url)),
				enable_rv_variations          = Boolean(Number(swatchly_params.enable_rv_variations)),
				enable_sp_variation_url       = Boolean(Number(swatchly_params.enable_sp_variation_url)),
				enable_sp_rv_variations       = Boolean(Number(swatchly_params.enable_sp_rv_variations)),
				enable_pl_variation_url       = Boolean(Number(swatchly_params.enable_pl_variation_url)),
				enable_pl_rv_variations       = Boolean(Number(swatchly_params.enable_pl_rv_variations)),
				is_product            		  = Boolean(Number(swatchly_params.is_product)),
				deselect_on_click             = Boolean(Number(swatchly_params.deselect_on_click)),
				show_selected_attribute_name  = Boolean(Number(swatchly_params.show_selected_attribute_name)),
				variation_label_separator     = swatchly_params.variation_label_separator;

			const rv_variations_config = {
				storage_key: 'swatchly_rv_variations',
			};

			const loopVariationUrlParams = ({event, value, paramName}) => {
				const product = builderCompat.findProductContainer($(event.target))[0];
				if (!product) return;

				// Get the product URL from data attribute (theme-agnostic approach)
				const variationForm = product.querySelector('.swatchly_loop_variation_form') ||
					product.querySelector('.variations_form') ||
					event.target.closest('.swatchly_loop_variation_form');
				const productUrlFromData = variationForm ? variationForm.getAttribute('data-product_url') : null;

				// Fallback to selector-based approach for backwards compatibility
				const productLink = product.querySelector('.woocommerce-loop-product__link') ||
					product.querySelector('.bundled_product_permalink') ||
					product.querySelector('a[href*="/product/"]') ||
					product.querySelector('a');

				// Determine the product URL pathname
				let productUrlPathname = null;
				if (productUrlFromData) {
					try {
						productUrlPathname = new URL(productUrlFromData).pathname;
					} catch (e) {
						// Invalid URL from data attribute, will use fallback
					}
				}
				if (!productUrlPathname && productLink) {
					try {
						productUrlPathname = new URL(productLink.href).pathname;
					} catch (e) {
						// Invalid URL from link
					}
				}

				const links = product.querySelectorAll('a');
				Array.from(links).forEach(link => {
					if(!$(link)?.attr('href')) return;
					try {
						const url = new URL(link.href);
						// Match links by comparing pathname OR if link contains /product/ (backwards compat)
						const shouldUpdate = productUrlPathname
							? (url.pathname === productUrlPathname)
							: (url.href.indexOf('/product/') !== -1);

						if(shouldUpdate) {
							if(value) {
								if(!url.searchParams.get(paramName)) {
									url.searchParams.append(paramName, value);
								} else {
									url.searchParams.set(paramName, value)
								}
							} else {
								url.searchParams.delete(paramName)
							}
							link.href = url.toString();
						}
					} catch (e) {
						// Ignore invalid URLs
					}
				});
			},
			singleVariationUrlParams = ({value, paramName}) => {
				const url = new URL(location);
				if(value) {
					if(!url.searchParams.get(paramName)) {
						url.searchParams.append(paramName, value);
					} else {
						url.searchParams.set(paramName, value)
					}
				} else {
					url.searchParams.delete(paramName)
				}
				history.pushState({}, '', url.href);
			};

			/**
			 * Recently Viewed Variations
			 * Update selected variations from local storage
			 */
			const updateRecentlyViewedVariations = ($el_variation_form) => {
				const rv_variations = localStorage.getItem(rv_variations_config?.storage_key);
				if(rv_variations){
					const parsed_rv_variations = JSON.parse(rv_variations);
					if(parsed_rv_variations[$el_variation_form.data('product_id')]){
						const selected_variations = parsed_rv_variations[$el_variation_form.data('product_id')];
						$el_variation_form.find('select').each(function(){
							const $el_default_select = $(this);
							const product_attributes = $el_default_select[0].name;
							if(selected_variations[product_attributes]){
								$el_default_select.val(selected_variations[product_attributes]);
								$el_default_select.change();
								const swatchly_swatch = $el_default_select.closest('td.value').find('.swatchly-swatch[data-attr_value="' + selected_variations[product_attributes] + '"]');
								if(swatchly_swatch.length){
									swatchly_swatch.addClass('swatchly-selected').siblings('div.swatchly-swatch').removeClass('swatchly-selected');
								}
							}
						});
					}
				}
			};

			if( enable_swatches){
				$.fn.swatchly_variation_form = function(){
					return this.each( function(){
						var $el_variation_form = $( this );

						/**
						 * Recently Viewed Variations
						 * Update selected variations from local storage
						 */
						// Global setting (when overrides are not active)
						if(enable_rv_variations && !sp_override_global && !pl_override_global){
							updateRecentlyViewedVariations($el_variation_form);
						} 
						// Single product page with override active
						else if(enable_sp_rv_variations && is_product && sp_override_global){
							updateRecentlyViewedVariations($el_variation_form);
						} 
						// Product listing page with override active
						else if(enable_pl_rv_variations && !is_product && pl_override_global){
							updateRecentlyViewedVariations($el_variation_form);
						}

						// Actions while select a swatch
						$el_variation_form.on( 'click', 'div.swatchly-swatch', function ( e ) {
							var $el_swatch = $( this ),
								$el_default_select = $el_swatch.closest( '.value' ).find( 'select' ),
								value   = $el_swatch.attr( 'data-attr_value' );
								
							if( !deselect_on_click ){
								// Add selected class & remove siblings selected class
								$el_swatch.addClass('swatchly-selected').siblings('div.swatchly-swatch').removeClass('swatchly-selected');

								// Show selected variation name
								if( show_selected_attribute_name ){
									if($el_swatch.closest('tr').find('.swatchly_selected_variation_name').length){
										$el_swatch.closest('tr').find('.swatchly_selected_variation_name').text( variation_label_separator + $el_swatch.data('attr_label') );
									} else {
										$el_swatch.closest('tr').find('.label label').append('<span class="swatchly_selected_variation_name">'+ variation_label_separator + $el_swatch.data('attr_label') +'</span>');
									}
								}

							} else {

								if($el_swatch.hasClass('swatchly-selected')){

									// Remove selection
									$el_swatch.removeClass('swatchly-selected');

									// Change select field value to empty
									value = '';

									// Remove selected variation name
									$el_swatch.closest('tr').find('.swatchly_selected_variation_name').text( '' );

								} else {

									// Add selected class & remove siblings selected class
									$el_swatch.addClass('swatchly-selected').siblings('div.swatchly-swatch').removeClass('swatchly-selected');

									// Show selected variation name
									if( show_selected_attribute_name ){
										if($el_swatch.closest('tr').find('.swatchly_selected_variation_name').length){
											$el_swatch.closest('tr').find('.swatchly_selected_variation_name').text( variation_label_separator + $el_swatch.data('attr_label') );
										} else {
											$el_swatch.closest('tr').find('.label label').append('<span class="swatchly_selected_variation_name">'+ variation_label_separator + $el_swatch.data('attr_label') +'</span>');
										}
									}

								}
							}

							/**
							 * Recently Viewed Variations
							 * Save selected variations in local storage
							 */
							if((enable_rv_variations && !sp_override_global && !pl_override_global) ||
							(enable_sp_rv_variations && is_product && sp_override_global) ||
							(enable_pl_rv_variations && !is_product && pl_override_global)){
								const product_attributes = $el_default_select[0].name;
								const product_id = $el_variation_form.data('product_id');
								const rv_variations = localStorage.getItem(rv_variations_config?.storage_key);
								if(rv_variations){
									const parsed_rv_variations = JSON.parse(rv_variations);
									if(parsed_rv_variations[product_id]){
										const selected_variations = parsed_rv_variations[product_id];
										localStorage.setItem(rv_variations_config?.storage_key, JSON.stringify({
											...parsed_rv_variations,
											[product_id]: {
												...selected_variations,
												[product_attributes]: value
											}
										}));
									} else {
										localStorage.setItem(rv_variations_config?.storage_key, JSON.stringify({
											...parsed_rv_variations,
											[product_id]: {
												[product_attributes]: value
											}
										}));
									}
								} else {
									localStorage.setItem(rv_variations_config?.storage_key, JSON.stringify({
										[product_id]: {
											[product_attributes]: value
										}
									}));
								}
							}

							$el_default_select.val(value);
							$el_default_select.change();
							
						})
						.on('change', '.value select', function(e) {
							const QUICKVIEW_SELECTORS = [
								'.woosq-popup',
								'.qqvfw',
								'.acoqvw_quickview',
								'.quickviewwoo-product',
								'.adfy-quick-view-modal-content',
								'.swal2-content',
								'.merchant-quick-view-content',
								'.cawqv-modal',
								'.xoo-qv-modal',
								'.woolentor-quickview-modal',
								'#wcqv_contend',
								'.yith-quick-view',
								'.quickswish-modal',
								'.wd-popup',
							];

							const element = $(this)[0];
							const paramName = element.dataset.attribute_name;
							const value = element.value;

							// Handle product loop variation URL
							if ((pl_override_global && enable_pl_variation_url) || (!pl_override_global && enable_variation_url)) {
								const isInProductLoop = e.target.closest('.products') && !e.target.closest('.summary');
								const isBundleProduct = e.target.closest('.bundled_product');
								// Fallback: check if inside swatchly_loop_variation_form (works regardless of theme structure)
								const isInSwatchlyLoopForm = e.target.closest('.swatchly_loop_variation_form') && !e.target.closest('.summary');

								if ((isInProductLoop || isBundleProduct || isInSwatchlyLoopForm) && !e.target.closest(QUICKVIEW_SELECTORS.join(', '))) {
									loopVariationUrlParams({
										event: e,
										value,
										paramName,
									});
								}
							}

							// Handle single product variation URL
							if ((sp_override_global && enable_sp_variation_url) || (!sp_override_global && enable_variation_url)) {
								const isInSingleProduct = is_product && e.target.closest('.summary');
								if (isInSingleProduct && !e.target.closest([...QUICKVIEW_SELECTORS, '.bundle_form'].join(', '))) {
									singleVariationUrlParams({
										value,
										paramName,
									});
								}
							}
						})
						.on( 'woocommerce_update_variation_values', function() {
							setTimeout( function() {

								// Loop through each variation row
								$el_variation_form.find( 'tbody tr' ).each( function() {
									var $tr = $(this),
									values = [];

									// Set default attribute label
									if( show_selected_attribute_name && !$tr.find('.swatchly_selected_variation_name').length ){
										var default_attr_label = $tr.find('.swatchly-type-wrap').attr('data-default_attr_value');

										if(default_attr_label){
											$tr.find('.label label').append('<span class="swatchly_selected_variation_name">'+ variation_label_separator + default_attr_label +'</span>');
										} else {
											$tr.find('.label label').append('<span class="swatchly_selected_variation_name"></span>');
										}
										
									}

									var $select = $tr.find('select');
									var attribute_name = $select.data('attribute_name') || $select.attr('name');
									var variationData = $el_variation_form.data('product_variations');

									// Collect chosen values of OTHER attributes so we respect multi-attribute constraints
									var chosenAttributes = {};
									$el_variation_form.find('.variations select').each(function(){
										var $s = $(this),
											name = $s.data('attribute_name') || $s.attr('name'),
											val = $s.val() || '';
										if( name && name !== attribute_name ){
											chosenAttributes[ name ] = val;
										}
									});

									// Primary path: consult product_variations JSON for stock/purchasability
									if( Array.isArray(variationData) && variationData.length ){
										$select.find('option').each(function(index, option){
											if( option.value === '' ){ return; }
											var value = option.value;
											var hasAvailable = false;
											for( var i = 0; i < variationData.length; i++ ){
												var v = variationData[i];
												if( !v || !v.attributes ){ continue; }
												if( !v.is_in_stock || !v.is_purchasable ){ continue; }
												// Variation's value for THIS attribute ('' means "Any")
												var vAttrVal = v.attributes[ attribute_name ];
												if( vAttrVal !== '' && vAttrVal !== value ){ continue; }
												// Other chosen attributes must be compatible
												var mismatch = false;
												for( var otherName in chosenAttributes ){
													var chosen = chosenAttributes[ otherName ];
													if( chosen === '' ){ continue; }
													var otherVal = v.attributes[ otherName ];
													if( otherVal !== '' && otherVal !== chosen ){ mismatch = true; break; }
												}
												if( !mismatch ){ hasAvailable = true; break; }
											}
											if( hasAvailable ){
												values.push( value );
											}
										});
									} else {
										// Fallback (e.g. AJAX variation mode): use the select's current options,
										// excluding ones WooCommerce has disabled.
										$select.find('option:not(:disabled)').each(function(index, option){
											if( option.value !== '' ){
												values.push( option.value );
											}
										});

										// Secondary fallback: if select has no options (stripped by another plugin),
										// read from the stored attribute_html reference set.
										if( values.length === 0 ){
											var attrHtml = $select.data('attribute_html');
											if( attrHtml ){
												$(attrHtml).filter('option').each(function(index, option){
													if( option.value !== '' ){
														values.push( option.value );
													}
												});
											}
										}
									}

									// If values is STILL empty, don't disable anything -
									// showing all enabled is better than all disabled
									if( values.length === 0 ){
										$tr.find( 'div.swatchly-swatch' ).removeClass('swatchly-disabled');
									} else {
										// Disable unavailable swatches
										$tr.find( 'div.swatchly-swatch' ).each( function() {
											var $el_swatch = $( this ),
												value = $el_swatch.attr( 'data-attr_value' );

											if( values.indexOf( value ) == -1 ){
												$el_swatch.addClass('swatchly-disabled');
											} else {
												$el_swatch.removeClass('swatchly-disabled');
											}
										});
									}

								}); // tbody tr each
							}, 100 ); // timeout

							// Update price for product loop
							var $price_selector = '.price, .wc-block-components-product-price',
								$el_product = builderCompat.findProductContainer($el_variation_form);
							if(!swatchly_params.is_product){
								if($el_product.find('.swatchly_price').length){
									$el_product.find($price_selector).removeClass('swatchly_d_none');
									$el_product.find('.swatchly_price').remove();
								}
							}
						})
						.on('found_variation', function(e, variation){
							// some user use single product add to cart button in the product loop
							// so we need to change the image for the product loop
							if( !swatchly_params.is_product ){
								var $product_thumbnail = $(this).get_product_image_selector();

								let $el_product = builderCompat.findProductContainer($(this));
								$el_product.backup_product_image();
	
								$product_thumbnail.attr('src', variation.image.url);
								$product_thumbnail.attr('srcset', variation.image.srcset);
							}
						})
						.on( 'click', '.reset_variations', function () {
							const url = new URL(location);
							$el_variation_form.find( '.value select' ).each(function() {
								url.searchParams.delete($(this)[0].name);
							})
							history.pushState({}, '', url.href);
							$el_variation_form.find( '.swatchly-selected' ).removeClass( 'swatchly-selected' );
							$el_variation_form.find( '.swatchly-disabled' ).removeClass( 'swatchly-disabled' );
							$el_variation_form.find('.swatchly_selected_variation_name').text( '' );

							// some user use single product add to cart button in the product loop
							// so we need to reset the image for the product loop
							if( !swatchly_params.is_product ){
								let $el_product = builderCompat.findProductContainer($(this));
								$el_product.reset_to_default_image()
							}

							/**
							 * Recently Viewed Variations
							 * reset selected variations from local storage
							 */
							if((enable_rv_variations && !sp_override_global && !pl_override_global) ||
							(enable_sp_rv_variations && is_product && sp_override_global) ||
							(enable_pl_rv_variations && !is_product && pl_override_global)){
								const rv_variations = localStorage.getItem(rv_variations_config?.storage_key);
								if(rv_variations){
									const parsed_rv_variations = JSON.parse(rv_variations);
									if(parsed_rv_variations[$el_variation_form.data('product_id')]){
										delete parsed_rv_variations[$el_variation_form.data('product_id')];
										localStorage.setItem(rv_variations_config?.storage_key, JSON.stringify(parsed_rv_variations));
									}
								}
							}
						}); // on click div.swatchly-swatch
					});
				}

				const initVariationForms = () => {
					const variationForm = $('.variations_form:not(.swatchly_initialized):not(.bundled_item_cart_content)');
					const bundleForm = $('.bundle_form:not(.swatchly_initialized)');
					if(variationForm.length) {
						variationForm
							.addClass('swatchly_variation_form swatchly_initialized')
							.swatchly_variation_form();
					}
					if(bundleForm.length) {
						bundleForm
							.addClass('swatchly_variation_form swatchly_initialized')
							.swatchly_variation_form();
					}
				};

				// Do stuffs for each variations form
				$(document).ready(initVariationForms)

				// All major quick view plugin support
				const QUICKVIEW_KEYWORDS = [
					'woosq_quickview',
					'quickswish_product',
					'wqv_popup_content',
					'acoqvw_get_quickview',
					'qode-quick-view-for-woocommerce',
					'quickviewwoo',
					'ca-quick-view',
					'xoo_qv_ajax',
					'wcqv_get_product',
				];
				$(document).ajaxComplete(function (event, request, settings) {
					if(QUICKVIEW_KEYWORDS.some(word => settings?.url?.includes(word) || settings?.data?.includes(word))) {
						initVariationForms();
					}
				});
				const QUICKVIEW_EVENTS = [
					'addonifyQuickViewModalContentLoaded',
					'gpls-arcw-quick-view-buy-now-for-woocommerce-quick-view-popup-rendered',
					'yith_quick_view_loaded',
					'merchant.quickview.ajax.loaded',
					'wdQuickViewOpen',
				];
				// Handle event-based quick view plugins
				QUICKVIEW_EVENTS.forEach(eventName => {
					const target = eventName.startsWith('merchant') ? window : document;
					$(target).on(eventName, initVariationForms);
				});
			}
		}
	}

	// Single product init
	single_product.init();
	

	/* Third party plugin/theme's compatibility
	======================================================= */

	/**
	 * 1. annasta Woocommerce Product Filters
	 */
	 $( document ).on( 'awf_after_ajax_products_update', function() {
		product_loop.prevent_click();
		product_loop.init_variation_form();
		single_product.init();
	});

	/**
	 * 2. Jet Smart Filters
	 */
	 $( document ).on( 'jet-filter-content-rendered', function() {
		product_loop.prevent_click();
		product_loop.init_variation_form();
		single_product.init();
	});

	/**
	 * 3. Woolentor Support
	 */
	$( document ).on('woolentor_quick_view_rendered', function(){
		product_loop.prevent_click();
		product_loop.init_variation_form();
		single_product.init();
	});

	/**
	 * 4. infiniteScroll Support
	 * It's oly work under document.ready and on 'append.infiniteScroll'
	 */
	$(document).ready(function(){
		$('.products').on('append.infiniteScroll', function(){
			product_loop.prevent_click();
			product_loop.init_variation_form();
			single_product.init();
		});
	});

	/**
	 * 5. YITH infiniteScroll Support
	 */
	$(document).on('yith_infs_added_elem', function(){
		product_loop.prevent_click();
		product_loop.init_variation_form();
		single_product.init();
	});

	/**
	 * 6. Barn2 WCF Product Filter
	 */
	$(window).on('load', function(){
		if( $('.wcf-filter-form').length && typeof window.wp.hooks.addAction !== 'undefined' ){
			window.wp.hooks.addAction( 'wcpf.filteringDone', 'namespace', function( config, filteredDOM ){
				product_loop.prevent_click();
				product_loop.init_variation_form();
				single_product.init();
			} );
		}
	});

	/**
	 * 7. Astra Pro quick view Support
	 */
	$( document ).on('ast_quick_view_loader_stop', function(){
		product_loop.prevent_click();
		product_loop.init_variation_form();
		single_product.init();
	});

	/**
	 * Themes compatibility
	 * 1. Airi theme
	 * It is intended to add compatibility for the airi theme's infinite scroll
	 */
	$( document ).on('ajaxComplete', function (event, jqxhr, settings) {
		$('.swatchly_loop_variation_form:not(.swatchly_loaded_on_ready)').each(function(){
			product_loop.prevent_click();
			product_loop.init_variation_form();
			single_product.init();
		});
	});

	/**
	 * 8. Block-theme Query Loop pagination (WP Interactivity API) + generic fetch-based DOM replacement.
	 *    jQuery's ajaxComplete does not fire for native fetch(), so new swatches on paginated
	 *    pages never get initialized. Watch the DOM for added variation/loop forms and reinit.
	 */
	if ( typeof window.MutationObserver !== 'undefined' ) {
		var swatchlyReinitScheduled = false;
		var swatchlyReinit = function() {
			swatchlyReinitScheduled = false;
			if ( ! document.querySelector(
				'.variations_form:not(.swatchly_initialized), .swatchly_loop_variation_form:not(.swatchly_loaded_on_ready), .variations_form:not(.swatchly_wc_initialized)'
			) ) {
				return;
			}
			try {
				// Initialize WooCommerce's own VariationForm on any new .variations_form
				// (needed for native-fetch pagination since ajaxComplete does not fire).
				if ( typeof window.wc_add_to_cart_variation_params !== 'undefined' ) {
					$( '.variations_form:not(.swatchly_wc_initialized)' ).each( function() {
						$( this ).addClass( 'swatchly_wc_initialized' ).wc_variation_form();
					});
				}
				product_loop.prevent_click();
				product_loop.init_variation_form();
				single_product.init();
			} catch ( e ) { /* noop */ }
		};
		var swatchlyScheduleReinit = function() {
			if ( swatchlyReinitScheduled ) { return; }
			swatchlyReinitScheduled = true;
			( window.requestAnimationFrame || function( fn ){ setTimeout( fn, 16 ); } )( swatchlyReinit );
		};
		var swatchlyDomObserver = new MutationObserver( function( mutations ) {
			for ( var i = 0; i < mutations.length; i++ ) {
				var added = mutations[ i ].addedNodes;
				if ( ! added || ! added.length ) { continue; }
				for ( var j = 0; j < added.length; j++ ) {
					var node = added[ j ];
					if ( node.nodeType !== 1 ) { continue; }
					if (
						( node.matches && node.matches( '.variations_form, .swatchly_loop_variation_form' ) ) ||
						( node.querySelector && node.querySelector( '.variations_form, .swatchly_loop_variation_form' ) )
					) {
						swatchlyScheduleReinit();
						return;
					}
				}
			}
		});
		$( document ).ready( function() {
			if ( document.body ) {
				swatchlyDomObserver.observe( document.body, { childList: true, subtree: true } );
			}
		});
	}
})(jQuery);