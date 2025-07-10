<?php

namespace ProcessWire;

class FramexElem extends WireData implements Module {

    private string $component;
    private array $parameters;

    public static function getModuleInfo() {
        return [
            'title' => 'Framex Elements',
            'version' => '0.0.1',
            'summary' => 'A module for reusable components in ProcessWire',
            'author' => 'Prodigital Framex',
            'href' => 'http://grmatics.com/',
            'singular' => true,
            'autoload' => false,
        ];
    }

    public function __construct(string $component = '', array $parameters = [])
    {
        if (!empty($component)) {
            $this->parameters = $parameters;

            // Convert component path to directory separator format
            $component = str_replace(['/', '.'], DIRECTORY_SEPARATOR, $component);

            // Define potential paths for the component
            $component_fullpath = $this->config->paths->templates . "components" . DIRECTORY_SEPARATOR . $component . ".php";
            $component_index_fullpath = $this->config->paths->templates . "components" . DIRECTORY_SEPARATOR . $component . DIRECTORY_SEPARATOR . "index.php";

            // Determine which path exists and set the component path
            if (file_exists($component_fullpath)) {
                $this->component = $component_fullpath;
            } elseif (file_exists($component_index_fullpath)) {
                $this->component = $component_index_fullpath;
            } else {
                throw new WireException("Component not found: $component");
            }

            ob_start();
        }
    }

    public function close(): void
    {
        $content = ob_get_clean();
        ob_start();

        // Extract parameters to local variables
        extract($this->parameters);

        // Include the component file
        include($this->component);

        $component = ob_get_clean();
        echo str_replace('@slot', $content, $component);
    }

    public static function widget(string $component, array $parameters = []): self
    {
        return new self($component, $parameters);
    }

    // Add a method to set a single parameter
    public function setParameter(string $key, $value): void
    {
        $this->parameters[$key] = $value;
    }

    // Add a method to get a single parameter
    public function getParameter(string $key)
    {
        return $this->parameters[$key] ?? null;
    }

    // Add a method to remove a parameter
    public function removeParameter(string $key): void
    {
        unset($this->parameters[$key]);
    }

    // Add a method to get all parameters
    public function getParameters(): array
    {
        return $this->parameters;
    }
}
