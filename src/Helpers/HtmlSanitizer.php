<?php
/**
 * HtmlSanitizer
 * Minimal, self-contained sanitizer that prefers HTMLPurifier when available.
 * Goals: remove inline event handlers, drop javascript: URIs, restrict iframe hosts,
 * and preserve data:* URIs and data-* attributes via placeholders when using purifier.
 */
class HtmlSanitizer {
    protected static $allowedTags = [
        'div'=>['class','id','data-*','style','role'],
        'p'=>['class','id','style'],
        'span'=>['class','id','data-*','style'],
        'a'=>['href','title','target','rel','class','id','style'],
        'img'=>['src','alt','title','class','id','data-*','style'],
        'iframe'=>['src','width','height','allow','allowfullscreen','class','id','style'],
        'br'=>[],
        'strong'=>['class','id','style'],'b'=>['class','id','style'],'em'=>['class','id','style'],'i'=>['class','id','style'],
        'table'=>['class','id','style'],'thead'=>['class','id','style'],'tbody'=>['class','id','style'],'tr'=>['class','id','style'],'td'=>['class','id','style'],'th'=>['class','id','style'],
    ];

    protected static $allowedClasses = [
        'container','row','col','card','card-body','btn','btn-primary','btn-outline-light','tpl-block','placeholder-img','card-preview'
    ];

    protected static $allowedClassPatterns = [
        '/^col-(?:xs|sm|md|lg|xl|xxl)-?\\d*$/','/^col-(auto|\\d+)$/','/^p[trblxy]?-?[0-5]$/','/^m[trblxy]?-?[0-5]$/','/^text-[a-z0-9_-]+$/'
    ];

    protected static $allowedIframeHosts = [
        'www.youtube.com','youtube.com','player.vimeo.com','vimeo.com'
    ];

    public static function getAllowedClasses(){ return self::$allowedClasses; }
    public static function getAllowedClassPatterns(){ return self::$allowedClassPatterns; }

    public static function sanitize_html($html){
        if (!$html) return '';

        $vendor = __DIR__ . '/../../vendor/autoload.php';
        // HTMLPurifier is available but opt-in only: set env HTMLPURIFIER_ENABLED=1 to enable.
        $usePurifier = (getenv('HTMLPURIFIER_ENABLED') === '1');
        if ($usePurifier && file_exists($vendor)){
            require_once $vendor;
            if (class_exists('HTMLPurifier')){
                // Protect data: URIs in src attributes
                $placeholders = [];
                $i = 0;
                    $html_protected = preg_replace_callback("#(src\s*=\s*)(['\"])(data:[^'\"]*?)\\2#si", function($m) use (&$placeholders,&$i){ $t='__DATA_URI_'.($i++).'__'; $placeholders[$t]=$m[3]; return $m[1].$m[2].$t.$m[2]; }, $html);
                if ($html_protected === null) $html_protected = $html;

                // Protect data-* attributes
                $attr_placeholders = [];
                $j = 0;
                    $html_protected = preg_replace_callback("#\s(data-[a-z0-9_\-]+)\s*=\s*(['\"])(.*?)\\2#si", function($m) use (&$attr_placeholders,&$j){ $t='__DATA_ATTR_'.($j++).'__'; $attr_placeholders[$t]=' '.$m[1].'='.$m[2].$m[3].$m[2]; return ' '.$t; }, $html_protected);

                // Configure purifier
                $config = HTMLPurifier_Config::createDefault();
                $config->set('HTML.SafeIframe', true);
                try{ $schemes = $config->get('URI.AllowedSchemes'); if (!is_array($schemes)) $schemes = []; $schemes['data'] = true; $config->set('URI.AllowedSchemes', $schemes); }catch(Exception $e){}
                $purifier = new HTMLPurifier($config);
                $clean = $purifier->purify($html_protected);

                // Restore placeholders
                if (!empty($placeholders)) $clean = str_replace(array_keys($placeholders), array_values($placeholders), $clean);
                if (!empty($attr_placeholders)) $clean = str_replace(array_keys($attr_placeholders), array_values($attr_placeholders), $clean);

                // Strip inline event handlers
                    $clean = preg_replace('/\son[a-z]+\s*=\s*([\'\"]).*?\1/si','',$clean);
                return $clean;
            }
        }

        // DOM fallback
        libxml_use_internal_errors(true);
        $doc = new DOMDocument();
        $doc->loadHTML('<?xml encoding="utf-8" ?><div>'.$html.'</div>', LIBXML_HTML_NOIMPLIED|LIBXML_HTML_NODEFDTD);
        $root = $doc->getElementsByTagName('div')->item(0);
        if ($root) self::cleanNode($root);
        $out = '';
        if ($root){ foreach($root->childNodes as $c) $out .= $doc->saveHTML($c); }
        return $out;
    }

    protected static function cleanNode($node){
        if ($node->nodeType !== XML_ELEMENT_NODE){ foreach(iterator_to_array($node->childNodes) as $c) self::cleanNode($c); return; }
        $tag = strtolower($node->nodeName);
        if (!array_key_exists($tag, self::$allowedTags)){
            $parent = $node->parentNode;
            while ($node->firstChild) $parent->insertBefore($node->firstChild, $node);
            $parent->removeChild($node);
            return;
        }

        if ($tag === 'iframe'){
            $src = $node->getAttribute('src') ?: '';
            $parts = @parse_url($src);
            $host = isset($parts['host']) ? strtolower($parts['host']) : '';
            $ok = false;
            if ($host){ foreach (self::$allowedIframeHosts as $ah){ if (strpos($host, $ah) !== false) { $ok = true; break; } } }
            if (!$ok){ $node->parentNode->removeChild($node); return; }
        }

        $allowed = self::$allowedTags[$tag];
        $attrs = [];
        foreach (iterator_to_array($node->attributes) as $a) $attrs[$a->name] = $a->value;
        foreach (array_keys($attrs) as $n) $node->removeAttribute($n);

        foreach ($attrs as $name => $value){
            if (preg_match('/^on/i', $name)) continue; // drop inline handlers
            if (stripos($name,'aria-') === 0){ $node->setAttribute($name, htmlspecialchars($value, ENT_QUOTES|ENT_SUBSTITUTE, 'UTF-8')); continue; }
            if (strpos($name,'data-') === 0 && in_array('data-*', $allowed, true)){ $node->setAttribute($name, htmlspecialchars($value, ENT_QUOTES|ENT_SUBSTITUTE, 'UTF-8')); continue; }
            if (in_array($name, $allowed, true)){
                if (strtolower($name) === 'class'){
                    $pieces = preg_split('/\s+/', $value);
                    $keep = [];
                    foreach ($pieces as $c){ $c = trim($c); if ($c === '') continue; if (in_array($c, self::$allowedClasses, true)) { $keep[] = $c; continue; } foreach (self::$allowedClassPatterns as $pat){ if (preg_match($pat, $c)) { $keep[] = $c; break; } } }
                    if (!empty($keep)) $node->setAttribute('class', implode(' ', $keep));
                    continue;
                }
                if (in_array($name, ['href','src'], true)){
                    $v = trim($value);
                    if (preg_match('#^javascript:#i', $v)) continue;
                    $node->setAttribute($name, $v);
                    continue;
                }
                $node->setAttribute($name, htmlspecialchars($value, ENT_QUOTES|ENT_SUBSTITUTE, 'UTF-8'));
            }
        }

        foreach (iterator_to_array($node->childNodes) as $c) self::cleanNode($c);
    }
}
