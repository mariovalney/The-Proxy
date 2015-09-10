<?php

/* 
 * The Header of theme "AvantDoc".
 * 
 * @package Default
 */
?>
<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <meta charset="UTF-8">
        
        <title><?php av_title() ?></title>
        
        <meta name="description" content="<?php get_meta('description', 'Sistema de Controle de Pacotes do Proxy') ?>">
        <meta name="keywords" content="<?php get_meta('keywords', 'theproxy, faculdade, proxy, estudo') ?>">
        <meta name="author" content="<?php get_meta('author', 'Mário Valney') ?>">
        
        <meta property="og:title" content="<?php av_title() ?>"/>
        <meta property="og:description" content="<?php get_meta('description') ?>"/>
        <meta property="og:url" content="<?php get_meta('url', get_permalink()) ?>"/>
        <meta property="og:type" content="<?php get_meta('og-type', 'website') ?>"/>
        <meta property="og:image" content="<?php theme_file_url('img/facebook.jpg') ?>"/>

        <link href="<?php theme_file_url('favicon.png') ?>" rel="icon" />
        
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link type="text/css" rel="stylesheet" href="<?php theme_file_url('css/materialize.min.css'); ?>"  media="screen,projection"/>
        <link type="text/css" rel="stylesheet" href="<?php theme_file_url('css/main.css'); ?>"/>
    </head>
    <body class="<?php body_class() ?>">
        <header class="navbar-fixed">
            <nav>
                <div class="nav-wrapper blue darken-3 row">
                    <div class="col s10 offset-s1">
                        <a href="<?php echo BASE_URL ?>" class="brand-logo center">THE PROXY</a>
                    </div>
                </div>
            </nav>
        </header>
        <div class="main roll">