<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\RouteCollection;
use Illuminate\Support\Facades\Route;

class ListApiRoutes extends Controller
{
    /**
     * Lista rotas da API em JSON (padrão) ou CSV.
     *
     * Query params:
     * - format: json|csv
     * - filter: termo para filtrar por URI, nome ou action
     */
    public function index(Request $request)
    {
        /** @var RouteCollection $routes */
        $routes = Route::getRoutes();

        $apiRoutes = [];
        $filter = $request->query('filter');

        foreach ($routes as $route) {
            $uri = $route->uri();
            $middlewares = $route->gatherMiddleware();
            $name = $route->getName();

            $isApiRoute = (
                strpos($uri, 'api/') === 0 ||
                in_array('api', $middlewares, true) ||
                ($name && strpos($name, 'api.') === 0)
            );

            if (!$isApiRoute) {
                continue;
            }

            if ($filter) {
                $matches = (
                    strpos($uri, $filter) !== false ||
                    ($name && strpos($name, $filter) !== false) ||
                    strpos($route->getActionName(), $filter) !== false
                );

                if (!$matches) {
                    continue;
                }
            }

            $apiRoutes[] = [
                'method' => implode('|', $route->methods()),
                'uri' => $uri,
                'name' => $name ?: 'N/A',
                'action' => $route->getActionName(),
                'middleware' => implode(', ', $middlewares),
            ];
        }

        usort($apiRoutes, fn($a, $b) => strcmp($a['uri'], $b['uri']));

        $format = $request->query('format', 'json');

        if ($format === 'csv') {
            $lines = [];
            $lines[] = 'Method,URI,Name,Action,Middleware';
            foreach ($apiRoutes as $route) {
                $lines[] = implode(',', array_map(function ($field) {
                    return '"' . str_replace('"', '""', $field) . '"';
                }, $route));
            }

            return response(implode("\n", $lines))
                ->header('Content-Type', 'text/csv');
        }

        return response()->json([
            'total' => count($apiRoutes),
            'routes' => $apiRoutes,
        ]);
    }
}
