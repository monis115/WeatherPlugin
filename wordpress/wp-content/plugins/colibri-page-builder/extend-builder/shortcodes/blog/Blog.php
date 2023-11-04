<?php

namespace ExtendBuilder;


class Blog
{
    
    private static $instance;
    private        $partialsHandler = array(
        'image'       => 'printPostAttachment',
        'category'    => 'printPostCategory',
        'meta-data'   => 'printPostMetadata',
        'title'       => 'printPostTitle',
        'excerpt'     => 'printPostContent', // - backwards compatibility
        'content'     => 'printPostContent',
        'read-button' => 'printPostReadMore',
    );


    private $metadataIcons = array(
        'time'     => '<svg width="24px" height="24px" class="svg-inline--fa fa-clock" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"> <path fill="currentColor" d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8zm0 448c-110.5 0-200-89.5-200-200S145.5 56 256 56s200 89.5 200 200-89.5 200-200 200zm61.8-104.4l-84.9-61.7c-3.1-2.3-4.9-5.9-4.9-9.7V116c0-6.6 5.4-12 12-12h32c6.6 0 12 5.4 12 12v141.7l66.8 48.6c5.4 3.9 6.5 11.4 2.6 16.8L334.6 349c-3.9 5.3-11.4 6.5-16.8 2.6z"></path></svg>',
        'author'   => '<svg width="24px" height="24px" class="svg-inline--fa fa-user" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"> <path fill="currentColor" d="M313.6 304c-28.7 0-42.5 16-89.6 16-47.1 0-60.8-16-89.6-16C60.2 304 0 364.2 0 438.4V464c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48v-25.6c0-74.2-60.2-134.4-134.4-134.4zM400 464H48v-25.6c0-47.6 38.8-86.4 86.4-86.4 14.6 0 38.3 16 89.6 16 51.7 0 74.9-16 89.6-16 47.6 0 86.4 38.8 86.4 86.4V464zM224 288c79.5 0 144-64.5 144-144S303.5 0 224 0 80 64.5 80 144s64.5 144 144 144zm0-240c52.9 0 96 43.1 96 96s-43.1 96-96 96-96-43.1-96-96 43.1-96 96-96z"></path></svg>',
        'comments' => '<svg width="24px" height="24px" class="svg-inline--fa fa-comment" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"> <path fill="currentColor" d="M256 32C114.6 32 0 125.1 0 240c0 47.6 19.9 91.2 52.9 126.3C38 405.7 7 439.1 6.5 439.5c-6.6 7-8.4 17.2-4.6 26S14.4 480 24 480c61.5 0 110-25.7 139.1-46.3C192 442.8 223.2 448 256 448c141.4 0 256-93.1 256-208S397.4 32 256 32zm0 368c-26.7 0-53.1-4.1-78.4-12.1l-22.7-7.2-19.5 13.8c-14.3 10.1-33.9 21.4-57.5 29 7.3-12.1 14.4-25.7 19.9-40.2l10.6-28.1-20.6-21.8C69.7 314.1 48 282.2 48 240c0-88.2 93.3-160 208-160s208 71.8 208 160-93.3 160-208 160z"></path></svg>',
        'date'     => '<svg width="24px" height="24px" class="svg-inline--fa fa-calendar"  xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"> <path fill="currentColor" d="M400 64h-48V12c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v52H160V12c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v52H48C21.5 64 0 85.5 0 112v352c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48V112c0-26.5-21.5-48-48-48zm-6 400H54c-3.3 0-6-2.7-6-6V160h352v298c0 3.3-2.7 6-6 6z"></path></svg>',

    );

    public function __construct()
    {
       // to make a shortcode from a function that prints content use ob_wrap
       // add_shortcode('colibri_archive_loop', array($this, 'archiveLoop'));
       // add_shortcode('colibri_blog_posts', array($this, 'blogPosts'));
       // add_shortcode('colibri_single_post', array($this, 'singlePost'));

    }

