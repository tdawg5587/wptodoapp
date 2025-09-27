<?php
/**
 * Plugin Name: Todo Manager
 * Plugin URI: https://github.com/tdawg5587/wptodoapp
 * Description: A comprehensive todo management system with REST API support for MCP integration with Docker Desktop.
 * Version: 1.0.0
 * Author: Tony
 * Author URI: https://github.com/tdawg5587
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: todo-manager
 * Domain Path: /languages
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('TODO_MANAGER_VERSION', '1.0.0');
define('TODO_MANAGER_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('TODO_MANAGER_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * Main Todo Manager Plugin Class
 */
class TodoManager {
    
    /**
     * Constructor
     */
    public function __construct() {
        add_action('init', array($this, 'init'));
        add_action('rest_api_init', array($this, 'register_rest_routes'));
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }
    
    /**
     * Initialize the plugin
     */
    public function init() {
        $this->register_post_type();
        $this->register_taxonomies();
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post', array($this, 'save_todo_meta'));
        add_filter('manage_todo_item_posts_columns', array($this, 'add_admin_columns'));
        add_action('manage_todo_item_posts_custom_column', array($this, 'display_admin_columns'), 10, 2);
    }
    
    /**
     * Register the todo item custom post type
     */
    public function register_post_type() {
        $labels = array(
            'name'                  => _x('Todo Items', 'Post type general name', 'todo-manager'),
            'singular_name'         => _x('Todo Item', 'Post type singular name', 'todo-manager'),
            'menu_name'             => _x('Todo Items', 'Admin Menu text', 'todo-manager'),
            'name_admin_bar'        => _x('Todo Item', 'Add New on Toolbar', 'todo-manager'),
            'add_new'               => __('Add New', 'todo-manager'),
            'add_new_item'          => __('Add New Todo Item', 'todo-manager'),
            'new_item'              => __('New Todo Item', 'todo-manager'),
            'edit_item'             => __('Edit Todo Item', 'todo-manager'),
            'view_item'             => __('View Todo Item', 'todo-manager'),
            'all_items'             => __('All Todo Items', 'todo-manager'),
            'search_items'          => __('Search Todo Items', 'todo-manager'),
            'parent_item_colon'     => __('Parent Todo Items:', 'todo-manager'),
            'not_found'             => __('No todo items found.', 'todo-manager'),
            'not_found_in_trash'    => __('No todo items found in Trash.', 'todo-manager'),
            'featured_image'        => _x('Todo Item Image', 'Overrides the "Featured Image" phrase', 'todo-manager'),
            'set_featured_image'    => _x('Set todo image', 'Overrides the "Set featured image" phrase', 'todo-manager'),
            'remove_featured_image' => _x('Remove todo image', 'Overrides the "Remove featured image" phrase', 'todo-manager'),
            'use_featured_image'    => _x('Use as todo image', 'Overrides the "Use as featured image" phrase', 'todo-manager'),
            'archives'              => _x('Todo Item archives', 'The post type archive label used in nav menus', 'todo-manager'),
            'insert_into_item'      => _x('Insert into todo item', 'Overrides the "Insert into post" phrase', 'todo-manager'),
            'uploaded_to_this_item' => _x('Uploaded to this todo item', 'Overrides the "Uploaded to this post" phrase', 'todo-manager'),
            'filter_items_list'     => _x('Filter todo items list', 'Screen reader text for the filter links', 'todo-manager'),
            'items_list_navigation' => _x('Todo items list navigation', 'Screen reader text for the pagination', 'todo-manager'),
            'items_list'            => _x('Todo items list', 'Screen reader text for the items list', 'todo-manager'),
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array('slug' => 'todo-item'),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => null,
            'menu_icon'          => 'dashicons-list-view',
            'show_in_rest'       => true,
            'rest_base'          => 'todo-items',
            'rest_controller_class' => 'WP_REST_Posts_Controller',
            'supports'           => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'custom-fields'),
        );

