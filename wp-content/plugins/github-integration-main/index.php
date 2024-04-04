<?php 
/*
 * Plugin Name: github integration take 2
 */
add_action( 'wp_dashboard_setup', function() {
	wp_add_dashboard_widget(
		'test_admin_take2',
		'Github Integration Take 2',
        function() {
            echo '<x-githubintegration2 />';
        }
	);
});

add_action( 'admin_enqueue_scripts', function($hook){
    if( 'index.php' != $hook ) {
		return;
	}
    wp_enqueue_script('custom_javascript2', 'https://standalone.wp-mfe.pages.dev/bundle.js');
} );
