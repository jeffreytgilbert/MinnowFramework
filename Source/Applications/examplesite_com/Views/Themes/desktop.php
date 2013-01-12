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
	if(File::exists($this_path.'/css/'.$css_file.'.css')){
		$css .= "\n".'<link rel="stylesheet" type="text/css" href="/css/'.$css_file.'.css?'.date('dmhis',filemtime(File::osPath($this_path.'/css/'.$css_file.'.css'))).'">'."\n"; 
	} else {
		$css .= pr('Error loading css file: '.File::osPath($this_path.'/css/'.$css_file.'.css'),1);
	}
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
				<a class="brand" href="/"><img src="/img/Minnow-Framework-logo-slim.png" style="height:40px;margin-top:-28px" alt="Minnow Framework"></a>
				<div class="nav-collapse collapse">
					<ul class="nav">
						<li<?= ($_SERVER['REQUEST_URI'] == '/')?' class="active"':''?>><a href="/">About</a></li>
						<li><a href="https://github.com/jeffreytgilbert">Contact</a></li>
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown">Project <b class="caret"></b></a>
							<ul class="dropdown-menu">
								<li><a href="https://github.com/jeffreytgilbert/MinnowFramework">Visit us on GitHub!</a></li>
								<li><a href="https://github.com/jeffreytgilbert/MinnowFramework/archive/master.zip">Download</a></li>
								<li class="divider"></li>
								<li class="nav-header">Project activity</li>
								<li><a href="https://github.com/jeffreytgilbert/MinnowFramework/commits/master">Commit Log</a></li>
								<li><a href="https://github.com/jeffreytgilbert/MinnowFramework/graphs/contributors">Contributors</a></li>
								<li><a href="https://github.com/jeffreytgilbert/MinnowFramework/wiki/Overview">Structural Overview</a></li>
							</ul>
						</li>
					</ul>
					<div class="navbar-form pull-right">
					
						<ul class="nav">
						<?php if($Page->getID()->isOnline()){ ?>
						
							<li><a href="/Account/-/Welcome"><?= $Page->getID()->getObject('UserAccount')->getString('first_name') ?>
								<?= $Page->getID()->getObject('UserAccount')->getString('last_name') ?></a></li>
							<li><a href="<?php echo $Page->getComponents()->Authentication($this)->getConfig()->get('logout_page_url') ?>">Sign out</a></li>
						
						<?php } else { ?>
						
							<li<?= ($_SERVER['REQUEST_URI'] == '/Account/-/Login')?' class="active"':''?>><a href="<?php echo $Page->getComponents()->Authentication($this)->getConfig()->get('login_page_url') ?>">Sign in</a></li>
						
						<?php } ?>
						</ul>
						
					</div>
				</div><!--/.nav-collapse -->
			</div>
		</div>
	</div>
	
	<header></header>

	<div class="container">

		<?php foreach($Page->getErrors() as $error): ?>

			<div class="alert alert-error"><span class="label label-important">&nbsp;!&nbsp;</span> <?= $error; ?></div>
		
		<?php endforeach; ?>
		<?php foreach($Page->getNotices() as $notice): ?>

			<div class="alert alert-info"><span class="label label-info">&nbsp;!&nbsp;</span> <?= $notice; ?></div>
		
		<?php endforeach; ?>
		<?php foreach($Page->getConfirmations() as $confirmation): ?>

			<div class="alert alert-success"><span class="label label-success">&nbsp;!&nbsp;</span> <?= $confirmation; ?></div>
		
		<?php endforeach; ?>
		
		<?= $Page->getPageBody(); ?>
		
	</div> <!-- /container -->

	<footer class="footer navbar navbar-fixed-bottom">
		<div class="container">
			<p>This work is licensed under the <a href="http://creativecommons.org/licenses/by-sa/3.0/">Creative Commons Attribution-ShareAlike 3.0 Unported License</a>.</p>
		</div>
	</footer>
	
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
	if(File::exists($this_path.'/js/'.$js_file.'.js')){
		$js .= "\n".'<script src="/js/'.$js_file.'.js?'.date('dmhis',filemtime(File::osPath($this_path.'/js/'.$js_file.'.js'))).'"></script>'."\n";
	} else {
		$js .= pr('Error loading js file: '.File::osPath($this_path.'/js/'.$js_file.'.js'),1);
	}
	
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
$format = function ($bytes, $precision = 2) { 
    $units = array('B', 'KB', 'MB', 'GB', 'TB'); 
   
    $bytes = max($bytes, 0); 
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
    $pow = min($pow, count($units) - 1); 
   
    $bytes /= pow(1024, $pow); 
   
    return round($bytes, $precision) . ' ' . $units[$pow]; 
}
?>
<!-- 
execution time: <?= $RuntimeInfo->pageTimer()->toString(); ?>

ram usage: <?php echo $format(memory_get_usage(true)); ?>

peak ram usage: <?php echo $format(memory_get_peak_usage(true)); ?>

-->
