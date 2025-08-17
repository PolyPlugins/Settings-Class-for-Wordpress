<?php

namespace PolyPlugins\Settings;

class Settings
{

  /**
	 * Full path and filename of reusable admin panel.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $admin_panel    Full path and filename of reusable admin panel.
	 */
	private $admin_panel;

  /**
	 * The slug but with _ instead of -
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $admin_panel_slug    The slug but with _ instead of -
	 */
	private $admin_panel_slug;

  /**
	 * The ID of the reusable admin panel.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $admin_panel_slug_id    The ID of the reusable admin panel.
	 */
	private $admin_panel_slug_id;

  /**
	 * The name used to uniquely identify this plugins options.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $admin_panel_settings_name    The name used to uniquely identify this plugins options.
	 */
	private $admin_panel_settings_name;

  /**
	 * Store settings related to the reusable admin panel and how other plugins are using it
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $admin_panel_settings    Reusable Admin Panel options array
	 */
	private $admin_panel_settings;

  /**
	 * Full path and filename of plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin    Full path and filename of plugin.
	 */
	private $plugin;
  
  /**
	 * Namespace of plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin    Full path and filename of plugin.
	 */
  private $namespace;

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_slug    The ID of this plugin.
	 */
	private $plugin_slug;

	/**
	 * The slug but with _ instead of -
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_slug_id    The slug but with _ instead of -
	 */
  private $plugin_slug_id;

	/**
	 * The plugin name
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    Name of the plugin
	 */
  private $plugin_name;

	/**
	 * The plugin menu name
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    Name of the menu for the plugin
	 */
  private $plugin_menu_name;

	/**
	 * The unique name for the plugins options.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $settings_name    The name used to uniquely identify this plugins options.
	 */
  private $settings_name;
  
	/**
	 * The plugin's options array
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $settings    The plugin's options array
	 */
  private $settings;

  
	/**
	 * The settings class configuration
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $fields    The settings class configuration
	 */
  private $config;

  
	/**
	 * The plugin's options fields
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $fields    The plugin's options fields
	 */
  private $fields;

  
	/**
	 * The plugin's version
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $fields    The plugin's version
	 */

  /**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param    string    $plugin         Full path and filename of plugin.
	 * @param    array     $config         The settings class configuration
	 * @param    array     $fields         The plugin's settings fields
	 */
	public function __construct($plugin, $namespace, $config, $fields) {
    // Reusable Admin Panel properties
		$this->admin_panel               = __FILE__;
    $this->admin_panel_slug          = dirname(plugin_basename($this->admin_panel));
    $this->admin_panel_slug_id       = str_replace('-', '_', $this->admin_panel_slug);
    $this->admin_panel_settings_name = $this->admin_panel_slug_id . '_settings';
    $this->admin_panel_settings      = get_option($this->admin_panel_settings_name);

    // Properties passed from the plugin using this class
		$this->plugin           = $plugin;
		$this->namespace        = $namespace;
    $this->plugin_slug      = dirname(plugin_basename($this->plugin));
    $this->plugin_slug_id   = str_replace('-', '_', $this->plugin_slug);
    $this->plugin_name      = __(mb_convert_case(str_replace('-', ' ', $this->plugin_slug), MB_CASE_TITLE), $this->plugin_slug);
    $this->plugin_menu_name = __($this->plugin_name, $this->plugin_slug);
    $this->settings_name    = $this->plugin_slug_id . '_' .  strtolower(str_replace('\\', '_', $this->namespace)) . '_settings';
    $this->settings         = get_option($this->settings_name);
    $this->config           = $config;
    $this->fields           = $fields;

    // Admin setup
    add_action('admin_init', array($this, 'admin_setup'));
	}
  
  /**
   * Initialize Reusable Admin Panel
   *
   * @return void
   */
  public function init()
  {
    add_action('admin_enqueue_scripts', array($this, 'admin_enqueue'));
    add_action('admin_menu', array($this, 'admin_menu'));
  }
  
  /**
   * Let's setup the plugin to use Reusable Admin Panel if it hasn't already been configured.
   * This only runs on admin as it's not needed for frontend.
   *
   * @return void
   */
  public function admin_setup() {
    $page = (isset($_GET['page'])) ? sanitize_text_field(wp_unslash($_GET['page'])) : '';

    // Let's only run this on the plugin's page to reduce resource usage
    if($page == $this->plugin_slug) {
      // Let's store the name of the plugin so we can display it in the list of plugins being used if someone attempts to uninstall Reusable Admin Panel
      if (!isset($this->admin_panel_settings[$this->plugin_slug_id])) {
        $this->admin_panel_settings[$this->plugin_slug_id]['name'] = $this->plugin_name;

        update_option($this->admin_panel_settings_name, $this->admin_panel_settings);
      }

      // Let's add the plugin settings if they don't exist
      if(!$this->settings) {
        add_option($this->settings_name);
      }
    }

    register_setting(
      $this->plugin_slug_id . '_option_group', // option_group
      $this->settings_name, // option_name
      array($this, 'save_settings_callback') // sanitize_callback
    );

    add_settings_section(
      $this->plugin_slug_id . '_setting_section', // id
      '', // title
      array(), // callback
      $this->plugin_slug . '-admin' // page
    );

    add_settings_field(
      $this->plugin_slug_id, // id
      '', // title
      array($this, 'settings_callback'), // callback
      $this->plugin_slug . '-admin', // page
      $this->plugin_slug_id . '_setting_section' // section
    );
  }
  
