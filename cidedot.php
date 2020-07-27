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
                                <p><strong><?php the_sub_field('title', false); ?></strong><br />
                                <?php the_sub_field('description', false); ?></p>
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

/*if( function_exists('acf_add_local_field_group') ):

    acf_add_local_field_group(array(
        'key' => 'group_5f16c5f22af60',
        'title' => 'CV',
        'fields' => array(
            array(
                'key' => 'field_5f16c5fa41a0d',
                'label' => 'CV',
                'name' => 'cv',
                'type' => 'repeater',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'collapsed' => '',
                'min' => 0,
                'max' => 0,
                'layout' => 'table',
                'button_label' => '',
                'sub_fields' => array(
                    array(
                        'key' => 'field_5f16c62041a0f',
                        'label' => 'Kategoria',
                        'name' => 'category',
                        'type' => 'text',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'maxlength' => '',
                    ),
                    array(
                        'key' => 'field_5f1929d745cb6',
                        'label' => 'Näkyvissä',
                        'name' => 'visible',
                        'type' => 'checkbox',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'choices' => array(
                            'dev' => 'Dev',
                            'music' => 'Musiikki',
                            'all' => 'kaikki',
                        ),
                        'allow_custom' => 0,
                        'default_value' => array(
                        ),
                        'layout' => 'vertical',
                        'toggle' => 0,
                        'return_format' => 'value',
                        'save_custom' => 0,
                    ),
                    array(
                        'key' => 'field_5f16c67541a10',
                        'label' => 'Kategorian sisältö',
                        'name' => 'category-content',
                        'type' => 'repeater',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'collapsed' => '',
                        'min' => 0,
                        'max' => 0,
                        'layout' => 'table',
                        'button_label' => '',
                        'sub_fields' => array(
                            array(
                                'key' => 'field_5f16c8b1c0afd',
                                'label' => 'Otsikko',
                                'name' => 'title',
                                'type' => 'text',
                                'instructions' => '',
                                'required' => 0,
                                'conditional_logic' => 0,
                                'wrapper' => array(
                                    'width' => '',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'default_value' => '',
                                'placeholder' => '',
                                'prepend' => '',
                                'append' => '',
                                'maxlength' => '',
                            ),
                            array(
                                'key' => 'field_5f16c6b041a11',
                                'label' => 'Kuvaus',
                                'name' => 'description',
                                'type' => 'wysiwyg',
                                'instructions' => '',
                                'required' => 0,
                                'conditional_logic' => 0,
                                'wrapper' => array(
                                    'width' => '',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'default_value' => '',
                                'tabs' => 'all',
                                'toolbar' => 'full',
                                'media_upload' => 1,
                                'delay' => 0,
                            ),
                            array(
                                'key' => 'field_5f16c6f241a12',
                                'label' => 'Näkyvissä',
                                'name' => 'visible',
                                'type' => 'checkbox',
                                'instructions' => '',
                                'required' => 0,
                                'conditional_logic' => 0,
                                'wrapper' => array(
                                    'width' => '',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'choices' => array(
                                    'dev' => 'Dev',
                                    'music' => 'Musiikki',
                                    'all' => 'kaikki',
                                ),
                                'allow_custom' => 0,
                                'default_value' => array(
                                ),
                                'layout' => 'vertical',
                                'toggle' => 0,
                                'return_format' => 'value',
                                'save_custom' => 0,
                            ),
                        ),
                    ),
                ),
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'page',
                    'operator' => '==',
                    'value' => '6',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => true,
        'description' => '',
    ));
    
    acf_add_local_field_group(array(
        'key' => 'group_5f19151e9a440',
        'title' => 'Yritykset',
        'fields' => array(
            array(
                'key' => 'field_5f1915b8fc0cd',
                'label' => 'Yritykset',
                'name' => 'firms',
                'type' => 'repeater',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'collapsed' => '',
                'min' => 0,
                'max' => 0,
                'layout' => 'table',
                'button_label' => '',
                'sub_fields' => array(
                    array(
                        'key' => 'field_5f1915d1fc0ce',
                        'label' => 'Nimi',
                        'name' => 'name',
                        'type' => 'text',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'maxlength' => '',
                    ),
                    array(
                        'key' => 'field_5f1915e1fc0cf',
                        'label' => 'Avain',
                        'name' => 'key',
                        'type' => 'text',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'maxlength' => '',
                    ),
                    array(
                        'key' => 'field_5f1915f0fc0d0',
                        'label' => 'Kategoria',
                        'name' => 'category',
                        'type' => 'select',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'choices' => array(
                            'dev' => 'Devaus',
                            'music' => 'Musiikki',
                        ),
                        'default_value' => array(
                        ),
                        'allow_null' => 0,
                        'multiple' => 0,
                        'ui' => 0,
                        'return_format' => 'value',
                        'ajax' => 0,
                        'placeholder' => '',
                    ),
                    array(
                        'key' => 'field_5f191687d82a0',
                        'label' => 'Tietoja',
                        'name' => 'tietoja',
                        'type' => 'wysiwyg',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'tabs' => 'all',
                        'toolbar' => 'full',
                        'media_upload' => 1,
                        'delay' => 0,
                    ),
                ),
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'options_page',
                    'operator' => '==',
                    'value' => 'acf-options-yritykset',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => true,
        'description' => '',
    ));
    
endif; */

?>