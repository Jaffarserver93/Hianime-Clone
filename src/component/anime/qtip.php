<?php

function animeHttpGetJson(string $url, int $timeout = 15): array
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_CONNECTTIMEOUT => 8,
        CURLOPT_TIMEOUT => $timeout,
        CURLOPT_USERAGENT => 'AniPaca/1.0 (+https://anipaca)',
        CURLOPT_HTTPHEADER => [
            'Accept: application/json',
        ],
    ]);

    $body = curl_exec($ch);
    $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    $json = null;
    if (is_string($body) && $body !== '') {
        $decoded = json_decode($body, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            $json = $decoded;
        }
    }

    return [
        'status' => $status,
        'body' => $body,
        'json' => $json,
        'error' => $error,
    ];
}

function normalizeAnimekaiResponse(array $animeResponse, string $animeId): array
{
    $data = $animeResponse['results']['data'] ?? [];
    $animeInfo = $data['animeInfo'] ?? [];
    $tvInfo = $animeInfo['tvInfo'] ?? [];
    $seasons = $animeResponse['results']['seasons'] ?? [];

    $seasonList = [];
    foreach ($seasons as $season) {
        $seasonList[] = [
            'id' => $season['id'] ?? null,
            'name' => $season['season'] ?? null,
            'title' => $season['title'] ?? null,
            'poster' => $season['season_poster'] ?? null,
            'isCurrent' => (($season['id'] ?? null) === $animeId),
        ];
    }

    $chList = [];
    foreach (($data['charactersVoiceActors'] ?? []) as $character) {
        $voiceActors = [];
        foreach (($character['voiceActors'] ?? []) as $actor) {
            $voiceActors[] = [
                'id' => $actor['id'] ?? null,
                'name' => $actor['name'] ?? 'Unknown voice actor',
                'poster' => $actor['poster'] ?? null,
            ];
        }

        $chList[] = [
            'character' => [
                'id' => $character['character']['id'] ?? null,
                'name' => $character['character']['name'] ?? 'Unknown character',
                'poster' => $character['character']['poster'] ?? null,
                'cast' => $character['character']['cast'] ?? null,
            ],
            'voiceActors' => $voiceActors,
        ];
    }

    $recommendedAnimeList = [];
    foreach (($data['recommended_data'] ?? []) as $recommendedAnime) {
        $recommendedAnimeList[] = [
            'id' => $recommendedAnime['id'] ?? null,
            'data_id' => $recommendedAnime['data_id'] ?? null,
            'name' => $recommendedAnime['title'] ?? $recommendedAnime['jname'] ?? null,
            'japanese' => $recommendedAnime['jname'] ?? null,
            'poster' => $recommendedAnime['poster'] ?? null,
            'duration' => $recommendedAnime['tvInfo']['duration'] ?? null,
            'type' => $recommendedAnime['tvInfo']['showType'] ?? null,
            'adultContent' => $recommendedAnime['adultContent'] ?? false,
            'episodes' => [
                'sub' => $recommendedAnime['tvInfo']['sub'] ?? $recommendedAnime['tvInfo']['eps'] ?? null,
                'dub' => $recommendedAnime['tvInfo']['dub'] ?? null,
            ],
        ];
    }

    $relatedAnimeList = [];
    foreach (($data['related_data'] ?? []) as $relatedAnime) {
        $relatedAnimeList[] = [
            'id' => $relatedAnime['id'] ?? null,
            'data_id' => $relatedAnime['data_id'] ?? null,
            'name' => $relatedAnime['title'] ?? $relatedAnime['jname'] ?? null,
            'japanese' => $relatedAnime['jname'] ?? null,
            'poster' => $relatedAnime['poster'] ?? null,
            'type' => $relatedAnime['tvInfo']['showType'] ?? null,
            'adultContent' => $relatedAnime['adultContent'] ?? false,
            'episodes' => [
                'sub' => $relatedAnime['tvInfo']['sub'] ?? $relatedAnime['tvInfo']['eps'] ?? null,
                'dub' => $relatedAnime['tvInfo']['dub'] ?? null,
            ],
        ];
    }

    return [
        'poster' => $data['poster'] ?? 'default_poster.jpg',
        'id' => $data['id'] ?? $animeId,
        'malId' => $data['malId'] ?? null,
        'anilistId' => $data['anilistId'] ?? null,
        'data_id' => $data['data_id'] ?? null,
        'title' => $data['title'] ?? null,
        'japanese' => $data['jname'] ?? null,
        'synonyms' => $data['synonyms'] ?? null,
        'overview' => $animeInfo['Overview'] ?? 'No description',
        'showType' => $tvInfo['showType'] ?? null,
        'rating' => $tvInfo['rating'] ?? null,
        'subEp' => $tvInfo['sub'] ?? $tvInfo['eps'] ?? 0,
        'dubEp' => $tvInfo['dub'] ?? 0,
        'aired' => $animeInfo['Aired'] ?? null,
        'premiered' => $animeInfo['Premiered'] ?? null,
        'malscore' => $animeInfo['MAL Score'] ?? null,
        'status' => $animeInfo['Status'] ?? null,
        'genres' => $animeInfo['Genres'] ?? [],
        'quality' => $tvInfo['quality'] ?? null,
        'duration' => $tvInfo['duration'] ?? $animeInfo['Duration'] ?? null,
        'actors' => $chList,
        'studio' => $animeInfo['Studios'] ?? null,
        'producer' => $animeInfo['Producers'] ?? [],
        'season' => $seasonList,
        'relatedAnimes' => $relatedAnimeList,
        'recommendedAnimes' => $recommendedAnimeList,
        'adultContent' => $data['adultContent'] ?? false,
        'source' => 'animekai',
    ];
}

