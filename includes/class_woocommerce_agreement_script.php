<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Script and stylesheet class
 *
 *  Enqueue all scripts and stylesheets for our plugin
 *
 * @package woocommerce agreement
 * @since 1.0.0
 */

if (!class_exists('Woocommerce_Agreement_Scripts')) {

    class Woocommerce_Agreement_Scripts
    {
        public function w_agree_css_styles_admin()
        {
            // Register & Enqueue public style admin side
            wp_register_style('custom_css', W_AGREE_PLUGIN_URL . 'includes/assets/css/style.css', '', '', 'all');
            wp_register_style('bootstrap_min_css', W_AGREE_PLUGIN_URL . 'includes/assets/css/bootstrap.min.css', array());
            wp_enqueue_style('custom_css');
            wp_enqueue_style('bootstrap_min_css');
        }

        public function w_agree_css_styles_front()
        {
            // Register & Enqueue public style admin side
            wp_register_style('custom_css_public', W_AGREE_PLUGIN_URL . 'public/assets/css/style.css', '', '', 'all');
            wp_register_style('bootstrap_min_css_public', W_AGREE_PLUGIN_URL . 'public/assets/css/bootstrap.min.css', array());
            wp_enqueue_style('custom_css_public');
            wp_enqueue_style('bootstrap_min_css_public');
        }


        /**
         * Enqueuing Scripts
         *
         * @package woocommerce agreement
         * @since 1.0.0
         */

        public function w_agree_scripts_admin()
        {
            // Register & Enqueue public style front side
            wp_register_script('custom_script_js', W_AGREE_PLUGIN_URL . 'includes/assets/js/script.js', array('jquery'), '', true);
            wp_enqueue_script('custom_script_js');
            wp_localize_script('custom_script_js', 'regenerate_pdf', array('ajaxurl' => admin_url('admin-ajax.php')));
        }



        public function w_agree_scripts_front()
        {
            // Register & Enqueue public style front side
            wp_register_script('custom_script_js_public', W_AGREE_PLUGIN_URL . 'public/assets/js/script.js', array('jquery'), '', true);
            wp_enqueue_script('custom_script_js_public');
        }


        /**
         * Adding hooks
         *
         * @package woocommerce agreement
         * @since 1.0.0
         */

        public function add_hooks()
        {
            //add style and scripts for front side
            add_action('admin_enqueue_scripts', array($this, 'w_agree_css_styles_admin'));
            add_action('admin_enqueue_scripts', array($this, 'w_agree_scripts_admin'));
            add_action('wp_enqueue_scripts', array($this, 'w_agree_css_styles_front'));
            add_action('wp_enqueue_scripts', array($this, 'w_agree_scripts_front'));
        }
    }
}
