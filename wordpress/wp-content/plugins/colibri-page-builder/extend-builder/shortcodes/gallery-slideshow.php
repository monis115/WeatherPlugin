<?php

use ColibriWP\PageBuilder\PageBuilder;

add_shortcode( 'colibri-gallery-slideshow', 'colibri_gallery_slideshow' );


$mesmerize_slideshow_index = 0;


function sanitizeAtts( $atts ) {
	$sanitizedAtts = [];
	foreach ( $atts as $key => $value ) {
		$sanitizedAtts[ filter_var( $key, FILTER_SANITIZE_STRING ) ] = filter_var( $atts[ $key ], FILTER_SANITIZE_STRING );
	}

	return $sanitizedAtts;
}

function colibri_gallery_slideshow( $atts ) {

	$mesmerize_slideshow_index = intval( get_theme_mod( 'mesmerize_slideshow_element_index', 0 ) );
	set_theme_mod( 'mesmerize_slideshow_element_index', $mesmerize_slideshow_index === PHP_INT_MAX ? 0 : $mesmerize_slideshow_index + 1 );

	$atts = shortcode_atts(
		array(
			'id'                         => 'ope-slideshow-' . ( $mesmerize_slideshow_index ),
			'size'                       => 'medium',
			'ids'                        => '',
			'style-type'                 => 'default',
			'aspect-ratio'               => '4_3',
			'drop-shadow'                => '1',
			'shadow-level'               => '5',
			'show-thumbnails'            => '1',
			'thumbnails-padding'         => 'default',
			'thumbnail-background-color' => 'rgba(248,248,248,1)',
			'thumbnail-padding'          => 'default',
			'show-captions'              => '0',
			'caption-align'              => 'center',
			'caption-font-color'         => 'dark',
			'caption-font-size'          => 'default',
			'caption-background-color'   => 'rgba(248,248,248,1)',
			'caption-padding'            => 'default',
		),
		$atts
	);


	//options

	//aspect ratio
	$aspectRatioHeight = getHeightBasedOnAspectRation( $atts['aspect-ratio'] );

	//drop shadow
	$dropShadowClass = $atts['drop-shadow'] ? ( 'elevation-z' . $atts['shadow-level'] ) : '';

	//overlay
	$overlayColorExist = true;
	$useOverlay        = true;

	//caption color
	$captionFontColor = getCaptionFontColor( $atts['caption-font-color'] );

	//caption font size
	$captionFontSize = getCaptionFontSize( $atts['caption-font-size'] );

	//caption background color
	$captionBackgroundColorExists = $atts['caption-background-color'] ? true : false;

	//caption padding
	$captionPadding = getCaptionPadding( $atts['caption-padding'] );

	//thumbnails

	$showThumbnails = $atts['show-thumbnails'] ? true : false;

	if ( $showThumbnails ) {
		$atts['showThumbnails']          = true;
		$thumbnailsPadding               = getThumbnailsPadding( $atts['thumbnails-padding'] );
		$thumbnailPadding                = getThumbnailPadding( $atts['thumbnail-padding'] );
		$atts['thumbnailPadding']        = intval( $thumbnailPadding );
		$thumbnailsBackgroundColorExists = $atts['thumbnail-background-color'] ? true : false;
		$thumbnails                      = array();
	} else {
		$atts['showThumbnails'] = false;
	}

	if ( empty( $atts['ids'] ) ) {

		ob_start();

		$imagesColors = array();

		?>
        <div class="<?= $atts['id'] ?>-dls-wrapper slideshow-items-wrapper slideshow-element-style-<?= $atts['style-type'] ?> <?= $dropShadowClass ?>">
            <div class="slideshow-inner">

				<?php for ( $img = 0; $img < 6; $img ++ ) {
					$imgIndex = $img % 8 + 1;
					$imgURL   = PageBuilder::instance()->rootURL() . "/extend-builder/assets/images/{$imgIndex}.jpg";
					if ( $showThumbnails ) {
						$thumbnails[] = $imgURL;
					}
					?>
                    <dl class="slideshow-item">
                        <dt class="slideshow-icon landscape <?= $atts['id'] ?>-image"
                            style="background-image: url(<?= $imgURL ?>)">

                            <!-- Show captions -->
							<?php if ( $atts['show-captions'] == 1 )  : ?>
                                <div class="slideshow-caption">
                                    <p>Slide <?php echo( $img + 1 ) ?></p>
                                </div>
							<?php endif; ?>

                        </dt>
                    </dl>
					<?php
				}
				?>
            </div>

            <!-- Show thumbnails -->
			<?php if ( $showThumbnails )  : ?>
                <div class="slideshow-thumbnails-outer">
                    <div class="slideshow-thumbnails-inner">
                        <div class="slideshow-thumbnails owl-carousel">
							<?php foreach ( $thumbnails as $ti => $thumbnail ): ?>
                                <div class="slideshow-thumbnail" data-index="<?= $ti ?>"
                                     style="background-image: url(<?= $thumbnail ?>)"></div>
							<?php endforeach; ?>
                        </div>
                    </div>
                </div>
			<?php endif; ?>

        </div>
		<?php

		$slideshow = ob_get_clean();
	} else {


		add_filter( 'use_default_gallery_style', '__return_false' );

		// make sure the slideshow_shortcode function will return the default slideshow
		// fixes japck issue
		add_filter( 'post_gallery', '__return_empty_string', PHP_INT_MAX );

		$slideshow = gallery_shortcode( $atts );

		remove_filter( 'post_gallery', '__return_empty_string', PHP_INT_MAX ); // remove the previous filter
		remove_filter( 'use_default_gallery_style', '__return_false' );

		// construct the content

		$new_content = '';
		$pos_index   = 0;

		$new_content .= '<div class="' . $atts['id'] . '-dls-wrapper slideshow-items-wrapper slideshow-element-style-' . $atts['style-type'] . ' ' . $dropShadowClass . '"><div class="slideshow-inner">';

		while ( strpos( $slideshow, 'src="', $pos_index ) !== false /* && strpos($slideshow, 'wp-caption-text', $pos_index)!==false */ ) {

			$extracted_url = $extracted_caption = '';

			$string        = $slideshow;
			$start         = strpos( $string, '768w, ', $pos_index ) + strlen( '768w, ' );
			$stop          = strpos( $string, ' ', $start + strlen( '768w, ' ) );
			$extracted_url = trim( substr( $string, $start, $stop - $start ) );

			if ( $showThumbnails ) {

				$start2              = strpos( $string, 'srcset="', $pos_index ) + strlen( 'srcset="' );
				$stop2               = strpos( $string, ' 300w', $start2 + strlen( ' 300w' ) );
				$extracted_thumb_url = trim( substr( $string, $start2, $stop2 - $start2 ) );

				$thumbnails[] = $extracted_thumb_url;
			}

			$jump = strpos( $string, '</dl>', $pos_index ) + strlen( '</dl>' );

			$extra_len = strlen( 'wp-caption-text' );
			$start     = strpos( $string, 'wp-caption-text', $pos_index ) + $extra_len;
			if ( $start < $jump && $start != $extra_len ) {
				$next_pos          = strpos( $string, '>', $start + strlen( '>' ) ) + 1;
				$stop              = strpos( $string, '</dd>', $next_pos );
				$extracted_caption = trim( substr( $string, $next_pos, $stop - $next_pos ) );
			}

			$caption_content = '';
			if ( $atts['show-captions'] == 1 && trim( $extracted_caption ) != '' ) {
				$caption_content .= "<div class='slideshow-caption'>" .
				                    "<p>" . $extracted_caption . "</p>" .
				                    "</div>";
			}

			$replace_with = "<dl class='slideshow-item'>"
			                . "<dt class='slideshow-icon landscape " . $atts['id'] . "-image' style='background-image: url(" . $extracted_url . ");'>"
			                . $caption_content
			                . "</dt>"
			                . "</dl>";

			$new_content .= $replace_with;

			$pos_index = $jump;

		}

		$thumbnails_content = '';
		if ( $showThumbnails ) {
			$thumbnails_content .= "<div class='slideshow-thumbnails-outer'>" .
			                       "<div class='slideshow-thumbnails-inner'>" .
			                       "<div class='slideshow-thumbnails owl-carousel'>";
			foreach ( $thumbnails as $ti => $thumbnail ) {
				$thumbnails_content .= "<div class='slideshow-thumbnail' data-index='" . $ti . "' style='background-image: url(" . $thumbnail . ")'></div>";
			}
			$thumbnails_content .= "</div>" .
			                       "</div>" .
			                       "</div>";
		}

		$new_content .= '</div>' . $thumbnails_content . '</div>';

		$slideshow = $new_content;


		if ( $atts['show-captions'] == 1 ) {
			$slideshow = $slideshow . "
                    <style>
                        #{$atts['id']} .slideshow-caption {
                            position: absolute;
                            width: 100%;
                            font-style: italic;
                        }
                        #{$atts['id']} .slideshow-caption > p {
                            overflow: hidden;
                            cursor: default;
                            text-overflow: ellipsis;
                            max-height: 100%;
                        }
                    </style>";
		}

	}


	ob_start();

	$slideshow_selector = '#' . $atts['id'];

	?>
    <style type="text/css">

        <?=$slideshow_selector?>
        .slideshow-items-wrapper {
            position: relative;
        }

        <?=$slideshow_selector?>
        .slideshow-inner {
            position: relative;
            overflow: hidden;
        }

        <?=$slideshow_selector?>
        .slideshow-item {
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0;
            right: 0;
        }

        <?=$slideshow_selector?>
        .slideshow-item:not(:first-child) {
            display: none;
        }

        <?=$slideshow_selector?>
        .slideshow-item, <?=$slideshow_selector?> .slideshow-item > dt {
            height: 100%;
        }

        <?=$slideshow_selector?>
        .slideshow-item > dt {
            background-size: cover;
            background-position: center center;
        }

        /* bordered start */
        <?=$slideshow_selector?>
        .slideshow-element-style-bordered {
            border: 5px solid #03a9f4;
        }

        /* bordered end */

        /* Style option end */

        /* Aspect ratio start*/
        <?=$slideshow_selector?>
        .slideshow-items-wrapper .slideshow-inner {
            padding-top: <?= $aspectRatioHeight ?>;
        }

        /* Aspect ratio end*/

        /* Column spacing start*/
        <?=$slideshow_selector?>
        .slideshow-item > dt {
            max-width: 100%;
            width: auto;
        }

        /* Column spacing end*/

        /* Overlay color start*/
        <?=$slideshow_selector?>
        .slideshow-element-image-overlay {
            position: absolute;
            top: 0;
            bottom: 0;
            height: 100%;
            left: -1px;
            right: -1px;
            background-color: rgba(0, 0, 0, 0.15);
            transition: opacity .6s, -webkit-transform .3s;
            transition: transform .3s, opacity .6s;
            transition: transform .3s, opacity .6s, -webkit-transform .3s;
            pointer-events: none;
            padding: 0 !important;
            margin: 0 !important;
        }

        <?php if($overlayColorExist) : ?>
        <?=$slideshow_selector?>
        .slideshow-item a > .slideshow-element-image-overlay {
            background-color: black;
        }

        <?php endif; ?>
        /* Overlay color end*/

        /* Grayscale start */
        <?php if(1 == 1 ) : ?>
        <?=$slideshow_selector?>
        .slideshow-item a > img {
            -webkit-filter: grayscale(100%); /* Safari 6.0 - 9.0 */
            filter: grayscale(100%);
        }

        <?php endif; ?>
        /* Grayscale end */

        /* Image hover styles start */

        <?=$slideshow_selector?>
        .image-has-hover .slideshow-item:hover .grayscale {
            -webkit-filter: grayscale(100%); /* Safari 6.0 - 9.0 */
            filter: grayscale(100%);
        }

        /* Hover styles end */

        <?php if($atts['show-captions'] == 1) : ?>

        /* Caption align */
        <?=$slideshow_selector?>
        .slideshow-caption {
            text-align: <?=$atts['caption-align']?>;
        }


        /* Caption font size */
        <?=$slideshow_selector?>
        .slideshow-caption {
            font-size: <?=$captionFontSize?>;
        }

        /* Caption background color*/
        <?php if($captionBackgroundColorExists) : ?>
        <?=$slideshow_selector?>
        .slideshow-caption {
            background-color: <?=$atts['caption-background-color']?>;
        }

        <?php endif; ?>


        <?php endif; ?>

        /* Thumbnails style */

        <?php if( $atts['show-thumbnails'] == 1 )  : ?>

        <?=$slideshow_selector?>
        .slideshow-thumbnails-outer {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            width: 100%;
            padding: <?=$thumbnailsPadding?>;
        }

        <?=$slideshow_selector?>
        .slideshow-thumbnails-inner {
            width: 100%;
        }

        <?=$slideshow_selector?>
        .slideshow-thumbnails {
        }

        /* Thumbnails background color*/
        <?php if($thumbnailsBackgroundColorExists) : ?>
        <?=$slideshow_selector?>
        .slideshow-thumbnails-outer {
            background-color: <?=$atts['thumbnail-background-color']?>;
        }

        <?php endif; ?>

        <?=$slideshow_selector?>
        .slideshow-thumbnail {
            background-size: cover;
            background-position: center center;
        }

        <?=$slideshow_selector?>
        .owl-carousel {
            position: initial;
        }

        <?=$slideshow_selector?>
        .owl-stage {
            background: none;
        }

        <?=$slideshow_selector?>
        .owl-stage > .owl-item:last-child .slideshow-thumbnail {
            margin-right: 0px;
        }

        <?=$slideshow_selector?>
        .owl-nav {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
        }

        <?=$slideshow_selector?>
        .owl-nav .owl-prev, <?=$slideshow_selector?> .owl-nav .owl-next {
            display: inline-block;
            font: normal normal normal 14px/1 FontAwesome;
            font-size: inherit;
            text-rendering: auto;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            position: absolute;
            bottom: 0;
            border: none;
            padding: 5px 8px;
            background: rgba(0, 0, 0, 0.5);
        }

        <?=$slideshow_selector?>
        .owl-nav .owl-prev {
            left: 0;
        }

        <?=$slideshow_selector?>
        .owl-nav .owl-next {
            right: 0;
        }

        <?=$slideshow_selector?>
        .owl-nav .owl-prev span, <?=$slideshow_selector?> .owl-nav .owl-next span {
            display: none;
        }

        <?=$slideshow_selector?>
        .owl-nav .owl-prev:before {
            display: block;
            font-size: 26px;
            content: "\f053";
            color: #fff;
        }

        <?=$slideshow_selector?>
        .owl-nav .owl-next:before {
            display: block;
            font-size: 26px;
            content: "\f054";
            color: #fff;
        }

        <?php endif; ?>

        /* Elevation classes */

        <?=$slideshow_selector?>
        .elevation-z0 {
            box-shadow: 0px 0px 0px 0px rgba(0, 0, 0, 0.2), 0px 0px 0px 0px rgba(0, 0, 0, 0.14), 0px 0px 0px 0px rgba(0, 0, 0, 0.12);
        }

        <?=$slideshow_selector?>
        .elevation-z1 {
            box-shadow: 0px 2px 1px -1px rgba(0, 0, 0, 0.2), 0px 1px 1px 0px rgba(0, 0, 0, 0.14), 0px 1px 3px 0px rgba(0, 0, 0, 0.12);
        }

        <?=$slideshow_selector?>
        .elevation-z2 {
            box-shadow: 0px 3px 1px -2px rgba(0, 0, 0, 0.2), 0px 2px 2px 0px rgba(0, 0, 0, 0.14), 0px 1px 5px 0px rgba(0, 0, 0, 0.12);
        }

        <?=$slideshow_selector?>
        .elevation-z3 {
            box-shadow: 0px 3px 3px -2px rgba(0, 0, 0, 0.2), 0px 3px 4px 0px rgba(0, 0, 0, 0.14), 0px 1px 8px 0px rgba(0, 0, 0, 0.12);
        }

        <?=$slideshow_selector?>
        .elevation-z4 {
            box-shadow: 0px 2px 4px -1px rgba(0, 0, 0, 0.2), 0px 4px 5px 0px rgba(0, 0, 0, 0.14), 0px 1px 10px 0px rgba(0, 0, 0, 0.12);
        }

        <?=$slideshow_selector?>
        .elevation-z5 {
            box-shadow: 0px 3px 5px -1px rgba(0, 0, 0, 0.2), 0px 5px 8px 0px rgba(0, 0, 0, 0.14), 0px 1px 14px 0px rgba(0, 0, 0, 0.12);
        }

        <?=$slideshow_selector?>
        .elevation-z6 {
            box-shadow: 0px 3px 5px -1px rgba(0, 0, 0, 0.2), 0px 6px 10px 0px rgba(0, 0, 0, 0.14), 0px 1px 18px 0px rgba(0, 0, 0, 0.12);
        }

        <?=$slideshow_selector?>
        .elevation-z7 {
            box-shadow: 0px 4px 5px -2px rgba(0, 0, 0, 0.2), 0px 7px 10px 1px rgba(0, 0, 0, 0.14), 0px 2px 16px 1px rgba(0, 0, 0, 0.12);
        }

        <?=$slideshow_selector?>
        .elevation-z8 {
            box-shadow: 0px 5px 5px -3px rgba(0, 0, 0, 0.2), 0px 8px 10px 1px rgba(0, 0, 0, 0.14), 0px 3px 14px 2px rgba(0, 0, 0, 0.12);
        }

        <?=$slideshow_selector?>
        .elevation-z9 {
            box-shadow: 0px 5px 6px -3px rgba(0, 0, 0, 0.2), 0px 9px 12px 1px rgba(0, 0, 0, 0.14), 0px 3px 16px 2px rgba(0, 0, 0, 0.12);
        }

        <?=$slideshow_selector?>
        .elevation-z10 {
            box-shadow: 0px 6px 6px -3px rgba(0, 0, 0, 0.2), 0px 10px 14px 1px rgba(0, 0, 0, 0.14), 0px 4px 18px 3px rgba(0, 0, 0, 0.12);
        }

        <?=$slideshow_selector?>
        .elevation-z11 {
            box-shadow: 0px 6px 7px -4px rgba(0, 0, 0, 0.2), 0px 11px 15px 1px rgba(0, 0, 0, 0.14), 0px 4px 20px 3px rgba(0, 0, 0, 0.12);
        }

        <?=$slideshow_selector?>
        .elevation-z12 {
            box-shadow: 0px 7px 8px -4px rgba(0, 0, 0, 0.2), 0px 12px 17px 2px rgba(0, 0, 0, 0.14), 0px 5px 22px 4px rgba(0, 0, 0, 0.12);
        }

        <?=$slideshow_selector?>
        .elevation-z13 {
            box-shadow: 0px 7px 8px -4px rgba(0, 0, 0, 0.2), 0px 13px 19px 2px rgba(0, 0, 0, 0.14), 0px 5px 24px 4px rgba(0, 0, 0, 0.12);
        }

        <?=$slideshow_selector?>
        .elevation-z14 {
            box-shadow: 0px 7px 9px -4px rgba(0, 0, 0, 0.2), 0px 14px 21px 2px rgba(0, 0, 0, 0.14), 0px 5px 26px 4px rgba(0, 0, 0, 0.12);
        }

        <?=$slideshow_selector?>
        .elevation-z15 {
            box-shadow: 0px 8px 9px -5px rgba(0, 0, 0, 0.2), 0px 15px 22px 2px rgba(0, 0, 0, 0.14), 0px 6px 28px 5px rgba(0, 0, 0, 0.12);
        }

        <?=$slideshow_selector?>
        .elevation-z16 {
            box-shadow: 0px 8px 10px -5px rgba(0, 0, 0, 0.2), 0px 16px 24px 2px rgba(0, 0, 0, 0.14), 0px 6px 30px 5px rgba(0, 0, 0, 0.12);
        }

        <?=$slideshow_selector?>
        .elevation-z17 {
            box-shadow: 0px 8px 11px -5px rgba(0, 0, 0, 0.2), 0px 17px 26px 2px rgba(0, 0, 0, 0.14), 0px 6px 32px 5px rgba(0, 0, 0, 0.12);
        }

        <?=$slideshow_selector?>
        .elevation-z18 {
            box-shadow: 0px 9px 11px -5px rgba(0, 0, 0, 0.2), 0px 18px 28px 2px rgba(0, 0, 0, 0.14), 0px 7px 34px 6px rgba(0, 0, 0, 0.12);
        }

        <?=$slideshow_selector?>
        .elevation-z19 {
            box-shadow: 0px 9px 12px -6px rgba(0, 0, 0, 0.2), 0px 19px 29px 2px rgba(0, 0, 0, 0.14), 0px 7px 36px 6px rgba(0, 0, 0, 0.12);
        }

        <?=$slideshow_selector?>
        .elevation-z20 {
            box-shadow: 0px 10px 13px -6px rgba(0, 0, 0, 0.2), 0px 20px 31px 3px rgba(0, 0, 0, 0.14), 0px 8px 38px 7px rgba(0, 0, 0, 0.12);
        }

        <?=$slideshow_selector?>
        .elevation-z21 {
            box-shadow: 0px 10px 13px -6px rgba(0, 0, 0, 0.2), 0px 21px 33px 3px rgba(0, 0, 0, 0.14), 0px 8px 40px 7px rgba(0, 0, 0, 0.12);
        }

        <?=$slideshow_selector?>
        .elevation-z22 {
            box-shadow: 0px 10px 14px -6px rgba(0, 0, 0, 0.2), 0px 22px 35px 3px rgba(0, 0, 0, 0.14), 0px 8px 42px 7px rgba(0, 0, 0, 0.12);
        }

        <?=$slideshow_selector?>
        .elevation-z23 {
            box-shadow: 0px 11px 14px -7px rgba(0, 0, 0, 0.2), 0px 23px 36px 3px rgba(0, 0, 0, 0.14), 0px 9px 44px 8px rgba(0, 0, 0, 0.12);
        }

        <?=$slideshow_selector?>
        .elevation-z24 {
            box-shadow: 0px 11px 15px -7px rgba(0, 0, 0, 0.2), 0px 24px 38px 3px rgba(0, 0, 0, 0.14), 0px 9px 46px 8px rgba(0, 0, 0, 0.12);
        }

    </style>

	<?php


	$style = ob_get_clean();

	$slideshow = $style . $slideshow;


	return "<div id='{$atts['id']}' class='slideshow-wrapper'>{$slideshow}</div>";

}

