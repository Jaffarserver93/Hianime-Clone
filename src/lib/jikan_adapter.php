<?php

function jikan_api_get(string $path, array $query = []): ?array {
    $base = 'https://api.jikan.moe/v4';
    $url = rtrim($base, '/') . '/' . ltrim($path, '/');
    if (!empty($query)) {
        $url .= '?' . http_build_query($query);
    }

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 25,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_USERAGENT => 'AniPaca/1.0',
        CURLOPT_HTTPHEADER => ['Accept: application/json'],
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    if (!is_string($response) || $response === '') {
        return null;
    }

    $data = json_decode($response, true);
    return is_array($data) ? $data : null;
}

function jikan_extract_data(array $payload): array {
    if (isset($payload['data']) && is_array($payload['data'])) {
        return $payload['data'];
    }
    return [];
}

function jikan_map_item(array $item): array {
    $displayTitle = !empty($item['title_english']) ? $item['title_english'] : ($item['title'] ?? 'Unknown');
    $episodes = $item['episodes'] ?? null;

    return [
        'id' => (string)($item['mal_id'] ?? ''),
        'title' => $displayTitle,
        'jname' => $item['title_japanese'] ?? ($item['title'] ?? $displayTitle),
        'poster' => $item['images']['jpg']['large_image_url']
            ?? $item['images']['jpg']['image_url']
            ?? '',
        'adultContent' => (($item['rating'] ?? '') === 'Rx - Hentai'),
        'tvInfo' => [
            'showType' => $item['type'] ?? 'TV',
            'duration' => $item['duration'] ?? 'N/A',
            'sub' => $episodes,
            'dub' => $episodes,
            'rating' => $item['rating'] ?? null,
            'eps' => $episodes,
        ],
        'duration' => $item['duration'] ?? 'N/A',
        'releaseDate' => $item['aired']['from'] ?? null,
    ];
}

function jikan_legacy_wrap(array $items, int $page = 1, ?int $totalPages = null): array {
    $mapped = array_map('jikan_map_item', $items);
    $tp = $totalPages ?? max(1, (int)($page + (count($mapped) >= 20 ? 1 : 0)));

    return [
        'success' => true,
        'results' => [
            'data' => $mapped,
            'totalPage' => $tp,
            'totalPages' => $tp,
            'currentPage' => $page,
            'hasNextPage' => $page < $tp,
            'total' => count($mapped)
        ]
    ];
}

function jikan_search_legacy(string $keyword, int $page = 1): array {
    $payload = jikan_api_get('anime', [
        'q' => $keyword,
        'page' => $page,
        'order_by' => 'members',
        'sort' => 'desc',
    ]);
    if (!$payload) return ['success' => false, 'results' => ['data' => []]];

    $items = jikan_extract_data($payload);
    $tp = $payload['pagination']['last_visible_page'] ?? null;
    return jikan_legacy_wrap($items, $page, $tp);
}

function jikan_category_legacy(string $category, int $page = 1): array {
    $query = ['page' => $page, 'order_by' => 'score', 'sort' => 'desc'];

    switch ($category) {
        case 'movie':
        case 'tv':
        case 'ova':
        case 'ona':
        case 'special':
        case 'specials':
            $query['type'] = $category === 'specials' ? 'special' : $category;
            $payload = jikan_api_get('anime', $query);
            break;
        case 'top-upcoming':
            $payload = jikan_api_get('top/anime', ['filter' => 'upcoming', 'page' => $page]);
            break;
        case 'most-popular':
            $payload = jikan_api_get('top/anime', ['filter' => 'bypopularity', 'page' => $page]);
            break;
        default:
            $payload = jikan_api_get('seasons/now', ['page' => $page]);
            break;
    }

    if (!$payload) return ['success' => false, 'results' => ['data' => []]];
    $items = jikan_extract_data($payload);
    $tp = $payload['pagination']['last_visible_page'] ?? null;
    return jikan_legacy_wrap($items, $page, $tp);
}

