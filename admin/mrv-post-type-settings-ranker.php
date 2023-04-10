<?php

/**
 *
 * This file is responsible for creating all admin settings in Timeline Builder (post)
 */
if (!defined("ABSPATH")) {
    exit('Can not load script outside of WordPress Enviornment!');
}
$activated = get_option('metaRankerActivated');
//$activated = "gg";

// if(!$activated ){

//     //
// // Create a metabox
// $plugin_not_active="mrv_not_active_notice";
//     CSF::createMetabox($plugin_not_active, array(
//         'title' => 'Plugin Not Activated',
//         'post_type' => 'meta-ranker',
//         'context' => 'advanced', // The context within the screen where the boxes should display. `normal`, `side`, `advanced`
//     ));
//     //
//     // Create a section
//     CSF::createSection($plugin_not_active, array(
//         'title' => '',
//         'fields' => array(
//                     array(
//             'type'    => 'notice',
//             'style'   => 'warning',
//             'content' => 'Please activate the plugin to use these features <a href="'.admin_url('edit.php?post_type=meta-ranker&page=metaranker-activation').'">Link</a>',
//             ),
//         ),
//     ));



// }
// else{
$post_array = ["meta-ranker"];

if (class_exists('CSF_Setup') && array(mrv_get_cpt(), $post_array)):



//
// Metabox of the PAGE
// Set a unique slug-like ID
//
    $prefix_page_opts = 'mrv_post_settinga';
    // require_once MRV_PATH . 'includes/db/mrv-db.php';

    // $dbobj = new MRV_Database();
    $post_idd = isset($_GET['post']) ? sanitize_text_field($_GET['post']) : '';
    $list_data = get_post_meta($post_idd, 'mrv_post_settinga', true);
    $items = !empty($list_data['mrv-item']) ? $list_data['mrv-item'] : '';

    $tabel_body = "";
    if (is_array($items)) {
        foreach ($items as $key => $value) {
            $itemtitle = $value['item-multiple-settings']['item-title'];

            $uniq_id = mrv_clean_sc($itemtitle);
            $total_votes = get_post_meta($post_idd, 'mrv_total_votes' . $uniq_id, true);
            $total_votes = !empty($total_votes) ? $total_votes : '--';

            $up_vote = get_post_meta($post_idd, 'mrv_up_votes' . $uniq_id, true);
            $up_vote = !empty($up_vote) ? $up_vote : '--';

            $down_vote = get_post_meta($post_idd, 'mrv_down_votes' . $uniq_id, true);
            $down_vote = !empty($down_vote) ? $down_vote : '--';

            $tabel_body .= ' <tr>
		    <td>' . $key . '</td>
		      <td>' . $itemtitle . '</td>
		    <td>' . $up_vote . '</td>
		    <td>' . $down_vote . '</td>
		    <td>' . $total_votes . '</td>
		  </tr>';

        }
    }

//
// Create a metabox
//

    CSF::createMetabox($prefix_page_opts, array(
        'title' => __('Meta Ranker', 'cptbx'),
        'post_type' => 'meta-ranker',
        'data_type' => 'serialize',
        'output_css' => true,
        'nav' => 'inline',
        'context' => 'advanced', // The context within the screen where the boxes should display. `normal`, `side`, `advanced`
        'show_restore' => false,
    ));
