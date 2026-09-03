<?php

namespace CrudBooster\Components\ActionButton;

use Closure;
use CrudBooster\Modules\Role\Enum\RolePermission;

class ActionButton
{
    private array $option;

    public function __construct(array $option)
    {
        $this->option = $option;
        $this->updateOption();
    }

    /**
     * To set visible button
     * @param Closure|bool $visible
     * @return $this
     */
    public function visible(Closure|bool $visible)
    {
        $this->option['visible'] = $visible;
        return $this->updateOption();
    }

    public function icon(string $icon)
    {
        $this->option['icon'] = $icon;
        return $this->updateOption();
    }
    public function buttonGreen()
    {
        $this->option['class'] = 'btn btn-success';
        return $this->updateOption();
    }
    public function buttonRed()
    {
        $this->option['class'] = 'btn btn-danger';
        return $this->updateOption();
    }
    public function buttonBlue()
    {
        $this->option['class'] = 'btn btn-primary';
        return $this->updateOption();
    }
    public function buttonYellow()
    {
        $this->option['class'] = 'btn btn-warning';
        return $this->updateOption();
    }
    public function buttonDefault()
    {
        $this->option['class'] = 'btn btn-default';
        return $this->updateOption();
    }
    public function buttonClass(string $className)
    {
        $this->option['class'] = $className;
        return $this->updateOption();
    }
    public function openTarget(string $target)
    {
        $this->option['target'] = $target;
        return $this->updateOption();
    }
    public function confirmation()
    {
        $this->option['confirm'] = true;
        return $this->updateOption();
    }

    /**
     * To add permission to the button
     * @param RolePermission|RolePermission[] $permission
     * @return $this
     */
    public function permission(array|RolePermission $permission): static
    {
        if(is_array($permission)) {
            foreach ($permission as $p) {
                if(!$p instanceof RolePermission) {
                    throw new \InvalidArgumentException('Permission must be instance of RolePermission');
                }
            }
        }
        $this->option['permission'] = $permission;
        return $this->updateOption();
    }

    private function updateOption(): static
    {
        app(ActionButtonOptions::class)->setOption($this->option['label'], $this->option);
        return $this;
    }

    public static function __getOption(): array
    {
        return app(ActionButtonOptions::class)->getOptions();
    }

    /**
     * Get options filtered by module key
     */
    public static function __getOptionsByModule(string $moduleKey): array
    {
        return app(ActionButtonOptions::class)->getOptionsByModule($moduleKey);
    }

    /**
     * Set module key for action button isolation
     */
    public static function __setModuleKey(string $moduleKey): void
    {
        app(ActionButtonOptions::class)->setModuleKey($moduleKey);
    }

    /**
     * Clear all action button options
     * This is useful for sub modules to avoid mixing with parent module action buttons
     */
    public static function __clearOptions(): void
    {
        app(ActionButtonOptions::class)->clearOptions();
    }

    /**
     * Clear options for specific module
     */
    public static function __clearOptionsByModule(string $moduleKey): void
    {
        app(ActionButtonOptions::class)->clearOptionsByModule($moduleKey);
    }
}
