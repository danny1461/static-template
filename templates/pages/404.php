<?php
http_response_code(404);
get_header();
?>

<main class="main main-404">
	<div class="container my-100">
		<h1>Are you lost? This page does not exist.</h1>
		<p>Try checking your url again or click <a href="<?= baseUrl() ?>">here</a> to go to the homepage.</p>
	</div>
</main>

<?php get_footer(); ?>