  /**
   * Enqueue all scripts and styles for Settings class
   *
   * @return void
   */
  public function admin_enqueue() {
    global $pagenow;

    $page = (isset($_GET['page'])) ? sanitize_text_field(wp_unslash($_GET['page'])) : '';

    // Let's only run this on the plugin's page to reduce the number of scripts that need to load and prevent conflicts.
    if($page == $this->plugin_slug) {
      // JS
      wp_enqueue_style('wp-color-picker');
      wp_enqueue_script($this->plugin_slug . '-settings', plugins_url('/js/settings.js', $this->admin_panel), array('jquery', 'wp-color-picker'), filemtime(plugin_dir_path($this->admin_panel) . '/js/settings.js'), true);
      
      // Styles
      wp_enqueue_style('bootstrap-icons', plugins_url('/css/bootstrap-icons.min.css', $this->admin_panel), array(), filemtime(plugin_dir_path($this->admin_panel) . '/css/bootstrap-icons.min.css'));
      wp_enqueue_style($this->plugin_slug . '-settings', plugins_url('/css/settings.css', $this->admin_panel), array(), filemtime(plugin_dir_path($this->admin_panel) . '/css/settings.css'));
      
      // Color variables or custom CSS/Colors
      if (!isset($this->config['css'])) {
        wp_enqueue_style($this->plugin_slug . '-settings-colors', plugins_url('/css/colors.css', $this->admin_panel), array(), filemtime(plugin_dir_path($this->admin_panel) . '/css/colors.css'));
      } else {
        wp_enqueue_style($this->plugin_slug . '-settings-custom', plugins_url($this->config['css'], $this->plugin), array(), filemtime(plugin_dir_path(dirname($this->plugin)) . dirname(plugin_basename($this->plugin)) . $this->config['css']));
      }

      // Custom JS
      if (isset($this->config['js'])) {
        wp_enqueue_script($this->plugin_slug . '-settings-custom', plugins_url($this->config['js'], $this->plugin), array('jquery'), filemtime(plugin_dir_path(dirname($this->plugin)) . dirname(plugin_basename($this->plugin))  . $this->config['js']), true);
      }
      
      // Bootstrap
      wp_enqueue_style('bootstrap', plugins_url('/css/bootstrap-wrapper.min.css', $this->admin_panel), array(), filemtime(plugin_dir_path($this->admin_panel) . '/css/bootstrap-wrapper.min.css'));
      wp_enqueue_script('bootstrap', plugins_url('/js/bootstrap.min.js', $this->admin_panel), array(), filemtime(plugin_dir_path($this->admin_panel)  . '/js/bootstrap.min.js'), true);
      
      // Validator
      wp_enqueue_script('validator', plugins_url('/js/validator.min.js', $this->admin_panel), array(), filemtime(plugin_dir_path($this->admin_panel)  . '/js/validator.min.js'), true);
    }
    
    // Check that no plugins are using Reusable Admin Panel before allowing deactivation
    if ($pagenow === 'plugins.php') {
      if (!$this->admin_panel_settings) return;
      
      $used_by = '';

      foreach($this->admin_panel_settings as $admin_panel_setting) {
        if (!$used_by) {
          $used_by .= $admin_panel_setting['name'];
        } else {
          $used_by .= "<br />" . $admin_panel_setting['name'];
        }
      }

      // Script to check if plugin can be deactivated
      wp_enqueue_script('deactivation-check', plugins_url('/js/deactivation-check.js', $this->admin_panel), array(), filemtime(plugin_dir_path($this->admin_panel)  . '/js/deactivation-check.js'), true);
      wp_localize_script('deactivation-check', 'deactivation_check', array('used_by' => $used_by));
    }

    if ($pagenow === 'plugins.php' || $page == $this->plugin_slug) {
      // Sweet Alert to give a nice alert for the admin
      wp_enqueue_style('sweetalert2', plugins_url('/css/sweetalert2.min.css', $this->admin_panel), array(), filemtime(plugin_dir_path($this->admin_panel) . '/css/sweetalert2.min.css'));
      wp_enqueue_script('sweetalert2', plugins_url('/js/sweetalert2.min.js', $this->admin_panel), array(), filemtime(plugin_dir_path($this->admin_panel)  . '/js/sweetalert2.min.js'), true);
    }
  }

  /**
   * Adds a link to manage settings in the admin menu
   *
   * @return void
   */
  public function admin_menu() {
    add_submenu_page(
      $this->config['page'], // parent_slug
      $this->plugin_name . ' Settings', // page_title
      $this->plugin_menu_name, // menu_title
      $this->config['capability'], // capability
      $this->plugin_slug, // menu_slug
      array($this, 'create_admin_page'), // function
      $this->config['position']
    );
  }

