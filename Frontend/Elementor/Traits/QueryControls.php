<?php
namespace BlogKit\Frontend\Elementor\Traits;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

use Elementor\Controls_Manager;

/**
 * Shared "extra query options" for post based widgets.
 *
 * Adds tags, authors, post types, exclude posts and date range controls,
 * and converts the saved settings into WP_Query arguments.
 */
trait QueryControls
{
    /**
     * Register the extra query controls. Call inside an open controls section.
     */
    protected function blogkit_register_query_controls()
    {
        $this->add_control(
            'blogkit_post_types',
            [
                'label' => esc_html__('Post Types', 'blogkit'),
                'type' => Controls_Manager::SELECT2,
                'options' => $this->blogkit_get_post_type_options(),
                'default' => ['post'],
                'multiple' => true,
                'label_block' => true,
                'description' => esc_html__('Category and Tag filters apply to Posts only.', 'blogkit'),
            ]
        );

        $this->add_control(
            'blogkit_tags',
            [
                'label' => esc_html__('Tags', 'blogkit'),
                'type' => Controls_Manager::SELECT2,
                'options' => $this->blogkit_get_tag_options(),
                'multiple' => true,
                'label_block' => true,
            ]
        );

        $this->add_control(
            'blogkit_authors',
            [
                'label' => esc_html__('Authors', 'blogkit'),
                'type' => Controls_Manager::SELECT2,
                'options' => $this->blogkit_get_author_options(),
                'multiple' => true,
                'label_block' => true,
            ]
        );

        $this->add_control(
            'blogkit_exclude_posts',
            [
                'label' => esc_html__('Exclude Posts (IDs)', 'blogkit'),
                'type' => Controls_Manager::TEXT,
                'placeholder' => '12, 45, 78',
                'description' => esc_html__('Comma separated post IDs.', 'blogkit'),
                'label_block' => true,
            ]
        );

        $this->add_control(
            'blogkit_date_range',
            [
                'label' => esc_html__('Date Range', 'blogkit'),
                'type' => Controls_Manager::SELECT,
                'default' => 'any',
                'options' => [
                    'any' => esc_html__('Any time', 'blogkit'),
                    '7' => esc_html__('Last 7 days', 'blogkit'),
                    '30' => esc_html__('Last 30 days', 'blogkit'),
                    '90' => esc_html__('Last 90 days', 'blogkit'),
                    '365' => esc_html__('Last year', 'blogkit'),
                    'custom' => esc_html__('Custom', 'blogkit'),
                ],
            ]
        );

        $this->add_control(
            'blogkit_date_after',
            [
                'label' => esc_html__('From', 'blogkit'),
                'type' => Controls_Manager::DATE_TIME,
                'picker_options' => ['enableTime' => false],
                'condition' => ['blogkit_date_range' => 'custom'],
            ]
        );

        $this->add_control(
            'blogkit_date_before',
            [
                'label' => esc_html__('To', 'blogkit'),
                'type' => Controls_Manager::DATE_TIME,
                'picker_options' => ['enableTime' => false],
                'condition' => ['blogkit_date_range' => 'custom'],
            ]
        );

        $this->add_control(
            'blogkit_schema',
            [
                'label' => esc_html__('Schema Markup (ItemList)', 'blogkit'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('On', 'blogkit'),
                'label_off' => esc_html__('Off', 'blogkit'),
                'return_value' => 'yes',
                'default' => '',
                'description' => esc_html__('Adds JSON-LD structured data for the listed posts. Turn it off if your SEO plugin already outputs it.', 'blogkit'),
            ]
        );
    }

    /**
     * Print schema.org ItemList JSON-LD for the posts of a query (opt-in).
     *
     * @param \WP_Query $query    The widget query.
     * @param array     $settings Widget settings.
     */
    protected function blogkit_output_schema($query, array $settings)
    {
        if (empty($settings['blogkit_schema']) || 'yes' !== $settings['blogkit_schema'] || empty($query->posts)) {
            return;
        }

        $per_page = max(1, (int) $query->get('posts_per_page'));
        $offset = max(0, ((int) $query->get('paged') ?: 1) - 1) * $per_page;
        $items = [];

        foreach (array_values($query->posts) as $index => $post) {
            $items[] = [
                '@type' => 'ListItem',
                'position' => $offset + $index + 1,
                'url' => get_permalink($post),
                'name' => wp_strip_all_tags(get_the_title($post)),
            ];
        }

        $data = [
            '@context' => 'https://schema.org',
            '@type' => 'ItemList',
            'itemListElement' => $items,
        ];

        wp_print_inline_script_tag(
            wp_json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP),
            ['type' => 'application/ld+json']
        );
    }

