# phpci-jasmine-node-plugin
This is a plugin for [PHPCI](https://github.com/block8/phpci). It allows you
to run [jasmine-node](https://github.com/mhevery/jasmine-node) tests via PHPCI.

## Installation
*Note: This will not install `jasmine-node` for you. You will have to install that yourself.*

1. Navigate to your PHPCI path. `cd /path/to/phpci`
2. Edit the composer.json file. `nano composer.json`
3. Add `"austp\/phpci-jasmine-node-plugin": "~1.1"` in the `"require"` section.

        "require": {
            ...,
            ...,
            "austp\/phpci-jasmine-node-plugin": "~1.1"
        }
4. Download the plugin via composer. `composer update austp/phpci-jasmine-node-plugin`
5. Copy `build-plugins/jasminenode.js` to `/path/to/phpci/public/assets/js/build-plugins/jasminenode.js`

        cd /path/to/phpci/vendor/austp/phpci-jasmine-node-plugin/build-plugins
        cp jasminenode.js /path/to/phpci/public/assets/js/build-plugins/jasminenode.js

That's it as far as installation goes. Continue reading to see available options.


## Configuration
In order to configure PHPCI to run jasmine-node, you need to edit the `phpci.yml` file.
If you don't already have this file in your repository, [go ahead and add it](https://www.phptesting.org/wiki/Adding-PHPCI-Support-to-Your-Projects).
*Note: If you can't add a phpci.yml file to the repo, you can edit your project in PHPCI and configure it there.*

### Options
    executable: "/path/to/jasmine-node" | Full path to a jasmine-node executable.
    directory:  "specs/"                | The directory to run the tests on.
    log:        true                    | (optional) Log jasmine-node's output to PHPCI.

### phpci.yml
1. Navigate to your repository. `cd /path/to/repo`
2. Edit the phpci.yml file. `nano phpci.yml`
3. Add `\PHPCI_Jasmine_Node_Plugin\Jasmine_Node:` in the `"test"` section.

        test:
          ...:
            ...: ...
            ...: ...
          ...:
            ...: ...
          \PHPCI_Jasmine_Node_Plugin\Jasmine_Node:
4. Add your options under the `\PHPCI_Jasmine_Node_Plugin\Jasmine_Node:` line.

        \PHPCI_Jasmine_Node_Plugin\Jasmine_Node:
          executable: "/path/to/jasmine-node"
          directory: "specs/"
          log: true
