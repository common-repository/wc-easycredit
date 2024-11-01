<?php
use Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType;
use Automattic\WooCommerce\Blocks\Payments\PaymentResult;
use Automattic\WooCommerce\Blocks\Payments\PaymentContext;

defined( 'ABSPATH' ) || exit;

class WC_Gateway_Ratenkaufbyeasycredit_Payment_Method extends AbstractPaymentMethodType {

    protected $plugin_file = null;
    protected $name = 'ratenkaufbyeasycredit';

    public function __construct($plugin_file) {
        $this->plugin_file = $plugin_file;
    }

    public function initialize()
    {
        $this->settings = get_option('woocommerce_ratenkaufbyeasycredit_settings', []);
    }

    public function is_active()
    {
        return $this->settings['enabled'] ?? false;
    }

    public function get_payment_method_script_handles()
    {
        $this->register_script_handles();

        return ['wc-easycredit-blocks'];
    }

    public function get_payment_method_script_handles_for_admin()
    {
        return $this->get_payment_method_script_handles();
    }

    private function register_script_handles()
    {
        $dir = 'modules/checkout/build';

        $dependencies = [];
        $version = '1.0';

        require plugin_dir_path($this->plugin_file) . $dir . '/index.asset.php';

        wp_register_script(
            'wc-easycredit-blocks',
            plugin_dir_url($this->plugin_file) . $dir . '/index.js',
            $dependencies,
            $version,
            true
        );
        wp_set_script_translations(
            'wc-easycredit-blocks',
            'wc-easycredit'
        );
    }

    public function get_payment_method_data()
    {
        return [
            'id'          => $this->name,
            'title'       => $this->settings['title'] ?? '',
            'description' => $this->settings['description'] ?? '',
            'supports'    => ['products'],
            'enabled'     => $this->is_active() === 'yes',
            'apiKey'      => $this->settings['api_key'],
            'expressUrl'  => get_site_url(null, 'easycredit/express')
        ];
    }
}