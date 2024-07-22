<?php

declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', 1);

$subDirectory = isset($_GET['dir']) ? $_GET['dir'] : '';
$directory = __DIR__ . '/' . $subDirectory;

// Function to list sub-directories
function listDirectories($directory) {
    $directories = [];
    foreach (new DirectoryIterator($directory) as $dir) {
        if ($dir->isDir() && !$dir->isDot()) {
            $directories[] = $dir->getFilename();
        }
    }
    return $directories;
}

// Check if directory is valid
if (!is_dir($directory)) {
    die("Invalid directory specified.");
}

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));
$filesToDisplay = [];

foreach ($iterator as $file) {
    if ($file->isFile() && (strtolower($file->getExtension()) === 'php' || in_array($file->getFilename(), ['composer.json', 'package.json']))) {
        $filesToDisplay[] = $file->getPathname();
    }
}

// Page styling
echo "<style>
    body {
        font-family: 'Arial', sans-serif;
        background-color: #f2f2f2;
        color: #333;
        margin: 0;
        padding: 20px;
    }
    form {
        margin-bottom: 20px;
    }
    label {
        font-weight: bold;
    }
    select {
        padding: 5px;
        margin-right: 10px;
    }
    input[type='submit'] {
        padding: 5px 10px;
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 3px;
        cursor: pointer;
    }
    input[type='submit']:hover {
        background-color: #0056b3;
    }
    pre {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        padding: 10px;
        border-radius: 5px;
        white-space: pre-wrap;
        word-wrap: break-word;
    }
</style>";

// Display directory selection form
if (empty($subDirectory)) {
    echo "<form method='GET'>";
    echo "<label for='dir'>Select a directory:</label>";
    echo "<select name='dir' id='dir'>";
    echo "<option value=''>--Select Directory--</option>";
    foreach (listDirectories(__DIR__) as $dir) {
        echo "<option value='$dir'>$dir</option>";
    }
    echo "</select>";
    echo "<input type='submit' value='Go'>";
    echo "</form>";
} else {
    echo "<form method='GET'>";
    echo "<label for='dir'>Select another directory:</label>";
    echo "<select name='dir' id='dir'>";
    echo "<option value=''>--Select Directory--</option>";
    foreach (listDirectories(__DIR__) as $dir) {
        echo "<option value='$dir'>$dir</option>";
    }
    echo "</select>";
    echo "<input type='submit' value='Go'>";
    echo "</form>";

    // Display the list of files
    echo "<pre>";
    foreach ($filesToDisplay as $file) {
        $path = str_replace($directory, '', $file);

        if (
            strpos($path, '/vendor/') !== 0
            && '/analyse.php' !== $path
        ) {
            echo htmlspecialchars($path) . ':' . PHP_EOL;
            echo '```' . htmlspecialchars(file_get_contents($file)) . '```';
            echo PHP_EOL . PHP_EOL . PHP_EOL;
        }
    }
    echo "</pre>";
}
