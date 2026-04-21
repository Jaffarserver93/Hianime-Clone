
<?php 

$conn = new mysqli("sql113.infinityfree.com", "if0_41711685", "ThI3nYB2Kpqr", "if0_41711685_jxfr"); //just like $conn = new mysqli("localhost", "root", "", "anipaca");


if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    echo("Database connection failed.");
}

$websiteTitle = "AniPaca";
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
$websiteUrl = "{$protocol}://{$_SERVER['SERVER_NAME']}";
$websiteLogo = $websiteUrl . "/public/logo/logo.png";
$contactEmail = "raisulentertainment@gmail.com";

$version = "1.0.2";

$discord = "https://dcd.gg/";
$github = "https://github.com/";
$telegram = "https://t.me/";
$instagram = "https://www.instagram.com/"; 

// all the api you need
$zpi = "https://cooren-labs--ibgchggu.replit.app/anime/animekai";
$proxy = $websiteUrl . "https://m3u8proxy.vercel.app/proxy?url=";

//If you want faster loading speed just put // before the first proxy and remove slashes from this one 
//$proxy = "https://your-hosted-proxy.com/proxy?url="; //https://github.com/PacaHat/shrina-proxy


$banner = $websiteUrl . "/public/images/banner.png";

    
