<?php
/**
 * The template for displaying the content.
 * @package Blogus
 */
$layout = esc_attr(get_theme_mod('blogus_content_layout','grid-right-sidebar')) == 'grid-fullwidth' ? '3': '2'; ?> 
<div id="grid" class="bs-content-grid column<?php echo esc_attr($layout)?>">
    <?php while(have_posts()){ the_post();  ?>
        <div id="post-<?php the_ID(); ?>" <?php post_class('bs-blog-post grid-blog'); ?>> 
            <?php   $url = blogus_get_freatured_image_url($post->ID, 'blogus-medium');
            blogus_post_image_display_type($post); 
            ?>
            <article class="small">
                <?php 
                $blogus_global_category_enable = get_theme_mod('blogus_global_category_enable','true');
                if($blogus_global_category_enable == 'true') { blogus_post_categories(); } ?>
                <h4 class="title"><a href="<?php the_permalink();?>"><?php the_title();?></a></h4>
                <?php blogus_post_meta();  
                blogus_posted_content();  
                $blogus_readmore_excerpt=get_theme_mod('blogus_blog_content','excerpt');
                if ($blogus_readmore_excerpt=="excerpt") { ?>
                <a href="<?php the_permalink();?>" class="more-link"><?php echo esc_html('Read More', 'blogier'); ?></a>
                <?php } ?>
            </article>
        </div> 
    <?php } ?>
</div>
<div class="col-lg-12 text-center d-md-flex justify-content-center mt-5">
    <?php //Previous / next page navigation
        the_posts_pagination( array(
        'prev_text'          => '<i class="fa fa-angle-left"></i>',
        'next_text'          => '<i class="fa fa-angle-right"></i>',
        ) ); 
    ?>
</div>