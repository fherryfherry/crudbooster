<?php

namespace CrudBooster\Components\ActionButton;

class ActionButtonOptions
{
    private array $options = [];
    private string $currentModuleKey = '';
    private static array $moduleOptions = []; // Static storage per module

    public function __construct()
    {

    }

    /**
     * Set the current module key for action button isolation
     */
    public function setModuleKey(string $moduleKey): void
    {
        $this->currentModuleKey = $moduleKey;
        
        // Initialize module storage if not exists
        if (!isset(self::$moduleOptions[$moduleKey])) {
            self::$moduleOptions[$moduleKey] = [];
        }
    }

    /**
     * Get the current module key
     */
    public function getModuleKey(): string
    {
        return $this->currentModuleKey;
    }

    public function setOption(string $label, array $option): void
    {
        // Add module key to the option for better isolation
        $option['module_key'] = $this->currentModuleKey;
        
        // Store in module-specific storage
        if ($this->currentModuleKey) {
            self::$moduleOptions[$this->currentModuleKey][$label] = $option;
        } else {
            // Fallback to global storage for backward compatibility
            $this->options[$label] = $option;
        }
    }

    public function getOptions(): array
    {
        // Return current module options if module key is set
        if ($this->currentModuleKey && isset(self::$moduleOptions[$this->currentModuleKey])) {
            return self::$moduleOptions[$this->currentModuleKey];
        }
        
        // Fallback to global options
        return $this->options;
    }

    /**
     * Get options filtered by module key
     */
    public function getOptionsByModule(string $moduleKey): array
    {
        return self::$moduleOptions[$moduleKey] ?? [];
    }

    /**
     * Clear all action button options
     * This is useful for sub modules to avoid mixing with parent module action buttons
     */
    public function clearOptions(): void
    {
        $this->options = [];
        self::$moduleOptions = [];
    }

    /**
     * Clear options for specific module
     */
    public function clearOptionsByModule(string $moduleKey): void
    {
        unset(self::$moduleOptions[$moduleKey]);
    }

    /**
     * Clear options from other modules (keep only current module)
     */
    public function clearOptionsFromOtherModules(string $moduleKey): void
    {
        // Keep only the specified module
        $currentModuleOptions = self::$moduleOptions[$moduleKey] ?? [];
        self::$moduleOptions = [$moduleKey => $currentModuleOptions];
    }

    /**
     * Get all module options (for debugging/testing)
     */
    public static function getAllModuleOptions(): array
    {
        return self::$moduleOptions;
    }
}
