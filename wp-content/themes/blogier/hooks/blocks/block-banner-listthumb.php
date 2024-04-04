<?php //Banner 4 Post 
if (is_front_page() || is_home()) {
       
        $number_of_posts = '4';
        $blogus_slider_category = blogus_get_option('select_recent_news_category');
        $blogus_all_posts_main = blogus_get_posts($number_of_posts, $blogus_slider_category);
        if ($blogus_all_posts_main->have_posts()) :
        while ($blogus_all_posts_main->have_posts()) : $blogus_all_posts_main->the_post();
        global $post;
        $blogus_url = blogus_get_freatured_image_url($post->ID, 'blogus-slider-full');
        ?>
                        
          <div class="col-md-6">
              <!-- // small-post -->
              <div class="bs-blog-post three sm back-img bshre" style="background-image: url('<?php echo esc_url($blogus_url); ?>');">
                   <a href="<?php the_permalink(); ?>" class="link-div"></a>
                <div class="inner">
                  <h4 class="title"> <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                </div>
              </div>
              <!-- // small-post -->
           </div>
<?php
    endwhile;
endif;
wp_reset_postdata();
?>
<?php }