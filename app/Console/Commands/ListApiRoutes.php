<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use Illuminate\Routing\RouteCollection;

class ListApiRoutes extends Command
{
    /**
     * O nome e assinatura do comando.
     *
     * @var string
     */
    protected $signature = 'route:api 
                            {--format=table : Formato de saída (table, json, csv)}
                            {--filter= : Filtrar rotas por termo}';

    /**
     * A descrição do comando.
     *
     * @var string
     */
    protected $description = 'Lista todas as rotas da API';

    /**
     * Execute o comando.
     */
    public function handle()
    {
        /** @var RouteCollection $routes */
        $routes = Route::getRoutes();
        
        $apiRoutes = [];
        
        foreach ($routes as $route) {
            // Verificar se é rota API
            $uri = $route->uri();
            $middlewares = $route->gatherMiddleware();
            $name = $route->getName();
            
            $isApiRoute = (
                strpos($uri, 'api/') === 0 || 
                in_array('api', $middlewares) ||
                ($name && strpos($name, 'api.') === 0)
            );
            
            if (!$isApiRoute) {
                continue;
            }
            
            // Aplicar filtro
            $filter = $this->option('filter');
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
        
        // Ordenar por URI
        usort($apiRoutes, fn($a, $b) => strcmp($a['uri'], $b['uri']));
        
        $format = $this->option('format');
        
        switch ($format) {
            case 'json':
                $this->line(json_encode([
                    'total' => count($apiRoutes),
                    'routes' => $apiRoutes
                ], JSON_PRETTY_PRINT));
                break;
                
            case 'csv':
                $this->line("Method,URI,Name,Action,Middleware");
                foreach ($apiRoutes as $route) {
                    $this->line(implode(',', array_map(function($field) {
                        return '"' . str_replace('"', '""', $field) . '"';
                    }, $route)));
                }
                break;
                
            case 'table':
            default:
                $headers = ['Method', 'URI', 'Name', 'Action', 'Middleware'];
                $this->table($headers, $apiRoutes);
                $this->info("\nTotal de rotas API: " . count($apiRoutes));
                break;
        }
    }
}