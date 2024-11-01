<div class="preparser">
	<div class="preparser-header pp-row pp-h-width">
		<div class="pp-width">
			<h1 class="preparser-title"><?php _e( 'YouTube Preview', $this->textdomain ); ?></h1>
			<div
				class="preparser-desc"><?php echo __( 'Get an available preview of the image for your video on Youtube', $this->textdomain ); ?></div>
		</div>
		<form method="POST" class="preparser-form pp-col w8-12 off2" id="preparser-form" name="preparser-form">
			<input class="preparser-form-input" type="text" data-def="<?php echo $this->baseVideo; ?>"
			       name="you[url]" value="<?php echo $uriRaw ?>" size="40">
			<input class="preparser-form-button" type="submit" id="preparser-form-submit" value="Получить">
		</form>
		<div class="pp-row">
			<div class="preparser-desc-bottom">
				<?php _e( 'Copy the address of the video from YouTube and we\'ll show you all available preview of the image', $this->textdomain ); ?>
			</div>
		</div>
	</div>
	<div class="pp-row preparser-result-wr pp-width">
		<div class="pp-col w8-12 off2 preparser-result">
			<div class="js-result-small-wr">
				<div class="preparser-img-desc">
					<?php _e( 'Thumbnail size 120&times;90', $this->textdomain ); ?></div>
				<div class="preparser-result-small preparser-result-container js-result-small">
					<?php // echo $this->getSmallImg(); ?>
				</div>
			</div>
			<div class="js-result-medium-wr pp-row">
				<div class="preparser-img-desc">
					<?php _e( 'Normal size 480&times;360', $this->textdomain ); ?>
				</div>
				<div class="preparser-result-medium preparser-result-container js-result-medium">
					<?php //echo $this->getMediumImg(); ?>
				</div>
			</div>
			<div class="js-result-full-wr pp-row">
				<div class="preparser-img-desc">
					<?php _e( 'Full size 1920&times;1080', $this->textdomain ); ?>
				</div>
				<div class="preparser-result-full preparser-result-container js-result-full">
					<?php //echo $this->getFullImg(); ?>
				</div>
			</div>
			<div class="preparser-result-iframe preparser-result-container js-result-iframejs-result-iframe">
				<iframe width="640" height="360"
				        src="//www.youtube.com/embed/<?php echo $this->baseVideo; ?>"
				        frameborder="0" allowfullscreen></iframe>
			</div>
		</div>
	</div>
</div>