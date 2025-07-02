<?php
// Imports globaux pour les vues
use JobBoard\Utils\UrlHelper as UrlHelper;

// Créer un alias global pour UrlHelper dans les vues
if (!class_exists('UrlHelper', false)) {
    class_alias('JobBoard\\Utils\\UrlHelper', 'UrlHelper');
}

// Variables globales disponibles dans toutes les vues
$appName = 'JobBoard';
$appVersion = '1.0.0';
?>