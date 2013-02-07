
<div class="row-fluid">
	<div class="span8">
		<form name="Sitemap" id="SitemapForm" method="post">
			<fieldset>
				<legend>Modify existing pages</legend>
				<table class="table table-striped">
					<thead>
						<tr>
							<th>Page</th>
							<th>Title / Description</th>
							<th>Hide&nbsp;from&nbsp;sitemap</th>
						</tr>
					</thead>
					<tbody>
					
						<?php foreach($SitemapCollection as $SitemapPage): ?>
						<?php $SitemapPage = Sitemap::cast($SitemapPage); ?>
						
						<tr>
							<td class="span3"><?= $SitemapPage->getString('url') ?></td>
							<td class="span5">
								<input 
									name="Sitemap[title][<?= $SitemapPage->getInteger('link_id') ?>]" 
									type="text" 
									class="input-block-level" 
									placeholder="Title"
									value="<?= $SitemapPage->getStringAsHTMLEntities('title') ?>"
								>
								<input 
									name="Sitemap[description][<?= $SitemapPage->getInteger('link_id') ?>]" 
									type="text" 
									class="input-block-level" 
									placeholder="Description"
									value="<?= $SitemapPage->getStringAsHTMLEntities('description') ?>"
								>
							</td>
							<td class="span1">
								<input 
									name="Sitemap[ignore_in_sitemap][<?= $SitemapPage->getInteger('link_id') ?>]" 
									type="checkbox"
									<?= ($SitemapPage->getBoolean('ignore_in_sitemap'))?'checked="checked"':'' ?>
								>
							</td>
						</tr>
						
						<?php endforeach; ?>
						
					</tbody>
				</table>
				<div class="form-actions">
					<button type="submit" class="btn btn-primary">Save modifications</button>
				</div>
			</fieldset>
		</form>
		<div style="margin-bottom:100px"><br clear="all"></div>
	</div>
	<div class="span4">
	
		<form name="PageBuilder" id="PageBuilderForm" method="post">
			<fieldset>
				<legend>Build a new page</legend>
				<input 
					name="PageBuilder[url]" 
					type="text" 
					class="input-block-level" 
					placeholder="Path... ex: Folder/Path/ControllerName"
					value="<?= $PageBuilder->getStringAsHTMLEntities('url') ?>"
				>
				<input 
					name="PageBuilder[title]" 
					type="text" 
					class="input-block-level" 
					placeholder="Title... ex: My Profile page"
					value="<?= $PageBuilder->getStringAsHTMLEntities('title') ?>"
				>
				<input 
					name="PageBuilder[description]" 
					type="text" 
					class="input-block-level" 
					placeholder="Description... ex: This is your personalized profile page..."
					value="<?= $PageBuilder->getStringAsHTMLEntities('description') ?>"
				>
				<label class="checkbox">
					<input 
						name="PageBuilder[ignore_in_sitemap]" 
						type="checkbox" 
						value="1" 
						<?= ($PageBuilder->getBoolean('ignore_in_sitemap'))?'checked="checked"':'' ?>
					> Hide&nbsp;from&nbsp;sitemap
				</label>
				
				<h5>Supported output formats:</h5>
				<label class="checkbox inline">
					<input
						type="checkbox"
						name="PageBuilder[HTML]"
						value="1"
						<?= ($PageBuilder->getBoolean('HTML'))?'checked="checked"':'' ?>
					> HTML
				</label>
				<label class="checkbox inline">
					<input
						type="checkbox"
						name="PageBuilder[JSON]"
						value="1"
						<?= ($PageBuilder->getBoolean('JSON'))?'checked="checked"':'' ?>
					> JSON
				</label>
				<label class="checkbox inline">
					<input
						type="checkbox"
						name="PageBuilder[XML]"
						value="1"
						<?= ($PageBuilder->getBoolean('XML'))?'checked="checked"':'' ?>
					> XML
				</label>
				<label class="checkbox inline">
					<input
						type="checkbox"
						name="PageBuilder[HTMLBody]"
						value="1"
						<?= ($PageBuilder->getBoolean('HTMLBody'))?'checked="checked"':'' ?>
					> HTML&nbsp;(body&nbsp;only)
				</label>
				<div class="form-actions">
					<button type="submit" class="btn btn-primary">Build</button>
				</div>
			</fieldset>
		</form>
		
	</div>
</div>

