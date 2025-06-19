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


        // === Featured Section Style ===
        $this->start_controls_section(
            'blogkit_fs_style_featured',
            [
                'label' => esc_html__('Featured Box', 'blogkit'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'blogkit_fs_featured_bg',
            [
                'label' => esc_html__('Background Color', 'blogkit'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .blogkit-fs-featured' => 'background-color: {{VALUE}};',
                ],
            ]
        );

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

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'blogkit_fs_featured_title_typo',
                'selector' => '{{WRAPPER}} .blogkit-fs-title a',
            ]
        );

        $this->end_controls_section();

        // === Sidebar Section Style ===
        $this->start_controls_section(
            'blogkit_fs_style_sidebar',
            [
                'label' => esc_html__('Sidebar Posts', 'blogkit'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'blogkit_fs_sidebar_bg',
            [
                'label' => esc_html__('Item Background', 'blogkit'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .blogkit-fs-sidebar-item' => 'background-color: {{VALUE}};',
                ],
            ]
        );

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

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'blogkit_fs_sidebar_title_typo',
                'selector' => '{{WRAPPER}} .blogkit-fs-sidebar-title a',
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