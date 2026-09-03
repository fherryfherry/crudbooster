# Summernote WYSIWYG Editor for CRUDBooster

## Overview

Summernote is a rich text editor component for CRUDBooster that provides a clean and intuitive interface for rich text editing. This component is designed to work seamlessly with Livewire and includes features like image upload and auto-reformat.

## Features

- **Rich Text Editing**: Full WYSIWYG editor with toolbar
- **Image Upload**: Drag and drop or click to upload images
- **Auto-Reformat**: Clean pasted content from Word, Google Docs, etc.
- **CSS Scoped**: Prevents conflicts with existing CSS frameworks
- **Livewire Integration**: Seamless integration with Livewire components
- **SPA Navigation**: Supports single-page application navigation
- **Responsive Design**: Works well on desktop and mobile devices

## Installation

The Summernote component is automatically installed with CRUDBooster v7.9.12 and above. No additional installation steps are required.

## Usage

### Basic Usage

```php
use CrudBooster\Livewire\FormBuilder\Form;

Form::add(label: 'Content', key: 'content', type: 'summernote')
```

### With Options

```php
use CrudBooster\Livewire\FormBuilder\Form;
use CrudBooster\Components\Type\Summernote\Function\Summernote;

Form::add(label: 'Content', key: 'content', type: 'summernote')
    ->option(Summernote::option()
        ->height(400)
        ->autoReformat()
    )
```

## Available Options

- **height(int $height)**: Set the editor height in pixels (default: 300)
- **autoReformat(bool $enabled = true)**: Enable auto-reformat for pasted content

## Image Upload

Images are automatically uploaded to `storage/app/public/summernote/images/` and served through the public storage link. Make sure to run:

```bash
php artisan storage:link
```

## Auto-Reformat Feature

When enabled, the auto-reformat feature automatically cleans pasted content by:
- Removing unwanted HTML tags and attributes
- Converting to clean, semantic HTML
- Removing inline styles and classes
- Cleaning up formatting from Word, Google Docs, and other sources

## CSS Scoping

All Summernote styles are scoped to `.summernote-container` to prevent conflicts with existing CSS frameworks like Tailwind CSS.

## Dependencies

- jQuery 3.5.1
- Bootstrap 3.4.1 (JS only)
- Summernote 0.8.18

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)

## License

This component is part of CRUDBooster and follows the same license terms. 