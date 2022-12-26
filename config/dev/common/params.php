<?php

$secrets = require __DIR__ . "/../../secrets.php";

return [
    'synchronizer' => [
        'this_project' => [
            'baseRepository' => [
                'dirpath' => YII_PROJECT_ROOT . "/public_html/uploads"
            ],
        ],
        'objects_project' => [
            'baseRepository' => [
                'dirpath' => YII_PROJECT_ROOT . "/public_html/uploads"
            ],
        ],
        'frontend_project' => [
            'baseRepository' => [
                'dirpath' => YII_PROJECT_ROOT . "/public_html/uploads"
            ],
        ]
    ],
    'compressorPath' => 'C:\Users\\tim-a\Desktop\pdfcompressor\pdf_compressor.py',
    'pythonPath' => 'C:\Python310\python.exe',
];
