/**
 * Elementor Widgets JavaScript
 *
 * @package Addify Gift Card
 * @since 1.7.1
 */

(function($) {
    'use strict';

    /**
     * Gift Cards Grid Widget Handler
     */
    var AFGCGiftCardsGridHandler = function($scope, $) {
        var $giftCardsGrid = $scope.find('.afgc-gift-cards-grid');

        if ($giftCardsGrid.length > 0) {
            // Grid widget initialized
        }
    };

    /**
     * Single Gift Card Widget Handler
     */
    var AFGCSingleGiftCardHandler = function($scope, $) {
        var $singleGiftCard = $scope.find('.afgc-single-gift-card-widget');

        if ($singleGiftCard.length > 0) {
            // Single gift card widget initialized
        }
    };

    /**
     * Gift Card Gallery Widget Handler
     */
    var AFGCGalleryHandler = function($scope, $) {
        var $gallery = $scope.find('.afgc-gallery-widget');

        if ($gallery.length > 0) {
            // Initialize lightbox if needed
            var $galleryItems = $gallery.find('.afgc-gallery-item a');

            if ($galleryItems.length > 0) {
                // Lightbox functionality is handled by Elementor's built-in lightbox
            }
        }
    };

    /**
     * Product Form Widget Handler
     * For Elementor Single Product Page templates
     */
    var AFGCProductFormHandler = function($scope, $) {
        var $productForm = $scope.find('.afgc-elementor-product-form');

        if ($productForm.length > 0) {
            // Re-initialize gift card form functionality
            initGiftCardForm($productForm);
        }
    };

    /**
     * Product Gallery Widget Handler
     * For image selection on product pages
     */
    var AFGCProductGalleryHandler = function($scope, $) {
        var $galleryWidget = $scope.find('.afgc-product-gallery-widget');

        if ($galleryWidget.length > 0) {
            initProductGallery($galleryWidget);
        }
    };

    /**
     * Product Add to Cart Widget Handler
     */
    var AFGCProductAddToCartHandler = function($scope, $) {
        var $addToCart = $scope.find('.afgc-add-to-cart-widget');

        if ($addToCart.length > 0) {
            // Add to cart widget initialized
        }
    };

    /**
     * Initialize Gift Card Form functionality
     */
    function initGiftCardForm($container) {
        // Image selection in gallery
        $container.on('click', '.afgc-choose-image-item:not(.afgc-view), .afgc-gift-pro-gall-img', function(e) {
            e.preventDefault();
            var $this = $(this);
            var $img = $this.find('img');
            var imgUrl = $img.attr('src');

            // Remove selected class from all items
            $container.find('.afgc-choose-image-item, .afgc-gift-pro-gall-img').removeClass('selected');

            // Add selected class to clicked item
            $this.addClass('selected');

            // Update hidden input
            $container.find('.afgc_selected_img').val(imgUrl);

            // Update preview if exists
            var $preview = $container.find('.afgc_selected_image');
            if ($preview.length > 0) {
                $preview.html('<img src="' + imgUrl + '" alt="Selected Image">');
            }
        });

        // Amount radio button selection
        $container.on('change', 'input[name="afgc_admin_set_price"]', function() {
            var selectedPrice = $(this).val();
            $container.find('input[name="final_selected_price_of_gift_card"]').val(selectedPrice);

            // Clear custom amount input when selecting predefined amount
            $container.find('input[name="afgc_virtual_custom_amount"]').val('');
        });

        // Custom amount input
        $container.on('input', 'input[name="afgc_virtual_custom_amount"]', function() {
            var customAmount = $(this).val();
            if (customAmount) {
                // Uncheck radio buttons
                $container.find('input[name="afgc_admin_set_price"]').prop('checked', false);
                // Update final price
                $container.find('input[name="final_selected_price_of_gift_card"]').val(customAmount);
            }
        });

        // Select first image as default if none selected
        var $images = $container.find('.afgc-choose-image-item:not(.afgc-view), .afgc-gift-pro-gall-img');
        if ($images.length > 0 && $container.find('.afgc-choose-image-item.selected, .afgc-gift-pro-gall-img.selected').length === 0) {
            $images.first().addClass('selected');
            var firstImgUrl = $images.first().find('img').attr('src');
            $container.find('.afgc_selected_img').val(firstImgUrl);
        }
    }

    /**
     * Initialize Product Gallery functionality
     */
    function initProductGallery($container) {
        // Image selection
        $container.on('click', '.afgc-product-gallery-item:not(.afgc-view-all-link)', function(e) {
            e.preventDefault();
            var $this = $(this);
            var imgUrl = $this.data('img-url');

            // Remove selected class from all items
            $container.find('.afgc-product-gallery-item').removeClass('selected');

            // Add selected class to clicked item
            $this.addClass('selected');

            // Update hidden input in this widget
            $container.find('.afgc_selected_img').val(imgUrl);

            // Also update the main form's hidden input if it exists
            var $mainForm = $('form.gift_card_cart');
            if ($mainForm.length > 0) {
                $mainForm.find('.afgc_selected_img').val(imgUrl);
            }

            // Update any preview containers
            $('.afgc_selected_image').html('<img src="' + imgUrl + '" alt="Selected Image">');
        });

        // View all link - trigger modal
        $container.on('click', '.afgc-view-all-link a', function(e) {
            e.preventDefault();
            // Trigger the existing modal
            var $modal = $('.afgc-choose-image-wrapper');
            if ($modal.length > 0) {
                $modal.fadeIn(300);
            }
        });
    }

    /**
     * Global initialization for Elementor widgets
     */
    function initElementorGiftCardWidgets() {
        // Handle modal image selection for Elementor pages
        $(document).on('click', '.afgc-tax-gallery-item', function(e) {
            var $this = $(this);
            var imgSrc = $this.find('img').data('src') || $this.find('img').attr('src');

            // Update all selected image inputs
            $('.afgc_selected_img').val(imgSrc);

            // Update preview
            $('.afgc_selected_image').html('<img src="' + imgSrc + '" alt="Selected Image">');

            // Close modal
            $('.afgc-choose-image-wrapper').fadeOut(300);

            // Update visual selection in galleries
            $('.afgc-product-gallery-item').removeClass('selected');
            $('.afgc-choose-image-item').removeClass('selected');
            $('.afgc-gift-pro-gall-img').removeClass('selected');
        });
    }

    /**
     * Register widget handlers
     */
    $(window).on('elementor/frontend/init', function() {
        // Original widgets
        elementorFrontend.hooks.addAction('frontend/element_ready/afgc-gift-cards-grid.default', AFGCGiftCardsGridHandler);
        elementorFrontend.hooks.addAction('frontend/element_ready/afgc-gift-card-single.default', AFGCSingleGiftCardHandler);
        elementorFrontend.hooks.addAction('frontend/element_ready/afgc-gift-card-gallery.default', AFGCGalleryHandler);

        // Single Product Page widgets
        elementorFrontend.hooks.addAction('frontend/element_ready/afgc-product-form.default', AFGCProductFormHandler);
        elementorFrontend.hooks.addAction('frontend/element_ready/afgc-product-gallery.default', AFGCProductGalleryHandler);
        elementorFrontend.hooks.addAction('frontend/element_ready/afgc-product-add-to-cart.default', AFGCProductAddToCartHandler);
    });

    // Initialize on document ready
    $(document).ready(function() {
        initElementorGiftCardWidgets();
    });

})(jQuery);
