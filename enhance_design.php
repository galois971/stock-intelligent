<?php
// Professional design enhancements

$replacements = [
    // Shadows improvements
    'border border-gray-200 overflow-hidden hover:border-emerald-600 hover:shadow-lg transition-shadow' => 'border border-gray-200 overflow-hidden hover:border-emerald-600 hover:shadow-md transition-all duration-300',
    'border border-gray-200 overflow-hidden' => 'border border-gray-200 overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-300',
    'rounded-lg border border-gray-200 p-5 hover:border-emerald-600 hover:shadow-md transition-all' => 'rounded-lg border border-gray-200 p-5 hover:border-emerald-600 hover:shadow-md transition-all duration-300 hover:-translate-y-0.5',
    
    // Improve table headers
    'bg-gray-100 border-b border-gray-300' => 'bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200',
    'bg-gray-50 border-t border-gray-200' => 'bg-gradient-to-r from-gray-50 to-gray-100 border-t border-gray-200',
    
    // Better spacing
    'px-6 py-3 text-left text-xs font-medium text-gray-700' => 'px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase letter-spacing tracking-wide',
    
    // Form improvements
    'border border-gray-300 bg-white text-gray-900 p-3 rounded-lg' => 'border border-gray-300 bg-white text-gray-900 p-3 rounded-lg shadow-sm focus:shadow-md transition-shadow',
    
    // Button improvements
    'px-6 py-3 bg-emerald-600 text-white' => 'px-6 py-3 bg-emerald-600 text-white shadow-md hover:shadow-lg',
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
            echo "✓ Enhanced: {$relPath}\n";
        }
    }
}

echo "\n✓ Total files enhanced: $count\n";
?>
