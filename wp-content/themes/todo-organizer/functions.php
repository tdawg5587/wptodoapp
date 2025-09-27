<?php
/**
 * Todo Organizer Theme Functions
 *
 * @package TodoOrganizer
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Theme setup
 */
function todo_organizer_setup() {
    // Add theme support for post thumbnails
    add_theme_support('post-thumbnails');
    
    // Add theme support for title tag
    add_theme_support('title-tag');
    
    // Add theme support for HTML5 markup
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ));
    
    // Add theme support for custom logo
    add_theme_support('custom-logo', array(
        'height'      => 100,
        'width'       => 400,
        'flex-height' => true,
        'flex-width'  => true,
    ));
    
    // Register navigation menus
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'todo-organizer'),
        'footer'  => __('Footer Menu', 'todo-organizer'),
    ));
    
    // Add theme support for custom header
    add_theme_support('custom-header', array(
        'default-color' => 'ffffff',
        'width'         => 1200,
        'height'        => 280,
        'flex-height'   => true,
        'flex-width'    => true,
    ));
    
    // Add theme support for custom background
    add_theme_support('custom-background', array(
        'default-color' => 'f8f9fa',
    ));
}
add_action('after_setup_theme', 'todo_organizer_setup');

/**
 * Enqueue scripts and styles
 */
function todo_organizer_scripts() {
    // Enqueue main stylesheet
    wp_enqueue_style('todo-organizer-style', get_stylesheet_uri(), array(), '1.0.0');
    
    // Enqueue custom JavaScript
    wp_enqueue_script('todo-organizer-scripts', get_template_directory_uri() . '/js/theme.js', array('jquery'), '1.0.0', true);
    
    // Enqueue comment reply script on single posts with comments open
    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
}
add_action('wp_enqueue_scripts', 'todo_organizer_scripts');

/**
 * Register widget areas
 */
