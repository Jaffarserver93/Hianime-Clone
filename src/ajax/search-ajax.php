<?php
require_once('../../_config.php');
require_once('../../src/lib/jikan_adapter.php');
header('Content-Type: application/json');

if (isset($_GET['keyword'])) {
    $keyword = trim($_GET['keyword']); // DO NOT alter the keyword here
    $cacheKey = md5($keyword);
    $cachePath = __DIR__ . '/../../cache/search/';
    $cacheFile = $cachePath . $cacheKey . '.json';
    $cacheTime = 300;

    if (!is_dir($cachePath)) {
        mkdir($cachePath, 0777, true);
    }

    if (file_exists($cacheFile) && (time() - filemtime($cacheFile) < $cacheTime)) {
        echo file_get_contents($cacheFile);
        exit;
    }

    try {
        $data = jikan_search_legacy($keyword, 1);
        $response = json_encode($data);

        if ($data && isset($data['success']) && $data['success']) {
            file_put_contents($cacheFile, $response);
            echo $response;
        } else {
            $errorResponse = json_encode([
                'success' => false,
                'message' => 'No results found'
            ]);
            file_put_contents($cacheFile, $errorResponse);
            echo $errorResponse;
        }
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'No keyword provided'
    ]);
}
