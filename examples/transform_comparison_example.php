<?php

use CrudBooster\Livewire\FormBuilder\Form;

/**
 * Example: Comparison between transform, transformValue, and transformDisplay functions
 * 
 * This example demonstrates the differences between the three transform functions:
 * - transform: General transformation for all contexts
 * - transformValue: Transformation for input fields only
 * - transformDisplay: Transformation for detail page display only
 */

class UserForm extends BaseFormComponent
{
    public function init(): void
    {
        $this->makeForm([
            // Example 1: DateTime field with all three transforms
            Form::add(label: 'Created At', key: 'created_at', type: 'datetime')
                ->transformValue(function($value) {
                    // Transform for input field (HTML datetime-local format)
                    return $value ? date('Y-m-d\TH:i', strtotime($value)) : '';
                })
                ->transform(function($value) {
                    // General transform (affects all contexts)
                    return $value ? date('Y-m-d H:i', strtotime($value)) : '-';
                })
                ->transformDisplay(function($value) {
                    // Transform for detail page display only
                    return $value ? date('F d, Y \a\t H:i A', strtotime($value)) : 'Not Available';
                }),

            // Example 2: Status field with badge display
            Form::add(label: 'Status', key: 'status', type: 'text')
                ->transformValue(function($value) {
                    // Transform for input field (clean value)
                    return $value ?: '';
                })
                ->transform(function($value) {
                    // General transform (affects all contexts)
                    return $value ? ucfirst($value) : '-';
                })
                ->transformDisplay(function($value) {
                    // Transform for detail page display only (with HTML badge)
                    $statusMap = [
                        'active' => '<span class="badge badge-success">🟢 Active</span>',
                        'inactive' => '<span class="badge badge-danger">🔴 Inactive</span>',
                        'pending' => '<span class="badge badge-warning">🟡 Pending</span>'
                    ];
                    return $statusMap[$value] ?? $value;
                }),

            // Example 3: Price field with currency formatting
            Form::add(label: 'Price', key: 'price', type: 'number')
                ->transformValue(function($value) {
                    // Transform for input field (remove formatting)
                    return is_numeric($value) ? number_format($value, 2, '.', '') : '';
                })
                ->transform(function($value) {
                    // General transform (affects all contexts)
                    return is_numeric($value) ? number_format($value, 2) : '-';
                })
                ->transformDisplay(function($value) {
                    // Transform for detail page display only (with currency symbol)
                    return is_numeric($value) ? '$' . number_format($value, 2) : 'N/A';
                }),

            // Example 4: Email field with link in display
            Form::add(label: 'Email', key: 'email', type: 'email')
                ->transformValue(function($value) {
                    // Transform for input field (lowercase)
                    return $value ? strtolower($value) : '';
                })
                ->transform(function($value) {
                    // General transform (affects all contexts)
                    return $value ?: '-';
                })
                ->transformDisplay(function($value) {
                    // Transform for detail page display only (with mailto link)
                    return $value ? '<a href="mailto:' . $value . '" class="text-blue-600 hover:underline">' . $value . '</a>' : 'Not Set';
                }),

            // Example 5: Tags field with array handling
            Form::add(label: 'Tags', key: 'tags', type: 'select')
                ->transformValue(function($value) {
                    // Transform for input field (array to string)
                    return is_array($value) ? implode(',', $value) : $value;
                })
                ->transform(function($value) {
                    // General transform (affects all contexts)
                    if (is_array($value)) {
                        return implode(', ', $value);
                    }
                    return $value ?: '-';
                })
                ->transformDisplay(function($value) {
                    // Transform for detail page display only (with badges)
                    if (is_array($value) && !empty($value)) {
                        $badges = array_map(function($tag) {
                            return '<span class="badge badge-info mr-1">' . ucfirst($tag) . '</span>';
                        }, $value);
                        return implode('', $badges);
                    }
                    return 'No tags';
                }),

            // Example 6: Boolean field with different displays
            Form::add(label: 'Active', key: 'is_active', type: 'checkbox')
                ->transformValue(function($value) {
                    // Transform for input field (boolean to string)
                    return $value ? '1' : '0';
                })
                ->transform(function($value) {
                    // General transform (affects all contexts)
                    return $value ? 'Yes' : 'No';
                })
                ->transformDisplay(function($value) {
                    // Transform for detail page display only (with colored text)
                    return $value ? '<span class="text-green-600 font-bold">✓ Active</span>' : '<span class="text-red-600">✗ Inactive</span>';
                }),
        ]);
    }
}

