<?php get_header(); ?>

    <div class="row">
        <div class="col-sm-12">
            <?php 
                $args = array(
                    'post_type' => 'my-custom-post',
                    'orderby' => 'menu_order',
                    'order' => 'ASC'
                );
                $custom_query = new WP_Query( $args );
                if ( $your_loop->have_posts() ) : while ($custom_query->have_posts()) : $custom_query->the_post();
                $meta = get_post_meta( $post->ID, 'my_fields', true );
            ?>
                <div class="blog-post">
                    <h2 class="blog-post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                    <?php the_excerpt(); ?>
                <?php endwhile; endif; wp_reset_postdata(); ?>
        </div>
    </div>

<?php get_footer(); ?>