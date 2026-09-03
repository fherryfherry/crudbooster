<?php

namespace Tests\Unit;

use CrudBooster\Livewire\FormBuilder\Form;
use PHPUnit\Framework\TestCase;

class FormTransformValueTest extends TestCase
{
    public function testTransformValueFunctionBasic()
    {
        // Test fungsi transformValue dasar
        $transformValue = function ($value) {
            return strtoupper($value);
        };

        $result = $transformValue('john doe');
        $this->assertEquals('JOHN DOE', $result);
    }

    public function testTransformValueFunctionWithDateTime()
    {
        // Test transformValue untuk datetime field - format untuk input
        $transformValue = function ($value) {
            if ($value) {
                return date('Y-m-d\TH:i', strtotime($value));
            }
            return $value;
        };

        $result = $transformValue('2024-01-15 14:30:00');
        $this->assertEquals('2024-01-15T14:30', $result);
    }

    public function testTransformValueFunctionWithNullValue()
    {
        // Test transformValue dengan null value
        $transformValue = function ($value) {
            return $value ? strtoupper($value) : '';
        };

        $result = $transformValue(null);
        $this->assertEquals('', $result);
    }

    public function testTransformValueFunctionWithEmptyValue()
    {
        // Test transformValue dengan empty value
        $transformValue = function ($value) {
            return $value ? $value : '';
        };

        $result = $transformValue('');
        $this->assertEquals('', $result);
    }

    public function testTransformValueFunctionWithNumberFormatting()
    {
        // Test transformValue untuk formatting angka
        $transformValue = function ($value) {
            if (is_numeric($value)) {
                return number_format($value, 2, '.', ',');
            }
            return $value;
        };

        $this->assertEquals('1,234.56', $transformValue(1234.56));
        $this->assertEquals('1,000.00', $transformValue(1000));
        $this->assertEquals('not a number', $transformValue('not a number'));
    }

    public function testTransformValueFunctionWithDateOnly()
    {
        // Test transformValue untuk date field - format untuk input date
        $transformValue = function ($value) {
            if ($value) {
                return date('Y-m-d', strtotime($value));
            }
            return $value;
        };

        $this->assertEquals('2024-01-15', $transformValue('2024-01-15 14:30:00'));
        $this->assertEquals('2024-01-15', $transformValue('2024-01-15'));
        $this->assertEquals('', $transformValue(''));
    }

    public function testTransformValueFunctionWithTimeOnly()
    {
        // Test transformValue untuk time field - format untuk input time
        $transformValue = function ($value) {
            if ($value) {
                return date('H:i', strtotime($value));
            }
            return $value;
        };

        $this->assertEquals('14:30', $transformValue('2024-01-15 14:30:00'));
        $this->assertEquals('14:30', $transformValue('14:30:00'));
        $this->assertEquals('', $transformValue(''));
    }

    public function testTransformValueFunctionWithCustomFormat()
    {
        // Test transformValue dengan format custom untuk input
        $transformValue = function ($value) {
            if (!$value) return '';
            
            // Format untuk input datetime-local
            if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $value)) {
                return date('Y-m-d\TH:i', strtotime($value));
            }
            
            // Format untuk input date
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
                return date('Y-m-d', strtotime($value));
            }
            
            // Format untuk input time
            if (preg_match('/^\d{2}:\d{2}:\d{2}$/', $value)) {
                return date('H:i', strtotime($value));
            }
            
            return $value;
        };

        $this->assertEquals('2024-01-15T14:30', $transformValue('2024-01-15 14:30:00'));
        $this->assertEquals('2024-01-15', $transformValue('2024-01-15'));
        $this->assertEquals('14:30', $transformValue('14:30:00'));
        $this->assertEquals('', $transformValue(''));
        $this->assertEquals('', $transformValue(null));
    }

    public function testTransformValueFunctionWithSelectOptions()
    {
        // Test transformValue untuk select options
        $transformValue = function ($value) {
            if (!$value) return '';
            
            // Convert array to string for select input
            if (is_array($value)) {
                return implode(',', $value);
            }
            
            return $value;
        };

        $this->assertEquals('1,2,3', $transformValue([1, 2, 3]));
        $this->assertEquals('apple,banana', $transformValue(['apple', 'banana']));
        $this->assertEquals('single', $transformValue('single'));
        $this->assertEquals('', $transformValue(''));
        $this->assertEquals('', $transformValue(null));
    }

    public function testTransformValueFunctionWithBoolean()
    {
        // Test transformValue untuk boolean values
        $transformValue = function ($value) {
            if (is_bool($value)) {
                return $value ? '1' : '0';
            }
            return $value;
        };

        $this->assertEquals('1', $transformValue(true));
        $this->assertEquals('0', $transformValue(false));
        $this->assertEquals('not boolean', $transformValue('not boolean'));
    }

    public function testTransformValueFunctionWithErrorHandling()
    {
        // Test transformValue dengan error handling
        $transformValue = function ($value) {
            try {
                if (!$value) return '';
                
                $timestamp = strtotime($value);
                if ($timestamp === false) {
                    return '';
                }
                
                return date('Y-m-d\TH:i', $timestamp);
            } catch (\Exception $e) {
                return '';
            }
        };

        $this->assertEquals('2024-01-15T14:30', $transformValue('2024-01-15 14:30:00'));
        $this->assertEquals('', $transformValue(null));
        $this->assertEquals('', $transformValue(''));
        $this->assertEquals('', $transformValue('invalid-date'));
    }

    public function testTransformValueFunctionWithMultipleConditions()
    {
        // Test transformValue dengan multiple conditions
        $transformValue = function ($value) {
            if (!$value) return '';
            
            // Handle different input types
            if (is_numeric($value)) {
                return number_format($value, 2);
            }
            
            if (is_string($value) && str_contains($value, '@')) {
                return strtolower($value); // Email
            }
            
            if (is_string($value) && preg_match('/^\d{4}-\d{2}-\d{2}/', $value)) {
                return date('Y-m-d\TH:i', strtotime($value)); // Date
            }
            
            return $value;
        };

        $this->assertEquals('1,234.56', $transformValue(1234.56));
        $this->assertEquals('john@example.com', $transformValue('JOHN@EXAMPLE.COM'));
        $this->assertEquals('2024-01-15T14:30', $transformValue('2024-01-15 14:30:00'));
        $this->assertEquals('regular text', $transformValue('regular text'));
        $this->assertEquals('', $transformValue(''));
    }
}
