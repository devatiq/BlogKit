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
