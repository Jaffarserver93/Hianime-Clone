<?php
require_once('../../_config.php');
require_once('../../src/lib/jikan_adapter.php');

header('Content-Type: application/json');

try {
    $home = jikan_home_payload();
    $top = jikan_top_ten_legacy();

    if (
        !is_array($home) ||
        !isset($home['trending'], $home['spotlights']) ||
        !is_array($top) ||
        empty($top['success'])
    ) {
        http_response_code(502);
        echo json_encode([
            'success' => false,
            'message' => 'Failed to load home sections',
        ]);
        exit;
    }

    echo json_encode([
        'success' => true,
        'results' => [
            'trending' => $home['trending'],
            'spotlights' => $home['spotlights'],
            'top10' => $top['results'],
        ],
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Unexpected server error',
    ]);
}
