<?php
title_part('Home');
get_header();
?>

<main class="py-100">
	<div class="container">

		<div class="row justify-content-between">
			<div class="col-lg-7">
				<article class="article">
					<h1><?= getPageData('left.title') ?></h1>
					<?= getPageData('left.content') ?>
				</article>
			</div>
			<aside class="col-lg-4">
				<h2>Sidebar?!</h2>
				<p>many design, plz full. i can haz text! very ipsum! much layout. wow. very full! plz full. much full, many design, want dolor. such doge. such aenean! such aenean! plz doge! many design, very ipsum! need word. plz full. such doge. such word! much full, very full! need swag! such word! plz ipsum. very ipsum! many consectetur. so swag. very layout. wow! wow, need word. very full! so adipiscing. need word. wow! plz full. </h3>
				<div class="mt-4 d-flex flex-wrap justify-content-around align-items-center">
					<i class="fab fa-fort-awesome fa-2x"></i>
					<i class="fab fa-bootstrap fa-2x"></i>
					<i class="fab fa-sass fa-2x"></i>
					<i class="fab fa-php fa-2x"></i>
					<i class="fab fa-js-square fa-2x"></i>
				</div>
			</aside>
		</div>

		<div class="row">
			<div class="col-lg-4 my-5 mt-lg-100">
				<div class="card">
					<img class="card-img-top img-fluid mb-3" src="https://placeimg.com/640/400/tech" alt="Card image cap">
					<div class="card-body">
						<h4 class="card-title">Card title</h4>
						<p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
						<a href="#" class="btn btn-primary">Go somewhere</a>
					</div>
				</div>
			</div>
			<div class="col-lg-4 my-5 mt-lg-100">
				<div class="card">
					<img class="card-img-top img-fluid mb-3" src="https://placeimg.com/640/400/tech" alt="Card image cap">
					<div class="card-body">
						<h4 class="card-title">Card title</h4>
						<p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
						<a href="#" class="btn btn-primary">Go somewhere</a>
					</div>
				</div>
			</div>
			<div class="col-lg-4 my-5 mt-lg-100">
				<div class="card">
					<img class="card-img-top img-fluid mb-3" src="https://placeimg.com/640/400/tech" alt="Card image cap">
					<div class="card-body">
						<h4 class="card-title">Card title</h4>
						<p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
						<a href="https://www.amazon.co.uk/Flossers-Fairywill-Rechargeable-Waterproof-Irragator/dp/B08P53R5MT/ref=sr_1_2?dchild=1&keywords=CREMAX+flosser&qid=1623104819&sr=8-2&tag=097-56" class="btn btn-primary">Go somewhere</a>
					</div>
				</div>
			</div>
		</div>

	</div>
</main>

<?php get_footer(); ?>