/**
 * Example: Using only transformDisplay for detail page specific formatting
 */
class DetailOnlyForm extends BaseFormComponent
{
    public function init(): void
    {
        $this->makeForm([
            // Only use transformDisplay for detail page formatting
            Form::add(label: 'Description', key: 'description', type: 'textarea')
                ->transformDisplay(function($value) {
                    // Only format for detail page display
                    return $value ? nl2br(htmlspecialchars($value)) : '<em>No description available</em>';
                }),

            // Phone number with clickable link in detail page
            Form::add(label: 'Phone', key: 'phone', type: 'text')
                ->transformDisplay(function($value) {
                    // Only add tel: link in detail page
                    return $value ? '<a href="tel:' . $value . '" class="text-blue-600 hover:underline">' . $value . '</a>' : 'Not provided';
                }),

            // File size with human readable format in detail page
            Form::add(label: 'File Size', key: 'file_size', type: 'number')
                ->transformDisplay(function($value) {
                    // Only format file size in detail page
                    if (!$value) return 'N/A';
                    
                    $units = ['B', 'KB', 'MB', 'GB'];
                    $size = $value;
                    $unit = 0;
                    
                    while ($size >= 1024 && $unit < count($units) - 1) {
                        $size /= 1024;
                        $unit++;
                    }
                    
                    return round($size, 2) . ' ' . $units[$unit];
                }),

            // Rating with stars in detail page
            Form::add(label: 'Rating', key: 'rating', type: 'number')
                ->transformDisplay(function($value) {
                    // Only show stars in detail page
                    if (!$value || $value < 0 || $value > 5) return 'No rating';
                    
                    $stars = '';
                    for ($i = 1; $i <= 5; $i++) {
                        if ($i <= $value) {
                            $stars .= '⭐'; // Full star
                        } else {
                            $stars .= '☆'; // Empty star
                        }
                    }
                    
                    return $stars . ' (' . $value . '/5)';
                }),
        ]);
    }
}

/**
 * Example: Advanced transformDisplay with conditional formatting
 */
class AdvancedDisplayForm extends BaseFormComponent
{
    public function init(): void
    {
        $this->makeForm([
            // Conditional formatting based on value ranges
            Form::add(label: 'Score', key: 'score', type: 'number')
                ->transformDisplay(function($value) {
                    if (!$value) return 'No score';
                    
                    if ($value >= 90) {
                        return '<span class="text-green-600 font-bold">' . $value . ' (Excellent)</span>';
                    } elseif ($value >= 80) {
                        return '<span class="text-blue-600">' . $value . ' (Good)</span>';
                    } elseif ($value >= 70) {
                        return '<span class="text-yellow-600">' . $value . ' (Average)</span>';
                    } else {
                        return '<span class="text-red-600">' . $value . ' (Poor)</span>';
                    }
                }),

            // Progress bar for completion percentage
            Form::add(label: 'Progress', key: 'progress', type: 'number')
                ->transformDisplay(function($value) {
                    if (!$value) return 'No progress';
                    
                    $percentage = min(100, max(0, $value));
                    $color = $percentage >= 80 ? 'bg-green-500' : ($percentage >= 50 ? 'bg-yellow-500' : 'bg-red-500');
                    
                    return '
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="' . $color . ' h-2.5 rounded-full" style="width: ' . $percentage . '%"></div>
                        </div>
                        <span class="text-sm text-gray-600">' . $percentage . '%</span>
                    ';
                }),

            // Social media links
            Form::add(label: 'Social Media', key: 'social_media', type: 'text')
                ->transformDisplay(function($value) {
                    if (!$value) return 'No social media';
                    
                    $platforms = [
                        'facebook' => ['icon' => '📘', 'url' => 'https://facebook.com/'],
                        'twitter' => ['icon' => '🐦', 'url' => 'https://twitter.com/'],
                        'instagram' => ['icon' => '📷', 'url' => 'https://instagram.com/'],
                        'linkedin' => ['icon' => '💼', 'url' => 'https://linkedin.com/in/']
                    ];
                    
                    $links = [];
                    foreach ($platforms as $platform => $config) {
                        if (str_contains(strtolower($value), $platform)) {
                            $links[] = '<a href="' . $config['url'] . $value . '" target="_blank" class="text-blue-600 hover:underline mr-2">' . $config['icon'] . ' ' . ucfirst($platform) . '</a>';
                        }
                    }
                    
                    return !empty($links) ? implode('', $links) : $value;
                }),
        ]);
    }
}
