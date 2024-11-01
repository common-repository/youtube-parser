<?php

/**
 * Plugin Name: SA_ytp Youtube Parser
 * Plugin URI: http://sadesign.pro/services/youtubeparser/
 * Description: Take away video screenshots from a link. Locate shortcode [sa_ytp] on the page.
 * Version: 1.0.3
 * Author: sadesign
 * Author URI: http://sadesign.pro
 * Created by PhpStorm.
 * User: arizona
 * Date: 05.08.2016
 * Time: 11:06
 *
 */
class SA_ytp {
	var $textdomain;
	var $shortcode;
	var $baseVideo;
	var $videoId;

	function __construct() {
		$this->textdomain = 'sa_ytp';
		$this->shortcode  = 'sa_ytp';
		$this->baseVideo  = 'c6bEs3dxjPg';
		add_action( 'init', array( $this, 'init' ) );
	}

	function init() {
		load_plugin_textdomain( $this->textdomain, false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		add_action( 'wp_enqueue_scripts', array( $this, 'registerStyle' ) );
		add_shortcode( $this->shortcode, array( $this, 'doShortcode' ) );
		add_shortcode( 'previewparser', array( $this, 'doShortcode' ) );

		add_action( 'wp_ajax_get_preview_img', array( $this, 'getPreviewImg' ) );
		add_action( 'wp_ajax_nopriv_get_preview_ img', array( $this, 'getPreviewImg' ) );
	}

	function registerStyle() {
		wp_register_style( 'sa_ytp', plugins_url( 'css/style.css', __FILE__ ) );
		wp_register_script( 'sa_ytp', plugins_url( 'js/script.js', __FILE__ ), array( 'jquery' ) );

	}

	function doShortcode( $args ) {
		wp_enqueue_style( 'sa_ytp' );
		wp_enqueue_script( 'sa_ytp' );
		wp_localize_script( 'sa_ytp', 'sa_ytp',
			array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
		$args = shortcode_atts( array(
			'h-width' => 'full',
			'uridef'  => $this->baseVideo,
		), $args );

		$w = $args['h-width'];
		switch ( $w ) {
			case 'full' :
				$width = '100%';
				break;
			case '0' :
				$width = '980px';
				break;
			default :
				$width = $w . 'px';
		}
		$uriRaw = '';
		if ( isset( $_GET['url'] ) && ! empty( $_GET['url'] ) ) {
			$uriRaw = htmlspecialchars( $_GET["url"] );
		} else if ( isset( $_POST["you"]["url"] ) ) {
			$uriRaw = htmlspecialchars( $_POST["you"]["url"] );
		}
		if ( $width ) {
			?>
			<style>
				.pp-h-width {
					width: <?php echo $width; ?>;
				}
			</style>
		<?php }
		include 'template.php';
	}

	function getPreview( $uriRaw, $isFullUrlReceive = false ) {
		if ( $isFullUrlReceive == true ) {
			$this->videoId = $this->getId( $uriRaw );
		} else {
			$this->videoId = $uriRaw;
		}
		$smallImage  = $this->getSmallImg();
		$mediumImage = $this->getMediumImg();
		$fullImage   = $this->getFullImg();
		if ( empty( $smallImage ) && empty( $mediumImage ) && empty( $fullImage ) ) {
			$this->videoId = $this->baseVideo;
		}
	}

	function getPreviewImg() {
		$uriRaw = $_POST['url'];
		$type   = $_POST['type'];

		$isFullUrlReceive = (bool) $_POST['if_full_url_received'];

		if ( $isFullUrlReceive == true ) {
			$this->videoId = $this->getId( $uriRaw );
		} else {
			$this->videoId = $uriRaw;
		}
		//todo: проверка на адекватность урл
		$headers = get_headers('http://www.youtube.com/oembed?url=http://www.youtube.com/watch?v=' . $this->videoId);
		if (!strpos($headers[0], '200')) {
			$this->videoId = $this->baseVideo;
		}
//		wp_send_json_error( array(
////			'POST'             => $_POST,
////			'isFullUrlReceive' => $isFullUrlReceive,
////			'$uriRaw'          => $uriRaw,
//			'videoId'          => $this->videoId
//		) );
//		die();
		switch ( $type ) {
			case 'small':
				$data = $this->getSmallImg();
				break;
			case 'medium':
				$data = $this->getMediumImg();
				break;
			case 'full':
				$data = $this->getFullImg();
				break;
			case 'iframe':
				$data = $this->getIframe();
				break;
		}
		if ( ! empty( $data['html'] ) ) {
			wp_send_json_success( $data );
		} else {
			wp_send_json_error( 'No ' . $type . ' preview' );
		}
		die();
//		return $img;
	}

	public function getSmallImg() {
		$templates = array(
			"http://img.youtube.com/vi/%s/default.jpg",
			"http://img.youtube.com/vi/%s/1.jpg",
			"http://img.youtube.com/vi/%s/2.jpg",
			"http://img.youtube.com/vi/%s/3.jpg",
		);
		$html      = '';
		$url       = [ ];
		foreach ( $templates as $template ) {
			$src     = sprintf( $template, $this->videoId );
			$headers = get_headers( $src );
			$exist   = strpos( $headers[0], "200" );
			if ( $exist !== false ) {
				$url[] = $src;
				$html .= sprintf(
					'<a target="_blank" href="%1$s" class="nofancybox preparser-img-wr" download>'
					. '<img class="preparser-img" src="%1$s">'
					. '</a> ',
					$src );
			}
		}

		return array( 'html' => $html, 'url' => $url );
	}

	public function getMediumImg() {
		$templates = array(
			"http://img.youtube.com/vi/%s/0.jpg",
			"http://img.youtube.com/vi/%s/hqdefault.jpg"
		);
		$html      = '';
		$url       = [ ];
		foreach ( $templates as $template ) {
			$src     = sprintf( $template, $this->videoId );
			$headers = get_headers( $src );
			$exist   = strpos( $headers[0], "200" );
			if ( $exist !== false ) {
				$url[] = $src;
				$html .= sprintf(
					'<div class="pp-col w6-12 preparser-result-medium-img">'
					. '<a target="_blank" href="%1$s" download="%1$s" class="nofancybox preparser-img-wr">'
					. '<img class="preparser-img" src="%1$s">'
					. '</a>'
					. '</div>', $src );
			}
		}

		return array( 'html' => $html, 'url' => $url );
	}

	public function getFullImg() {
		$templates = array(
			"http://img.youtube.com/vi/%s/maxresdefault.jpg"
		);
		$html      = '';
		$url       = [ ];
		foreach ( $templates as $template ) {
			$src     = sprintf( $template, $this->videoId );
			$headers = get_headers( $src );
			$exist   = strpos( $headers[0], "200" );
			if ( $exist !== false ) {
				$url[] = $src;
				$html .= sprintf(
					'<div class="pp-col w12-12 preparser-result-full-img">'
					. '<a target="_blank" href="%1$s" download="%1$s" class="nofancybox preparser-img-wr">'
					. '<img class="preparser-img" src="%1$s">'
					. '</a>'
					. '</div>',
					$src );
			}
		}

		return array( 'html' => $html, 'url' => $url );
	}

	public function getIframe() {
		$html = sprintf(
			'<iframe width="640" height="360" src="//www.youtube.com/embed/%s" frameborder="0" allowfullscreen></iframe>',
			$this->videoId );
		$url  = '//www.youtube.com/embed/' . $this->videoId;

		return array( 'html' => $html, 'url' => $url );
	}

	public function getId( $uriRaw = false ) {
		$uri       = htmlspecialchars( $uriRaw );
		$parsedUri = parse_url( $uri );

		if ( isset( $parsedUri["host"] ) && $parsedUri["host"] === "youtu.be" ) {
			$id = trim( $parsedUri["path"], "/" );
		} else if ( isset( $parsedUri["query"] ) ) {
			parse_str( $parsedUri["query"], $query );
			$id = $query["v"];
		}
		if ( empty( $id ) ) {
			$id = $this->videoId;
		}

		return $id;
	}
}

$sa_ytp = new SA_ytp();