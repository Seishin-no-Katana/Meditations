<?php
$token = $_GET['token'] ?? '';
$tokens = json_decode(file_get_contents('tokens.json'), true);

if (!$token || !isset($tokens[$token])) {
    die("Lien invalide ou expiré.");
}

$data = $tokens[$token];

// Vérifie expiration
if ($data['expire'] < time()) {
    unset($tokens[$token]);
    file_put_contents('tokens.json', json_encode($tokens));
    die("Lien expiré.");
}

$file = __DIR__ . "/pdfs/" . $data['file'];
if (!file_exists($file)) die("Fichier introuvable.");

// Force le téléchargement
header('Content-Description: File Transfer');
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="'.basename($file).'"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($file));
readfile($file);

// Supprime le token après téléchargement
unset($tokens[$token]);
file_put_contents('tokens.json', json_encode($tokens));
exit;
