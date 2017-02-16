<!DOCTYPE html>
<html {if $lan=='ar'}dir="rtl"{/if} lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Drop Market - Mobile</title>
		
		<link href="{$settings['rootdir']}/assets/css/bootstrap.min.css" rel="stylesheet">    
		<link href="{$settings['rootdir']}/assets/css/select2.css" rel="stylesheet" />
		<link href="{$settings['rootdir']}/assets/css/select2-bootstrap.css" rel="stylesheet" />
		<link href="{$settings['rootdir']}/assets/css/mobile.css" rel="stylesheet">    

		<script src="{$settings['rootdir']}/assets/js/jquery-3.1.1.min.js"></script>
		<script src="{$settings['rootdir']}/assets/js/jquery.validate.min.js"></script>
		<script src="{$settings['rootdir']}/assets/js/bootstrap.min.js"></script>
		<script src="{$settings['rootdir']}/assets/js/select2.min.js"></script>
		<script src="{$settings['rootdir']}/assets/js/mobile.js"></script>

    <link rel="apple-touch-icon" sizes="180x180" href="{$settings['rootdir']}/assets/img/apple-touch-icon.png">
    <link rel="icon" type="image/png" href="{$settings['rootdir']}/assets/img/favicon-32x32.png" sizes="32x32">
    <link rel="icon" type="image/png" href="{$settings['rootdir']}/assets/img/favicon-16x16.png" sizes="16x16">

	</head>
	<body class="mobile">		
		<strong><a href="{$settings['rootdir']}/mobile.php">{$settings['site_name']}</a></strong>
		{if $smarty.session.user}
	 		{if $camps|count>1}
	 			<div id="campselect">
					<select name="campselect" dir="rtl">
					 		{foreach $camps as $c}
				 				<option value="?barcode={$smarty.get.barcode|escape:'html'}&camp={$c['id']}" {if $c['id']==$currentcamp['id']}selected{/if}>{$c['name']}</option>
					 		{/foreach}
					</select>
	 			</div>
	 		{elseif $camps|count==1}
	 				{$camps[0]['name']}
	 		{/if}
		{/if}
		<hr>
		{if $data['message']}<div class="message {if $data['warning']}warning{/if}">{$data['message']}</div>{/if}
		{if $include}{include file="{$include}"}{/if}
		{if $smarty.session.user}
			<footer>
				<a href="?logout=1">{$translate['cms_menu_logout']}</a>
			</footer>
		{/if}
		<div id="loading"><div class="cp-spinner cp-round"></div></div>
	</body>
</html>