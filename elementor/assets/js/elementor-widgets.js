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
        // Initialization code if needed
        var $giftCardsGrid = $scope.find('.afgc-gift-cards-grid');

        if ($giftCardsGrid.length > 0) {
            // Add any custom JavaScript for the grid widget here
            console.log('AFGC Gift Cards Grid widget loaded');
        }
    };

    /**
     * Single Gift Card Widget Handler
     */
    var AFGCSingleGiftCardHandler = function($scope, $) {
        var $singleGiftCard = $scope.find('.afgc-single-gift-card-widget');

        if ($singleGiftCard.length > 0) {
            // Add any custom JavaScript for the single gift card widget here
            console.log('AFGC Single Gift Card widget loaded');
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
                console.log('AFGC Gallery widget loaded');
            }
        }
    };

    /**
     * Register widget handlers
     */
    $(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction('frontend/element_ready/afgc-gift-cards-grid.default', AFGCGiftCardsGridHandler);
        elementorFrontend.hooks.addAction('frontend/element_ready/afgc-gift-card-single.default', AFGCSingleGiftCardHandler);
        elementorFrontend.hooks.addAction('frontend/element_ready/afgc-gift-card-gallery.default', AFGCGalleryHandler);
    });

})(jQuery);
