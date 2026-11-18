<?php 
namespace BlogKit\Admin;

if (!defined('ABSPATH')) {
    exit;
}

class AdminManager
{
    /**
     * @var Settings
     */
    protected $settings;

    public function __construct()
    {
        $this->settings = new Settings();
    }
}
