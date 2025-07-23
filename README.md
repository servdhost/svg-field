# SVG Field Plugin for Craft CMS 5

A Craft CMS 5 plugin that provides a custom field type for storing and managing SVG files directly in the database with built-in sanitization and validation.

## Features

- **Admin Interface**: Upload SVG files or paste SVG content directly in the admin panel
- **Database Storage**: SVG content is stored directly in the database as text
- **Security**: Built-in SVG sanitization to remove potentially dangerous elements and attributes
- **Validation**: Comprehensive validation to ensure only valid SVG content is accepted
- **Twig Integration**: Easy-to-use Twig methods for outputting SVG content in templates

## Installation

You can install this plugin from the Craft Plugin Store, or with Composer.

## Usage

### Creating an SVG Field

1. Go to Settings > Fields in your Craft CMS admin panel
2. Create a new field and select "SVG Field" as the field type
3. Configure the field settings and assign it to your sections

### Admin Interface

The field provides two ways to add SVG content:

1. **File Upload**: Click the file input to upload an `.svg` file
2. **Direct Input**: Paste or type SVG code directly into the textarea

The field includes a preview of the current SVG content when editing entries.

### Template Usage

#### Basic Output

```twig
{# Output the SVG content directly #}
{{ entry.mySvgField|raw }}

{# Or use the helper variable #}
{{ craft.svgField.render(entry.mySvgField)|raw }}
```

#### With Custom Attributes

```twig
{# Add custom attributes to the SVG element #}
{{ craft.svgField.render(entry.mySvgField, {
    class: 'my-svg-class',
    width: '100',
    height: '100'
})|raw }}
```

#### Inline Usage

```twig
{# Same as render() - for semantic clarity #}
{{ craft.svgField.inline(entry.mySvgField, {
    class: 'inline-svg'
})|raw }}
```

#### Getting SVG Attributes

```twig
{# Get specific SVG attributes #}
{% set width = craft.svgField.getWidth(entry.mySvgField) %}
{% set height = craft.svgField.getHeight(entry.mySvgField) %}
{% set viewBox = craft.svgField.getViewBox(entry.mySvgField) %}

<p>SVG dimensions: {{ width }} x {{ height }}</p>
<p>ViewBox: {{ viewBox }}</p>
```

## Security Features

The plugin includes comprehensive security measures:

### Allowed Elements
- Standard SVG elements: `svg`, `g`, `path`, `rect`, `circle`, `ellipse`, etc.
- Text elements: `text`, `tspan`
- Gradient and pattern elements: `linearGradient`, `radialGradient`, `pattern`, etc.
- Animation elements: `animate`, `animateTransform`

### Blocked Content
- JavaScript execution (`javascript:`, `on*` event handlers)
- External content loading (`data:` URLs)
- Script tags and other dangerous HTML elements
- VBScript and other scripting languages

### Sanitization Process
1. **Pattern Detection**: Scans for dangerous patterns before processing
2. **XML Validation**: Ensures the content is valid XML
3. **Element Filtering**: Removes non-whitelisted tags and attributes
4. **Attribute Sanitization**: Validates attribute values for security

## Field Methods

The SVG field provides these methods in your templates:

- `getSvgContent()`: Returns the raw SVG content (same as accessing the field directly)

## Twig Variables

Access the `craft.svgField` variable in your templates:

- `render(content, attributes)`: Render SVG with optional attributes
- `inline(content, attributes)`: Alias for render() method
- `getWidth(content)`: Extract width attribute
- `getHeight(content)`: Extract height attribute  
- `getViewBox(content)`: Extract viewBox attribute

## Development

### File Structure

```
src/
├── Plugin.php                 # Main plugin class
├── fields/
│   └── SvgField.php           # SVG field type
├── services/
│   └── SvgSanitizer.php       # SVG sanitization service
├── variables/
│   └── SvgFieldVariable.php   # Twig variables
└── templates/
    └── _input.twig            # Admin field input template
```

### Customization

To customize the allowed SVG elements and attributes, modify the arrays in `src/services/SvgSanitizer.php`:

- `$allowedTags`: Add or remove allowed SVG elements
- `$allowedAttributes`: Add or remove allowed attributes
- `$dangerousPatterns`: Add patterns to detect and block

## Requirements

- Craft CMS 5.0+
- PHP 8.1+

## License

MIT License