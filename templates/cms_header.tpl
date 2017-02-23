<!DOCTYPE html>
<html {if $lan=='ar'}dir="rtl"{/if} lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{if $title}{$title} - {/if}{$translate['site_name']}</title>

    <!-- Bootstrap -->
    <link href="{$settings['rootdir']}/assets/css/css.php?v=2" rel="stylesheet">
    <link href="{$settings['rootdir']}/assets/css/custom.css" rel="stylesheet">    
    <link href="{$settings['rootdir']}/assets/css/print.css" rel="stylesheet" media="print">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

    <link rel="apple-touch-icon" sizes="180x180" href="{$settings['rootdir']}/assets/img/apple-touch-icon.png">
    <link rel="icon" type="image/png" href="{$settings['rootdir']}/assets/img/favicon-32x32.png" sizes="32x32">
    <link rel="icon" type="image/png" href="{$settings['rootdir']}/assets/img/favicon-16x16.png" sizes="16x16">
    
  </head>

  <body class="{$action}">