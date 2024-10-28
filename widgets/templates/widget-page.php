<?php
/**
 * Template for displaying the Atom Builder Page Widget content
 *
 * @link    https://codex.wordpress.org/Template_Hierarchy
 * @var     array   $instance   The current instance of the widget
 * 
 * @package atom-builder
 */
?>
<article class="atom-builder-page-widget-entry">

    <header class="atom-builder-page-widget-header">
        <?php if ( empty( $instance['title'] ) ){
            the_title( '<h2 class="atom-builder-page-widget-entry-title">', '</h2>' ); 
        } else {
            the_title( '<h3 class="atom-builder-page-widget-entry-title">', '</h3>' );
        } ?>
    </header>

    <?php if ( has_post_thumbnail() && 'no-thumbnail' != $instance['thumbnail_option'] ): ?>
        <picture class="atom-builder-page-widget-thumbnail <?php echo esc_attr( $instance['thumbnail_option'] ); ?>">
            <?php the_post_thumbnail(); ?>
        </picture>
    <?php endif; ?>

    <div class="atom-builder-page-widget-content <?php echo esc_attr( $instance['content_size'] . ' ' . $instance['text_style'] ); ?>"> 
        <?php 
            if( $instance['display_full_page'] ) {
                the_content();
            } else {
                the_excerpt();
            }
        ?>
    </div>

</article>
