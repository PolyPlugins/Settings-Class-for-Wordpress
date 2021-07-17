# Settings Class for WordPress
![image](https://user-images.githubusercontent.com/85666508/125552415-76589585-3ad4-4f3d-98d6-4c8270bdbdc6.png)
Our goal was to create a class that could easily be imported into projects to give easy methods to handle adding a settings to the backend of WordPress.

**Features**
- Bootstrap Container (Courtesy of [Rush Frisby](https://rushfrisby.com/using-bootstrap-in-wordpress-admin-panel))
- Font-Awesome Field Info Buttons and Bootstrap Toasts
- jQuery Dynamic Navigation
- Settings Grouped Under One Option in Database (Saved as Multi-Dimensional Array)
- Bootstrap Spinner Preloader (Prevents Layout Shifting on Load)

**Usage**  
We built and used this in our [fork](https://github.com/PolyPlugins/PSR4-WordPress-Plugin-Boilerplate) of Devin Vinson [WordPress Plugin Boilerplate](https://github.com/DevinVinson/WordPress-Plugin-Boilerplate) however you can adapt this to suit the structure of your plugin. Our PSR4 version of Devin's plugin uses Namespacing and Autoloading, which is perfect for this class. So if you aren't familiar with those, now is a wonderful time to learn, because we'll be referencing our fork moving forward.

In your constructor for your backend loader, you'll want to define the settings property referencing the class.  
`$this->settings = new Settings($this->plugin, $this->plugin_slug, $this->plugin_slug_id, $this->options_name, $this->options, $this->fields());`

As you can see we are passing a few properties this class requires in order to initialize. Below are how those properties are defined.
```
$this->plugin = __FILE__;
$this->plugin_slug = dirname( plugin_basename( $this->plugin ) );
$this->plugin_slug_id   = str_replace( '-', '_', $this->plugin_slug );
$this->options_name = $this->plugin_slug_id . '_options';
$this->options = get_option( $this->options_name );
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
        'name'    => __('Username', $this->plugin_slug), // Name of the field
        'type'    => 'text', // Field type
        'default' => false, // Default value
        'help'    => __('Enter a username.', $this->plugin_slug), // Toast message (optional)
        'timeout'    => 5000, // Dismiss notice after 5 seconds (Default 5 seconds)
      ),
      array(
        'name'    => __('Date', $this->plugin_slug),
        'type'    => 'text',
        'default' => false,
        'help'    => __('test.', $this->plugin_slug),
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
```

```
public function admin_init() {
  // Initialize settings
  $this->settings->init();
}

public function enqueue() {
  $this->settings->enqueue();
}
```

**To-Do**
- Currently only 2 field types are supported, but as we are actively developing software using this class, so we will be adding the additional field types soon.
- Menu Location Parameters


**Consider Contributing**  
We know this class will be useful to many in cutting down development times, but we would love help from the community. We are actively using this class for our software and will continue to build off of it, but we know it can become something greater, faster, with the help of the community. Feel free to submit a PR or submit any issues you have as we will be actively maintaining this.
