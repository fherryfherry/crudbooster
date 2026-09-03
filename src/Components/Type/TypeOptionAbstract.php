<?php

namespace CrudBooster\Components\Type;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionMethod;
use ReflectionParameter;

class TypeOptionAbstract
{
    private array $inputEvents = [];
    public function __construct(public $option = [])
    {}

    public static function option(): static
    {
        return new static();
    }

    public function __getOption(): array
    {
        $this->option['inputEvents'] = $this->getInputEvents() ?? [];
        return $this->option;
    }

    public function html(): static
    {
        $this->option['html'] = true;
        return $this;
    }

    private function getInputEvents(): string
    {
        return implode('; ', $this->inputEvents);
    }

    public function uppercase(): static
    {
        $this->option['uppercase'] = [
            'onSave'=>fn($value) => strtoupper($value),
            'onView'=>fn($value) => strtoupper($value)
        ];
        $this->inputEvents[] = "event.target.value = event.target.value.toUpperCase()";
        return $this;
    }

    public function lowercase(): static
    {
        $this->option['lowercase'] = [
            'onSave'=>fn($value) => strtolower($value),
            'onView'=>fn($value) => strtolower($value)
        ];
        $this->inputEvents[] = "event.target.value = event.target.value.toLowerCase()";
        return $this;
    }

    public function noSpace(): static
    {
        $this->option['noSpace'] = [
            'onSave'=>fn($value) => preg_replace('/\\s+/', '', $value),
            'onView'=>fn($value) => preg_replace('/\\s+/', '', $value)
        ];
        $this->inputEvents[] = "event.target.value = event.target.value.replace(/\\s+/g, '')";
        return $this;
    }

    /**
     * This feature is used to remove special characters
     * @param array|string|null $except E.g: ['-', '_']
     * @return $this
     */
    public function noSpecialChar(array|string $except = null): static
    {
        if(is_string($except)) {
            $except = [$except];
        }

        $implodeException = $except ? implode('', $except) : null;
        $this->option['noSpecialChar'] = [
            'onSave'=>fn($value) => preg_replace('/[^a-zA-Z0-9\\s' . $implodeException . ']/', '', $value),
            'onView'=>fn($value) => preg_replace('/[^a-zA-Z0-9\\s' . $implodeException . ']/', '', $value)
        ];
        $this->inputEvents[] = "event.target.value = event.target.value.replace(/[^a-zA-Z0-9\\s" . $implodeException . "]/g, '')";
        return $this;
    }

    /**
     * This feature is used to remove special characters and space
     * @param array|string|null $except E.g: ['-', '_']
     * @return $this
     */
    public function noSpecialCharAndSpace(array|string $except = null): static
    {
        if(is_string($except)) {
            $except = [$except];
        }
        $implodeException = $except ? implode('', $except) : null;
        $this->option['noSpecialCharAndSpace'] = [
            'onSave'=>fn($value) => preg_replace('/[^a-zA-Z0-9' . $implodeException . ']/', '', $value),
            'onView'=>fn($value) => preg_replace('/[^a-zA-Z0-9' . $implodeException . ']/', '', $value)
        ];
        $this->inputEvents[] = "event.target.value = event.target.value.replace(/[^a-zA-Z0-9" . $implodeException . "]/g, '')";
        return $this;
    }

    public function numeric(): static
    {
        $this->option['numeric'] = [
            'onSave'=>fn($value) => preg_replace('/[^0-9]/', '', $value),
            'onView'=>fn($value) => preg_replace('/[^0-9]/', '', $value)
        ];
        $this->inputEvents[] = "event.target.value = event.target.value.replace(/[^0-9]/g, '')";
        return $this;
    }

    public function nonNumeric(): static
    {
        $this->option['nonNumeric'] = [
          'onSave'=>fn($value) => preg_replace('/[0-9]/', '', $value),
            'onView'=>fn($value) => preg_replace('/[0-9]/', '', $value)
        ];
        $this->inputEvents[] = "event.target.value = event.target.value.replace(/[0-9]/g, '')";
        return $this;
    }

    public function numberFormat(): static
    {
        $this->option['numberFormat'] = [
            'onSave'=>fn($value) => str_replace(',', '', $value),
            'onView'=>fn($value) => number_format(preg_replace('/[^0-9]/', '', $value))
        ];
        $this->inputEvents[] = "event.target.value = event.target.value.replace(/[^0-9]/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, ',');";
        return $this;
    }

    public function phoneFormat(): static
    {
        $this->option['phoneFormat'] = [
            'onSave'=>fn($value) => $value, // we want as is formatted phone number
            'onView'=>fn($value) => $value
        ];
        $this->inputEvents[] = "event.target.value = event.target.value.replace(/[^0-9]/g, '').replace(/(\\d{4})(?=\\d)/g, '$1-')";
        return $this;
    }
}
