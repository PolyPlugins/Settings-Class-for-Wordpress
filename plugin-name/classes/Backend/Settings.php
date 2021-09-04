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
	 * @var      string    $options_name    The name used to uniquely identify this plugins options.
	 */
  protected $options_name;
  
	/**
	 * The plugin's options array
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $options    The plugin's options array
	 */
  protected $options;

  
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
	public function __construct( $plugin, $plugin_slug, $plugin_slug_id, $options_name, $options, $fields ) {
		$this->plugin          = $plugin;
		$this->plugin_slug     = $plugin_slug;
		$this->plugin_slug_id  = $plugin_slug_id;
		$this->options_name    = $options_name;
		$this->options         = $options;
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
      $this->options_name, // option_name
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
    // JS
    wp_enqueue_script($this->plugin_slug . '-settings', plugins_url('/js/backend/settings.js', $this->plugin), array('jquery'), filemtime(plugin_dir_path(dirname($this->plugin)) . dirname(plugin_basename($this->plugin))  . '/js/backend/settings.js'), true);
    // Styles
    wp_enqueue_style('font-awesome', plugins_url('/css/backend/font-awesome.min.css', $this->plugin), array(), filemtime(plugin_dir_path(dirname($this->plugin)) . dirname(plugin_basename($this->plugin)) . '/css/backend/font-awesome.min.css'));
    wp_enqueue_style($this->plugin_slug . '-settings', plugins_url('/css/backend/settings.css', $this->plugin), array(), filemtime(plugin_dir_path(dirname($this->plugin)) . dirname(plugin_basename($this->plugin)) . '/css/backend/settings.css'));
    wp_enqueue_style($this->plugin_slug . '-admin', plugins_url('/css/backend/admin.css', $this->plugin), array(), filemtime(plugin_dir_path(dirname($this->plugin)) . dirname(plugin_basename($this->plugin)) . '/css/backend/admin.css'));
    // Bootstrap
    wp_enqueue_script('bootstrap', plugins_url('/js/backend/bootstrap.js', $this->plugin), array(), filemtime(plugin_dir_path(dirname($this->plugin)) . dirname(plugin_basename($this->plugin))  . '/js/backend/bootstrap.js'), true);
    wp_enqueue_script('bootstrap-less', plugins_url('/js/backend/bootstrap-less.js', $this->plugin), array(), filemtime(plugin_dir_path(dirname($this->plugin)) . dirname(plugin_basename($this->plugin))  . '/js/backend/bootstrap-less.js'), true);
    // Localize
    wp_localize_script('bootstrap-less', 'plugin_properties', array('plugin_url' => plugins_url(), 'plugin_slug' => $this->plugin_slug));
  }
  
  /**
   * Using the Settings API we trigger a custom method used for building the layout of the form
   *
   * @return void
   */
  public function settings_callback()
  {
    $options     = (!empty($this->options)) ? $this->options : '';
    $get_fields  = $this->fields;
    $sections    = array_keys($get_fields);
    ?>
    
    <div class="bootstrap-wrapper">
      <div class="row top-bar">
        <?php echo $sections[0]; ?>
      </div>

      <!-- Display a loader as a placeholder until page is loaded -->
      <div class="load-settings d-flex justify-content-center m-3">
        <div class="spinner-border" style="width: 3rem; height: 3rem;" role="status">
          <span class="sr-only">Loading...</span>
        </div>
      </div>

      <!-- Settings are hidden until page loaded to prevent layout shifting -->
      <div class="row settings-container align-items-start" style="display: none;">
        
        <!-- Fields -->
        <?php foreach($get_fields as $section => $fields) : ?>
          <div class="col-lg-9 col-md-12 options"<?php echo ($sections[0] != $section) ? ' style="display: none;"' : ''; ?> section="<?php echo $section ?>">
            <?php foreach($fields as $field) : ?>
              <?php $field['section'] = $section; ?>
              <?php $this->add_field($field); ?>
            <?php endforeach; ?>
          </div>
        <?php endforeach; ?>

        <!-- Navigation -->
        <div class="col-lg-3 col-md-12 tabs">
          <nav class="nav flex-column">
            <?php foreach ($sections as $section) : ?>
              <a class="nav-link<?php echo ($sections[0] == $section) ? ' active' : ''; ?>" href="javascript:void(0)" selected-section="<?php echo $section; ?>"><?php echo str_replace('-', ' ', $section); ?></a>
            <?php endforeach; ?>
          </nav>
        </div>

      </div>
    </div>
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
    $settings     = array();
    $old_settings = $this->options;

    // If empty return old settings
    if (empty($input)) return $old_settings;

    foreach($input as $section => $options) {
      foreach($options as $name => $option) {
        $type = key($option);
        $value = $this->sanitize($type, $option[$type]);

        if ($value !== false) {
          // Sanitization succeeded, add option to settings array
          $settings[$section][$name] = array(
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
    return ($validated) ? $settings : $old_settings;
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

    if ($type == 'text') {
      $sanitized_value = sanitize_text_field($value);
    }

    if ($type == 'switch') {
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
    $options = $this->options;
    $section = $field['section'];
    $name    = sanitize_title($field['name']);
    $label   = $field['name'];
    $id      = $section . '-' . $name;
    $type    = $field['type'];
    $checked = (!empty($options[$section][$name]['value'])) ? ' checked' : '';
    ?>
    <div class="form-group">
      <div class="custom-control custom-switch">
        <input type="checkbox" class="custom-control-input" name="<?php echo $this->options_name . '[' . $section . '][' . $name . '][' . $type . ']' ; ?>" id="<?php echo $id; ?>"<?php echo $checked ?>>
        <label class="custom-control-label" for="<?php echo $id; ?>"><?php echo $label; ?></label>
      </div>
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
    $options = $this->options;
    $section = $field['section'];
    $name    = sanitize_title($field['name']);
    $label   = $field['name'];
    $id      = $section . '-' . $name;
    $type    = $field['type'];
    $value   = (!empty($options[$section][$name]['value'])) ? $options[$section][$name]['value'] : '';
    ?>
    <div class="input-group">
      <input type="text" class="form-control" name="<?php echo $this->options_name . '[' . $section . '][' . $name . '][' . $type . ']' ; ?>" id="<?php echo $id; ?>" placeholder="<?php echo $label; ?>" value="<?php echo $value; ?>">
      
      <!-- Display a info button which displays a toast when clicked -->
      <?php $this->toast($field['help'], $field['timeout']); ?>
    </div>
    <?php
  }
    
  /**
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

}