  /**
	 * Builds the admin page
	 *
	 * @return void
	 */
	public function create_admin_page()
  {
  ?>
    <div class="wrap">
      <h2><?php echo esc_html($this->plugin_name . ' Settings'); ?></h2>
      <p></p>

      <form class="reusable-admin-panel-form" method="post" action="options.php">
        <?php
        settings_fields($this->plugin_slug_id . '_option_group');
        do_settings_sections($this->plugin_slug . '-admin');
        submit_button();
        ?>
      </form>
    </div>
  <?php
  }
  
  /**
   * Using the Settings API we trigger a custom method used for building the layout of the form
   *
   * @return void
   */
  public function settings_callback()
  {
    $get_fields  = $this->fields;
    $sections    = array_keys($get_fields);
    ?>
    
    <div class="bootstrap-wrapper">

      <!-- Display a loader as a placeholder until page is loaded -->
      <div class="load-settings d-flex justify-content-center m-3">
        <div class="spinner-border" style="width: 3rem; height: 3rem;" role="status">
          <span class="sr-only">Loading...</span>
        </div>
      </div>

      <!-- Settings are hidden until page loaded to prevent layout shifting -->
      <div class="row settings-container align-items-start" style="display: none;">

        <!-- Navigation -->
        <div class="col-lg-2 col-md-12 tabs">
          <nav class="nav flex-column">
            <img src="<?php echo isset($this->config['logo']) ? esc_url(plugins_url($this->config['logo'], $this->plugin)) : esc_url(plugins_url('/img/logo.png', $this->admin_panel)); ?>" class="nav-logo" />
          </nav>
          <nav class="nav flex-column">
            <?php foreach ($sections as $section) : ?>
              <?php 
                $icon      = isset($get_fields[$section]['icon']) ? $get_fields[$section]['icon'] : '';
                $label     = str_replace('-', ' ', $section);
                $has_icons = false;
              ?>
              <a class="nav-link<?php echo ($sections[0] == $section) ? ' active' : ''; ?>" 
                href="#<?php echo esc_attr($section); ?>" 
                selected-section="<?php echo esc_attr($section); ?>">
                <?php if ($icon) : ?>
                  <?php $has_icons = true; ?>
                  <i class="bi bi-<?php echo esc_attr($icon); ?>"></i>
                <?php endif; ?>
                <?php echo esc_html($label); ?>
              </a>
            <?php endforeach; ?>
            <?php if (isset($this->config['support'])) : ?>
              <a class="nav-link" href="<?php echo esc_url($this->config['support']); ?>" target="_blank">
                <?php if ($has_icons) : ?>
                  <i class="bi bi-chat-left-dots"></i>
                <?php endif; ?>
                Support
              </a>
            <?php endif; ?>
          </nav>
        </div>
        
        <!-- Fields -->
        <?php foreach($get_fields as $section => $section_data) : ?>
          <?php $fields = isset($section_data['fields']) ? $section_data['fields'] : array(); ?>
          <div class="col-lg-10 col-md-12 options"<?php echo ($sections[0] != $section) ? ' style="display: none;"' : ''; ?> section="<?php echo esc_attr($section) ?>">
            <h2>
              <?php if (!empty($section_data['icon'])) : ?>
                <i class="<?php echo esc_attr($section_data['icon']); ?>"></i>
              <?php endif; ?>
              <?php echo esc_html($section) . ' Settings'; ?>
            </h2>
            <div class="fields">
              <?php foreach($fields as $field) : ?>
                <?php $field['section'] = $section; ?>
                <?php $this->add_field($field); ?>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endforeach; ?>

        <div class="col-lg-2 col-md-2 helper-sidebar" style="display: none;">
          <h2>Help<span class="helper-close"><a href="javascript:void(0)"><i class="bi bi-x-circle-fill"></i></a></span></h2>
          <div class="helper-text"></div>
        </div>
        
      </div>
    </div>
    <p class="credit">Built with <a href="https://wordpress.org/plugins/reusable-admin-panel/">Reusable Admin Panel</a><span>
  <?php
  }
  
  /**
   * This is the callback used in the Settings API that we piggyback off of to save the settings array
   *
   * @param  mixed $input    The submitted fields from the settings form
   * @return array $settings The settings to be saved, returns old settings if validation fails
   */
  public function save_settings_callback($input)
  {
    $validated    = true;
    $new_settings = array();
    $old_settings = $this->settings;

    // If empty return old settings
    if (empty($input)) return $old_settings;

    foreach($input as $section => $settings) {
      foreach($settings as $name => $option) {
        $type = key($option);
        $value = $this->sanitize($type, $option[$type]);

        if ($value !== false) {
          // Sanitization succeeded, add option to settings array
          $new_settings[$section][$name] = array(
            'value' => $value,
            'type' => $type
          );
        } else {
          // Sanitization failed
          $validated = false;
          // No need to continue loop since validation failed
          break;
        }

        // No need to continue loop if validation fails
        if (!$validated) break;
      }
    }

    // Only save settings if sanitizing was successful
    return ($validated) ? $new_settings : $old_settings;
  }
  
