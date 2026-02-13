<?php
// Script for harmonizing colors across all blade.php files

$replacements = [
    // Dark theme to light theme
    'bg-gray-950' => 'bg-white',
    'bg-gray-900' => 'bg-white',
     'text-gray-100' => 'text-gray-900',
    'text-gray-400' => 'text-gray-600',
    'border-gray-800' => 'border-gray-200',
    'hover:border-blue-700' => 'hover:border-indigo-600',
    'hover:border-green-700' => 'hover:border-teal-600',
    'hover:border-red-700' => 'hover:border-red-600',
    'hover:border-purple-700' => 'hover:border-purple-600',
    'hover:bg-blue-700' => 'hover:bg-indigo-200',
    'hover:bg-blue-600' => 'hover:bg-indigo-200',
    'hover:bg-green-600' => 'hover:bg-teal-200',
    'hover:bg-gray-800' => 'hover:bg-gray-100',
    // Color accent updates
    'bg-blue-900/30' => 'bg-indigo-100',
    'text-blue-400' => 'text-indigo-600',
    'bg-blue-900' => 'bg-indigo-100',
    'text-blue-500' => 'text-indigo-600',
    'text-blue-600' => 'text-indigo-600',
    'bg-green-900/30' => 'bg-teal-100',
    'text-green-400' => 'text-teal-600',
    'bg-green-600' => 'bg-teal-600',
    'bg-red-900/30' => 'bg-red-100',
    'text-red-400' => 'text-red-600',
    'bg-purple-900/30' => 'bg-purple-100',
    'text-purple-400' => 'text-purple-600',
    'bg-indigo-900/30' => 'bg-indigo-100',
    'text-indigo-400' => 'text-indigo-600',
    'bg-yellow-900' => 'bg-amber-100',
    'text-yellow-400' => 'text-amber-600',
];

$viewsPath = __DIR__ . '/resources/views';
$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($viewsPath),
    RecursiveIteratorIterator::LEAVES_ONLY
);

$count = 0;
foreach ($files as $file) {
    if ($file->getExtension() === 'php' && strpos($file->getPathname(), '.blade.php') !== false) {
        $path = $file->getPathname();
        $content = file_get_contents($path);
        $original = $content;
        
        forEach ($replacements as $find => $replace) {
            $content = str_replace($find, $replace, $content);
        }
        
        if ($content !== $original) {
            file_put_contents($path, $content);
            $count++;
            $relativePath = str_replace(__DIR__ . '\\', '', $path);
            echo "✓ Updated: {$relativePath}\n";
        }
    }
}

echo "\n✓ Total files updated: $count\n";
?>
