<?php

/**
 * Woocommerce agreement front side functionality class
 *
 * @package Woocommerce Agreement
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (!class_exists('Woocommerce_Agreement_Front')) {

    class Woocommerce_Agreement_Front
    {
        /**
         * Display signature drawer ,If only one product in cart, and signature feature enable. Then it will show signature drawer on checkout page. .
         *  
         * @package Woocommerce Agreement
         * @since 1.0.0
         */

        public function digital_signature_drawer()
        {
            $cart = WC()->cart;

            $show_modal = false;

            foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
                // Get the product ID for each cart item
                $product_id = $cart_item['product_id'];
                $agreement_status = get_post_meta($product_id, W_AGREE_META_PREFIX . 'agreement_status', true);
                $product_agreement_data = wpautop(get_post_meta($product_id, W_AGREE_META_PREFIX . 'product_agreement_data', true));

                if ($agreement_status && $product_agreement_data) {
                    $show_modal = true; // Set the flag to true if any product has agreement status enabled
                    break; // Exit the loop as soon as we find a product with enabled agreement status
                }
            }
            if ($show_modal) {
            ?>
                <button class="btn btn-danger" type="button" id="w_agree_openModal_Btn"><?php esc_attr_e("Open Signature Pad", "w_agree"); ?>
                </button> <br><br>
                <div id="signatureModal" class="modal">
                    <div class="modal-content">
                        <form method="post" action="">
                            <span class="close">&times;</span>
                            <label for="signerName"><?php esc_attr_e('Signer Name', 'w_agree'); ?></label>
                            <input type="text" name="signer_name" value="<?php esc_attr_e(get_post_meta($product_id, 'signer_name', true), 'w_agree'); ?>" id="signerName" required>
                            <br>
                            <label for="canvas"><?php esc_attr_e('Draw Signature', 'w_agree'); ?></label>
                            <canvas id="canvas" name="signatureCanvas" class="w-75 h-75"></canvas>
                            <input type="hidden" name="signer_canvas" value="<?php esc_attr_e(get_post_meta($product_id, 'signer_canvas', true), 'w_agree'); ?>" class="signer_canvas">
                            <div class="text-danger" id="signatureError" class="error"></div>
                            <input type="hidden" data-product="<?php esc_attr_e($product_id, 'w_agree');?>" name="product_id" id="wp_agree_product_id">
                            <br>
                            <button type="button" id="clearBtn"><?php esc_attr_e('Clear Signature', 'w_agree'); ?></button>
                            <br>
                            <button type="button" id="acceptBtn"><?php esc_attr_e('Accept Agreement', 'w_agree'); ?></button>
                        </form>
                    </div>
                </div>
                <div class="signatureDiv">
                    <span class="fs-5">
                        <?php esc_attr_e('Signer Name:', 'w_agree'); ?>
                    </span>
                    <span class="signer_value">
                        <?php esc_attr_e(get_post_meta($product_id, 'signer_name', true), 'w_agree'); ?>
                    </span>
                    <br><br>
                    <span class="fs-5"><?php esc_attr_e('Your Signature :', 'w_agree'); ?></span>
                    <img class="signatureImage"> <br><br>
                    <a class="btn btn-success" id="download_sign"></a>
                    <br>
                    <br>
                </div>
        <?php }
        }

        /**
         * Update signer name and and signature and display on checkout page when customer accept agreement. .
         *  
         * @package Woocommerce Agreement
         * @since 1.0.0
         */

        function update_canvas_value()
        {
            // Get the product ID for each cart item
            $product_id = $_POST['productID'];
            $agreement_status = get_post_meta($product_id,  W_AGREE_META_PREFIX.'agreement_status', true);
            $product_agreement_data = wpautop(get_post_meta($product_id, W_AGREE_META_PREFIX.'product_agreement_data', true));
            if ($agreement_status && $product_agreement_data) {
                if (isset($_POST['signer_name']) && isset($_POST['signer_canvas'])) {
                    $signer_name = sanitize_text_field($_POST['signer_name']);
                    $image_data = wp_unslash($_POST['signer_canvas']);

                    $data = array(
                        "success" => true,
                        'signer_name' => $signer_name,
                        'signer_canvas' => $image_data
                    );

                    $error = array(
                        "success" => true,
                        "message" => "Something went wrong"
                    );

                    if ($data) {
                        wp_send_json_success($data);
                    } else {
                        wp_send_json_error($error);
                    }
                } else {
                    wp_send_json_error("Invalid request");
                }
            }
            wp_die();
        }

        /**
         * When agreement status enable from product tab then pdf links generate after checkout order process .
         * 
         * pdf links generate after checkout order process
         * 
         * @package Woocommerce Agreement
         * @since 1.0.0
         */

        public function generate_pdf_link_agreement($order_id)
        {
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

            // Loop through each line item
            foreach ($order->get_items() as $item_id => $line_item) {
                $data = $line_item->get_data();
                $product_id =  $data['product_id'];

                // Check the agreement status for the product

                $agreement_status = get_post_meta($product_id,  W_AGREE_META_PREFIX.'agreement_status', true);
                $product_agreement_data = wpautop(get_post_meta($product_id, W_AGREE_META_PREFIX.'product_agreement_data', true));

                $product = $line_item->get_product();
                $product_price_org = $product->get_price();
                $product_order_total = $line_item->get_total();
                $product_name = $line_item->get_name();
                $product_qty = $line_item->get_quantity();
                $product_type = $product->get_type();
                $product_price = '<span>' . $currency_symbol . '</span>' . $product_price_org;
                $order_total = '<span>' . $currency_symbol . '</span>' . $product_order_total;

                if ($agreement_status && $product_agreement_data) {

                    $customer_id = $order->get_user_id();
                    $signer_name = $_POST['signer_name'];
                    $digital_sign_draw = $_POST['signer_canvas'];

                    update_post_meta($order_id, 'signer_id', $customer_id);
                    update_post_meta($order_id, 'signer_name', $signer_name);
                    update_post_meta($order_id, 'signer_canvas', $digital_sign_draw);

                    $digital_sign = '<img style="margin-top: 20px;" src="' . $digital_sign_draw . '" width="300" height="300">';
                   
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

                    // Generate a unique download link
                    $download_link = $uploads_dir['baseurl'] . '/wc_agreement/' . $order_id . '/' . $pdf_file_name;
                    
                    $line_item->update_meta_data('_pdf_download_link', $download_link);
                    $line_item->save();
                } 
                else {
                    $get_option_agreement = wpautop(get_option(W_AGREE_META_PREFIX.'agreement_option'));
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

                    // Generate a unique download link
                    $download_link = $uploads_dir['baseurl'] . '/wc_agreement/' . $order_id . '/' . $pdf_file_name;
                    $line_item->update_meta_data('_pdf_download_link', $download_link);
                    $line_item->save();
                }
            }
        }

        /**
         * Generated pdf links display on thankyou page .
         * 
         * Pdf links display on thankyou page
         * 
         * @package Woocommerce Agreement
         * @since 1.0.0
         */

        public function display_pdf_download_links($order_id)
        {
            $order = wc_get_order($order_id);
            // Initialize the table structure
            $table = '<table class="display_table_agreement">';
            $table .= '<tr>';
            $table .= '<th style="border: 1px solid #000; padding: 10px;">'.esc_html("Product Title","w_agree").'</th>';
            $table .= '<th style="border: 1px solid #000; padding: 10px;">'.esc_html("Agreement Links","w_agree").'</th>';
            $table .= '</tr>';

            foreach ($order->get_items() as $item_id => $line_item) {

                $product_name = $line_item->get_name();
                $download_link = $line_item->get_meta('_pdf_download_link');

                if (!empty($download_link)) {
                    // Add a row to the table for each product
                    $table .= '<tr>';
                    $table .= '<td style="border: 1px solid #000; padding: 10px;">' . $product_name . '</td>';
                    $table .= '<td style="border: 1px solid #000; padding: 10px;"><a href="' . esc_url($download_link) . '" download>' . esc_html("Download", "w_agree") . '</a></td>';
                    $table .= '</tr>';
                }
            }
            // Close the table
            $table .= '</table>';
            // Display the table
            echo $table;
        }

        /**
         * Adding hooks
         *
         * @package Woocommerce Agreement
         * @since 1.0.0
         */

        public function add_hooks()
        {
            add_action('woocommerce_checkout_order_processed', array($this, 'generate_pdf_link_agreement'), 20);
            add_action('woocommerce_thankyou', array($this, 'display_pdf_download_links'));
            add_action('woocommerce_review_order_after_payment',  array($this, 'digital_signature_drawer'), 20);
            add_action('wp_ajax_update_canvas_value', array($this, 'update_canvas_value'));
            add_action('wp_ajax_nopriv_update_canvas_value', array($this, 'update_canvas_value'));
        }
    }
}