  /**
   * Sanitize settings
   *
   * @param  string       $type
   * @param  mixed        $value
   * @return string|array $sanitized_value 
   */
  public function sanitize($type, $value) {
    // Need to get previous settings and pass them back
    $sanitize_callbacks = array(
      'switch'          => 'sanitize_text_field',
      'button'          => 'sanitize_text_field',
      'text'            => 'sanitize_text_field',
      'textarea'        => 'sanitize_text_field',
      'email'           => 'sanitize_email',
      'url'             => 'sanitize_url',
      'password'        => 'sanitize_text_field',
      'number'          => 'intval',
      'dropdown'        => 'sanitize_text_field',
      'dropdown_toggle' => 'sanitize_text_field',
      'date'            => 'sanitize_text_field',
      'time'            => 'sanitize_text_field',
      'color'           => 'sanitize_hex_color',
    );

    if (key_exists($type, $sanitize_callbacks)) {
      foreach($sanitize_callbacks as $callback_type => $callback) {
        if ($type === $callback_type) {
          $sanitized_value = call_user_func( $callback, $value );
        }
      }
    }

    // Return false if it didn't detect any type
    return (isset($sanitized_value)) ? $sanitized_value : false;
  }
  
  /**
   * Passes the field array to the appropriate callback to properly handle displaying fields
   *
   * @param  array $field
   * @return void
   */
  public function add_field($field) {
    if (!isset($field['type'])) {
      echo '<div style="color: var(--settings-validation-bg); margin-bottom: 20px;">Error: The "type" parameter was not provided for<br />' . esc_html(print_r($field, true)) . '</div>';
    } else if (!isset($field['name'])) {
      echo '<div style="color: var(--settings-validation-bg); margin-bottom: 20px;">Error: The "name" parameter was not provided for<br />' . esc_html(print_r($field, true)) . '</div>';
    }

    call_user_func( array( $this, 'callback_' . $field['type'] ), $field );
  }
  
  /**
   * A custom callback built to handle displaying of switch fields
   *
   * @param  array $field
   * @return void
   */
  public function callback_switch($field) {
    $settings = $this->settings;
    $section  = $field['section'];
    $name     = sanitize_title($field['name']);
    $label    = isset($field['label']) & $field['label'] ? $field['label'] : $field['name'];
    $id       = $section . '-' . $name;
    $class    = $field['class'] ? sanitize_title($field['class']) : '';
    $type     = $field['type'];
    $default  = ($field['default']) ? $field['default'] : '';
    $checked  = (!empty($settings[$section][$name]['value'])) ? ' checked' : $default;
    ?>
    <div class="form-group">
      <div class="form-check form-switch">
        <input type="checkbox" class="form-check-input <?php echo esc_attr($class); ?>" name="<?php echo esc_attr($this->settings_name) . '[' . esc_attr($section) . '][' . esc_attr($name) . '][' . esc_attr($type) . ']'; ?>" id="<?php echo esc_attr($id); ?>"<?php echo esc_attr($checked) ?>>
        <label class="form-check-label" for="<?php echo esc_attr($id); ?>"><?php echo esc_html($label); ?></label>
      </div>
    </div>
    <?php
  }
  
  /**
   * A custom callback built to handle displaying of button fields
   *
   * @param  array $field
   * @return void
   */
  public function callback_button($field) {
    $section = $field['section'];
    $name    = sanitize_title($field['name']);
    $label   = isset($field['label']) && $field['label'] ? $field['label'] : $field['name'];
    $id      = $section . '-' . $name;
    $section = $field['section'];
    $class   = $field['class'] ? sanitize_title($field['class']) : '';
    ?>
    <div class="input-group">
      <label class="input-group-text" for="<?php echo esc_attr($id); ?>"><?php echo esc_html($label); ?></label>
      <?php foreach ($field['data'] as $button) : ?>
        <a href="<?php echo $button['url'] ? esc_url($button['url']) : 'javascript:void(0);' ?>" class="btn btn-<?php echo $button['class'] ? esc_attr($button['class']) : 'primary'; ?> <?php echo esc_attr($class); ?>" <?php echo $section && $button['title'] ? 'id="' . esc_attr($section) . '-' . esc_attr(sanitize_title($button['title'])) . '"' : '' ?> target="<?php echo $button['target'] ? esc_attr($button['target']) : ''; ?>"><?php echo $button['title'] ? esc_attr($button['title']) : ''; ?></a>
      <?php endforeach; ?>
    </div>
    <?php
  }

