<?php 
/**
 * Plugin Name: Cidedot plugin
 * Plugin URI: https://cidedot.com/
 * Description: This is plugin for cidedot site functionalities
 * Version: 1.0
 * Author: Janne Seppänen
 * Author URI: https:/cidedot.com/
 **/


// Add options page 
if( function_exists('acf_add_options_page') ) {
    
    acf_add_options_page('Yritykset');
    
}

// CV Shortcut
function cidedot_cv() {
    
    if(is_admin()) 
        return; 

    $allowed = false;
    $allowed = apply_filters('check_key', $allowed);

    ob_start();

    ?>
    <div id="cv-content">
        <?php if ( have_rows('cv') ) : ?>       
            <?php while( have_rows('cv') ) : the_row(); ?>
                <?php if( in_array( 'all', get_sub_field('visible') ) || in_array( $allowed, get_sub_field('visible') ) ) { ?>
                    <h2><?php the_sub_field('category', false); ?></h2>
                    <?php if ( have_rows('category-content') ) : ?>                        
                        <?php while( have_rows('category-content') ) : the_row(); ?>
                            <?php if( in_array( 'all', get_sub_field('visible') )  || in_array( $allowed, get_sub_field('visible') ) ) { ?>
                                <?php if( get_sub_field('title') ) { ?>
                                    <p><strong><?php the_sub_field('title', false); ?></strong><br />
                                <?php } ?>
                                <?php if( get_sub_field('sub_title') ) { ?>
                                    <?php if( !get_sub_field('title') ) { ?>
                                        <p>
                                    <?php } else { ?>
                                        <br/>
                                    <?php } ?>
                                    <?php the_sub_field('sub_title', false); ?></strong><br />
                                <?php } ?>
                                <?php if( get_sub_field('title') || get_sub_field('sub_title') ) { ?>
                                    </p> 
                                <?php } ?>
                                <?php if( get_sub_field('description') ) { ?>
                                    <?php the_sub_field('description'); ?></p>
                                <?php } ?>
                            <?php } ?>
                        <?php endwhile; ?>  
                    <?php endif; ?>
                <?php } ?>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>
    
    <?php $output = ob_get_contents();
    ob_end_clean();

    return $output;

}

add_shortcode( 'cidedot-cv', 'cidedot_cv' );

// Filtering cv topics by id and key;

function check_key($allowed) {
    
    if( isset($_GET['key']) && isset($_GET['id']) ) {
        
        global $wpdb;
        
        $key = $_GET['key'];
        $id = intval($_GET['id']);
        $option_name = 'options_firms_' . $id . '_key';

        /* Check key and id pair */
        $query = $wpdb->prepare(
            "SELECT option_name FROM $wpdb->options
            WHERE option_name = %s AND 
            option_value = %s",
            $option_name,
            $key,
        );

        $results = $wpdb->get_results( $query, ARRAY_A );
        
        if(!empty($results)) {
            
            $option_name = 'options_firms_' . $id . '_category';
            
            /* Get category */
            $query = $wpdb->prepare(
                "SELECT option_value FROM $wpdb->options
                WHERE option_name = %s",
                $option_name,
            );
            
            $results = $wpdb->get_results( $query, ARRAY_A );

            $allowed = $results[0]['option_value'];
            return $allowed;
        }
    }

    if( current_user_can( 'administrator' ) ) {
        return 'all';
    }

}

add_filter( 'check_key', 'check_key', 10, 1 );

?>