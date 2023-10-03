<?php

/**
 * Plugin Name: nethttp.net-base-plugin
 * Plugin URI: https://github.com/yrbane/nethttp.net-base-plugin
 * Description: The WordPress plugin "BasePlugin" is a foundational class designed to streamline the development of custom plugins for WordPress. It provides an organized structure and essential features for rapidly creating custom extensions. This class simplifies translation management, activation message display, and offers a robust foundation for adding plugin-specific functionality. Developers can extend this class by creating a child class to implement project-specific features, all while benefiting from a well-documented and ready-to-use structure. 
 * Version: 1.0.0
 * Author: Barney <yrbane@nethttp.net>
 * Author URI: https://github.com/yrbane
 * Requires PHP: 7.4
 * Text Domain: default
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Domain Path:       /languages
 */

/**
 * Class BasePlugin
 *
 * Base class for WordPress plugins.
 */
class BasePlugin
{
    /**
     * @var string Plugin name.
     */
    protected string $plugin_name = '';

    /**
     * @var string Plugin nice name.
     */
    protected string $plugin_nice_name = '';

    /**
     * @var string Plugin name slug.
     */
    protected string $slug = '';

    /**
     * @var string Plugin short description.
     */
    protected string $plugin_short_description = '';

    /**
     * @var string Plugin GitHub URL.
     */
    protected string $github_url = '';

    /**
     * @var string Plugin author.
     */
    protected string $plugin_author = '';

    /**
     * @var string Plugin version.
     */
    protected string $version = '1.0.0';

    /**
     * @var string Current class name.
     */
    protected string $current_classname;

    /**
     * @var string Plugin file.
     */
    protected string $plugin_file;

    /**
     * @var string Activation message.
     */
    protected string $activation_message = 'Thank you';

    /**
     * BasePlugin constructor.
     * @param string $file Plugin file.
     */
    final public function __construct($file=__FILE__)
    {
        ob_start();
        $this->current_classname = get_class($this);
        $this->plugin_file = $file;

        $this->set_locale();

        // Generate a unique form token for each session
        $this->generate_form_token();

        $this->verifyPluginRequirements();

        $this->slug = sanitize_title($this->plugin_name);

        

        $this->activation_message = sprintf(
            '<div class="notice notice-success is-dismissible custom-activation-message">
                <p><strong>🤩 %s ' . $this->plugin_nice_name . ' from nethttp.net!</strong></p>
                <p>%s <a href="%s">' . $this->plugin_nice_name . ' %s</a> page.</p>
                <p>%s</p>
                <form method="post" action=""><button type="submit" name="'.$this->slug.'_hide_activation_message" value="1" class="button">%s</button></form>
              </div>',
            __('Thank you for installing the '),
            __('To configure the plugin settings, please visit the'),
            admin_url('admin.php?page='.$this->slug.'-admin'),
            __('Settings'),
            __($this->plugin_short_description),
            __('Don\'t show this message again')

        );

        // Load your plugin functionality here
        $this->loadPluginFunctionality();

        $this->init();
    }

    /**
     * Verify plugin requirements.
     */
    final private function verifyPluginRequirements(): void
    {
        $this->checkPropertyToOverride('plugin_name', 'nethttp.net-base-plugin');
        $this->checkPropertyToOverride('plugin_nice_name', 'Plugin\'s Base');
        $this->checkPropertyToOverride('plugin_author', 'Barney');
        $this->checkPropertyToOverride('github_url', 'https://github.com/yrbane/nethttp.net-base-plugin');
        $this->checkPropertyToOverride('plugin_short_description',__('The WordPress plugin "BasePlugin" is a foundational class designed to streamline the development of custom plugins for WordPress. It provides an organized structure and essential features for rapidly creating custom extensions. This class simplifies translation management, activation message display, and offers a robust foundation for adding plugin-specific functionality. Developers can extend this class by creating a child class to implement project-specific features, all while benefiting from a well-documented and ready-to-use structure.'));
    }

    /**
     * Output ob buffer.
     */
    final public function ob_output(): void
    {
        echo ob_get_clean();
    }

    /**
     * Check and set properties that need to be overridden in child classes.
     *
     * @param string $name Property name.
     * @param string $default_value Default value for the property.
     */
    final private function checkPropertyToOverride($name, $default_value): void
    {
        if(empty($this->{$name})){
            $this->{$name} = $default_value;
            if ($this->current_classname != 'BasePlugin') {
                $this->showError(__(sprintf('You have to override property `%s` in your child class',$name)) . ' `' . $this->current_classname . '`');
            }
        }
    }

    /**
     * Set the plugin's locale.
     *
     * This method sets the locale for the plugin's translation files based on the site's current locale
     * and any filters applied to the 'plugin_locale' hook.
     *
     */
    public function set_locale(): void
    {
        $domain = 'default';
        $locale = apply_filters('plugin_locale', get_locale(), $domain);
        load_plugin_textdomain($domain, false, basename(dirname($this->plugin_file)) . '/languages');
    }

