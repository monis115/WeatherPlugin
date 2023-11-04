<?php

namespace ExtendBuilder;

add_shortcode(prefix('render_js'), function($attrs, $content = "" ) {
    ob_start();
    
    $atts = shortcode_atts(
        array(
        	'deps'        => ""
        ),
        $attrs
    );

    $deps = explode(",", $atts['deps']);
    ?>
    	<script>
    		<?php echo strip_tags($content); ?>
    	</script>
    <?php
    $content = ob_get_clean();
    return $content;
});
