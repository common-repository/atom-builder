<?php
/**
 * Template for displaying the Atom Builder Post Widget content
 *
 * @link    https://codex.wordpress.org/Template_Hierarchy
 * @var     array   $instance   The current instance of the widget
 * 
 * @package atom-builder
 */
?>

<article class="atom-builder-post-widget-entry">

    <header class="atom-builder-post-widget-header">   
        <?php if ( empty( $instance['title'] ) ) {
            the_title( '<h2 class="atom-builder-post-widget-entry-title">', '</h2>' );
        } else {
            the_title( '<h3 class="atom-builder-post-widget-entry-title">', '</h3>' );
        } ?>   

        <?php if ( $instance['display_meta'] ): ?>
            <p class="atom-builder-post-widget-meta"><?php atom_builder_entry_meta(); ?></p>
        <?php endif; ?>
    </header>

    <?php if ( has_post_thumbnail() && 'no-thumbnail' != $instance['thumbnail_option'] ): ?>
        <picture class="atom-builder-post-widget-thumbnail <?php echo esc_attr( $instance['thumbnail_option'] ); ?>">
            <?php the_post_thumbnail(); ?>
        </picture>
    <?php endif; ?>

    <div class="atom-builder-post-widget-content <?php echo esc_attr( $instance['content_size'] . ' ' . $instance['text_style'] ); ?>">
        <?php the_excerpt(); ?>					
    </div>

</article>