    /**
     * Merge the extra query settings into WP_Query arguments.
     *
     * @param array $args     Existing query arguments.
     * @param array $settings Widget settings.
     * @return array
     */
    protected function blogkit_apply_query_args(array $args, array $settings)
    {
        // Never let a widget load an unbounded number of posts (-1, 9999...).
        if (isset($args['posts_per_page'])) {
            $per_page = (int) $args['posts_per_page'];
            $args['posts_per_page'] = $per_page < 1 ? 100 : min(100, $per_page);
        }

        // Post types (only registered, public types).
        if (!empty($settings['blogkit_post_types']) && is_array($settings['blogkit_post_types'])) {
            $types = array_values(array_intersect(
                array_map('sanitize_key', $settings['blogkit_post_types']),
                array_keys($this->blogkit_get_post_type_options())
            ));
            if ($types) {
                $args['post_type'] = $types;
            }
        }

        if (!empty($settings['blogkit_tags']) && is_array($settings['blogkit_tags'])) {
            $args['tag_slug__in'] = array_map('sanitize_title', $settings['blogkit_tags']);
        }

        if (!empty($settings['blogkit_authors']) && is_array($settings['blogkit_authors'])) {
            $args['author__in'] = array_filter(array_map('absint', $settings['blogkit_authors']));
        }

        if (!empty($settings['blogkit_exclude_posts'])) {
            $ids = array_filter(array_map('absint', explode(',', (string) $settings['blogkit_exclude_posts'])));
            if ($ids) {
                $args['post__not_in'] = array_values($ids);
            }
        }

        $range = isset($settings['blogkit_date_range']) ? (string) $settings['blogkit_date_range'] : 'any';
        if (in_array($range, ['7', '30', '90', '365'], true)) {
            $args['date_query'] = [
                [
                    'after' => absint($range) . ' days ago',
                    'inclusive' => true,
                ],
            ];
        } elseif ('custom' === $range) {
            $date_query = ['inclusive' => true];
            foreach (['after' => 'blogkit_date_after', 'before' => 'blogkit_date_before'] as $key => $setting) {
                if (!empty($settings[$setting])) {
                    $value = sanitize_text_field($settings[$setting]);
                    if (false !== strtotime($value)) {
                        $date_query[$key] = $value;
                    }
                }
            }
            if (count($date_query) > 1) {
                $args['date_query'] = [$date_query];
            }
        }

        return $args;
    }

