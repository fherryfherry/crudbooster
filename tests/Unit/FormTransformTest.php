<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class FormTransformTest extends TestCase
{
    public function testTransformFunctionBasic()
    {
        // Test fungsi transform dasar
        $transform = function ($value) {
            return strtoupper($value);
        };

        $result = $transform('john doe');
        $this->assertEquals('JOHN DOE', $result);
    }

    public function testTransformFunctionWithDateTime()
    {
        // Test transform untuk datetime field
        $transform = function ($value) {
            if ($value) {
                return date('d/m/Y H:i', strtotime($value));
            }
            return $value;
        };

        $result = $transform('2024-01-15 14:30:00');
        $this->assertEquals('15/01/2024 14:30', $result);
    }

    public function testTransformFunctionWithNullValue()
    {
        // Test transform dengan null value
        $transform = function ($value) {
            return $value ? strtoupper($value) : 'N/A';
        };

        $result = $transform(null);
        $this->assertEquals('N/A', $result);
    }

    public function testTransformFunctionWithComplexLogic()
    {
        // Test transform dengan logic yang lebih kompleks
        $transform = function ($value) {
            $statusMap = [
                'active' => '🟢 Active',
                'inactive' => '🔴 Inactive',
                'pending' => '🟡 Pending'
            ];
            return $statusMap[$value] ?? $value;
        };

        // Test transform function dengan berbagai status
        $this->assertEquals('🟢 Active', $transform('active'));
        $this->assertEquals('🔴 Inactive', $transform('inactive'));
        $this->assertEquals('🟡 Pending', $transform('pending'));
        $this->assertEquals('unknown', $transform('unknown'));
    }

    public function testTransformFunctionWithMultipleFormats()
    {
        // Test transform untuk berbagai format datetime
        $transform = function ($value) {
            if (!$value) return 'N/A';
            
            // Coba berbagai format
            $formats = [
                'Y-m-d\TH:i',
                'Y-m-d H:i:s',
                'd/m/Y H:i',
                'M d, Y H:i'
            ];
            
            foreach ($formats as $format) {
                $parsed = date($format, strtotime($value));
                if ($parsed !== false) {
                    return $parsed;
                }
            }
            
            return $value;
        };

        $this->assertEquals('2024-01-15T14:30', $transform('2024-01-15 14:30:00'));
        $this->assertEquals('2024-01-15T14:30', $transform('2024-01-15T14:30'));
        $this->assertEquals('2024-01-15T14:30', $transform('2024-01-15 14:30:00'));
    }

    public function testTransformFunctionWithConditionalFormatting()
    {
        // Test transform dengan conditional formatting
        $transform = function ($value) {
            if (!$value) return 'Not Set';
            
            // Jika format datetime, ubah ke format yang lebih readable
            if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $value)) {
                return date('d/m/Y H:i', strtotime($value));
            }
            
            // Jika format date saja, ubah ke format yang lebih readable
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
                return date('d/m/Y', strtotime($value));
            }
            
            // Jika format time saja, ubah ke format yang lebih readable
            if (preg_match('/^\d{2}:\d{2}:\d{2}$/', $value)) {
                return date('H:i', strtotime($value));
            }
            
            return $value;
        };

        $this->assertEquals('15/01/2024 14:30', $transform('2024-01-15 14:30:00'));
        $this->assertEquals('15/01/2024', $transform('2024-01-15'));
        $this->assertEquals('14:30', $transform('14:30:00'));
        $this->assertEquals('Not Set', $transform(null));
        $this->assertEquals('Not Set', $transform(''));
    }

    public function testTransformFunctionWithCustomDateTimeFormat()
    {
        // Test transform dengan format datetime custom
        $transform = function ($value) {
            if (!$value) return '-';
            
            // Format yang diminta user: date('Y-m-d\TH:i', strtotime($value))
            return date('Y-m-d\TH:i', strtotime($value));
        };

        $this->assertEquals('2024-01-15T14:30', $transform('2024-01-15 14:30:00'));
        $this->assertEquals('2024-01-15T14:30', $transform('2024-01-15T14:30:00'));
        $this->assertEquals('-', $transform(null));
        $this->assertEquals('-', $transform(''));
    }

    public function testTransformFunctionWithErrorHandling()
    {
        // Test transform dengan error handling
        $transform = function ($value) {
            try {
                if (!$value) return 'N/A';
                
                $timestamp = strtotime($value);
                if ($timestamp === false) {
                    return 'Invalid Date';
                }
                
                return date('d/m/Y H:i', $timestamp);
            } catch (\Exception $e) {
                return 'Error: ' . $e->getMessage();
            }
        };

        $this->assertEquals('15/01/2024 14:30', $transform('2024-01-15 14:30:00'));
        $this->assertEquals('N/A', $transform(null));
        $this->assertEquals('N/A', $transform(''));
        $this->assertEquals('Invalid Date', $transform('invalid-date'));
    }
}