function getHeightBasedOnAspectRation( $aspectRatio ) {
	switch ( $aspectRatio ) {
		case '1_1':
			return '100%';
		case '2_1':
			return '50%';
		case '4_3':
			return '75%';
		case '16_9':
			return '56.25%';
		case '1_2':
			return '200%';
		default:
			return null;
	}
}

function getCaptionFontColor( $captionColor ) {

	switch ( $captionColor ) {
		case 'light' :
			return '#FFFFFF';
		case 'dark' :
			return '#8C8C8C';
		default :
			return '#8C8C8C';
	}
}

function getCaptionFontSize( $captionFontSize ) {
	switch ( $captionFontSize ) {
		case 'default':
			return '.9em';
		case 'small':
			return '0.8em';
		case 'large':
			return '1.15em';
		default:
			return '.9em';
	}
}

function getCaptionPadding( $captionPadding ) {
	switch ( $captionPadding ) {
		case 'default':
			return '10px';
		case 'small':
			return '5px';
		case 'large':
			return '15px';
		default:
			return '10px';
	}
}

function getThumbnailsPadding( $thumbnailsPadding ) {
	switch ( $thumbnailsPadding ) {
		case 'default':
			return '20px 30px';
		case 'small':
			return '15px';
		case 'large':
			return '30px 45px';
		default:
			return '20px 30px';
	}
}

function getThumbnailPadding( $thumbnailPadding ) {
	switch ( $thumbnailPadding ) {
		case 'default':
			return '10px';
		case 'small':
			return '5px';
		case 'large':
			return '15px';
		default:
			return '10px';
	}
}
