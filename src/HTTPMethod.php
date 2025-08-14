<?php

namespace JscPhp\Router2;

enum HTTPMethod: string
{
    case Any    = 'ANY';
    case Get    = 'GET';
    case Post   = 'POST';
    case Put    = 'PUT';
    case Delete = 'DELETE';
    case Patch  = 'PATCH';
    case Head   = 'HEAD';
}
