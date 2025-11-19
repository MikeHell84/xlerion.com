<?php
class MediaHelper {
    public static function publicPath($absPath) {
        // try to map storage/uploads to /storage/uploads if public
        return str_replace('\\','/',$absPath);
    }
}