        register_post_type('todo_item', $args);
    }
    
    /**
     * Register taxonomies
     */
    public function register_taxonomies() {
        // Priority taxonomy
        $priority_labels = array(
            'name'              => _x('Priorities', 'taxonomy general name', 'todo-manager'),
            'singular_name'     => _x('Priority', 'taxonomy singular name', 'todo-manager'),
            'search_items'      => __('Search Priorities', 'todo-manager'),
            'all_items'         => __('All Priorities', 'todo-manager'),
            'parent_item'       => __('Parent Priority', 'todo-manager'),
            'parent_item_colon' => __('Parent Priority:', 'todo-manager'),
            'edit_item'         => __('Edit Priority', 'todo-manager'),
            'update_item'       => __('Update Priority', 'todo-manager'),
            'add_new_item'      => __('Add New Priority', 'todo-manager'),
            'new_item_name'     => __('New Priority Name', 'todo-manager'),
            'menu_name'         => __('Priority', 'todo-manager'),
        );

        $priority_args = array(
            'hierarchical'      => false,
            'labels'            => $priority_labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'show_in_rest'      => true,
            'rest_base'         => 'todo-priorities',
            'rewrite'           => array('slug' => 'todo-priority'),
        );

        register_taxonomy('todo_priority', array('todo_item'), $priority_args);
        
        // Categories taxonomy
        $category_labels = array(
            'name'              => _x('Categories', 'taxonomy general name', 'todo-manager'),
            'singular_name'     => _x('Category', 'taxonomy singular name', 'todo-manager'),
            'search_items'      => __('Search Categories', 'todo-manager'),
            'all_items'         => __('All Categories', 'todo-manager'),
            'parent_item'       => __('Parent Category', 'todo-manager'),
            'parent_item_colon' => __('Parent Category:', 'todo-manager'),
            'edit_item'         => __('Edit Category', 'todo-manager'),
            'update_item'       => __('Update Category', 'todo-manager'),
            'add_new_item'      => __('Add New Category', 'todo-manager'),
            'new_item_name'     => __('New Category Name', 'todo-manager'),
            'menu_name'         => __('Categories', 'todo-manager'),
        );

        $category_args = array(
            'hierarchical'      => true,
            'labels'            => $category_labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'show_in_rest'      => true,
            'rest_base'         => 'todo-categories',
            'rewrite'           => array('slug' => 'todo-category'),
        );

        register_taxonomy('todo_category', array('todo_item'), $category_args);
    }
    
    /**
     * Add meta boxes
     */
    public function add_meta_boxes() {
        add_meta_box(
            'todo-details',
            __('Todo Details', 'todo-manager'),
            array($this, 'todo_details_meta_box'),
            'todo_item',
            'normal',
            'high'
        );
    }
    
    /**
     * Todo details meta box callback
     */
    public function todo_details_meta_box($post) {
        wp_nonce_field('todo_manager_meta_box', 'todo_manager_meta_box_nonce');
        
        $status = get_post_meta($post->ID, '_todo_status', true);
        $due_date = get_post_meta($post->ID, '_todo_due_date', true);
        $completed_date = get_post_meta($post->ID, '_todo_completed_date', true);
        $estimated_time = get_post_meta($post->ID, '_todo_estimated_time', true);
        $actual_time = get_post_meta($post->ID, '_todo_actual_time', true);
        $progress = get_post_meta($post->ID, '_todo_progress', true);
        ?>
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="todo_status"><?php _e('Status', 'todo-manager'); ?></label>
                </th>
                <td>
                    <select name="todo_status" id="todo_status">
                        <option value="pending" <?php selected($status, 'pending'); ?>><?php _e('Pending', 'todo-manager'); ?></option>
                        <option value="in_progress" <?php selected($status, 'in_progress'); ?>><?php _e('In Progress', 'todo-manager'); ?></option>
                        <option value="completed" <?php selected($status, 'completed'); ?>><?php _e('Completed', 'todo-manager'); ?></option>
                        <option value="cancelled" <?php selected($status, 'cancelled'); ?>><?php _e('Cancelled', 'todo-manager'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="todo_due_date"><?php _e('Due Date', 'todo-manager'); ?></label>
                </th>
                <td>
                    <input type="date" name="todo_due_date" id="todo_due_date" value="<?php echo esc_attr($due_date); ?>" />
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="todo_completed_date"><?php _e('Completed Date', 'todo-manager'); ?></label>
                </th>
                <td>
                    <input type="datetime-local" name="todo_completed_date" id="todo_completed_date" value="<?php echo esc_attr($completed_date); ?>" />
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="todo_estimated_time"><?php _e('Estimated Time (hours)', 'todo-manager'); ?></label>
                </th>
                <td>
                    <input type="number" name="todo_estimated_time" id="todo_estimated_time" value="<?php echo esc_attr($estimated_time); ?>" step="0.25" min="0" />
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="todo_actual_time"><?php _e('Actual Time (hours)', 'todo-manager'); ?></label>
                </th>
                <td>
                    <input type="number" name="todo_actual_time" id="todo_actual_time" value="<?php echo esc_attr($actual_time); ?>" step="0.25" min="0" />
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="todo_progress"><?php _e('Progress (%)', 'todo-manager'); ?></label>
                </th>
                <td>
                    <input type="number" name="todo_progress" id="todo_progress" value="<?php echo esc_attr($progress); ?>" min="0" max="100" />
                </td>
            </tr>
        </table>
        <?php
    }
    
    /**
     * Save todo meta data
     */
    public function save_todo_meta($post_id) {
        if (!isset($_POST['todo_manager_meta_box_nonce'])) {
            return;
        }

        if (!wp_verify_nonce($_POST['todo_manager_meta_box_nonce'], 'todo_manager_meta_box')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        $fields = array(
            'todo_status',
            'todo_due_date',
            'todo_completed_date',
            'todo_estimated_time',
            'todo_actual_time',
            'todo_progress'
        );

        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                update_post_meta($post_id, '_' . $field, sanitize_text_field($_POST[$field]));
            }
        }
        
        // Auto-set completed date when status changes to completed
        if (isset($_POST['todo_status']) && $_POST['todo_status'] === 'completed' && empty($_POST['todo_completed_date'])) {
            update_post_meta($post_id, '_todo_completed_date', current_time('Y-m-d\TH:i'));
        }
    }
    
    /**
     * Add custom columns to admin list
     */
    public function add_admin_columns($columns) {
        $columns['todo_status'] = __('Status', 'todo-manager');
        $columns['todo_priority'] = __('Priority', 'todo-manager');
        $columns['todo_category'] = __('Category', 'todo-manager');
        $columns['todo_due_date'] = __('Due Date', 'todo-manager');
        $columns['todo_progress'] = __('Progress', 'todo-manager');
        return $columns;
    }
    
    /**
     * Display custom column content
     */
    public function display_admin_columns($column, $post_id) {
        switch ($column) {
            case 'todo_status':
                $status = get_post_meta($post_id, '_todo_status', true);
                $statuses = array(
                    'pending' => __('Pending', 'todo-manager'),
                    'in_progress' => __('In Progress', 'todo-manager'),
                    'completed' => __('Completed', 'todo-manager'),
                    'cancelled' => __('Cancelled', 'todo-manager')
                );
                echo isset($statuses[$status]) ? $statuses[$status] : __('Pending', 'todo-manager');
                break;
                
            case 'todo_priority':
                $terms = wp_get_post_terms($post_id, 'todo_priority');
                if (!empty($terms)) {
                    $priority_names = array();
                    foreach ($terms as $term) {
                        $priority_names[] = $term->name;
                    }
                    echo implode(', ', $priority_names);
                }
                break;
                
            case 'todo_category':
                $terms = wp_get_post_terms($post_id, 'todo_category');
                if (!empty($terms)) {
                    $category_names = array();
                    foreach ($terms as $term) {
                        $category_names[] = $term->name;
                    }
                    echo implode(', ', $category_names);
                }
                break;
                
            case 'todo_due_date':
                $due_date = get_post_meta($post_id, '_todo_due_date', true);
                if ($due_date) {
                    echo date_i18n(get_option('date_format'), strtotime($due_date));
                }
                break;
                
            case 'todo_progress':
                $progress = get_post_meta($post_id, '_todo_progress', true);
                if ($progress !== '') {
                    echo $progress . '%';
                }
                break;
        }
    }
    
    /**
     * Register REST API routes
     */
    public function register_rest_routes() {
        register_rest_route('todo/v1', '/items', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array($this, 'get_todos'),
                'permission_callback' => array($this, 'get_items_permissions_check'),
            ),
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => array($this, 'create_todo'),
                'permission_callback' => array($this, 'create_item_permissions_check'),
                'args'                => $this->get_endpoint_args_for_item_schema(WP_REST_Server::CREATABLE),
            ),
        ));

        register_rest_route('todo/v1', '/items/(?P<id>\d+)', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array($this, 'get_todo'),
                'permission_callback' => array($this, 'get_item_permissions_check'),
                'args'                => array(
                    'context' => $this->get_context_param(array('default' => 'view')),
                ),
            ),
            array(
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => array($this, 'update_todo'),
                'permission_callback' => array($this, 'update_item_permissions_check'),
                'args'                => $this->get_endpoint_args_for_item_schema(WP_REST_Server::EDITABLE),
            ),
            array(
                'methods'             => WP_REST_Server::DELETABLE,
                'callback'            => array($this, 'delete_todo'),
                'permission_callback' => array($this, 'delete_item_permissions_check'),
                'args'                => array(
                    'force' => array(
                        'type'        => 'boolean',
                        'default'     => false,
                        'description' => __('Whether to bypass Trash and force deletion.', 'todo-manager'),
                    ),
                ),
            ),
        ));
    }
    
    /**
     * Get todos
     */
    public function get_todos($request) {
        $args = array(
            'post_type' => 'todo_item',
            'post_status' => 'publish',
            'posts_per_page' => $request->get_param('per_page') ?: 10,
            'paged' => $request->get_param('page') ?: 1,
        );
        
        // Filter by status
        if ($request->get_param('status')) {
            $args['meta_query'] = array(
                array(
                    'key' => '_todo_status',
                    'value' => $request->get_param('status'),
                    'compare' => '='
                )
            );
        }
        
        $posts = get_posts($args);
        $data = array();
        
        foreach ($posts as $post) {
            $data[] = $this->prepare_item_for_response($post, $request);
        }
        
        return rest_ensure_response($data);
    }
    
    /**
     * Get single todo
     */
    public function get_todo($request) {
        $post = get_post($request['id']);
        
        if (empty($post) || $post->post_type !== 'todo_item') {
            return new WP_Error('todo_invalid_id', __('Invalid todo ID.', 'todo-manager'), array('status' => 404));
        }
        
        $data = $this->prepare_item_for_response($post, $request);
        return rest_ensure_response($data);
    }
    
    /**
     * Create todo
     */
    public function create_todo($request) {
        $post_data = array(
            'post_title'   => $request->get_param('title'),
            'post_content' => $request->get_param('description'),
            'post_type'    => 'todo_item',
            'post_status'  => 'publish',
        );
        
        $post_id = wp_insert_post($post_data);
        
        if (is_wp_error($post_id)) {
            return $post_id;
        }
        
        // Save meta fields
        $meta_fields = array('status', 'due_date', 'estimated_time', 'progress');
        foreach ($meta_fields as $field) {
            if ($request->has_param($field)) {
                update_post_meta($post_id, '_todo_' . $field, $request->get_param($field));
            }
        }
        
        // Set default status
        if (!$request->has_param('status')) {
            update_post_meta($post_id, '_todo_status', 'pending');
        }
        
        $post = get_post($post_id);
        $response = $this->prepare_item_for_response($post, $request);
        $response = rest_ensure_response($response);
        $response->set_status(201);
        
        return $response;
    }
    
    /**
     * Update todo
     */
    public function update_todo($request) {
        $post = get_post($request['id']);
        
        if (empty($post) || $post->post_type !== 'todo_item') {
            return new WP_Error('todo_invalid_id', __('Invalid todo ID.', 'todo-manager'), array('status' => 404));
        }
        
        $post_data = array('ID' => $request['id']);
        
        if ($request->has_param('title')) {
            $post_data['post_title'] = $request->get_param('title');
        }
        
        if ($request->has_param('description')) {
            $post_data['post_content'] = $request->get_param('description');
        }
        
        if (count($post_data) > 1) {
            wp_update_post($post_data);
        }
        
        // Update meta fields
        $meta_fields = array('status', 'due_date', 'completed_date', 'estimated_time', 'actual_time', 'progress');
        foreach ($meta_fields as $field) {
            if ($request->has_param($field)) {
                update_post_meta($request['id'], '_todo_' . $field, $request->get_param($field));
            }
        }
        
        $post = get_post($request['id']);
        $response = $this->prepare_item_for_response($post, $request);
        
        return rest_ensure_response($response);
    }
    
    /**
     * Delete todo
     */
    public function delete_todo($request) {
        $post = get_post($request['id']);
        
        if (empty($post) || $post->post_type !== 'todo_item') {
            return new WP_Error('todo_invalid_id', __('Invalid todo ID.', 'todo-manager'), array('status' => 404));
        }
        
        $force = (bool) $request->get_param('force');
        
        if ($force) {
            $result = wp_delete_post($request['id'], true);
        } else {
            $result = wp_trash_post($request['id']);
        }
        
        if (!$result) {
            return new WP_Error('todo_cannot_delete', __('The todo cannot be deleted.', 'todo-manager'), array('status' => 500));
        }
        
        return rest_ensure_response(array('deleted' => true, 'previous' => $this->prepare_item_for_response($post, $request)));
    }
    
    /**
     * Prepare item for response
     */
    public function prepare_item_for_response($post, $request) {
        $data = array(
            'id'            => $post->ID,
            'title'         => $post->post_title,
            'description'   => $post->post_content,
            'status'        => get_post_meta($post->ID, '_todo_status', true) ?: 'pending',
            'due_date'      => get_post_meta($post->ID, '_todo_due_date', true),
            'completed_date' => get_post_meta($post->ID, '_todo_completed_date', true),
            'estimated_time' => get_post_meta($post->ID, '_todo_estimated_time', true),
            'actual_time'   => get_post_meta($post->ID, '_todo_actual_time', true),
            'progress'      => get_post_meta($post->ID, '_todo_progress', true),
            'created_date'  => $post->post_date,
            'modified_date' => $post->post_modified,
            'author'        => $post->post_author,
        );
        
        // Get taxonomies
        $priorities = wp_get_post_terms($post->ID, 'todo_priority', array('fields' => 'names'));
        $categories = wp_get_post_terms($post->ID, 'todo_category', array('fields' => 'names'));
        
        if (!is_wp_error($priorities)) {
            $data['priorities'] = $priorities;
        }
        
        if (!is_wp_error($categories)) {
            $data['categories'] = $categories;
        }
        
        return $data;
    }
    
    /**
     * Permission callbacks
     */
    public function get_items_permissions_check($request) {
        return true; // Allow reading for all users
    }
    
    public function get_item_permissions_check($request) {
        return true; // Allow reading for all users
    }
    
    public function create_item_permissions_check($request) {
        return current_user_can('edit_posts');
    }
    
    public function update_item_permissions_check($request) {
        $post = get_post($request['id']);
        return current_user_can('edit_post', $post->ID);
    }
    
    public function delete_item_permissions_check($request) {
        $post = get_post($request['id']);
        return current_user_can('delete_post', $post->ID);
    }
    
    /**
     * Get endpoint args for item schema
     */
    public function get_endpoint_args_for_item_schema($method) {
        $args = array(
            'title' => array(
                'type'        => 'string',
                'description' => __('The title for the todo item.', 'todo-manager'),
            ),
            'description' => array(
                'type'        => 'string',
                'description' => __('The description for the todo item.', 'todo-manager'),
            ),
            'status' => array(
                'type'        => 'string',
                'enum'        => array('pending', 'in_progress', 'completed', 'cancelled'),
                'description' => __('The status of the todo item.', 'todo-manager'),
            ),
            'due_date' => array(
                'type'        => 'string',
                'format'      => 'date',
                'description' => __('The due date for the todo item.', 'todo-manager'),
            ),
            'estimated_time' => array(
                'type'        => 'number',
                'description' => __('The estimated time in hours for the todo item.', 'todo-manager'),
            ),
            'progress' => array(
                'type'        => 'integer',
                'minimum'     => 0,
                'maximum'     => 100,
                'description' => __('The progress percentage of the todo item.', 'todo-manager'),
            ),
        );
        
        if ($method === WP_REST_Server::CREATABLE) {
            $args['title']['required'] = true;
        }
        
        return $args;
    }
    
    /**
     * Get context param
     */
    public function get_context_param($args = array()) {
        return array_merge(array(
            'type'        => 'string',
            'enum'        => array('view', 'embed', 'edit'),
            'description' => __('Scope under which the request is made; determines fields present in response.', 'todo-manager'),
        ), $args);
    }
    
    /**
     * Plugin activation
     */
    public function activate() {
        $this->register_post_type();
        $this->register_taxonomies();
        flush_rewrite_rules();
        
        // Create default priorities
        $default_priorities = array('Low', 'Medium', 'High', 'Critical');
        foreach ($default_priorities as $priority) {
            if (!term_exists($priority, 'todo_priority')) {
                wp_insert_term($priority, 'todo_priority');
            }
        }
        
        // Create default categories
        $default_categories = array('Personal', 'Work', 'Projects', 'Ideas');
        foreach ($default_categories as $category) {
            if (!term_exists($category, 'todo_category')) {
                wp_insert_term($category, 'todo_category');
            }
        }
    }
    
    /**
     * Plugin deactivation
     */
    public function deactivate() {
        flush_rewrite_rules();
    }
}

// Initialize the plugin
new TodoManager();