// Item Section
//
    CSF::createSection($prefix_page_opts, array(
        'title' => __('List Item', 'cptbx'),
        'icon' => 'fas fa-rocket',
        'fields' => array(
            array(
                'id' => 'mrv-item',
                'type' => 'group',
                'title' => '',
                'accordion_title_by' => array('item-title'),

                'accordion_title_number' => true,
                'fields' => array(
                    // Content
                    array(
                        'id' => 'item-multiple-settings',
                        'type' => 'tabbed',
                        'tabs' => array(
                            array(
                                'id' => 'cptb-item-tab-content',
                                'title' => __('Content', 'cptbx'),
                                'fields' => array(
                                    array(
                                        'id' => 'item-title',
                                        'type' => 'text',
                                        'class' => 'mrv_custom_item',
                                        'title' => __('Title', 'cptbx'),
                                        'placeholder' => 'Write Your Title',
                                    ),

                                    // item media
                                    array(
                                        'id' => 'item-media-type',
                                        'type' => 'button_set',
                                        'title' => __('Media Type', 'cptbx'),
                                        'default' => 'image',
                                        'options' => array(
                                            'image' => 'Image',
                                            'youtube' => 'Youtube Video',
                                            'none' => 'None',
                                        ),
                                    ),
                                    // item media
                                    array(
                                        'id' => 'item-media-image',
                                        'type' => 'media',
                                        'title' => __('Choose image', 'cptbx'),
                                        'library' => 'image',
                                        'url' => false,
                                        'dependency' => array(
                                            'item-media-type',
                                            '==',
                                            'image',
                                        ),
                                    ),
                                    array(
                                    'id'=>'item-image-size',
                                    'type'=>'select',
                                    'title'=> __('Image Size','cptbx'),
                                    'default'=>'medium',
                                    'desc'=>'This settings only work with media type = image.',
                                    'options'=>'mrv_available_featured_image_size',
                                    'dependency'=>array(
                                        'item-media-type',
                                        '==',
                                        'image'
                                    ) 
                                    ),

                                    array(
                                        'id' => 'item-media-youtube',
                                        'type' => 'text',
                                        'title' => __('Add Youtube URL', 'cptbx'),
                                        'dependency' => array(
                                            'item-media-type',
                                            '==',
                                            'youtube',
                                        ),
                                        //'validate'=>'itb_validate_youtubeUrl'
                                    ),
                                    // item media

                                    // item Description
                                    array(
                                        'id' => 'item-desc',
                                        'type' => 'wp_editor',
                                        'title' => __('Description', 'cptbx'),
                                        'media_buttons' => false,
                                    ),

                                ),
                            ),
                            // Advanced Settings Tab fields
                            array(
                                'title' => __('Advanced Settings', 'cptbx'),
                                'fields' => array(
                                    array(
                                        'id' => 'item_title_style',
                                        'title' => __('Item Title Style', 'cptbx'),
                                        'type' => 'typography', // Do not add unnecessary typography settings
                                       'font_weight' => false,
                                        //'font_style'=>false,
                                        'text_align' => false,
                                        
                                        'text_transform' => false,
                                        'subset' => false,
                                        'letter_spacing' => false,
                                        'preview' => false,
                                        'default' => array(                                               
                                            'font-family'        => 'Arial',
                                            'font-style'         => 'Normal 400',
                                            'font-size'          => '20',
                                            'line-height'        => '2',
                                            'font-weight'        => 'bold',
                                            'color'              => 'black',
                                            ),
                                    ),
                                    array(
                                        'id' => 'desc_style',
                                        'title' => __('Description  Style', 'cptbx'),
                                        'type' => 'typography', // Do not add unnecessary typography settings
                                       'font_weight' => false,
                                        //'font_style'=>false,
                                        'text_align' => false,
                                        
                                        'text_transform' => false,
                                        'subset' => false,
                                        'letter_spacing' => false,
                                        'preview' => false,
                                        'default' => array(                                               
                                            'font-family'        => 'Arial',
                                            'font-style'         => 'Normal 400',
                                            'font-size'          => '16',
                                            'line-height'        => '2',
                                            'font-weight'        => 'normal',
                                            'color'              => 'black',
                                            ),
                                    ),

                                    array(
                                        'id' => 'item-bg-color',
                                        'type' => 'color',
                                        'title' => __('Item Background Color', 'cptbx'),
                                        'default' => 'aliceblue',
                                    ),
                                    array(
                                        'id' => 'item-border',
                                        'type' => 'border',
                                        'title' => 'Item Border',
                                        'default' => array(
                                            'color'      => 'black',                                              
                                            'style'      => 'solid',
                                            'top'        => '2',
                                            'right'      => '2',
                                            'bottom'     => '2',
                                            'left'       => '2',                                           
                                        ),
                                    ),
                                ),
                            ),

                        ),
                    ),
                ),
                /*
                Default items to show when new timeline is created.
                Callback function to render default stories
                 */
                // 'default'   =>
                //  cptb_default_stories()

            ),
        ),
    ));

    CSF::createSection($prefix_page_opts, array(
        'title' => __('Design & Settings', 'cptbx'),
        'icon' => 'fas fa-palette',
        'fields' => array(
            array(
                'id' => 'mrvt-list-style',
                'type' => 'select',
                'title' => 'List Style',
                'placeholder' => 'Select List Style',
                'options' => array(
                    'style-1' => 'Simple List',
                    'style-2' => 'Bullet List',
                    'style-3' => 'Number List',
                ),
                'default' => 'style-1',

            ),
                        array(
                'id' => 'mrvt-media-position',
                'type' => 'select',
                'title' => 'Media Position',
                'placeholder' => 'Select Media Position',
                'options' => array(                 
                    'mrv-media-top' => 'Media Top',
                    'mrv-media-bottom' => 'Media Bottom',                    
                    'mrv-media-right' => 'Media Right',
                    'mrv-media-left' => 'Media Left',
                ),
                'default' => 'mrv-media-left',
            ),

         
            array(
                'id'     => 'vote_settings',
                'type'   => 'fieldset',
                'title'  => 'Vote Settings',
                'fields' => array(
                      array(
                'id' => 'mrv-vote-type',
                'type' => 'radio',
                'inline' => true,
                'title' => 'Vote Type',
                'options' => array(
                    'upvote_only' => 'Up Vote Only',
                    'both_vote' => 'Both Up/Down',

                ),
                'default' => 'upvote_only',
            ),
            array(
                'id' => 'mrv-image-select-up',
                'type' => 'image_select',
                'title' => 'Vote Icon',
                'options' => array(
                    'dashicons-up' => MRV_URL . 'assets/images/up2.png',
                    'dashicons-up3' => MRV_URL . 'assets/images/up4.png',
                    'dashicons-up4' => MRV_URL . 'assets/images/up5.png',
                    'dashicons-up5' => MRV_URL . 'assets/images/up6.png',
                    'dashicons-up6' => MRV_URL . 'assets/images/up7.png',
                    'dashicons-heart' => MRV_URL . 'assets/images/heart.png',
                ),
                'dependency' => array(
                    'mrv-vote-type',
                    '==',
                    'upvote_only',
                ),
                'default' => 'dashicons-up',
            ),
            array(
                'id' => 'mrv-image-select-up-dowwn',
                'type' => 'image_select',
                'title' => 'Vote Icon',
                'options' => array(
                    'dashicons-up' => MRV_URL . 'assets/images/up-down-1.png',
                    'dashicons-up3' => MRV_URL . 'assets/images/up-down-4.png',
                    'dashicons-up4' => MRV_URL . 'assets/images/up-down-5.png',
                    'dashicons-up5' => MRV_URL . 'assets/images/up-down-7.png',
                    'dashicons-up6' => MRV_URL . 'assets/images/up-down-8.png',
                ),
                'dependency' => array(
                    'mrv-vote-type',
                    '==',
                    'both_vote',
                ),
                'default' => 'dashicons-up',
            ),
            array(
                'id'          => 'vote_icon_size',
                'type'        => 'number',
                'title'       => 'Icon Size',
                'unit'        => 'px',
                'output'      => '.heading', 
                'default' => '30',              
                
                ),
             array(
                'id'          => 'vote_count_size',
                'type'        => 'number',
                'title'       => 'Count Size',
                'unit'        => 'px',
                'output'      => '.heading',               
                'default' => '15',
                ),
            array(
                'id' => 'mrvt-icon-position',
                'type' => 'select',
                'title' => 'Icon Position',
                'placeholder' => 'Select Icon Position',
                'options' => array(
                    'mrv-list-icon-left' => 'Before Title',
                    'mrv-list-icon-right2' => 'After Title Begin',
                    'mrv-list-icon-right' => 'After Title Last',
                    'mrv-icon-before-list' => 'Before list Item',
                    'mrv-icon-after-list' => 'After list Item',
                ),
                'default' => 'mrv-list-icon-left',
            ),
            array(
                'id' => 'mrvt-vote-count-position',
                'type' => 'select',
                'title' => 'Vote Count Position',
                'placeholder' => 'Select Vote Count Position',
                'options' => array(
                    '-left' => 'Left to Icon',
                    '-right' => 'Right to Icon',
                    '-up' => 'Above Icon',
                    '-down' => 'Below Icon',
                ),
                'default' => '-left',
            ),

                        array(
                'id' => 'mrv-show-votes',
                'type' => 'switcher',
                'text_on' => 'Yes',
                'text_off' => 'No',
                'title' => 'Show Votes',
                'default' => false,
            ),
            array(
                'id' => 'mrv-show-votes-after-vote',
                'type' => 'switcher',
                'text_on' => 'Yes',
                'text_off' => 'No',
                'dependency' => array('mrv-show-votes', '==', false),
                'title' => 'Show Votes after Voting',
                'default' => true,
            ),
                  array(
                'id' => 'mrv-sort-vote',
                'type' => 'switcher',
                'text_on' => 'Yes',
                'text_off' => 'No',
                'title' => 'Sort List Based On Votes',
                'default' => false,
            ),
            array(
                'id' => 'mrvt-sort-order',
                'type' => 'select',
                'title' => 'Sort Order',
                'options' => array(
                    'asc' => 'ASC',
                    'desc' => 'DESC',
                ),
                'dependency' => array('mrv-sort-vote', '==', true),

            ),

                )),
                array(
                    'id'     => 'vote_icon_style',
                    'type'   => 'fieldset',
                    'title'  => 'Vote Icon',
                    'fields' => array(
                                array(
                    'id' => 'vote_up_color',
                    'type' => 'color',
                    'title' => __('Up Color ', 'cptbx'),
                    'default' => '#21d521',
    
                ),
                        array(
                    'id' => 'vote_down_color',
                    'type' => 'color',
                    'title' => __('Down Color', 'cptbx'),
                    'default' => '#d52121',
                ),
                        array(
                    'id' => 'vote_count_color',
                    'type' => 'color',
                    'title' => __('Count Color ', 'cptbx'),
                    'default' => 'black',
                ),
    
    
                    )),
            array(
                'id' => 'mrv-g-list-title-style',
                'title' => __('List Title Style', 'cptbx'),
                'type' => 'typography',
                'font_weight' => false,
                //'font_style'=>false,
                'text_align' => false,
                
                'text_transform' => false,
                'subset' => false,
                'letter_spacing' => false,
                'preview' => true,
                'default' => array(                                               
                    'font-family'        => 'Arial',
                    'font-style'         => 'Normal 400',
                    'font-size'          => '20',
                    'line-height'        => '2',
                    'font-weight'        => 'normal',
                    'color'              => 'black',
                    ),
            ),
          
      

            array(
                'id' => 'mrv_list_settings',
                'type' => 'submessage',
                'content' => 'These settings will be overriden by specific item\'s settings',
                'style' => 'warning',
            ),
            array(
                'id' => 'mrv-g-item-title-style',
                'title' => __('Item Title Style', 'cptbx'),
                'type' => 'typography',
                'font_weight' => false,
                //'font_style'=>false,
                'text_align' => false,
                
                'text_transform' => false,
                'subset' => false,
                'letter_spacing' => false,
                'preview' => true,
                'default' => array(                                               
                    'font-family'        => 'Arial',
                    'font-style'         => 'Normal 400',
                    'font-size'          => '20',
                    'line-height'        => '2',
                    'font-weight'        => 'normal',
                    'color'              => 'black',
                    ),
            ),

            array(
                'id' => 'mrv-g-desc-typo',
                'title' => __('Description Style', 'cptbx'),
                'type' => 'typography', // Do not add unnecessary typography settings
                'font_weight' => false, //   Description is added from WP Classic Editor
                //  'font_style'=>false,                          // All formatting can be done through WP Classic Editor
                'text_align' => false,
                 
                'text_transform' => false,
                'subset' => false,
                'letter_spacing' => false,
                'preview' => true,
                'default' => array(                                               
                    'font-family'        => 'Arial',
                    'font-style'         => 'Normal 400',
                    'font-size'          => '16',
                    'line-height'        => '2',
                    'font-weight'        => 'normal',
                    'color'              => 'black',
                    ),
            ),
           
        
           
             
           

            array(
                'id'     => 'list_item_style',
                'type'   => 'fieldset',
                'title'  => 'List Item Style',
                'fields' => array(
                     array(
                'id' => 'item-g-bg-color',
                'type' => 'color',
                'title' => __('Background', 'cptbx'),
                'default' => 'aliceblue',

            ),
                     array(
                'id' => 'item-g-margin',
                'type' => 'border',
                'title' => 'Margin',
                'color'=>false,
                'style'=>false,  
                'default' => array(
                    'color'      => 'black',                                              
                    'style'      => 'solid',
                    'top'        => '10',
                    'right'      => '0',
                    'bottom'     => '0',
                    'left'       => '0',                                           
                ),              
            ),
                      array(
                'id' => 'item-g-padding',
                'type' => 'border',
                'title' => 'Padding',
                'color'=>false,
                'style'=>false,
                'default' => array(
                    'color'      => 'black',                                              
                    'style'      => 'solid',
                    'top'        => '10',
                    'right'      => '10',
                    'bottom'     => '10',
                    'left'       => '10',                                           
                ),                 
            ),

                     array(
                        'id' => 'item-g-border',
                        'type' => 'border',
                        'title' => 'Border',
                        'default' => array(
                            'color'      => 'black',                                              
                            'style'      => 'solid',
                            'top'        => '2',
                            'right'      => '2',
                            'bottom'     => '2',
                            'left'       => '2',                                           
                        ),
                 ),

                )),

                 array(
                'id'     => 'list_style',
                'type'   => 'fieldset',
                'title'  => 'List Style',
                'fields' => array(
                        array(
                'id' => 'item-list-g-bg-color',
                'type' => 'color',
                'title' => __('Background ', 'cptbx'),
                'default' => 'aliceblue',
            ),
           
             array(
                'id' => 'item-list-g-padding',
                'type' => 'border',
                'title' => 'Padding',
                'color'=>false,
                'style'=>false,  
                'default' => array(
                    'color'      => 'black',                                              
                    'style'      => 'solid',
                    'top'        => '0',
                    'right'      => '0',
                    'bottom'     => '0',
                    'left'       => '0',                                           
                ),                   
            ),
                      array(
                'id' => 'item-list-g-border',
                'type' => 'border',
                'title' => 'Border',       
                'default' => array(
                    'color'      => 'black',                                              
                    'style'      => 'solid',
                    'top'        => '2',
                    'right'      => '2',
                    'bottom'     => '2',
                    'left'       => '2',                                           
                ),
            ),

                )),
          
        ),
    ));

