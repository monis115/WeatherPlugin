<?php
namespace ExtendBuilder;

add_shortcode('colibri_video_player', function ($atts) {
    ob_start();
   if($atts['type']==='external')
       colibri_html_embed_iframe( $atts['url'],$atts['autoplay']);
   else
       colibri_html_embed_video($atts['url'],$atts['attributes']);
   $content = ob_get_clean();
   return $content;
});

function colibri_html_embed_iframe($url,$autoplay){
    echo "<iframe src=".esc_url($url)." class='h-video-main'".(($autoplay === 'true') ? 'allow="autoplay"' : '')."  allowfullscreen></iframe>";
}

function colibri_html_embed_video($url,$attributes){
    echo "<video class='h-video-main' ".$attributes." ><source src=".esc_url($url)." type='video/mp4' /></video>";
}


