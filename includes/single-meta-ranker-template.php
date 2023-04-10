<?php
/***
 *
 *
 *  ========    EXPERIMENTAL    ==========
 *
 * Custom template for rendering shortcode preview at backend without loading WP theme
 *
 *
 *
 **/
?>

<?php get_header();?>

<?php
echo "<!-- THIS IS A CUSTOM TEMPLATE CREATED FOR `Meta Ranker` " . MRV_VERSION . " -->";
echo "<div style='position:relative;width:100%;'>";

echo do_shortcode('[meta-ranker id=' . esc_html($post->ID) . ']');

echo "</div>";

get_footer();