  /**
   * A custom callback built to handle displaying of text fields
   *
   * @param  array $field
   * @return void
   */
  public function callback_text($field) {
    $settings    = $this->settings;
    $section     = $field['section'];
    $name        = sanitize_title($field['name']);
    $label       = isset($field['label']) && $field['label'] ? $field['label'] : $field['name'];
    $placeholder = isset($field['placeholder']) && $field['placeholder'] ? $field['placeholder'] : $field['label'];
    $id          = $section . '-' . $name;
    $class       = $field['class'] ? sanitize_title($field['class']) : '';
    $type        = $field['type'];
    $default     = ($field['default']) ? $field['default'] : '';
    $required    = (isset($field['required']) && $field['required']) ? ' required' : '';
    $value       = (!empty($settings[$section][$name]['value'])) ? $settings[$section][$name]['value'] : $default;
    ?>
    <div class="input-group">
      <label class="input-group-text" for="<?php echo esc_attr($id); ?>"><?php echo esc_html($label); ?></label>
      <input type="text" class="form-control <?php echo esc_attr($class); ?>" name="<?php echo esc_attr($this->settings_name) . '[' . esc_attr($section) . '][' . esc_attr($name) . '][' . esc_attr($type) . ']'; ?>" id="<?php echo esc_attr($id); ?>" placeholder="<?php echo esc_attr($placeholder); ?>" value="<?php echo esc_html($value); ?>"<?php echo esc_attr($required); ?>>
      
      <!-- Display a info button which displays a toast when clicked -->
      <?php $this->helper($field['help']); ?>
    </div>
    <?php
  }

  /**
   * A custom callback built to handle displaying of textarea fields
   *
   * @param  array $field
   * @return void
   */
  public function callback_textarea($field) {
    $settings    = $this->settings;
    $section     = $field['section'];
    $name        = sanitize_title($field['name']);
    $rows        = (isset($field['rows']) && is_numeric($field['rows'])) ? $field['rows'] : '';
    $label       = isset($field['label']) && $field['label'] ? $field['label'] : $field['name'];
    $placeholder = isset($field['placeholder']) && $field['placeholder'] ? $field['placeholder'] : $field['label'];
    $id          = $section . '-' . $name;
    $class       = $field['class'] ? sanitize_title($field['class']) : '';
    $type        = $field['type'];
    $default     = ($field['default']) ? $field['default'] : '';
    $required    = (isset($field['required']) && $field['required']) ? ' required' : '';
    $value       = (!empty($settings[$section][$name]['value'])) ? $settings[$section][$name]['value'] : $default;
    ?>
    <div class="input-group">
      <label class="input-group-text" for="<?php echo esc_attr($id); ?>"><?php echo esc_html($label); ?></label>
      <textarea class="form-control <?php echo esc_attr($class); ?>" name="<?php echo esc_attr($this->settings_name) . '[' . esc_attr($section) . '][' . esc_attr($name) . '][' . esc_attr($type) . ']'; ?>" id="<?php echo esc_attr($id); ?>" placeholder="<?php echo esc_attr($placeholder); ?>"<?php echo esc_attr($required); ?><?php echo $rows ? ' rows="' . esc_html($rows) . '"' : ''; ?>><?php echo esc_html($value); ?></textarea>
      
      <!-- Display a info button which displays a toast when clicked -->
      <?php $this->helper($field['help']); ?>
    </div>
    <?php
  }

  /**
   * A custom callback built to handle displaying of email fields
   *
   * @param  array $field
   * @return void
   */
  public function callback_email($field) {
    $settings    = $this->settings;
    $section     = $field['section'];
    $name        = sanitize_title($field['name']);
    $label       = isset($field['label']) && $field['label'] ? $field['label'] : $field['name'];
    $placeholder = isset($field['placeholder']) && $field['placeholder'] ? $field['placeholder'] : $field['label'];
    $id          = $section . '-' . $name;
    $class       = $field['class'] ? sanitize_title($field['class']) : '';
    $type        = $field['type'];
    $default     = ($field['default']) ? $field['default'] : '';
    $required    = (isset($field['required']) && $field['required']) ? ' required' : '';
    $value       = (!empty($settings[$section][$name]['value'])) ? $settings[$section][$name]['value'] : $default;
    ?>
    <div class="input-group">
      <label class="input-group-text" for="<?php echo esc_attr($id); ?>"><?php echo esc_html($label); ?></label>
      <input type="email" class="form-control <?php echo esc_attr($class); ?>" name="<?php echo esc_attr($this->settings_name) . '[' . esc_attr($section) . '][' . esc_attr($name) . '][' . esc_attr($type) . ']'; ?>" id="<?php echo esc_attr($id); ?>" placeholder="<?php echo esc_attr($placeholder); ?>" value="<?php echo esc_attr($value); ?>"<?php echo esc_attr($required); ?>>
      
      <!-- Display a info button which displays a toast when clicked -->
      <?php $this->helper($field['help']); ?>
    </div>
    <?php
  }

