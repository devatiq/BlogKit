<?php
if (!defined('ABSPATH')) {
    exit;
}

$settings = $this->get_settings_for_display();

// === Featured Post Query ===
$featured_args = [
    'post_type' => 'post',
    'posts_per_page' => 1,
];

if (!empty($settings['featured_posts'])) {
    $featured_ids = is_array($settings['featured_posts']) ? $settings['featured_posts'] : (array) $settings['featured_posts'];
    $featured_args = [
        'post_type' => 'post',
        'post__in' => array_map('absint', $featured_ids),
        'orderby' => 'post__in',
        'posts_per_page' => 1,
        'offset' => 0, // offset ignored when post__in is used
    ];
}

$featured_query = new WP_Query($featured_args);


// === Sidebar Post Query ===
$sidebar_args = [
    'post_type' => 'post',
    'posts_per_page' => 4,
    'offset' => 1,
];

if (!empty($settings['sidebar_posts'])) {
    $sidebar_ids = is_array($settings['sidebar_posts']) ? $settings['sidebar_posts'] : (array) $settings['sidebar_posts'];
    $sidebar_args = [
        'post_type' => 'post',
        'post__in' => array_map('absint', $sidebar_ids),
        'orderby' => 'post__in',
        'posts_per_page' => count($sidebar_ids),
        'offset' => 0,
    ];
}

$sidebar_query = new WP_Query($sidebar_args);

?>
<!-- Featured Sidebar Widget -->
<div class="blogkit-fs-widget">
    <div class="blogkit-fs-grid">
        <!-- Featured Post -->
        <div class="blogkit-fs-featured">
            <?php if ($featured_query->have_posts()):
                while ($featured_query->have_posts()):
                    $featured_query->the_post(); ?>
                    <!-- Featured Post Thumbnail -->
                    <a href="<?php the_permalink(); ?>" class="blogkit-fs-featured-thumb">
                        <?php if (has_post_thumbnail()): ?>
                            <?php the_post_thumbnail('large'); ?>
                        <?php else: ?>
                            <img src="<?php echo esc_url(BLOGKIT_ELEMENTOR_ASSETS . '/img/placeholder.png'); ?>"
                                alt="<?php the_title_attribute(); ?>">
                        <?php endif; ?>
                    </a><!-- / Featured Post Thumbnail -->

                    <!-- Featured Post Content -->
                    <div class="blogkit-fs-featured-content">
                        <?php
                        $cat = get_the_category();
                        if (!empty($cat)) {
                            echo '<span class="blogkit-fs-category">' . esc_html($cat[0]->name) . '</span>';
                        }
                        ?>
                        <!-- Featured Post Title -->
                        <h2 class="blogkit-fs-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                        <!-- Meta -->
                        <div class="blogkit-fs-meta">
                            <span
                                class="blogkit-fs-author"><?php echo esc_html__('BY', 'blogkit') . ' ' . get_the_author(); ?></span>
                            <span class="blogkit-fs-date"><?php echo get_the_date(); ?></span>
                        </div><!-- / Meta -->
                    </div><!-- / Featured Post Content -->
                <?php endwhile;
                wp_reset_postdata();
            endif; ?>
        </div><!-- / Featured Sidebar Widget -->

        <!-- Sidebar Posts -->
        <div class="blogkit-fs-sidebar">
            <?php if ($sidebar_query->have_posts()):
                while ($sidebar_query->have_posts()):
                    $sidebar_query->the_post(); ?>
                    <!-- Sidebar Single Post -->
                    <div class="blogkit-fs-sidebar-item">
                        <!-- Sidebar Post Thumbnail -->
                        <a href="<?php the_permalink(); ?>" class="blogkit-fs-sidebar-thumb">
                            <?php if (has_post_thumbnail()): ?>
                                <?php the_post_thumbnail('thumbnail'); ?>
                            <?php else: ?>
                                <img src="<?php echo esc_url(BLOGKIT_ELEMENTOR_ASSETS . '/img/placeholder.png'); ?>"
                                    alt="<?php the_title_attribute(); ?>">
                            <?php endif; ?>
                        </a><!-- / Sidebar Post Thumbnail -->
                        <!-- Sidebar Post Content -->
                        <div class="blogkit-fs-sidebar-content">
                            <!-- Post title -->
                            <h4 class="blogkit-fs-sidebar-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h4><!-- / Post title -->

                            <!-- Post meta -->
                            <div class="blogkit-fs-sidebar-meta">
                                <?php
                                $cat = get_the_category();
                                if (!empty($cat)) {
                                    echo '<span class="blogkit-fs-badge">' . esc_html($cat[0]->name) . '</span>';
                                }
                                ?>
                                <span class="blogkit-fs-sidebar-date"><?php echo get_the_date(); ?></span>
                            </div><!-- / Post meta -->
                        </div><!-- / Sidebar Post Content -->
                    </div><!-- / Sidebar Single Post -->
                <?php endwhile;
                wp_reset_postdata();
            endif; ?>
        </div><!-- / Featured Sidebar Widget -->
    </div>
</div>