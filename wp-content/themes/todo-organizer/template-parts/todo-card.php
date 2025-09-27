<?php
/**
 * Template part for displaying todo cards
 *
 * @package TodoOrganizer
 */

$post_id = get_the_ID();
$status = get_post_meta($post_id, '_todo_status', true) ?: 'pending';
$due_date = get_post_meta($post_id, '_todo_due_date', true);
$progress = get_post_meta($post_id, '_todo_progress', true);
$priority_class = todo_organizer_get_priority_class($post_id);
$status_class = todo_organizer_get_status_class($post_id);
?>

<article <?php post_class('todo-card ' . $priority_class . ' ' . $status_class); ?> id="post-<?php the_ID(); ?>">
    <h2 class="todo-title">
        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
    </h2>
    
    <div class="todo-meta">
        <?php
        // Status badge
        $status_labels = array(
            'pending' => __('Pending', 'todo-organizer'),
            'in_progress' => __('In Progress', 'todo-organizer'),
            'completed' => __('Completed', 'todo-organizer'),
            'cancelled' => __('Cancelled', 'todo-organizer')
        );
        ?>
        <span class="todo-status <?php echo esc_attr($status); ?>">
            <?php echo isset($status_labels[$status]) ? $status_labels[$status] : $status_labels['pending']; ?>
        </span>
        
        <?php
        // Priority badge
        $priorities = wp_get_post_terms($post_id, 'todo_priority', array('fields' => 'names'));
        if (!empty($priorities) && !is_wp_error($priorities)) :
        ?>
            <span class="todo-priority"><?php echo esc_html($priorities[0]); ?></span>
        <?php endif; ?>
        
        <?php
        // Category badge
        $categories = wp_get_post_terms($post_id, 'todo_category', array('fields' => 'names'));
        if (!empty($categories) && !is_wp_error($categories)) :
        ?>
            <span class="todo-category"><?php echo esc_html($categories[0]); ?></span>
        <?php endif; ?>
    </div>
    
    <?php if (has_excerpt() || get_the_content()) : ?>
        <div class="todo-description">
            <?php 
            if (has_excerpt()) {
                the_excerpt();
            } else {
                echo wp_trim_words(get_the_content(), 20, '...');
            }
            ?>
        </div>
    <?php endif; ?>
    
    <?php if ($progress && $progress > 0) : ?>
        <div class="todo-progress">
            <div class="progress-bar">
                <div class="progress-fill" style="width: <?php echo intval($progress); ?>%"></div>
            </div>
            <div class="progress-text"><?php echo intval($progress); ?>% <?php _e('Complete', 'todo-organizer'); ?></div>
        </div>
    <?php endif; ?>
    
    <div class="todo-dates">
        <span class="todo-created"><?php echo get_the_date(); ?></span>
        <?php echo todo_organizer_format_due_date($post_id); ?>
    </div>
</article>