  /**
   * A custom callback built to handle displaying of url fields
   *
   * @param  array $field
   * @return void
   */
  public function callback_url($field) {
    $settings    = $this->settings;
    $section     = $field['section'];
    $name        = sanitize_title($field['name']);
    $label       = isset($field['label']) && $field['label'] ? $field['label'] : $field['name'];
    $placeholder = isset($field['placeholder']) && $field['placeholder'] ? $field['placeholder'] : $field['label'];
    $id          = $section . '-' . $name;
    $class       = $field['class'] ? sanitize_title($field['class']) : '';
    $type        = $field['type'];
    $default     = ($field['default']) ? $field['default'] : '';
    $required    = (isset($field['required']) && $field['required']) ? ' required' : '';
    $value       = (!empty($settings[$section][$name]['value'])) ? $settings[$section][$name]['value'] : $default;
    ?>
    <div class="input-group">
      <label class="input-group-text" for="<?php echo esc_attr($id); ?>"><?php echo esc_html($label); ?></label>
      <input type="url" class="form-control <?php echo esc_attr($class); ?>" name="<?php echo esc_attr($this->settings_name) . '[' . esc_attr($section) . '][' . esc_attr($name) . '][' . esc_attr($type) . ']'; ?>" id="<?php echo esc_attr($id); ?>" placeholder="<?php echo $placeholder ? esc_html($placeholder) : 'https://www.example.com'; ?>" value="<?php echo esc_attr($value); ?>"<?php echo esc_attr($required); ?>>
      
      <!-- Display a info button which displays a toast when clicked -->
      <?php $this->helper($field['help']); ?>
    </div>
    <?php
  }

  /**
   * A custom callback built to handle displaying of password fields
   *
   * @param  array $field
   * @return void
   */
  public function callback_password($field) {
    $settings    = $this->settings;
    $section     = $field['section'];
    $name        = sanitize_title($field['name']);
    $label       = isset($field['label']) && $field['label'] ? $field['label'] : $field['name'];
    $placeholder = isset($field['placeholder']) && $field['placeholder'] ? $field['placeholder'] : $field['label'];
    $id          = $section . '-' . $name;
    $class       = $field['class'] ? sanitize_title($field['class']) : '';
    $type        = $field['type'];
    $default     = ($field['default']) ? $field['default'] : '';
    $required    = (isset($field['required']) && $field['required'] ) ? ' required' : '';
    $value       = (!empty($settings[$section][$name]['value'])) ? $settings[$section][$name]['value'] : $default;
    ?>
    <div class="input-group">
      <label class="input-group-text" for="<?php echo esc_attr($id); ?>"><?php echo esc_html($label); ?></label>
      <input type="password" class="form-control <?php echo esc_attr($class); ?>" name="<?php echo esc_attr($this->settings_name) . '[' . esc_attr($section) . '][' . esc_attr($name) . '][' . esc_attr($type) . ']'; ?>" id="<?php echo esc_attr($id); ?>" placeholder="<?php echo esc_attr($placeholder); ?>" value="<?php echo esc_attr($value); ?>"<?php echo esc_attr($required); ?>>
      
      <!-- Display a info button which displays a toast when clicked -->
      <?php $this->helper($field['help']); ?>
    </div>
    <?php
  }

  /**
   * A custom callback built to handle displaying of number fields
   *
   * @param  array $field
   * @return void
   */
  public function callback_number($field) {
    $settings    = $this->settings;
    $section     = $field['section'];
    $name        = sanitize_title($field['name']);
    $min         = (isset($field['min']) && is_numeric($field['min'])) ? $field['min'] : '';
    $max         = (isset($field['max']) && is_numeric($field['max'])) ? $field['max'] : '';
    $step        = (isset($field['step']) && is_numeric($field['step'])) ? $field['step'] : '';
    $label       = isset($field['label']) && $field['label'] ? $field['label'] : $field['name'];
    $placeholder = isset($field['placeholder']) && $field['placeholder'] ? $field['placeholder'] : $field['label'];
    $id          = $section . '-' . $name;
    $class       = $field['class'] ? sanitize_title($field['class']) : '';
    $type        = $field['type'];
    $default     = ($field['default']) ? $field['default'] : '';
    $required    = (isset($field['required']) && $field['required']) ? ' required' : '';
    $value       = (!empty($settings[$section][$name]['value'])) ? $settings[$section][$name]['value'] : $default;
    ?>
    <div class="input-group">
      <label class="input-group-text" for="<?php echo esc_attr($id); ?>"><?php echo esc_html($label); ?></label>
      <input type="number" class="form-control <?php echo esc_attr($class); ?>" name="<?php echo esc_attr($this->settings_name) . '[' . esc_attr($section) . '][' . esc_attr($name) . '][' . esc_attr($type) . ']'; ?>" id="<?php echo esc_attr($id); ?>" placeholder="<?php echo esc_attr($placeholder); ?>" value="<?php echo esc_attr($value); ?>"<?php echo esc_attr($required); ?><?php echo $step ? ' step="' . esc_html($step) . '"' : ''; ?><?php echo $min ? ' min="' . esc_html($min) . '"' : ''; ?><?php echo $max ? ' max="' . esc_html($max) . '"' : ''; ?>>
      
      <!-- Display a info button which displays a toast when clicked -->
      <?php $this->helper($field['help']); ?>
    </div>
    <?php
  }

