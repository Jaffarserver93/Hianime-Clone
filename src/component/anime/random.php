<?php 
require_once($_SERVER['DOCUMENT_ROOT'] . '/_config.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/src/lib/jikan_adapter.php');
$getAnime = jikan_random_legacy();
if (isset($getAnime['success']) && $getAnime['success'] === true && isset($getAnime['results'])) {
    $animeId = $getAnime['results']['id'];
    $newURL = "$websiteUrl/details/$animeId";
    header('Location: '.$newURL);
    exit;
} else {  
    header('Location: '.$websiteUrl.'/404');
    exit;
}
?>
