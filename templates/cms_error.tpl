{include file="cms_header.tpl"}
	<div class="login-reset-template">
		<h1>{$translate['site_name']}</h1>
		<div class="well-center">
			<h2>{$translate['cms_error']}</h2>
			<p>{$error}</p>
			{if isset($exception)}
			<small><pre>{$exception}</pre></small>
			{/if}
		</div>
	</div>
{include file="cms_footer.tpl"}