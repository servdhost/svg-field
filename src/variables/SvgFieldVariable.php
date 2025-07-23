<?php

namespace servd\svgfield\variables;

class SvgFieldVariable
{
    public function render(?string $svgContent, array $attributes = []): string
    {
        if (!$svgContent) {
            return '';
        }

        // If attributes are provided, we need to merge them with the SVG
        if (!empty($attributes)) {
            return $this->mergeSvgAttributes($svgContent, $attributes);
        }

        return $svgContent;
    }

    public function inline(?string $svgContent, array $attributes = []): string
    {
        return $this->render($svgContent, $attributes);
    }

    private function mergeSvgAttributes(string $svgContent, array $attributes): string
    {
        $dom = new \DOMDocument();
        $dom->loadXML($svgContent);
        
        $svgElement = $dom->documentElement;
        
        foreach ($attributes as $name => $value) {
            $svgElement->setAttribute($name, $value);
        }
        
        return $dom->saveXML($svgElement);
    }

    public function getWidth(?string $svgContent): ?string
    {
        if (!$svgContent) {
            return null;
        }

        $dom = new \DOMDocument();
        $dom->loadXML($svgContent);
        
        return $dom->documentElement->getAttribute('width') ?: null;
    }

    public function getHeight(?string $svgContent): ?string
    {
        if (!$svgContent) {
            return null;
        }

        $dom = new \DOMDocument();
        $dom->loadXML($svgContent);
        
        return $dom->documentElement->getAttribute('height') ?: null;
    }

    public function getViewBox(?string $svgContent): ?string
    {
        if (!$svgContent) {
            return null;
        }

        $dom = new \DOMDocument();
        $dom->loadXML($svgContent);
        
        return $dom->documentElement->getAttribute('viewBox') ?: null;
    }
}