  /**
   * A custom callback built to handle displaying of dropdowns
   *
   * @param  array $field
   * @return void
   */
  public function callback_dropdown($field) {
    $settings = $this->settings;
    $section  = $field['section'];
    $name     = sanitize_title($field['name']);
    $label    = isset($field['label']) && $field['label'] ? $field['label'] : $field['name'];
    $id       = $section . '-' . $name;
    $class    = $field['class'] ? sanitize_title($field['class']) : '';
    $type     = $field['type'];
    $options  = $field['options'];
    $default  = $field['default'] ? $field['default'] : '';
    $disabled = $field['disabled'] ? $field['disabled'] : '';
    $required = (isset($field['required']) && $field['required']) ? ' required' : '';
    $value    = (!empty($settings[$section][$name]['value'])) ? $settings[$section][$name]['value'] : $default;
    ?>
    <div class="input-group">
      <label class="input-group-text" for="<?php echo esc_attr($id); ?>"><?php echo esc_html($label); ?></label>
      <select class="form-select <?php echo esc_attr($class); ?>" name="<?php echo esc_attr($this->settings_name) . '[' . esc_attr($section) . '][' . esc_attr($name) . '][' . esc_attr($type) . ']'; ?>" id="<?php echo esc_attr($id); ?>" aria-label="<?php echo esc_attr($label); ?>"<?php echo esc_attr($required); ?><?php echo $disabled ? " disabled" : ''; ?>>
        <option value="" disabled selected>Select Option</option>
        <?php foreach ($options as $option) : ?>
          <option value="<?php echo esc_attr($option); ?>" <?php echo ($option == $value) ? ' selected' : ''; ?>><?php echo esc_html($option); ?></option>
        <?php endforeach; ?>
      </select>
      
      <!-- Display a info button which displays a toast when clicked -->
      <?php $this->helper($field['help']); ?>
    </div>
    <?php
  }

  /**
   * A custom callback built to handle displaying of dropdown toggles
   *
   * @param  array $field
   * @return void
   */
  public function callback_dropdown_toggle($field) {
    $settings = $this->settings;
    $section  = $field['section'];
    $name     = sanitize_title($field['name']);
    $label    = isset($field['label']) && $field['label'] ? $field['label'] : $field['name'];
    $id       = $section . '-' . $name;
    $class    = $field['class'] ? sanitize_title($field['class']) : '';
    $type     = $field['type'];
    $options  = $field['options'];
    $default  = ($field['default']) ? $field['default'] : '';
    $required = (isset($field['required']) && $field['required']) ? ' required' : '';
    $value    = (!empty($settings[$section][$name]['value'])) ? $settings[$section][$name]['value'] : $default;
    ?>
    <hr class="field-separator" />
    <div class="toggle">
      <div class="input-group">
        <label class="input-group-text" for="<?php echo esc_attr($id); ?>"><?php echo esc_html($label); ?></label>
        <select class="form-select toggle-select <?php echo esc_attr($class); ?>" name="<?php echo esc_attr($this->settings_name) . '[' . esc_attr($section) . '][' . esc_attr($name) . '][' . esc_attr($type) . ']'; ?>" id="<?php echo esc_attr($id); ?>" aria-label="<?php echo esc_attr($label); ?>"<?php echo esc_attr($required); ?>>
          <option value="" disabled selected><?php echo esc_html($label); ?></option>
          <?php foreach ($options as $option => $option_field) : ?>
            <option value="<?php echo esc_attr($option); ?>" <?php echo ($option == $value) ? ' selected' : ''; ?>><?php echo esc_html($option); ?></option>
          <?php endforeach; ?>
        </select>
        
        <!-- Display a info button which displays a toast when clicked -->
        <?php $this->helper($field['help']); ?>
      </div>
      <?php foreach ($options as $option => $option_field) : ?>
        <?php $option_class = isset($option_field['section']) ? sanitize_text_field($option_field['section']) : ''; ?>
        <?php $option_type = isset($option_field['type']) ? sanitize_text_field($option_field['type']) : ''; ?>
        <div class="input-group togglable <?php echo esc_attr(strtolower($option)); ?> <?php echo esc_attr($option_class); ?> ">
          <?php call_user_func( array( $this, 'callback_' . $option_type ), $option_field ); ?>
        </div>
      <?php endforeach; ?>
    </div>
    <hr class="field-separator" />
    <?php
  }

  /**
   * A custom callback built to handle displaying of date
   *
   * @param  array $field
   * @return void
   */
  public function callback_date($field) {
    $settings    = $this->settings;
    $section     = $field['section'];
    $name        = sanitize_title($field['name']);
    $label       = isset($field['label']) && $field['label'] ? $field['label'] : $field['name'];
    $placeholder = isset($field['placeholder']) && $field['placeholder'] ? $field['placeholder'] : $field['label'];
    $id          = $section . '-' . $name;
    $class       = $field['class'] ? sanitize_title($field['class']) : '';
    $type        = $field['type'];
    $default     = ($field['default']) ? $field['default'] : '';
    $required    = (isset($field['required']) && $field['required']) ? ' required' : '';
    $value       = (!empty($settings[$section][$name]['value'])) ? $settings[$section][$name]['value'] : $default;
    ?>
    <div class="input-group">
      <label class="input-group-text" for="<?php echo esc_attr($id); ?>"><?php echo esc_html($label); ?></label>
      <input type="date" class="form-control <?php echo esc_attr($class); ?>" name="<?php echo esc_attr($this->settings_name) . '[' . esc_attr($section) . '][' . esc_attr($name) . '][' . esc_attr($type) . ']'; ?>" id="<?php echo esc_attr($id); ?>" placeholder="<?php echo esc_attr($placeholder); ?>" value="<?php echo esc_attr($value); ?>"<?php echo esc_attr($required); ?>>
      
      <!-- Display a info button which displays a toast when clicked -->
      <?php $this->helper($field['help']); ?>
    </div>
    <?php
  }