// Create a section
    CSF::createSection($prefix_page_opts, array(
        'title' => 'List Log',
        'icon' => 'fas fa-rocket',
        'fields' => array(
            // A text field
            array(
                'type' => 'content',
                'content' => '

		      <table class="mrv_table_wrap">
		      <thead class="mrv_table_head">
		  <tr>
		    <th>#</th>
		    <th>Item Name</th>
		    <th>Up Vote</th>
		    <th>Down Vote</th>
		    <th>Total Vote</th>
		  </tr>
		  </thead>
		<tbody class="mrv_table_body">
		   ' . $tabel_body . '
		  </tbody>
		</table>',
            ),

        ),
    ));

    $shortcode_box = $prefix_page_opts . '_shortcode_bar';

//
// Create a metabox
    CSF::createMetabox($shortcode_box, array(
        'title' => 'Shortcode',
        'post_type' => 'meta-ranker',
        'context' => 'side', // The context within the screen where the boxes should display. `normal`, `side`, `advanced`
    ));
    //
    // Create a section
    CSF::createSection($shortcode_box, array(
        'title' => '',
        'fields' => array(
            //
            // A text field
            array(
                'id' => 'cptb-shortcode-box',
                'type' => 'callback',
                'function' => 'mrv_get_shortcode_field',
            ),

        ),
    ));


  

