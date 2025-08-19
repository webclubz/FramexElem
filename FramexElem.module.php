<?php

namespace ProcessWire;

class FramexElem extends WireData implements Module {

    private string $component;
    private array $parameters;

    /** NEW: named slots storage */
    private array $slots = [];

    public static function getModuleInfo() {
        return [
            'title' => 'Framex Elements',
            'version' => '0.0.2',
            'summary' => 'A module for reusable components in ProcessWire (with named slots)',
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

            ob_start(); // default slot buffer (όπως πριν)
        }
    }

    /** NEW: set named slot content via closure */
public function slot(string $name, callable $producer): self
{
    ob_start();
    try {
        $producer();
        $this->slots[$name] = ob_get_clean();
    } catch (\Throwable $e) {
        ob_end_clean();
        throw $e;
    }
    return $this;
}


public function close(): void
{
    $content = ob_get_clean(); // default slot
    ob_start();

    // Extract parameters to local variables
    extract($this->parameters, EXTR_SKIP);

    // Include the component file
    include($this->component);

    $component = ob_get_clean();

    // 1) Named slots: @slot:name  (πρώτα)
    $component = preg_replace_callback('/@slot:([a-zA-Z0-9_\-]+)/', function ($m) {
        $name = $m[1];
        return $this->slots[$name] ?? '';
    }, $component);

    // 2) Default slot: @slot  (μόνο όταν ΔΕΝ ακολουθεί :)
    $component = preg_replace('/@slot(?!:)/', $content, $component);

    echo $component;
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
