<nav class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <div class="logo-icon">
                        <i class="bi bi-diagram-3"></i>
                    </div>
                    <div class="logo-text">NexusFlow</div>
                </div>
            </div>
            
            <div class="sidebar-nav">
                <?php
                // Scan view directory for .php files and build menu dynamically
                $viewDir = __DIR__ . '/../view';
                $files = [];
                if (is_dir($viewDir)) {
                    foreach (scandir($viewDir) as $f) {
                        if (in_array($f, ['.', '..', 'config.js'])) continue;
                        $path = $viewDir . '/' . $f;
                        if (is_file($path) && strtolower(pathinfo($f, PATHINFO_EXTENSION)) === 'php') {
                            $files[] = $f;
                        }
                    }
                }

                // Group files into sections by prefix
                $groups = [
                    'Dashboard' => [],
                    'Admin' => [],
                    'Segmentos' => [],
                    'PDV' => [],
                    'Outros' => []
                ];

                foreach ($files as $f) {
                    $base = pathinfo($f, PATHINFO_FILENAME);
                    // classify
                    if (strpos($base, 'home-') === 0) {
                        $groups['Segmentos'][] = $base;
                    } elseif (strpos($base, 'admin') === 0 || strpos($base, 'adm') === 0) {
                        $groups['Admin'][] = $base;
                    } elseif (stripos($base, 'pdv') !== false) {
                        $groups['PDV'][] = $base;
                    } elseif ($base === 'home' || $base === 'home-admin' || $base === 'home-admins') {
                        $groups['Dashboard'][] = $base;
                    } else {
                        $groups['Outros'][] = $base;
                    }
                }

                // Helper to prettify label
                function pretty_label($s) {
                    $s = str_replace(['home-', '_', '-'], ['', ' ', ' '], $s);
                    $s = preg_replace('/\s+/', ' ', trim($s));
                    $s = ucfirst($s);
                    return $s;
                }

                // Icon maps: group-level default and item-level overrides
                $groupIcons = [
                    'Dashboard' => 'bi-speedometer2',
                    'Admin' => 'bi-gear',
                    'Segmentos' => 'bi-grid',
                    'PDV' => 'bi-bag-check',
                    'Outros' => 'bi-folder'
                ];

                // A small map of specific basenames to more representative icons
                $itemIconMap = [
                    'home-DashboardPDV' => 'bi-bar-chart',
                    'home-DashboardPDVS' => 'bi-bar-chart-line',
                    'home' => 'bi-house',
                    'home-admin' => 'bi-speedometer2',
                    'admin-empresas' => 'bi-building',
                    'admin-filiais' => 'bi-diagram-3',
                    'admin-produtos' => 'bi-box-seam',
                    'gestao_usuarios' => 'bi-people',
                    'pdv-venda' => 'bi-cash-stack',
                    'debug-session' => 'bi-bug',
                    'empresas' => 'bi-shop-window'
                ];

                // Render groups
                foreach ($groups as $title => $items) {
                    if (count($items) === 0) continue;
                    $groupIcon = isset($groupIcons[$title]) ? $groupIcons[$title] : 'bi-circle';
                    echo "<div class=\"nav-section\">";
                    echo "<div class=\"nav-header\"><i class=\"bi $groupIcon nav-header-icon\"></i><span class=\"nav-header-text\">$title</span></div>";

                    foreach ($items as $item) {
                        $label = pretty_label($item);
                        $href = '?view=' . $item;
                        $iconClass = isset($itemIconMap[$item]) ? $itemIconMap[$item] : 'bi-circle';
                        echo "<div class=\"nav-item\"><a href=\"$href\" class=\"nav-link\"><i class=\"bi $iconClass nav-icon\"></i><span class=\"nav-text\">$label</span></a></div>";
                    }

                    echo "</div>";
                }
                ?>
            </div>
        </nav>