
<div class="row-fluid">
	<div class="span2">
		<ul class="nav nav-list">
			<li class="active"><a href="/DeveloperTools"><i class="icon-home icon-white"></i> Home</a></li>
			<li><a href="/DeveloperTools/SitemapBuilder"><i class="icon-globe"></i> Create Pages</a></li>
			<li><a href="/DeveloperTools/FormBuilder"><i class="icon-check"></i> Create Forms</a></li>
		</ul>
	</div>
	<div class="span8">
		<fieldset>
			<legend>Modify existing pages</legend>
			<table class="table table-striped">
				<thead>
					<tr>
						<th>Page</th>
						<th style="text-align:center">Edit</th>
					</tr>
				</thead>
				<tbody>
				
					<?php foreach($SitemapCollection as $SitemapPage): ?>
					<?php $SitemapPage = Sitemap::cast($SitemapPage); ?>
					
					<tr>
						<td class="span9">
							<a href="<?= $SitemapPage->getString('url') ?>" 
								title="<?= $SitemapPage->getStringAsHTMLEntities('description') ?>"
							><?= ($SitemapPage->getString('title') == '')?$SitemapPage->getStringAsHTMLEntities('url'):$SitemapPage->getStringAsHTMLEntities('title') ?></a>
						</td>
						<td class="span1" style="text-align:center"><a href="/DeveloperTools/ViewEditor/?id=<?= $SitemapPage->getInteger('link_id') ?>"><i class="icon-pencil"></i></a></td>
					</tr>
					
					<?php endforeach; ?>
					
				</tbody>
			</table>
		</fieldset>		
	</div>
</div>

