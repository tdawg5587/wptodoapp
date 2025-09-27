<?php
/**
 * The sidebar containing the main widget area
 *
 * @package TodoOrganizer
 */

if (!is_active_sidebar('sidebar-1')) {
    return;
}
?>

<aside id="secondary" class="sidebar">
    <?php
    // Check if we're on a todo page to show todo-specific sidebar
    if (is_post_type_archive('todo_item') || is_singular('todo_item') || (is_home() && get_option('show_on_front') === 'posts')) :
    ?>
        
        <!-- Todo Quick Stats -->
        <div class="widget">
            <h2 class="widget-title"><?php _e('Todo Overview', 'todo-organizer'); ?></h2>
            <?php
            $stats = todo_organizer_get_todo_stats();
            if ($stats) :
            ?>
                <ul class="todo-sidebar-stats">
                    <li><strong><?php echo $stats['total']; ?></strong> <?php _e('Total', 'todo-organizer'); ?></li>
                    <li><strong><?php echo $stats['pending']; ?></strong> <?php _e('Pending', 'todo-organizer'); ?></li>
                    <li><strong><?php echo $stats['in_progress']; ?></strong> <?php _e('In Progress', 'todo-organizer'); ?></li>
                    <li><strong><?php echo $stats['completed']; ?></strong> <?php _e('Completed', 'todo-organizer'); ?></li>
                    <?php if ($stats['overdue'] > 0) : ?>
                        <li><strong style="color: #e74c3c;"><?php echo $stats['overdue']; ?></strong> <?php _e('Overdue', 'todo-organizer'); ?></li>
                    <?php endif; ?>
                </ul>
            <?php endif; ?>
        </div>

        <!-- Todo Categories -->
        <?php
        $categories = get_terms(array(
            'taxonomy' => 'todo_category',
            'hide_empty' => true,
        ));
        
        if (!empty($categories) && !is_wp_error($categories)) :
        ?>
            <div class="widget">
                <h2 class="widget-title"><?php _e('Categories', 'todo-organizer'); ?></h2>
                <ul>
                    <?php foreach ($categories as $category) : ?>
                        <li>
                            <a href="<?php echo get_term_link($category); ?>">
                                <?php echo esc_html($category->name); ?>
                                <span class="count">(<?php echo $category->count; ?>)</span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Todo Priorities -->
        <?php
        $priorities = get_terms(array(
            'taxonomy' => 'todo_priority',
            'hide_empty' => true,
        ));
        
        if (!empty($priorities) && !is_wp_error($priorities)) :
        ?>
            <div class="widget">
                <h2 class="widget-title"><?php _e('Priorities', 'todo-organizer'); ?></h2>
                <ul>
                    <?php foreach ($priorities as $priority) : ?>
                        <li>
                            <a href="<?php echo get_term_link($priority); ?>">
                                <?php echo esc_html($priority->name); ?>
                                <span class="count">(<?php echo $priority->count; ?>)</span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Quick Actions -->
        <?php if (current_user_can('edit_posts')) : ?>
            <div class="widget">
                <h2 class="widget-title"><?php _e('Quick Actions', 'todo-organizer'); ?></h2>
                <ul class="quick-actions">
                    <li><a href="<?php echo admin_url('post-new.php?post_type=todo_item'); ?>" class="btn btn-success"><?php _e('Add New Todo', 'todo-organizer'); ?></a></li>
                    <li><a href="<?php echo admin_url('edit.php?post_type=todo_item'); ?>" class="btn"><?php _e('Manage Todos', 'todo-organizer'); ?></a></li>
                    <?php if (current_user_can('manage_categories')) : ?>
                        <li><a href="<?php echo admin_url('edit-tags.php?taxonomy=todo_category&post_type=todo_item'); ?>" class="btn btn-secondary"><?php _e('Manage Categories', 'todo-organizer'); ?></a></li>
                    <?php endif; ?>
                </ul>
            </div>
        <?php endif; ?>

    <?php endif; ?>

    <!-- Default WordPress Widgets -->
    <?php dynamic_sidebar('sidebar-1'); ?>
    
</aside><!-- #secondary -->

<style>
.todo-sidebar-stats {
    list-style: none;
    padding: 0;
}

.todo-sidebar-stats li {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    border-bottom: 1px solid #eee;
}

.quick-actions {
    list-style: none;
    padding: 0;
}

.quick-actions li {
    margin-bottom: 10px;
}

.quick-actions .btn {
    width: 100%;
    text-align: center;
    font-size: 14px;
    padding: 8px 16px;
}

.sidebar .count {
    color: #999;
    font-size: 0.9em;
}
</style>