function jikan_genre_legacy(string $genreSlug, int $page = 1): array {
    $payload = jikan_api_get('anime', [
        'q' => str_replace('-', ' ', $genreSlug),
        'page' => $page,
        'order_by' => 'members',
        'sort' => 'desc',
    ]);
    if (!$payload) return ['success' => false, 'results' => ['data' => []]];

    $items = jikan_extract_data($payload);
    $tp = $payload['pagination']['last_visible_page'] ?? null;
    return jikan_legacy_wrap($items, $page, $tp);
}

function jikan_az_legacy(string $letter, int $page = 1): array {
    $payload = jikan_api_get('top/anime', ['page' => $page]);
    if (!$payload) return ['success' => false, 'results' => ['data' => []]];

    $items = jikan_extract_data($payload);
    if (!empty($letter) && $letter !== 'az-list' && $letter !== '0-9') {
        $items = array_values(array_filter($items, function ($item) use ($letter) {
            $title = strtolower($item['title_english'] ?? ($item['title'] ?? ''));
            return str_starts_with($title, strtolower($letter));
        }));
    }

    $tp = $payload['pagination']['last_visible_page'] ?? null;
    return jikan_legacy_wrap($items, $page, $tp);
}

function jikan_random_legacy(): array {
    $payload = jikan_api_get('random/anime');
    if (!$payload || empty($payload['data']['mal_id'])) return ['success' => false];

    return ['success' => true, 'results' => ['id' => (string)$payload['data']['mal_id']]];
}

function jikan_top_ten_legacy(): array {
    $todayPayload = jikan_api_get('top/anime', ['filter' => 'airing', 'limit' => 10]);
    $weekPayload = jikan_api_get('top/anime', ['filter' => 'bypopularity', 'limit' => 10]);
    $monthPayload = jikan_api_get('top/anime', ['filter' => 'favorite', 'limit' => 10]);

    $toList = function (array $items): array {
        $mapped = array_map('jikan_map_item', array_slice($items, 0, 10));
        $n = 1;
        foreach ($mapped as &$item) {
            $item['number'] = $n++;
        }
        return $mapped;
    };

    return [
        'success' => true,
        'results' => [
            'today' => $toList(jikan_extract_data($todayPayload ?? [])),
            'week' => $toList(jikan_extract_data($weekPayload ?? [])),
            'month' => $toList(jikan_extract_data($monthPayload ?? [])),
        ]
    ];
}

function jikan_home_payload(): array {
    $spotPayload = jikan_api_get('seasons/now', ['limit' => 10]);
    $trendPayload = jikan_api_get('top/anime', ['filter' => 'airing', 'limit' => 10]);

    $spotItems = jikan_extract_data($spotPayload ?? []);
    $trendItems = jikan_extract_data($trendPayload ?? []);

    $trending = [];
    $i = 1;
    foreach (array_slice($trendItems, 0, 10) as $item) {
        $m = jikan_map_item($item);
        $m['number'] = $i++;
        $trending[] = $m;
    }

    $spotlights = [];
    foreach (array_slice($spotItems, 0, 10) as $item) {
        $m = jikan_map_item($item);
        $spotlights[] = [
            'id' => $m['id'],
            'title' => $m['title'],
            'jname' => $m['jname'],
            'poster' => $m['poster'],
            'description' => $item['synopsis'] ?? '',
            'tvInfo' => [
                'showType' => $m['tvInfo']['showType'],
                'duration' => $m['tvInfo']['duration'],
                'releaseDate' => $m['releaseDate'] ?? '',
                'quality' => 'HD',
                'episodeInfo' => [
                    'sub' => $m['tvInfo']['sub'] ?? '?',
                    'dub' => $m['tvInfo']['dub'] ?? null,
                ]
            ]
        ];
    }

    return ['trending' => $trending, 'spotlights' => $spotlights];
}
