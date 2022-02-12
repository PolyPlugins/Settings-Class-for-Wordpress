# Settings Class for WordPress
![image](https://www.polyplugins.com/plugins/settings-class/1.gif)
Our goal was to create a class that could easily be imported into projects to give easy methods to handle adding a clean and dynamic settings panel to the backend of WordPress.

# Example
You can see this class in action in our [Loginator](https://github.com/PolyPlugins/Loginator) plugin. Loginator's implementation does not use autoloading and therefor is not PSR-4 compliant. We did this to make invoking the logger easier.

# Features
- Bootstrap Container (Courtesy of [Rush Frisby](https://rushfrisby.com/using-bootstrap-in-wordpress-admin-panel))
- Font-Awesome Field Info Buttons and <s>Bootstrap Toasts</s> Sidebar Info Helper
- jQuery Dynamic Navigation
- Settings Grouped Under One Option in Database (Saved as Multi-Dimensional Array)
- Bootstrap Spinner Preloader (Prevents Layout Shifting on Load)

# Usage  
We built and used this in our [fork](https://github.com/PolyPlugins/PSR4-WordPress-Plugin-Boilerplate) of Devin Vinson's [WordPress Plugin Boilerplate](https://github.com/DevinVinson/WordPress-Plugin-Boilerplate) however you can adapt this to suit the structure of your plugin. Our PSR4 version of Devin's plugin uses Namespacing and Autoloading, which is perfect for this class. So if you aren't familiar with those, now is a wonderful time to learn, because we'll be referencing our fork moving forward.

In your constructor for your backend loader, you'll want to define the settings property referencing the class.  
`$this->settings = new Settings($this->plugin, $this->plugin_slug, $this->plugin_slug_id, $this->options_name, $this->options, $fields);`

As you can see we are passing a few properties this class requires in order to initialize. Below are how those properties are defined.
```
$this->plugin           = __FILE__;
$this->plugin_slug      = dirname( plugin_basename( $this->plugin ) );
$this->plugin_slug_id   = str_replace( '-', '_', $this->plugin_slug );
$this->options_name     = $this->plugin_slug_id . '_options';
$this->options_page     = 'options-general.php';
$this->options          = get_option( $this->options_name );
```

The fields method is defined as follows
```
public function fields() {
  $fields = array(
    'general' => array(
      	array(
	  'name'    => __('Enabled', $this->plugin_slug),
	  'type'    => 'switch',
	  'default' => false,
	),
	array(
	  'name'    => __('Username', $this->plugin_slug),
	  'type'    => 'text',
	  'default' => false,
	  'help'    => __('Enter a username.', $this->plugin_slug),
	),
	array(
	  'name'    => __('Password', $this->plugin_slug),
	  'type'    => 'password',
	  'default' => false,
	  'help'    => __('Enter a password. Note: This is stored in the DB as plain text as most other plugins do, we will change this if requested.', $this->plugin_slug),
	),
	array(
	  'name'     => __('Number', $this->plugin_slug),
	  'type'     => 'number',
	  'default'  => false,
	  'help'     => __('Enter a number.', $this->plugin_slug),
	  'required' => true,
	),
	array(
	  'name'    => __('Time', $this->plugin_slug),
	  'type'    => 'time',
	  'default' => false,
	  'help'    => __('Select a time.', $this->plugin_slug),
	),
	array(
	  'name'    => __('Date', $this->plugin_slug),
	  'type'    => 'date',
	  'default' => false,
	  'help'    => __('Select a date.', $this->plugin_slug),
	),
	array(
	  'name'    => __('Color', $this->plugin_slug),
	  'type'    => 'dropdown',
	  'options' => array('Red', 'Blue'),
	  'default' => false,
	  'help'    => __('Select a date.', $this->plugin_slug),
	),
    ),
    'woocommerce' => array(
      array(
        'name'    => __('Enabled', $this->plugin_slug),
        'type'    => 'switch',
        'default' => false,
        'help'    => __('Toggle this switch to the right to enable the plugin.', $this->plugin_slug),
      ),
    ),
    'license' => array(
      array(
        'name'    => __('Enabled', $this->plugin_slug),
        'type'    => 'switch',
        'default' => false,
        'help'    => __('Toggle this switch to the right to enable the plugin.', $this->plugin_slug),
      ),
    ),
    'support' => array(
      array(
        'name'    => __('Enabled', $this->plugin_slug),
        'type'    => 'switch',
        'default' => false,
        'help'    => __('Toggle this switch to the right to enable the plugin.', $this->plugin_slug),
      ),
    ),
  );

  return $fields;
}
```

You'll also have to initialize the settings on admin init and enqueue scripts on admin enqueue
```
$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue' );
$this->loader->add_action( 'admin_init', $plugin_admin, 'admin_init' );
$this->loader->add_action( 'admin_menu', $plugin_admin, 'admin_menu' );
```

```
public function admin_init() {
  // Initialize settings
  $this->settings->init();
}

public function enqueue() {
  $this->settings->enqueue();
}

public function admin_menu() {
  $this->settings->admin_menu($this->options_page);
}
```

# To-Do
- Add switch toggle with additional options field
- Add WP Color Picker field


# Consider Contributing
We know this class will be useful to many in cutting down development times, but we would love help from the community. We are actively using this class for our software and will continue to build off of it, but we know it can become something greater, faster, with the help of the community. Feel free to submit a PR or submit any issues you have as we will be actively maintaining this.
