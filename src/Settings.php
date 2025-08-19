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
	 * Full path and filename of plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin    Full path and filename of plugin.
	 */
	private $plugin;

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
	public function __construct($plugin, $config, $fields) {
    // Reusable Admin Panel properties
		$this->admin_panel      = __FILE__;

    // Properties passed from the plugin using this class
		$this->plugin           = sanitize_text_field($plugin);
    $this->plugin_slug      = dirname(plugin_basename($this->plugin));
    $this->plugin_slug_id   = str_replace('-', '_', $this->plugin_slug);
    $this->plugin_name      = isset($config['name']) ? sanitize_text_field($config['name']) : mb_convert_case(str_replace('-', ' ', $this->plugin_slug), MB_CASE_TITLE);
    $this->plugin_menu_name = isset($config['menu_name']) ? sanitize_text_field($config['menu_name']) : $this->plugin_name;
    $this->settings_name    = isset($config['settings_name']) ? sanitize_text_field($config['settings_name']) : strtolower(str_replace(' ', '_', $this->plugin_name) . '_settings');
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
  public function init() {
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
      $template = isset($this->config['template']) ? $this->config['template'] : 'default';

      // JS
      wp_enqueue_style('wp-color-picker');
      wp_enqueue_script($this->plugin_slug . '-settings', plugins_url('/templates/' . $template . '/settings.js', $this->admin_panel), array('jquery', 'wp-color-picker'), filemtime(plugin_dir_path($this->admin_panel) . '/js/settings.js'), true);
      
      // Styles
      wp_enqueue_style('bootstrap-icons', plugins_url('/css/bootstrap-icons.min.css', $this->admin_panel), array(), filemtime(plugin_dir_path($this->admin_panel) . '/css/bootstrap-icons.min.css'));
      wp_enqueue_style($this->plugin_slug . '-settings', plugins_url('/templates/' . $template . '/style.css', $this->admin_panel), array(), filemtime(plugin_dir_path($this->admin_panel) . '/css/settings.css'));
      
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
	public function create_admin_page() {
  ?>
    <div class="wrap">
      <h2><?php echo esc_html($this->plugin_name . ' Settings'); ?></h2>
      <p></p>

      <form class="reusable-admin-panel-form" method="post" action="options.php">
        <?php
        settings_fields($this->plugin_slug_id . '_option_group');
        do_settings_sections($this->plugin_slug . '-admin');
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
  public function settings_callback() {
    $get_fields  = $this->fields;
    $sections    = array_keys($get_fields);
    $template    = isset($this->config['template']) ? sanitize_key($this->config['template']) : 'default';

    ob_start();

    include plugin_dir_path($this->admin_panel) . '/templates/' . $template . '/' . 'index.php';

    echo ob_get_clean();
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
    $settings    = $this->settings;
    $section     = $field['section'];
    $name        = sanitize_title($field['name']);
    $label       = isset($field['label']) && $field['label'] ? $field['label'] : $field['name'];
    $description = isset($field['description']) && $field['description'] ? $field['description'] : '';
    $id          = $section . '-' . $name;
    $class       = $field['class'] ? sanitize_title($field['class']) : '';
    $type        = $field['type'];
    $default     = ($field['default']) ? $field['default'] : '';
    $checked     = (!empty($settings[$section][$name]['value'])) ? ' checked' : $default;
    ?>
    <div class="field-container">
      <label for="<?php echo esc_attr($id); ?>"><?php echo esc_html($label); ?></label>
      <?php if ($description) : ?>
        <p class="description"><?php echo esc_html($description); ?></p>
      <?php endif; ?>
      <div class="form-check form-switch">
        <input class="form-check-input <?php echo esc_attr($class); ?>" name="<?php echo esc_attr($this->settings_name) . '[' . esc_attr($section) . '][' . esc_attr($name) . '][' . esc_attr($type) . ']'; ?>" type="checkbox" role="switch" id="<?php echo esc_attr($id); ?>"<?php echo esc_attr($checked) ?>>
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
    $section     = $field['section'];
    $name        = sanitize_title($field['name']);
    $label       = isset($field['label']) && $field['label'] ? $field['label'] : $field['name'];
    $description = isset($field['description']) && $field['description'] ? $field['description'] : '';
    $id          = $section . '-' . $name;
    $section     = $field['section'];
    $class       = $field['class'] ? sanitize_title($field['class']) : '';
    ?>
    <div class="field-container">
      <label class="field-container-text" for="<?php echo esc_attr($id); ?>"><?php echo esc_html($label); ?></label>

      <?php if ($description) : ?>
        <p class="description"><?php echo esc_html($description); ?></p>
      <?php endif; ?>

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
    $description = isset($field['description']) && $field['description'] ? $field['description'] : '';
    $placeholder = isset($field['placeholder']) && $field['placeholder'] ? $field['placeholder'] : $field['label'];
    $id          = $section . '-' . $name;
    $class       = $field['class'] ? sanitize_title($field['class']) : '';
    $type        = $field['type'];
    $default     = ($field['default']) ? $field['default'] : '';
    $required    = (isset($field['required']) && $field['required']) ? ' required' : '';
    $value       = (!empty($settings[$section][$name]['value'])) ? $settings[$section][$name]['value'] : $default;
    ?>
    <div class="field-container">
      <label for="<?php echo esc_attr($id); ?>"><?php echo esc_html($label); ?> <?php $this->helper($field['help']); ?></label>

      <?php if ($description) : ?>
        <p class="description"><?php echo esc_html($description); ?></p>
      <?php endif; ?>
      
      <input type="text" class="<?php echo esc_attr($class); ?>" name="<?php echo esc_attr($this->settings_name) . '[' . esc_attr($section) . '][' . esc_attr($name) . '][' . esc_attr($type) . ']'; ?>" id="<?php echo esc_attr($id); ?>" placeholder="<?php echo esc_attr($placeholder); ?>" value="<?php echo esc_html($value); ?>"<?php echo esc_attr($required); ?>>
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
    $description = isset($field['description']) && $field['description'] ? $field['description'] : '';
    $placeholder = isset($field['placeholder']) && $field['placeholder'] ? $field['placeholder'] : $field['label'];
    $id          = $section . '-' . $name;
    $class       = $field['class'] ? sanitize_title($field['class']) : '';
    $type        = $field['type'];
    $default     = ($field['default']) ? $field['default'] : '';
    $required    = (isset($field['required']) && $field['required']) ? ' required' : '';
    $value       = (!empty($settings[$section][$name]['value'])) ? $settings[$section][$name]['value'] : $default;
    ?>
    <div class="field-container">
      <label class="field-container-text" for="<?php echo esc_attr($id); ?>"><?php echo esc_html($label); ?> <?php $this->helper($field['help']); ?></label>
      
      <?php if ($description) : ?>
        <p class="description"><?php echo esc_html($description); ?></p>
      <?php endif; ?>
      
      <textarea class="<?php echo esc_attr($class); ?>" name="<?php echo esc_attr($this->settings_name) . '[' . esc_attr($section) . '][' . esc_attr($name) . '][' . esc_attr($type) . ']'; ?>" id="<?php echo esc_attr($id); ?>" placeholder="<?php echo esc_attr($placeholder); ?>"<?php echo esc_attr($required); ?><?php echo $rows ? ' rows="' . esc_html($rows) . '"' : ''; ?>><?php echo esc_html($value); ?></textarea>
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
    $description = isset($field['description']) && $field['description'] ? $field['description'] : '';
    $placeholder = isset($field['placeholder']) && $field['placeholder'] ? $field['placeholder'] : $field['label'];
    $id          = $section . '-' . $name;
    $class       = $field['class'] ? sanitize_title($field['class']) : '';
    $type        = $field['type'];
    $default     = ($field['default']) ? $field['default'] : '';
    $required    = (isset($field['required']) && $field['required']) ? ' required' : '';
    $value       = (!empty($settings[$section][$name]['value'])) ? $settings[$section][$name]['value'] : $default;
    ?>
    <div class="field-container">
      <label class="field-container-text" for="<?php echo esc_attr($id); ?>"><?php echo esc_html($label); ?> <?php $this->helper($field['help']); ?></label>
      
      <?php if ($description) : ?>
        <p class="description"><?php echo esc_html($description); ?></p>
      <?php endif; ?>
      
      <input type="email" class="<?php echo esc_attr($class); ?>" name="<?php echo esc_attr($this->settings_name) . '[' . esc_attr($section) . '][' . esc_attr($name) . '][' . esc_attr($type) . ']'; ?>" id="<?php echo esc_attr($id); ?>" placeholder="<?php echo esc_attr($placeholder); ?>" value="<?php echo esc_attr($value); ?>"<?php echo esc_attr($required); ?>>
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
    $description = isset($field['description']) && $field['description'] ? $field['description'] : '';
    $placeholder = isset($field['placeholder']) && $field['placeholder'] ? $field['placeholder'] : $field['label'];
    $id          = $section . '-' . $name;
    $class       = $field['class'] ? sanitize_title($field['class']) : '';
    $type        = $field['type'];
    $default     = ($field['default']) ? $field['default'] : '';
    $required    = (isset($field['required']) && $field['required']) ? ' required' : '';
    $value       = (!empty($settings[$section][$name]['value'])) ? $settings[$section][$name]['value'] : $default;
    ?>
    <div class="field-container">
      <label class="field-container-text" for="<?php echo esc_attr($id); ?>"><?php echo esc_html($label); ?> <?php $this->helper($field['help']); ?></label>
      
      <?php if ($description) : ?>
        <p class="description"><?php echo esc_html($description); ?></p>
      <?php endif; ?>
      
      <input type="url" class="<?php echo esc_attr($class); ?>" name="<?php echo esc_attr($this->settings_name) . '[' . esc_attr($section) . '][' . esc_attr($name) . '][' . esc_attr($type) . ']'; ?>" id="<?php echo esc_attr($id); ?>" placeholder="<?php echo $placeholder ? esc_html($placeholder) : 'https://www.example.com'; ?>" value="<?php echo esc_attr($value); ?>"<?php echo esc_attr($required); ?>>
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
    $description = isset($field['description']) && $field['description'] ? $field['description'] : '';
    $placeholder = isset($field['placeholder']) && $field['placeholder'] ? $field['placeholder'] : $field['label'];
    $id          = $section . '-' . $name;
    $class       = $field['class'] ? sanitize_title($field['class']) : '';
    $type        = $field['type'];
    $default     = ($field['default']) ? $field['default'] : '';
    $required    = (isset($field['required']) && $field['required'] ) ? ' required' : '';
    $value       = (!empty($settings[$section][$name]['value'])) ? $settings[$section][$name]['value'] : $default;
    ?>
    <div class="field-container">
      <label class="field-container-text" for="<?php echo esc_attr($id); ?>"><?php echo esc_html($label); ?> <?php $this->helper($field['help']); ?></label>
      
      <?php if ($description) : ?>
        <p class="description"><?php echo esc_html($description); ?></p>
      <?php endif; ?>
      
      <input type="password" class="<?php echo esc_attr($class); ?>" name="<?php echo esc_attr($this->settings_name) . '[' . esc_attr($section) . '][' . esc_attr($name) . '][' . esc_attr($type) . ']'; ?>" id="<?php echo esc_attr($id); ?>" placeholder="<?php echo esc_attr($placeholder); ?>" value="<?php echo esc_attr($value); ?>"<?php echo esc_attr($required); ?>>
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
    $description = isset($field['description']) && $field['description'] ? $field['description'] : '';
    $placeholder = isset($field['placeholder']) && $field['placeholder'] ? $field['placeholder'] : $field['label'];
    $id          = $section . '-' . $name;
    $class       = $field['class'] ? sanitize_title($field['class']) : '';
    $type        = $field['type'];
    $default     = ($field['default']) ? $field['default'] : '';
    $required    = (isset($field['required']) && $field['required']) ? ' required' : '';
    $value       = (!empty($settings[$section][$name]['value'])) ? $settings[$section][$name]['value'] : $default;
    ?>
    <div class="field-container">
      <label class="field-container-text" for="<?php echo esc_attr($id); ?>"><?php echo esc_html($label); ?> <?php $this->helper($field['help']); ?></label>
      
      <?php if ($description) : ?>
        <p class="description"><?php echo esc_html($description); ?></p>
      <?php endif; ?>
      
      <input type="number" class="<?php echo esc_attr($class); ?>" name="<?php echo esc_attr($this->settings_name) . '[' . esc_attr($section) . '][' . esc_attr($name) . '][' . esc_attr($type) . ']'; ?>" id="<?php echo esc_attr($id); ?>" placeholder="<?php echo esc_attr($placeholder); ?>" value="<?php echo esc_attr($value); ?>"<?php echo esc_attr($required); ?><?php echo $step ? ' step="' . esc_html($step) . '"' : ''; ?><?php echo $min ? ' min="' . esc_html($min) . '"' : ''; ?><?php echo $max ? ' max="' . esc_html($max) . '"' : ''; ?>>
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
    $settings    = $this->settings;
    $section     = $field['section'];
    $name        = sanitize_title($field['name']);
    $label       = isset($field['label']) && $field['label'] ? $field['label'] : $field['name'];
    $description = isset($field['description']) && $field['description'] ? $field['description'] : '';
    $id          = $section . '-' . $name;
    $class       = $field['class'] ? sanitize_title($field['class']) : '';
    $type        = $field['type'];
    $options     = $field['options'];
    $default     = $field['default'] ? $field['default'] : '';
    $disabled    = $field['disabled'] ? $field['disabled'] : '';
    $required    = (isset($field['required']) && $field['required']) ? ' required' : '';
    $value       = (!empty($settings[$section][$name]['value'])) ? $settings[$section][$name]['value'] : $default;
    ?>
    <div class="field-container">
      <label class="field-container-text" for="<?php echo esc_attr($id); ?>"><?php echo esc_html($label); ?> <?php $this->helper($field['help']); ?></label>
      
      <?php if ($description) : ?>
        <p class="description"><?php echo esc_html($description); ?></p>
      <?php endif; ?>
      
      <select class="form-select <?php echo esc_attr($class); ?>" name="<?php echo esc_attr($this->settings_name) . '[' . esc_attr($section) . '][' . esc_attr($name) . '][' . esc_attr($type) . ']'; ?>" id="<?php echo esc_attr($id); ?>" aria-label="<?php echo esc_attr($label); ?>"<?php echo esc_attr($required); ?><?php echo $disabled ? " disabled" : ''; ?>>
        <option value="" disabled selected>Select Option</option>
        <?php foreach ($options as $option) : ?>
          <option value="<?php echo esc_attr($option); ?>" <?php echo ($option == $value) ? ' selected' : ''; ?>><?php echo esc_html($option); ?></option>
        <?php endforeach; ?>
      </select>
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
    $settings    = $this->settings;
    $section     = $field['section'];
    $name        = sanitize_title($field['name']);
    $label       = isset($field['label']) && $field['label'] ? $field['label'] : $field['name'];
    $description = isset($field['description']) && $field['description'] ? $field['description'] : '';
    $id          = $section . '-' . $name;
    $class       = $field['class'] ? sanitize_title($field['class']) : '';
    $type        = $field['type'];
    $options     = $field['options'];
    $default     = ($field['default']) ? $field['default'] : '';
    $required    = (isset($field['required']) && $field['required']) ? ' required' : '';
    $value       = (!empty($settings[$section][$name]['value'])) ? $settings[$section][$name]['value'] : $default;
    ?>
    <div class="toggle">
      <div class="field-container">
        <label class="field-container-text" for="<?php echo esc_attr($id); ?>"><?php echo esc_html($label); ?> <?php $this->helper($field['help']); ?></label>
        
        <?php if ($description) : ?>
          <p class="description"><?php echo esc_html($description); ?></p>
        <?php endif; ?>
        
        <select class="form-select toggle-select <?php echo esc_attr($class); ?>" name="<?php echo esc_attr($this->settings_name) . '[' . esc_attr($section) . '][' . esc_attr($name) . '][' . esc_attr($type) . ']'; ?>" id="<?php echo esc_attr($id); ?>" aria-label="<?php echo esc_attr($label); ?>"<?php echo esc_attr($required); ?>>
          <option value="" disabled selected><?php echo esc_html($label); ?></option>
          <?php foreach ($options as $option => $option_field) : ?>
            <option value="<?php echo esc_attr($option); ?>" <?php echo ($option == $value) ? ' selected' : ''; ?>><?php echo esc_html($option); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <?php foreach ($options as $option => $option_field) : ?>
        <?php $option_class = isset($option_field['section']) ? sanitize_text_field($option_field['section']) : ''; ?>
        <?php $option_type = isset($option_field['type']) ? sanitize_text_field($option_field['type']) : ''; ?>
        <div class="field-container togglable <?php echo esc_attr(strtolower($option)); ?> <?php echo esc_attr($option_class); ?> ">
          <?php call_user_func( array( $this, 'callback_' . $option_type ), $option_field ); ?>
        </div>
      <?php endforeach; ?>
    </div>
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
    $description = isset($field['description']) && $field['description'] ? $field['description'] : '';
    $placeholder = isset($field['placeholder']) && $field['placeholder'] ? $field['placeholder'] : $field['label'];
    $id          = $section . '-' . $name;
    $class       = $field['class'] ? sanitize_title($field['class']) : '';
    $type        = $field['type'];
    $default     = ($field['default']) ? $field['default'] : '';
    $required    = (isset($field['required']) && $field['required']) ? ' required' : '';
    $value       = (!empty($settings[$section][$name]['value'])) ? $settings[$section][$name]['value'] : $default;
    ?>
    <div class="field-container">
      <label class="field-container-text" for="<?php echo esc_attr($id); ?>"><?php echo esc_html($label); ?> <?php $this->helper($field['help']); ?></label>
      
      <?php if ($description) : ?>
        <p class="description"><?php echo esc_html($description); ?></p>
      <?php endif; ?>
      
      <input type="date" class="<?php echo esc_attr($class); ?>" name="<?php echo esc_attr($this->settings_name) . '[' . esc_attr($section) . '][' . esc_attr($name) . '][' . esc_attr($type) . ']'; ?>" id="<?php echo esc_attr($id); ?>" placeholder="<?php echo esc_attr($placeholder); ?>" value="<?php echo esc_attr($value); ?>"<?php echo esc_attr($required); ?>>
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
    $description = isset($field['description']) && $field['description'] ? $field['description'] : '';
    $placeholder = isset($field['placeholder']) && $field['placeholder'] ? $field['placeholder'] : $field['label'];
    $id          = $section . '-' . $name;
    $class       = $field['class'] ? sanitize_title($field['class']) : '';
    $type        = $field['type'];
    $default     = ($field['default']) ? $field['default'] : '';
    $required    = (isset($field['required']) && $field['required']) ? ' required' : '';
    $value       = (!empty($settings[$section][$name]['value'])) ? $settings[$section][$name]['value'] : $default;
    ?>
    <div class="field-container">
      <label class="field-container-text" for="<?php echo esc_attr($id); ?>"><?php echo esc_html($label); ?> <?php $this->helper($field['help']); ?></label>
      
      <?php if ($description) : ?>
        <p class="description"><?php echo esc_html($description); ?></p>
      <?php endif; ?>
      
      <input type="time" class="<?php echo esc_attr($class); ?>" name="<?php echo esc_attr($this->settings_name) . '[' . esc_attr($section) . '][' . esc_attr($name) . '][' . esc_attr($type) . ']'; ?>" id="<?php echo esc_attr($id); ?>" placeholder="<?php echo esc_attr($placeholder); ?>" value="<?php echo esc_attr($value); ?>"<?php echo esc_attr($required); ?>>
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
    $settings    = $this->settings;
    $section     = $field['section'];
    $name        = sanitize_title($field['name']);
    $label       = isset($field['label']) && $field['label'] ? $field['label'] : $field['name'];
    $description = isset($field['description']) && $field['description'] ? $field['description'] : '';
    $id          = $section . '-' . $name;
    $class       = $field['class'] ? sanitize_title($field['class']) : '';
    $type        = $field['type'];
    $default     = ($field['default']) ? $field['default'] : '';
    $required    = (isset($field['required']) && $field['required']) ? ' required' : '';
    $value       = (!empty($settings[$section][$name]['value'])) ? $settings[$section][$name]['value'] : $default;
    ?>
    <div class="field-container">
      <label class="field-container-text" for="<?php echo esc_attr($id); ?>"><?php echo esc_html($label); ?> <?php $this->helper($field['help']); ?></label>
      
      <?php if ($description) : ?>
        <p class="description"><?php echo esc_html($description); ?></p>
      <?php endif; ?>
      
      <input type="text" class="color-picker <?php echo esc_attr($class); ?>" id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr($this->settings_name) . '[' . esc_attr($section) . '][' . esc_attr($name) . '][' . esc_attr($type) . ']'; ?>" value="<?php echo esc_attr($value); ?>" data-default-color="<?php echo esc_attr($default); ?>"<?php echo esc_attr($required); ?>>
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
    <div class="field-container">
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
    <div class="info">
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