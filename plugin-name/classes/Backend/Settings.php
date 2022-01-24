<?php

/**
 * Settings class to handle adding an admin page and options.
 * @author Poly Plugins <contact@polyplugins.com>
 * @link   https://github.com/PolyPlugins/wordpress-settings-class
 */

namespace Company\Plugin\Backend;

if (!defined('ABSPATH')) exit;

class Settings
{

  /**
	 * Full path and filename of plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin    Full path and filename of plugin.
	 */
	protected $plugin;

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_slug    The ID of this plugin.
	 */
	protected $plugin_slug;

	/**
	 * The slug but with _ instead of -
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_slug_id    The slug but with _ instead of -
	 */
  protected $plugin_slug_id;

	/**
	 * The unique name for the plugins options.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $settings_name    The name used to uniquely identify this plugins options.
	 */
  protected $settings_name;
  
	/**
	 * The plugin's options array
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $settings    The plugin's options array
	 */
  protected $settings;

  
	/**
	 * The plugin's options fields
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $fields    The plugin's options fields
	 */
  protected $fields;

  /**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param    string    $plugin_slug       The name of this plugin.
	 * @param    string    $version    The version of this plugin.
	 */
	public function __construct( $plugin, $plugin_slug, $plugin_slug_id, $settings_name, $settings, $fields ) {
		$this->plugin          = $plugin;
		$this->plugin_slug     = $plugin_slug;
		$this->plugin_slug_id  = $plugin_slug_id;
		$this->settings_name   = $settings_name;
		$this->settings        = $settings;
    $this->fields          = $fields;
	}
  
