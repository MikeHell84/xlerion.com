<?php
class Helpers {
    public static function env($key, $default=null) {
        $v = getenv($key);
        if ($v !== false) return $v;
        $path = dirname(__DIR__) . '/.env';
        if (file_exists($path)) {
            foreach (file($path, FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES) as $line) {
                if (strpos(trim($line),'#')===0) continue;
                [$k,$val] = array_map('trim', explode('=', $line,2)+[1=>null]);
                if ($k===$key) return trim($val, " \"'");
            }
        }
        return $default;
    }
}
