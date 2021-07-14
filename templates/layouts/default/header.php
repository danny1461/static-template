<!DOCTYPE html>
<html dir="ltr" lang="en-US" class="html5 <?php echo htmlClass() ?>">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title><?php echo title_part('Site Title') ?></title>
	<meta name="theme-color" content="#ff0000">
	<link rel="canonical" href="<?= baseUrl(get_request_uri()) ?>">

	<script>
		function docReady(fn) {
			if (document.readyState != 'loading') {
				fn();
			} else {
				document.addEventListener('DOMContentLoaded', fn);
			}
		}
	</script>

	<?php
	stylesheet_register('fonts', 'https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600|Roboto:300,400,700');
	stylesheet_register('bootstrap',  publicUrl('css/bootstrap.min.css?ver=4.0.0'));
	stylesheet_enqueue('style', publicUrl('css/style.min.css'), ['fonts', 'bootstrap']);

	script_register('fa-all', publicUrl('js/fa/all.min.js'));
	script_register('bootstrap.bundle', publicUrl('js/bootstrap.bundle.min.js'));
	script_enqueue('main', publicUrl('js/main.min.js'), ['bootstrap.bundle', 'fa-all']);

	meta('og:site_name', 'Site Title');
	meta('twitter:title', 'Site Title');

	header_resources();
	?>
</head>

<body class="<?php echo bodyClass() ?>">

	<?php if (is_multilingual()) : ?>
		<div class="language-switcher">
			<ul class="language-list">
				<?php foreach (get_languages() as $lang => $langName) : ?>
					<?php
					$active = $lang == current_lang() ? 'active' : '';
					?>
					<li class="language-item lang-<?= $lang ?> <?= $active ?>">
						<a class="language-link <?= $active ?>" href="<?= baseUrl(get_request_uri(), $lang)  ?>"><?= $langName ?></a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endif; ?>

	<header class="site-header">
		<nav class="navbar navbar-expand-lg navbar-light bg-light">
			<div class="container-fluid">
				<a class="navbar-brand" href="#">
					<img src="<?php echo publicUrl('images/logo.png') ?>" class="img-fluid" alt="Go to home page">
				</a>
				<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#main-nav" aria-controls="main-nav" aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-toggler-icon"></span>
				</button>
				<div class="collapse navbar-collapse ml-lg-auto" id="main-nav">
					<ul class="navbar-nav me-auto mb-2 mb-lg-0">
						<li class="nav-item <?php echo get_request_uri() == '' ? 'active' : '' ?>">
							<a class="nav-link" aria-current="page" href="<?php echo baseUrl() ?>">Home</a>
						</li>
						<li class="nav-item <?php echo get_request_uri() == 'subpage' ? 'active' : '' ?>">
							<a class="nav-link" href="<?php echo baseUrl('subpage') ?>">Subpage</a>
						</li>
					</ul>
				</div>
			</div>
		</nav>
	</header>