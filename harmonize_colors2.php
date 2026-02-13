<?php
// Script for comprehensive color harmonization

$replacements = [
    // Input fields with dark backgrounds
    'bg-gray-800 text-white' => 'bg-white text-gray-900',
    'border-gray-700 bg-gray-800' => 'border-gray-300 bg-white',
    'text-gray-300' => 'text-gray-700',
    'text-gray-200' => 'text-gray-800',
    'bg-gray-800' => 'bg-white',
    'border-gray-700' => 'border-gray-300',
    // Slate colors to standard colors (from partial replacements)
    'text-slate-100' => 'text-gray-900',
    'text-slate-300' => 'text-gray-600',
    'text-slate-400' => 'text-gray-600',
    'text-slate-800' => 'bg-white text-gray-900',
    'bg-slate-800' => 'bg-white',
    'border-slate-700' => 'border-gray-200',
    'hover:bg-slate-700' => 'hover:bg-gray-100',
    'divide-slate-700' => 'divide-gray-200',
    'bg-slate-700' => 'bg-gray-100',
    // Text color corrections in inputs and labels
    'text-gray-300 mb-2' => 'text-gray-700 mb-2',
    'text-gray-200' => 'text-gray-800',
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
