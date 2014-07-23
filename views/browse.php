<?php 
wp_enqueue_style( 'bootstrap_style', plugins_url( 'hash-viewer/css/bootstrap.min.css'));
wp_enqueue_style( 'hashviewer_style', plugins_url( 'hash-viewer/css/main.css'));
wp_enqueue_script( 'hashviewer_script', plugins_url( 'hash-viewer/js/main.js' ));
?>

<h3>Instagram HashViewer</h3>
<div class="row">
	<div class="col-sm-9">
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

		
	</div>
	<div class="col-sm-2">
		<div id="hashviewer-admin-panel">
			This would be a GREAT place for a admin panel
		</div>
	</div>
</div>
<div class="clearfix"></div>
<footer>
	<hr />
	<p><em>This page is made with the Instagram API.</em>
</footer>


