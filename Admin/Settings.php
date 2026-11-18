<?php
namespace BlogKit\Admin;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Settings → BlogKit: switch individual widgets on or off.
 */
class Settings
{
    const GROUP = 'blogkit_settings';
    const PAGE = 'blogkit';
    const FALLBACK_OPTION = 'blogkit_fallback_image';
    const ACCENT_OPTION = 'blogkit_accent_color';
    const DELETE_OPTION = 'blogkit_delete_data';

    public function __construct()
    {
        add_action('admin_menu', [$this, 'add_menu']);
        add_action('admin_init', [$this, 'register']);
        add_filter('plugin_action_links_' . BLOGKIT_BASENAME, [$this, 'action_links']);
    }

    /**
     * Option name => widget label.
     *
     * @return array<string,string>
     */
    public static function widgets()
    {
        return [
            'blogkit_blog_grid_widget' => __('Blog Grid', 'blogkit'),
            'blogkit_classic_blog_grid' => __('Blog Classic Grid', 'blogkit'),
            'blogkit_card_grid' => __('Card Grid', 'blogkit'),
            'blogkit_post_card' => __('Post Card', 'blogkit'),
            'blogkit_featured_grid' => __('Featured Grid', 'blogkit'),
            'blogkit_featured_sidebar' => __('Featured Sidebar', 'blogkit'),
            'smart_posts_list' => __('Smart Posts List', 'blogkit'),
            'blogkit_post_carousel' => __('Post Carousel', 'blogkit'),
            'blogkit_taxonomy_list' => __('Taxonomy List', 'blogkit'),
            'taxonomy_slider' => __('Taxonomy Slider', 'blogkit'),
        ];
    }

    /**
     * Fallback image URL for posts without a featured image (empty = built-in placeholder).
     *
     * @return string
     */
    public static function fallback_image()
    {
        $url = (string) get_option(self::FALLBACK_OPTION, '');
        return '' !== $url ? $url : BLOGKIT_ELEMENTOR_ASSETS . '/img/placeholder.png';
    }

    public function add_menu()
    {
        add_options_page(
            __('BlogKit', 'blogkit'),
            __('BlogKit', 'blogkit'),
            'manage_options',
            self::PAGE,
            [$this, 'render']
        );
    }

    public function register()
    {
        register_setting(self::GROUP, self::FALLBACK_OPTION, [
            'type' => 'string',
            'default' => '',
            'sanitize_callback' => 'esc_url_raw',
        ]);

        register_setting(self::GROUP, self::DELETE_OPTION, [
            'type' => 'integer',
            'default' => 0,
            'sanitize_callback' => [__CLASS__, 'sanitize_toggle'],
        ]);

        register_setting(self::GROUP, self::ACCENT_OPTION, [
            'type' => 'string',
            'default' => '',
            'sanitize_callback' => [__CLASS__, 'sanitize_color'],
        ]);

        foreach (array_keys(self::widgets()) as $option) {
            register_setting(self::GROUP, $option, [
                'type' => 'integer',
                'default' => 1,
                'sanitize_callback' => [__CLASS__, 'sanitize_toggle'],
            ]);
        }
    }

    /**
     * Checkbox value to 1 or 0 (an unchecked box is not submitted at all).
     *
     * @param mixed $value Submitted value.
     * @return int
     */
    public static function sanitize_toggle($value)
    {
        return empty($value) ? 0 : 1;
    }

    /**
     * Hex color or empty string.
     *
     * @param mixed $value Submitted value.
     * @return string
     */
    public static function sanitize_color($value)
    {
        $color = sanitize_hex_color(is_string($value) ? trim($value) : '');
        return $color ? $color : '';
    }

    public function action_links($links)
    {
        array_unshift($links, '<a href="' . esc_url(admin_url('options-general.php?page=' . self::PAGE)) . '">' . esc_html__('Settings', 'blogkit') . '</a>');
        return $links;
    }

    public function render()
    {
        if (!current_user_can('manage_options')) {
            return;
        }
        ?>
        <div class="wrap">
            <h1><?php echo esc_html__('BlogKit', 'blogkit'); ?></h1>
            <p><?php echo esc_html__('Choose which BlogKit widgets are available in the Elementor editor. A widget that is switched off is not loaded.', 'blogkit'); ?></p>
            <form method="post" action="options.php">
                <?php settings_fields(self::GROUP); ?>
                <table class="form-table" role="presentation">
                    <?php foreach (self::widgets() as $option => $label) : ?>
                        <tr>
                            <th scope="row"><?php echo esc_html($label); ?></th>
                            <td>
                                <label for="<?php echo esc_attr($option); ?>">
                                    <input type="checkbox" id="<?php echo esc_attr($option); ?>" name="<?php echo esc_attr($option); ?>" value="1" <?php checked(1, (int) get_option($option, 1)); ?>>
                                    <?php echo esc_html__('Enabled', 'blogkit'); ?>
                                </label>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <th scope="row"><label for="<?php echo esc_attr(self::FALLBACK_OPTION); ?>"><?php echo esc_html__('Fallback image', 'blogkit'); ?></label></th>
                        <td>
                            <input type="url" class="regular-text" id="<?php echo esc_attr(self::FALLBACK_OPTION); ?>" name="<?php echo esc_attr(self::FALLBACK_OPTION); ?>" value="<?php echo esc_attr((string) get_option(self::FALLBACK_OPTION, '')); ?>" placeholder="https://">
                            <p class="description"><?php echo esc_html__('Image URL used by the Post Carousel for posts without a featured image. Leave empty for the built-in placeholder.', 'blogkit'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="<?php echo esc_attr(self::ACCENT_OPTION); ?>"><?php echo esc_html__('Accent color', 'blogkit'); ?></label></th>
                        <td>
                            <input type="text" class="small-text" id="<?php echo esc_attr(self::ACCENT_OPTION); ?>" name="<?php echo esc_attr(self::ACCENT_OPTION); ?>" value="<?php echo esc_attr((string) get_option(self::ACCENT_OPTION, '')); ?>" placeholder="#1d9fb0" maxlength="7" pattern="#[0-9a-fA-F]{3}([0-9a-fA-F]{3})?">
                            <p class="description"><?php echo esc_html__('Hex color (for example #1d9fb0) for the load more button, filter tabs and the Post Carousel. Leave empty for the default.', 'blogkit'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php echo esc_html__('Uninstall', 'blogkit'); ?></th>
                        <td>
                            <label for="<?php echo esc_attr(self::DELETE_OPTION); ?>">
                                <input type="checkbox" id="<?php echo esc_attr(self::DELETE_OPTION); ?>" name="<?php echo esc_attr(self::DELETE_OPTION); ?>" value="1" <?php checked(1, (int) get_option(self::DELETE_OPTION, 0)); ?>>
                                <?php echo esc_html__('Delete all BlogKit settings and category images when the plugin is deleted', 'blogkit'); ?>
                            </label>
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
}
