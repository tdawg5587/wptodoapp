<?php
/**
 * Template part for displaying todo filters
 *
 * @package TodoOrganizer
 */

// Only show filters if there are todos
$todo_count = wp_count_posts('todo_item')->publish;
if ($todo_count == 0) {
    return;
}

$current_status = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : '';
$current_priority = isset($_GET['priority']) ? sanitize_text_field($_GET['priority']) : '';
$current_category = isset($_GET['category']) ? sanitize_text_field($_GET['category']) : '';
?>

<div class="todo-filters">
    <form method="get" action="<?php echo esc_url(home_url('/')); ?>">
        <div class="filter-group">
            <label for="status-filter"><?php _e('Status:', 'todo-organizer'); ?></label>
            <select name="status" id="status-filter">
                <option value=""><?php _e('All Statuses', 'todo-organizer'); ?></option>
                <option value="pending" <?php selected($current_status, 'pending'); ?>><?php _e('Pending', 'todo-organizer'); ?></option>
                <option value="in_progress" <?php selected($current_status, 'in_progress'); ?>><?php _e('In Progress', 'todo-organizer'); ?></option>
                <option value="completed" <?php selected($current_status, 'completed'); ?>><?php _e('Completed', 'todo-organizer'); ?></option>
                <option value="cancelled" <?php selected($current_status, 'cancelled'); ?>><?php _e('Cancelled', 'todo-organizer'); ?></option>
            </select>
            
            <label for="priority-filter"><?php _e('Priority:', 'todo-organizer'); ?></label>
            <select name="priority" id="priority-filter">
                <option value=""><?php _e('All Priorities', 'todo-organizer'); ?></option>
                <?php
                $priorities = get_terms(array(
                    'taxonomy' => 'todo_priority',
                    'hide_empty' => false,
                ));
                foreach ($priorities as $priority) {
                    echo '<option value="' . esc_attr($priority->slug) . '"' . selected($current_priority, $priority->slug, false) . '>' . esc_html($priority->name) . '</option>';
                }
                ?>
            </select>
            
            <label for="category-filter"><?php _e('Category:', 'todo-organizer'); ?></label>
            <select name="category" id="category-filter">
                <option value=""><?php _e('All Categories', 'todo-organizer'); ?></option>
                <?php
                $categories = get_terms(array(
                    'taxonomy' => 'todo_category',
                    'hide_empty' => false,
                ));
                foreach ($categories as $category) {
                    echo '<option value="' . esc_attr($category->slug) . '"' . selected($current_category, $category->slug, false) . '>' . esc_html($category->name) . '</option>';
                }
                ?>
            </select>
            
            <input type="submit" value="<?php _e('Filter', 'todo-organizer'); ?>" class="btn">
            
            <?php if ($current_status || $current_priority || $current_category) : ?>
                <a href="<?php echo esc_url(remove_query_arg(array('status', 'priority', 'category'))); ?>" class="btn btn-secondary">
                    <?php _e('Clear Filters', 'todo-organizer'); ?>
                </a>
            <?php endif; ?>
        </div>
    </form>
</div>