
<script type="text/javascript">
//var form_vars = '.json_encode($this->field_vars).';
//var li_vars = '.json_encode($li_vars).';
var ValidatorList = {'validators':<?= json_encode($validators) ?>};
</script>

<div id="FormBuilder" class="row-fluid">
	<div class="span6 bs-docs-example">
		<div id="TablePickerArea">
			<form style="margin:0">
				<label style="margin:0">
					<select id="TablePicker" class="input-block-level" style="margin:0">
						<option value="">Generate form based on a table:</option>
						<?php foreach($object_names as $table_name => $object_name): ?>
						<option value="<?= $table_name ?>"><?= $object_name ?></option>
						<?php endforeach; ?>
					</select>
					<div class="clearfix"></div>
				</label>
				<div class="clearfix"></div>
			</form>
			<div class="clearfix"></div>
		</div>
		<div class="clearfix"></div>
		<hr>
		<div id="ExampleForm" style="min-height:500px">
			
		</div>
		<form method="post" id="FormBuilderSaveForm" style="margin:0;">
			<div style="display:none" id="hidden_form_fields"></div>
			<div class="form-actions" style="margin-left:-18px; margin-right:-18px; margin-bottom:-14px; text-align:center">
				<select>
					<option value="">Save to:</option>
					<?php foreach($SitemapCollection as $SitemapPage): ?>
					<?php $SitemapPage = Sitemap::cast($SitemapPage); ?>
					<option value="<?= $SitemapPage->getInteger('link_id') ?>"><?= $SitemapPage->getStringAndConvertHTMLToEntities('url') ?>
					<?php endforeach; ?>
				</select>
				&nbsp;
				<button type="submit" class="btn btn-primary" style="margin-top:-8px">Save form</button>
			</div>
		</form>
	</div>
	<div class="span6" id="FieldBuilderControls">
	
		<form name="FieldBuilder" id="FieldBuilderForm">
			<fieldset>
				<legend>Form Editor</legend>
				
				<div id="AddField">
				
					<select 
						name="FieldBuilder[field_type]" 
						id="FieldBuilder_field_type" 
						class="input-block-level">
						<option>Field Type:</option>
					</select>
					
					<input 
						name="FieldBuilder[input_label]" 
						id="FieldBuilder_field_type" 
						type="text" 
						class="input-block-level" 
						placeholder="Label" 
						value="">
					
					<input 
						name="FieldBuilder[default_value]" 
						id="FieldBuilder_field_type" 
						type="text" 
						class="input-block-level" 
						placeholder="Default Value" 
						value="">
					
					<h5>Form validation methods:</h5>
					
					<div id="FieldBuilderValidators"></div>
					
					<div class="form-actions">
						<button type="button" class="btn" id="AddFieldButton"><i class="icon-chevron-left"></i> Add field</button>
					</div>
					
				</div>
				<div id="EditField"></div>
				
			</fieldset>
		</form>
	
	</div>
</div>




