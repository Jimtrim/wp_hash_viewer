<?php 
wp_enqueue_style( 'bootstrap_style', '//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css');
wp_enqueue_style( 'hashviewer_gallery', plugins_url( 'hash-viewer/css/gallery.css'));
wp_enqueue_script( 'hashviewer_script', plugins_url( 'hash-viewer/js/main.js' ));
?>

<h1>Instagram HashViewer</h1>

<div class="container col-md-11">
	<div class="row">
		<div class="col-md-12">
			<div class="form-group">

				<div class="input-group">
					<input type="text" id="tag-text" class="form-control">
					<span class="input-group-btn">
						<button class="btn btn-default" type="button" id="tag-btn" onClick="HashViewer.updateTag()">Go!</button>
					</span>
				</div>

				<span class="help-block">Input Instagram tag</span>
			</div>
		</div>
	</div>

	<div class="container col-sm-12">
		<div id="error-container" class="hidden alert alert-danger">
		</div>
		<div id="gallery" class="row">
		</div>
		<div class="col-xs-12 text-center">
			<button onclick="HashViewer.updateGallery()" class="hidden btn btn-default btn-lg" type="button" id="more-btn">Load more</button>
		</div>
	</div>

	<div class="clearfix"></div>
	<footer>
		<hr />
		<p><em>This page is made with the Instagram API.</em> <a href="https://github.com/Jimtrim/TagViewer/">GitHub</a></p>
	</footer>
</div>


