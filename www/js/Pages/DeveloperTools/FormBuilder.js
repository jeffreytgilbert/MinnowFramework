
require(
	[
		'jquery',
		'can',
		'bs/bootstrap-datetimepicker',
		'bs/bootstrap-colorpicker',
		'can/view/mustache'
	],
	function($, can){
		
		var input_types_allowed = ['checkbox','date','datetime','file','full_name','mutliselect','password','radio_list','select','text','textarea','time'];
		var variables_needing_input = ['hint','input_name','form_name','required','size'];
		var input_type_map = {
			'enum':'text',
			'char':'text',
			'varchar':'text',
			'text':'textarea',
			'tinytext':'textarea',
			'mediumtext':'textarea',
			'longtext':'textarea',
			'set':'select',
			'blob':'textarea',
			'varblob':'textarea',
			'tinyblob':'textarea',
			'mediumblob':'textarea',
			'longblob':'textarea',
			'clob':'textarea',
			'binary':'textarea', // image?
			'varbinary':'textarea', // image?
			'bit':'checkbox',
			'bool':'checkbox',
			'boolean':'checkbox',
			'int':'text',
			'integer':'text',
			'tinyint':'text',
			'smallint':'text',
			'mediumint':'text',
			'bigint':'text',
			'decimal':'text',
			'dec':'text',
			'float':'text',
			'double':'text',
			'double precision':'text',
			'serial':'text',
			'number':'text',
			'date':'date',
			'year':'year',
			'datetime':'datetime',
			'time':'time',
			'timestamp':'datetime'
		};
		
		var resolve_object_name = function($underscored_name){
			return $underscored_name.replace(/(?:^|\s|_)\w/g, function(match) {
		        return match.toUpperCase();
		    });
		};
		
		var resolve_input_label = function($underscored_name){
			$underscored_name = $underscored_name.replace(/(_)/g,' ');
			return resolve_object_name($underscored_name);
		};
		
		var convert_mysql_datetime_to_javascript_date = function(mysql_datetime){
			var t = mysql_datetime.split(/[- :]/);

			// Apply each element to the Date function
			var d = new Date(t[0], t[1]-1, t[2], t[3], t[4], t[5]);
			return d;
		};
		
		// Make a list for model storage
		// Store models in the list as fields are created for them in the form builder
		// Use the models to bind the data to the view
		// On the focus of a form element, trigger an event handler that changes the form builder option to the settings in the model
		// 

		// Set up the page routing for when table selection changes
		can.route( 'filter/:table_name' );
		can.route( '', { } );
		
		// SampleFormController could control how the fields react once the data has been entered into them
		
		var LiveFieldEditor = new can.Observe({
			form_name: '',
			input_name: '',
			input_label: '',
			input_value: '',
			size: '',
			required: '',
			field_order: ''
		});
		
		FieldEditorController = can.Control({
			self:null,
			FieldEditorView:null,
			init: function(){
				this.self = $('#EditField');
				
				this.self.hide();
				
				this.FieldEditorView = can.view(
					'/js/Pages/DeveloperTools/FormBuilder/FieldEditor.mustache', 
					LiveFieldEditor
				);

				// jquery selector, but through can so it can have data bindings
				can.$('#EditField').append(this.FieldEditorView);
				
				$.each(input_types_allowed, function(key,value){
					$('#FieldEditor_field_type').append('<option>'+value+'</option>'); 
				});
				
				can.$('#FieldEditorValidators').append(can.view(
					'/js/Pages/DeveloperTools/FormBuilder/EditorValidatorInput.mustache',
					ValidatorList
				));
				
				$('#EditField .method_name').tooltip({placement:'right'});
				$('#EditField .method_name').tooltip();
				
				$('.parameters',this.self).hide();
			},
			'.methods change': function(element, jquery_event){
				if($(element).filter(':checked').val()){
					$(element).parent().next().show();
				} else {
					$(element).parent().next().hide();
				}
			}
		});
		
		FieldBuilderController = can.Control({
			self:null,
			init: function(){
				this.self = $('#AddField');
				
				$.each(input_types_allowed, function(key,value){
					$('#FieldBuilder_field_type').append('<option>'+value+'</option>'); 
				});
				
				can.$('#FieldBuilderValidators').append(can.view(
					'/js/Pages/DeveloperTools/FormBuilder/BuilderValidatorInput.mustache',
					ValidatorList
				));
				
				
				$('#AddField .method_name').tooltip({placement:'right'});
				$('#AddField .method_name').tooltip();
				
				$('.parameters',this.self).hide();
			},
			'.methods change': function(element, jquery_event){
				if($(element).filter(':checked').val()){
					$(element).parent().next().show();
				} else {
					$(element).parent().next().hide();
				}
			}
		});
		
		FormBuilderController = can.Control({
			
			// instance cache for jquery objects for these two forms
			FieldBuilder:null,
			FieldEditor:null,
			
			init: function(){
				
				this.FieldBuilder = new FieldBuilderController('#AddField',{
					// options to pass builder constructor
				});
				
				this.FieldEditor = new FieldEditorController('#EditField',{
					// options to pass editor constructor
				});
				
			},
			'#TablePicker change': function(element, jquery_event){
				
				var self = this;
				
				var example_form_name = resolve_object_name(element.val());
				var table_name = element.val();
				
				$.ajax({
					url: '/DeveloperTools/FormBuilder.JSON/?TableDefinition[table_name]='+table_name,
					dataType: 'json',
					success:function(data, textStatus, jqXHR){
						$('#ExampleForm').unbind().html('');
						
						var field_list = [];
						
						$.each(data, function(key, val){
							field_position = field_list.length;
							field_list[field_position] = FieldEditorObj = new can.Observe({
								form_name: example_form_name,
								input_type: input_type_map[val.type],
								input_name: key,
								input_label: resolve_input_label(key),
								input_value: (!val['default'])?'':val['default'],
								default_value: (!val['default'])?'':val['default'],
								size: val['size'],
								required: (!val['required'])?'':'required',
								field_order: field_position
							});
							
							// In theory, this binds the view to the data in this object
							var FieldView = can.view(
								'/js/Pages/DeveloperTools/FormBuilder/FieldTypes/'+input_type_map[val.type]+'.mustache', 
								FieldEditorObj
							);
							
							can.$('#ExampleForm').append(
								'<div id="field'+field_position+'" class="field_editor_container" style="position:relative;" data-field-position="'+field_position+'">'
									+'<button class="top-right-link btn-mini" style="display:none">Edit</button>'
									+'<div class="field_container"></div>'
									+'<div class="rules"></div>'
								+'</div>'
							);
							
							can.$('#field'+field_position).append(FieldView);
							
							$('#field'+field_position).hover(function(evt){
								$(this).addClass('highlighted_field');
								$('.top-right-link',$(this)).show();
							}, function(evt){
								$(this).removeClass('highlighted_field');
								$('.top-right-link',$(this)).hide();
							});
							
						});
						
						// bind focus handler to the inputs that were just created
						$('.top-right-link').click(function(){
							
							CurrentField = field_list[$(this).parent().attr('data-field-position')];
							
							LiveFieldEditor.attr('form_name', CurrentField.form_name);
							LiveFieldEditor.attr('input_type', CurrentField.input_type);
							LiveFieldEditor.attr('input_name', CurrentField.input_name);
							LiveFieldEditor.attr('input_label', CurrentField.input_label);
							LiveFieldEditor.attr('input_value', CurrentField.input_value);
							LiveFieldEditor.attr('default_value', CurrentField.input_value);
							LiveFieldEditor.attr('size', CurrentField.size);
							LiveFieldEditor.attr('required', CurrentField.required);
							LiveFieldEditor.attr('field_order', CurrentField.field_order);
							
							// If Add Field form is visible, hide it and show the hidden edit field, then bind actions to it
							if($('#AddField:visible').length){ 
//								console.log(self.FieldBuilder);
//								console.log(self.FieldEditor);
								// show the edit fields
								$(self.FieldBuilder.element[0]).hide();
								$(self.FieldEditor.element[0]).show();
								
								$('#FieldEditor_field_type').val(CurrentField.input_type);
								
							} else {
								// do things when its already visible
							}
							
							$('#FieldEditor_field_type').val(CurrentField.input_type);
							
							
						});
						
						// once fields have been built, throw the date picker handler on the time ones.
						$('.bs_datepicker').each(function(){ 
							$(this).datetimepicker();
						});
						
					},
					failure:function(jqXHR, textStatus, errorThrown){
						console.log(textStatus + ':' + errorThrown);
					}
				});				
			},
			'#FieldPicker change': function(){
				$('#field_type_picker').change(function(key,value){
					console.log('show new options for this field type.');
				});
			}
		});
		
		$(document).ready(function(){
			// Initialize the default control foro this page
			new FormBuilderController('#FormBuilder',{
				// things that would be passed in if there were things that the app was waiting to load, which there arent
			});
		});
		
	}
);

// What to do next:
// Get a mustache template, and render data to it.
// Populate some form options from data gathered from the server
// Populate a drop down filled with validators from field type with data from server side 
// Build a sample form
// Create settings in a hidden form that will be submitted to the server for the build request
// Allow data bindings for settings that span between labels on sample form and hidden inputs
// Create saving mechanism that builds the controller logic and form in the layout and saves them to the files already present and backs up the old files

//TableDefinition.findOne( { table_name:'account_status' }, function( Table ){
////console.log( Table );
////console.log( Table.account_status_id );
////account_status_id = TD.Data.account_status_id;
////created_datetime = TD.Data.created_datetime;
////hierarchical_order = TD.Data.hierarchical_order;
////status_type = TD.Data.status_type;
//
//$.each(Table, function(key,value){
//console.log(Table[key]);
//});
//
//}, function( xhr ){
//// called if an error
//});
