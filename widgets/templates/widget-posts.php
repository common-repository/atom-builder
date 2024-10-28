<?php
/**
 * Template for displaying the Atom Posts Widget content
 *
 * @link    https://codex.wordpress.org/Template_Hierarchy
 * @var     array   $instance   The current instance of the widget
 * 
 * @package atom-builder
 */
?>

<article class="atom-builder-posts-widget-entry <?php echo esc_attr( $instance['num_posts'] . '-posts' ) ?>">
    
    <header class="atom-builder-posts-widget-header">
        <?php if ( empty( $instance['title'] ) ) {
            the_title( '<h2 class="atom-builder-posts-widget-entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
        } else {
            the_title( '<h3 class="atom-builder-posts-widget-entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h3>' );
        } ?>

        <?php if ( $instance['display_meta'] ): ?>
            <p class="atom-builder-posts-widget-meta"><?php atom_builder_entry_meta(); ?></p>
        <?php endif; ?>
    </header>

    <?php if ( has_post_thumbnail() && $instance['display_thumbnail'] ): ?>
        <picture class="atom-builder-posts-widget-thumbnail">
            <?php the_post_thumbnail( 'widget-thumbnail' ); ?>
        </picture>
    <?php endif; ?>

    <?php if ( $instance['display_excerpt'] ) : ?>   
        <div class="atom-builder-posts-widget-entry-content">
            <?php the_excerpt(); ?>
        </div>
    <?php endif; ?>

</article>