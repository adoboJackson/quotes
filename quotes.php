<?php
/*
  Plugin Name: Quotes Plugin
  Description: Simple Quotes and Testimonials Plugin and Widget for Goseso Site
  Author: Neil Bernardo
  Version: 1.0
 */

class Quotes_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct('Quotes_Widget', 'Quotes and Testimonials', array('description' => __('This is a Quotes and Testimonials Widget', 'text_domain')));
    }

    public function form($instance) {
        if (isset($instance['title'])) {
            $title = $instance['title'];
        } else {
            $title = __('Widget Quotes', 'text_domain');
        }
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
            <iquotesut class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = strip_tags($new_instance['title']);

        return $instance;
    }
    public function widget($args, $instance) {
        extract($args);
        //the title	
        $title = apply_filters('widget_title', $instance['title']);
        echo $before_widget;
        if (!empty($title))
            echo $before_title . $title . $after_title;
        echo quotes_function('Quotes_Widget');
        echo $after_widget;
    }

}

function quotes_register_scripts() {
    if (!is_admin()) {
        // register  
        wp_register_script('quotes_script', plugins_url('script.js', __FILE__));
        // enqueue  
        wp_enqueue_script('jquery');
        wp_enqueue_script('quotes_script');
    }
}

function quotes_register_styles() {
    // register  
    wp_register_style('quotes_styles', plugins_url('quotes/quotes.css', __FILE__));
    // enqueue  
    wp_enqueue_style('quotes_styles');
}

function quotes_widgets_init() {
    register_widget('Quotes_Widget');
}

function quotes_function($type='quotes_function') {
    $args = array('post_type' => 'quotes_images', 'posts_per_page' => 5);
    $result = '<div class="slider-wrapper theme-default">';
    $result .= '<div id="slider" class="quotes-panel">';
    //the loop
    $loop = new WP_Query($args);
    while ($loop->have_posts()) {
        $loop->the_post();

        $the_url = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), $type);

        $result .='<img title="'.get_the_title().'" src="' . $the_url[0] . '" data-thumb="' . $the_url[0] . '" alt=""/>';
    }
    $result .= '</div>';
    $result .='<div id = "htmlcaption" class = "quotes-html-caption">';
    $result .='<strong>This</strong> is an example of a <em>HTML</em> caption with <a href = "#">a link</a>.';
    $result .='</div>';
    $result .='</div>';
    return $result;
}

function quotes_init() {
    add_shortcode('quotes-shortcode', 'quotes_function');
    
    add_image_size('quotes_widget', 100, 100, true);
    add_image_size('quotes_function', 100, 100, true);
    
    $args = array('public' => true, 'label' => 'Quotes & Testimonials', 'supports' => array('title', 'thumbnail'));
    register_post_type('quotes_images', $args);
}

//hooks
add_theme_support('post-thumbnails');
add_action('init', 'quotes_init');
add_action('widgets_init', 'quotes_widgets_init');
add_action('wp_print_scripts', 'quotes_register_scripts');
add_action('wp_print_styles', 'quotes_register_styles');
?>