function todo_organizer_widgets_init() {
    register_sidebar(array(
        'name'          => __('Main Sidebar', 'todo-organizer'),
        'id'            => 'sidebar-1',
        'description'   => __('Add widgets here to appear in your sidebar.', 'todo-organizer'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ));
    
    register_sidebar(array(
        'name'          => __('Footer Widgets', 'todo-organizer'),
        'id'            => 'footer-widgets',
        'description'   => __('Add widgets here to appear in your footer.', 'todo-organizer'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));
}
add_action('widgets_init', 'todo_organizer_widgets_init');

/**
 * Modify main query for todo items on home page
 */
function todo_organizer_modify_main_query($query) {
    if (!is_admin() && $query->is_main_query()) {
        if (is_home()) {
            // Show todo items on the home page
            $query->set('post_type', array('post', 'todo_item'));
        }
    }
}
add_action('pre_get_posts', 'todo_organizer_modify_main_query');

/**
 * Get todo statistics
 */
function todo_organizer_get_todo_stats() {
    $stats = wp_cache_get('todo_organizer_stats');
    
    if (false === $stats) {
        global $wpdb;
        
        $stats = array(
            'total' => 0,
            'pending' => 0,
            'in_progress' => 0,
            'completed' => 0,
            'cancelled' => 0,
            'overdue' => 0,
        );
        
        // Get total count
        $stats['total'] = wp_count_posts('todo_item')->publish;
        
        // Get status counts
        $status_counts = $wpdb->get_results($wpdb->prepare("
            SELECT pm.meta_value as status, COUNT(*) as count 
            FROM {$wpdb->postmeta} pm 
            INNER JOIN {$wpdb->posts} p ON pm.post_id = p.ID 
            WHERE pm.meta_key = '_todo_status' 
            AND p.post_type = 'todo_item' 
            AND p.post_status = 'publish'
            GROUP BY pm.meta_value
        "));
        
        foreach ($status_counts as $status_count) {
            if (isset($stats[$status_count->status])) {
                $stats[$status_count->status] = $status_count->count;
            }
        }
        
        // Get overdue count
        $overdue_count = $wpdb->get_var($wpdb->prepare("
            SELECT COUNT(*) 
            FROM {$wpdb->postmeta} pm1
            INNER JOIN {$wpdb->postmeta} pm2 ON pm1.post_id = pm2.post_id
            INNER JOIN {$wpdb->posts} p ON pm1.post_id = p.ID 
            WHERE pm1.meta_key = '_todo_due_date' 
            AND pm1.meta_value < %s
            AND pm2.meta_key = '_todo_status'
            AND pm2.meta_value NOT IN ('completed', 'cancelled')
            AND p.post_type = 'todo_item' 
            AND p.post_status = 'publish'
        ", date('Y-m-d')));
        
        $stats['overdue'] = (int) $overdue_count;
        
        wp_cache_set('todo_organizer_stats', $stats, '', 300); // Cache for 5 minutes
    }
    
    return $stats;
}

/**
 * Clear stats cache when todo is updated
 */
function todo_organizer_clear_stats_cache($post_id) {
    if (get_post_type($post_id) === 'todo_item') {
        wp_cache_delete('todo_organizer_stats');
    }
}
add_action('save_post', 'todo_organizer_clear_stats_cache');
add_action('delete_post', 'todo_organizer_clear_stats_cache');

/**
 * Get todo priority class
 */
function todo_organizer_get_priority_class($post_id) {
    $priorities = wp_get_post_terms($post_id, 'todo_priority', array('fields' => 'names'));
    
    if (!empty($priorities) && !is_wp_error($priorities)) {
        $priority = strtolower($priorities[0]);
        return 'priority-' . $priority;
    }
    
    return 'priority-medium';
}

/**
 * Get todo status class
 */
function todo_organizer_get_status_class($post_id) {
    $status = get_post_meta($post_id, '_todo_status', true);
    return $status ? 'status-' . $status : 'status-pending';
}

/**
 * Format due date with overdue check
 */
function todo_organizer_format_due_date($post_id) {
    $due_date = get_post_meta($post_id, '_todo_due_date', true);
    
    if (!$due_date) {
        return '';
    }
    
    $due_timestamp = strtotime($due_date);
    $current_timestamp = current_time('timestamp');
    $status = get_post_meta($post_id, '_todo_status', true);
    
    $formatted_date = date_i18n(get_option('date_format'), $due_timestamp);
    
    if ($current_timestamp > $due_timestamp && !in_array($status, array('completed', 'cancelled'))) {
        return '<span class="todo-due-date overdue">' . sprintf(__('Due: %s (Overdue)', 'todo-organizer'), $formatted_date) . '</span>';
    }
    
    return '<span class="todo-due-date">' . sprintf(__('Due: %s', 'todo-organizer'), $formatted_date) . '</span>';
}

/**
 * Custom excerpt length
 */
function todo_organizer_excerpt_length($length) {
    return 20;
}
add_filter('excerpt_length', 'todo_organizer_excerpt_length');

/**
 * Custom excerpt more
 */
function todo_organizer_excerpt_more($more) {
    return '&hellip;';
}
add_filter('excerpt_more', 'todo_organizer_excerpt_more');

/**
 * Add custom body classes
 */
function todo_organizer_body_classes($classes) {
    // Add class if we're on a todo-related page
    if (is_post_type_archive('todo_item') || is_singular('todo_item')) {
        $classes[] = 'todo-page';
    }
    
    return $classes;
}
add_filter('body_class', 'todo_organizer_body_classes');

/**
 * Customize archive title for todo items
 */
function todo_organizer_archive_title($title) {
    if (is_post_type_archive('todo_item')) {
        $title = __('Todo Items', 'todo-organizer');
    }
    
    return $title;
}
add_filter('get_the_archive_title', 'todo_organizer_archive_title');

/**
 * Add REST API support for theme
 */
function todo_organizer_rest_api_support() {
    // Add CORS headers for API access
    add_action('rest_api_init', function () {
        remove_filter('rest_pre_serve_request', 'rest_send_cors_headers');
        add_filter('rest_pre_serve_request', function ($value) {
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
            header('Access-Control-Allow-Headers: Content-Type, Authorization');
            
            if ('OPTIONS' === $_SERVER['REQUEST_METHOD']) {
                status_header(200);
                exit();
            }
            
            return $value;
        });
    });
}
add_action('init', 'todo_organizer_rest_api_support');

/**
 * Enqueue admin styles for todo post type
 */
function todo_organizer_admin_styles($hook) {
    global $post_type;
    
    if ('todo_item' === $post_type) {
        wp_enqueue_style('todo-organizer-admin', get_template_directory_uri() . '/css/admin.css', array(), '1.0.0');
    }
}
add_action('admin_enqueue_scripts', 'todo_organizer_admin_styles');