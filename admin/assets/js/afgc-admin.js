jQuery(document).ready(function($) {
	
	var inputField = $('#afgc_gift_card_amnt');
	
	$('#afgc_special_discount_amount').on('keypress', function(event) {
		var charCode = event.which;

		// Allow only alphanumeric characters
		if (charCode > 31 && (charCode < 48 || charCode > 57) && (charCode < 65 || charCode > 90) && (charCode < 97 || charCode > 122)) {
			event.preventDefault();
		}
	});

	var ajaxurl = k_php_var.admin_url;

	var nonce = k_php_var.nonce;

	// Coupon Code Info Fields readonly.
	$("#afgc_shortcodes").attr('readonly','readonly');


	// Select Product.
	$('.afgc_select_product').select2({

		ajax: {

			url: ajaxurl,

			dataType: 'json',

			type: 'POST',

			delay: 250,

			data: function (params) {

				return {

					q: params.term,

					action: 'afgc_select_products',

					nonce: nonce

				};

			},

			processResults: function( data ) {

				var options = [];

				if ( data ) {
					
					$.each( data, function( index, text ) { 

						options.push( { id: text[0], text: text[1]  } );

					});
				}

				return {

					results: options

				};

			},

			cache: true

		},

		multiple: true,

		placeholder: 'Choose Products',

		minimumInputLength: 3

	});



	// Select Categories.
	$('.afgc-category-search').select2({

		ajax: {
			url: ajaxurl,

			dataType: 'json',

			type: 'POST',

			delay: 250,

			data: function (params) {

				return {

					q: params.term,

					action: 'afgc_category_search',

					nonce: nonce,

				};

			},

			processResults: function( data ) {

				var options = [];

				if ( data ) {

					$.each( data, function( index, text ) { 

						options.push( { id: text[0], text: text[1]  } );

					});

				}
				return {

					results: options

				};

			},

			cache: true

		},

		multiple: true,

		placeholder: 'Choose Categories',

		minimumInputLength: 3
		   
	});


	// Select User.
	$('.afgc-user-role-search').select2({

		multiple: true,

		placeholder: 'Choose User',

		minimumInputLength: 3
		   
	});


	// User Role Meta Box Script on Select All
	$("#afgc_all_user_roles").change(function() {
		if (this.checked) {
			$(".afgc_user_roles").each(function() {
				this.checked =true;
			});
		} else {
			$(".afgc_user_roles").each(function() {
				this.checked =false;
			});
		}
	});


	// Enable customize gift card theme.
	$( 'input#afgc_customize_theme' ).on( 'change', function() {

		if ( $( this ).is( ':checked' ) ) {

			$( '#afgc_pick_theme_color' ).closest( 'tr' ).show('slow');

			$( '#afgc_pick_text_color' ).closest( 'tr' ).show('slow');

			$( '#afgc_pick_border_color' ).closest( 'tr' ).show('slow');

			$( '#afgc_hover_theme_color' ).closest( 'tr' ).show('slow');

			$( '#afgc_hover_border_color' ).closest( 'tr' ).show('slow');

			$( '#afgc_hover_text_color' ).closest( 'tr' ).show('slow');

		} else {

			$( '#afgc_pick_theme_color' ).closest( 'tr' ).hide('slow');

			$( '#afgc_pick_text_color' ).closest( 'tr' ).hide('slow');

			$( '#afgc_pick_border_color' ).closest( 'tr' ).hide('slow');

			$( '#afgc_hover_theme_color' ).closest( 'tr' ).hide('slow');

			$( '#afgc_hover_border_color' ).closest( 'tr' ).hide('slow');

			$( '#afgc_hover_text_color' ).closest( 'tr' ).hide('slow');

		}

	}).trigger( 'change' );


	// Enter a custom text in the email template.
	$( 'input#afgc_custom_text_email_temp' ).on( 'change', function() {

		if ( $( this ).is( ':checked' ) ) {

			$( '#afgc_custom_text_email' ).closest( 'tr' ).show('slow');

		} else {

			$( '#afgc_custom_text_email' ).closest( 'tr' ).hide('slow');

		}
	}).trigger( 'change' );


	// Another Recipient option.
	$( 'input#afgc_another_recp' ).on( 'change', function() {

		if ( $( this ).is( ':checked' ) ) {

			$( '#afgc_another_recp_title' ).closest( 'tr' ).show('slow');

		} else {

			$( '#afgc_another_recp_title' ).closest( 'tr' ).hide('slow');

		}
	}).trigger( 'change' );
	

	// Ask sender name.
	$( 'input#afgc_sender_name' ).on( 'change', function() {

		if ( $( this ).is( ':checked' ) ) {

			$( '#afgc_sender_info_section_title' ).closest( 'tr' ).show('slow');

		} else {

			$( '#afgc_sender_info_section_title' ).closest( 'tr' ).hide('slow');

		}
	}).trigger( 'change' );


	// Enable Gift Cards Gallery.
	$( 'input#afgc_enable_card_gallery' ).on( 'change', function() {

		if ( $( this ).is( ':checked' ) ) {

			$( '#afgc_card_gallery_section_title' ).closest( 'tr' ).show('slow');

		} else {

			$( '#afgc_card_gallery_section_title' ).closest( 'tr' ).hide();

		}
	}).trigger( 'change' );



	// Allow gift card codes in WooCommerce coupon fields.
	$( 'input#afgc_card_codes_Woo_coupon_fields' ).on( 'change', function() {

		if ( $( this ).is( ':checked' ) ) {

			$( '#afgc_apply_coupon_btn_text' ).closest( 'tr' ).show('slow');

			$( '#afgc_coupon_title_in_cart' ).closest( 'tr' ).show('slow');

		} else {

			$( '#afgc_apply_coupon_btn_text' ).closest( 'tr' ).hide('slow');

			$( '#afgc_coupon_title_in_cart' ).closest( 'tr' ).hide('slow');

		}
	}).trigger( 'change' );


	// Gift Card Field On Checkout Page.
	$( 'input#afgc_enable_gift_this_pro' ).on( 'change', function() {

		if ( $( this ).is( ':checked' ) ) {

			$( '#afgc_include_shipping_cost' ).closest( 'tr' ).show('slow');

			$( '#afgc_label_style' ).closest( 'tr' ).show('slow');

			$( '#afgc_des_before_button' ).closest( 'tr' ).show('slow');

		} else {

			$( '#afgc_include_shipping_cost' ).closest( 'tr' ).hide('slow');

			$( '#afgc_label_style' ).closest( 'tr' ).hide('slow');

			$( '#afgc_des_before_button' ).closest( 'tr' ).hide('slow');

		}
	}).trigger( 'change' );


	// Allow Min & Max Custom Overide Amounts settings.
	$( 'input#afgc_allow_overide_custom_amount' ).on( 'change', function() {
		adf_gfd_custom_amount();
	});

	adf_gfd_custom_amount();

	function adf_gfd_custom_amount(){
		if ( $( 'input#afgc_allow_overide_custom_amount' ).prop( 'checked' ) ) {

			$( '#afgc_allow_overide_min_custom_amount' ).closest( '.form-field' ).show('slow');

			$( '#afgc_allow_overide_max_custom_amount' ).closest( '.form-field' ).show('slow');

			$( '#afgc_special_discount_type' ).closest( '.afgc_special_discount_type_field' ).hide('slow');

			$( '#afgc_special_discount_amount' ).closest( '.afgc_special_discount_amount_field' ).hide('slow');
			
		} else {

			$( '#afgc_allow_overide_min_custom_amount' ).closest( '.form-field' ).hide('slow');

			$( '#afgc_allow_overide_max_custom_amount' ).closest( '.form-field' ).hide('slow');

			$( '#afgc_special_discount_type' ).closest( '.afgc_special_discount_type_field' ).show('slow');

			$( '#afgc_special_discount_amount' ).closest( '.afgc_special_discount_amount_field' ).show('slow');

		}
	}


	// Virtual.
	$( 'input#afgc_virtual' ).on( 'change', function() {

		$( '#afgc_allow_custom_img' ).closest( '.form-field' ).show('slow');

		if ( $( this ).is( ':checked' ) ) {

			$('.shipping_tab').addClass('hide_if_afgc_virtual');
			
			$( '#afgc_allow_custom_img' ).closest( '.form-field' ).show('slow');

			$( '#afgc_product_as_gift').closest('.afgc-product-as-gift-card').show('slow');

			$( 'input#afgc_allow_custom_img' ).on( 'change', function() {

				if ( $( this ).is( ':checked' ) ) {

					$( '#afgc_enable_custom_image_btn' ).closest( '.form-field ' ).show('slow');

				} else {

					$( '#afgc_enable_custom_image_btn' ).closest( '.form-field ' ).hide('slow');

				}
			}).trigger( 'change' );

		} else {

			$('.shipping_tab').removeClass('hide_if_afgc_virtual');
			$( '#afgc_allow_custom_img' ).closest( '.form-field' ).hide('slow');
			$( '#afgc_enable_custom_image_btn' ).closest( '.form-field ' ).hide('slow');
			$( '#afgc_product_as_gift').closest('.afgc-product-as-gift-card').hide('slow');

		}
		
	}).trigger( 'change' );


	// Enable | Disable Store Name on PDF.
	$( 'input#afgc_enable_store_name_pdf' ).on( 'change', function() {
		if ( $( this ).is( ':checked' ) ) {
			$( '#afgc_store_name_pdf' ).closest( 'tr' ).show('slow');
		} else {
			$( '#afgc_store_name_pdf' ).closest( 'tr' ).hide('slow');
		}
	}).trigger( 'change' );

	$( 'input#afgc_enable_store_logo_pdf' ).on( 'change', function() {
		if ( $( this ).is( ':checked' ) ) {
			$( '#afgc_store_logo' ).closest( 'tr' ).show('slow');
		} else {
			$( '#afgc_store_logo' ).closest( 'tr' ).hide('slow');
		}
	}).trigger( 'change' );

	// Enable|Disable Message on PDF.
	$( 'input#afgc_enable_gift_card_message' ).on( 'change', function() {

		if ( $( this ).is( ':checked' ) ) {

			$( '#afgc_gift_card_message' ).closest( 'tr' ).show('slow');

		} else {

			$( '#afgc_gift_card_message' ).closest( 'tr' ).hide('slow');

		}
	}).trigger( 'change' );


	// Enable|Disable Disclaimer on PDF.
	$( 'input#afgc_enable_disclaimer' ).on( 'change', function() {

		if ( $( this ).is( ':checked' ) ) {

			$( '#afgc_disclaimer_text' ).closest( 'tr' ).show('slow');

		} else {

			$( '#afgc_disclaimer_text' ).closest( 'tr' ).hide('slow');

		}
	}).trigger( 'change' );

	

	/*=======================================*/

	/*======== Checked Virtual Option ======*/

	/*=====================================*/


	$('#_virtual').change(function () {

		adf_virual_checbox_sec();
	});
	adf_virual_checbox_sec();
	$('#_virtual').trigger('change');

	$('#afgc_virtual').change(function () {
		adf_virual_checbox();
	});
	adf_virual_checbox();
	function adf_virual_checbox(){
		if ($('#afgc_virtual').prop('checked')) {

			$('input#_virtual').prop('checked', true);

		} else {

			$('input#_virtual').prop('checked', false);

		}
	}
	function adf_virual_checbox_sec(){
		if ($('#_virtual').prop('checked')) {

			$('input#afgc_virtual').prop('checked', true);

		} else {

			$('input#afgc_virtual').prop('checked', false);

		}
	}
	$('#afgc_virtual').trigger('change');

	$(document).on('keyup','input[name="afgc_gift_card_amnt"]',function(){
		var value_of_field = $(this).val();
		$('input[name="_regular_price"]').val(value_of_field);
	});  
	
	/*======== Gidt Card Product Type ======*/

	$('.gift-card_tab').addClass('show_if_gift_card hide_if_simple hide_if_virtual hide_if_grouped hide_if_external hide_if_variable');
	$('.shipping_options').addClass('hide_if_gift_card ');

	/*======== Enable | Disable Gift Card Discount Amount ======*/

	if ($("#afgc_special_discount_type").val()=='percentage' && !$( 'input#afgc_allow_overide_custom_amount' ).prop( 'checked' )) {

		$( '.afgc_special_discount_amount_field' ).show(); // Discount Amount Field.

	} else if ($("#afgc_special_discount_type").val()=='fixed' && !$( 'input#afgc_allow_overide_custom_amount' ).prop( 'checked' )) {

		$( '.afgc_special_discount_amount_field' ).show(); // Discount Amount Field.

	} else if ($("#afgc_special_discount_type").val()=='none') {

		$( '.afgc_special_discount_amount_field' ).hide(); // Discount Amount Field.

	} else {

		$( '.afgc_special_discount_amount_field' ).hide(); // Discount Amount Field.

	}

	jQuery(function ($) {

		$("#afgc_special_discount_type").change(function () {

			if ($(this).val()=='percentage') {

				$( '.afgc_special_discount_amount_field' ).show(); // Discount Amount Field.

			} else if ($(this).val()=='fixed') {

				$( '.afgc_special_discount_amount_field' ).show(); // Discount Amount Field.

			} else if ($(this).val()=='none') {

				$( '.afgc_special_discount_amount_field' ).hide(); // Discount Amount Field.

			} else {

				$( '.afgc_special_discount_amount_field' ).hide(); // Discount Amount Field.

			}

		});

	});


	// Enable | Disable Store Name on PDF.
	$( 'input#afgc_sender_recipent_name' ).on( 'change', function() {

		if ( $( this ).is( ':checked' ) ) {

			$( '#afgc_physical_section_title' ).closest( 'tr' ).show('slow');

		} else {

			$( '#afgc_physical_section_title' ).closest( 'tr' ).hide('slow');

		}
	}).trigger( 'change' );

	// Upload PDF logo
    $(document).on('click', '.afgc_upload_button', function(e) {
        e.preventDefault();
        
        var button = $(this);
        var inputId = button.data('target');

        var frame = wp.media({
            title: 'Select or Upload an Image',
            button: {
                text: 'Use this image'
            },
            multiple: false
        });
        frame.on('select', function() {
            var attachment = frame.state().get('selection').first().toJSON();
            $('#' + inputId).val(attachment.url);
            $('.afgc-pdf-logo').attr('src', attachment.url);
            $('.woocommerce-save-button').prop('disabled', false)
        });

        frame.open();
    });
});

