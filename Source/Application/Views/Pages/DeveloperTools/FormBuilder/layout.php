
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
				<legend>Configure your field:</legend>
				
				<div id="AddField">
				
					<Label>Field Type:</Label>
					<select 
						name="FieldBuilder[input_type]" 
						id="FieldBuilder_input_type" 
						class="input-block-level"
						required="required">
						<option></option>
					</select>
					
					<label for="FieldBuilder_table_name">Table name</label>
					<input 
						name="FieldBuilder[table_name]" 
						id="FieldBuilder_table_name" 
						type="text" 
						class="input-block-level" 
						placeholder="example_table_name" 
						required="required"
						value="">
					
					<label for="FieldBuilder_input_label">Input label</label>
					<input 
						name="FieldBuilder[input_label]" 
						id="FieldBuilder_input_label" 
						type="text" 
						class="input-block-level" 
						placeholder="Example field label" 
						required="required"
						value="">
					
					<label for="FieldBuilder_input_name">DB column name</label>
					<input 
						name="FieldBuilder[input_name]" 
						id="FieldBuilder_input_name" 
						type="text" 
						class="input-block-level" 
						placeholder="example_column_name" 
						required="required"
						value="">
					
					<label for="FieldEditor_default_value">Default Value</label>
					<input 
						name="FieldBuilder[default_value]" 
						id="FieldBuilder_default_value" 
						type="text" 
						class="input-block-level" 
						placeholder="" 
						value="">
					
					<div>
						<button class="btn-mini" style="float:right" id="ShowHideButtonOnBuilder">show</button>
						<h5>Form validation methods:</h5>
					</div>
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

<!-- Modal -->
<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="outline: -webkit-focus-ring-color auto 0;">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icon-remove"></i></button>
    <h3 id="myModalLabel">Hey! You forgot something!</h3>
  </div>
  <div class="modal-body">
    <p>One or more of the settings required to build this form field wasn't filled in, so it could not be built.</p>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
  </div>
</div>


