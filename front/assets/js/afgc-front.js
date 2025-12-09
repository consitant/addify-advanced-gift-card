jQuery(document).ready(function($) {

	if ( $('.wc-item-meta-label').text("afgc_log_id") ) {
		$( '.wc-item-meta-label' ).closest( '.wc-item-meta' ).hide();
	}
	// Clone Receipent.
	$(document).on('click',"#afgc_clone_btn",function(e){
		e.preventDefault();
		var k = 1;
		$('.afgc-total-email-fields').each(function() {
			if ($(this).hasClass('afgc-email-input-field')) {
				k++;
			}

		});

		$('input[name="total_input_field_admin_select"]').val(k);

		var total_field_created = $('input[name="total_input_field_admin_select"]').val();

		var main_create_main_div = document.createElement('div');
		main_create_main_div.setAttribute('id','afgc_items_for_clone'+total_field_created);
		main_create_main_div.setAttribute('class','afgc_items_for_clone');

		var span = document.createElement('afgc-delete-field');
		span.setAttribute('class','afgc-delete-field');
		span.setAttribute('id','afgc-delete-field'+total_field_created);
		span.innerHTML = 'X';

		var div_1st = document.createElement('div');
		div_1st.setAttribute('id','afgc-form-group');
		div_1st.setAttribute('class','afgc-form-group');
		var input_name_label = document.createElement('label');
		input_name_label.setAttribute('for','afgc_recipient_name');
		input_name_label. innerHTML = 'Name:';

		// creating mulitiple input fields to save image url
		var input_name_filed = document.createElement('input');
		input_name_filed.setAttribute('name','afgc_recipient_name'+total_field_created);
		input_name_filed.setAttribute('class','afgc-frnt-input-fld afgc-name-input-field afgc-total-email-fields');
		input_name_filed.setAttribute('id','afgc_recipient_name');
		input_name_filed.setAttribute('type','text');
		input_name_filed.setAttribute('placeholder','Enter the recipient name');

		var div_2nd = document.createElement('div');
		div_2nd.setAttribute('class','afgc-form-group');
		div_2nd.setAttribute('id','afgc-form-group');

		var email_label = document.createElement('label');
		email_label.setAttribute('for','afgc_recipient_email');
		email_label.innerHTML = 'Email:';

		// creating mulitiple input fields to save image url
		var input_email_filed = document.createElement('input');
		input_email_filed.setAttribute('name','afgc_recipient_email'+total_field_created);
		input_email_filed.setAttribute('class','afgc-frnt-input-fld afgc-email-input-field afgc-total-email-fields');
		input_email_filed.setAttribute('id','afgc_recipient_email');
		input_email_filed.setAttribute('type','email');
		input_email_filed.setAttribute('placeholder','Enter the recipient email');

		main_create_main_div.appendChild(span);
		
		div_1st.appendChild(input_name_label);
		div_1st.appendChild(input_name_filed);


		div_2nd.appendChild(email_label);
		div_2nd.appendChild(input_email_filed);


		main_create_main_div.appendChild(div_1st);
		main_create_main_div.appendChild(div_2nd);


		document.getElementById('afgc-recipient-info').appendChild(main_create_main_div);

		
	});

	$(document).on('click','.afgc-delete-field',function(){
		$(this).next('input[type="text"]').val('');
		$(this).next('input[type="email"]').val('');
		$(this).closest('div.afgc_items_for_clone').remove();
	});

	/*=======================================*/

	/*===== Choose Images Modal Tabs =======*/

	/*=====================================*/

	// Click function
	$('#tabs-nav li').click(function(){
		$('#tabs-nav li').removeClass('active');
		$(this).addClass('active');
		$('.tab-content').hide();
		var activeTab = $(this).find('a').attr('data-tab-class');
		$( '#'+ activeTab ).fadeIn();
		return false;
	});
	
	$('.afgc-tax-gallery-item').click(function(){
		$('.afgc-choose-image-wrapper').hide('slow');
		$('.afgc-choose-image-modal').hide('slow');
	});

	/*================================================*/

	/*=========== Choose Gift Card Modal ============*/

	/*==============================================*/


	$('.afgc-choose-image-wrapper').hide();

	$("#afgc_view_all_card").click(function(e){

		e.preventDefault();

		$('.afgc-choose-image-wrapper').fadeIn('slow');
		$('.afgc-choose-image-modal').fadeIn('slow');
	
	});

	$("#afgc_choose_image_modal_close").click(function(e){

		e.preventDefault();

		$('.afgc-choose-image-wrapper').fadeOut('slow');
		$('.afgc-choose-image-modal').fadeOut('slow');
	
	});


	/*=======================================*/

	/*=========== Select Image ============*/

	/*=====================================*/


	$('.afgc-choose-image ul li:first-child').addClass('afgc-marked');

	$('.afgc-choose-image ul li').click(function(){

		$('.afgc-choose-image ul li').removeClass('afgc-marked');

		$(this).addClass('afgc-marked');

	});



	/*=======================================*/

	/*=========== Replace Image ============*/

	/*=====================================*/


	$('.afgc-choose-image ul li img').on('click', function(){

		var wc_gallery_image = $( '.woocommerce-product-gallery__image a' );
		
		var wc_gallery_image_placeholder = $( '.woocommerce-product-gallery__image--placeholder' );

		let image_url = $(this).attr('src');

		$('.afgc_selected_img').val($(this).attr('src'));

		let srcset = $('.flex-active-slide .wp-post-image').attr('srcset');

		var html_content = '<img src="' + image_url + '" class="wp-post-image size-full" alt="" data-caption="" data-src="' + image_url + '" data-large_image="' + image_url + '" data-large_image_width="1024" data-large_image_height="1024" sizes="(max-width: 600px) 100vw, 600px" ' + srcset + 'width="600" height="600">';
	
		if ( wc_gallery_image.length != 0 ) {

			$( '.woocommerce-product-gallery__image a' ).html(html_content);

			$( '.woocommerce-product-gallery__image .zoomImg').attr("src", image_url);
		   
		} else {

			$( '.woocommerce-product-gallery__image--placeholder img' ).remove;

			wc_gallery_image_placeholder.html(html_content);
		
		}

	});

	 $('.afgc-tax-gallery-item img').on('click', function(){

		var wc_gallery_image = $( '.woocommerce-product-gallery__image a' );
		
		var wc_gallery_image_placeholder = $( '.woocommerce-product-gallery__image--placeholder' );

		let image_url = $(this).data('src');

		$('.afgc_selected_img').val($(this).data('src'));

		let srcset = $('.flex-active-slide .wp-post-image').attr('srcset');

		var html_content = '<img src="' + image_url + '" class="wp-post-image size-full" alt="" data-caption="" data-src="' + image_url + '" data-large_image="' + image_url + '" data-large_image_width="1024" data-large_image_height="1024" sizes="(max-width: 600px) 100vw, 600px" ' + srcset + 'width="600" height="600">';

		if ( wc_gallery_image.length != 0 ) {

			$( '.woocommerce-product-gallery__image a' ).html(html_content);

			$( '.woocommerce-product-gallery__image .zoomImg').attr("src", image_url);
		
		} else {

			$( '.woocommerce-product-gallery__image--placeholder img' ).remove;
			
			$('wc_gallery_image_placeholder').html(html_content);
			
		}
		if ( typeof wp !== 'undefined' && wp.data && wp.data.dispatch ) {
	        wp.data.dispatch('wc/store/cart').invalidateResolutionForStoreCart();
	    }

	 });


	$(".afgc-main-form-preview-container").appendTo(".woocommerce-product-gallery");


	$(document).on('keyup','input[name="afgc_sender_name"]',function(){
		var value_of_field = $(this).val();
		$('.afgc-sender-name span').html(value_of_field);
		$('#afgc_pre_sender_name').html(value_of_field);
	});    

	$(document).on('keyup','.afgc-name-input-field',function(){

		var value_of_field = $(this).val();
		$('.afgc-recip-name span').html(value_of_field);
		$('#afgc_recipient_name_pre').html(value_of_field);
	});

	$(document).on('keyup','.afgc-email-input-field',function(){
		var value_of_field = $(this).val();
		$('.afgc-recip-email span').html(value_of_field);
	});

	$(document).on('focus','input[name="afgc_sender_name"]',function(){
		var value_of_field = $(this).val();
		$('.afgc-sender-name span').html(value_of_field);
	});    

	$(document).on('focus','.afgc-name-input-field',function(){

		var value_of_field = $(this).val();
		$('.afgc-recip-name span').html(value_of_field);
	});

	 $(document).on('focus','.afgc-email-input-field',function(){
		var value_of_field = $(this).val();
		$('.afgc-recip-email span').html(value_of_field);
	 });

	$(document).on('input','input[name="afgc_sender_name"]',function(){
		var value_of_field = $(this).val();
		$('.afgc-sender-name span').html(value_of_field);
	});    

	$(document).on('input','.afgc-name-input-field',function(){

		var value_of_field = $(this).val();
		$('.afgc-recip-name span').html(value_of_field);
	});

	 $(document).on('input','.afgc-email-input-field',function(){
		var value_of_field = $(this).val();
		$('.afgc-recip-email span').html(value_of_field);
	 });



	// Display Enter Card Code on Cart & Checkout Page.
	$('.afgc-show-giftcard').click(function(e){
		e.preventDefault();
		$('.afgc-enter-code').toggle('slow');
	});


	if ($('input[name="selected_price_of_gift_card"]')) {

		var old_val = $('input[name="selected_price_of_gift_card"]').val();

	}


	$(document).on('keyup','input[name="afgc_virtual_custom_amount"]',function(){

		var new_gift_card_val = $(this).val();

		$('input[name="selected_price_of_gift_card"]').val(new_gift_card_val);

		$('input[name="final_selected_price_of_gift_card"]').val(new_gift_card_val);

		if ('' == new_gift_card_val || undefined == new_gift_card_val || 0 == new_gift_card_val) {

			$('input[name="selected_price_of_gift_card"]').val(old_val);

			$('input[name="final_selected_price_of_gift_card"]').val(old_val);

		}   

	});
$('.afgc-option-radio').removeClass('active');
	 $(document).on('change', 'input[name="afgc_admin_set_price"]', function() {
	 	$('.afgc-option-radio').removeClass('active');
        if ($(this).is(':checked')) {
        	$(this).closest('.afgc-option-radio').addClass('active');
            var price = $(this).data('price');
            $('input[name="final_selected_price_of_gift_card"]').val(price);
            $('input[name="afgc_virtual_custom_amount"]').val('');
        }
    });
	
	$(document).on('change','input[name="afgc_virtual_custom_amount"]',function(){

		var new_gift_card_val = $(this).val();

		$('input[name="afgc_admin_set_price"]').prop('checked', false);

		$('input[name="selected_price_of_gift_card"]').val(new_gift_card_val);

		$('.afgc-option-radio').removeClass('active');

		$('input[name="final_selected_price_of_gift_card"]').val(new_gift_card_val);

		if ('' == new_gift_card_val || undefined == new_gift_card_val || 0 == new_gift_card_val) {

			$('input[name="selected_price_of_gift_card"]').val(old_val);

			$('input[name="final_selected_price_of_gift_card"]').val(old_val);

		}   

	});

	$(document).on('click', '.afgc_submit_btn', function(){
		var emailCheckbox = $('input[ name="afgc_email_tab"]');
        var pdfCheckbox = $('input[ name="afgc_pdf_tab"]');
        var notice = $('.woocommerce-notices-wrapper');
        var selectedGiftType = $('select[name="afgc_gift_type"]').val();
        if (selectedGiftType === 'product-gift'){
        	var afgc_gift_products = $('#afgc_gift_products').val();
        	if (!afgc_gift_products) {
		            event.preventDefault();
		            $('.woocommerce-error').remove();
		            notice.append( '<div class="woocommerce-error">Please select product.</div>');
		            notice.show();
		            $('html, body').animate({
		                scrollTop: $('header').offset().top
		            }, 500); 
		        }
        }else{
	        if ($('#afgc_virtual_custom_amount').length > 0 || $('input[name="afgc_admin_set_price"]').length > 0) {
	        	var afCustomAmountOption = $('.afgc_virtual_custom_amount_opt').val();
	            var afCustomAmount = $('input[name="final_selected_price_of_gift_card"]').val();
		        var afRadioSelected = $('input[name="afgc_admin_set_price"]:checked').length > 0;
		        if (!afRadioSelected && !afCustomAmountOption) {
		            event.preventDefault();
		            $('.woocommerce-error').remove();
		            notice.append( '<div class="woocommerce-error">Please select a predefined amount or enter a custom amount.</div>');
		            notice.show();
		            $('html, body').animate({
		                scrollTop: $('header').offset().top
		            }, 500);
		        }
		    }
		}

		if (emailCheckbox.length > 0 || pdfCheckbox.length > 0) {
	        if (!emailCheckbox.is(':checked') && !pdfCheckbox.is(':checked')) {
	            event.preventDefault();
	            let optionsText = [];
		        $(".afgc_gift_card_opt li").each(function() {
		            const labelText = $(this).find("label").text();
		            optionsText.push(labelText);
		        });

		        const optionsMessage = optionsText.join(" or ");

	            notice.find('.woocommerce-error').remove();
	            notice.append(`<div class="woocommerce-error">Please select ${optionsMessage} before adding to cart.</div>`);
	            $('html, body').animate({
	                scrollTop: $('header').offset().top
	            }, 500); 
	        }
	    }

	});

	$(document).on('change','input[name="afgc_upload_img_btn"]',function(){

		if ( !$('.afgc-upload-img-btn').prop("files") ) {

			return;

		}

		var admin_url = k_php_var.admin_url;

		var file_data = $('.afgc-upload-img-btn').prop("files")[0];

		var errorNotice = $('.woocommerce-notices-wrapper');
        errorNotice.hide();
        if (file_data) {
            var fileName = file_data.name;
            var fileSize = file_data.size;
            var fileExtension = fileName.split('.').pop().toLowerCase();
            var allowedExtensions = ['png', 'gif', 'jpeg', 'jpg'];
            var maxSize = 2 * 1024 * 1024;
            $('.woocommerce-error').remove();
            if (!allowedExtensions.includes(fileExtension)) {
                errorNotice.append( '<div class="woocommerce-error">Please upload a valid image file PNG, GIF, JPEG, JPG.</div>');
                errorNotice.show();
                 $('html, body').animate({
	                scrollTop: $('header').offset().top
	            }, 500);
                $(this).val('');
                return;
            } else if(fileSize > maxSize){
            	errorNotice.append('<div class="woocommerce-error">File size must be less than 2MB.</div>');
                errorNotice.show();
                $(this).val('');
                $('html, body').animate({
	                scrollTop: $('header').offset().top
	            }, 500);
                return;
            }
            else {
                errorNotice.hide();
            }
        }

		var form_data = new FormData();

		form_data.append("file", file_data);

		form_data.append( 'action' , 'afgc_cuctom_card_image_cb' );
		form_data.append( 'nonce' , k_php_var.nonce );
		jQuery.ajax({
			url: admin_url,
			type: 'POST',
			contentType: false,
			processData: false,
			data: form_data,
			success: function(data){

				var wc_gallery_image = $( '.woocommerce-product-gallery__image a' );
		
				var wc_gallery_image_placeholder = $( '.woocommerce-product-gallery__image--placeholder' );

				var image_url = data['prd_pg_img_links'];

				$('.afgc_selected_img').val(image_url);

				let srcset = $('.flex-active-slide .wp-post-image').attr('srcset');

				var html_content = '<img src="' + image_url + '" class="wp-post-image size-full" alt="" data-caption="" data-src="' + image_url + '" data-large_image="' + image_url + '" data-large_image_width="1024" data-large_image_height="1024" sizes="(max-width: 600px) 100vw, 600px" ' + srcset + 'width="600" height="600">';
		

				if ( wc_gallery_image.length != 0 ) {

					$( '.woocommerce-product-gallery__image a' ).html(html_content);

					$( '.woocommerce-product-gallery__image .zoomImg').attr("src", image_url);
				   
				
				} if ( wc_gallery_image_placeholder.length != 0 ) {

					$( '.woocommerce-product-gallery__image--placeholder' ).html(html_content);

					$( '.woocommerce-product-gallery__image .zoomImg').attr("src", image_url);
				   
				
				} else {

					$( '.woocommerce-product-gallery__image--placeholder img' ).remove;

					$( 'wc_gallery_image_placeholder' ).html(html_content);
					
				}
					
			}

		});

	});

    $('#preview_button').on('click', function() {
    	
        // Get selected values from the form
        var recipientName = $('input[name^="afgc_recipient_name"]').text();
        var sender_name_physical = $('#afgc_phy_gift_sender_name').val();
        var selectedImage = $('.afgc_selected_img').val();
        var sender_message = $('#afgc_sender_message').val();
        var sender_message_physical = $('#afgc_phy_gift_sender_message').val();
        var amount = $('input[name="final_selected_price_of_gift_card"]').val();
        $('#afgc_recipient_name').text(recipientName);
        $('#afgc_selected_price').text(amount);
        if(sender_message && sender_message !== undefined){
        	$('.afg-preview-sender-message').text(sender_message);
        }else if(sender_message_physical && sender_message_physical !== undefined){
        	$('.afg-preview-sender-message').text(sender_message_physical);
        }
        
        if(sender_name_physical && sender_name_physical !== undefined){
        	$('#afgc_pre_sender_name').text(sender_name_physical);
        }
        var physicSelectedImage = $('.product-type-gift_card .woocommerce-product-gallery__image img').attr('src');
        if (selectedImage) {
            $('#afgc_preview_popup .afgc_selected_image').html('<img src="' + selectedImage + '" alt="Gift Card Image" />');
        }else if(physicSelectedImage){
        	$('#afgc_preview_popup .afgc_selected_image').html('<img src="' + physicSelectedImage + '" alt="Gift Card Image" />');
        }

        $('#afgc_preview_popup').fadeIn();
        $('body').addClass('popup-active');
    });
    $('#afgc_close_popup').on('click', function() {
        $('#afgc_preview_popup').fadeOut();
        $('body').removeClass('popup-active');
    });
	var currenySymbol = k_php_var.afgc_curreny_symbol;
        $('.product_search').select2({
        placeholder: 'Search for products',
        ajax: {
            url: k_php_var.admin_url,
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    q: params.term,
                    action: 'product_search',
                    nonce:k_php_var.nonce
                };
            },
            processResults: function(data) {
            	 return {
	                results: data.map(item => ({
	                    id: item.id,
	                    text: item.text,
	                    price: item.price // Include price in the data
	                }))
	            };
            },
            cache: true
        },
        minimumInputLength: 3
    });

    $('.product_search').on('select2:select', function(e) {
	    let selectedData = e.params.data;
	    if (selectedData.price) {
	        $('input[name="final_selected_price_of_gift_card"]').val(selectedData.price);
	        $('.product .price').text(currenySymbol + ' ' + selectedData.price);
	    }
	});

    $(document).on('change', '.afgc-gift-type-select', function(){
    	afgc_gift_type_select();
    })
    function afgc_gift_type_select(){
    	var giftType = $('.afgc-gift-type-select').val();
    	if (giftType == 'product-gift'){
    		$('.agf-selected-product').show();
    		$('.afgc-virtual-custom-amount').hide();
    		$('#preview_button').hide();
    		$('#afgc_clone_btn').hide();
    	}else{
    		$('.agf-selected-product').hide();
    		$('.afgc-virtual-custom-amount').show();
    		$('#preview_button').show();
    		$('#afgc_clone_btn').show();
    	}
    }
    afgc_gift_type_select();

    const dateInput = $('#afgc_phy_gift_delivery_date');
    const selecteDate = parseInt(dateInput.attr('date-selected_date'));
    if(selecteDate){
	    const today = new Date();
	    const todayFormatted = today.toISOString().split('T')[0];

	    const maxDate = new Date();
	    maxDate.setDate(today.getDate() + (isNaN(selecteDate) ? 0 : selecteDate));
	    const maxDateFormatted = maxDate.toISOString().split('T')[0];
	    dateInput.attr('min', todayFormatted);
	    dateInput.attr('max', maxDateFormatted);
	}

	const virtDateInput = $('#afgc_delivery_date');
	const virtSelecteDate = parseInt(virtDateInput.attr('date-virtualSelected_date'));
	if(virtSelecteDate){
	    const virtToday = new Date();
	    const virtTodayFormatted = virtToday.toISOString().split('T')[0];

	    const virtMaxDate = new Date();
	    virtMaxDate.setDate(virtToday.getDate() + (isNaN(virtSelecteDate) ? 0 : virtSelecteDate));
	    const virtMaxDateFormatted = virtMaxDate.toISOString().split('T')[0];

	    virtDateInput.attr('min', virtTodayFormatted);
	    virtDateInput.attr('max', virtMaxDateFormatted);
	}

    $(document).on('click', '.afg-change-link', function(e) {
        e.preventDefault();

        let url = new URL(window.location.href);
        url.searchParams.delete('gift_product');
        window.location.href = url.toString();
    });
});

jQuery(document).ready(function($) {
    $('form.variations_form').on('show_variation', function(event, variation) {
        $('.afgc-gift-btn').remove();
        if (variation.afgc_gift_button) {
            $('.single_variation_wrap .woocommerce-variation-add-to-cart').append(variation.afgc_gift_button);
        }
    });
    $('form.variations_form').on('reset_data', function() {
        $('.afgc-gift-btn').remove();
    });

});
