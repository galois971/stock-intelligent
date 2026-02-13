<?php

// Comprehensive final design improvements script
// This script applies professional enhancements across all remaining files

$basePath = __DIR__ . '/resources/views';

// Improvements to apply
$improvements = [
    // Enhance table styling globally
    'old' => [
        'border border-gray-200 shadow',
        'px-6 py-3 text-left' => 'px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider',
        'hover:bg-gray-100/50' => 'hover:bg-emerald-50/40',
        'rounded-lg' => 'rounded-xl',
        'shadow-sm' => 'shadow-md',
    ],
];

function improveFileDesigns($dir) {
    $count = 0;
    $files = array_diff(scandir($dir), ['.', '..']);
    
    foreach ($files as $file) {
        $path = $dir . '/' . $file;
        
        if (is_dir($path)) {
            $count += improveFileDesigns($path);
        } elseif (substr($file, -11) === '.blade.php') {
            $content = file_get_contents($path);
            $original = $content;
            
            // Enhance form inputs
            $content = str_replace(
                'px-4 py-2 border border-gray-300 rounded-md focus:ring-emerald-500 focus:border-emerald-500',
                'px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition',
                $content
            );
            
            // Enhance buttons
            $content = str_replace(
                'inline-flex px-4 py-2 bg-emerald-600 text-white rounded-md hover:bg-emerald-700 transition',
                'inline-flex px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 text-white rounded-lg hover:from-emerald-700 hover:to-teal-700 transition duration-200 shadow-md hover:shadow-lg',
                $content
            );
            
            // Enhance card/container styling
            $content = str_replace(
                'rounded-lg border border-gray-200 shadow-sm',
                'rounded-xl border border-gray-200 shadow-md hover:shadow-lg transition',
                $content
            );
            
            // Enhance links and hover states
            $content = str_replace(
                'hover:text-emerald-600 hover:bg-gray-100',
                'hover:text-emerald-600 hover:bg-emerald-50/50 transition-colors',
                $content
            );
            
            // Improve table headers
            $content = str_replace(
                'border-b border-gray-200 text-gray-600 uppercase text-xs',
                'bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200 text-gray-700 uppercase text-xs font-bold',
                $content
            );
            
            // Better table row hover
            $content = str_replace(
                'hover:bg-gray-100 transition',
                'hover:bg-emerald-50/30 transition duration-200',
                $content
            );
            
            // Modernize alerts
            $content = str_replace(
                'bg-red-50 border-l-4 border-red-500 p-4 rounded-lg',
                'bg-red-50 border-l-4 border-red-500 p-4 rounded-lg animate-fade-in',
                $content
            );
            
            // Modernize success alerts
            $content = str_replace(
                'bg-green-50 border-l-4 border-green-500 p-4 rounded-lg',
                'bg-green-50 border-l-4 border-green-500 p-4 rounded-lg animate-fade-in',
                $content
            );
            
            if ($content !== $original) {
                file_put_contents($path, $content);
                $count++;
                echo "✓ Enhanced: " . str_replace($GLOBALS['basePath'] . '/', '', $path) . "\n";
            }
        }
    }
    
    return $count;
}

$GLOBALS['basePath'] = $basePath;
$improved = improveFileDesigns($basePath);

// Now improve specific critical files

// Movements index
$movementsPath = $basePath . '/movements/index.blade.php';
if (file_exists($movementsPath)) {
    $content = file_get_contents($movementsPath);
    
    // Add search functionality for movements
    if (strpos($content, 'id="movementsTable"') === false) {
        $content = str_replace(
            '<table class="w-full text-sm text-gray-600">',
            '<table class="w-full text-sm text-gray-600" id="movementsTable">',
            $content
        );
        file_put_contents($movementsPath, $content);
        echo "✓ Enhanced: movements/index.blade.php\n";
        $improved++;
    }
}

// Inventories index
$inventoriesPath = $basePath . '/inventories/index.blade.php';
if (file_exists($inventoriesPath)) {
    $content = file_get_contents($inventoriesPath);
    
    if (strpos($content, 'id="inventoriesTable"') === false) {
        $content = str_replace(
            '<table class="w-full text-sm text-gray-600">',
            '<table class="w-full text-sm text-gray-600" id="inventoriesTable">',
            $content
        );
        file_put_contents($inventoriesPath, $content);
        echo "✓ Enhanced: inventories/index.blade.php\n";
        $improved++;
    }
}

// Alerts index
$alertsPath = $basePath . '/alerts/index.blade.php';
if (file_exists($alertsPath)) {
    $content = file_get_contents($alertsPath);
    
    if (strpos($content, 'rounded-lg') !== false) {
        $content = str_replace('rounded-lg', 'rounded-xl', $content);
        file_put_contents($alertsPath, $content);
        echo "✓ Enhanced: alerts/index.blade.php\n";
        $improved++;
    }
}

echo "\n✓ Total files enhanced: " . $improved . "\n";
?>
