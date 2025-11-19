<?php
class Security {
    public static function sanitizeHtml($html) {
        // minimal sanitization: remove script tags and on* attributes
        $html = preg_replace('#<script[^>]*>.*?</script>#is','',$html);
        // remove on* attributes
        $html = preg_replace_callback('#<([^>]+)>#', function($m){ return '<' . preg_replace('#\s(on[a-z]+)="[^"]*"#i','',$m[1]) . '>'; }, $html);
        return $html;
    }
}
