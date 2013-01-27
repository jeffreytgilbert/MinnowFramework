
require(
	['jquery', 'Modules/ValidateForm'],
	function($, ValidateForm){

		var tpl_li = $('#tpl_li').html();
		
		// bindings for submitting data to the server
		$('#input_type_selector').submit(function(event){
			event.preventDefault();
			
			var dummy_form = $('<form></form>');
			
			var form_field_order = 0;
			$('#sample_form_layout li').each(function(){
				$('.form_builder_pref',$(this)).each(function(){
					dummy_form.append('<input class="form_builder_pref" type="hidden" name="o['+form_field_order+']['+$(this).attr('name')+']" value="'+$(this).val()+'">');
				});
				form_field_order++;
			});
			
//			console.log(dummy_form);
			
			$.ajax({
				url:'?',
				success:function(data, textStatus, jqXHR){
					$('#results_window').html(data);
//					console.log(data);
				},
				error:function(jqXHR, textStatus, errorThrown){
//					console.log('abject failure');
				},
				dataType:'html',
				data:dummy_form.serialize()+
					'&folder_name='+$('#page_properties input:first').val()+
					'&page_name='+$('#page_properties input:last').val(),
				type:'POST',
				cache: false
			});
		});
		
		// bindings for the lower right hand form to preview the builder options as chosen by the user
		$('#form_variables_form').submit(function(event){
			event.preventDefault();
			
			var input_template = $('#tpl_'+$('#currently_selected_input_type').val()).html();
			var selected_item = $('#currently_selected_input_type').val();
			
			var this_li = tpl_li;
			$.each(li_vars, function(i,n){
				switch(n){
					case 'inputs':
					break;
					default:
						var user_specified_value = $('#'+selected_item+'_'+n).val()?$('#'+selected_item+'_'+n).val():'';
						this_li = replaceAll(this_li, '{% '+n+' %}', user_specified_value);
					break;
				}
			});
			this_li = replaceAll(this_li, '{% inputs %}', input_template);
			
			$.each(form_vars[$('#currently_selected_input_type').val()], function(i,n){
				this_li = replaceAll(this_li, '{% '+n+' %}', $('#'+selected_item+'_'+n).val()?$('#'+selected_item+'_'+n).val():'');
			});
			
			$('#sample_form_layout').append(this_li);
			
			// add form fields with config options to the end of the li (hidden).
			// do not add an order to them until they're processed for submission so they never have to be reordered
			var buffer = '';
			$('#form_variables_form :input').each(function(){
				if($(this).attr('type') != 'submit'){
					$('#input_type_selector li:last').append('<input class="form_builder_pref" type="hidden" name="'+$(this).attr('name')+'" value="'+$(this).val()+'">');
				}
			});
			
			var mo = function() {
				$(this).prepend('<div class="btn_menu">'
						+'<div class="delete_btn">X</div>'
						+'<div class="down_btn">-</div>'
						+'<div class="up_btn">+</div>'
						+'<div class="change_btn">&Delta;</div>'
					+'</div>');
				
				$('.delete_btn',this).click(function(){
					$(this).parents('li').unbind().remove();
				});
				
				$('.up_btn',this).click(function(){
					var li = $(this).parents('li');
					var index = li.index();
					if (li.index() != $("#sample_form_layout li:first").index()) {
						console.log(index);
						// li item is not sitting in the first position
						$("#sample_form_layout li:eq(" + (index - 1) + ")").before(li.detach());
					}
				});
				
				$('.down_btn',this).click(function(){
					var li = $(this).parents('li');
					var index = li.index();
					if (li.index() != $("#sample_form_layout li:last").index()) {
						console.log(index);
						// li item is not sitting in the first position
						$("#sample_form_layout li:eq(" + (index + 1) + ")").after(li.detach());
					}
				});
				
				$('.change_btn',this).click(function(){
					var li = $(this).parents('li');
					var index = li.index();
					
					li.unbind('mouseenter',mo);
					
					// rebuild form for edit
					var rebuild = function(){
						var input_template = $('#tpl_'+$('input[name=field_type]',li).val()).html();
						var selected_item = $('input[name=field_type]',li).val();
						
						var this_li = tpl_li;
						$.each(li_vars, function(i,n){
							switch(n){
								case 'inputs':
								break;
								default:
									var user_specified_value = $('#'+selected_item+'_'+n).val()?$('#'+selected_item+'_'+n).val():'';
									this_li = replaceAll(this_li, '{% '+n+' %}', user_specified_value);
								break;
							}
						});
						this_li = replaceAll(this_li, '{% inputs %}', input_template);
						
						$.each(form_vars[$('#currently_selected_input_type').val()], function(i,n){
							this_li = replaceAll(this_li, '{% '+n+' %}', $('#'+selected_item+'_'+n).val()?$('#'+selected_item+'_'+n).val():'');
						});
						
						$('.form_li_preview li',li).html($(this_li).html());
					};
					// end rebuild form for edit
					
					var buffer = '';
					
					li.html('<div class="editor_container">'
							+'<ul class="form_li_preview"><li>'+li.html()+'</li></ul>'
							+'<form>Change type: <select name="editor"></select>'
							+'<div class="editor"></div>'
							+'<input type="button" value="Save Changes">&nbsp;<input type="button" value="Cancel">'
							+'</form>'
							+'<div class="original hidden">'+li.html()+'</div>'
						+'</div>');
					
					$('input[value="Cancel"]').click(function(){
						li.mouseenter(mo);
						li.html($('.original',li).html());
					});
					
					$('.editor_container select',li).append('<option></option>');
					// bindings for each of the field types. 
					$('#field_type_selector option').each(function(){
						$('.editor_container select',li).append('<option>'+$(this).val()+'</option>');
					});	
					
					var redisplay_form = function(){
						var selected_item = $(this).val();
						
						$.each(form_vars, function(i,n){
							if(selected_item == i){
								
								var buffer = '<table class="'+selected_item+'" style="width:100%"><tr><td>title </td><td><input type="text" id="'+selected_item+'_detail" name="detail" value="'+$('.editor input[name="detail"]',li).val()+'"></td></tr>';
								
								$.each(form_vars[i], function(i,n){
									if($.inArray(n,variables_needing_input) > -1){
										var field_name = n;
										var required = '';
										if(n == 'input_name'){
											field_name = 'property_name';
										} else if(n == 'form_name'){
											field_name = 'ModelName';
										} else if(n == 'size'){
											field_name = 'maximum field size';
										}
										
										if(n == 'required'){
											var value = $('.editor input[name="'+n+'"]',li).val()=='true'?'checked="checked"':'';
											buffer += '<tr class="'+n+'"><td><input type="checkbox" id="'+selected_item+'_'+n+'" name="'+n+'" value="true" '+value+'></td><td>Required this field?</td></tr>';
										} else {
											var value = $('.editor input[name="'+n+'"]',li).val()?$('.editor input[name="'+n+'"]',li).val():'';
											buffer += '<tr class="'+n+'"><td>'+replaceAll(field_name,'_',' ')+' </td><td><input type="text" id="'+selected_item+'_'+n+'" name="'+n+'" value="'+value+'" '+required+'></td></tr>';
										}
									}
								});
								
								buffer += '</table><input type="hidden" id="currently_selected_input_type" name="field_type" value="'+selected_item+'">';
								$('.editor_container .editor',li).html(buffer);
							}
						});
						rebuild();
					};
					
					$('.editor_container select',li).change(redisplay_form);
					
					var recall_form = function(){
						var selected_item = $('.original input[name="field_type"]',li).val();
						
						$.each(form_vars, function(i,n){
							if(selected_item == i){
								
								var buffer = '<table class="'+selected_item+'" style="width:100%"><tr><td>title </td><td><input type="text" id="'+selected_item+'_detail" name="detail" value="'+$('.original input[name="detail"]',li).val()+'"></td></tr>';
								
								$.each(form_vars[i], function(i,n){
									if($.inArray(n,variables_needing_input) > -1){
										var field_name = n;
										var required = '';
										if(n == 'input_name'){
											field_name = 'property_name';
										} else if(n == 'form_name'){
											field_name = 'ModelName';
										} else if(n == 'size'){
											field_name = 'maximum field size';
										}
										
										if(n == 'required'){
											var value = $('.original input[name="'+n+'"]',li).val()=='true'?'checked="checked"':'';
											buffer += '<tr class="'+n+'"><td><input type="checkbox" id="'+selected_item+'_'+n+'" name="'+n+'" value="true" '+value+'></td><td>Required this field?</td></tr>';
										} else {
											var value = $('.original input[name="'+n+'"]',li).val()?$('.editor input[name="'+n+'"]',li).val():'';
											buffer += '<tr class="'+n+'"><td>'+replaceAll(field_name,'_',' ')+' </td><td><input type="text" id="'+selected_item+'_'+n+'" name="'+n+'" value="'+value+'" '+required+'></td></tr>';
										}
									}
								});
								
								buffer += '</table><input type="hidden" id="currently_selected_input_type" name="field_type" value="'+selected_item+'">';
								$('.editor_container .editor',li).html(buffer);
							}
						});
						rebuild();
					};
					
					recall_form();
				});
				
			};
			
			// delete and ordering handlers
			$('#sample_form_layout li').unbind().hover(
				mo,
				function() {
					$('.btn_menu').unbind().remove();
				}
			);
			
		});
		
		var input_types_allowed = ['checkbox','date','datetime','file','full_name','mutliselect','password','radio_list','select','text','textarea','time'];
		var variables_needing_input = ['hint','input_name','form_name','required','size'];
		
		// bindings for each of the field types. 
		$('#input_type_selector li').each(function(){
			
			if($.inArray($.trim($(this).attr('class')),input_types_allowed) > -1){
				$('#field_type_selector').append('<option>'+$(this).attr('class')+'</option>')
				.change(function(){
					
					var selected_item = $(this).val();
					$('#sample_field').html($('#input_type_selector li.'+selected_item).clone());
					
					$.each(form_vars, function(i,n){
						if(selected_item == i){
							var buffer = '<table class="'+selected_item+'" style="width:100%"><tr><td>title </td><td><input type="text" id="'+selected_item+'_detail" name="detail" value="'+selected_item+'"></td></tr>';
							
							$.each(form_vars[i], function(i,n){
								if($.inArray(n,variables_needing_input) > -1){
									var field_name = n;
									var required = '';
									if(n == 'input_name'){
										field_name = 'property_name';
									} else if(n == 'form_name'){
										field_name = 'ModelName';
									} else if(n == 'size'){
										field_name = 'maximum field size';
									}
									
									if(n == 'required'){
										buffer += '<tr class="'+n+'"><td><input type="checkbox" id="'+selected_item+'_'+n+'" name="'+n+'" value="true"></td><td>Required this field?</td></tr>';
									} else {
										buffer += '<tr class="'+n+'"><td>'+replaceAll(field_name,'_',' ')+' </td><td><input type="text" id="'+selected_item+'_'+n+'" name="'+n+'" value="" '+required+'></td></tr>';
									}
								}
							});
							
							buffer += '</table><input type="hidden" id="currently_selected_input_type" name="field_type" value="'+selected_item+'">';
							$('#form_variables').html(buffer);
						}
					});
				});
			} // end input type check
		});	

		
	}
);


