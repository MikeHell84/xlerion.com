<?php
require_once __DIR__ . '/HtmlSanitizer.php';
require_once __DIR__ . '/../Model/Template.php';
require_once __DIR__ . '/../Model/Database.php';
// optional composer autoload if scssphp installed via composer
if (file_exists(__DIR__ . '/../../vendor/autoload.php')) {
    require_once __DIR__ . '/../../vendor/autoload.php';
}

class TemplateRenderer {
    public static function render_section_for_page($pageId, $section){
        $pdo = Database::pdo();
        // lookup assignment
        $stmt = $pdo->prepare('SELECT template_id FROM template_assignments WHERE page_id = ? AND section = ? LIMIT 1');
        $stmt->execute([$pageId, $section]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) return '';
        $tpl = Template::find($row['template_id']);
        if (!$tpl) return '';
        $html = $tpl['data']['html'] ?? '';
        $styles = $tpl['data']['styles'] ?? '';
        // sanitize HTML
        $safeHtml = HtmlSanitizer::sanitize_html($html);
        $id = $tpl['id'];
        // compile scss to css or use cached compiled css
        $css = '';
        if ($styles && trim($styles) !== ''){
            $css = self::getCompiledCssForTemplate($id, $styles);
            // scope css to selector
            $css = self::scopeCssToSelector($css, '[data-tpl-id="'.$id.'"]');
        }
        $out = '';
        if ($css) $out .= "<style data-tpl-id=\"{$id}\">".htmlspecialchars($css, ENT_NOQUOTES)."</style>";
        $out .= "<div class=\"tpl-instance\" data-tpl-id=\"{$id}\">" . $safeHtml . "</div>";
        return $out;
    }

    public static function getCompiledCssForTemplate($id, $scss){
    $cacheDir = dirname(__DIR__, 2) . '/storage/cache/templates';
        if (!is_dir($cacheDir)) @mkdir($cacheDir, 0755, true);
        $cacheFile = $cacheDir . '/tpl-' . intval($id) . '.css';
        // If cache exists and is newer than modification, return it
        if (file_exists($cacheFile)) return file_get_contents($cacheFile);
        // Try to compile using scssphp if available
        $css = '';
        try{
            if (class_exists('\ScssPhp\ScssPhp\Compiler')){
                $cls = '\\ScssPhp\\ScssPhp\\Compiler';
                $c = new $cls();
                // scssphp versions have different method names; try compileString->getCss or compile
                try{ if (method_exists($c,'compileString')){ $res = $c->compileString($scss); $css = is_object($res) && isset($res->css) ? $res->css : (is_string($res)?$res:''); }
                    else if (method_exists($c,'compile')){ $css = $c->compile($scss); } else { $css = (string)$scss; }
                }catch(Exception $e){ $css = (string)$scss; }
            } else {
                // no scss compiler available: treat as plain css
                $css = $scss;
            }
        }catch(\Exception $e){
            $css = $scss; // fallback
        }
        // write cache
        @file_put_contents($cacheFile, $css);
        return $css;
    }

    // Very small and safe scoping: prefix top-level selectors. Not perfect for complex selectors.
    public static function scopeCssToSelector($css, $scopeSelector){
        // split by }, then prefix each selector portion
        $out = [];
        $parts = preg_split('/(\})/', $css, -1, PREG_SPLIT_DELIM_CAPTURE);
        for ($i=0;$i<count($parts);$i+=2){
            $selBlock = $parts[$i];
            $closing = ($i+1 < count($parts)) ? $parts[$i+1] : '';
            $selBlock = trim($selBlock);
            if ($selBlock === '') { continue; }
            // find first { to split selectors and rules
            $p = strpos($selBlock, '{');
            if ($p === false){ $out[] = $selBlock . $closing; continue; }
            $selectors = trim(substr($selBlock, 0, $p));
            $rules = substr($selBlock, $p);
            $selParts = explode(',', $selectors);
            $newSelParts = array_map(function($s) use ($scopeSelector){
                $s = trim($s);
                if ($s === '') return $s;
                // if selector starts with @ (e.g., @media) keep as is
                if (strpos($s, '@') === 0) return $s;
                // avoid scoping keyframes
                if (stripos($s, 'from')===0 || stripos($s,'to')===0 || stripos($s,'%')!==false) return $s;
                return $scopeSelector . ' ' . $s;
            }, $selParts);
            $out[] = implode(', ', $newSelParts) . ' ' . $rules . $closing;
        }
        return implode('\n', $out);
    }
}