  /**
   * Initialize settings
   *
   * @return void
   */
  public function init()
  {
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
  public function enqueue() {
    $page = (isset($_GET['page'])) ? sanitize_text_field($_GET['page']) : '';
    if($page == $this->plugin_slug) {
      // JS
      wp_enqueue_script($this->plugin_slug . '-settings', plugins_url('/js/backend/settings.js', $this->plugin), array('jquery'), filemtime(plugin_dir_path(dirname($this->plugin)) . dirname(plugin_basename($this->plugin))  . '/js/backend/settings.js'), true);
      // Styles
      wp_enqueue_style('font-awesome', plugins_url('/css/backend/font-awesome.min.css', $this->plugin), array(), filemtime(plugin_dir_path(dirname($this->plugin)) . dirname(plugin_basename($this->plugin)) . '/css/backend/font-awesome.min.css'));
      wp_enqueue_style($this->plugin_slug . '-settings', plugins_url('/css/backend/settings.css', $this->plugin), array(), filemtime(plugin_dir_path(dirname($this->plugin)) . dirname(plugin_basename($this->plugin)) . '/css/backend/settings.css'));
      // Bootstrap
      wp_enqueue_script('bootstrap', plugins_url('/js/backend/bootstrap.min.js', $this->plugin), array(), filemtime(plugin_dir_path(dirname($this->plugin)) . dirname(plugin_basename($this->plugin))  . '/js/backend/bootstrap.min.js'), true);
      wp_enqueue_script('bootstrap-less', plugins_url('/js/backend/bootstrap-less.js', $this->plugin), array(), filemtime(plugin_dir_path(dirname($this->plugin)) . dirname(plugin_basename($this->plugin))  . '/js/backend/bootstrap-less.js'), true);
      // Localize
      wp_localize_script('bootstrap-less', 'plugin_properties', array('plugin_url' => plugins_url(), 'plugin_slug' => $this->plugin_slug));
    }
  }
  
  /**
   * Using the Settings API we trigger a custom method used for building the layout of the form
   *
   * @return void
   */
  public function settings_callback()
  {
    $settings     = (!empty($this->settings)) ? $this->settings : '';
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
            <img src="<?php echo plugins_url('/img/backend/logo.png', $this->plugin); ?>" class="nav-logo" />
          </nav>
          <nav class="nav flex-column">
            <?php foreach ($sections as $section) : ?>
              <a class="nav-link<?php echo ($sections[0] == $section) ? ' active' : ''; ?>" href="#<?php echo $section; ?>" selected-section="<?php echo $section; ?>"><?php echo str_replace('-', ' ', $section); ?></a>
            <?php endforeach; ?>
          </nav>
        </div>
        
        <!-- Fields -->
        <?php foreach($get_fields as $section => $fields) : ?>
          <div class="col-lg-10 col-md-12 options"<?php echo ($sections[0] != $section) ? ' style="display: none;"' : ''; ?> section="<?php echo $section ?>">
            <h2><?php echo $sections[0]; ?></h2>
            <div class="fields">
              <?php foreach($fields as $field) : ?>
                <?php $field['section'] = $section; ?>
                <?php $this->add_field($field); ?>
                <hr class="field-separator" />
              <?php endforeach; ?>
            </div>
            <div class="helper"></div>
          </div>
        <?php endforeach; ?>

        <div class="col-lg-2 col-md-2 helper-sidebar" style="display: none;">
          <h2>Help<span class="helper-close"><a href="javascript:void(0)"><i class="fas fa-times-circle"></i></a></span></h2>
          <div class="helper-text"></div>
        </div>
        
      </div>
    </div>
    <p class="credit">Built with <a href="https://github.com/PolyPlugins/Settings-Class-for-Wordpress">Settings Class for WordPress</a> by <a href="https://www.polyplugins.com">Poly Plugins</a><span>
  <?php
  }
  
  /**
   * This is the callback used in the Settings API that we piggyback off of to save the settings array
   *
   * @param  mixed $input    The submitted fields from the options form
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
   * Sanitize options
   *
   * @param  string       $type
   * @param  mixed        $value
   * @return string|array $sanitized_value 
   */
  public function sanitize($type, $value) {
    // Need to get previous settings and pass them back

    if ($type == 'switch') {
      $sanitized_value = sanitize_text_field($value);
    }

    if ($type == 'switch_additional_options') {
      $sanitized_value = sanitize_text_field($value);
    }

    if ($type == 'text') {
      $sanitized_value = sanitize_text_field($value);
    }

    if ($type == 'password') {
      $sanitized_value = sanitize_text_field($value);
    }

    if ($type == 'number') {
      $sanitized_value = (int) $value;
    }

    if ($type == 'dropdown') {
      $sanitized_value = sanitize_text_field($value);
    }

    if ($type == 'date') {
      $sanitized_value = sanitize_text_field($value);
    }

    if ($type == 'time') {
      $sanitized_value = sanitize_text_field($value);
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
    $label    = $field['name'];
    $id       = $section . '-' . $name;
    $type     = $field['type'];
    $checked  = (!empty($settings[$section][$name]['value'])) ? ' checked' : '';
    ?>
    <div class="form-group">
      <div class="custom-control custom-switch">
        <input type="checkbox" class="custom-control-input" name="<?php echo $this->settings_name . '[' . $section . '][' . $name . '][' . $type . ']' ; ?>" id="<?php echo $id; ?>"<?php echo $checked ?>>
        <label class="custom-control-label" for="<?php echo $id; ?>"><?php echo $label; ?></label>
      </div>
    </div>
    <?php
  }

  /**
   * A custom callback built to handle displaying of switch fields with additional options
   *
   * @param  array $field
   * @return void
   */
  public function callback_switch_additional_options($field) {
    $settings           = $this->settings;
    $section            = $field['section'];
    $name               = sanitize_title($field['name']);
    $label              = $field['name'];
    $id                 = $section . '-' . $name;
    $type               = $field['type'];
    $checked            = (!empty($settings[$section][$name]['value'])) ? ' checked' : '';
    $options            = $field['options'];
    $additional_options = (!empty($field['additional_options'])) ? $field['additional_options'] : false;
    ?>
    <label for="<?php echo $id; ?>"><?php echo $label; ?></label>
    <div class="form-group">
      <?php foreach ($options as $option) : ?>
        <div class="custom-control custom-switch">
          <input type="checkbox" class="custom-control-input" name="<?php echo $this->settings_name . '[' . $section . '][' . $name . '][' . $type . ']' ; ?>" id="<?php echo $id; ?>"<?php echo $checked ?>>
          <label class="custom-control-label" for="<?php echo $id; ?>"><?php echo $option; ?></label>
        </div>
        <?php if ($additional_options) : ?>
          <?php foreach ($additional_options as $additional_option) : ?>
            <?php var_dump($additional_option); ?>
          <?php endforeach; ?>
        <?php endif; ?>
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
    $settings = $this->settings;
    $section  = $field['section'];
    $name     = sanitize_title($field['name']);
    $label    = $field['name'];
    $id       = $section . '-' . $name;
    $type     = $field['type'];
    $value    = (!empty($settings[$section][$name]['value'])) ? $settings[$section][$name]['value'] : '';
    ?>
    <label for="<?php echo $id; ?>"><?php echo $label; ?></label>
    <div class="input-group">
      <input type="text" class="form-control" name="<?php echo $this->settings_name . '[' . $section . '][' . $name . '][' . $type . ']' ; ?>" id="<?php echo $id; ?>" placeholder="<?php echo $label; ?>" value="<?php echo $value; ?>">
      
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
    $settings = $this->settings;
    $section  = $field['section'];
    $name     = sanitize_title($field['name']);
    $label    = $field['name'];
    $id       = $section . '-' . $name;
    $type     = $field['type'];
    $value    = (!empty($settings[$section][$name]['value'])) ? $settings[$section][$name]['value'] : '';
    ?>
    <label for="<?php echo $id; ?>"><?php echo $label; ?></label>
    <div class="input-group">
      <input type="password" class="form-control" name="<?php echo $this->settings_name . '[' . $section . '][' . $name . '][' . $type . ']' ; ?>" id="<?php echo $id; ?>" placeholder="<?php echo $label; ?>" value="<?php echo $value; ?>">
      
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
    $settings = $this->settings;
    $section  = $field['section'];
    $name     = sanitize_title($field['name']);
    $label    = $field['name'];
    $id       = $section . '-' . $name;
    $type     = $field['type'];
    $value    = (!empty($settings[$section][$name]['value'])) ? $settings[$section][$name]['value'] : '';
    ?>
    <label for="<?php echo $id; ?>"><?php echo $label; ?></label>
    <div class="input-group">
      <input type="number" class="form-control" name="<?php echo $this->settings_name . '[' . $section . '][' . $name . '][' . $type . ']' ; ?>" id="<?php echo $id; ?>" placeholder="<?php echo $label; ?>" value="<?php echo $value; ?>">
      
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
    $label    = $field['name'];
    $id       = $section . '-' . $name;
    $type     = $field['type'];
    $options  = $field['options'];
    $value    = (!empty($settings[$section][$name]['value'])) ? $settings[$section][$name]['value'] : '';
    ?>
    <label for="<?php echo $id; ?>"><?php echo $label; ?></label>
    <div class="input-group">
      <select class="form-select" name="<?php echo $this->settings_name . '[' . $section . '][' . $name . '][' . $type . ']' ; ?>" id="<?php echo $id; ?>" aria-label="<?php echo $label; ?>">
        <option value="" disabled selected><?php echo $label; ?></option>
        <?php foreach ($options as $option) : ?>
          <option value="<?php echo $option; ?>" <?php echo ($option == $value) ? ' selected' : ''; ?>><?php echo $option; ?></option>
        <?php endforeach; ?>
      </select>
      
      <!-- Display a info button which displays a toast when clicked -->
      <?php $this->helper($field['help']); ?>
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
    $settings = $this->settings;
    $section  = $field['section'];
    $name     = sanitize_title($field['name']);
    $label    = $field['name'];
    $id       = $section . '-' . $name;
    $type     = $field['type'];
    $value    = (!empty($settings[$section][$name]['value'])) ? $settings[$section][$name]['value'] : '';
    ?>
    <label for="<?php echo $id; ?>"><?php echo $label; ?></label>
    <div class="input-group">
      <input type="date" class="form-control" name="<?php echo $this->settings_name . '[' . $section . '][' . $name . '][' . $type . ']' ; ?>" id="<?php echo $id; ?>" placeholder="<?php echo $label; ?>" value="<?php echo $value; ?>">
      
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
    $settings = $this->settings;
    $section  = $field['section'];
    $name     = sanitize_title($field['name']);
    $label    = $field['name'];
    $id       = $section . '-' . $name;
    $type     = $field['type'];
    $value    = (!empty($settings[$section][$name]['value'])) ? $settings[$section][$name]['value'] : '';
    ?>
    <label for="<?php echo $id; ?>"><?php echo $label; ?></label>
    <div class="input-group">
      <input type="time" class="form-control" name="<?php echo $this->settings_name . '[' . $section . '][' . $name . '][' . $type . ']' ; ?>" id="<?php echo $id; ?>" placeholder="<?php echo $label; ?>" value="<?php echo $value; ?>">
      
      <!-- Display a info button which displays a toast when clicked -->
      <?php $this->helper($field['help']); ?>
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
      <a href="javascript:void(0)" class="helper-icon"><i class="fa fa-info-circle" aria-hidden="true"></i></a>
    </div>
    
    <!-- Queue Helper -->
    <div class="helper-placeholder" style="display: none;">
      <?php echo $help; ?>
    </div>
    <?php
  }
    
  /**
   * @deprecated
   * 
   * Adds a toast notification
   *
   * @param  string $help    The message to be displayed in the toast.
   * @param  int    $timeout How long the message should be displayed. Default 5000 (5 seconds)
   * @return void
   */
  public function toast($help, $timeout) {
    // If no helper do nothing
    if (empty($help)) return;

    $timeout = (!empty($field['timeout'])) ? (int) $field['timeout'] : 5000;
    ?>

    <!-- Add info button -->
    <div class="info d-flex align-items-center justify-content-center">
      <a href="javascript:void(0)" class="helper"><i class="fa fa-info-circle" aria-hidden="true"></i></a>
    </div>
    
    <!-- Display toast -->
    <div class="position-fixed bottom-0 right-0 p-3" style="z-index: 5; right: 0; bottom: 0;">
      <div class="toast hide" role="alert" aria-live="assertive" aria-atomic="true" data-delay="<?php echo $timeout; ?>">
        <div class="toast-header">
          <img src="<?php echo plugins_url('/img/backend/icon.png', $this->plugin); ?>" class="rounded mr-2" alt="Poly Plugins Icon">
          <strong class="mr-auto">Information</strong>
          <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="toast-body">
          <?php echo $help; ?>
        </div>
      </div>
    </div>
    <?php
  }

  /**
   * Get option from options array
   *
   * @param  mixed $section      Section of setting
   * @param  mixed $option       Get option of the previously specified section
   * @return mixed $option_value Returns the value of the option
   */
  public function get_option($section, $option) {
    if (!empty($this->options[$section][$option]['value'])) {
      $option_value = $this->options[$section][$option]['value'];
    } else {
      $option_value = '';
    }
    
    return $option_value;
  }

}