    /**
     * Register the pagination type controls (numbers, load more, infinite scroll).
     *
     * @param array $condition Optional Elementor condition for the controls.
     */
    protected function blogkit_register_pagination_controls(array $condition = [])
    {
        $this->add_control(
            'blogkit_pagination_type',
            [
                'label' => esc_html__('Pagination Type', 'blogkit'),
                'type' => Controls_Manager::SELECT,
                'default' => 'numbers',
                'options' => [
                    'numbers' => esc_html__('Page numbers', 'blogkit'),
                    'load_more' => esc_html__('Load more button', 'blogkit'),
                    'infinite' => esc_html__('Infinite scroll', 'blogkit'),
                ],
                'condition' => $condition,
            ]
        );

        $this->add_control(
            'blogkit_load_more_text',
            [
                'label' => esc_html__('Button Text', 'blogkit'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Load More', 'blogkit'),
                'condition' => $condition + ['blogkit_pagination_type' => ['load_more', 'infinite']],
            ]
        );
    }

    /**
     * Output the pagination for a widget.
     *
     * @param string $links     Result of paginate_links() (list markup) or empty.
     * @param array  $settings  Widget settings.
     * @param int    $paged     Current page.
     * @param int    $max_pages Total pages.
     */
    protected function blogkit_render_pagination($links, array $settings, $paged, $max_pages)
    {
        $type = isset($settings['blogkit_pagination_type']) ? $settings['blogkit_pagination_type'] : 'numbers';

        if (in_array($type, ['load_more', 'infinite'], true)) {
            if ((int) $paged < (int) $max_pages) {
                $text = !empty($settings['blogkit_load_more_text']) ? $settings['blogkit_load_more_text'] : __('Load More', 'blogkit');
                $url = add_query_arg('paged', (int) $paged + 1, get_pagenum_link(1));
                printf(
                    '<div class="blogkit-load-more-wrap" data-type="%1$s"><a class="blogkit-load-more" href="%2$s" rel="next">%3$s</a></div>',
                    esc_attr($type),
                    esc_url($url),
                    esc_html($text)
                );
            } else {
                // Last page: keep an empty marker so the script can find the items container.
                printf('<div class="blogkit-load-more-wrap" data-type="%s" data-done="1"></div>', esc_attr($type));
            }
            return;
        }

        if ($links) {
            echo '<div class="blogkit-pagination">' . wp_kses_post($links) . '</div>';
        }
    }

    /**
     * Register the reading time controls.
     *
     * @param array $condition Optional Elementor condition for the controls.
     */
    protected function blogkit_register_reading_time_controls(array $condition = [])
    {
        $this->add_control(
            'blogkit_show_reading_time',
            [
                'label' => esc_html__('Show Reading Time', 'blogkit'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Show', 'blogkit'),
                'label_off' => esc_html__('Hide', 'blogkit'),
                'return_value' => 'yes',
                'default' => '',
                'condition' => $condition,
            ]
        );

        $this->add_control(
            'blogkit_words_per_minute',
            [
                'label' => esc_html__('Words Per Minute', 'blogkit'),
                'type' => Controls_Manager::NUMBER,
                'default' => 200,
                'min' => 100,
                'max' => 400,
                'condition' => $condition + ['blogkit_show_reading_time' => 'yes'],
            ]
        );
    }

    /**
     * Estimated reading time in minutes (at least 1).
     *
     * @param int $post_id Post ID.
     * @param int $wpm     Words per minute.
     * @return int
     */
    protected function blogkit_get_reading_time($post_id, $wpm = 200)
    {
        $wpm = min(400, max(100, (int) $wpm));
        $text = wp_strip_all_tags(strip_shortcodes((string) get_post_field('post_content', $post_id)));
        $words = preg_split('/\s+/u', trim($text), -1, PREG_SPLIT_NO_EMPTY);
        $count = is_array($words) ? count($words) : 0;

        return max(1, (int) ceil($count / $wpm));
    }

    /**
     * Output the reading time for the current post (inside the loop).
     *
     * @param array $settings Widget settings.
     */
    protected function blogkit_render_reading_time(array $settings)
    {
        if (empty($settings['blogkit_show_reading_time']) || 'yes' !== $settings['blogkit_show_reading_time']) {
            return;
        }

        $minutes = $this->blogkit_get_reading_time(get_the_ID(), isset($settings['blogkit_words_per_minute']) ? $settings['blogkit_words_per_minute'] : 200);

        printf(
            '<span class="blogkit-reading-time">%s</span>',
            esc_html(
                sprintf(
                    /* translators: %d: number of minutes. */
                    _n('%d min read', '%d min read', $minutes, 'blogkit'),
                    $minutes
                )
            )
        );
    }

    /**
     * Register the category filter tabs and live search controls.
     *
     * @param array $condition Optional Elementor condition for the controls.
     */
    protected function blogkit_register_filter_controls(array $condition = [])
    {
        $this->add_control(
            'blogkit_show_filter',
            [
                'label' => esc_html__('Filter Tabs', 'blogkit'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Show', 'blogkit'),
                'label_off' => esc_html__('Hide', 'blogkit'),
                'return_value' => 'yes',
                'default' => '',
                'description' => esc_html__('Shows the selected categories or tags (or the most used ones) as tabs.', 'blogkit'),
                'condition' => $condition,
            ]
        );

        $this->add_control(
            'blogkit_filter_taxonomy',
            [
                'label' => esc_html__('Filter By', 'blogkit'),
                'type' => Controls_Manager::SELECT,
                'default' => 'category',
                'options' => [
                    'category' => esc_html__('Category', 'blogkit'),
                    'post_tag' => esc_html__('Tag', 'blogkit'),
                ],
                'condition' => $condition + ['blogkit_show_filter' => 'yes'],
            ]
        );

        $this->add_control(
            'blogkit_filter_all_text',
            [
                'label' => esc_html__('"All" Tab Text', 'blogkit'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('All', 'blogkit'),
                'condition' => $condition + ['blogkit_show_filter' => 'yes'],
            ]
        );

        $this->add_control(
            'blogkit_show_search',
            [
                'label' => esc_html__('Live Search', 'blogkit'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Show', 'blogkit'),
                'label_off' => esc_html__('Hide', 'blogkit'),
                'return_value' => 'yes',
                'default' => '',
                'condition' => $condition,
            ]
        );

        $this->add_control(
            'blogkit_search_placeholder',
            [
                'label' => esc_html__('Search Placeholder', 'blogkit'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Search posts...', 'blogkit'),
                'condition' => $condition + ['blogkit_show_search' => 'yes'],
            ]
        );
    }

    /**
     * Query-string parameter names for this widget instance.
     *
     * @return array{cat:string,search:string}
     */
    private function blogkit_filter_params()
    {
        $id = sanitize_key($this->get_id());
        return ['cat' => 'blogkit_f_' . $id, 'search' => 'blogkit_s_' . $id];
    }

    /**
     * Category slugs offered as filter tabs.
     *
     * @param array $settings Widget settings.
     * @return array<string,string> slug => name
     */
    private $blogkit_filter_terms_cache = [];

    private function blogkit_get_filter_terms(array $settings)
    {
        $taxonomy = $this->blogkit_filter_taxonomy($settings);
        $cache_key = $taxonomy . '|' . wp_json_encode('post_tag' === $taxonomy ? ($settings['blogkit_tags'] ?? []) : ($settings['category'] ?? []));
        if (isset($this->blogkit_filter_terms_cache[$cache_key])) {
            return $this->blogkit_filter_terms_cache[$cache_key];
        }
        $args = ['taxonomy' => $taxonomy, 'hide_empty' => true, 'number' => 12, 'orderby' => 'count', 'order' => 'DESC'];
        $chosen = 'post_tag' === $taxonomy ? ($settings['blogkit_tags'] ?? []) : ($settings['category'] ?? []);
        if (!empty($chosen) && is_array($chosen)) {
            $args['slug'] = array_map('sanitize_title', $chosen);
        }
        $terms = get_terms($args);
        $out = [];
        if (is_array($terms)) {
            foreach ($terms as $term) {
                $out[$term->slug] = $term->name;
            }
        }
        $this->blogkit_filter_terms_cache[$cache_key] = $out;
        return $out;
    }

    /**
     * Taxonomy used by the filter tabs.
     *
     * @param array $settings Widget settings.
     * @return string category|post_tag
     */
    private function blogkit_filter_taxonomy(array $settings)
    {
        return 'post_tag' === ($settings['blogkit_filter_taxonomy'] ?? 'category') ? 'post_tag' : 'category';
    }

    /**
     * Current filter values from the query string (validated).
     *
     * @param array $settings Widget settings.
     * @return array{cat:string,search:string}
     */
    private function blogkit_get_filter_state(array $settings)
    {
        $params = $this->blogkit_filter_params();
        $state = ['cat' => '', 'search' => ''];

        // Read-only display filters: no state is changed, so no nonce is needed.
        if ('yes' === ($settings['blogkit_show_filter'] ?? '') && isset($_GET[$params['cat']])) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            $slug = sanitize_title(wp_unslash($_GET[$params['cat']])); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            if ('' !== $slug && isset($this->blogkit_get_filter_terms($settings)[$slug])) {
                $state['cat'] = $slug;
            }
        }
        if ('yes' === ($settings['blogkit_show_search'] ?? '') && isset($_GET[$params['search']])) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            $term = sanitize_text_field(wp_unslash($_GET[$params['search']])); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            $state['search'] = function_exists('mb_substr') ? mb_substr($term, 0, 100) : substr($term, 0, 100);
        }

        return $state;
    }

    /**
     * Apply the active filter tab and search term to WP_Query arguments.
     *
     * @param array $args     Query arguments.
     * @param array $settings Widget settings.
     * @return array
     */
    protected function blogkit_apply_filter_args(array $args, array $settings)
    {
        $state = $this->blogkit_get_filter_state($settings);
        if ('' !== $state['cat']) {
            if ('post_tag' === $this->blogkit_filter_taxonomy($settings)) {
                $args['tag_slug__in'] = [$state['cat']];
            } else {
                $args['category_name'] = $state['cat'];
            }
        }
        if ('' !== $state['search']) {
            $args['s'] = $state['search'];
        }
        return $args;
    }

    /**
     * Print the filter tabs and search box. Call before the query output.
     *
     * @param array $settings Widget settings.
     */
    protected function blogkit_render_filter_bar(array $settings)
    {
        $show_filter = 'yes' === ($settings['blogkit_show_filter'] ?? '');
        $show_search = 'yes' === ($settings['blogkit_show_search'] ?? '');
        if (!$show_filter && !$show_search) {
            return;
        }

        $params = $this->blogkit_filter_params();
        $state = $this->blogkit_get_filter_state($settings);

        printf(
            '<div class="blogkit-filter-bar" data-cat-param="%1$s" data-search-param="%2$s" data-cat="%3$s">',
            esc_attr($params['cat']),
            esc_attr($params['search']),
            esc_attr($state['cat'])
        );

        if ($show_filter) {
            $base = remove_query_arg([$params['cat'], 'paged']);
            echo '<ul class="blogkit-filter-tabs">';
            $all = !empty($settings['blogkit_filter_all_text']) ? $settings['blogkit_filter_all_text'] : __('All', 'blogkit');
            printf(
                '<li><a href="%1$s" class="blogkit-filter-tab%2$s" data-slug="">%3$s</a></li>',
                esc_url($base),
                '' === $state['cat'] ? ' is-active' : '',
                esc_html($all)
            );
            foreach ($this->blogkit_get_filter_terms($settings) as $slug => $name) {
                printf(
                    '<li><a href="%1$s" class="blogkit-filter-tab%2$s" data-slug="%3$s">%4$s</a></li>',
                    esc_url(add_query_arg($params['cat'], $slug, $base)),
                    $slug === $state['cat'] ? ' is-active' : '',
                    esc_attr($slug),
                    esc_html($name)
                );
            }
            echo '</ul>';
        }

        if ($show_search) {
            $placeholder = !empty($settings['blogkit_search_placeholder']) ? $settings['blogkit_search_placeholder'] : __('Search posts...', 'blogkit');
            printf(
                '<input type="search" class="blogkit-filter-search" value="%1$s" placeholder="%2$s" aria-label="%2$s" autocomplete="off">',
                esc_attr($state['search']),
                esc_attr($placeholder)
            );
        }

        echo '</div>';
    }

    /**
     * Register the post format badge control.
     *
     * @param array $condition Optional Elementor condition for the control.
     */
    protected function blogkit_register_format_badge_control(array $condition = [])
    {
        $this->add_control(
            'blogkit_show_format_badge',
            [
                'label' => esc_html__('Post Format Badge', 'blogkit'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Show', 'blogkit'),
                'label_off' => esc_html__('Hide', 'blogkit'),
                'return_value' => 'yes',
                'default' => '',
                'description' => esc_html__('Shows Video, Audio, Gallery, etc. on posts that use a non-standard post format.', 'blogkit'),
                'condition' => $condition,
            ]
        );
    }

    /**
     * Output the post format badge for the current post (inside the loop).
     *
     * @param array $settings Widget settings.
     */
    protected function blogkit_render_format_badge(array $settings)
    {
        if ('yes' !== ($settings['blogkit_show_format_badge'] ?? '')) {
            return;
        }
        $format = get_post_format();
        if (!$format || 'standard' === $format) {
            return;
        }
        printf(
            '<span class="blogkit-format-badge blogkit-format-%1$s">%2$s</span>',
            esc_attr(sanitize_html_class($format)),
            esc_html(get_post_format_string($format))
        );
    }

    /**
     * Public post types (attachments excluded).
     */
    private function blogkit_get_post_type_options()
    {
        $options = [];
        foreach (get_post_types(['public' => true], 'objects') as $slug => $type) {
            if ('attachment' === $slug) {
                continue;
            }
            $options[$slug] = $type->labels->singular_name;
        }
        return $options;
    }

    /**
     * Tag options (capped for large sites).
     */
    private function blogkit_get_tag_options()
    {
        // Editor panel only; avoids a query on every frontend render.
        if (!is_admin()) {
            return [];
        }
        $options = [];
        $tags = get_tags(['hide_empty' => false, 'number' => 200, 'orderby' => 'count', 'order' => 'DESC']);
        if (is_array($tags)) {
            foreach ($tags as $tag) {
                $options[$tag->slug] = $tag->name;
            }
        }
        return $options;
    }

    /**
     * Author options (users who can edit posts, capped).
     */
    private function blogkit_get_author_options()
    {
        // Editor panel only; avoids a query on every frontend render.
        if (!is_admin()) {
            return [];
        }
        $options = [];
        $users = get_users([
            'capability' => ['edit_posts'],
            'fields' => ['ID', 'display_name'],
            'number' => 200,
            'orderby' => 'display_name',
        ]);
        foreach ($users as $user) {
            $options[$user->ID] = $user->display_name;
        }
        return $options;
    }
}