    public static function run()
    {
        if (!static::$instance) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    public function archiveLoop($atts)
    {

        $atts = $this->normalizeLoopAtts($atts, array(
            'classes_row_inside'  => 'h-row,align-items-stretch,gutters-col-lg-0,gutters-col-md-0,gutters-col-0',
            'classes_row_outside' => 'h-row-container,gutters-row-lg-0,gutters-row-md-0,gutters-row-0',
        ));

        ob_start(); ?>

        <div class="<?php echo str_replace(',', ' ', $atts['classes_row_outside']) ?>">
            <div class="<?php echo str_replace(',', ' ', $atts['classes_row_inside']) ?>">
                <?php
                if (have_posts()):
                    while (have_posts()):
                        the_post();
                        $this->printArchiveLoopItem($atts);
                    endwhile;
                else:
                    get_template_part('template-parts/content', 'none');
                endif;
                ?>
            </div>
        </div>
        <?php


        return ob_get_clean();
    }

    protected function normalizeLoopAtts($atts, $defaults = array())
    {
        $defaults = array_merge(
            array(
                'classes_row_inside'       => '',
                'classes_row_outside'      => '',
                //
                'columns_desktop'          => "12",
                'columns_tablet'           => "12",
                //
                'title_type'               => 'h6',
                'excerpt_length'           => '55',
                //
                'metadata_order'           => '',
                'post_order'               => 'image,category,meta-data,title,content,read-button',
                'single_post'              => null,
                //
                'show_placeholder'         => 'true',
                'placeholder_color'        => 'rgb(255,127,80)',
                //
                'spacer_metadata'          => 'true',
                'metadata_separator'       => '|',
                //
                'show_read_more_button'    => 'true',
                'button_text'              => 'Read more',
                //
                'horizontal_content_align' => 'text-left',
            ),
            $defaults
        );

        if (is_singular() && $defaults['single_post'] === null) {
            $defaults['single_post'] = is_singular();
        }

        return shortcode_atts($defaults, $atts);
    }

    protected function printArchiveLoopItem($atts = array())
    {
        $cols_desktop = intval($atts['columns_desktop']);
        $cols_tablet  = intval($atts['columns_tablet']);
        $post_order   = explode(",", $atts['post_order']);

        ?>
        <div class="h-column h-column-container d-flex masonry-item h-col-lg-<?php echo esc_attr($cols_desktop); ?> h-col-md-<?php echo esc_attr($cols_tablet); ?>  h-col-xs-12"
             style="position: relative">
            <div class="d-flex h-flex-basis h-column__inner">
                <div class="h-column__content align-self-stretch" style="width: 100%;">
                    <div id="post-<?php the_ID(); ?>"
                         class="colibri_blog_post <?php echo esc_attr($atts['horizontal_content_align']); ?>">

                        <?php $this->displayPartialIfEnabled('image', $post_order, $atts); ?>
                        <div class="colibri-post-content-area">
                            <?php foreach ($post_order as $to_print): ?>
                                <?php $this->displayPartial($to_print, $atts); ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    public function displayPartialIfEnabled($partial, &$list, $atts = array())
    {
        if (in_array($partial, $list)) {
            $list = array_diff($list, array($partial));
            $this->displayPartial($partial, $atts);
        }
    }

    public function displayPartial($partial = "", $atts = array())
    {
        $handle = $this->partialsHandler[$partial];

        if (method_exists($this, $handle)) {
            call_user_func(array($this, $handle), $atts);
        }
    }

    public function singlePost($atts)
    {

        apply_customizer_preview_context();

        $atts = $this->normalizeLoopAtts($atts, array(
            'show_navigation'     => 'true',
            'next_post'           => 'Next post:',
            'prev_post'           => 'Previous post:',
            'classes_row_inside'  => 'h-row,align-items-stretch,gutters-col-lg-0,gutters-col-md-0,gutters-col-0',
            'classes_row_outside' => 'h-row-container,gutters-row-lg-0,gutters-row-md-0,gutters-row-0',
        ));

        ob_start();
        ?>

        <div class="<?php echo str_replace(',', ' ', $atts['classes_row_outside']) ?>">
            <div class="<?php echo str_replace(',', ' ', $atts['classes_row_inside']) ?>">
                <?php
                if (have_posts()):
                    while (have_posts()):
                        the_post();
                        $this->printArchiveLoopItem($atts);
                        $this->printPostNavigation($atts);
                    endwhile;
                else:
                    get_template_part('template-parts/content', 'none');
                endif;
                ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    public function blogPosts($atts)
    {
        ob_start();

        $atts = $this->normalizeLoopAtts($atts, array(
            'columns_desktop'   => "4",
            'columns_tablet'    => "6",
            'posts'             => '3',
            'filter_categories' => '',
            'filter_tags'       => '',
            'filter_authors'    => '',
            'order_by'          => 'date',
            'order_type'        => 'ASC',
        ));

        $cols_desktop = intval($atts['columns_desktop']);
        $post_numbers = ($atts['posts']) ? $atts['posts'] : 12 / $cols_desktop;

        $query = new \WP_Query(array(
            'posts_per_page' => $post_numbers,
            'category_name'  => $atts['filter_categories'],
            'tag'            => $atts['filter_tags'],
            'author'         => $atts['filter_authors'],
            'orderby'        => $atts['order_by'],
            'order'          => $atts['order_type'],
        ));

        ?>
        ?>
        <div class="<?php echo str_replace(',', ' ', $atts['classes_row_outside']) ?>">
            <div class="<?php echo str_replace(',', ' ', $atts['classes_row_inside']) ?>">
                <?php
                if ($query->have_posts()):
                    while ($query->have_posts()):
                        $query->the_post();

                        if (is_sticky()) {
                            continue;
                        }
                        $this->printArchiveLoopItem($atts);
                    endwhile;
                    wp_reset_postdata();
                else:
                    ?>
                    <div style="text-align: center; width: 100%">No posts found</div>
                <?php
                endif;
                ?>
            </div>
        </div>
        <?php

        return ob_get_clean();
    }

    public function addPostContentFilters($content)
    {
        add_filter('the_content', 'wpautop');

        return $content;
    }

    public function printPostMetadata($atts)
    {
        $metadata_order = explode(",", $atts['metadata_order']);
        $wrapper_class  = array(
            "colibri_post_metadata",
        );

        $current_side = 'left';
        $content      = array(
            'left'  => array(),
            'right' => array(),
        );

        foreach ($metadata_order as $item) {
            if ($item === 'spacer') {
                $current_side = 'right';
            } else {
                if ($item_content = $this->getMetadataItemContent($item)) {
                    $content[$current_side][] = $item_content;
                }
            }
        }

        if (is_true($atts['spacer_metadata'])) {
            $wrapper_class[] = 'd-flex';
        }

        $separator = "<span>" . $atts['metadata_separator'] . "</span>";

        $content['left']  = "<div class=\"left d-block\" >" . implode($separator, $content['left']) . "</div>";
        $content['right'] = "<div class=\"right d-block\" >" . implode($separator, $content['right']) . "</div>";

        if (is_true($atts['spacer_metadata'])) {
            $content = implode('<div class="spacer d-flex" style="margin:auto"></div>', $content);
        } else {
            $content = $content['left'];
        }

        ?>
        <div class="<?php echo esc_attr(implode(' ', $wrapper_class)); ?>">
            <?php echo $content; ?>
        </div>
        <?php
    }

    public function getMetadataItemContent($metadata)
    {
        $template = '' .
            '<a class="colibri_post_%s d-inline-block" href="%s">' .
            '%s<span class="d-inline-block">%s</span>' .
            '</a>';

        $content = '';

        switch ($metadata) {
            case 'time':
                $content = sprintf($template,
                    $metadata,
                    '#',
                    $this->metadataIcons[$metadata],
                    get_the_time()
                );
                break;
            case 'date':
                $content = sprintf($template,
                    $metadata,
                    esc_url(get_day_link(get_post_time('Y'), get_post_time('m'), get_post_time('j'))),
                    $this->metadataIcons[$metadata],
                    get_the_date(get_option('date_format'))
                );
                break;

            case 'author':
                $content = sprintf($template,
                    $metadata,
                    esc_url(get_author_posts_url(get_the_author_meta('ID'))),
                    $this->metadataIcons[$metadata],
                    get_the_author()
                );
                break;

            case 'comments':
                $content = sprintf($template,
                    $metadata,
                    esc_url(get_comments_link()),
                    $this->metadataIcons[$metadata],
                    get_comments_number()
                );
                break;
        }
        return $content;
    }

    public function printPostTitle($atts)
    {
        // ignore PHPStorm error 'Closing tag name missing'
        $title_template = "<a href=\"%3\$s\"><%1\$s>%2\$s</%1\$s></a>";

        ?>
        <div class="colibri_post_title">
            <?php printf($title_template,
                $atts['title_type'],
                get_the_title(),
                esc_url(get_the_permalink())
            ); ?>
        </div>
        <?php
    }

    public function printPostReadMore($atts)
    {
        if (is_false($atts['single_post'])) {
            printf('<a class="colibri_post_read_more d-inline-flex" href="%s">%s</a>',
                esc_url(get_permalink()),
                $atts['button_text']
            );
        }
    }

    protected function printPostNavigation($atts)
    {
        $prev_post = get_previous_post();
        $next_post = get_next_post();

        if (!($prev_post !== '' || $next_post !== '')) {
            if (is_customize_preview()) {
                if ($atts['show_navigation'] === 'true') {
                    echo '<nav class="navigation post-navigation" role="navigation">
                                      <div class="nav-links">
                                        <div class="nav-previous">
                                            <a href="" rel="prev"><i class="font-icon-post fa fa-angle-double-left"></i>
                                                <span class="meta-nav" aria-hidden="true">' . esc_html__($atts['prev_post']) . '</span>
                                                <span class="screen-reader-text">' . esc_html__($atts['prev_post']) . '</span>
                                                <span class="post-title">Test previous post</span>
                                            </a>
                                        </div>
                                        <div class="nav-next">
                                            <a href="" rel="next">
                                                <span class="meta-nav" aria-hidden="true">' . esc_html__($atts['next_post']) . '</span>
                                                <span class="screen-reader-text">' . esc_html__($atts['next_post']) . '</span>
                                                <span class="post-title">Test next post</span>
                                                <i class="font-icon-post fa fa-angle-double-right"></i>
                                            </a>
                                        </div>
                                      </div>
                                      </nav>';
                }
            }
        }
        if ($atts['show_navigation'] === 'true') {
            the_post_navigation(array(
                'next_text' => '<span class="meta-nav" aria-hidden="true">' . esc_html__($atts['next_post']) . '</span> ' .
                    '<span class="screen-reader-text">' . esc_html__($atts['next_post']) . '</span> ' .
                    '<span class="post-title">%title</span><i class="font-icon-post fa fa-angle-double-right"></i>',
                'prev_text' => '<i class="font-icon-post fa fa-angle-double-left"></i>' .
                    '<span class="meta-nav" aria-hidden="true">' . esc_html__($atts['prev_post']) . '</span> ' .
                    '<span class="screen-reader-text">' . esc_html__($atts['prev_post']) . '</span> ' .
                    '<span class="post-title">%title</span>',
            ));
        }
    }

    protected function printPostAttachment($atts)
    {
        ?>
        <div class="colibri_post_thumb">
            <?php if (has_post_thumbnail()): ?>
                <?php $this->printPostThumbnail($atts); ?>
            <?php else: ?>
                <?php $this->printPostThumbnailPlaceholder($atts); ?>
            <?php endif; ?>
        </div>
        <?php
    }

    protected function printPostThumbnail($atts)
    {
        if (is_false($atts['single_post'])) : ?>
            <a href="<?php the_permalink(); ?>">
                <?php the_post_thumbnail(null, array('class' => 'w-100',)); ?>
            </a>
        <?php else: ?>
            <?php the_post_thumbnail(null, array('class' => 'w-100',)); ?>
        <?php endif;
    }

    protected function printPostThumbnailPlaceholder($atts)
    {
        if (is_true($atts['show_placeholder'])) : ?>
            <?php if (is_true($atts['single_post'])) : ?>
                <div class="colibri_placeholder_image">
                    <div style="background-color:<?php echo esc_attr($atts['placeholder_color']); ?> "></div>
                </div>
            <?php else: ?>
                <a href="<?php the_permalink(); ?>">
                    <div class="colibri_placeholder_image">
                        <div style="background-color:<?php echo esc_attr($atts['placeholder_color']); ?> "></div>
                    </div>
                </a>
            <?php endif; ?>
        <?php endif;
    }

    protected function printPostCategory($atts)
    {
        $categories   = get_the_category();
        $linkTemplate = '<a href="%1$s"  class="colibri_category_button">%2$s</a>';
        if (!count($categories)) {
            return;
        }

        ?>
        <div class="colibri_post_category">
            <?php


            foreach ($categories as $category) {
                printf($linkTemplate,
                    esc_url(get_category_link($category->term_id)),
                    esc_html($category->name)
                );
            }

            ?>
        </div>
        <?php
    }

    protected function printPostContent($atts)
    {
        if (is_true($atts['single_post'])) : ?>
            <div class="colibri_post_content">
                <?php
                add_filter('the_content', array($this, 'addPostContentFilters'), 5);
                the_content();
                $this->removePostContentFilters();
                ?>
            </div>
        <?php else: ?>
            <div class="colibri_post_excerpt">
                <?php
                add_filter('the_content', 'wpautop');
                the_excerpt();
                remove_filter('the_content', 'wpautop');
                ?>
            </div>
        <?php endif;
    }

    public function removePostContentFilters()
    {
        if (false !== has_filter('the_content', 'wpautop')) {
            remove_filter('the_content', 'wpautop');
        }

    }

}
