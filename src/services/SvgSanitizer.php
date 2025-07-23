<?php

namespace servd\svgfield\services;

use yii\base\Component;

class SvgSanitizer extends Component
{
    private array $allowedTags = [
        'svg', 'g', 'path', 'rect', 'circle', 'ellipse', 'line', 'polyline', 'polygon',
        'text', 'tspan', 'defs', 'use', 'symbol', 'marker', 'clipPath', 'mask',
        'pattern', 'linearGradient', 'radialGradient', 'stop', 'animate', 'animateTransform',
        'title', 'desc', 'metadata'
    ];

    private array $allowedAttributes = [
        'class', 'id', 'style', 'transform', 'fill', 'stroke', 'stroke-width', 'stroke-linecap',
        'stroke-linejoin', 'stroke-miterlimit', 'stroke-dasharray', 'stroke-dashoffset',
        'opacity', 'fill-opacity', 'stroke-opacity', 'viewBox', 'width', 'height',
        'x', 'y', 'x1', 'y1', 'x2', 'y2', 'cx', 'cy', 'r', 'rx', 'ry', 'points',
        'd', 'xmlns', 'xmlns:xlink', 'version', 'preserveAspectRatio',
        'gradientUnits', 'gradientTransform', 'spreadMethod', 'stop-color', 'stop-opacity',
        'offset', 'patternUnits', 'patternTransform', 'clipPathUnits', 'maskUnits'
    ];

    private array $dangerousPatterns = [
        '/javascript:/i',
        '/data:/i',
        '/vbscript:/i',
        '/<script/i',
        '/on\w+\s*=/i', // event handlers like onclick, onload, etc.
        '/<iframe/i',
        '/<object/i',
        '/<embed/i',
        '/<link/i',
        '/<meta/i',
    ];

    public function sanitize(string $svgContent): string
    {
        // Check for dangerous patterns first
        foreach ($this->dangerousPatterns as $pattern) {
            if (preg_match($pattern, $svgContent)) {
                throw new \Exception('SVG content contains potentially dangerous elements.');
            }
        }

        // Load and validate XML
        $previousUseInternalErrors = libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        $dom->formatOutput = true;
        $dom->preserveWhiteSpace = false;

        if (!$dom->loadXML($svgContent)) {
            libxml_use_internal_errors($previousUseInternalErrors);
            throw new \Exception('Invalid XML structure in SVG.');
        }

        libxml_use_internal_errors($previousUseInternalErrors);

        // Validate root element is SVG
        if (!$dom->documentElement || $dom->documentElement->tagName !== 'svg') {
            throw new \Exception('Root element must be <svg>.');
        }

        // Sanitize the DOM
        $this->sanitizeNode($dom->documentElement);

        return $dom->saveXML($dom->documentElement);
    }

    private function sanitizeNode(\DOMElement $node): void
    {
        // Remove disallowed tags
        if (!in_array(strtolower($node->tagName), $this->allowedTags)) {
            $node->parentNode->removeChild($node);
            return;
        }

        // Remove disallowed attributes
        $attributesToRemove = [];
        foreach ($node->attributes as $attribute) {
            if (!in_array(strtolower($attribute->name), $this->allowedAttributes)) {
                $attributesToRemove[] = $attribute->name;
            } else {
                // Check attribute values for dangerous content
                foreach ($this->dangerousPatterns as $pattern) {
                    if (preg_match($pattern, $attribute->value)) {
                        $attributesToRemove[] = $attribute->name;
                        break;
                    }
                }
            }
        }

        foreach ($attributesToRemove as $attrName) {
            $node->removeAttribute($attrName);
        }

        // Recursively sanitize child nodes
        $childNodes = [];
        foreach ($node->childNodes as $child) {
            if ($child instanceof \DOMElement) {
                $childNodes[] = $child;
            }
        }

        foreach ($childNodes as $child) {
            $this->sanitizeNode($child);
        }
    }

    public function isValidSvg(string $content): bool
    {
        try {
            $this->sanitize($content);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}