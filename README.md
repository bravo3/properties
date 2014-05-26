YAML Property Loader
====================

This property loader is designed for unit testing and uses the singleton design pattern to achieve this. Consider a
dependency injection container if you wanted a property loader for the main application.

Usage
-----

    Conf::init('/path');
    $c = Conf::getInstance();
    $property = $c['some.property'];

or simply -

    Conf::init('/path');
    $property = Conf::get('some.property');

Defaults can be used here too -

    $property = Conf::get('some.property', 'default value');


Config File
-----------

The path in the init line should point to where the properties.yml file should be, if the properties.yml file isn't
present, it will look for a properties.yml.dist file. You can change the properties filename with the `init` functions
second parameter.

Properties
----------

YAML arrays are delimited in the property key using periods.

properties.yml:

    some:
        property: hello world

Code:

    echo Conf::get('some.property');   // hello world