jQuery( document ).on( 'click', '.afgc-gift-card-cat-gallery ul li span', function(){
	jQuery(this).closest('li').remove();
	jQuery.ajax ({
		url: ajaxurl,
		type: 'POST',
		data: {
			action:'afgc_delete_update_gift_gallery_images',

			thumbnail_id: jQuery(this).data('thumbnail_id'),

			term_id: jQuery(this).data('term_id'),
			afgc_nonce : k_php_var.nonce,

		},
		success:function(){
			

		}
	});

});

jQuery(document).ready(function($){
	agfc_current_product_gift_checkbox();
	$(document).on('click', '#_agfc_current_product_gift_checkbox', function(){
		agfc_current_product_gift_checkbox();
	});
	function agfc_current_product_gift_checkbox() {
		if ($('#_agfc_current_product_gift_checkbox').is(':checked')) {
			$('._agfc_selected_gift_card_field').show();
		}else{
			$('._agfc_selected_gift_card_field').hide();
		}
	}
	agfc_variation_current_gift_checkbox();

    // Run on checkbox click
    $(document).on('change', '.afgc-variation-check', function () {
        agfc_variation_current_gift_checkbox();
    });

    function agfc_variation_current_gift_checkbox() {
        $('.afgc-variation-check').each(function () {
            let $checkbox = $(this);
            let $selectField = $checkbox.closest('p').next('p');

            if ($checkbox.is(':checked')) {
                $selectField.show();
            } else {
                $selectField.hide();
            }
        });
    }
});


