<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
    <?php wp_body_open(); ?>
    
    <header class="site-header">
        <div class="container">
            <h1 class="site-title">
                <a href="<?php echo esc_url(home_url('/')); ?>" rel="home">
                    <?php bloginfo('name'); ?>
                </a>
            </h1>
            
            <?php if (get_bloginfo('description', 'display')) : ?>
                <p class="site-description"><?php bloginfo('description'); ?></p>
            <?php endif; ?>
            
            <nav class="main-navigation">
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'primary',
                    'menu_id' => 'primary-menu',
                    'fallback_cb' => 'todo_organizer_default_menu',
                ));
                ?>
            </nav>
        </div>
    </header>

    <?php
    /**
     * Default menu fallback if no menu is set
     */
    function todo_organizer_default_menu() {
        ?>
        <ul id="primary-menu" class="menu">
            <li><a href="<?php echo home_url('/'); ?>"><?php _e('Home', 'todo-organizer'); ?></a></li>
            <?php if (post_type_exists('todo_item')) : ?>
                <li><a href="<?php echo get_post_type_archive_link('todo_item'); ?>"><?php _e('All Todos', 'todo-organizer'); ?></a></li>
                <?php if (current_user_can('edit_posts')) : ?>
                    <li><a href="<?php echo admin_url('post-new.php?post_type=todo_item'); ?>"><?php _e('Add Todo', 'todo-organizer'); ?></a></li>
                <?php endif; ?>
            <?php endif; ?>
            <?php if (current_user_can('manage_options')) : ?>
                <li><a href="<?php echo admin_url(); ?>"><?php _e('Admin', 'todo-organizer'); ?></a></li>
            <?php endif; ?>
        </ul>
        <?php
    }
    ?>