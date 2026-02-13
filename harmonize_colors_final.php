<?php
// Final cleanup pass

$replacements = [
    // Fix focus states
    'focus:border-blue-500' => 'focus:border-indigo-500',
    'focus:ring-blue-500' => 'focus:ring-indigo-500',
    // Fix double hover classes on buttons (remove the lighter one)
    'bg-indigo-600 text-white hover:bg-indigo-700 font-medium rounded-lg hover:bg-indigo-200' => 'bg-indigo-600 text-white hover:bg-indigo-700 font-medium rounded-lg',
    'bg-indigo-600 text-white hover:bg-indigo-700 font-medium rounded-lg hover:bg-indigo-200 transition' => 'bg-indigo-600 text-white hover:bg-indigo-700 font-medium rounded-lg transition',
    // Fix alert text colors
    'text-green-300' => 'text-green-700',
    'text-red-300' => 'text-red-700',
    'text-green-200' => 'text-green-700',
    'text-red-200' => 'text-red-700',
    // Ensure delete buttons are red
    'bg-red-600' => 'bg-red-600',
    'hover:bg-red-700' => 'hover:bg-red-700',
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
            echo "✓ Cleaned: {$relPath}\n";
        }
    }
}

echo "\n✓ Total files cleaned: $count\n";
?>
