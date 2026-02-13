<?php
// Final harmonization pass for buttons and actions

$replacements = [
    // Button colors
    'bg-blue-600 text-white' => 'bg-indigo-600 text-white hover:bg-indigo-700',
    'hover:bg-blue-700' => 'hover:bg-indigo-700',
    'bg-blue-600' => 'bg-indigo-600',
    'focus:border-blue-500 focus:ring-blue-500' => 'focus:border-indigo-500 focus:ring-indigo-500',
    'focus:ring-blue-500' => 'focus:ring-indigo-500',
    'focus:border-blue-600' => 'focus:border-indigo-600',
    // Action button colors
    'bg-yellow-500' => 'bg-amber-600',
    'hover:bg-yellow-600' => 'hover:bg-amber-700',
    'text-yellow-' => 'text-amber-',
    'bg-green-600' => 'bg-teal-600',
    'hover:bg-green-700' => 'hover:bg-teal-700',
    // Focus states
    'focus:ring-2' => 'focus:ring-1',
    // Modal and card backgrounds
    'bg-gray-50' => 'bg-gray-50',
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
            $relPath = str_replace(__DIR__ . '\\', '', $path);
            echo "✓ Updated: {$relPath}\n";
        }
    }
}

echo "\n✓ Total files updated: $count\n";
?>
