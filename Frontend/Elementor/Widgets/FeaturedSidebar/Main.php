<?php
namespace BlogKit\Frontend\Elementor\Widgets\FeaturedSidebar;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

class Main extends Widget_Base
{
    public function get_name()
    {
        return 'blogkit-FeaturedSidebar';
    }

    public function get_title()
    {
        return esc_html__('Featured & Sidebar', 'blogkit');
    }

    public function get_icon()
    {
        return 'blogkit-taxonomy blogkit-icon';
    }

    public function get_categories()
    {
        return ['blogkit'];
    }

    public function get_keywords()
    {
        return ['post', 'featured', 'sidebar post', 'blogkit'];
    }

    public function get_style_depends()
    {
        return ['blogkit-style-2', 'blogkit-responsive'];
    }

    /**
     * Register controls.
     */
    protected function register_controls()
    {

        // General Settings
        $this->start_controls_section(
            'blogkit_featured_settings',
            [
                'label' => esc_html__('Settings', 'blogkit'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->end_controls_section();


        // Featured Sidebar Box
        $this->start_controls_section(
            'blogkit_fs_featured_box',
            [
                'label' => esc_html__('Featured & Sidebar Box', 'blogkit'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'blogkit_fs_gap',
            [
                'label' => esc_html__('Gap', 'blogkit'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em', '%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                    'em' => [
                        'min' => 0,
                        'max' => 10,
                        'step' => 0.1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 30,
                ],
                'selectors' => [
                    '{{WRAPPER}} .blogkit-fs-grid' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section(); // end Featured sidebar box

        // === Featured Section Style ===
        $this->start_controls_section(
            'blogkit_fs_style_featured',
            [
                'label' => esc_html__('Featured Posts', 'blogkit'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        // Featured Background
        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name' => 'blogkit_fs_featured_background',
                'label' => esc_html__('Background', 'blogkit'),
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .blogkit-fs-featured',
            ]
        );

        // Featured Padding
        $this->add_responsive_control(
            'blogkit_fs_featured_padding',
            [
                'label' => esc_html__('Padding', 'blogkit'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .blogkit-fs-featured' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        // Featured Title Typography
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'blogkit_fs_featured_title_typo',
                'label' => esc_html__('Title Typography', 'blogkit'),
                'selector' => '{{WRAPPER}} .blogkit-fs-title a',
            ]
        );

        // Featured Title Color
        $this->add_control(
            'blogkit_fs_featured_title_color',
            [
                'label' => esc_html__('Title Color', 'blogkit'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .blogkit-fs-title a' => 'color: {{VALUE}};',
                ],
            ]
        );

        // Featured Meta Typography
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'blogkit_fs_featured_meta_typo',
                'label' => esc_html__('Meta Typography', 'blogkit'),
                'selector' => '{{WRAPPER}} .blogkit-fs-meta',
            ]
        );
        // Featured Meta Color
        $this->add_control(
            'blogkit_fs_featured_meta_color',
            [
                'label' => esc_html__('Meta Color', 'blogkit'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .blogkit-fs-meta' => 'color: {{VALUE}};',
                ],
            ]
        );

        //Category Background
        $this->add_control(
            'blogkit_fs_featured_category_bg',
            [
                'label' => esc_html__('Category Background', 'blogkit'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .blogkit-fs-category' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        // Category Color
        $this->add_control(
            'blogkit_fs_featured_category_color',
            [
                'label' => esc_html__('Category Color', 'blogkit'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .blogkit-fs-category' => 'color: {{VALUE}};',
                ],
            ]
        );
        //category padding
        $this->add_responsive_control(
            'blogkit_fs_featured_category_padding',
            [
                'label' => esc_html__('Category Padding', 'blogkit'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .blogkit-fs-category' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        // Featured Border
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'blogkit_fs_featured_border',
                'label' => esc_html__('Border', 'blogkit'),
                'selector' => '{{WRAPPER}} .blogkit-fs-featured',
            ]
        );

        // Featured Border Radius
        $this->add_responsive_control(
            'blogkit_fs_featured_border_radius',
            [
                'label' => esc_html__('Border Radius', 'blogkit'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .blogkit-fs-featured' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section(); // Featured Section Style

        // === Sidebar Section Style ===
        $this->start_controls_section(
            'blogkit_fs_style_sidebar',
            [
                'label' => esc_html__('Sidebar Posts', 'blogkit'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        // Sidebar Background
        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name' => 'blogkit_fs_sidebar_background',
                'label' => esc_html__('Background', 'blogkit'),
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .blogkit-fs-sidebar-item',
            ]
        );

        // Sidebar Padding
        $this->add_responsive_control(
            'blogkit_fs_sidebar_padding',
            [
                'label' => esc_html__('Item Padding', 'blogkit'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .blogkit-fs-sidebar-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        // Sidebar Border
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'blogkit_fs_sidebar_border',
                'label' => esc_html__('Border', 'blogkit'),
                'selector' => '{{WRAPPER}} .blogkit-fs-sidebar-item',
            ]
        );

        // Sidebar Border Radius
        $this->add_responsive_control(
            'blogkit_fs_sidebar_border_radius',
            [
                'label' => esc_html__('Border Radius', 'blogkit'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .blogkit-fs-sidebar-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        // Sidebar Title Typography
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'blogkit_fs_sidebar_title_typo',
                'label' => esc_html__('Title Typography', 'blogkit'),
                'selector' => '{{WRAPPER}} .blogkit-fs-sidebar-title a',
            ]
        );

        // Sidebar Title Color
        $this->add_control(
            'blogkit_fs_sidebar_title_color',
            [
                'label' => esc_html__('Title Color', 'blogkit'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .blogkit-fs-sidebar-title a' => 'color: {{VALUE}};',
                ],
            ]
        );
        // Sidebar Title Color Hover
        $this->add_control(
            'blogkit_fs_sidebar_title_color_hover',
            [
                'label' => esc_html__('Title Color Hover', 'blogkit'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .blogkit-fs-sidebar-title a:hover' => 'color: {{VALUE}};',
                ],
            ]
        );
        // Sidebar Category Typography
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'blogkit_fs_sidebar_category_typo',
                'label' => esc_html__('Category Typography', 'blogkit'),
                'selector' => '{{WRAPPER}} .blogkit-fs-badge',
            ]
        );

        // Sidebar Category Color
        $this->add_control(
            'blogkit_fs_sidebar_category_color',
            [
                'label' => esc_html__('Category Color', 'blogkit'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .blogkit-fs-badge' => 'color: {{VALUE}};',
                ],
            ]
        );

        // Sidebar Category Background
        $this->add_control(
            'blogkit_fs_sidebar_category_bg',
            [
                'label' => esc_html__('Category Background', 'blogkit'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .blogkit-fs-badge' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        // Sidebar Category Padding
        $this->add_responsive_control(
            'blogkit_fs_sidebar_category_padding',
            [
                'label' => esc_html__('Category Padding', 'blogkit'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .blogkit-fs-badge' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        // Sidebar Category Border
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'blogkit_fs_sidebar_category_border',
                'label' => esc_html__('Category Border', 'blogkit'),
                'selector' => '{{WRAPPER}} .blogkit-fs-badge',
            ]
        );
        // Sidebar Category Border Radius
        $this->add_responsive_control(
            'blogkit_fs_sidebar_category_border_radius',
            [
                'label' => esc_html__('Category Border Radius', 'blogkit'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .blogkit-fs-badge' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        //thumbnail size
        $this->add_responsive_control(
            'blogkit_fs_sidebar_thumbnail_size',
            [
                'label' => esc_html__('Thumbnail Size', 'blogkit'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 60,
                        'max' => 300,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .blogkit-fs-sidebar-thumb img' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        //Sidebar Meta Typography
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'blogkit_fs_sidebar_meta_typo',
                'label' => esc_html__('Meta Typography', 'blogkit'),
                'selector' => '{{WRAPPER}} .blogkit-fs-sidebar-date',
            ]
        );

        //Item gap
        $this->add_responsive_control(
            'blogkit_fs_sidebar_item_gap',
            [
                'label' => esc_html__('Item Gap', 'blogkit'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .blogkit-fs-sidebar' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->end_controls_section();



    }

    /**
     * Render frontend output.
     */
    protected function render()
    {
        include 'RenderView.php';
    }
}