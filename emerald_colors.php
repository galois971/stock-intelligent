<?php
// Replace indigo with emerald throughout the project

$replacements = [
    // Primary color changes
    'bg-indigo-600' => 'bg-emerald-600',
    'bg-indigo-700' => 'bg-emerald-700',
    'bg-indigo-800' => 'bg-emerald-800',
    'bg-indigo-100' => 'bg-emerald-50',
    'hover:bg-indigo-700' => 'hover:bg-emerald-700',
    'hover:bg-indigo-200' => 'hover:bg-emerald-200',
    'focus:bg-indigo-700' => 'focus:bg-emerald-700',
    'text-indigo-600' => 'text-emerald-600',
    'text-indigo-700' => 'text-emerald-700',
    'border-indigo-600' => 'border-emerald-600',
    'hover:border-indigo-600' => 'hover:border-emerald-600',
    'focus:border-indigo-500' => 'focus:border-emerald-500',
    'focus:ring-indigo-500' => 'focus:ring-emerald-500',
    'focus:ring-indigo-600' => 'focus:ring-emerald-600',
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

echo "\n✓ Total files updated to Emerald: $count\n";
?>
