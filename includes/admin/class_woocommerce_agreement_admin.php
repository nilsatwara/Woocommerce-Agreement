<?php

/**
 * Woocommerce Agreement admin functionality class
 *
 * @package Woocommerce Agreement
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly.
}

if (!class_exists('Woocommerce_Agreement_Admin')) {

  class Woocommerce_Agreement_Admin
  {
    /**
     * Woocommerce add settings tabs agreement
     *
     * @package Woocommerce Agreement
     * @since 1.0.0
     */

    public function woocommerce_add_setting_tabs_agreement($settings_tabs)
    {
      $settings_tabs['agreement'] = __('Agreement', 'w_agree');
      return $settings_tabs;
    }

    /**
     * Woocommerce Agreement display settings fields
     *
     * @package Woocommerce Agreement
     * @since 1.0.0
     */

    public function display_agreement_settings_fields()
    {
      $text_editor_content_settings = wpautop(get_option(W_AGREE_META_PREFIX . 'agreement_option'));
      $agreement_title_settings = get_option(W_AGREE_META_PREFIX . 'agreement_title');
      woocommerce_wp_text_input(
        array(
          'id'          => 'agreement_title',
          'name'        => 'agreement_title',
          'label'       => __('Agreement Title', 'w_agree'),
          'type'        => 'text',
          'value' => $agreement_title_settings

        )
      );
      wp_editor($text_editor_content_settings, 'text_editor_content_settings');
      ?>
      <h5 style="text-align: center;margin-top: 30px;margin-bottom: 20px;"><?php esc_html('Available placeholders:', 'w_agree'); ?></h5>
      <table style="margin:auto">
        <tr>
          <td><code>{product_price}</code></td>
          <td><code>{order_number}</code></td>
          <td><code>{first_name}</code></td>
        </tr>
        <tr>
          <td><code>{last_name}</code></td>
          <td><code>{product_name}</code></td>
        </tr>
        <tr>
          <td><code>{product_qty}</code></td>
          <td><code>{order_total}</code></td>
        </tr>
        <tr>
          <td><code>{payment_method}</code></td>
          <td><code>{shipping_method}</code></td>
        </tr>
        <tr>
          <td><code>{product_type}</code></td>
          <td><code>{order_date}</code></td>
        </tr>
        <tr>
          <td><code>{company_name}</code></td>
        </tr>
        <tr>
          <td><code>{state}</code></td>
          <td><code>{phone_no}</code></td>
        </tr>
        <tr>
          <td><code>{country}</code></td>
          <td><code>{street_address}</code></td>
        </tr>
        <tr>
          <td><code>{email_address}</code></td>
          <td><code>{pin_code}</code></td>
        </tr>
      </table>
    <?php }


    /**
     * Woocommerce Agreement wp_editor content save from woocommerce settings tabs
     *
     * @package Woocommerce Agreement
     * @since 1.0.0
     */

    public function agreement_update_settings()
    {
      if (isset($_POST['save'])) {
        // Retrieve the agreement_text_editor_content from $_POST
        $agreement_text_editor_content = stripslashes(isset($_POST['text_editor_content_settings']) ? $_POST['text_editor_content_settings'] : '');
        $agreement_title = sanitize_text_field(isset($_POST['agreement_title']) ? $_POST['agreement_title'] : 'Agreeement');
        // Update the agreement_text_editor_content option
        update_option(W_AGREE_META_PREFIX . 'agreement_option', wp_kses_post($agreement_text_editor_content));
        update_option(W_AGREE_META_PREFIX . 'agreement_title',  $agreement_title);
      }
    }

    /**
     * Woocommerce Agreement tab display from product level
     *
     * @package Woocommerce Agreement
     * @since 1.0.0
     */

    public function w_agreement_product_data_tab_display($tabs)
    {
      $tabs['product_agreement_setting'] = array(
        'label'    => __('Product Agreement', 'agreement'),
        'target'   => 'product_agreement',
        'priority' => '999'
      );
      return $tabs;
    }

    /**
     * Woocommerce Agreement tab content display from product level
     *
     * @package Woocommerce Agreement
     * @since 1.0.0
     */

    public function w_agreement_product_tab_content_display()
    {
      global $post;

      $post_id = $post->ID;
      $text_editor_content_product = wpautop(get_post_meta($post_id, W_AGREE_META_PREFIX . 'product_agreement_data', true));
      $enable_agreement = get_post_meta($post_id, W_AGREE_META_PREFIX . 'agreement_status', true);


      // Retrieve the current product meta value
      echo '<div id="product_agreement" class="panel woocommerce_options_panel">';
      echo '<div class="options_group">';

      woocommerce_wp_checkbox(
        array(
          'id'          => 'agreement_status',
          'name'        => 'agreement_status',
          'label'       => __('Enable Agreement', 'w_agree'),
          'type'        => 'checkbox',
          'value' => $enable_agreement
        )
      );
      wp_editor($text_editor_content_product, 'text_editor_content_product');
    ?>
      <h5 style="text-align: center;margin-top: 30px;margin-bottom: 20px;"><?php esc_html('Available placeholders:', 'w_agree'); ?></h5>
      <table style="margin:auto; margin-bottom: 20px;">
        <tr>
          <td><code>{product_price}</code></td>
          <td><code>{order_number}</code></td>
          <td><code>{first_name}</code></td>
        </tr>
        <tr>
          <td><code>{last_name}</code></td>
          <td><code>{product_name}</code></td>
        </tr>
        </tr>
        <tr>
          <td><code>{product_qty}</code></td>
          <td><code>{order_total}</code></td>
        </tr>
        <tr>
          <td><code>{payment_method}</code></td>
          <td><code>{shipping_method}</code></td>
        </tr>
        <tr>
          <td><code>{product_type}</code></td>
          <td><code>{order_date}</code></td>
        </tr>
        <tr>
          <td><code>{company_name}</code></td>
        </tr>
        <tr>
          <td><code>{state}</code></td>
          <td><code>{phone_no}</code></td>
        </tr>
        <tr>
          <td><code>{country}</code></td>
          <td><code>{street_address}</code></td>
        </tr>
        <tr>
          <td><code>{email_address}</code></td>
          <td><code>{pin_code}</code></td>
        </tr>
        <tr>
          <td><code>{signer_name}</code></td>
          <td><code>{digital_sign}</code></td>
        </tr>
      </table>

<?php }
    /**
     * woocommerce save agreement data when the user enables agreement from the product level
     *
     * @package Woocommerce Agreement
     * @since 1.0.0
     */

    public function save_agreement_product_data_tab($post_id)
    {
      $wp_editor_content_products = stripslashes(isset($_POST['text_editor_content_product']) ? $_POST['text_editor_content_product'] : '');
      $agreement_status = isset($_POST['agreement_status']) ? 'yes' : ''; // Properly handle checkbox value
      if (isset($_POST['save'])) {
        update_post_meta($post_id, W_AGREE_META_PREFIX . 'agreement_status', $agreement_status);
        update_post_meta($post_id, W_AGREE_META_PREFIX . 'product_agreement_data', wp_kses_post($wp_editor_content_products));
      }
    }

    /**
     *  Add custom meta box to order details page
     *
     * @package Woocommerce Agreement
     * @since 1.0.0
     */


    public function agreement_meta_box()
    {
      add_meta_box(
        'agreement_download_section',
        'Agreement Download Section',
        array($this, 'render_agreement_download_section'),
        'shop_order',
        'normal',
        'default'
      );
    }


    /**
     *  Render agreement download section on order details page
     *
     * @package Woocommerce Agreement
     * @since 1.0.0
     */


    public function render_agreement_download_section($post)
    {
      // Add your HTML markup for the custom section here
      $order = wc_get_order($post->ID);
      $table = '<table style="width: 100%; border-collapse: collapse; border: 1px solid #000;">';
      $table .= '<tr>';
      $table .= '<th style="border: 1px solid #000; padding: 10px;">' . esc_html("Product Title", "w_agree") . '</th>';
      $table .= '<th style="border: 1px solid #000; padding: 10px;">' . esc_html("Agreement Links", "w_agree") . '</th>';
      $table .= '</tr>';

      foreach ($order->get_items() as $item_id => $line_item) {
        $data = $line_item->get_data();
        $product_id = $data['product_id'];

        $product_name = $line_item->get_name() . "<br>";
        $download_link = $line_item->get_meta('_pdf_download_link');

        if (!empty($download_link)) {
          // Add a row to the table with inline CSS
          $table .= '<tr>';
          $table .= '<td style="border: 1px solid #000; padding: 10px;">' . $product_name . '</td>';
          $table .= '<td style="border: 1px solid #000; padding: 10px;"><a style="text-decoration: none; background-color: #0073e6; color: #fff; padding: 5px 10px; border-radius: 5px;" href="' . esc_url($download_link) . '" download>Download</a>
            <a class="pdf_regenerate" data-product="' . $product_id . '" data-order="' . $post->ID . '"  style="text-decoration: none; background-color: green; color: #fff; padding: 5px 10px; border-radius: 5px;" href="javascript:void(0);">Regenerate</a>
            </td>';
          $table .= '</tr>';
        }
      }
      // Close the table
      $table .= '</table>';

      // Display the table
      echo $table;
    }


    /**
     *  Regenerate pdf using wp ajax on order details page
     *
     * @package Woocommerce Agreement
     * @since 1.0.0
     */

    public function regenerate_pdf_admin()
    {
      $order_id = isset($_POST['order_id']) ? $_POST['order_id'] : '';
      $get_product_id = isset($_POST['product_id']) ? $_POST['product_id'] : '';

      if ($order_id && $get_product_id) {

        $order = wc_get_order($order_id);
        $order_date = $order->get_date_created()->format('d-m-Y');
        $billing_first_name = $order->get_billing_first_name();
        $billing_last_name = $order->get_billing_last_name();
        $billing_company_name = $order->get_billing_company();
        $billing_country_name = $order->get_billing_country();
        $billing_phone = $order->get_billing_phone();
        $billing_email = $order->get_billing_email();
        $billing_address = $order->get_formatted_billing_address();
        $billing_postcode = $order->get_billing_postcode();
        $billing_state = $order->get_billing_state();
        $currency_symbol = get_woocommerce_currency_symbol();
        $get_final_total = '<span>' . $currency_symbol . $order->get_total() . '</span> ';
        $get_payment_method = $order->get_payment_method();
        $get_shipping_method = $order->get_shipping_method();

        foreach ($order->get_items() as $item_id => $line_item) {
          $data = $line_item->get_data();
          $product_id =  $data['product_id'];

          if ($get_product_id != $product_id) {
            continue;
          }
          // Check the agreement status for the product

          $agreement_status = get_post_meta($product_id,  W_AGREE_META_PREFIX . 'agreement_status', true);

          $product = $line_item->get_product();
          $product_price_org = $product->get_price();
          $product_order_total = $line_item->get_total();
          $product_name = $line_item->get_name();
          $product_qty = $line_item->get_quantity();
          $product_type = $product->get_type();
          $product_price = '<span>' . $currency_symbol . '</span>' . $product_price_org;
          $order_total = '<span>' . $currency_symbol . '</span>' . $product_order_total;
          $product_agreement_data = wpautop(get_post_meta($product_id, W_AGREE_META_PREFIX . 'product_agreement_data', true));
          if ($agreement_status && $product_agreement_data) {

            $signer_name = get_post_meta($order_id, 'signer_name', true);
            $cutomer_sign = get_post_meta($order_id, 'signer_canvas', true);
            $digital_sign =  '<img src="' . $cutomer_sign . '" alt="">';

            $search_value = array(
              '{order_number}',
              '{first_name}',
              '{last_name}',
              '{product_name}',
              '{product_price}',
              '{product_qty}',
              '{order_total}',
              '{final_price}',
              '{payment_method}',
              '{shipping_method}',
              '{product_type}',
              '{order_date}',
              '{company_name}',
              '{state}',
              '{phone_no}',
              '{country}',
              '{email_address}',
              '{street_address}',
              '{pin_code}',
              '{signer_name}',
              '{digital_sign}'
            );

            $replace_value = array(
              $order_id,
              $billing_first_name,
              $billing_last_name,
              $product_name,
              $product_price,
              $product_qty,
              $order_total,
              $get_final_total,
              $get_payment_method,
              $get_shipping_method,
              $product_type,
              $order_date,
              $billing_company_name,
              $billing_state,
              $billing_phone,
              $billing_country_name,
              $billing_email,
              $billing_address,
              $billing_postcode,
              $signer_name,
              $digital_sign
            );

            $updated_pdf_data = str_replace($search_value, $replace_value, $product_agreement_data);

            require_once W_AGREE_DIR . '/public/dompdf/autoload.inc.php';

            $options = new Dompdf\Options();
            $options->set('isRemoteEnabled', true);

            $dompdf = new Dompdf\Dompdf($options);

            $dompdf->loadHtml($updated_pdf_data);

            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            $output = $dompdf->output();
            $pdf_file_name = $product->get_slug() . '.pdf';
            $uploads_dir = wp_upload_dir();
            $pdf_dir = $uploads_dir['basedir'] . '/wc_agreement/' . $order_id . '/';

            if (!file_exists($pdf_dir)) {
              wp_mkdir_p($pdf_dir);
            }
            $pdf_path = $pdf_dir . $pdf_file_name;
            file_put_contents($pdf_path, $output);
          } else {

            $get_option_agreement = wpautop(get_option(W_AGREE_META_PREFIX . 'agreement_option'));

            $search_value = array(
              '{order_number}',
              '{first_name}',
              '{last_name}',
              '{product_name}',
              '{product_price}',
              '{product_qty}',
              '{order_total}',
              '{final_price}',
              '{payment_method}',
              '{shipping_method}',
              '{product_type}',
              '{order_date}',
              '{company_name}',
              '{state}',
              '{phone_no}',
              '{country}',
              '{email_address}',
              '{street_address}',
              '{pin_code}'
            );

            $replace_value = array(
              $order_id,
              $billing_first_name,
              $billing_last_name,
              $product_name,
              $product_price,
              $product_qty,
              $order_total,
              $get_final_total,
              $get_payment_method,
              $get_shipping_method,
              $product_type,
              $order_date,
              $billing_company_name,
              $billing_state,
              $billing_phone,
              $billing_country_name,
              $billing_email,
              $billing_address,
              $billing_postcode
            );

            $updated_pdf_data_option = str_replace($search_value, $replace_value, $get_option_agreement);

            require_once W_AGREE_DIR . '/public/dompdf/autoload.inc.php';

            $options = new Dompdf\Options();
            $options->set('isRemoteEnabled', true);

            $dompdf = new Dompdf\Dompdf($options);

            $dompdf->loadHtml($updated_pdf_data_option);

            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            $output = $dompdf->output();
            $pdf_file_name = $product->get_slug() . '.pdf';
            $uploads_dir = wp_upload_dir();
            $pdf_dir = $uploads_dir['basedir'] . '/wc_agreement/' . $order_id . '/';

            if (!file_exists($pdf_dir)) {
              wp_mkdir_p($pdf_dir);
            }

            $pdf_path = $pdf_dir . $pdf_file_name;
            file_put_contents($pdf_path, $output);
          }

          $data = array(
            "success" => true,
            "message" => $product_name . " " . "For PDF Generated Successfully",
            "order_id" => $order_id,
            "product_id" => $product_id
          );
          $error = array(
            'success' => false,
            "message" => "Something went wrong please try again",
          );
          if ($data) {
            wp_send_json_success($data);
          } else {
            wp_send_json_error($error);
          }
        }
      } else {
        wp_send_json_error('OrderID or ProductID not exist.');
      }
      wp_die();
    }

    function display_product_variations( $loop, $variation_data, $variation ) {
      global $post;
      $post_id = $post->ID;

      $product_variations_content = wpautop(get_post_meta($post_id,W_AGREE_META_PREFIX .'product_variations_content',true));
      wp_editor($product_variations_content, 'product_variations_content');
    }

    function product_variations_save($variation_id, $loop)
    { print_r($_POST);
      die;
      $product_variations_save = stripslashes(isset($_POST['product_variations_content'][$loop]) ? $_POST['product_variations_content'][$loop] : '');
      update_post_meta($variation_id, 'rudr_text', wp_kses_post($product_variations_save));
    }



    /**
     * Adding hooks
     *
     * @package Woocommerce Agreement
     * @since 1.0.0
     */

    public function add_hooks()
    {
      add_filter('woocommerce_settings_tabs_array', array($this, 'woocommerce_add_setting_tabs_agreement'), 50);
      add_action('woocommerce_settings_tabs_agreement', array($this, 'display_agreement_settings_fields'));
      add_action('woocommerce_update_options_agreement', array($this, 'agreement_update_settings'));
      add_filter('woocommerce_product_data_tabs', array($this, 'w_agreement_product_data_tab_display'));
      add_action('woocommerce_product_data_panels', array($this, 'w_agreement_product_tab_content_display'));
      add_action('woocommerce_process_product_meta', array($this, 'save_agreement_product_data_tab'), 10);
      add_action('add_meta_boxes', array($this, 'agreement_meta_box'));
      add_action('wp_ajax_regenerate_pdf_admin', array($this, 'regenerate_pdf_admin'));
      add_action('wp_ajax_nopriv_regenerate_pdf_admin', array($this, 'regenerate_pdf_admin'));
      add_action( 'woocommerce_product_after_variable_attributes', array($this,'display_product_variations'),10,3);
      add_action( 'woocommerce_save_product_variation', array($this,'product_variations_save'),10, 2);
    }
  }
}