function normalizeJikanResponse(array $anime, string $animeId): array
{
    $genres = [];
    foreach (($anime['genres'] ?? []) as $genre) {
        if (!empty($genre['name'])) {
            $genres[] = $genre['name'];
        }
    }

    $producers = [];
    foreach (($anime['producers'] ?? []) as $producer) {
        if (!empty($producer['name'])) {
            $producers[] = $producer['name'];
        }
    }

    return [
        'poster' => $anime['images']['jpg']['large_image_url']
            ?? $anime['images']['jpg']['image_url']
            ?? 'default_poster.jpg',
        'id' => $animeId,
        'malId' => $anime['mal_id'] ?? null,
        'anilistId' => null,
        'data_id' => null,
        'title' => $anime['title'] ?? null,
        'japanese' => $anime['title_japanese'] ?? null,
        'synonyms' => $anime['title_synonyms'] ?? [],
        'overview' => $anime['synopsis'] ?? 'No description',
        'showType' => $anime['type'] ?? null,
        'rating' => $anime['rating'] ?? null,
        'subEp' => $anime['episodes'] ?? 0,
        'dubEp' => 0,
        'aired' => $anime['aired']['string'] ?? null,
        'premiered' => $anime['season'] ?? null,
        'malscore' => $anime['score'] ?? null,
        'status' => $anime['status'] ?? null,
        'genres' => $genres,
        'quality' => null,
        'duration' => $anime['duration'] ?? null,
        'actors' => [],
        'studio' => $anime['studios'][0]['name'] ?? null,
        'producer' => $producers,
        'season' => [],
        'relatedAnimes' => [],
        'recommendedAnimes' => [],
        'adultContent' => (($anime['rating'] ?? '') === 'Rx - Hentai'),
        'source' => 'jikan',
    ];
}

function fetchAnimeData($animeId)
{
    $formattedTitle = urldecode((string)$animeId);
    $formattedTitle = str_replace(['-', '_'], ' ', $formattedTitle);
    $formattedTitle = preg_replace('/\s+/', ' ', $formattedTitle ?? '');
    $formattedTitle = trim((string)$formattedTitle);

    if ($formattedTitle === '') {
        return false;
    }

    $jikanUrl = 'https://api.jikan.moe/v4/anime?q=' . rawurlencode($formattedTitle) . '&order_by=popularity&sort=asc&limit=1';
    $jikan = animeHttpGetJson($jikanUrl);

    if ($jikan['status'] !== 200 || empty($jikan['json']['data'][0])) {
        return false;
    }

    return normalizeJikanResponse($jikan['json']['data'][0], (string)$animeId);
}
