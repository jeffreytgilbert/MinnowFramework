
<div class="row-fluid">
	<div class="span8">
		<fieldset>
			<legend>Existing data structures</legend>
			<table class="table table-striped">
				<thead>
					<tr>
						<th>Table name</th>
						<th style="text-align:center">Has model</th>
						<th style="text-align:center">Has action</th>
					</tr>
				</thead>
				<tbody>
				
					<?php foreach($table_names as $table_name): ?>
					
					<tr>
						<td class="span3">
							<?= $table_name ?>
						</td>
						<td class="span3" style="text-align:center">
							<?= isset($missing_models[$table_name])?'<i class="text-error">No</i>':'<i class="text-success">Yes</i>' ?>
						</td>
						<td class="span3" style="text-align:center">
							<?= isset($missing_actions[$table_name])?'<i class="text-error">No</i>':'<i class="text-success">Yes</i>' ?>
						</td>
					</tr>
					
					<?php endforeach; ?>
					
				</tbody>
			</table>
		</fieldset>
		<div style="margin-bottom:100px"><br clear="all"></div>
	</div>
	<div class="span4">
	
		<form name="ActionBuilder" id="PageBuilderForm" method="post">
			<fieldset>
				<legend>Build actions</legend>
				
				<?php foreach($missing_actions as $table_name => $action_name): ?>
				
				<label class="checkbox">
					<input 
						name="ActionBuilder[<?= $table_name ?>]" 
						type="checkbox" 
						value="1" 
					> <?= $action_name ?>Actions
				</label>
				
				<?php endforeach; ?>
				
				<div class="form-actions">
					<button type="submit" class="btn btn-primary">Build files</button>
				</div>
			</fieldset>
		</form>
		
		<form name="ModelBuilder" id="PageBuilderForm" method="post">
			<fieldset>
				<legend>Build models</legend>
				
				<?php foreach($missing_models as $table_name => $model_name): ?>
				
				<label class="checkbox">
					<input 
						name="ModelBuilder[<?= $table_name ?>]" 
						type="checkbox" 
						value="1" 
					> <?= $model_name ?>
				</label>
				
				<?php endforeach; ?>
				
				<div class="form-actions">
					<button type="submit" class="btn btn-primary">Build files</button>
				</div>
			</fieldset>
		</form>
		
	</div>
</div>

