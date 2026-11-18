<?php
namespace BlogKit\Frontend\Elementor\Widgets\PostCarousel;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

/**
 * Post Carousel widget: posts in a Swiper slider.
 */
class Main extends Widget_Base
{
    use \BlogKit\Frontend\Elementor\Traits\QueryControls;

    public function get_name()
    {
        return 'blogkit-post-carousel';
    }

    public function get_title()
    {
        return esc_html__('Post Carousel', 'blogkit');
    }

    public function get_icon()
    {
        return 'eicon-post-slider blogkit-icon';
    }

    public function get_categories()
    {
        return ['general'];
    }

    public function get_keywords()
    {
        return ['blogkit', 'post', 'carousel', 'slider', 'blog'];
    }

    public function get_script_depends()
    {
        return ['swiper-bundle-js', 'blogkit-post-carousel'];
    }

    public function get_style_depends()
    {
        return ['swiper-bundle-css', 'blogkit-elementor-style'];
    }

    private function get_blogkit_categories()
    {
        $cats = [];
        $categories = get_categories(['hide_empty' => false, 'number' => 200]);
        if ($categories) {
            foreach ($categories as $category) {
                $cats[$category->slug] = $category->name;
            }
        }
        return $cats;
    }

    protected function register_controls()
    {
        /* Query */
        $this->start_controls_section('blogkit_pc_query', [
            'label' => esc_html__('Query', 'blogkit'),
            'tab' => Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control('posts_per_page', [
            'label' => esc_html__('Number of Posts', 'blogkit'),
            'type' => Controls_Manager::NUMBER,
            'default' => 8,
            'min' => 1,
            'max' => 30,
        ]);

        $this->add_control('orderby', [
            'label' => esc_html__('Order By', 'blogkit'),
            'type' => Controls_Manager::SELECT,
            'default' => 'date',
            'options' => [
                'date' => esc_html__('Date', 'blogkit'),
                'title' => esc_html__('Title', 'blogkit'),
                'comment_count' => esc_html__('Comments', 'blogkit'),
                'rand' => esc_html__('Random', 'blogkit'),
            ],
        ]);

        $this->add_control('order', [
            'label' => esc_html__('Order', 'blogkit'),
            'type' => Controls_Manager::SELECT,
            'default' => 'DESC',
            'options' => [
                'DESC' => esc_html__('Descending', 'blogkit'),
                'ASC' => esc_html__('Ascending', 'blogkit'),
            ],
        ]);

        $this->add_control('category', [
            'label' => esc_html__('Category', 'blogkit'),
            'type' => Controls_Manager::SELECT2,
            'options' => $this->get_blogkit_categories(),
            'multiple' => true,
            'label_block' => true,
        ]);

        $this->blogkit_register_query_controls();

        $this->end_controls_section();

        /* Content */
        $this->start_controls_section('blogkit_pc_content', [
            'label' => esc_html__('Content', 'blogkit'),
            'tab' => Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control('show_image', [
            'label' => esc_html__('Featured Image', 'blogkit'),
            'type' => Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default' => 'yes',
        ]);

        $this->add_control('show_category', [
            'label' => esc_html__('Category Badge', 'blogkit'),
            'type' => Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default' => 'yes',
        ]);

        $this->blogkit_register_format_badge_control();

        $this->add_control('show_meta', [
            'label' => esc_html__('Date and Author', 'blogkit'),
            'type' => Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default' => 'yes',
        ]);

        $this->add_control('show_excerpt', [
            'label' => esc_html__('Excerpt', 'blogkit'),
            'type' => Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default' => 'yes',
        ]);

        $this->add_control('excerpt_length', [
            'label' => esc_html__('Excerpt Words', 'blogkit'),
            'type' => Controls_Manager::NUMBER,
            'default' => 18,
            'min' => 5,
            'max' => 80,
            'condition' => ['show_excerpt' => 'yes'],
        ]);

        $this->add_control('read_more_text', [
            'label' => esc_html__('Read More Text', 'blogkit'),
            'type' => Controls_Manager::TEXT,
            'default' => '',
            'description' => esc_html__('Leave empty to hide the link.', 'blogkit'),
        ]);

        $this->add_control('title_tag', [
            'label' => esc_html__('Title HTML Tag', 'blogkit'),
            'type' => Controls_Manager::SELECT,
            'default' => 'h3',
            'options' => [
                'h2' => 'H2', 'h3' => 'H3', 'h4' => 'H4', 'h5' => 'H5', 'h6' => 'H6',
                'div' => 'div', 'span' => 'span', 'p' => 'p',
            ],
        ]);

        $this->end_controls_section();

        /* Slider */
        $this->start_controls_section('blogkit_pc_slider', [
            'label' => esc_html__('Slider', 'blogkit'),
            'tab' => Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_responsive_control('slides_per_view', [
            'label' => esc_html__('Slides Per View', 'blogkit'),
            'type' => Controls_Manager::NUMBER,
            'min' => 1,
            'max' => 6,
            'default' => 3,
            'tablet_default' => 2,
            'mobile_default' => 1,
        ]);

        $this->add_responsive_control('space_between', [
            'label' => esc_html__('Space Between (px)', 'blogkit'),
            'type' => Controls_Manager::NUMBER,
            'min' => 0,
            'max' => 100,
            'default' => 24,
            'tablet_default' => 20,
            'mobile_default' => 16,
        ]);

        $this->add_control('autoplay', [
            'label' => esc_html__('Autoplay', 'blogkit'),
            'type' => Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default' => '',
        ]);

        $this->add_control('autoplay_delay', [
            'label' => esc_html__('Autoplay Delay (ms)', 'blogkit'),
            'type' => Controls_Manager::NUMBER,
            'min' => 1000,
            'max' => 20000,
            'default' => 4000,
            'condition' => ['autoplay' => 'yes'],
        ]);

        $this->add_control('loop', [
            'label' => esc_html__('Loop', 'blogkit'),
            'type' => Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default' => '',
        ]);

        $this->add_control('arrows', [
            'label' => esc_html__('Arrows', 'blogkit'),
            'type' => Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default' => 'yes',
        ]);

        $this->add_control('dots', [
            'label' => esc_html__('Dots', 'blogkit'),
            'type' => Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default' => 'yes',
        ]);

        $this->end_controls_section();

        /* Style */
        $this->start_controls_section('blogkit_pc_style', [
            'label' => esc_html__('Card', 'blogkit'),
            'tab' => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('card_bg', [
            'label' => esc_html__('Background', 'blogkit'),
            'type' => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .blogkit-pc-card' => 'background-color: {{VALUE}};'],
        ]);

        $this->add_control('card_radius', [
            'label' => esc_html__('Border Radius (px)', 'blogkit'),
            'type' => Controls_Manager::NUMBER,
            'min' => 0,
            'max' => 40,
            'selectors' => ['{{WRAPPER}} .blogkit-pc-card' => 'border-radius: {{VALUE}}px;'],
        ]);

        $this->add_control('title_color', [
            'label' => esc_html__('Title Color', 'blogkit'),
            'type' => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .blogkit-pc-title a' => 'color: {{VALUE}};'],
        ]);

        $this->add_group_control(Group_Control_Typography::get_type(), [
            'name' => 'title_typography',
            'selector' => '{{WRAPPER}} .blogkit-pc-title',
        ]);

        $this->add_control('accent_color', [
            'label' => esc_html__('Accent (badge, arrows, dots)', 'blogkit'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .blogkit-pc-badge' => 'background-color: {{VALUE}};',
                '{{WRAPPER}} .blogkit-pc-nav' => 'background-color: {{VALUE}};',
                '{{WRAPPER}} .swiper-pagination-bullet-active' => 'background-color: {{VALUE}};',
            ],
        ]);

        $this->end_controls_section();
    }

    protected function render()
    {
        include 'RenderView.php';
    }
}
