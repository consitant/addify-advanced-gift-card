<?php

$afgc_customize_theme  = get_option( 'afgc_customize_theme' );
$afgc_pick_theme_color = get_theme_mod( 'button_color' );

$afgc_pick_border_color = get_theme_mod( 'border_color' );

$afgc_pick_text_color = get_theme_mod( 'text_color' );

	// On Hover.

$afgc_hover_theme_color = get_theme_mod( 'theme_color' );

$afgc_hover_border_color = get_theme_mod( 'border_color' );

$afgc_hover_text_color = get_theme_mod( 'text_color' );

if ( 'yes' == $afgc_customize_theme ) {

	$afgc_pick_theme_color = get_option( 'afgc_pick_theme_color' );

	$afgc_pick_border_color = get_option( 'afgc_pick_border_color' );

	$afgc_pick_text_color = get_option( 'afgc_pick_text_color' );

	// On Hover.

	$afgc_hover_theme_color = get_option( 'afgc_hover_theme_color' );

	$afgc_hover_border_color = get_option( 'afgc_hover_border_color' );

	$afgc_hover_text_color = get_option( 'afgc_hover_text_color' );

}
?>


<style>
	.afgc-gift-card-amount-box button,
	.afgc-gift-card-amount-box label.afgc-option-radio,
	.afgc_preview_btn{
		border: 1px solid <?php echo esc_attr( $afgc_pick_border_color ); ?>!important;
		background-color: <?php echo esc_attr( $afgc_pick_theme_color ); ?>!important;
		color: <?php echo esc_attr( $afgc_pick_text_color ); ?>!important;
	}

	.afgc-gift-card-amount-box input[type=text],
	.afgc-gift-card-amount-box input[type=number],
	.afgc-gift-card-amount-box input[type=email],
	.afgc-gift-card-amount-box input[type=date],
	.afgc-frnt-txtarea-fld, .afgc-gift-type-wrap .select2-selection.select2-selection--single,
	.afgc-gift-type-select{
		border: 1px solid <?php echo esc_attr( $afgc_pick_border_color ); ?> !important;
	}
	.afgc-gift-card-amount-box input[type=text]:hover,
	.afgc-gift-card-amount-box input[type=number]:hover,
	.afgc-gift-card-amount-box input[type=email]:hover,
	.afgc-gift-card-amount-box input[type=date]:hover,
	.afgc-frnt-txtarea-fld:hover, .afgc-gift-type-wrap .select2-selection.select2-selection--single:hover,
	.afgc-gift-type-select:hover{
		border: 1px solid <?php echo esc_attr( $afgc_hover_border_color ); ?>!important;
	}

	.afgc-upload-img label{
		border: 1px solid <?php echo esc_attr( $afgc_pick_border_color ); ?>;
		background-color: <?php echo esc_attr( $afgc_pick_theme_color ); ?>;
		color: <?php echo esc_attr( $afgc_pick_text_color ); ?>;
	}

	.afgc-marked:before{
		background-color: <?php echo esc_attr( $afgc_pick_theme_color ); ?> !important;
	}

	.afgc-marked{
		position: relative;
		border: 1px dashed <?php echo esc_attr( $afgc_pick_border_color ); ?> !important;
	}

	.afgc-view{
		background-color: <?php echo esc_attr( $afgc_pick_theme_color ); ?>;
	}

	.afgc-view a{
		color: <?php echo esc_attr( $afgc_pick_text_color ); ?>;
	}

	.afgc-choose-image-modal-close{
		background-color: <?php echo esc_attr( $afgc_pick_theme_color ); ?>;
		color: <?php echo esc_attr( $afgc_pick_text_color ); ?>;
	}

	.afgc-upload-img label:hover,
	.afgc-upload-img label:active,
	.afgc-upload-img label:focus,
	.afgc-gift-card-amount-box label.afgc-option-radio:hover,
	.afgc-gift-card-amount-box label.afgc-option-radio:active,
	.afgc-gift-card-amount-box label.afgc-option-radio:focus,
	.afgc-gift-card-amount-box label.afgc-option-radio.active{
		border: 1px solid <?php echo esc_attr( $afgc_hover_border_color ); ?>!important;
		background-color: <?php echo esc_attr( $afgc_hover_theme_color ); ?>!important;
		color: <?php echo esc_attr( $afgc_hover_text_color ); ?>!important;
	}
	.afgc_preview_btn:hover{
		background-color: <?php echo esc_attr( $afgc_hover_theme_color ); ?>!important;
		color: <?php echo esc_attr( $afgc_hover_text_color ); ?>!important;
		border: 1px solid <?php echo esc_attr( $afgc_hover_border_color ); ?>!important;
	}

	.afgc-gift-card-amount-box button.active,
	.afgc-gift-card-amount-box button:hover,
	.afgc-gift-card-amount-box button:active,
	.afgc-gift-card-amount-box button:focus{
		border: 1px solid <?php echo esc_attr( $afgc_hover_border_color ); ?>;
		background-color: <?php echo esc_attr( $afgc_hover_theme_color ); ?>;
		color: <?php echo esc_attr( $afgc_hover_text_color ); ?>;
		outline: 0;
	}

	.afgc-view:hover,
	.afgc-view:focus,
	.afgc-view:active{
		background-color: <?php echo esc_attr( $afgc_hover_theme_color ); ?>;
		color: <?php echo esc_attr( $afgc_hover_text_color ); ?>;
	}

	.afgc-view:hover a,
	.afgc-view:focus a,
	.afgc-view:active a{
		color: <?php echo esc_attr( $afgc_hover_text_color ); ?>;
		outline: 0px !important;
	}

	.afgc-view a:hover,
	.afgc-view a:focus,
	.afgc-view a:active{
		outline: 0px !important;
	}

	.afgc-choose-image-modal-close:hover,
	.afgc-choose-image-modal-close:focus,
	.afgc-choose-image-modal-close:active{
		background-color: <?php echo esc_attr( $afgc_hover_theme_color ); ?>;
		color: <?php echo esc_attr( $afgc_hover_text_color ); ?>;
	}

</style>
