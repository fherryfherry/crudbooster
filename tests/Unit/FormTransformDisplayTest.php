<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class FormTransformDisplayTest extends TestCase
{
    public function testTransformDisplayFunctionBasic()
    {
        // Test fungsi transformDisplay dasar
        $transformDisplay = function ($value) {
            return strtoupper($value);
        };

        $result = $transformDisplay('john doe');
        $this->assertEquals('JOHN DOE', $result);
    }

    public function testTransformDisplayFunctionWithDateTime()
    {
        // Test transformDisplay untuk datetime field - format untuk display
        $transformDisplay = function ($value) {
            if ($value) {
                return date('d/m/Y H:i', strtotime($value));
            }
            return $value;
        };

        $result = $transformDisplay('2024-01-15 14:30:00');
        $this->assertEquals('15/01/2024 14:30', $result);
    }

    public function testTransformDisplayFunctionWithNullValue()
    {
        // Test transformDisplay dengan null value
        $transformDisplay = function ($value) {
            return $value ? strtoupper($value) : 'N/A';
        };

        $result = $transformDisplay(null);
        $this->assertEquals('N/A', $result);
    }

    public function testTransformDisplayFunctionWithEmptyValue()
    {
        // Test transformDisplay dengan empty value
        $transformDisplay = function ($value) {
            return $value ? $value : 'Not Set';
        };

        $result = $transformDisplay('');
        $this->assertEquals('Not Set', $result);
    }

    public function testTransformDisplayFunctionWithCurrency()
    {
        // Test transformDisplay untuk currency formatting
        $transformDisplay = function ($value) {
            if (is_numeric($value)) {
                return '$' . number_format($value, 2);
            }
            return $value;
        };

        $this->assertEquals('$1,234.56', $transformDisplay(1234.56));
        $this->assertEquals('$1,000.00', $transformDisplay(1000));
        $this->assertEquals('not a number', $transformDisplay('not a number'));
    }

    public function testTransformDisplayFunctionWithStatusBadge()
    {
        // Test transformDisplay untuk status dengan badge
        $transformDisplay = function ($value) {
            $statusMap = [
                'active' => '<span class="badge badge-success">🟢 Active</span>',
                'inactive' => '<span class="badge badge-danger">🔴 Inactive</span>',
                'pending' => '<span class="badge badge-warning">🟡 Pending</span>'
            ];
            return $statusMap[$value] ?? $value;
        };

        $this->assertEquals('<span class="badge badge-success">🟢 Active</span>', $transformDisplay('active'));
        $this->assertEquals('<span class="badge badge-danger">🔴 Inactive</span>', $transformDisplay('inactive'));
        $this->assertEquals('<span class="badge badge-warning">🟡 Pending</span>', $transformDisplay('pending'));
        $this->assertEquals('unknown', $transformDisplay('unknown'));
    }

    public function testTransformDisplayFunctionWithDateOnly()
    {
        // Test transformDisplay untuk date field - format untuk display
        $transformDisplay = function ($value) {
            if ($value) {
                return date('d/m/Y', strtotime($value));
            }
            return $value;
        };

        $this->assertEquals('15/01/2024', $transformDisplay('2024-01-15 14:30:00'));
        $this->assertEquals('15/01/2024', $transformDisplay('2024-01-15'));
        $this->assertEquals('', $transformDisplay(''));
    }

    public function testTransformDisplayFunctionWithTimeOnly()
    {
        // Test transformDisplay untuk time field - format untuk display
        $transformDisplay = function ($value) {
            if ($value) {
                return date('H:i A', strtotime($value));
            }
            return $value;
        };

        $this->assertEquals('14:30 PM', $transformDisplay('2024-01-15 14:30:00'));
        $this->assertEquals('14:30 PM', $transformDisplay('14:30:00'));
        $this->assertEquals('', $transformDisplay(''));
    }

    public function testTransformDisplayFunctionWithCustomFormat()
    {
        // Test transformDisplay dengan format custom untuk display
        $transformDisplay = function ($value) {
            if (!$value) return 'Not Available';
            
            // Format untuk display yang lebih readable
            if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $value)) {
                return date('F d, Y \a\t H:i', strtotime($value));
            }
            
            // Format untuk display date
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
                return date('F d, Y', strtotime($value));
            }
            
            // Format untuk display time
            if (preg_match('/^\d{2}:\d{2}:\d{2}$/', $value)) {
                return date('H:i A', strtotime($value));
            }
            
            return $value;
        };

        $this->assertEquals('January 15, 2024 at 14:30', $transformDisplay('2024-01-15 14:30:00'));
        $this->assertEquals('January 15, 2024', $transformDisplay('2024-01-15'));
        $this->assertEquals('14:30 PM', $transformDisplay('14:30:00'));
        $this->assertEquals('Not Available', $transformDisplay(''));
        $this->assertEquals('Not Available', $transformDisplay(null));
    }

    public function testTransformDisplayFunctionWithArrayHandling()
    {
        // Test transformDisplay untuk array handling
        $transformDisplay = function ($value) {
            if (!$value) return 'None';
            
            // Convert array to readable string for display
            if (is_array($value)) {
                return implode(', ', array_map('ucfirst', $value));
            }
            
            return $value;
        };

        $this->assertEquals('Apple, Banana, Orange', $transformDisplay(['apple', 'banana', 'orange']));
        $this->assertEquals('single', $transformDisplay('single'));
        $this->assertEquals('None', $transformDisplay(''));
        $this->assertEquals('None', $transformDisplay(null));
    }

    public function testTransformDisplayFunctionWithBoolean()
    {
        // Test transformDisplay untuk boolean values
        $transformDisplay = function ($value) {
            if (is_bool($value)) {
                return $value ? 'Yes' : 'No';
            }
            return $value;
        };

        $this->assertEquals('Yes', $transformDisplay(true));
        $this->assertEquals('No', $transformDisplay(false));
        $this->assertEquals('not boolean', $transformDisplay('not boolean'));
    }

    public function testTransformDisplayFunctionWithErrorHandling()
    {
        // Test transformDisplay dengan error handling
        $transformDisplay = function ($value) {
            try {
                if (!$value) return 'Not Available';
                
                $timestamp = strtotime($value);
                if ($timestamp === false) {
                    return 'Invalid Date';
                }
                
                return date('d/m/Y H:i', $timestamp);
            } catch (\Exception $e) {
                return 'Error: ' . $e->getMessage();
            }
        };

        $this->assertEquals('15/01/2024 14:30', $transformDisplay('2024-01-15 14:30:00'));
        $this->assertEquals('Not Available', $transformDisplay(null));
        $this->assertEquals('Not Available', $transformDisplay(''));
        $this->assertEquals('Invalid Date', $transformDisplay('invalid-date'));
    }

    public function testTransformDisplayFunctionWithMultipleConditions()
    {
        // Test transformDisplay dengan multiple conditions
        $transformDisplay = function ($value) {
            if (!$value) return 'Not Set';
            
            // Handle different data types for display
            if (is_numeric($value)) {
                return number_format($value, 2) . ' units';
            }
            
            if (is_string($value) && str_contains($value, '@')) {
                return '<a href="mailto:' . $value . '">' . $value . '</a>'; // Email link
            }
            
            if (is_string($value) && preg_match('/^\d{4}-\d{2}-\d{2}/', $value)) {
                return date('F d, Y', strtotime($value)); // Date
            }
            
            return ucfirst($value);
        };

        $this->assertEquals('1,234.56 units', $transformDisplay(1234.56));
        $this->assertEquals('<a href="mailto:john@example.com">john@example.com</a>', $transformDisplay('john@example.com'));
        $this->assertEquals('January 15, 2024', $transformDisplay('2024-01-15 14:30:00'));
        $this->assertEquals('Regular text', $transformDisplay('regular text'));
        $this->assertEquals('Not Set', $transformDisplay(''));
    }

    public function testTransformDisplayFunctionWithHtmlOutput()
    {
        // Test transformDisplay dengan HTML output untuk display
        $transformDisplay = function ($value) {
            if (!$value) return '<em>Not Available</em>';
            
            return '<strong>' . htmlspecialchars($value) . '</strong>';
        };

        $this->assertEquals('<strong>John Doe</strong>', $transformDisplay('John Doe'));
        $this->assertEquals('<em>Not Available</em>', $transformDisplay(''));
        $this->assertEquals('<em>Not Available</em>', $transformDisplay(null));
    }

    public function testTransformDisplayFunctionWithConditionalFormatting()
    {
        // Test transformDisplay dengan conditional formatting
        $transformDisplay = function ($value) {
            if (!$value) return 'N/A';
            
            // Conditional formatting based on value
            if (is_numeric($value)) {
                if ($value > 1000) {
                    return '<span class="text-green-600 font-bold">' . number_format($value) . '</span>';
                } elseif ($value > 100) {
                    return '<span class="text-yellow-600">' . number_format($value) . '</span>';
                } else {
                    return '<span class="text-red-600">' . number_format($value) . '</span>';
                }
            }
            
            return $value;
        };

        $this->assertEquals('<span class="text-green-600 font-bold">1,500</span>', $transformDisplay(1500));
        $this->assertEquals('<span class="text-yellow-600">500</span>', $transformDisplay(500));
        $this->assertEquals('<span class="text-red-600">50</span>', $transformDisplay(50));
        $this->assertEquals('N/A', $transformDisplay(''));
    }
}
