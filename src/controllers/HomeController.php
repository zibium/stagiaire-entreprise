<?php

namespace JobBoard\Controllers;

use JobBoard\Utils\UrlHelper;

class HomeController
{
    public function index()
    {
        
        echo "<!DOCTYPE html>";
        echo "<html lang='fr'>";
        echo "<head>";
        echo "<meta charset='UTF-8'>";
        echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
        echo "<title>JobBoard - Plateforme de stages</title>";
        echo "<link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>";
        echo "<link href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css' rel='stylesheet'>";
        echo "</head>";
        echo "<body>";
        
        // Navigation simple
        echo "<nav class='navbar navbar-expand-lg navbar-dark bg-primary'>";
        echo "<div class='container'>";
        echo "<a class='navbar-brand' href='/'>";
        echo "<i class='fas fa-briefcase me-2'></i>JobBoard";
        echo "</a>";
        echo "<div class='navbar-nav ms-auto'>";
        echo "<a class='nav-link' href='" . UrlHelper::url('auth/login') . "'>Connexion</a>";
        echo "<a class='nav-link' href='" . UrlHelper::url('auth/register') . "'>Inscription</a>";
        echo "</div>";
        echo "</div>";
        echo "</nav>";
        
        // Contenu principal
        echo "<div class='container mt-5'>";
        echo "<div class='row'>";
        echo "<div class='col-lg-8 mx-auto text-center'>";
        echo "<h1 class='display-4 mb-4'>Bienvenue sur JobBoard</h1>";
        echo "<p class='lead mb-4'>La plateforme qui connecte les stagiaires et les entreprises</p>";
        
        echo "<div class='row g-3 mt-4'>";
        echo "<div class='col-md-6'>";
        echo "<div class='card h-100'>";
        echo "<div class='card-body text-center'>";
        echo "<i class='fas fa-user-graduate fa-3x text-primary mb-3'></i>";
        echo "<h5>Vous êtes stagiaire ?</h5>";
        echo "<p>Trouvez le stage de vos rêves</p>";
        echo "<a href='" . UrlHelper::url('auth/login-stagiaire') . "' class='btn btn-primary'>Se connecter</a>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
        echo "<div class='col-md-6'>";
        echo "<div class='card h-100'>";
        echo "<div class='card-body text-center'>";
        echo "<i class='fas fa-building fa-3x text-success mb-3'></i>";
        echo "<h5>Vous êtes une entreprise ?</h5>";
        echo "<p>Recrutez les meilleurs talents</p>";
        echo "<a href='" . UrlHelper::url('auth/login-entreprise') . "' class='btn btn-success'>Se connecter</a>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
        
        echo "</div>";
        echo "</div>";
        echo "</div>";
        
        echo "<script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js'></script>";
        echo "</body>";
        echo "</html>";
    }
}