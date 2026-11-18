<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
/**
 * Post Carousel view.
 */

$settings = $this->get_settings_for_display();

$args = [
    'post_type' => 'post',
    'posts_per_page' => min(30, max(1, (int) ($settings['posts_per_page'] ?? 8))),
    'orderby' => in_array($settings['orderby'] ?? 'date', ['date', 'title', 'comment_count', 'rand'], true) ? $settings['orderby'] : 'date',
    'order' => 'ASC' === ($settings['order'] ?? 'DESC') ? 'ASC' : 'DESC',
    'ignore_sticky_posts' => true,
    'no_found_rows' => true,
];

if (!empty($settings['category']) && is_array($settings['category'])) {
    $args['category_name'] = implode(',', array_map('sanitize_title', $settings['category']));
}
$args = $this->blogkit_apply_query_args($args, $settings);

$query = new WP_Query($args);
$this->blogkit_output_schema($query, $settings);

if (!$query->have_posts()) {
    echo '<p>' . esc_html__('No posts found.', 'blogkit') . '</p>';
    return;
}

$allowed_tags = ['h2', 'h3', 'h4', 'h5', 'h6', 'div', 'span', 'p'];
$title_tag = in_array($settings['title_tag'] ?? 'h3', $allowed_tags, true) ? $settings['title_tag'] : 'h3';

$per_view = static function ($key, $default) use ($settings) {
    $value = isset($settings[$key]) && '' !== $settings[$key] ? (int) $settings[$key] : $default;
    return max(1, min(6, $value));
};
$space = static function ($key, $default) use ($settings) {
    $value = isset($settings[$key]) && '' !== $settings[$key] ? (int) $settings[$key] : $default;
    return max(0, min(100, $value));
};

$config = [
    'loop' => 'yes' === ($settings['loop'] ?? ''),
    'autoplay' => 'yes' === ($settings['autoplay'] ?? '') ? max(1000, min(20000, (int) ($settings['autoplay_delay'] ?? 4000))) : 0,
    'perView' => ['desktop' => $per_view('slides_per_view', 3), 'tablet' => $per_view('slides_per_view_tablet', 2), 'mobile' => $per_view('slides_per_view_mobile', 1)],
    'space' => ['desktop' => $space('space_between', 24), 'tablet' => $space('space_between_tablet', 20), 'mobile' => $space('space_between_mobile', 16)],
];
$uid = 'blogkit-pc-' . sanitize_key($this->get_id());
?>
<div class="blogkit-post-carousel-wrap">
    <div id="<?php echo esc_attr($uid); ?>" class="blogkit-post-carousel swiper"
        data-config="<?php echo esc_attr(wp_json_encode($config)); ?>"
        data-prev="#<?php echo esc_attr($uid); ?>-prev" data-next="#<?php echo esc_attr($uid); ?>-next"
        data-dots="#<?php echo esc_attr($uid); ?>-dots">
        <div class="swiper-wrapper">
            <?php
            while ($query->have_posts()) :
                $query->the_post();
                $categories = get_the_category();
                ?>
                <div class="swiper-slide">
                    <article class="blogkit-pc-card">
                        <?php $this->blogkit_render_format_badge($settings); ?>
                        <?php if ('yes' === ($settings['show_image'] ?? 'yes')) : ?>
                            <a class="blogkit-pc-thumb" href="<?php echo esc_url(get_permalink()); ?>" tabindex="-1" aria-hidden="true">
                                <?php
                                if (has_post_thumbnail()) {
                                    echo wp_kses_post(get_the_post_thumbnail(null, 'medium_large'));
                                } else {
                                    echo '<img src="' . esc_url(\BlogKit\Admin\Settings::fallback_image()) . '" alt="" loading="lazy">';
                                }
                                ?>
                            </a>
                        <?php endif; ?>
                        <div class="blogkit-pc-body">
                            <?php if ('yes' === ($settings['show_category'] ?? 'yes') && !empty($categories)) : ?>
                                <a class="blogkit-pc-badge" href="<?php echo esc_url(get_category_link($categories[0]->term_id)); ?>"><?php echo esc_html($categories[0]->name); ?></a>
                            <?php endif; ?>
                            <<?php echo esc_html($title_tag); ?> class="blogkit-pc-title"><a href="<?php echo esc_url(get_permalink()); ?>"><?php echo esc_html(get_the_title()); ?></a></<?php echo esc_html($title_tag); ?>>
                            <?php if ('yes' === ($settings['show_meta'] ?? 'yes')) : ?>
                                <div class="blogkit-pc-meta">
                                    <span><?php echo esc_html(get_the_author()); ?></span>
                                    <span><?php echo esc_html(get_the_date()); ?></span>
                                </div>
                            <?php endif; ?>
                            <?php if ('yes' === ($settings['show_excerpt'] ?? 'yes')) : ?>
                                <p class="blogkit-pc-excerpt"><?php echo esc_html(wp_trim_words(get_the_excerpt(), max(5, min(80, (int) ($settings['excerpt_length'] ?? 18))))); ?></p>
                            <?php endif; ?>
                            <?php if (!empty($settings['read_more_text'])) : ?>
                                <a class="blogkit-pc-more" href="<?php echo esc_url(get_permalink()); ?>"><?php echo esc_html($settings['read_more_text']); ?></a>
                            <?php endif; ?>
                        </div>
                    </article>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
    <?php if ('yes' === ($settings['arrows'] ?? 'yes')) : ?>
        <button type="button" id="<?php echo esc_attr($uid); ?>-prev" class="blogkit-pc-nav blogkit-pc-prev" aria-label="<?php echo esc_attr__('Previous slide', 'blogkit'); ?>">&#8249;</button>
        <button type="button" id="<?php echo esc_attr($uid); ?>-next" class="blogkit-pc-nav blogkit-pc-next" aria-label="<?php echo esc_attr__('Next slide', 'blogkit'); ?>">&#8250;</button>
    <?php endif; ?>
    <?php if ('yes' === ($settings['dots'] ?? 'yes')) : ?>
        <div id="<?php echo esc_attr($uid); ?>-dots" class="swiper-pagination blogkit-pc-dots"></div>
    <?php endif; ?>
</div>
<?php
wp_reset_postdata();
