<?php
class App {
    public function __construct(public string $baseUri = '/') {}

    public function baseUri(): string { return $this->baseUri; }

    public function method(): string { return $_SERVER['REQUEST_METHOD'] ?? 'GET'; }

    public function path(): string {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        // strip query string
        if (($q = strpos($uri, '?')) !== false) $uri = substr($uri, 0, $q);
        // remove base
        if ($this->baseUri !== '/' && str_starts_with($uri, $this->baseUri)) {
            $uri = substr($uri, strlen($this->baseUri));
        }
        $uri = '/' . ltrim($uri, '/');
        return rtrim($uri,'/') === '' ? '/' : rtrim($uri,'/');
    }
}
