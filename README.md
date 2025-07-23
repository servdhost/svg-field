# SVG Field Plugin for Craft CMS 5

A Craft CMS plugin that provides a custom field type for storing SVG content directly in the database.

## Who's it for?

It's for folks that need to inline SVG assets efficiently and resiliently, but aren't able to store the SVG files on the local filesystem.

This may be suitable if your site...

* Is load-balanced.
* Is running on ephemeral filesystems, which aren't guaranteed to persist.
* Takes a long time to request SVG files for the purposes of inlining them (e.g. they may be stored in a bucket many miles away).

## Features

- **Drag & Drop Upload**: Intuitive interface for uploading SVG files
- **Database Storage**: SVG content is stored as text in the database (not as files)
- **Live Preview**: See uploaded SVGs immediately in the admin interface
- **Content Management**: Remove or replace SVG content with built-in controls
- **Validation**: Ensures only valid SVG content is accepted
- **Template Ready**: Easy integration with Twig templates

## Installation

Install via composer:

```
composer require servd/svg-field
```

## Usage

### Creating an SVG Field

1. Go to **Settings** → **Fields** in your Craft Control Panel
2. Click **New field**
3. Choose **SVG Field** as the field type
4. Configure your field settings and save

### Using the Field

1. **Upload**: Click the upload area or drag and drop an SVG file
2. **Preview**: The SVG will display immediately after upload
3. **Manage**: Use the Remove or Replace buttons to modify content
4. **Save**: Save your entry to store the SVG content in the database

### Template Usage

Display SVG content in your Twig templates:

```twig
{# Basic usage #}
{{ entry.yourSvgField|raw }}

{# Check if field has content #}
{% if entry.yourSvgField %}
    <div class="svg-container">
        {{ entry.yourSvgField|raw }}
    </div>
{% endif %}

{# Add custom classes or attributes #}
<div class="my-svg-wrapper">
    {{ entry.yourSvgField|raw }}
</div>
```

## Technical Details

### Storage
- SVG content is stored as text in the database
- No files are created on the server filesystem
- Content is validated as proper XML and SVG format

### Browser Support
- Modern browsers with File API support
- Drag and drop functionality
- SVG preview rendering

### Security
- SVG content is validated before storage
- Only valid SVG XML is accepted
- Content is properly escaped in admin interface

## Requirements

- Craft CMS 5.0+
- PHP 8.2+
- Modern web browser with JavaScript enabled


## License

MIT License - see LICENSE file for details
