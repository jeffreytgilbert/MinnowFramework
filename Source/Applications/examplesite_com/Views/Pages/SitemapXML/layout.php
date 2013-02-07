<?php
header('Content-Type:text/xml');
echo '<'.'?xml version="1.0" encoding="UTF-8"'.'?'.'>'; 
?>

<urlset
      xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
      xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
      xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
            http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">

<?php
$SitemapCollection = $this->getDataObject()->getCollection('SitemapCollection');
foreach($SitemapCollection as $SitemapPage){
	$SitemapPage = Sitemap::cast($SitemapPage);
	if(!$SitemapPage->getBoolean('ignore_in_sitemap')){
		?>
		<url>
		  <loc>http<?= isset($_SERVER['HTTPS'])?'s':'' ?>://<?= $_SERVER['SERVER_NAME'] ?>/<?= $SitemapPage->getStringAsHTMLEntities('url') ?></loc>
		  <changefreq>weekly</changefreq>
		</url>
		<?php 
	}
}
?>
</urlset>