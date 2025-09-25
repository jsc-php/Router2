<?php

namespace JscPhp\Router2;

/**
 * Represents an HTTP request and provides utilities for retrieving and normalizing the request URI.
 */
class Request
{

    /**
     * Retrieves the request URI from the server and optionally normalizes it.
     *
     * @param bool $normalized Indicates whether the URI should be normalized by removing unnecessary components and formatting.
     *
     * @return string The requested URI, normalized if specified.
     */
    static function getRequestURI(bool $normalized): string
    {
        $uri = filter_input(INPUT_SERVER, 'REQUEST_URI');
        if ($normalized) {
            $uri = self::_normalizeURI($uri);
        }
        return $uri;
    }

    /**
     * Normalizes the given URI by removing unnecessary query parameters and fragments,
     * trimming leading and trailing slashes, and ensuring the URI starts with a single slash.
     *
     * @param string $uri The URI to normalize.
     *
     * @return string The normalized URI.
     */
    private static function _normalizeURI(string $uri): string
    {
        $break_chars = ['?', '#'];
        foreach ($break_chars as $break_char) {
            if (str_contains($uri, $break_char)) {
                $uri = substr($uri, 0, strpos($uri, $break_char));
            }
        }
        $uri = trim($uri, '/');
        $uri = '/' . $uri;
        return $uri;
    }

    public static function getRequestMethod(): string
    {
        return strtoupper(filter_input(INPUT_SERVER, 'REQUEST_METHOD'));
    }
}