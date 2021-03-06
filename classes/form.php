<?php # 4.0.0 - 14/09/2017 19:23 - Bootstrap 4b1
class form{
	private	$args;
	private	$has_date		=false;
	private	$has_datetime	=false;
	private	$has_file		=false;
	public	$field_count	=1; # form_name is auto_prepended
	public	$button_count	=1;
	public	$uploaded_files	=0;
	private	$has_required	=false;
	private $session_data;	# Data carried over from an dev defined error
	# bootsrap settings
	protected	$label_width=2;
	protected	$value_width;
	# Inherit Methods
	public function __construct($args){
		global $app;
		$this->button_count=0;
		$this->field_count=0;
		$this->args=$args;
		if(is_array($args)){
			foreach($args as $key=>$value){
				$$key=$value;
			}
		}elseif(is_string($args)){
			parse_str($args,$values);
			foreach($values as $key=>$value){
				$$key=$value;
			}
		}
		$this->value_width=12-$this->label_width;
		if($hide_required_message==1){
			$this->hide_required_message=1;
		}else{
			$this->hide_required_message=0;
		}
		$this->data=array(
			'action'		=>$action,
			'autocomplete'	=>strtolower($autocomplete),
			'class'			=>$class,
			'data'			=>$data,
			'enctype'		=>$enctype,
			'name'			=>$name,
			'showinfo'		=>$showinfo
		);
		# Unset any other form data, the results are expired
		if($_SESSION['form_data']){
			if($_SESSION['form_data'][$this->data['name']]){
				$this->session_data=$this->unname($_SESSION['form_data'][$this->data['name']]);
				unset($_SESSION['form_data'][$this->data['name']]);
			}
		}
		if(strpos($class,'form-horizontal')!==false){
			$this->data['orientation']='horizontal';
		}elseif(strpos($class,'form-inline')!==false){
			$this->data['orientation']='inline';
		}else{
			$this->data['orientation']='stacked';
		}
		if($err){
			$app->set_message('error',implode('<br>',$err));
		}
		return $this;
	}
	public function set_label_width($col_sm_width){
		$this->label_width=$col_sm_width;
		$this->value_width=12-$col_sm_width;
	}
	public function reset_label_width(){
		$this->label_width=2;
		$this->value_width=12-$this->label_width;
	}
	# Initial processing... validation
	public function process(){
		if($_POST['form_name']==$this->data['name']){
			global $app;
			if($_FILES){
				foreach($_FILES as $field=>$files){
					if(is_array($files['error'])){
						foreach($files['error'] as $error){
							if($error===0){
								$mimes=json_decode(file_get_contents(ROOT.'/libraries/mimetypes.json'),1);
								$finfo=finfo_open(FILEINFO_MIME_TYPE);
								break;
							}
						}
						if($finfo){
							break;
						}
					}elseif($files['error']===0){
						$mimes=json_decode(file_get_contents(ROOT.'/libraries/mimetypes.json'),1);
						$finfo=finfo_open(FILEINFO_MIME_TYPE);
						break;
					}
				}
				foreach($_FILES as $name=>$file){
					if(is_array($file['error'])){
						foreach($file['error'] as $key=>$error){
							if($error!=4){
								$this->uploaded_files++;
								$extension=strtolower(substr($file['name'][$key],strrpos($file['name'][$key],'.')+1));
								$_FILES[$name]['extension'][$key]=$extension;
								$mime=finfo_file($finfo,$file['tmp_name'][$key]);
								$accepted=false;
								if(!in_array($extension,$this->data['files'][$name.'[]']) && !in_array($extension,$this->data['files'][$name.'['.$key.']'])){
									$app->set_message('error','File ('.$file['name'][$key].') does not have an accepted extension'.__LINE__);
								}else{
									foreach($this->data['files'][$name.'[]'] as $ext){
										if($mimes[$ext]==$mime || (is_array($mimes[$ext]) && in_array($mime,$mimes[$ext]))){
											$accepted=true;
										}
									}
								}
								if($accepted==true){
									switch($error){
										case 1:
										case 2:
											$app->set_message('error','The ('.$file['name'][$key].') file size exceeds the upload limit.');
											break;
										case 3:
											$app->set_message('error','There was an issue uploading the file ('.$file['name'][$key].'), please try again. If this problem persists, please contact <a href="mailto:'.SITE_EMAIL.'">'.SITE_EMAIL.'</a> referencing "Upload Error 3"');
											break;
										case 6:
										case 7:
										case 8:
											$app->set_message('error','There was an issue with the server, please contact <a href="mailto:'.SITE_EMAIL.'">'.SITE_EMAIL.'</a>, referencing "Upload Error 6-8".');
											break;
									}
								}else{
									$app->set_message('error','The file ('.$file['name'].') does not have the correct encoding for the extension.');
								}
								$_FILES[$name]['name'][$key]=slug($file['name'][$key]);
							}
						}
					}else{
						if($file['error']!=4){
							$this->uploaded_files++;
							$_FILES[$name]['extension']=strtolower(substr($file['name'],strrpos($file['name'],'.')+1));
							$mime=finfo_file($finfo,$file['tmp_name']);
							$accepted=false;
							if(!in_array($_FILES[$name]['extension'],$this->data['files'][$name])){
								$app->set_message('error','File ('.$file['name'].') does not have an accepted extension');
							}else{
								foreach($this->data['files'][$name] as $ext){
									if($mimes[$ext]==$mime || (is_array($mimes[$ext]) && in_array($mime,$mimes[$ext]))){
										$accepted=true;
									}
								}
							}
							if($accepted==true){
								switch($file['error']){
									case 1:
									case 2:
										$app->set_message('error','The file ('.$file['name'].') size exceeds the upload limit.');
										break;
									case 3:
										$app->set_message('error','There was an issue uploading the file ('.$file['name'].'), please try again. If this problem persists, please contact <a href="mailto:'.SITE_EMAIL.'">'.SITE_EMAIL.'</a> referencing "Upload Error 3"');
										break;
									case 6:
									case 7:
									case 8:
										$app->set_message('error','There was an issue with the server, please contact <a href="mailto:'.SITE_EMAIL.'">'.SITE_EMAIL.'</a>, referencing "Upload Error 6-8".');
										break;
								}
							}else{
								$app->set_message('error','The file ('.$file['name'].') does not have the correct encoding for the extension.');
								return array(
									'status'=>'error'
								);
							}
							$_FILES[$name]['name']=slug($file['name']);
						}
					}
				}
				if($finfo){
					finfo_close($finfo);
				}
			}
			// Validate
				# Against Captcha
			if(RECAPTCHA_SITE_KEY && RECAPTCHA_SECRET_KEY && isset($_POST[$this->form_name().'has_captcha'])){
				$google_response=@json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".RECAPTCHA_SECRET_KEY."&response=".$_POST['g-recaptcha-response']),1);
				if(!$_POST['g-recaptcha-response']){
					$app->set_message('error',"reCAPTCHA is required");
					$this->errors=true;
				}elseif(!$google_response['success']){
					$app->set_message(
						'error',
						implode(
							',<br>',
							array_map(
								function(&$return){
									$return=str_replace('-',' ',ucfirst($return));
								},
								$google_response['error-codes']
							)
						)
					);
				}
			}
			foreach($this->data['fields'] as $field){
				unset($temp_name);
				if(!$field['label']){
					$field['label']=ucwords(str_replace('_',' ',$field['name']));
				}
				if($field['type']!='html'){
					$field['name']=str_replace('[]','',$field['name']);
					if(strpos($field['name'],'[')===false){
						$post_key=$this->data['name'].'_'.$field['name'];
						if($post_key && array_key_exists($post_key,$_POST)){
							$temp_name=$_POST[$post_key];
						}
					}else{
						$open=strpos($field['name'],'[');
						$var=substr($field['name'],$open+1,-1);
						$post_key=$this->data['name'].'_'.substr($field['name'],0,$open);
						if($post_key && array_key_exists($post_key,$_POST)){
							$temp_name=$_POST[$post_key][$var];
						}
					}
					if(!in_array($field['type'],array('select'))){
						if($field['required']==1 && !in_array($field['type'],array('file','hidden')) && !isset($temp_name)){
							$app->set_message('error',"'{$field['label']}' is required, but has not been completed.");
							$this->errors=true;
						}
						if($temp_name && $field['type']=='email' && !validate_email($temp_name)){
							$app->set_message('error',"'".$temp_name."' is not a valid email address.");
							$this->errors=true;
						}
					}elseif($field['required']==1 && !isset($temp_name)){
						$app->set_message('error',"'{$field['label']}' is required, but has not been completed.");
						$this->errors=true;
					}
				}
			}
			if($this->errors){
				$this->redirect(false,$_POST);
			}else{
				$return=array(
					'status'=>'success',
					'data'=>$_POST
				);
				if($_FILES){
					$return['stats']['uploaded_files']=$this->uploaded_files;
					$return['files']=$_FILES;
				}
				return $return;
			}
		}
	}
	# Form Methods
	public function add_button($args){
		global $app;
		if(is_string($args)){
			parse_str($args,$args);
		}
		if($args['type']=='image' && (!$args['src'] && (!$args['height'] || !$args['height']))){
			$app->set_message('error',"A button of type 'image' must include a 'src'. It also must include a 'height' or 'width'");
		}else{
			$this->data['fields'][]=$args;
		}
		$this->button_count++;
	}
	# Add captcha field
	public function add_captcha($args=NULL){
		global $app;
		if(RECAPTCHA_SITE_KEY && RECAPTCHA_SECRET_KEY){
			if($args && is_string($args)){
				parse_str($args,$args);
			}
			$args['type']='captcha';
			$this->data['fields'][]=$args;
			$this->add_field(array(
				'name'	=>'has_captcha',
				'type'	=>'hidden',
				'value'	=>1
			));
			$app->add_to_head('<script src="https://www.google.com/recaptcha/api.js"></script>');
		}
	}
	# Mass field add
	public function add_fields(array $fields){
		foreach($fields as $field){
			$this->add_field($field);
		}
	}
	# Add standard html <input> and textareas
	public function add_field($args){
		global $app;
		if(is_string($args)){
			parse_str($args,$args);
		}
		if(!in_array($args['type'],array('checkbox','color','date','datetime','datetime-local','email','file','hidden','month','number','password','radio','range','search','tel','text','textarea','time','url','week','static'))){# Static = Display text only
			$app->set_message('error',"Type of `".$args['type']."` is not a valid HTML5 type. Field Data: ".print_pre($args,1));
			$this->error=true;
		}elseif(in_array($args['type'],array('button','image','reset','submit'))){
			$app->set_message('error','Type of `'.$args['type'].' is not valid for this function, use $class->add_button() instead');
			$this->error=true;
		}elseif($args['type']!='static' && !$args['name']){
			$app->set_message('error',"Non-static fields must include `name` data");
			$this->error=true;
		}elseif($args['type']=='file' && !$args['accept']){
			$app->set_message('error',"Files must include `accept` data, a comma seperated list of supported file extensions.");
			$this->error=true;
		}else{
			if($args['type']=='datetime'){
				$args['type']='datetime-local';
			}
			if($args['type']=='date'){
				if($this->has_date==false){
					$app->add_to_head('<link rel="stylesheet" href="'.$app->cdn->datepicker->css.'">');
					$app->add_to_foot('<script src="'.$app->cdn->datepicker->js.'"></script>');
				}
				$this->has_date=true;
			}
			if($args['type']=='datetime-local'){
				if($this->has_datetime==false){
					$app->add_to_head('<link rel="stylesheet" href="'.$app->cdn->datetimepicker->css.'">');
					$app->add_to_foot('<script src="'.$app->cdn->moment.'"></script>');
					$app->add_to_foot('<script src="'.$app->cdn->datetimepicker->js.'"></script>');
				}
				$this->has_datetime=true;
			}
			if($args['required'] && $args['type']=='static'){
				unset($args['required']);
			}
			if($args['required']){
				$this->has_required=1;
			}
			if($args['type']=='file'){
				$this->data['files'][$this->data['name'].'_'.$args['name']]=explode(',',$args['accept']);
				$this->has_file=true;
			}
			if($this->session_data){
				if($val=$this->find_value($args['name'],$this->session_data)){
					$args['value']=$val;
				}
			}
			# Add to $data
			$this->data['fields'][]=$args;
			$this->field_count++;
		}
	}
	# Add HTML Element
	public function add_html($html){
		$this->data['fields'][]=array(
			'type'=>'html',
			'html'=>$html
		);
	}
	# Add option to select
	# Updated 16/03/2017 17:18
	private function add_options($data,$selected){
		if(!is_array($selected)){
			$temp=@json_decode($selected,1);
			if(is_array($temp)){
				$selected=$temp;
			}elseif(isset($selected)){
				$selected=array($selected);
			}
		}
		foreach($data as $key=>$d){
			$key=addslashes($key);
			if(is_array($d)){
				$out.="<optgroup label='".ucwords($key)."'>";
				foreach($d as $key2=>$options){
					$key2=addslashes($key2);
					$out.="<option";
					if(is_array($selected) && in_array($key2,$selected)){
						$out.=" selected";
					}
					$out.=' value="'.$key2.'">'.$options.'</option>';
				}
				$out.="</optgroup>";
			}else{
				$out.="<option";
				if(is_array($selected) && in_array($key,$selected)){
					$out.=" selected";
				}
				$out.=' value="'.$key.'">'.$d.'</option>';
			}
		}
		return $out;
	}
	# Add Select
	public function add_select($args,array $options,$default_text=NULL,array $before_options=NULL,array $after_options=NULL){
		if(is_string($args)){
			parse_str($args,$args);
		}
		if($args['required']){
			$this->has_required=1;
		}
		if($this->session_data[$args['name']]){
			$args['value']=$this->session_data[$args['name']];
		}
		# Append extra info
		$args['type']="select";
		$args['options']=$options;
		$args['default_text']=$default_text;
		$args['before']=$before_options;
		$args['after']=$after_options;
		# Add to $data
		$this->data['fields'][]=$args;
		$this->field_count++;
	}
	# Output form
	public function get_form($orientation=NULL,$return=false){
		global $app;
		if($this->field_count<1){
			$errors[]='No fields have been added to the form. Add fields to the form before attempting an output.';
		}elseif($this->error==true){
			$errors[]='There was an error generating this form, please <a href="/contact.php">contact us</a> to let us know.';
		}elseif(($this->field_count+$this->button_count)>ini_get('max_input_vars')){
			$errors[]='Could not render form as it contains <strong>'.($this->field_count+$this->button_count).'</strong> fields, which is above the allowed by the server of <strong>'.ini_get('max_input_vars').'</strong>.';
			$app->log_message(1,'Field count exceeds server limit.','The form <strong>'.$this->data['name'].'</strong> contains <strong>'.($this->field_count+$this->button_count).'</strong> fields, which is above the allowed by the server of <strong>'.ini_get('max_input_vars').'</strong>.');
		}
		if(!$this->data['name']){
			$errors[]='A form name is required';
		}elseif($this->data['data'] && in_array('form-id',array_keys($this->data['data']))){
			$errors[]='Form data cannot include the key <strong>form-id</strong>';
		}
		if($errors){
			if($return){
				return $app->show_message('error',implode('',$errors),1);
			}else{
				$app->show_message('error',implode('',$errors));
				return;
			}
		}else{
			if($orientation=='inline'){
				$this->data['orientation']='inline';
				$this->data['class']=str_replace('form-horizontal','form-inline',$this->data['class']);
			}elseif($orientation=='horizontal'){
				$this->data['orientation']='horizontal';
				$this->data['class']=str_replace('form-inline','form-horizontal',$this->data['class']);
			}elseif($orientation=='stacked'){
				$this->data['orientation']='stacked';
			}
			$ids=array();
			if($this->has_required==1 && $this->hide_required_message!=1){
				$out.='<div class="alert alert-warning"><p>* denotes a required field.</p></div>';
			}
			$out.='<form';
			if($this->data['action']){
				$out.=' action="'.$this->data['action'].'"';
			}
			if($this->data['autocomplete'] && in_array($this->data['autocomplete'],array('on','off'))){
				$out.=' autocomplete="'.$this->data['autocomplete'].'"';
			}
			if($this->data['class']){
				$out.=' class="'.$this->data['class'].'"';
			}
			if($this->data['data'] && is_array($this->data['data'])){
				asort($this->data['data']);
				foreach($this->data['data'] as $key=>$value){
					$out.=' data-'.$key.'="'.$value.'"';
				}
			}
			if($this->data['enctype']){
				$out.=' enctype="'.$this->data['enctype'].'"';
			}elseif($this->has_file){
				$out.=' enctype="multipart/form-data"';
			}
			$ids[]=$this->data['name'].'_form';
			$out.=' id="'.$ids[sizeof($ids)-1].'" method="post" novalidate>';
			foreach($this->data['fields'] as $field){
				$i=1;
				$found=true;
				if($field['name']){
					$id=str_replace(array('[]','[',']'),array('','_',''),$this->data['name'].'_'.$field['name']);
					$field['name']=$this->data['name'].'_'.$field['name'];
					$tempid=$id;
					while($found==true){
						if(in_array($tempid,$ids)){
							$tempid=$id.'_'.$i;
							$i++;
						}else{
							$ids[]=$tempid;
							$found=false;
						}
					}
					$id=$ids[sizeof($ids)-1];
				}
				switch($field['type']){
					case 'button':
					case 'image':
					case 'reset':
					case 'submit':
						$out.='<input';
							if($field['autofocus']){
								$out.=' autofocus';
							}
							$out.=' class="btn btn-sm';
							if($field['class']){
								$out.=' '.$field['class'];
							}
							$out.='"';
							if($field['data'] && is_array($field['data'])){
								asort($field['data']);
								foreach($field['data'] as $key=>$value){
									$out.=' data-'.$key.'="'.$value.'"';
								}
							}
							if($field['disabled']==1){
								$out.=' disabled';
							}
							if($field['formaction']){
								$out.=' formaction="'.$field['formaction'].'"';
							}
							if($field['formtarget']){
								$out.=' formtarget="'.$field['formtarget'].'"';
							}
							$out.=' id="'.$id.'" name="'.$field['name'].'"';
							if($field['tabindex']){
								$out.=' tabindex="'.$field['tabindex'].'"';
							}
							$out.=' type="'.$field['type'].'"';
							if($field['value']){
								$out.=' value="'.$field['value'].'"';
							}
						$out.='> ';
						break;
					case 'captcha':
						$out.='<div class="form-group';
							if($field['wrapclass']){
								$out.=' '.$field['wrapclass'];
							}
							if($this->data['orientation']=='horizontal'){
								$out.=' row';
							}
						$out.='">';
							if($field['label']){
								$out.='<label class="col-form-label';
								if($this->data['orientation']=='horizontal'){
									$out.=' col-sm-'.$this->label_width;
								}
								if($field['labelclass']){
									$out.=' '.$field['labelclass'];
								}
								$out.='" for="'.$id.'">'.$field['label'];
								if($field['required']){
									$out.='*';
								}
								$out.='</label>';
							}
							if($this->data['orientation']=='horizontal'){
								$out.='<div class="col-sm-'.$this->value_width;
									if(!$field['label']){
										$out.=' ml-auto';
									}
								$out.='">';
							}
							$out.=' <div class="g-recaptcha';
							if($this->data['orientation']=='inline'){
								$out.=' dib-vam';
							}
							$out.='" data-sitekey="'.RECAPTCHA_SITE_KEY.'"></div>';
							if($field['note']){
								$out.='<small class="form-text';
								if($this->data['orientation']=='horizontal'){
									$out.=' ml-auto col-sm-'.$this->value_width.' ';
								}
								$out.='">'.$field['note'].'</small>';
							}
							if($this->data['orientation']=='horizontal'){
								$out.='</div>';
							}
						$out.='</div> ';
						break;
					case 'checkbox':
					case 'radio':
						$out.='<div class="form-group '.str_replace(array('[',']'),'',$field['name']).'_outer';
							if($field['required']){
								$out.=' has-warning';
							}
							if($field['wrapclass']){
								$out.=' '.$field['wrapclass'];
							}
							if($this->data['orientation']=='horizontal'){
								$out.=' row';
							}
						$out.='" id="'.$id.'_outer">';
							if($field['label'] && $this->data['orientation']!='inline'){
								$out.='<label class="mb-0 form-check-label';
									if($this->data['orientation']=='horizontal'){
										$out.=' col-sm-'.$this->label_width;
									}
									if($field['labelclass']){
										$out.=' '.$field['labelclass'];
									}
								$out.='">';
									if($field['label']){
										$out.=$field['label'];
										if($field['required']){
											$out.='*';
										}
									}
								$out.='</label> ';
								if($this->data['orientation']=='horizontal'){
									$out.='<div class="col-sm-'.$this->value_width.'">';
								}
							}elseif($this->data['orientation']=='horizontal'){
								$out.='<div class="ml-auto col-sm-'.$this->value_width.'">';
							}
							$out.='<div class="'.strtolower($field['type']);
							if($field['note']){
								$out.=' mb-0';
							}
							$out.='">	
								<label class="col-check-label';
									if($field['labelclass']){
										$out.=' '.$field['labelclass'].'';
									}
								$out.='" for="'.$id.'">
									<input';
										if($field['autofocus']){
											$out.=' autofocus';
										}
										if($field['checked']==1){
											$out.=' checked';
										}
										if($field['class'] || $field['required']){
											$out.=' class="';
												if($field['class']){
													$out.=$field['class'];
												}
											$out.='"';
										}
										if($field['data'] && is_array($field['data'])){
											asort($field['data']);
											foreach($field['data'] as $key=>$value){
												$out.=' data-'.$key.'="'.$value.'"';
											}
										}
										if($field['disabled']==1){
											$out.=' disabled';
										}
										$out.=' id="'.$ids[sizeof($ids)-1].'"';
										if($field['list']){
											$out.=' list="'.$field['list'].'"';
										}
										if($field['max'] && in_array($field['type'],array('date','datetime-local','month','number','time','week'))){
											$out.=' max="'.$field['max'].'"';
										}
										if($field['maxlength'] && in_array($field['type'],array('text','email','search','password','tel'))){
											$out.=' maxlength="'.$field['maxlength'].'"';
										}
										if(isset($field['min']) && in_array($field['type'],array('date','datetime-local','month','number','time','week'))){
											$out.=' min="'.$field['min'].'"';
										}
										if($field['minlength'] && in_array($field['type'],array('text','email','search','password','tel'))){
											$out.=' minlength="'.$field['minlength'].'"';
										}
										if($field['multiple'] && in_array($field['type'],array('email','file'))){
											$out.=' multiple';
										}
										$out.=' name="'.$field['name'].'"';
										if($field['pattern'] && in_array($field['type'],array('email','search','tel','text','url'))){
											$out.=' pattern="'.$field['pattern'].'"';
										}
										if($field['readonly'] && !in_array($field['type'],array('color','file','range'))){
											$out.=' readonly';
										}
										if($field['required']==1){
											$out.=' required';
										}
										if($field['size'] && in_array($field['type'],array('email','password','search','tel','text','url'))){
											$out.=' size="'.$field['size'].'"';
										}
										if($field['spellcheck']){
											$out.=' spellcheck="'.$field['spellcheck'].'"';
										}
										if($field['step'] && in_array($field['type'],array('date','datetime-local','month','number','time','week'))){
											$out.=' step="'.$field['step'].'"';
										}
										if($field['tabindex']){
											$out.=' tabindex="'.$field['tabindex'].'"';
										}
										$out.=' type="'.$field['type'].'"';
										if(isset($field['value'])){
											$out.=' value="'.$field['value'].'"';
										}
									$out.='> ';
									if($this->data['orientation']!='inline'){
										$out.=$field['postfield'];
									}else{
										$out.=($field['label']?$field['label']:$field['postfield']);
									}
									if($field['required'] && !$field['label']){
										$out.='*';
									}
								$out.='</label>
							</div>';
							if($this->data['orientation']=='horizontal'){
								$out.='</div>';
							}
							if($field['note']){
								$out.='<small class="form-text ';
								if($this->data['orientation']=='horizontal'){
									$out.='ml-auto col-sm-'.$this->value_width.' ';
								}
								$out.='">'.$field['note'].'</small>';
							}
						$out.='</div> ';
						break;
					case 'color':
					case 'date':
					case 'datetime-local':
					case 'email':
					case 'file':
					case 'month':
					case 'number':
					case 'password':
					case 'range':
					case 'search':
					case 'tel':
					case 'time':
					case 'text':
					case 'url':
					case 'week':
						$out.=' <div class="form-group '.str_replace(array('[',']'),'',$field['name']).'_outer';
						if($field['required']){
							$out.=' has-warning';
						}
						if($field['wrapclass']){
							$out.=' '.$field['wrapclass'];
						}
						if($this->data['orientation']=='horizontal'){
							$out.=' row';
						}
						$out.='" id="'.$id.'_outer">';
							if($field['label']){
								$out.=' <label class="col-form-label';
								if($this->data['orientation']=='horizontal'){
									$out.=' col-sm-'.$this->label_width.' ';
								}
								if($field['labelclass']){
									$out.=' '.$field['labelclass'];
								}
								$out.='" for="'.$ids[sizeof($ids)-1].'">'.$field['label'];
								if($field['required']){
									$out.="*";
								}
								$out.='</label> ';
							}
							if($this->data['orientation']=='horizontal' || isset($field['prefield']) || isset($field['postfield']) || ($field['required'] && !$field['label'])){
								$out.='<div class="';
								if($this->data['orientation']=='horizontal'){
									if(!$field['label']){
										$out.='ml-auto';
									}
									$out.=' col-sm-'.$this->value_width;
								}
								if(isset($field['prefield']) || isset($field['postfield']) || ($field['required'] && !$field['label'])){
									$out.=' input-group';
								}
								$out.='">';
							}
							if(isset($field['prefield']) || ($field['required'] && !$field['label'])){
								$out.='<div class="input-group-addon">'.$field['prefield'];
								if($field['required'] && !$field['label']){
									$out.='*';
								}
								$out.='</div>';
							}
							$out.='<input';
								if($field['type']=='file' && $field['accept']){
									$out.=' accept=".'.implode(',.',explode(',',str_replace('.','',$field['accept']))).'"';
								}
								if(!in_array($field['type'],array('file','password')) &&
								strtolower($field['autocomplete'])=='off' || $field['autocomplete']===0){
									$out.=' autocomplete="off"';
								}
								if($field['autofocus']){
									$out.=' autofocus';
								}
								if($field['type']=='search'){
									$out.=' autosave="'.$field['name'].'"';
								}
								$out.=' class="form-control'.($field['type']=='file'?"-file":"");
								if($field['class']){
									$out.=' '.$field['class'];
								}
								if($field['required']){
									$out.=' form-control-warning';
								}
								$out.='"';
								if($field['data'] && is_array($field['data'])){
									asort($field['data']);
									foreach($field['data'] as $key=>$value){
										$out.=' data-'.$key.'="'.$value.'"';
									}
								}
								if($field['disabled']==1){
									$out.=' disabled';
								}
								$out.=' id="'.$ids[sizeof($ids)-1].'"';
								if($field['max'] && in_array($field['type'],array('date','datetime-local','month','number','time','week'))){
									$out.=' max="'.$field['max'].'"';
								}
								if($field['maxlength'] && in_array($field['type'],array('text','email','search','password','tel'))){
									$out.=' maxlength="'.$field['maxlength'].'"';
								}
								if(isset($field['min']) && in_array($field['type'],array('date','datetime-local','month','number','time','week'))){
									$out.=' min="'.$field['min'].'"';
								}
								if($field['minlength'] && in_array($field['type'],array('text','email','search','password','tel'))){
									$out.=' minlength="'.$field['minlength'].'"';
								}
								if($field['multiple'] && in_array($field['type'],array('email','file'))){
									$out.=' multiple';
								}
								$out.=' name="'.$field['name'].'"';
								if($field['pattern'] && in_array($field['type'],array('email','search','tel','text','url'))){
									$out.=' pattern="'.$field['pattern'].'"';
								}
								if(isset($field['placeholder']) && in_array($field['type'],array('email','number','password','search','tel','text','url'))){
									$out.=' placeholder="'.$field['placeholder'].'"';
								}
								if($field['readonly'] && !in_array($field['type'],array('color','file','range'))){
									$out.=' readonly';
								}
								if($field['required']==1){
									$out.=' required';
								}
								if($field['size'] && in_array($field['type'],array('email','password','search','tel','text','url'))){
									$out.=' size="'.$field['size'].'"';
								}
								if($field['spellcheck']){
									$out.=' spellcheck="'.$field['spellcheck'].'"';
								}
								if($field['step'] && in_array($field['type'],array('date','datetime-local','month','number','time','week'))){
									$out.=' step="'.$field['step'].'"';
								}
								if($field['tabindex']){
									$out.=' tabindex="'.$field['tabindex'].'"';
								}
								$out.=' type="'.$field['type'].'"';
								if(isset($field['value'])){
									if($field['type']=='datetime-local'){
										$field['value']=iso_datetime($field['value'],1);
									}
									$out.=' value="'.$field['value'].'"';
								}
							$out.='>';
							if(isset($field['postfield'])){
								$out.='<div class="input-group-addon">'.$field['postfield'].'</div>';
							}
							if($this->data['orientation']=='horizontal' || isset($field['prefield']) || isset($field['postfield'])){
								$out.='</div>';
							}
							if($field['type']=='file'){
								$out.='<p class="';
								if($this->data['orientation']=='horizontal'){
									$out.='ml-auto col-sm-'.$this->value_width.' ';
								}
								$out.='mb-0 text-muted"><em>Accepts: .'.implode(', .',explode(',',$field['accept'])).'</em></p>';
							}
							if($field['note'] || $field['type']=='datetime-local'){
								$out.='<small class="'.($this->data['orientation']=='horizontal'?'ml-auto col-sm-'.$this->value_width.' ':'').'form-text">';
									if($field['note']){
										$out.=$field['note'].($field['type']=='datetime-local'?'<br>':'');
									}
									if($field['type']=='datetime-local'){
										$out.='Please ensure that a date and time has been specified.';
									}
								$out.='</small>';
							}
						$out.='</div> ';
						break;
					case 'hidden':
						$out.='<input class="'.$field['class'].'" ';
						if($field['data'] && is_array($field['data'])){
							asort($field['data']);
							foreach($field['data'] as $key=>$value){
								$out.=' data-'.$key.'="'.$value.'"';
							}
						}
						$out.=' id="'.$ids[sizeof($ids)-1].'" name="'.$field['name'].'" type="hidden"';
						if(isset($field['value'])){
							$out.=' value="'.$field['value'].'"';
						}
						$out.='>';
						break;
					case 'html':
						$out.=$field['html'];
						break;
					case 'select':
						$out.=' <div class="form-group '.str_replace(array('[',']'),'',$field['name']).'_outer';
						if($field['required']){
							$out.=' has-warning';
						}
						if($field['wrapclass']){
							$out.=' '.$field['wrapclass'];
						}
						if($this->data['orientation']=='horizontal'){
							$out.=' row';
						}
						$out.='" id="'.$id.'_outer">';
							if($field['label']){
								$out.=' <label class="col-form-label';
								if($this->data['orientation']=='horizontal'){
									$out.=' col-sm-'.$this->label_width.' ';
								}
								if($field['labelclass']){
									$out.=' '.$field['labelclass'];
								}
								$out.='" for="'.$ids[sizeof($ids)-1].'">'.$field['label'];
								if($field['required']){
									$out.="*";
								}
								$out.='</label> ';
							}
							if($this->data['orientation']=='horizontal'){
								$out.='<div class="';
								if(!$field['label']){
									$out.='ml-auto';
								}
								$out.=' col-sm-'.$this->value_width.'">';
							}
								$out.='<select';
									if($field['autofocus']){
										$out.=' autofocus';
									}
									$out.=' class="form-control';
									if($field['class']){
										$out.=' '.$field['class'];
									}
									if($field['required']){
										$out.=' form-control-warning';
									}
									$out.='"';
									if($field['data'] && is_array($field['data'])){
										asort($field['data']);
										foreach($field['data'] as $key=>$value){
											$out.=' data-'.$key.'="'.$value.'"';
										}
									}
									if($field['disabled']==1){
										$out.=' disabled';
									}
									$out.=' id="'.$id.'"';
									if($field['multiple']){
										$out.=' multiple';
									}
									$out.=' name="'.$field['name'].'"';
									if($field['required']==1){
										$out.=' required';
									}
									if($field['multiple']){
										$out.=' size="'.($field['size']?($field['size']>25?25:$field['size']):5).'"';
									}
								$out.='>';
									if(isset($field['default_text']) && !$field['multiple']){
										$out.='<option value="">'.$field['default_text'].'</option>';
									}
									if(isset($field['before'])){
										$out.=$this->add_options($field['before'],$field['value']);
									}
									$out.=$this->add_options($field['options'],$field['value']);
									if(isset($field['after'])){
										$out.=$this->add_options($field['after'],$field['value']);
									}
								$out.='</select>';
							if($this->data['orientation']=='horizontal'){
								$out.='</div>';
							}
							if($field['multiple']==1){
								$out.='<p class="';
								if($this->data['orientation']=='horizontal'){
									$out.='ml-auto col-sm-'.$this->value_width.' ';
								}
								$out.='mb-0 text-muted"><em><kbd>CTRL/CMD</kbd> + Click to select multiple items.</em></p>';
							}
							if($field['note']){
								$out.='<small class="';
								if($this->data['orientation']=='horizontal'){
									$out.='ml-auto col-sm-'.$this->value_width.' ';
								}
								$out.='form-text">'.$field['note'].'</small>';
							}
						$out.='</div> ';
						break;
					case 'static':
						$out.=' <div class="form-group ';
						if($field['wrapclass']){
							$out.=' '.$field['wrapclass'];
						}
						if($this->data['orientation']=='horizontal'){
							$out.=' row';
						}
						$out.='" id="'.$id.'_outer">';
							if($field['label']){
								$out.=' <label class="col-form-label';
								if($this->data['orientation']=='horizontal'){
									$out.=' col-sm-'.$this->label_width.' ';
								}
								if($field['labelclass']){
									$out.=' '.$field['labelclass'];
								}
								$out.='" for="'.$ids[sizeof($ids)-1].'">'.$field['label'];
								$out.='</label> ';
							}
							if($this->data['orientation']=='horizontal'){
								$out.='<div class="';
								if(!$field['label']){
									$out.='ml-auto';
								}
								$out.=' col-sm-'.$this->value_width.'">';
							}
							$out.='<p class="form-control-plaintext">'.$field['prefield'].$field['value'].$field['postfield'].'</p>';
							if($field['note']){
								$out.='<small class="';
								if($this->data['orientation']=='horizontal'){
									$out.='ml-auto col-sm-'.$this->value_width.' ';
								}
								$out.='form-text">'.$field['note'].'</small>';
							}
							if($this->data['orientation']=='horizontal'){
								$out.='</div>';
							}
						$out.='</div> ';
						break;
					case 'textarea':
						$out.=' <div class="form-group '.str_replace(array('[',']'),'',$field['name']).'_outer';
						if($field['required']){
							$out.=' has-warning';
						}
						if($field['wrapclass']){
							$out.=' '.$field['wrapclass'];
						}
						if($this->data['orientation']=='horizontal'){
							$out.=' row';
						}
						$out.='" id="'.$id.'_outer">';
							if($field['label']){
								$out.=' <label class="col-form-label';
								if($this->data['orientation']=='horizontal'){
									$out.=' col-sm-'.$this->label_width.' ';
								}
								if($field['labelclass']){
									$out.=' '.$field['labelclass'];
								}
								$out.='" for="'.$ids[sizeof($ids)-1].'">'.$field['label'];
								if($field['required']){
									$out.="*";
								}
								$out.='</label> ';
							}
							if($this->data['orientation']=='horizontal'){
								$out.='<div class="';
								if(!$field['label']){
									$out.='ml-auto';
								}
								$out.=' col-sm-'.$this->value_width.'">';
							}
							$out.='<textarea';
								if(strtolower($field['autocomplete'])=='off' || $field['autocomplete']===0){
									$out.=' autocomplete="off"';
								}
								if($field['autofocus']){
									$out.=' autofocus';
								}
								$out.=' class="form-control';
								if($field['class']){
									$out.=' '.$field['class'];
								}
								if($field['required']){
									$out.=' form-control-warning';
								}
								$out.='"';
								if($field['cols']){
									$out.=' cols="'.$field['cols'].'"';
								}
								if($field['data'] && is_array($field['data'])){
									asort($field['data']);
									foreach($field['data'] as $key=>$value){
										$out.=' data-'.$key.'="'.$value.'"';
									}
								}
								if($field['disabled']==1){
									$out.=' disabled';
								}
								$out.=' id="'.$id.'"';
								if($field['maxlength'] && in_array($field['type'],array('text','email','search','password','tel'))){
									$out.=' maxlength="'.$field['maxlength'].'"';
								}
								if($field['minlength'] && in_array($field['type'],array('text','email','search','password','tel'))){
									$out.=' minlength="'.$field['minlength'].'"';
								}
								$out.=' name="'.$field['name'].'"';
								if($field['placeholder']){
									$out.=' placeholder="'.$field['placeholder'].'"';
								}
								if($field['readonly'] && !in_array($field['type'],array('color','file','range'))){
									$out.=' readonly';
								}
								if($field['required']==1){
									$out.=' required';
								}
								$out.=' rows="'.($field['rows']?$field['rows']:5).'"';
								if($field['selectionend']){
									$out.=' selectionEnd';
								}
								if($field['selectionstart']){
									$out.=' selectionStart';
								}
								if($field['spellcheck']){
									$out.=' spellcheck="'.$field['spellcheck'].'"';
								}
								$out.='>';
								if($field['value']){
									$out.=$field['value'];
								}
							$out.='</textarea>';
							if($this->data['orientation']=='horizontal'){
								$out.='</div>';
							}
							if($field['note']){
								$out.='<small class="';
								if($this->data['orientation']=='horizontal'){
									$out.='ml-auto col-sm-'.$this->value_width.' ';
								}
								$out.='form-text">'.$field['note'].'</small>';
							}
						$out.='</div> ';
						break;
				}
			}
			$out.='<input class="form_name" name="form_name" type="hidden" value="'.$this->data['name'].'">
			</form>';
			if($return){
				return $out;
			}else{
				echo $out;
			}
		}
	}
	# Get from name
	public function form_name(){
		return $this->data['name']."_";
	}
	# Unprepend form name
	public function unname(array $data){
		foreach($data as $key=>$value){
			if(strpos($key,$this->form_name())===0){
				$data[substr($key,strlen($this->form_name()))]=$value;
				unset($data[$key]);
			}else{
				$data[$key]=$value;
			}
		}
		return $data;
	}
	# Redirect after processing
	public function redirect($status=true,$args=NULL){
		# If there's any errors (status=false), capture submitted data and save it to $_SESSION['form_data'], redirect then in __construct, populate data and empty the session
		if($status==false && $args!==NULL){
			if($args['data']){
				$args=$args['data'];
			}
			$_SESSION['form_data'][$this->data['name']]=$args;
		}
		header('Location: '.$_SERVER['REQUEST_URI']);
		exit;
	}
	# Add colour select
	public function colour_select($name,$value=NULL,$label=NULL,$rgba=NULL){
		global $bootstrap;
		if($bootstrap){
			$this->add_select(
				array(
					'class'	=>'colour_select" style="border-left:4px solid rgba('.$rgba.')',
					'label'	=>$label,
					'name'	=>$name,
					'note'	=>'Set to <strong>Automatic</strong> to make this colour available elsewhere.',
					'value'	=>$value,
				),
				array_combine(array_keys((array) $bootstrap->colours->colours),array_map('ucwords',array_keys((array) $bootstrap->colours->colours))),
				'Automatic'
			);
		}
	}
	# Find value for re-populating data based on field type
	public function find_value($name,$data){
		if(is_array($data) && !is_array($name) && array_key_exists($name,$data)){
			return $data[$name];
		}elseif(is_array($name)){
			$key=key($name);
			if(!is_array($data[$key])){
				return $data[$key];
			}
			return $this->find_value($name[$key],$data[$key]);
		}elseif($name && $data){
			parse_str($name,$name);
			$key=key($name);
			return $this->find_value($name[$key],$data[$key]);
		}
	}
}