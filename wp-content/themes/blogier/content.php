<?php
/**
 * The template for displaying the content.
 * @package Blogier
 */
?>
<div class="bs-content-list">
    <?php while(have_posts()){ the_post(); ?>        
        <div id="post-<?php the_ID(); ?>" <?php post_class('bs-blog-post list-blog'); ?>>
            <?php  
            $url = blogus_get_freatured_image_url($post->ID, 'blogus-medium');
                blogus_post_image_display_type($post); 
            ?>
            <article class="small text-xs<?php if(empty($url)){ echo' p-0';} ?>">
              <?php 
                    $blogus_global_category_enable = get_theme_mod('blogus_global_category_enable','true');
                    if($blogus_global_category_enable == 'true') { ?>
                        <?php blogus_post_categories(); ?>
                    <?php } ?>
                    <h4 class="title"><a href="<?php the_permalink();?>"><?php the_title();?></a></h4>
                    <?php blogus_post_meta(); ?>
                    <?php blogier_blog_content(); 
                    $blogus_readmore_excerpt=get_theme_mod('blogus_blog_content','excerpt');
                    if ($blogus_readmore_excerpt=="excerpt") { ?>
                    <a href="<?php the_permalink();?>" class="more-link"><?php echo esc_html('Read More', 'blogier'); ?></a>
                    <?php } ?>
            </article>
          </div>
    <!-- // bs-posts-sec block_6 -->
    <?php } blogus_page_pagination(); ?>
</div>