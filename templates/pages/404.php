<?php
http_response_code(404);
get_header();
?>

<main class="main main-404">
	<div class="container my-100">
		<h1>This page does not exist.</h1>
		<p>Try checking your url again or <a href="<?= baseUrl() ?>">go to the homepage</a></p>
	</div>
</main>

<?php get_footer(); ?>