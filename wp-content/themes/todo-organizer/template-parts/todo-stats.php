<?php
/**
 * Template part for displaying todo statistics
 *
 * @package TodoOrganizer
 */

$stats = todo_organizer_get_todo_stats();
if (!$stats || $stats['total'] == 0) {
    return;
}
?>

<div class="todo-stats">
    <div class="stat-card">
        <div class="stat-number"><?php echo $stats['total']; ?></div>
        <div class="stat-label"><?php _e('Total Todos', 'todo-organizer'); ?></div>
    </div>
    
    <div class="stat-card">
        <div class="stat-number"><?php echo $stats['pending']; ?></div>
        <div class="stat-label"><?php _e('Pending', 'todo-organizer'); ?></div>
    </div>
    
    <div class="stat-card">
        <div class="stat-number"><?php echo $stats['in_progress']; ?></div>
        <div class="stat-label"><?php _e('In Progress', 'todo-organizer'); ?></div>
    </div>
    
    <div class="stat-card">
        <div class="stat-number"><?php echo $stats['completed']; ?></div>
        <div class="stat-label"><?php _e('Completed', 'todo-organizer'); ?></div>
    </div>
    
    <?php if ($stats['overdue'] > 0) : ?>
        <div class="stat-card" style="border-left: 4px solid #e74c3c;">
            <div class="stat-number" style="color: #e74c3c;"><?php echo $stats['overdue']; ?></div>
            <div class="stat-label"><?php _e('Overdue', 'todo-organizer'); ?></div>
        </div>
    <?php endif; ?>
</div>