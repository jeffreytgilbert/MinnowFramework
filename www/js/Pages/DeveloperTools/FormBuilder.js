
require(
	[
		'jquery',
		'can',
		'bs/bootstrap-datetimepicker',
		'bs/bootstrap-colorpicker',
		'can/view/mustache'
//		'can/vendor/tracker/tracker'
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
//		can.route( 'filter/:table_name' );
//		can.route( '', { } );
//		
		
		$(document).ready(function(){
			
			// SampleFormController could control how the fields react once the data has been entered into them
			
			var OCurrentFieldInEditor = new can.Observe({
				form_name: '',
				input_name: '',
				input_label: '',
				input_value: '',
				size: '',
				required: '',
				field_order: ''
			});
			
			
			
			// Create a model here that gets passed in with all the values that need to be bound to this field
			
			
			
					
			
			// This controller manages the functionality of a field that has been added to the example form
			var FormFieldController = can.Control({ // Declare the Form Field class structure
				init: function(element, options){
					
					var self = $(this.element);
					
					console.log('This form field has been created at position '+this.options.field_position);
					
					var OFieldEditor = this.options.OFieldEditor;
					
					// In theory, this binds the view to the data in this object
					var FieldView = can.view(
						'/js/Pages/DeveloperTools/FormBuilder/FieldTypes/'+OFieldEditor.attr('input_type')+'.mustache', 
						OFieldEditor
					);
					
					// Maybe this could be replaced by this.element.append
					$('.field_container',self).append(FieldView);
					
					// Store the field editor observer object used to render the view in the view so it can be retrieved and edited
					self.data('OFieldEditor',OFieldEditor);
					
					// once fields have been built, throw the date picker handler on the time ones.
					$('.bs_datepicker', element).each(function(){ 
						$(this).datetimepicker({
							format: "dd MM yyyy - hh:ii p",
							autoclose: true,
							todayBtn: true,
							showMeridian: true,
							minuteStep: 10
						});
					});

				},
				
				'mouseenter': function(element, jquery_event){
//					console.log('this is the mouse over state');
					
					$(this.element).addClass('highlighted_field');
					$('.top-right-link',$(this.element)).show();
					
				},
				
				'mouseleave': function(element, jquery_event){
//					console.log('this is the mouse out state');
					
					$(this.element).removeClass('highlighted_field');
					$('.top-right-link',$(this.element)).hide();
				},
				
				// bind focus handler to the inputs that were just created
				'.top-right-link click':function(element, jquery_event){
//					CurrentField = field_list[$(this.element).attr('data-field-position')];
					var OCurrentField = this.options.OFieldEditor;
					
					// copy over all the field data to the editor.
					OCurrentFieldInEditor.attr(OCurrentField.attr());
					
					// set the current field observer in the editor so it can save changes 
					CFieldEditor.setCurrentFieldObj(OCurrentField);
					
					// Update the field type picker to the correct input type
					$('#FieldEditor_input_type').val(OCurrentField.attr('input_type'));
					
					// If Add Field form is visible, hide it and show the hidden edit field, then bind actions to it
					if($('#AddField:visible').length){ 
//						console.log(self.FieldBuilder);
//						console.log(self.FieldEditor);
						// show the edit fields
						$('#AddField').hide();
						$('#EditField').show();
						
//					} else {
						// do things when its already visible, but not the same things done when its not visible
					}
					
					$('.field_editor_container').removeClass('highlighted_as_editing_field');
					$(element).parent().addClass('highlighted_as_editing_field');
					
				}
				
			});		
			
			
			
			
			// This controller manages editing view of the Form Editor panel
			var FieldEditorController = can.Control({ // Declare the Field editor class structure
				
				init: function(element, options){
					
					console.log('Form field editor initialized');
					
					var self = $(this.element);
					
					self.hide();
					
					var VFieldEditor = can.view(
						'/js/Pages/DeveloperTools/FormBuilder/FieldEditor.mustache', 
						OCurrentFieldInEditor
					);

					// jquery selector, but through can so it can have data bindings
					can.$('#EditField').append(VFieldEditor);
					
					$.each(input_types_allowed, function(key,value){
						$('#FieldEditor_input_type').append('<option>'+value+'</option>'); 
					});
					
					can.$('#FieldEditorValidators').append(can.view(
						'/js/Pages/DeveloperTools/FormBuilder/EditorValidatorInput.mustache',
						ValidatorList
					));
					
					$('#EditField .method_name').tooltip({placement:'right'});
					$('#EditField .method_name').tooltip();
					
					$('.parameters',this.self).hide();
					
					// Initialize validator list states/handlers

					$('#ShowHideButtonOnEditor').toggle(function(){
						$('#FieldEditorValidators').slideDown('fast');
						$(this).html('hide');
					},function(){
						$('#FieldEditorValidators').slideUp('fast');
						$(this).html('show');
					});
					
					$('#ShowHideButtonOnBuilder').toggle(function(){
						$('#FieldBuilderValidators').slideDown('fast');
						$(this).html('hide');
					},function(){
						$('#FieldBuilderValidators').slideUp('fast');
						$(this).html('show');
					});
					
					$('#FieldEditorValidators').hide();
					$('#FieldBuilderValidators').hide();
					
				},
				
				getValidatorSettings: function(){
					
					var validators = {};
					// Run through all the fields and 
					$('#FieldEditorValidators :input').each(function(){
						if($(this).attr('name') && $(this).attr('name') != ''){
							
//							console.log($(this).attr('name'),': ', $(this).val());
//							console.log($('#'+$(this).attr('id')+':checked').length);
//							console.log($(this).attr('type'));
							if($(this).attr('type') == 'checkbox' && $('#'+$(this).attr('id')+':checked').length > 0){
								var parameters = {};
								$(':input',$(this).parent().next()).each(function(){
									parameters[$(this).attr('id')] = {
										'parameter_details':$(this).data(),
										'value':$(this).val()
									};
								});
								validators[$(this).attr('id')] = {
									'method_details':$(this).data(),
									'parameters':parameters
								};
							}
						}
						
//						console.log($(this).attr('id'));
//						console.log(OCurrentFieldInEditor.attr());
//						console.log($(this).attr('name'),': ', $(this).val());
					});
//					console.log(validators);
					//OCurrentFieldInEditor.attr('validators',validators);
					return validators;
				},
				
				setCurrentFieldObj: function(OCurrentField){ 
					this.options.OCurrentField = OCurrentField; 
				},
				
				'.methods change': function(element, jquery_event){
					if($(element).filter(':checked').val()){
						$(element).parent().next().show();
					} else {
						$(element).parent().next().hide();
					}
				},
				
				'#SaveChangesButton click': function(element, jquery_event){
					console.log('clicked save');
					
					var OCurrentField = this.options.OCurrentField; // Collect the original data
//					console.log(OCurrentField);
					
//					console.log($('#FieldEditor_input_type').val());
					
					// Set validators
					var validators = this.getValidatorSettings();
					
					OCurrentField.attr({
						table_name: $('#FieldEditor_table_name').val(),
						object_name: resolve_object_name($('#FieldEditor_table_name').val()),
						input_type: $('#FieldEditor_input_type').val(),
						input_name: $('#FieldEditor_input_name').val(),
						input_label: $('#FieldEditor_input_label').val(),
						input_value: $('#FieldEditor_default_value').val(),
						default_value: $('#FieldEditor_default_value').val(),
						size: '',
						required: '',
						validators: validators
					},true);
					
					console.log(OCurrentField);
					
					// nuke the form field so the controller unbinds everything, then rebuild it with a new view
					$('.highlighted_as_editing_field').children('.field_container').remove();
					
					$('.highlighted_as_editing_field .top-right-link').after('<div class="field_container"></div>');
					
					var FieldView = can.view(
						'/js/Pages/DeveloperTools/FormBuilder/FieldTypes/'+OCurrentField.attr('input_type')+'.mustache', 
						OCurrentField
					);
					
					$('.highlighted_as_editing_field .field_container').append(FieldView);
					
					$('.highlighted_as_editing_field .rules').unbind().html('');
					
					$.each(validators, function(i,n){
						
						var object_size = $.map(n.parameters, function(n, i) { return i; }).length;
						var tooltip_text = '';
						if(object_size > 0){
//							console.log(object_size, n.method_details.method, n.method_details.validator );
							$.each(n.parameters,function(i,n){
								tooltip_text += n.parameter_details.parameter + ' = ' + n.value+ '<br>';
							});
						}
						
						$('.highlighted_as_editing_field .rules')
							.append('<span class="label label-info tt" data-toggle="tooltip" title="'+tooltip_text+'">'+n.method_details.validator+'->'+n.method_details.method+'</span>');
					});
					
					$('.highlighted_as_editing_field .tt').tooltip({placement:'bottom', html:true});
					$('.highlighted_as_editing_field .tt').tooltip();
					
					$('.field_editor_container').removeClass('highlighted_as_editing_field');
					
					$('#EditField :input').val('');
					$('#EditField :checked').attr('checked',false);
					$('.parameters',self).hide();
					$('#FieldEditorValidators').hide();
					$('#ShowHideButtonOnEditor').html('show');

					$('#EditField').hide();
					$('#AddField').show();
					
				},
				
				'#CancelChangesButton click': function(element, jquery_event){
					console.log('clicked cancel');
					
					$('.field_editor_container').removeClass('highlighted_as_editing_field');

					$('#EditField').hide();
					$('#AddField').show();
				},
				
				'#DeleteFieldButton click': function(element, jquery_event){
					console.log('clicked delete');
					
					$('.highlighted_as_editing_field').remove();
					
					$('#EditField').hide();
					$('#AddField').show();
				}
			});
			

			
			
			
			// This controller manages the Add Field view and its functionality for the Form Editor
			var FieldBuilderController = can.Control({ // Declare the Field builder class structure
				init: function(element, options){
					
					console.log('Form field builder initialized');
					
					var self = $(this.element);
					
					$.each(input_types_allowed, function(key,value){
						$('#FieldBuilder_input_type').append('<option>'+value+'</option>'); 
					});
					
					can.$('#FieldBuilderValidators').append(can.view(
						'/js/Pages/DeveloperTools/FormBuilder/BuilderValidatorInput.mustache',
						ValidatorList
					));
					
					$('#AddField .method_name').tooltip({placement:'right'});
					$('#AddField .method_name').tooltip();
					
					$('.parameters',self).hide();
				},
				
				getValidatorSettings: function(){
					
					var validators = {};
					// Run through all the fields and 
					$('#FieldBuilderValidators :input').each(function(){
						if($(this).attr('name') && $(this).attr('name') != ''){
							
//							console.log($(this).attr('name'),': ', $(this).val());
//							console.log($('#'+$(this).attr('id')+':checked').length);
//							console.log($(this).attr('type'));
							if($(this).attr('type') == 'checkbox' && $('#'+$(this).attr('id')+':checked').length > 0){
								var parameters = {};
								$(':input',$(this).parent().next()).each(function(){
									parameters[$(this).attr('id')] = {
										'parameter_details':$(this).data(),
										'value':$(this).val()
									};
								});
								validators[$(this).attr('id')] = {
									'method_details':$(this).data(),
									'parameters':parameters
								};
							}
						}
						
//						console.log($(this).attr('id'));
//						console.log(OCurrentFieldInEditor.attr());
//						console.log($(this).attr('name'),': ', $(this).val());
					});
					//console.log(validators);
					//OCurrentFieldInEditor.attr('validators',validators);
					return validators;
				},
				
				'#AddFieldButton click': function(element, jquery_event){
					console.log('clicked add field button');
					
					if( $('#FieldBuilder_table_name').val() == '' || 
						$('#FieldBuilder_input_type').val() == '' || 
						$('#FieldBuilder_input_name').val() == '' ||
						$('#FieldBuilder_input_label').val() == ''){
						$('#myModal').modal({});
						return;
					}
					
					var ExampleForm = $('#ExampleForm');
					
//					var OFieldEditor = $('#field'+this.options.OCurrentField.attr('field_position')).data('OFieldEditor');
//					console.log(OFieldEditor);
					
//					var OCurrentField = this.options.OCurrentField;
//					console.log(this.options.OCurrentField);
					
					validators = this.getValidatorSettings();
//					
//					console.log(OCurrentFieldInEditor);
//					// Set changed data back to the object the editor originally spawned from
//					//OCurrentField.attr(OCurrentFieldInEditor.attr());
					
					var OFieldEditor = new can.Observe({
						table_name: $('#FieldBuilder_table_name').val(),
						object_name: resolve_object_name($('#FieldBuilder_table_name').val()),
						input_type: $('#FieldBuilder_input_type').val(),
						input_name: $('#FieldBuilder_input_name').val(),
						input_label: $('#FieldBuilder_input_label').val(),
						input_value: $('#FieldBuilder_default_value').val(),
						default_value: $('#FieldBuilder_default_value').val(),
						size: '',
						required: '',
						validators: validators
					});
					
					// field_list[field_position] = OFieldEditor;
					
					console.log('Adding a new form field controller from the field builder');
					
					console.log(OFieldEditor);
					
					// Build a container for the form field to be applied
					ExampleForm.append(
						'<div class="field_editor_container" style="position:relative;">'
							+'<button class="top-right-link btn-mini" style="display:none">Edit</button>'
							+'<div class="field_container"></div>'
							+'<div class="rules"></div>'
						+'</div>'
					);
					
					var CFormField = new FormFieldController($('.field_editor_container:last'),{
						'OFieldEditor':OFieldEditor
					});
					
					$('.field_editor_container:last .rules').unbind().html('');
					
					$.each(validators, function(i,n){
						
						var object_size = $.map(n.parameters, function(n, i) { return i; }).length;
						var tooltip_text = '';
						if(object_size > 0){
//							console.log(object_size, n.method_details.method, n.method_details.validator );
							$.each(n.parameters,function(i,n){
								tooltip_text += n.parameter_details.parameter + ' = ' + n.value+ '<br>';
							});
						}
						
						$('.field_editor_container:last .rules')
							.append('<span class="label label-info tt" data-toggle="tooltip" title="'+tooltip_text+'">'+n.method_details.validator+'->'+n.method_details.method+'</span> ');
					});
					
					$('.field_editor_container:last .tt').tooltip({placement:'bottom', html:true});
					$('.field_editor_container:last .tt').tooltip();

					$('#AddField :input').val('');
					$('#AddField :checked').attr('checked',false);
					$('.parameters',self).hide();
					$('#FieldBuilderValidators').hide();
					$('#ShowHideButtonOnBuilder').html('show');
					
				},
				
				'.methods change': function(element, jquery_event){
					if($(element).filter(':checked').val()){
						$(element).parent().next().show();
					} else {
						$(element).parent().next().hide();
					}
				}
			});

			
			
			
			
			// This controller manages the overall application and it's primary purpose is starting the application up and 
			// generating example forms off of database table descriptions
			var FormBuilderController = can.Control({ // Declare the Form Builder class structure
				
				init: function(element, options){
					
					console.log('Form builder initialized');

				},
				
				'#TablePicker change': function(element, jquery_event){
					
					console.log('Caught change event from table generator picker');
					
					var object_name = resolve_object_name(element.val());
					var table_name = element.val();
					
					$.ajax({
						url: '/DeveloperTools/FormBuilder.JSON/?TableDefinition[table_name]='+table_name,
						dataType: 'json',
						success:function(data, textStatus, jqXHR){
							var ExampleForm = $('#ExampleForm');
							ExampleForm.unbind().html('');
							
//							var field_list = [];
//							var field_controllers = [];
//							
							$.each(data, function(key, val){
								//field_position = field_list.length;
								
								var OFieldEditor = new can.Observe({
									table_name: table_name,
									object_name: object_name,
									input_type: input_type_map[val.type],
									input_name: key,
									input_label: resolve_input_label(key),
									input_value: (!val['default'])?'':val['default'],
									default_value: (!val['default'])?'':val['default'],
									size: val['size'],
									required: (!val['required'])?'':'required',
									validators: {}
								});
								
								// field_list[field_position] = OFieldEditor;
								
								console.log('Add a new form field controller');
								
								// Build a container for the form field to be applied
								ExampleForm.append(
									'<div class="field_editor_container" style="position:relative;">'
										+'<button class="top-right-link btn-mini" style="display:none">Edit</button>'
										+'<div class="field_container"></div>'
										+'<div class="rules"></div>'
									+'</div>'
								);
								
								var CFormField = new FormFieldController($('.field_editor_container:last'),{
									'OFieldEditor':OFieldEditor
								});
								
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
			
			var CFieldBuilder = new FieldBuilderController('#AddField',{
				// options to pass builder constructor
			});
		
			var CFieldEditor = new FieldEditorController('#EditField',{
				// options to pass editor constructor
			});
			
			// Initialize the default control foro this page
			var CFormBuilder = new FormBuilderController('#FormBuilder',{
				CFieldBuilder : CFieldBuilder,
				CFieldEditor : CFieldEditor
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
