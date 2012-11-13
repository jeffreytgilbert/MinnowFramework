<?php 

/////////////////////
// shorthand 
//
$Page = PageController::cast($this);
$this_path=dirname(__FILE__).'/../../../../..';
//
/////////////////////
?><!DOCTYPE html>
<!--[if lt IE 7]><html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]><html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]><html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--><html class="no-js"> <!--<![endif]-->
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	
	<title><?= $Page->getPageTitle() ?></title>
	<meta name="keywords" content="<?= $Page->getPageKeywords() ?>">
	<meta name="description" content="<?= $Page->getPageDescription() ?>">
	<meta name="author" content="<?= $Page->getPageAuthor() ?>">
	
	<meta name="viewport" content="width=device-width">
	<link href="/css/Libraries/Bootstrap/bootstrap.min.css" rel="stylesheet">
	<style>
	body {
		padding-top: 60px;
		padding-bottom: 40px;
	}
	</style>
	<link rel="stylesheet" href="/css/Libraries/Bootstrap/bootstrap-responsive.min.css">
	
	<!-- CSS Links <?php 
	
$css = '-->';
foreach($Page->getRemoteCss() as $css_file) {
	$css .= "\n".'
<link rel="stylesheet" type="text/css" href="'.$css_file.'">'."\n"; 
}
foreach($Page->getCss() as $css_file) {
	$css .= "\n".'<link rel="stylesheet" type="text/css" href="/css/'.$css_file.'.css?'.date('dmhis',filemtime(File::osPath($this_path.'/css/'.$css_file.'.css'))).'">'."\n"; 
}
$css .= '<!-- End CSS Links ';

echo $css;
	?> -->
	<script src="/js/Libraries/Modernizr/modernizr-2.6.1-respond-1.1.0.min.js"></script>	
</head>

<body>

	<!--[if lt IE 7]>
		<p class="chromeframe">You are using an outdated browser. <a href="http://browsehappy.com/">Upgrade your browser today</a> or <a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a> to better experience this site.</p>
	<![endif]-->

	<!-- This code is taken from http://twitter.github.com/bootstrap/examples/hero.html -->

	<div class="navbar navbar-inverse navbar-fixed-top">
		<div class="navbar-inner">
			<div class="container">
				<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</a>
				<a class="brand" href="#">Minnow Framework</a>
				<div class="nav-collapse collapse">
					<ul class="nav">
						<li class="active"><a href="#">Home</a></li>
						<li><a href="#about">About</a></li>
						<li><a href="#contact">Contact</a></li>
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown">Dropdown <b class="caret"></b></a>
							<ul class="dropdown-menu">
								<li><a href="#">Action</a></li>
								<li><a href="#">Another action</a></li>
								<li><a href="#">Something else here</a></li>
								<li class="divider"></li>
								<li class="nav-header">Nav header</li>
								<li><a href="#">Separated link</a></li>
								<li><a href="#">One more separated link</a></li>
							</ul>
						</li>
					</ul>
					<form class="navbar-form pull-right">
						<input class="span2" type="text" placeholder="Email">
						<input class="span2" type="password" placeholder="Password">
						<button type="submit" class="btn">Sign in</button>
					</form>
				</div><!--/.nav-collapse -->
			</div>
		</div>
	</div>

	<div class="container">

		<?= $Page->getPageBody(); ?>
		
		<hr>

		<footer>
			<p>&copy; Company 2012</p>
		</footer>

	</div> <!-- /container -->

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
<script>window.jQuery || document.write('<script src="js/Libraries/jQuery/jquery-1.8.2.min.js"><\/script>');</script>
<script src="/js/Libraries/Bootstrap/bootstrap.min.js"></script>

<!-- JS Templates <?php 

$templates = $Page->getTemplateEngine()->getArrayOfTemplatesWithIdsAsKeys();
$js_templates = '-->';
if(isset($this->_extra_js)){
	foreach($templates as $template_name => $template) {
		$js_templates .= "\n".'<script type="text/html" id="'.$template_name.'">'."\n".$template."\n".'</script>'."\n";
	}
}
$js_templates .= '<!-- End JS Templates ';

?> -->

<!-- JS <?php

$js = '-->';
foreach($Page->getRemoteJs() as $js_file) {
	$js .= "\n".'<script src="'.$js_file.'"></script>'."\n";
}
foreach($Page->getJs() as $js_file) {
	$js .= "\n".'<script src="/js/'.$js_file.'.js?'.date('dmhis',filemtime(File::osPath($this_path.'/js/'.$js_file.'.js'))).'"></script>'."\n";
}
$js .= '<!-- End JS ';

echo $js;

?> -->
<script>
var _gaq=[['_setAccount','UA-XXXXX-X'],['_trackPageview'],['_trackPageLoadTime']];
(function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];
g.src=('https:'==location.protocol?'//ssl':'//www')+'.google-analytics.com/ga.js';
s.parentNode.insertBefore(g,s)}(document,'script'));
</script>
</body>
</html>

<?php
$RuntimeInfo = RuntimeInfo::instance();
?>
<!-- 
since: <?= CalculateDate::returnSimpleDatetimeStringSinceNow('1/3/2012 14:55:00') ?>
l <?= $RuntimeInfo->pageTimer()->toString(); ?>
-->
