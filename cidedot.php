<?php 
/**
 * Plugin Name: Cidedot plugin
 * Plugin URI: https://cidedot.com/
 * Description: This is plugin for cidedot site functionalities
 * Version: 1.0
 * Author: Janne Seppänen
 * Author URI: https:/cidedot.com/
 **/

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Add options page 
if( function_exists('acf_add_options_page') ) {
    
    acf_add_options_page('Yritykset');
    
}

// CV Shortcut
function cidedot_cv() {
    
    if(is_admin()) 
        return; 

    $allowed = false;

    ob_start();
    
    $allowed = apply_filters('check_key', $allowed);
    $i = 0;

    ?>
    <div id="cv-content">
        <?php if ( have_rows('cv') ) : ?>       
            <?php while( have_rows('cv') ) : the_row(); ?>
                <?php if( in_array( 'all', get_sub_field('visible') ) || in_array( $allowed, get_sub_field('visible') ) ) { 
                    $title = get_sub_field('category', false);
                    $title_id = sanitize_title($title) . '-' . $i; ?>

                    <h2 id="<?php echo $title_id ?>"><?php echo $title ?></h2>
                    <?php if ( have_rows('category-content') ) :            
                        while( have_rows('category-content') ) : the_row();
                            if( in_array( 'all', get_sub_field('visible') )  || in_array( $allowed, get_sub_field('visible') ) ) { ?>
                                <?php if( get_sub_field('title') ) {
                                    $title = get_sub_field('title', false);
                                    $title_id = sanitize_title($title) . '-' . $i;?>
                                    
                                    <p id ="<?php echo $title_id ?>" class="cv-item-title"><strong><?php echo $title ?></strong>
                                <?php } ?>
                                <?php if( get_sub_field('sub_title') ) { ?>
                                    <?php echo (get_sub_field('title')) ? '<br />' : '<p>'; ?>
                                    <span class="cv-item-subtitle"><?php the_sub_field('sub_title', false); ?></span><br />
                                <?php } ?>
                                <?php if( get_sub_field('title') || get_sub_field('sub_title') ) { ?>
                                    </p> 
                                <?php } ?>
                                <?php if( get_sub_field('description') ) { ?>
                                    <?php the_sub_field('description'); ?></p>
                                <?php } ?>
                            <?php }
                            $i++;
                        endwhile; ?>  
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

// Filter cv topics by id and key;

function check_key($allowed) {
    
    if( isset($_GET['key']) && isset($_GET['id']) ) {
        
        global $wpdb;
        
        $key = $_GET['key'];
        $id = intval($_GET['id']);
        $option_name = 'options_firms_' . $id . '_key';

        /* Check key and id pair */
        $query = $wpdb->prepare(
            "SELECT option_name FROM $wpdb->options WHERE option_name = %s AND option_value = %s",
            $option_name,
            $key
        );

        $results = $wpdb->get_results( $query, ARRAY_A );
        
        if(!empty($results)) {
            
            $option_name = 'options_firms_' . $id . '_category';
            
            /* Get category */
            $query = $wpdb->prepare(
                "SELECT option_value FROM $wpdb->options
                WHERE option_name = %s",
                $option_name
            );
            
            $results = $wpdb->get_results( $query, ARRAY_A );

            $allowed = $results[0]['option_value'];
            return $allowed;
        } else { ?>
            <script>
                alert('Tunnistautuminen ei onnistunut. Varmista, että käytät oikeaa linkkiä. Ongelmatilanteessa voit ottaa yhteyttä sähköpostitse tai puhelimitse.');
            </script>
        <?php }
    } else { ?>
        <script>
            alert('Et ole tunnistautunut. Nähdäksesi täydellisen CV:n käytä autorisoitua linkkiä. Voit pyytää linkin minulta sähköpostitse.');
        </script>
    <?php }

    if( current_user_can( 'administrator' ) ) {
        return 'all';
    }

}

add_filter( 'check_key', 'check_key', 10, 1 );

?>