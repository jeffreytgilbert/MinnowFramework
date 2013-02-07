
<div class="row-fluid">
	<div class="span8">
		<form name="Sitemap" id="SitemapForm">
			<fieldset>
				<legend>Sitemap</legend>
				<table class="table table-striped">
					<thead>
						<tr>
							<th>Page</th>
							<th>Keywords</th>
							<th>Description</th>
							<th>In&nbsp;Sitemap&nbsp;XML</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>DeveloperTools</td>
							<td>
								<input 
									name="Sitemap[keywords][path]" 
									type="text" 
									class="input-block-level" 
									placeholder="Minnow, Framework, Minnow Framework..."
									value=""
								>
							</td>
							<td>
								<input 
									name="Sitemap[description][path]" 
									type="text" 
									class="input-block-level" 
									placeholder="This is a modern web framework wrapped around PHP 5.4"
								>
							</td>
							<td>
								<input 
									name="Sitemap[show_in_xml][path]" 
									type="checkbox"
								>
							</td>
						</tr>
					</tbody>
				</table>
				<div class="form-actions">
					<button type="submit" class="btn btn-primary">Save info</button>
				</div>
			</fieldset>
		</form>
	</div>
	<div class="span4">
	
		<form name="PageBuilder" id="PageBuilderForm">
			<fieldset>
				<legend>Page Builder</legend>
				<input name="PageBuilder[path]" type="text" class="input-block-level" placeholder="Path... ex: Folder/Path/ControllerName">
				<input name="PageBuilder[keywords]" type="text" class="input-block-level" placeholder="Keywords... ex: Minnow, Framework, Minnow Framework">
				<input name="PageBuilder[description]" type="text" class="input-block-level" placeholder="Description... ex: This is a great Minnow Framework page">
				<label class="checkbox">
					<input name="PageBuilder[show_in_xml]" type="checkbox" value="1" checked="checked"> Show in XML
				</label>
				
				<h5>Supported output formats:</h5>
				<label class="checkbox inline">
					<input type="checkbox" name="PageBuilder[HTML]" value="1" checked="checked"> HTML
				</label>
				<label class="checkbox inline">
					<input type="checkbox" name="PageBuilder[JSON]" value="1" checked="checked"> JSON
				</label>
				<label class="checkbox inline">
					<input type="checkbox" name="PageBuilder[XML]" value="1" checked="checked"> XML
				</label>
				<label class="checkbox inline">
					<input type="checkbox" name="PageBuilder[HTMLBody]" value="1" checked="checked"> HTML&nbsp;(body&nbsp;only)
				</label>
				<div class="form-actions">
					<button type="submit" class="btn btn-primary">Build new page</button>
				</div>
			</fieldset>
		</form>
		
		<form>
			<fieldset>
				<legend>Data Models &amp; Actions</legend>
				<table class="table table-bordered">
					<thead>
						<tr>
							<th>Table</th>
							<th>Model</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>table_name</td>
							<td class="span1"><input type="checkbox" name="DataModel[tablename]" value="1"></td>
							<td class="span1"><input type="checkbox" name="DataAction[tablename]" value="1"></td>
						</tr>
					</tbody>
				</table>
				<div class="form-actions">
					<button type="submit" class="btn btn-primary">Create new objects</button>
				</div>
			</fieldset>
		</form>
	
	</div>
</div>

