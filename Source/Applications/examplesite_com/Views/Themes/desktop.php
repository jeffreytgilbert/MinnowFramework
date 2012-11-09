<?php 

/////////////////////
// shorthand 
//
$Page = PageController::cast($this);
$this_path=dirname(__FILE__).'/../../../../..';
//
/////////////////////
?><!doctype html>
<!--[if lt IE 7]> <html class="no-js ie6 oldie" lang="en"> <![endif]-->
<!--[if IE 7]>	<html class="no-js ie7 oldie" lang="en"> <![endif]-->
<!--[if IE 8]>	<html class="no-js ie8 oldie" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

	<title><?= $Page->getPageTitle() ?></title>
	<meta name="keywords" content="<?= $Page->getPageKeywords() ?>">
	<meta name="description" content="<?= $Page->getPageDescription() ?>">
	<meta name="author" content="<?= $Page->getPageAuthor() ?>">

	<meta name="viewport" content="width=device-width,initial-scale=1">
	
	<? 	// Home Page CSS
		if ($_SERVER['REQUEST_URI'] == '/') { ?><link rel="stylesheet" type="text/css" href="/css/home.css" /><? }
		
		else { ?><link rel="stylesheet" type="text/css" href="/css/inner.css" /><? } 
	?>
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
	<script src="/js/libs/modernizr-2.5.3.min.js"></script>	
</head>


<body>		

		
	<header>
		
	</header>
				
			
	<div id="container">
		
		<?= $Page->getPageBody() ?>
		
	</div>
	

	<footer>
				
	</footer>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script>
window.jQuery || document.write('<script src="/js/libs/jquery-1.7.1.min.js"><\/script>')
</script>

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
window._gaq = [['_setAccount','UA-99266-5'],['_trackPageview'],['_trackPageLoadTime']];
Modernizr.load({
  load: ('https:' == location.protocol ? '//ssl' : '//www') + '.google-analytics.com/ga.js'
});
</script>

<!--[if lt IE 7 ]>
	<script src="//ajax.googleapis.com/ajax/libs/chrome-frame/1.0.3/CFInstall.min.js"></script>
	<script>window.attachEvent('onload',function(){CFInstall.check({mode:'overlay'})})</script>
<![endif]-->
<?php
$RuntimeInfo = RuntimeInfo::instance();
?>
<!-- 
since: <?= CalculateDate::returnSimpleDatetimeStringSinceNow('1/3/2012 14:55:00') ?>
l <?= $RuntimeInfo->pageTimer()->toString(); ?>
--></body>
</html>