    /**
     * Generates a form token and stores it in the session.
     * @since 1.0.0
     */
    private function generate_form_token(): void
    {
        if (!isset($_SESSION)) {
            session_start();
        }

        // Generate and store the token in the session if it doesn't exist already
        if (!isset($_SESSION['contact_form_token'])) {
            $_SESSION['contact_form_token'] = bin2hex(random_bytes(16));
        }
    }


    /**
     * Set the email content type to "text/html".
     *
     * This method sets the content type for email messages to "text/html".
     * 
     *  @since 1.0.0
     *
     * @return string The email content type, which is "text/html".
     */
    public function set_email_content_type(): string
    {
        return "text/html";
    }

    /**
     * Enqueues the admin stylesheet.
     * @since 1.0.0
     */
    public function admin_enqueue_scripts(): void
    {
        wp_enqueue_style('activation-message', plugin_dir_url($this->plugin_file) . 'css/activation-message.css');
    }

    /**
     * Enqueues scripts for the front-end.
     */
    public function enqueue_scripts(): void
    {
        //Enqueues the frontend styles and scripts
    }

    /**
     * Set activation messages
     */
    protected function set_activation_message(): void
    {
        // Activation message with GitHub link
        $activation_message = sprintf(
            __('Thank you for installing the plugin!') . ' ' . __('Visit our') . ' <a href="%s" target="_blank">GitHub</a> ' . __('for more information and updates.'),
            esc_url($this->github_url)
        );

        $this->showNotice($activation_message);
    }

    /**
     * Display a welcome message on plugin activation.
     * @since 1.0.0
     */
    public function activation_message(): void
    {
        // Check if the option to hide the activation message is not set
        if (!get_option($this->slug.'_hide_activation_message')) {
            echo $this->activation_message;
        }
    }

    /**
     * Handle hiding the activation message.
     * @since 1.0.0
     */
    public function hide_activation_message(): void
    {
        // Check if the option to hide the activation message has been checked
        if (isset($_POST[$this->slug.'_hide_activation_message']) && $_POST[$this->slug.'_hide_activation_message'] === '1') {
            update_option($this->slug.'_hide_activation_message', '1');
        }
    }

    /**
     * Plugin activation
     */
    public function onActivation(): void
    {
        // Add actions to perform on plugin activation
        $this->add_hooks();
        $this->set_activation_message();
    }

    /**
     * Plugin deactivation
     */
    public function onDeactivation(): void
    {
        // Add actions to perform on plugin deactivation
        $this->remove_hooks();
        $this->set_deactivation_message();
    }

    /**
     * Load translations for the plugin.
     */
    public function loadTranslations(): void
    {
        load_plugin_textdomain($this->slug, false, dirname(plugin_basename(__FILE__)) . '/languages');
    }

    /**
     * Plugin initialization
     */
    public function init(): void
    {
        // Add actions for plugin initialization
        $this->add_hooks();
    }

