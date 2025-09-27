<?php get_header(); ?>

<div class="container">
    <div class="main-content">
        <main class="content-area">
            <?php
            // Check if we're on the todo items archive or home page
            if (is_home() || is_front_page() || (is_archive() && get_post_type() === 'todo_item')) {
                // Display todo stats
                get_template_part('template-parts/todo-stats');
                
                // Display todo filters
                get_template_part('template-parts/todo-filters');
                
                // Display todos in grid
                ?>
                <div class="todo-grid">
                    <?php
                    if (have_posts()) :
                        while (have_posts()) : the_post();
                            get_template_part('template-parts/todo-card');
                        endwhile;
                    else :
                        ?>
                        <div class="no-todos">
                            <h2><?php _e('No todo items found', 'todo-organizer'); ?></h2>
                            <p><?php _e('Get organized by creating your first todo item!', 'todo-organizer'); ?></p>
                            <?php if (current_user_can('edit_posts')) : ?>
                                <a href="<?php echo admin_url('post-new.php?post_type=todo_item'); ?>" class="btn">
                                    <?php _e('Create Todo', 'todo-organizer'); ?>
                                </a>
                            <?php endif; ?>
                        </div>
                        <?php
                    endif;
                    ?>
                </div>
                
                <?php
                // Pagination
                the_posts_pagination(array(
                    'mid_size' => 2,
                    'prev_text' => __('&laquo; Previous', 'todo-organizer'),
                    'next_text' => __('Next &raquo;', 'todo-organizer'),
                ));
                
            } else {
                // Default WordPress loop for other content
                if (have_posts()) :
                    while (have_posts()) : the_post();
                        ?>
                        <article <?php post_class('todo-card'); ?>>
                            <h2 class="todo-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h2>
                            
                            <div class="todo-meta">
                                <span class="todo-date"><?php echo get_the_date(); ?></span>
                                <span class="todo-author"><?php the_author(); ?></span>
                            </div>
                            
                            <div class="todo-description">
                                <?php the_excerpt(); ?>
                            </div>
                            
                            <a href="<?php the_permalink(); ?>" class="btn">
                                <?php _e('Read More', 'todo-organizer'); ?>
                            </a>
                        </article>
                        <?php
                    endwhile;
                    
                    the_posts_pagination();
                    
                else :
                    ?>
                    <div class="no-content">
                        <h2><?php _e('Nothing found', 'todo-organizer'); ?></h2>
                        <p><?php _e('It seems we can\'t find what you\'re looking for.', 'todo-organizer'); ?></p>
                    </div>
                    <?php
                endif;
            }
            ?>
        </main>

        <?php get_sidebar(); ?>
    </div>
</div>

<?php get_footer(); ?>