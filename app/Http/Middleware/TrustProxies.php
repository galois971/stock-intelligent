<?php
namespace App\Http\Middleware;

use Fideloper\Proxy\TrustProxies as Middleware;
use Illuminate\Http\Request;

class TrustProxies extends Middleware
{
    // Trust all proxies (Render/Cloudflare)
    protected $proxies = '*';

    // Use forwarded headers (X-Forwarded-For, X-Forwarded-Proto, etc.)
    protected $headers = Request::HEADER_X_FORWARDED_ALL;
}
