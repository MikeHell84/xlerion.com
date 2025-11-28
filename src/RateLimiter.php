<?php
class RateLimiter {
    // Simple filesystem-based rate limiter per IP
    protected $dir;
    protected $limit;
    protected $window;
    public function __construct($limit = 10, $window = 60) {
        $this->dir = dirname(__DIR__) . '/storage/ratelimit';
        if (!is_dir($this->dir)) mkdir($this->dir, 0755, true);
        $this->limit = $limit; $this->window = $window;
    }
    protected function file($ip) { return $this->dir . '/' . preg_replace('/[^a-z0-9_.-]/i','_',$ip) . '.json'; }
    public function hit($ip) {
        $f = $this->file($ip);
        $now = time();
        $data = ['count'=>0,'start'=>$now];
        if (file_exists($f)) { $data = json_decode(file_get_contents($f), true) ?: $data; }
        if ($now - $data['start'] > $this->window) { $data = ['count'=>1,'start'=>$now]; }
        else { $data['count'] = ($data['count'] ?? 0) + 1; }
        file_put_contents($f, json_encode($data));
        return $data['count'] <= $this->limit;
    }
    public function remaining($ip) {
        $f = $this->file($ip); if (!file_exists($f)) return $this->limit;
        $data = json_decode(file_get_contents($f), true) ?: ['count'=>0,'start'=>time()];
        return max(0, $this->limit - ($data['count'] ?? 0));
    }
}
