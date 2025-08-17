# Settings Class for WordPress
![image](https://www.polyplugins.com/plugins/settings-class/1.gif)
Our goal was to create a class that could easily be imported into projects to give easy methods to handle adding a clean and dynamic settings panel to the backend of WordPress.

## Update
This project will be actively maintained again as a Composer package.

You can still use [Reusable Admin Panel](https://wordpress.org/plugins/reusable-admin-panel/) if you prefer, but this package makes it easier for plugin developers to include the settings class directly without requiring an additional plugin. While we understand that multiple plugins might include the same files, we decided not to force users to install extra plugins for our plugins, which is why we are offering this Composer-based solution. Reusable Admin Panel will continue to be maintained as well.

## Features
- Bootstrap Container (Courtesy of [Rush Frisby](https://rushfrisby.com/using-bootstrap-in-wordpress-admin-panel))
- Font-Awesome Field Info Buttons and <s>Bootstrap Toasts</s> Sidebar Info Helper
- jQuery Dynamic Navigation
- Validation using [validator.js](https://github.com/validatorjs/validator.js)
- Settings Grouped Under One Option in Database (Saved as Multi-Dimensional Array)
- Bootstrap Spinner Preloader (Prevents Layout Shifting on Load)

## Installation
The easiest way to install Settings Class for WordPress is via [composer](http://getcomposer.org) within your /wp-content/plugins/test-plugin/ directory:

```composer require polyplugins/settings-class-for-wordpress```

After you run the require you'll see a vendor folder.

## Example Plugin
Create test-plugin.php within the test-plugin folder and add the below code:
```
<?php

/**
 * Plugin Name: Test Plugin
 * Description: Test
 * Version: 1.0.0
 * Author: Poly Plugins
 * Author URI: https://www.polyplugins.com
 * Plugin URI: https://www.polyplugins.com
 */

namespace PolyPlugins\Test_Plugin;

require plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

use PolyPlugins\Settings\Settings;

if (!defined('ABSPATH')) exit;

class Test_Plugin
{
  
  private $plugin;
  private $namespace;
  private $plugin_slug;
  private $config;
  private $fields;
  private $settings;
  
  public function __construct()
  {
    $this->plugin      = __FILE__;
    $this->namespace   = __NAMESPACE__;
    $this->plugin_slug = dirname(plugin_basename($this->plugin));
    
    $this->config  = array(
      'page'       => 'options-general.php', // You can use non php pages such as woocommerce here to display a submenu under Woocommerce
      'position'   => 1, // Lower number moves the link position up in the submenu
      'capability' => 'manage_options', // What permission is required to see and edit settings
      'css'        => '/css/style.css', // Your custom colors and styles. Comment out to use default style.
      'js'         => '/js/admin.js', // Your custom javascript. Comment out to use default js.
      'support'    => 'https://www.polyplugins.com/support/', // Your support link. Comment out to have no support link.
    );

    $this->fields = array(
      'general' => array(
        'icon' => 'gear-fill',
        'fields' => array(
          array(
            'name'     => __('Enabled', $this->plugin_slug),
            'type'     => 'switch',
            'default'  => false,
          ),
          array(
            'name'      => __('Button', $this->plugin_slug),
            'label'     => __('Dual Buttons', $this->plugin_slug),
            'type'      => 'button',
            'data'      => array(
              array(
                'title' => 'Action 1', // general-action-1 would be the id you'd target in js
                'class' => 'primary',
                ),
              array(
                'title'  => 'Action 2',
                'class'  => 'secondary',
                'url'    => 'https://www.polyplugins.com', // If no url then javascript:void(0) is used, this is useful for custom js
                'target' => '_blank',
              )
            )
          ),
          array(
            'name'        => __('Username', $this->plugin_slug),
            'type'        => 'text',
            'placeholder' => 'Enter your username...',
            'default'     => false,
            'help'        => __('Enter a username.', $this->plugin_slug),
          ),
          array(
            'name'     => __('Textarea', $this->plugin_slug),
            'type'     => 'textarea',
            'default'  => 'Description goes here...',

            'help'     => __('Enter a description.', $this->plugin_slug),
          ),
          array(
            'name'     => __('Larger Textarea', $this->plugin_slug),
            'type'     => 'textarea',
            'rows'     => 6,
            'default'  => 'Description goes here...',

            'help'     => __('Enter a description.', $this->plugin_slug),
          ),
          array(
            'name'     => __('Email', $this->plugin_slug),
            'type'     => 'email',
            'default'  => 'test@example.com',
            'help'     => __('Enter your email...', $this->plugin_slug),
          ),
          array(
            'name'     => __('URL', $this->plugin_slug),
            'type'     => 'url',
            'default'  => false,
            'help'     => __('Enter a URL. Ex: https://www.example.com', $this->plugin_slug),
          ),
          array(
            'name'     => __('Password', $this->plugin_slug),
            'type'     => 'password',
            'default'  => 'test',
            'help'     => __('Enter a password. Note: This is stored in the DB as plain text as most other plugins do, we will change this if requested.', $this->plugin_slug),
          ),
          array(
            'name'     => __('Number', $this->plugin_slug),
            'type'     => 'number',
            'min'      => 1,
            'max'      => 10,
            'step'     => 2,
            'default'  => false,
            'help'     => __('Enter a number.', $this->plugin_slug),
            'required' => true,
          ),
          array(
            'name'     => __('Time', $this->plugin_slug),
            'type'     => 'time',
            'default'  => false,
            'help'     => __('Select a time.', $this->plugin_slug),
          ),
          array(
            'name'     => __('Date', $this->plugin_slug),
            'type'     => 'date',
            'default'  => false,
            'help'     => __('Select a date.', $this->plugin_slug),
          ),
          array(
            'name'     => __('Color Picker', $this->plugin_slug),
            'type'     => 'color',
            'default'  => '#00ff00',
            'help'     => __('Select a color.', $this->plugin_slug),
          ),
          array(
            'name'     => __('Dropdown', $this->plugin_slug),
            'type'     => 'dropdown',
            'options'  => array('Red', 'Blue'),
            'default'  => false,
            'help'     => __('Select an option from the dropdown.', $this->plugin_slug),
          ),
          array(
            'name'     => __('Disabled Dropdown', $this->plugin_slug),
            'type'     => 'dropdown',
            'options'  => array('Red', 'Blue'),
            'default'  => false,
            'disabled' => true,
            'help'     => __('Select an option from the dropdown.', $this->plugin_slug),
          ),
        )
      ),
      'api' => array(
        'icon' => 'cloud-arrow-up-fill',
        'fields' => array(
          array(
            'name'     => __('Dropdown Toggle', $this->plugin_slug),
            'type'     => 'dropdown_toggle',
            'options'  => array(
              'Production' => array(
                'name'     => __('API Key', $this->plugin_slug),
                'type'     => 'text',
              ),
              'Development' => array(
                'name'     => __('API Key', $this->plugin_slug),
                'type'     => 'text',
              )
            ),
          ),
        ),
      )
    );
  }
  
  public function init(){
    add_action('init', array($this, 'loaded'));
  }

  public function loaded() {
    if (!class_exists('Settings')) {
      $this->settings = new Settings($this->plugin, $this->namespace, $this->config, $this->fields);
      $this->settings->init();
    }
  }

}

$test_plugin = new Test_Plugin;
$test_plugin->init();
```

Once activated you will now see Test Plugin under Settings -> Test Plugin in the backend of WordPress.

You can learn more about the fields you can use via our [Documentation](https://www.polyplugins.com/docs/reusable-admin-panel/fields/).

You can also download our [PSR4 WordPress Plugin Boilerplate](https://github.com/PolyPlugins/PSR4-WordPress-Plugin-Boilerplate) to give you a better understanding of composer's autoloading.

## Roadmap
* Add support for latest bootstrap
* Add sub-menus
* Add capability to use templates and have a config option to select templates
* Add switch toggle with additional options field

## Changelog

### 2.0.0
* Added: Composer
* Added: Ability to use icons in tab navigator
* Added: Bootstrap Icons
* Added: All the features from latest Reusable Admin Panel so code base is current
* Bugfix: Dropdown select preventing load
* Removed: Font Awesome Icons


## Consider Contributing
We know this class will be useful to many in cutting down development times, but we would love help from the community. We are actively using this class for our software and will continue to build off of it, but we know it can become something greater, faster, with the help of the community. Feel free to submit a PR or submit any issues you have as we will be actively maintaining this.
