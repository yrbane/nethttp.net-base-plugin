# nethttp.net Base Plugin

![WordPress](https://img.shields.io/badge/WordPress-Compatible-brightgreen)
![PHP](https://img.shields.io/badge/PHP-7.4%2B-blue)
![License](https://img.shields.io/badge/License-GPLv3-blue)

## Description

`nethttp.net Base Plugin` is a foundational class designed to streamline the development of custom plugins for WordPress. It provides an organized structure and essential features for rapidly creating custom extensions. This class simplifies translation management, activation message display, and offers a robust foundation for adding plugin-specific functionality. Developers can extend this class by creating a child class to implement project-specific features, all while benefiting from a well-documented and ready-to-use structure.

## Features

- Streamline custom WordPress plugin development.
- Simplify translation management.
- Display activation messages.
- Organized structure for extending functionality.
- Well-documented codebase.
- GitHub integration.

## Installation

1. Download the latest release from the [GitHub repository](https://github.com/yrbane/nethttp.net-base-plugin/releases).
2. Upload the plugin folder to your WordPress plugins directory.
3. Activate the plugin through the WordPress admin panel.

## Usage

To use and extend the `nethttp.net Base Plugin` in your own plugin:

```php
// Include the Base Plugin file
if (!class_exists('BasePlugin')) {
    include_once(realpath(plugin_dir_path(__FILE__) . '../nethttp.net-base-plugin/nethttp.net-base-plugin.php'));
}

// Output an error if nethttp.net-base-plugin is not installed!
if (!class_exists('BasePlugin')) {
    add_action('admin_notices', function () {
        echo '<div class="error"><p>🙃 ' . __('You should install and activate nethttp.net-base-plugin. You can find it on ') .
            '<a href="https://github.com/yrbane/nethttp.net-base-plugin">GitHub</a>!</p></div>';
    });
    return;
}

// Create a class that extends BasePlugin class
class MyPlugin extends BasePlugin {
    // Your custom code here
}

// Instantiate the class passing the plugin file path as a parameter
new MyPlugin(__FILE__);

```

## Contributing

Contributions are welcome! Feel free to open issues and pull requests.

## License

This project is licensed under the GNU General Public License v3.0 - see the [LICENSE](LICENSE.md) file for details.

## Author

- [Barney](https://github.com/yrbane)

## Links

- [GitHub Repository](https://github.com/yrbane/nethttp.net-base-plugin)
- [WordPress Plugin Directory](https://wordpress.org/plugins/nethttp-net-base-plugin/)

