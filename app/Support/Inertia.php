<?php
// app/Support/Inertia.php

namespace App\Support;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;

class Inertia
{
    protected static array $sharedProps = [];

    /**
     * Share a key/value pair or array across all pages.
     */
    public static function share(string|array $key, mixed $value = null): void
    {
        if (is_array($key)) {
            self::$sharedProps = array_merge(self::$sharedProps, $key);
        } else {
            self::$sharedProps[$key] = $value;
        }
    }

    /**
     * Render an Inertia component.
     */
    public static function render(string $component, array $props = []): mixed
    {
        // Resolve flash, errors, and auth data as standard Inertia props
        $defaultProps = [
            'auth' => [
                'user' => Request::user() ? [
                    'id' => Request::user()->id,
                    'name' => Request::user()->name,
                    'email' => Request::user()->email,
                ] : null,
            ],
            'flash' => [
                'success' => session('success'),
                'error' => session('error'),
            ],
            'errors' => (object) (session('errors')?->getBag('default')?->toArray() ?: []),
        ];

        // Merge standard, shared and local page props
        $mergedProps = array_merge($defaultProps, self::$sharedProps, $props);

        $page = [
            'component' => $component,
            'props' => $mergedProps,
            'url' => Request::getRequestUri(),
            'version' => '1',
        ];

        // If it's an Inertia AJAX request, return a JSON response with the appropriate headers
        if (Request::header('X-Inertia')) {
            return Response::json($page, 200, [
                'X-Inertia' => 'true',
                'Vary' => 'X-Inertia',
            ]);
        }

        // Otherwise, render the main blade template (app.blade.php) containing the mount element
        return view('app', ['page' => $page]);
    }
}