endif;


/**
 * Create HTML for shortcode input field
 */
function mrv_get_shortcode_field()
{
    $message="";
    // create timeline builder metabox
    if (!empty($_GET['post']) && get_post_type(sanitize_text_field($_GET['post'])) == 'meta-ranker') {
        $id = sanitize_text_field($_GET['post']);
        ?>
       <input style='width:100%;padding:0 2px 0 2px;text-align:center;' type='text' value='[meta-ranker id="<?php echo esc_attr($id)?>"]' readonly onClick='this.select();'>
        <button id='mrv-copy-shortcode' class='button button-primary button-small' style='margin-top:5px;float:right;'>Copy</button>
    <?php
    } else {
        ?>
   <p>Publish this post to generate the shortcode.</p>
   <?php
    }

   
}
function mrv_get_cpt()
{
    global $post, $typenow, $current_screen;

    if ($post && $post->post_type) {
        return $post->post_type;
    } elseif ($typenow) {
        return $typenow;
    } elseif ($current_screen && $current_screen->post_type) {
        return $current_screen->post_type;
    } elseif (isset($_REQUEST['page'])) {
        return sanitize_key($_REQUEST['page']);
    } elseif (isset($_REQUEST['post_type'])) {
        return sanitize_key($_REQUEST['post_type']);
    } elseif (isset($_REQUEST['post'])) {
        return get_post_type(filter_var($_REQUEST['post'], FILTER_SANITIZE_STRING));
    }
    return null;
}
function mrv_clean_sc($string)
{
    $string = str_replace(' ', '', $string); // Replaces all spaces with hyphens.
    $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.

    return preg_replace('/-+/', '-', $string); // Replaces multiple hyphens with single one.
}


/**
 * Get all the registered image sizes along with their dimensions
 *
 * @global array $_wp_additional_image_sizes
 *
 * @return array $image_sizes The image sizes
 */
function mrv_available_featured_image_size()
{
    global $_wp_additional_image_sizes;

    $default_image_sizes = get_intermediate_image_sizes();

    foreach ($default_image_sizes as $size) {
        $width = intval(get_option("{$size}_size_w"));
        $height = intval(get_option("{$size}_size_h"));

        $resolution = ($width != 0 && $height != 0) ? '(' . $width . 'X' . $height . ')' : '';
        $image_sizes[$size] = str_replace('_', ' ', ucwords($size)) . ' ' . $resolution;

    }

    $image_sizes['full'] = 'Full';
    $image_sizes['custom_size'] = 'Custom Size';
    return $image_sizes;
}



