### README.md Content


# FramexElem Module

FramexElem is a ProcessWire module that allows for the creation and use of reusable components within the `site/templates/components` directory.

## Installation

1. Place the `FramexElem` module in the `site/modules/` directory of your ProcessWire installation.
2. Install the module from the ProcessWire admin interface.

## Usage

### Simple Usage

Create a simple component and display it with no parameters.

```php
// Using the FramexElem module
$elem = new \ProcessWire\FramexElem('simpleComponent');
$elem->close();
```

### Using Parameters

Create a component and pass some parameters to it.

```php
// Using the FramexElem module with parameters
$parameters = [
    'title' => 'Welcome',
    'message' => 'Hello, world!'
];
$elem = new \ProcessWire\FramexElem('welcomeComponent', $parameters);
$elem->close();
```

### Using `widget` Static Method

Create a component using the static `widget` method for a cleaner syntax.

```php
// Using the widget method to create and display the component
\ProcessWire\FramexElem::widget('widgetComponent', ['name' => 'John Doe'])->close();
```

### Setting and Getting Parameters

Dynamically set and get parameters.

```php
// Initialize the component
$elem = new \ProcessWire\FramexElem('dynamicComponent');

// Set parameters
$elem->setParameter('greeting', 'Hello');
$elem->setParameter('name', 'Jane Doe');

// Get and print a parameter
echo $elem->getParameter('greeting'); // Outputs: Hello

// Display the component
$elem->close();
```

### Removing Parameters

Remove a parameter before displaying the component.

```php
// Initialize the component with parameters
$elem = new \ProcessWire\FramexElem('removeParameterComponent', ['user' => 'John', 'status' => 'active']);

// Remove a parameter
$elem->removeParameter('status');

// Display the component
$elem->close();
```

### Complex Example

Combining multiple methods to dynamically update the component.

```php
// Initialize the component with initial parameters
$elem = new \ProcessWire\FramexElem('complexComponent', ['title' => 'Initial Title']);

// Dynamically update parameters
$elem->setParameter('title', 'Updated Title');
$elem->setParameter('description', 'This is a detailed description.');

// Remove an unwanted parameter
$elem->removeParameter('title');

// Get a parameter and modify it
$currentDescription = $elem->getParameter('description');
$elem->setParameter('description', $currentDescription . ' Additional text.');

// Display the component
$elem->close();
```

## Methods

### `__construct(string $component, array $parameters = [])`

Initializes a new component with the specified parameters.

### `close()`

Renders the component and replaces the `@slot` with the output buffer content.

### `widget(string $component, array $parameters = [])`

Static method to create and return a new `FramexElem` instance.

### `setParameter(string $key, $value)`

Sets a single parameter.

### `getParameter(string $key)`

Gets a single parameter.

### `removeParameter(string $key)`

Removes a parameter.

### `getParameters()`

Returns all parameters.


## License

This module is open-source and available under the MIT license.

