<?php
/**
 * Plugin Name: CXPay Redirect Gateway for WooCommerce
 * Plugin URI: http://www.patsatech.com/
 * Description: WooCommerce Plugin for accepting payment through CXPay Redirect Gateway.
 * Version: 1.1.0
 * Author: PatSaTECH
 * Author URI: http://www.patsatech.com
 * Contributors: patsatech
 * Requires at least: 3.5
 * Tested up to: 4.9.8
 *
 * Text Domain: woo-cxpay_redirect-patsatech
 * Domain Path: /lang/
 *
 * @package CXPay Redirect Gateway for WooCommerce
 * @author PatSaTECH
 */

add_action('plugins_loaded', 'init_woocommerce_cxpay_redirect', 0);

function init_woocommerce_cxpay_redirect() {

  if ( ! class_exists( 'WC_Payment_Gateway' ) ) { return; }

	load_plugin_textdomain('woo-cxpay_redirect-patsatech', false, dirname( plugin_basename( __FILE__ ) ) . '/lang');

	class woocommerce_cxpay_redirect extends WC_Payment_Gateway {

    public function __construct() {
			global $woocommerce;

      $this->id			      = 'cxpay_redirect';
      $this->method_title = __( 'CXPay Redirect', 'woo-cxpay_redirect-patsatech' );
			$this->icon			= apply_filters( 'woocommerce_cxpay_redirect_icon', '' );
      $this->has_fields 	= false;
      $this->notify_url   = str_replace( 'https:', 'http:', home_url( '/wc-api/woocommerce_cxpay_redirect' ) );

      $default_card_type_options = array(
        'VISA' => 'VISA',
        'MC' => 'MasterCard',
        'AMEX' => 'American Express',
        'DISC' => 'Discover',
        'SECUREMASTERCARD' => 'MasterCard SecureCode',
        'SECUREVISA' => 'Verified by VISA'
      );
      $this->card_type_options = apply_filters( 'woocommerce_cxpay_redirect_card_types', $default_card_type_options );

			// Load the form fields.
			$this->init_form_fields();

			// Load the settings.
			$this->init_settings();

			// Define user set variables
			$this->title 		   = $this->get_option( 'title' );
			$this->description = $this->get_option( 'description' );
			$this->apikey      = $this->get_option( 'apikey' );
			$this->debug       = $this->get_option( 'debug' );
    	$this->cardtypes   = $this->get_option('cardtypes');
      $this->gateway_url = 'https://cxpay.transactiongateway.com/api/v2/three-step';

			// Logs
			if ( 'yes' == $this->debug ) {
				$this->log = new WC_Logger();
			}

			// Actions
			add_action( 'init', array($this, 'ipn_check') );
			add_action( 'woocommerce_api_woocommerce_cxpay_redirect', array( $this, 'ipn_check' ) );
			add_action( 'valid-cxpay_redirect-ipn-request', array( $this, 'successful_request' ) );
			add_action( 'woocommerce_receipt_cxpay_redirect', array( $this, 'receipt_page' ) );
			add_action( 'woocommerce_update_options_payment_gateways', array( $this, 'process_admin_options' ) );
			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
    }

		/**
		 * Admin Panel Options
		 * - Options for bits like 'title' and availability on a country-by-country basis
		 *
		 * @since 1.0.0
		 */
		public function admin_options() {
      ?>
      <h3><?php _e('CXPay Redirect', 'woo-cxpay_redirect-patsatech'); ?></h3>
      <p><?php _e('CXPay Redirect works by sending the user to CXPay Redirect to enter their payment information.', 'woo-cxpay_redirect-patsatech'); ?></p>
      <table class="form-table">
      <?php
        // Generate the HTML For the settings form.
        $this->generate_settings_html();
      ?>
      </table><!--/.form-table-->
      <?php
    } // End admin_options()

		/**
	   * Initialise Gateway Settings Form Fields
	   */
	  function init_form_fields() {
      $this->form_fields = array(
        'enabled' => array(
          'title' => __( 'Enable/Disable', 'woo-cxpay_redirect-patsatech' ),
          'type' => 'checkbox',
          'label' => __( 'Enable CXPay Redirect', 'woo-cxpay_redirect-patsatech' ),
          'default' => 'yes',
          'desc_tip'    => true,
        ),
        'title' => array(
          'title' => __( 'Title', 'woo-cxpay_redirect-patsatech' ),
          'type' => 'text',
          'description' => __( 'This controls the title which the user sees during checkout.', 'woo-cxpay_redirect-patsatech' ),
          'default' => __( 'Pay through a 3DS Enabled Credit Card solution of CX Pay.', 'woo-cxpay_redirect-patsatech' ),
          'desc_tip'    => true,
        ),
        'description' => array(
          'title' => __( 'Description', 'woo-cxpay_redirect-patsatech' ),
          'type' => 'textarea',
          'description' => __( 'This controls the description which the user sees during checkout.', 'woo-cxpay_redirect-patsatech' ),
          'default' => __("", 'woo-cxpay_redirect-patsatech'),
          'desc_tip'    => true,

        ),
        'apikey' => array(
          'title' => __( 'API Key', 'woo-cxpay_redirect-patsatech' ),
          'type' => 'text',
          'description' => __( 'Please enter your CXPay Redirect API Key; this is needed in order to take payment.', 'woo-cxpay_redirect-patsatech' ),
          'default' => '',
          'desc_tip'    => true,
        ),
        'cardtypes'	=> array(
          'title' => __( 'Accepted Cards', 'woo-cxpay_redirect-patsatech' ),
          'type' => 'multiselect',
          'description' => __( 'Select which card types to accept.', 'woo-cxpay_redirect-patsatech' ),
          'default' => 'visa',
          'options' => $this->card_type_options,
          'desc_tip'    => true,
        ),
        'debug' => array(
          'title' => __( 'Debug Log', 'woocommerce' ),
          'type' => 'checkbox',
          'label' => __( 'Enable logging', 'woocommerce' ),
          'default' => 'no',
          'description' => sprintf( __( 'Log CXPay Redirect events, such as IPN requests, inside <code>woocommerce/logs/cxpay_redirect-%s.txt</code>', 'woocommerce' ), sanitize_file_name( wp_hash( 'cxpay_redirect' ) ) ),
          'desc_tip'    => true,
        )
      );

    } // End init_form_fields()


    /**
    * get_icon function.
    *
    * @access public
    * @return string
    */
    function get_icon() {
      global $woocommerce;

      $icon = '<br>';
      if ( $this->icon ) {
        // default behavior
        $icon .= '<img src="' . $this->force_ssl( $this->icon ) . '" alt="' . $this->title . '" />';
      } elseif ( $this->cardtypes ) {
        // display icons for the selected card types
        $icon = '<br>';
        foreach ( $this->cardtypes as $cardtype ) {
          if ( file_exists( plugin_dir_path( __FILE__ ) . '/images/card-' . strtolower( $cardtype ) . '.png' ) ) {
            $icon .= '<img src="' . $this->force_ssl( plugins_url( '/images/card-' . strtolower( $cardtype ) . '.png', __FILE__ ) ) . '" alt="' . strtolower( $cardtype ) . '" />';
          }
        }
        $icon .= '<img src="' . $this->force_ssl( plugins_url( '/images/cxpay.png', __FILE__ ) ) . '" alt="cxpay" />';
      }

      return apply_filters( 'woocommerce_gateway_icon', $icon, $this->id );
    }

    private function force_ssl($url){

      if ( 'yes' == get_option( 'woocommerce_force_ssl_checkout' ) ) {
        $url = str_replace( 'http:', 'https:', $url );
      }

      return $url;
    }

    /**
     * There are no payment fields for cxpay_redirect, but we want to show the description if set.
     **/
    function payment_fields() {
      if ($this->description) echo wpautop(wptexturize($this->description));
    }

		/**
		 * Get cxpay_redirect Args for passing to PP
		 *
		 * @access public
		 * @param mixed $order
		 * @return array
		 */
		function get_cxpay_redirect_args( $order ) {
			global $woocommerce;

      if(empty($this->apikey) || $this->apikey == ''){
        // Order failed
        wc_add_notice( __('Please enter the Api Key in the Settings.', 'woo-cxpay_redirect-patsatech'), $notice_type = 'error' );
        wp_redirect( get_permalink(get_option( 'woocommerce_checkout_page_id' )) ); exit;
      }

      $order_id = $order->id;

      if ( 'yes' == $this->debug )
        $this->log->add( 'cxpay_redirect', 'Generating payment form for order ' . $order->get_order_number() . '. Notify URL: ' . $this->notify_url );

      //$total = strval(number_format($order->order_total, 2, '.', '')*100);

      $Currency = get_woocommerce_currency();

      $cart = WC()->session->get( 'cart', null );

      if ( is_array( $cart ) ) {
        foreach ( $cart as $key => $values ) {
          $_product = wc_get_product( $values['variation_id'] ? $values['variation_id'] : $values['product_id'] );
          if ( ! empty( $_product ) && $_product->exists() && $values['quantity'] > 0 ) {
            if ( ! $_product->is_purchasable() ) {
              // Flag to indicate the stored cart should be update
              $update_cart_session = true;
              wc_add_notice( sprintf( __( '%s has been removed from your cart because it can no longer be purchased. Please contact us if you need assistance.', 'woocommerce' ), $_product->get_title() ), 'error' );
              do_action( 'woocommerce_remove_cart_item_from_session', $key, $values );
            } else {
              // Put session data into array. Run through filter so other plugins can load their own session data
              $session_data = array_merge( $values, array( 'data' => $_product ) );
              $this->cart_contents[ $key ] = apply_filters( 'woocommerce_get_cart_item_from_session', $session_data, $values, $key );
            }
          }
        }
      }

      $gatewayURL = $this->gateway_url;
      $APIKey = $this->apikey;

      $xmlRequest = new DOMDocument('1.0','UTF-8');
      $xmlRequest->formatOutput = true;
      $xmlSale = $xmlRequest->createElement('sale');
      // Helper function to make building xml dom easier

      // Amount, authentication, and Redirect-URL are typically the bare minimum.
      $this->appendXmlNode($xmlRequest, $xmlSale,'api-key',$APIKey);
      $this->appendXmlNode($xmlRequest, $xmlSale,'redirect-url',$this->notify_url);
      $this->appendXmlNode($xmlRequest, $xmlSale, 'amount', $order->order_total);
      $this->appendXmlNode($xmlRequest, $xmlSale, 'ip-address', $_SERVER["REMOTE_ADDR"]);
      //appendXmlNode($xmlRequest, $xmlSale, 'processor-id' , 'processor-a');
      $this->appendXmlNode($xmlRequest, $xmlSale, 'currency', $Currency);

      // Some additonal fields may have been previously decided by user
      $this->appendXmlNode($xmlRequest, $xmlSale, 'order-id', $order_id);
      $this->appendXmlNode($xmlRequest, $xmlSale, 'order-description', 'Order No.'.$order_id);
      $this->appendXmlNode($xmlRequest, $xmlSale, 'merchant-defined-field-1' , 'Red');
      $this->appendXmlNode($xmlRequest, $xmlSale, 'merchant-defined-field-2', 'Medium');
      $this->appendXmlNode($xmlRequest, $xmlSale, 'tax-amount' , '0.00');
      $this->appendXmlNode($xmlRequest, $xmlSale, 'shipping-amount' , '0.00');

      // Set the Billing and Shipping from what was collected on initial shopping cart form
      $xmlBillingAddress = $xmlRequest->createElement('billing');
      $this->appendXmlNode($xmlRequest, $xmlBillingAddress,'first-name', $order->billing_first_name);
      $this->appendXmlNode($xmlRequest, $xmlBillingAddress,'last-name', $order->billing_last_name);
      $this->appendXmlNode($xmlRequest, $xmlBillingAddress,'address1', $order->billing_address_1);
      $this->appendXmlNode($xmlRequest, $xmlBillingAddress,'city', $order->billing_city);
      $this->appendXmlNode($xmlRequest, $xmlBillingAddress,'state', $order->billing_state);
      $this->appendXmlNode($xmlRequest, $xmlBillingAddress,'postal', $order->billing_postcode);
      //billing-address-email
      $this->appendXmlNode($xmlRequest, $xmlBillingAddress,'country', $order->billing_country);
      $this->appendXmlNode($xmlRequest, $xmlBillingAddress,'email', $order->billing_email);

      $this->appendXmlNode($xmlRequest, $xmlBillingAddress,'phone', $order->billing_phone);
      $this->appendXmlNode($xmlRequest, $xmlBillingAddress,'company', $order->billing_company);
      $this->appendXmlNode($xmlRequest, $xmlBillingAddress,'address2', $order->billing_address_2);
      $this->appendXmlNode($xmlRequest, $xmlBillingAddress,'fax', '');
      $xmlSale->appendChild($xmlBillingAddress);

      $xmlShippingAddress = $xmlRequest->createElement('shipping');
      $this->appendXmlNode($xmlRequest, $xmlShippingAddress,'first-name', $order->shipping_first_name);
      $this->appendXmlNode($xmlRequest, $xmlShippingAddress,'last-name', $order->shipping_last_name);
      $this->appendXmlNode($xmlRequest, $xmlShippingAddress,'address1', $order->shipping_address_1);
      $this->appendXmlNode($xmlRequest, $xmlShippingAddress,'city', $order->shipping_city);
      $this->appendXmlNode($xmlRequest, $xmlShippingAddress,'state', $order->shipping_state);
      $this->appendXmlNode($xmlRequest, $xmlShippingAddress,'postal', $order->shipping_postcode);
      $this->appendXmlNode($xmlRequest, $xmlShippingAddress,'country', $order->shipping_country);
      $this->appendXmlNode($xmlRequest, $xmlShippingAddress,'phone', $order->shipping_phone);
      $this->appendXmlNode($xmlRequest, $xmlShippingAddress,'company', $order->shipping_company);
      $this->appendXmlNode($xmlRequest, $xmlShippingAddress,'address2', $order->shipping_address_2);
      $xmlSale->appendChild($xmlShippingAddress);

      $xmlProduct = $xmlRequest->createElement('product');
      $this->appendXmlNode($xmlRequest, $xmlProduct,'product-code' , $values['product_id']);
      $this->appendXmlNode($xmlRequest, $xmlProduct,'description' , '');
      $this->appendXmlNode($xmlRequest, $xmlProduct,'commodity-code' , '');
      $this->appendXmlNode($xmlRequest, $xmlProduct,'unit-of-measure' , '');
      $this->appendXmlNode($xmlRequest, $xmlProduct,'unit-cost' , '');
      $this->appendXmlNode($xmlRequest, $xmlProduct,'quantity' , $values['quantity']);
      $this->appendXmlNode($xmlRequest, $xmlProduct,'total-amount' , $order->order_total);
      $this->appendXmlNode($xmlRequest, $xmlProduct,'tax-amount' , '');

      $this->appendXmlNode($xmlRequest, $xmlProduct,'tax-rate' , '');
      $this->appendXmlNode($xmlRequest, $xmlProduct,'discount-amount', '');
      $this->appendXmlNode($xmlRequest, $xmlProduct,'discount-rate' , '');
      $this->appendXmlNode($xmlRequest, $xmlProduct,'tax-type' , 'sales');
      $this->appendXmlNode($xmlRequest, $xmlProduct,'alternate-tax-id' , '');

      $xmlSale->appendChild($xmlProduct);

      $xmlRequest->appendChild($xmlSale);

      // Process Step One: Submit all transaction details to the Payment Gateway except the customer's sensitive payment information.
      // The Payment Gateway will return a variable form-url.
      $data = $this->sendXMLviaCurl($xmlRequest,$gatewayURL);

      // Parse Step One's XML response
      $gwResponse = @new SimpleXMLElement($data);

      if ((string)$gwResponse->result ==1 ) {
        // The form url for used in Step Two below
        $formURL = $gwResponse->{'form-url'};

      } else {
        throw New Exception(print " Error, received " . $data);
      }

      return $formURL;

	  }

		/**
		 * Generate the cxpay_redirect button link
		 **/
    public function generate_cxpay_redirect_form( $order_id ) {
      global $woocommerce;

			$order = new WC_Order( $order_id );

			$cxpay_redirect_url = $this->get_cxpay_redirect_args( $order );

			return $cxpay_redirect_url;

		}


		/**
		 * Process the payment and return the result
		 **/
		function process_payment( $order_id ) {

			$order = new WC_Order( $order_id );

      $redirect = $order->get_checkout_payment_url( true );

			return array(
				'result' 	=> 'success',
				'redirect'	=> $redirect
			);

		}

		/**
		 * receipt_page
		 **/
		function receipt_page( $order ) {
      global $woocommerce;

      echo '<p>'.__('Thank you for your order, please click the button below to pay with CXPay Redirect.', 'woo-cxpay_redirect-patsatech').'</p>';

      echo '<form action="'.$this->generate_cxpay_redirect_form( $order ). '" method="POST">
            <h3>Secure payment by credit card with CXPay</h3>
             <table>
               <tr><td>Credit Card Number</td><td><INPUT type ="text" name="billing-cc-number" value=""> </td></tr>
               <tr><td>Expiration Date</td><td><INPUT type ="text" name="billing-cc-exp" value="" placeholder="MM/YY" > </td></tr>
               <tr><td>CVV</td><td><INPUT type ="text" name="cvv" value="" > </td></tr>
               <tr><Td colspan="2" align=center><INPUT type ="submit" value="Submit"></td> </tr>
            </table>
           </form>';
			//exit();
		}

		/**
		 * Successful Payment!
		 **/
		function ipn_check() {
      if(!empty($_GET['token-id'])){
        if ( 'yes' == $this->debug ){
          $this->log->add( 'cxpay_redirect', 'IPN Response: ' . print_r( $_POST, true ) );
        }

        do_action( "valid-cxpay_redirect-ipn-request", $_POST );
      }else {
        wp_die( "CXPay Redirect IPN Request Failure" );
      }
    }

		/**
		 * Successful Payment!
		 **/
		function successful_request() {
			global $woocommerce;

		    $APIKey = $this->apikey;
		    $tokenId = $_GET['token-id'];

		    $xmlRequest = new DOMDocument('1.0','UTF-8');
		    $xmlRequest->formatOutput = true;
		    $xmlCompleteTransaction = $xmlRequest->createElement('complete-action');
		    $this->appendXmlNode($xmlRequest, $xmlCompleteTransaction,'api-key',$APIKey);
		    $this->appendXmlNode($xmlRequest, $xmlCompleteTransaction,'token-id',$tokenId);
		    $xmlRequest->appendChild($xmlCompleteTransaction);
		    $gatewayURL = $this->gateway_url;

		    $data = $this->sendXMLviaCurl($xmlRequest,$gatewayURL);

		    $gwResponse = @new SimpleXMLElement((string)$data);

        // print_r($gwResponse);echo $gwResponse->{'transaction-id'};die('success');
        $order_id = $gwResponse->{'order-id'};
        $transaction_id = $gwResponse->{'transaction-id'};
        $ResponseText = $gwResponse->{'result-text'};
        $ResponsCode = $gwResponse->{'result-code'};

        // OrderID holds post ID
		    if ( !empty($_GET['token-id']) ) {
          $order = new WC_Order( (int) $order_id );

          // We are here so lets check status and do actions
          if (strtolower($ResponsCode) == '100') {

            // Check order not already completed
            if ( $order->status == 'completed' ) {
              if ( 'yes' == $this->debug )
              $this->log->add( 'cxpay_redirect', 'Aborting, Order #' . $order->id . ' is already complete.' );
              exit;
            }
            // Payment completed
            $order->payment_complete();

            $order->add_order_note(sprintf(__('CXPay Redirect Payment Completed. The Transaction Id is %s.', 'woo-cxpay_redirect-patsatech'), $transaction_id));

            wp_redirect( $this->get_return_url( $order ) ); exit;

				}else{

          // Order failed
          wc_add_notice( sprintf(__('Transaction Failed. Error Message : %s', 'woo-cxpay_redirect-patsatech'), $ResponseText ), $notice_type = 'error' );

          wp_redirect( get_permalink(get_option( 'woocommerce_checkout_page_id' )) ); exit;
				}
      }
    }

		function appendXmlNode($domDocument, $parentNode, $name, $value) {
      $childNode      = $domDocument->createElement($name);
      $childNodeValue = $domDocument->createTextNode($value);
      $childNode->appendChild($childNodeValue);
      $parentNode->appendChild($childNode);
    }

    function sendXMLviaCurl($xmlRequest,$gatewayURL) {
      // helper function demonstrating how to send the xml with curl
      $response = wp_remote_post(
				$gatewayURL,
				array(
					'method' => 'POST',
					'timeout' => 60,
					'redirection' => 5,
					'httpversion' => '1.0',
					'headers' => array(
						'Content-Type' => 'text/xml'
					),
					'body' => $xmlRequest->saveXML(),
					'sslverify' => false
				)
			);

      if( !is_wp_error($response) && $response['response']['code'] >= 200 && $response['response']['code'] < 300 ){
        return $response['body'];
      }else{
        wc_add_notice( __('Gateway Error. Please Notify the Store Owner about this error.', 'woo-cxpay_redirect-patsatech'), 'error' );
        wp_redirect( get_permalink(get_option( 'woocommerce_checkout_page_id' )) ); exit;
      }

      return $response['body'];

	  }

	}

	/**
	 * Add the gateway to WooCommerce
	 **/
	function add_cxpay_redirect_gateway( $methods ) {
		$methods[] = 'woocommerce_cxpay_redirect'; return $methods;
	}
	add_filter('woocommerce_payment_gateways', 'add_cxpay_redirect_gateway' );

}
