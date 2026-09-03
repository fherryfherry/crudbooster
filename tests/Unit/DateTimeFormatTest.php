<?php

namespace Tests\Unit;

use CrudBooster\Components\Type\DateTime\Function\WithDateTime;
use PHPUnit\Framework\TestCase;

class DateTimeFormatTest extends TestCase
{
    use WithDateTime;

    private array $formData = [];
    private array $formColumns = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->formData = [];
        $this->formColumns = [];
    }

    public function testDateTimeFormatForFormDataLoading()
    {
        // Simulate database datetime value
        $this->formData['created_at'] = '2024-01-15 14:30:00';
        
        $this->formColumns = [
            [
                'type' => 'datetime',
                'key' => 'created_at'
            ]
        ];

        // Call the method that formats datetime for form display
        $this->__datetimeFormGetData(null, [], null);

        // Assert that the datetime is formatted correctly for HTML datetime-local input
        $this->assertEquals('2024-01-15T14:30', $this->formData['created_at']);
    }

    public function testDateTimeFormatForFormSaving()
    {
        // Simulate datetime-local input value
        $this->formData['created_at'] = '2024-01-15T14:30';
        
        $this->formColumns = [
            [
                'type' => 'datetime',
                'key' => 'created_at'
            ]
        ];

        // Call the method that formats datetime for database saving
        $this->__datetimeFormSaving(null, [], null);

        // Assert that the datetime is formatted correctly for database
        $this->assertEquals('2024-01-15 14:30:00', $this->formData['created_at']);
    }

    public function testDateTimeFormatWithEmptyValue()
    {
        // Test with empty value
        $this->formData['created_at'] = '';
        
        $this->formColumns = [
            [
                'type' => 'datetime',
                'key' => 'created_at'
            ]
        ];

        // Call the methods
        $this->__datetimeFormGetData(null, [], null);
        $this->__datetimeFormSaving(null, [], null);

        // Assert that empty values remain empty
        $this->assertEquals('', $this->formData['created_at']);
    }

    public function testDateTimeFormatWithNullValue()
    {
        // Test with null value
        $this->formData['created_at'] = null;
        
        $this->formColumns = [
            [
                'type' => 'datetime',
                'key' => 'created_at'
            ]
        ];

        // Call the methods
        $this->__datetimeFormGetData(null, [], null);
        $this->__datetimeFormSaving(null, [], null);

        // Assert that null values remain null
        $this->assertNull($this->formData['created_at']);
    }

    public function testNonDateTimeFieldsAreNotAffected()
    {
        // Test that non-datetime fields are not affected
        $this->formData['title'] = 'Test Title';
        $this->formData['created_at'] = '2024-01-15 14:30:00';
        
        $this->formColumns = [
            [
                'type' => 'text',
                'key' => 'title'
            ],
            [
                'type' => 'datetime',
                'key' => 'created_at'
            ]
        ];

        // Call the methods
        $this->__datetimeFormGetData(null, [], null);
        $this->__datetimeFormSaving(null, [], null);

        // Assert that text field is not affected
        $this->assertEquals('Test Title', $this->formData['title']);
        // Assert that datetime field is properly formatted
        $this->assertEquals('2024-01-15 14:30:00', $this->formData['created_at']);
    }

    /**
     * Mock method to simulate getFormColumns behavior
     */
    protected function getFormColumns($includeClosure = false): array
    {
        return $this->formColumns;
    }
}
