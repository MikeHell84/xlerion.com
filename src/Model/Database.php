<?php
class Database {
    private static $pdo;
    public static function pdo() {
        if (self::$pdo) return self::$pdo;
        $driver = getenv('DB_CONNECTION') ?: getenv('DB_DRIVER') ?: 'mysql';
        if ($driver === 'sqlite') {
            $path = getenv('DB_DATABASE') ?: dirname(__DIR__) . '/../storage/database.sqlite';
            if (!preg_match('#^(/|[A-Za-z]:\\\\)#',$path)) { // relative -> absolute
                $path = realpath(dirname(__DIR__) . '/../') . DIRECTORY_SEPARATOR . ltrim($path, '\\/');
            }
            $dsn = "sqlite:" . $path;
            $user = null; $pass = null;
        } else {
            $host = getenv('DB_HOST') ?: '127.0.0.1';
            $port = getenv('DB_PORT') ?: '3306';
            $name = getenv('DB_DATABASE') ?: 'xlerion_cmr';
            $user = getenv('DB_USERNAME') ?: 'root';
            $pass = getenv('DB_PASSWORD') ?: '';
            $dsn = "mysql:host=$host;port=$port;dbname=$name;charset=utf8mb4";
        }
        $opts = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC];
        try {
            if (isset($user)) self::$pdo = new PDO($dsn, $user, $pass, $opts);
            else self::$pdo = new PDO($dsn, null, null, $opts);
        } catch (PDOException $e) {
            // If the environment explicitly requested mysql (DB_CONNECTION=mysql),
            // rethrow the exception so the deploy fails fast. But when the
            // developer didn't set DB_CONNECTION, allow falling back to sqlite
            // (convenient for local dev where a MySQL database may not exist).
            $dbConnEnv = getenv('DB_CONNECTION');
            $explicitMysql = ($dbConnEnv !== false && strtolower($dbConnEnv) === 'mysql');
            if ($explicitMysql) {
                // rethrow for explicit mysql requests
                throw $e;
            }
            // fallback to sqlite
            $path = getenv('DB_DATABASE') ?: dirname(__DIR__) . '/../storage/database.sqlite';
            if (!preg_match('#^(/|[A-Za-z]:\\\\)#',$path)) {
                $path = realpath(dirname(__DIR__) . '/../') . DIRECTORY_SEPARATOR . ltrim($path, '\\/');
            }
            $dsn = "sqlite:" . $path;
            if (isset($user)) self::$pdo = new PDO($dsn, null, null, $opts);
            else self::$pdo = new PDO($dsn, null, null, $opts);
        }
        return self::$pdo;
    }
}
