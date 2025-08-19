# Settings Class for WordPress

https://github.com/user-attachments/assets/a5e318cc-e7c6-4f1f-a394-f5ac988f074e

Our goal was to create a class that could easily be imported into projects to give easy methods to handle adding a clean and dynamic settings panel to the backend of WordPress.

## Update
This project will be actively maintained again as a Composer package.

You can still use [Reusable Admin Panel](https://wordpress.org/plugins/reusable-admin-panel/) if you prefer, but this package makes it easier for plugin developers to include the settings class directly without requiring an additional plugin. While we understand that multiple plugins might include the same files, we decided not to force users to install extra plugins for our plugins, which is why we are offering this Composer based solution. Reusable Admin Panel will continue to be maintained as well.

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

You can also download our [test-plugin](https://github.com/PolyPlugins/PSR4-WordPress-Plugin-Boilerplate) boilerplate to get up and running.

If you want barebones you can create a test-plugin.php file within a new /wp-content/plugins/test-plugin/ folder and add the below code:
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
    
    $config = array(
      'name'             => __('Test Plugin', 'test-plugin'), // The plugin name. Comment out to have it build the name from plugin slug
      'menu_name'        => __('Test Plugin', 'test-plugin'), // The name you want to show in the admin menu. Comment out to have it build the name from plugin slug
      'settings_name'    => 'test_plugin_settings_polyplugins, // To prevent conflicts you should include your company name as the suffix. This is the setting name you want to use for get_option. 
      'page'             => 'options-general.php', // You can use non php pages such as woocommerce here to display a submenu under WooCommerce
      'position'         => 1, // Lower number moves the link position up in the submenu
      'capability'       => 'manage_options', // What permission is required to see and edit settings
      'css'              => '/css/style.css', // Your custom colors and styles. Comment out to use only the default style.
      'js'               => '/js/admin.js', // Your custom javascript. Comment out to only use the default js.
      'template'         => 'recharge', // Change the theme the settings uses. Comment out to use the default or enter 'default'
      'support'          => 'https://www.polyplugins.com/support/', // Your support link. Comment out to have no support link.
      'action_links' => array( // Optional, add action links to the listing on admin plugins page
        array(
          // Generates url for settings automatically
          'label'    => __('Settings', 'test-plugin'),
          'style'    => 'color: orange; font-weight: 700;',
          'external' => false
        ),
        array(
          'url'      => 'https://www.polyplugins.com',
          'label'    => __('Go Pro', 'test-plugin'),
          'style'    => 'color: green; font-weight: 700;',
          'external' => true
        ),
      ),
      'meta_links' => array( // Optional, add meta links to the listing on admin plugins page
        array(
          'url'      => 'https://github.com/users/PolyPlugins/projects/4',
          'label'    => __('Roadmap', 'test-plugin'),
          'style'    => 'color: purple; font-weight: 700;',
          'external' => true
        ),
        array(
          // Generates url for support automatically
          'label'    => __('Support', 'test-plugin'),
          'style'    => 'font-weight: 700;',
          'external' => true
        ),
      ),
      'sidebar' => array( // Optional, add a permanent sidebar
        'heading'      => __('Something Not Working?', 'test-plugin'),
        'body'         => __('Feel free to reach out!', 'test-plugin'),
        'button_label' => __('Email Us', 'test-plugin'),
        'button_url'   => 'https://www.polyplugins.com/contact/'
      ),
    );

    $fields = array(
      'general' => array(
        'icon' => 'gear-fill',
        'fields' => array(
          array(
            'name'     => __('Enabled', 'test-plugin'),
            'type'     => 'switch',
            'default'  => false,
          ),
          array(
            'name'      => __('Button', 'test-plugin'),
            'label'     => __('Dual Buttons', 'test-plugin'),
            'type'      => 'button',
            'data'      => array(
              array(
                'title' => __('Action 1', 'test-plugin'), // general-action-1 would be the id you'd target in js
                'class' => 'primary',
                ),
              array(
                'title'  => __('Action 2', 'test-plugin'),
                'class'  => 'secondary',
                'url'    => 'https://www.polyplugins.com', // If no url then javascript:void(0) is used, this is useful for custom js
                'target' => '_blank',
              )
            )
          ),
          array(
            'name'        => __('Username', 'test-plugin'),
            'label'       => __('The Username', 'test-plugin'),
            'description' => __('Enter a description.', 'test-plugin'),
            'type'        => 'text',
            'placeholder' => __('Enter your username...', 'test-plugin'),
            'default'     => false,
            'help'        => __('Enter a username.', 'test-plugin'),
          ),
          array(
            'name'     => __('Textarea', 'test-plugin'),
            'type'     => 'textarea',
            'default'  => __('Description goes here...', 'test-plugin'),

            'help'     => __('Enter a description.', 'test-plugin'),
          ),
          array(
            'name'     => __('Larger Textarea', 'test-plugin'),
            'type'     => 'textarea',
            'rows'     => 6,
            'default'  => __('Description goes here...', 'test-plugin'),

            'help'     => __('Enter a description.', 'test-plugin'),
          ),
          array(
            'name'     => __('Email', 'test-plugin'),
            'type'     => 'email',
            'default'  => 'test@example.com',
            'help'     => __('Enter your email...', 'test-plugin'),
          ),
          array(
            'name'     => __('URL', 'test-plugin'),
            'type'     => 'url',
            'default'  => false,
            'help'     => __('Enter a URL. Ex: https://www.example.com', 'test-plugin'),
          ),
          array(
            'name'     => __('Password', 'test-plugin'),
            'type'     => 'password',
            'default'  => 'test',
            'help'     => __('Enter a password. Note: This is stored in the DB as plain text as most other plugins do, we will change this if requested.', 'test-plugin'),
          ),
          array(
            'name'     => __('Number', 'test-plugin'),
            'type'     => 'number',
            'min'      => 1,
            'max'      => 10,
            'step'     => 2,
            'default'  => false,
            'help'     => __('Enter a number.', 'test-plugin'),
            'required' => true,
          ),
          array(
            'name'     => __('Time', 'test-plugin'),
            'type'     => 'time',
            'default'  => false,
            'help'     => __('Select a time.', 'test-plugin'),
          ),
          array(
            'name'     => __('Date', 'test-plugin'),
            'type'     => 'date',
            'default'  => false,
            'help'     => __('Select a date.', 'test-plugin'),
          ),
          array(
            'name'     => __('Color Picker', 'test-plugin'),
            'type'     => 'color',
            'default'  => '#00ff00',
            'help'     => __('Select a color.', 'test-plugin'),
          ),
          array(
            'name'     => __('Dropdown', 'test-plugin'),
            'type'     => 'dropdown',
            'options'  => array(__('Red', 'test-plugin'), __('Blue', 'test-plugin')),
            'default'  => false,
            'help'     => __('Select an option from the dropdown.', 'test-plugin'),
          ),
          array(
            'name'     => __('Disabled Dropdown', 'test-plugin'),
            'type'     => 'dropdown',
            'options'  => array(__('Red', 'test-plugin'), __('Blue', 'test-plugin')),
            'default'  => false,
            'disabled' => true,
            'help'     => __('Select an option from the dropdown.', 'test-plugin'),
          ),
        ),
        'subsections' => array(
          'debug' => array(
            'icon'  => 'bug-fill',
            'label' => __('Debug', 'test-plugin'),
            'fields' => array(
              array(
                'name'    => __('Debug Mode', 'test-plugin'),
                'type'    => 'switch',
                'default' => false,
              ),
            )
          ),
        ),
      ),
      'api' => array(
        'icon' => 'cloud-arrow-up-fill',
        'note' => array(
          'message' => __('Use notes to display messages about specific sections', 'test-plugin'),
          'class'   => 'warning', // Use success, warning, or error
        ),
        'fields' => array(
          array(
            'name'     => __('Dropdown Toggle', 'test-plugin'),
            'type'     => 'dropdown_toggle',
            'options'  => array(
              'Production' => array(
                'name'     => __('API Key', 'test-plugin'),
                'type'     => 'text',
              ),
              'Development' => array(
                'name'     => __('API Key', 'test-plugin'),
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

## Roadmap
Check out our [Roadmap](https://github.com/users/PolyPlugins/projects/4) to see our upcoming features!

## Consider Contributing
We know this class will be useful to many in cutting down development times, but we would love help from the community. We are actively using this class for our software and will continue to build off of it, but we know it can become something greater, faster, with the help of the community. Feel free to submit a PR or submit any issues you have as we will be actively maintaining this.

## GDPR
We are not lawyers and always recommend doing your own compliance research into third party plugins, libraries, ect, as we’ve seen other libraries not be in compliance with these regulations.

This library uses the Bootstrap, BootStrap Icons, and SweetAlert2 3rd party libraries. These libraries are loaded locally to be compliant with data protection regulations.

This library collects and stores certain data on your server to ensure proper functionality. This includes:

* Storing plugin settings
* Remembering which notices have been dismissed

## Changelog

### 3.0.0
* Added: Subsections
* Added: Templating
* Added: Ability to add your own settings_name in config.
* Added: Additional 'note' attribute to sections to have a note appear at the top of the section
* Added: Additional 'description' attribute to fields
* Added: Separators between fields
* Added: Ability to add action links and meta links with config
* Added: Sidebar
* Added: Sidebar config
* Added: Recharge template
* Updated: Positioning of save button and credit
* Updated: Default template to have the fields below the name as having the label next to the fields took up too much space
* Updated: Bootstrap
* Bugfix: Settings name generation
* Removed: Excess properties that were really only required for checking if Reusable Admin Panel was activated, but since this class is just included in all plugins, those properties are no longer needed. This makes instantiating the class shorter with less defining of properties.
* Removed: Reusable Admin Panel checks
* Reworked: Auto settings_name generation.

### 2.0.0
* Added: Composer
* Added: Ability to use icons in tab navigator
* Added: Bootstrap Icons
* Added: All the features from latest Reusable Admin Panel so code base is current
* Bugfix: Dropdown select preventing load
* Removed: Font Awesome Icons