    /**
     * Add WordPress hooks
     */
    protected function add_hooks(): void
    {
        // Activation and Deactivation Hooks
        register_activation_hook($this->plugin_file, array($this, 'onActivation'));
        register_deactivation_hook($this->plugin_file, array($this, 'onDeactivation'));

        // Load translation files
        add_action('init', array($this, 'loadTranslations'));

        // Display admin notices
        add_action('admin_notices', array($this, 'activation_message'));

        // Add action to display admin stylesheets and scripts
        add_action('admin_enqueue_scripts', [$this, 'admin_enqueue_scripts']);

        // Add action to handle activation message dismissal
        add_action('admin_init', [$this, 'hide_activation_message']);


        // Load plugin scripts and styles
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));


        // Add a menu item in the admin dashboard
        add_action('admin_menu', array($this, 'add_admin_menu'));

        // GitHub link in plugin description
        add_filter('plugin_action_links_' . plugin_basename($this->plugin_file), [$this, 'addGitHubLink']);

        add_filter('wp_mail_content_type', [$this, 'set_email_content_type']);

        add_action('admin_notices', array($this, 'ob_output'));

        // Add hooks that your plugin uses here
    }

    /**
     * Remove WordPress hooks
     */
    protected function remove_hooks()
    {
        // Remove hooks when deactivating the plugin
    }

    /**
     * Set deactivation message.
     */
    protected function set_deactivation_message()
    {
        // Default deactivation message
        $this->showNotice(__('Plugin deactivated.'));
    }

    /**
     * Show error message.
     *
     * @param string $message Error message.
     * @param bool $dismissible Whether the message is dismissible.
     * @param bool $unique Whether the message is unique.
     */
    protected function showError(string $message, bool $dismissible = true, bool $unique = false): void
    {
        $this->showMessage('error', $message, $dismissible, $unique);
    }

    /**
     * Show warning message.
     *
     * @param string $message Warning message.
     * @param bool $dismissible Whether the message is dismissible.
     * @param bool $unique Whether the message is unique.
     */
    protected function showWarning(string $message, bool $dismissible = true, bool $unique = false): void
    {
        $this->showMessage('warning', $message, $dismissible, $unique);
    }

    /**
     * Show notice message.
     *
     * @param string $message Notice message.
     * @param bool $dismissible Whether the message is dismissible.
     * @param bool $unique Whether the message is unique.
     */
    protected function showNotice(string $message, bool $dismissible = true, bool $unique = false): void
    {
        $this->showMessage('notice', $message, $dismissible, $unique);
    }

    /**
     * Show a custom message.
     *
     * @param string $type Message type (error, warning, notice, etc.).
     * @param string $message Message content.
     * @param bool $dismissible Whether the message is dismissible.
     * @param bool $unique Whether the message is unique.
     */
    protected function showMessage(string $type, string $message, bool $dismissible, bool $unique): void
    {
        switch ($type) {
            case 'error':
                $notice_class = 'error';
                break;
            case 'warning':
                $notice_class = 'warning';
                break;
            case 'notice':
                $notice_class = 'notice';
                break;
            default:
                $notice_class = 'updated';
        }

        if ($dismissible) {
            $notice_class .= ' is-dismissible';
        }

        if ($unique && get_transient($type . '_message_displayed')) {
            return;
        }

        echo '<div class="' . esc_attr($notice_class) . '"><p>' . $message . '</p></div>';

        if ($unique) {
            set_transient($type . '_message_displayed', true, DAY_IN_SECONDS);
        }
    }

    /**
     * Add an admin menu item.
     */
    public function add_admin_menu(): void
    {
        // Add a submenu under "Base Plugin" in the admin menu
        add_menu_page(
            $this->plugin_nice_name, // Page title
            $this->plugin_nice_name, // Menu text
            'manage_options', // Required capability to view the menu
            $this->slug . '-admin', // Page slug
            [$this, 'admin_page_content'] // Callback to display the page content
        );

        // Add a "Documentation" submenu under "Base Plugin"
        add_submenu_page(
            $this->slug . '-admin', // Parent slug
            'Documentation', // Page title
            'Documentation', // Menu text
            'manage_options', // Required capability to view the menu
            $this->slug . '-documentation', // Page slug
            [$this, 'documentation_page_content'] // Callback to display the documentation page content
        );
    }

    /**
     * Callback to display the admin page content.
     */
    public function admin_page_content(): void
    {
        echo '<h1>' . __('Base plugin') . '</h1>';

        echo $this->plugin_short_description; 

        // Add link to documentation page
        echo '<p>' . __('Nothing to configure here ;-) RTFM on the documentation page! ') . ' <a href="' . admin_url('admin.php?page=' . $this->slug . '-documentation') . '">' . __('documentation page') . '</a>.</p>';
    }

    /**
     * Callback to display the documentation page content.
     */
    public function documentation_page_content()
    {
        // Content for the documentation page
        echo '<div class="wrap">';
        echo '<h2>' . __('Base Plugin Documentation') . '</h2>';
        echo '<p>' . __('Here is the documentation for the Base Plugin.') . '</p>';
        echo '<p>' . __('To use and extend the Base Plugin in your own plugin') . ':</p>';

        // Example code to display in the documentation
        $code = "// Include the Base Plugin file
if(!class_exists('BasePlugin')){
    include_once(realpath(plugin_dir_path(__FILE__).'../nethttp.net-base-plugin/nethttp.net-base-plugin.php'));
}

//Output error if nethttp.net-base-plugin is not installed !
if(!class_exists('BasePlugin')){
    add_action('admin_notices', function(){
        echo '<div class=\"error\"><p>🙃 '.__('Yous should install and activate nethttp.net-base-plugin. You can find it on ').
        '<a href=\"https://github.com/yrbane/nethttp.net-base-plugin\">github</a>!</p></div>';
    });
    return;
}

//Create a class that extends BasePlugin class
class MyPlugin extends BasePlugin {
    // Your custom code here
}

// Instantiate the class passing plugin file path as parameter
new MyPlugin(__FILE__);

";

        // PHP code highlighting
        echo '<pre>';
        highlight_string('<?php ' . $code);
        echo '</pre>';

        echo '</div>';
    }

    /**
     * Add a GitHub link to the plugin's action links.
     *
     * @param array $links Action links.
     * @return array Modified action links.
     */
    public function addGitHubLink(array $links): array
    {
        $github_link = '<a href="' . esc_url($this->github_url) . '" target="_blank">GitHub</a>';
        array_push($links, $github_link);
        return $links;
    }

    /**
     * Load the functionality of your plugin.
     */
    protected function loadPluginFunctionality()
    {
        // Load your plugin functionality here
    }
}

$BasePlugin = new BasePlugin();