  /**
   * A custom callback built to handle displaying of time
   *
   * @param  array $field
   * @return void
   */
  public function callback_time($field) {
    $settings    = $this->settings;
    $section     = $field['section'];
    $name        = sanitize_title($field['name']);
    $label       = isset($field['label']) && $field['label'] ? $field['label'] : $field['name'];
    $placeholder = isset($field['placeholder']) && $field['placeholder'] ? $field['placeholder'] : $field['label'];
    $id          = $section . '-' . $name;
    $class       = $field['class'] ? sanitize_title($field['class']) : '';
    $type        = $field['type'];
    $default     = ($field['default']) ? $field['default'] : '';
    $required    = (isset($field['required']) && $field['required']) ? ' required' : '';
    $value       = (!empty($settings[$section][$name]['value'])) ? $settings[$section][$name]['value'] : $default;
    ?>
    <div class="input-group">
      <label class="input-group-text" for="<?php echo esc_attr($id); ?>"><?php echo esc_html($label); ?></label>
      <input type="time" class="form-control <?php echo esc_attr($class); ?>" name="<?php echo esc_attr($this->settings_name) . '[' . esc_attr($section) . '][' . esc_attr($name) . '][' . esc_attr($type) . ']'; ?>" id="<?php echo esc_attr($id); ?>" placeholder="<?php echo esc_attr($placeholder); ?>" value="<?php echo esc_attr($value); ?>"<?php echo esc_attr($required); ?>>
      
      <!-- Display a info button which displays a toast when clicked -->
      <?php $this->helper($field['help']); ?>
    </div>
    <?php
  }

  /**
   * A custom callback built to handle color picker
   *
   * @param  array $field
   * @return void
   */
  public function callback_color($field) {
    $settings = $this->settings;
    $section  = $field['section'];
    $name     = sanitize_title($field['name']);
    $label    = isset($field['label']) && $field['label'] ? $field['label'] : $field['name'];
    $id       = $section . '-' . $name;
    $class    = $field['class'] ? sanitize_title($field['class']) : '';
    $type     = $field['type'];
    $default  = ($field['default']) ? $field['default'] : '';
    $required = (isset($field['required']) && $field['required']) ? ' required' : '';
    $value    = (!empty($settings[$section][$name]['value'])) ? $settings[$section][$name]['value'] : $default;
    ?>
    <div class="input-group">
      <label class="input-group-text" for="<?php echo esc_attr($id); ?>"><?php echo esc_html($label); ?></label>
      <input type="text" class="form-control color-picker <?php echo esc_attr($class); ?>" id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr($this->settings_name) . '[' . esc_attr($section) . '][' . esc_attr($name) . '][' . esc_attr($type) . ']'; ?>" value="<?php echo esc_attr($value); ?>" data-default-color="<?php echo esc_attr($default); ?>"<?php echo esc_attr($required); ?>>
      
      <!-- Display a info button which displays a toast when clicked -->
      <?php $this->helper($field['help']); ?>
    </div>
    <?php
  }

  /**
   * A custom callback built to handle error messages
   *
   * @param  array $field
   * @return void
   */
  public function callback_error($field) {
    $message = $field['message'] ? sanitize_text_field($field['message']) : '';
    $color   = $field['color'] ? sanitize_text_field($field['color']) : '';
    $class   = $field['class'] ? sanitize_title($field['class']) : '';
    ?>
    <div class="input-group">
      <p class="<?php echo esc_attr($class); ?>" style="color: <?php echo esc_attr($color); ?>;"><?php echo esc_attr($message); ?></p>
    </div>
    <?php
  }

  /** 
   * Adds a sidebar for helpers
   *
   * @param  string $help The message to be displayed in the helper sidebar.
   * @return void
   */
  public function helper($help) {
    // If no helper do nothing
    if (empty($help)) return; ?>

    <!-- Add info button -->
    <div class="info d-flex align-items-center justify-content-center">
      <a href="javascript:void(0)" class="helper-icon" tabindex="-1"><i class="bi bi-info-circle-fill"></i></a>
    </div>
    
    <!-- Queue Helper -->
    <div class="helper-placeholder" style="display: none;">
      <?php echo wp_kses_post($help); ?>
    </div>
    <?php
  }

  /**
   * Get option from settings array
   *
   * @param  mixed $section      Section of setting
   * @param  mixed $option       Get option of the previously specified section
   * @return mixed $option_value Returns the value of the option
   */
  public function get_option($section, $option) {
    if (!empty($this->settings[$section][$option]['value'])) {
      $option_value = $this->settings[$section][$option]['value'];
    } else {
      $option_value = '';
    }
    
    return $option_value;
  }

}