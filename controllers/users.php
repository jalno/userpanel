<?php
namespace packages\userpanel\controllers;
use \packages\base;
use \packages\base\http;
use \packages\base\db;
use \packages\base\inputValidation;
use \packages\base\db\duplicateRecord;
use \packages\base\db\InputDataType;
use \packages\base\db\parenthesis;
use \packages\base\views\FormError;
use \packages\base\image;
use \packages\base\IO\file;
use \packages\base\packages;

use \packages\userpanel;
use \packages\userpanel\user;
use \packages\userpanel\user\socialnetwork;
use \packages\userpanel\usertype;
use \packages\userpanel\authorization;
use \packages\userpanel\authentication;
use \packages\userpanel\controller;
use \packages\userpanel\view;
use \packages\userpanel\country;
use \packages\userpanel\log;


class users extends controller{
	protected $authentication = true;
	public function index(){
		authorization::haveOrFail('users_list');
		$view = view::byName("\\packages\\userpanel\\views\\users\\listview");
		$types = authorization::childrenTypes();

		$user = new user();
		if($types){
			$user->where("type", $types, 'in');
		}else{
			$user->where("id", authentication::getID());
		}
		$inputsRules = array(
			'id' => array(
				'type' => 'number',
				'optional' => true,
				'empty' => true,
			),
			'name' => array(
				'type' => 'string',
				'optional' => true,
				'empty' => true,
			),
			'lastname' => array(
				'type' => 'string',
				'optional' => true,
				'empty' => true,
			),
			'email' => array(
				'type' => 'string',
				'optional' => true,
				'empty' => true,
			),
			'cellphone' => array(
				'type' => 'string',
				'optional' => true,
				'empty' => true,
			),
			'type' => array(
				'type' => 'number',
				'optional' => true,
				'empty' => true
			),
			'status' => array(
				'type' => 'number',
				'optional' => true,
				'empty' => true
			),
			'word' => array(
				'type' => 'string',
				'optional' => true,
				'empty' => true
			),
			'comparison' => array(
				'values' => array('equals', 'startswith', 'contains'),
				'default' => 'contains',
				'optional' => true
			)
		);
		try{
			$inputs = $this->checkinputs($inputsRules);
			if(isset($inputs['type']) and $inputs['type']){
				if(!in_array($inputs['type'], $types)){
					throw new inputValidation("type");
				}
			}
			if(isset($inputs['status']) and $inputs['status'] !== ''){
				if(!in_array($inputs['status'], array(user::active, user::deactive, user::suspend))){
					throw new inputValidation("status");
				}
			}
			foreach(array('id', 'name', 'lastname', 'type', 'email', 'cellphone', 'status') as $item){
				if(isset($inputs[$item]) and $inputs[$item]){
					$comparison = $inputs['comparison'];
					if(in_array($item, array('type', 'status'))){
						$comparison = 'equals';
					}
					$user->where($item, $inputs[$item], $comparison);
				}
			}
			if(isset($inputs['word']) and $inputs['word']){
				$parenthesis = new parenthesis();
				foreach(array('name', 'lastname', 'email', 'cellphone') as $item){
					if(!isset($inputs[$item]) or !$inputs[$item]){
						$parenthesis->where($item,$inputs['word'], $inputs['comparison'], 'OR');
					}
				}
				$user->where($parenthesis);
			}
		}catch(inputValidation $error){
			$view->setFormError(FormError::fromException($error));
		}
		$view->setDataForm($this->inputsvalue($inputs));
		$user->pageLimit = $this->items_per_page;
		$users = $user->paginate($this->page);
		$this->total_pages = $user->totalPages;

		$view->setDataList($users);
		$view->setPaginate($this->page, $user->totalCount, $this->items_per_page);
		$view->setUserTypes(usertype::where("id", $types, 'in')->get());

		$this->response->setStatus(true);
		$this->response->setView($view);
		return $this->response;
	}
	public function add($data){
		authorization::haveOrFail('users_add');
		$view = view::byName("\\packages\\userpanel\\views\\users\\add");
		$types = authorization::childrenTypes();
		$view->setCountries(country::get());
		$view->setTypes(usertype::where("id", $types, 'in')->get());
		if(http::is_post()){
			$inputs = array(
				'name' => array(
					'type' => 'string'
				),
				'lastname' => array(
					'type' => 'string',
					'optional' => true,
					'empty' => true
				),
				'email' => array(
					'type' => 'email',
				),
				'cellphone' => array(
					'type' => 'cellphone',
				),
				'password' => array(),
				'type' => array(
					'type' => 'number'
				),
				'zip' => array(
					'optional' => true,
					'type' => 'number',
					'empty' => true
				),
				'city' => array(
					'optional' => true,
					'type' => 'string',
					'empty' => true
				),
				'country' => array(
					'optional' => true,
					'type' => 'number',
					'empty' => true
				),
				'address' => array(
					'optional' => true,
					'type' => 'string',
					'empty' => true
				),
				'phone' => array(
					'optional' => true,
					'type' => 'string',
					'empty' => true
				),
				'credit' => array(
					'optional' => true,
					'type' => 'number',
					'empty' => true
				),
				'status' => array(
					'type' => 'number',
					'values' => array(user::active, user::deactive,user::suspend)
				),
				'socialnets' => array(
					'optional' => true
				),
				'visibility_email' => array(
					'optional' => true,
					'type' => 'bool',
					'empty' => true
				),
				'visibility_cellphone' => array(
					'optional' => true,
					'type' => 'bool',
					'empty' => true
				),
				'visibility_phone' => array(
					'optional' => true,
					'type' => 'bool',
					'empty' => true
				),
				'visibility_socialnetworks_'.socialnetwork::twitter => array(
					'optional' => true,
					'type' => 'bool',
					'empty' => true
				),
				'visibility_socialnetworks_'.socialnetwork::twitter => array(
					'optional' => true,
					'type' => 'bool',
					'empty' => true
				),
				'visibility_socialnetworks_'.socialnetwork::facebook => array(
					'optional' => true,
					'type' => 'bool',
					'empty' => true
				),
				'visibility_socialnetworks_'.socialnetwork::skype => array(
					'optional' => true,
					'type' => 'bool',
					'empty' => true
				),
				'visibility_socialnetworks_'.socialnetwork::gplus => array(
					'optional' => true,
					'type' => 'bool',
					'empty' => true
				),
				'visibility_socialnetworks_'.socialnetwork::instagram => array(
					'optional' => true,
					'type' => 'bool',
					'empty' => true
				),
				'visibility_socialnetworks_'.socialnetwork::telegram => array(
					'optional' => true,
					'type' => 'bool',
					'empty' => true
				)
			);
			$this->response->setStatus(false);
			try{
				$formdata = $this->checkinputs($inputs);
				if(!usertype::byId($formdata['type'])){
					throw new inputValidation("type");
				}
				if(isset($formdata['country'])){
					if(!country::byId($formdata['country'])){
						throw new inputValidation("country");
					}
				}

				if(isset($formdata['socialnets'])){
					foreach($formdata['socialnets'] as $network => $url){
						switch($network){
							case(socialnetwork::telegram):
								$regex = '/^(https?:\\/\\/(t(elegram)?\\.me|telegram\\.org)\\/)?(?<username>[a-z0-9\\_]{5,32})\\/?$/i';
								break;
							case(socialnetwork::instagram):
								$regex = '/^(https?:\/\/(www\.)?instagram\.com\/)?(?<username>[A-Za-z0-9_](?:(?:[A-Za-z0-9_]|(?:\.(?!\.))){0,28}(?:[A-Za-z0-9_]))?)$/i';
								break;
							case(socialnetwork::skype):
								$regex = '/^(?:(?:callto|skype):)?(?<username>(?:[a-z][a-z0-9\\.,\\-_]{5,31}))(?:\\?(?:add|call|chat|sendfile|userinfo))?$/i';
								$site = "skype:";
								break;
							case(socialnetwork::twitter):
								$regex = '/^(?:https?:\/\/(?:.*\.)?twitter\.com\/)?(?<username>[A-z 0-9 _]+)\/?$/i';
								break;
							case(socialnetwork::facebook):
								$regex = '/^(?:https?:\/\/(?:www\.)?(?:facebook|fb)\.com\/)?(?<username>[A-z 0-9 _ - \.]+)\/?$/i';
								break;
							case(socialnetwork::gplus):
								$regex = '/^(?:https?:\/\/plus\.google\.com\/)?(?<username>(?:\+[a-z0-9\_]+|\d{21}))\/?$/i';
								break;
							default:
								throw new inputValidation("socialnets[{$network}]");
						}
						if($url){
							if(preg_match($regex, $url, $matches)){
								$formdata['socialnets'][$network] = $matches['username'];
							}else{
								throw new inputValidation("socialnets[{$network}]");
							}
						}
					}
				}

				$user = new user($formdata);
				$user->password_hash($formdata['password']);
				unset($formdata['password']);
				$user->save();

				if(isset($formdata['socialnets'])){
					foreach($formdata['socialnets'] as $network => $username){
						if($username){
							$socialnet = new socialnetwork();
							$socialnet->user = $user->id;
							$socialnet->network = $network;
							$socialnet->username = $username;
							$socialnet->save();
						}
					}
				}
				if(authorization::is_accessed('profile_edit_privacy')){
					$visibilities = array();
					foreach(array(
						'email',
						'cellphone',
						'phone',
						'socialnetworks_'.socialnetwork::telegram,
						'socialnetworks_'.socialnetwork::instagram,
						'socialnetworks_'.socialnetwork::skype,
						'socialnetworks_'.socialnetwork::twitter,
						'socialnetworks_'.socialnetwork::facebook,
						'socialnetworks_'.socialnetwork::gplus,
					) as $field){
						if(array_key_exists('visibility_'.$field, $formdata)){
							if($formdata['visibility_'.$field]){
								$visibilities[] = $field;
							}
						}
					}
					$user->setOption("visibilities", $visibilities);
				}

				$log = new log();
				$log->type = log::user_edit;
				$log->users = array_unique(array($user->id, authentication::getID()));
				$log->params = array(
					'user' => $user->id,
					'inputs' => $formdata
				);
				$log->save();
				$this->response->setStatus(true);
				$this->response->go(userpanel\url('users/edit/'.$user->id));
			}catch(inputValidation $error){
				$view->setFormError(FormError::fromException($error));
			}catch(InputDataType $error){
				$view->setFormError(FormError::fromException($error));
			}catch(duplicateRecord $error){
				$view->setFormError(FormError::fromException($error));
			}
			$view->setDataForm($this->inputsvalue($inputs));
		}else{
			$this->response->setStatus(true);
			$view->setDataForm(105,'country');
		}
		$this->response->setView($view);
		return $this->response;
	}
	public function view($data){
		authorization::haveOrFail('users_view');
		$user = user::with('type')->with('socialnetworks')->byId($data['user']);
		if(!$user){
			throw new NotFound;
		}
		$view = view::byName("\\packages\\userpanel\\views\\users\\view");
		$view->setUserData($user);
		$this->response->setStatus(true);
		$this->response->setView($view);
		return $this->response;
	}
	public function edit($data){
		authorization::haveOrFail('users_edit');
		$types = authorization::childrenTypes();
		$user = user::where("id", $data['user'])->where("type", $types, 'in')->getOne();
		if(!$user->id){
			throw new NotFound();
		}
		$view = view::byName("\\packages\\userpanel\\views\\users\\edit");
		$view->setTypes(usertype::where("id", $types, 'in')->get());
		$view->setCountries(country::get());
		$view->setUserData($user);
		if(http::is_post()){
			$inputs = array(
				'name' => array(
					'optional' => true,
					'type' => 'string'
				),
				'lastname' => array(
					'optional' => true,
					'type' => 'string'
				),
				'email' => array(
					'type' => 'email',
					'optional' => true,
				),
				'cellphone' => array(
					'type' => 'cellphone',
					'optional' => true,
				),
				'password' => array(
					'optional' => true,
					'empty' => true
				),
				'type' => array(
					'optional' => true,
					'type' => 'number'
				),
				'zip' => array(
					'optional' => true,
					'type' => 'number'
				),
				'city' => array(
					'optional' => true,
					'type' => 'string'
				),
				'country' => array(
					'optional' => true,
					'type' => 'string'
				),
				'address' => array(
					'optional' => true,
					'type' => 'string'
				),
				'phone' => array(
					'optional' => true,
					'type' => 'string',
					'empty' => true
				),
				'credit' => array(
					'optional' => true,
					'type' => 'number',
					'empty' => true
				),
				'status' => array(
					'optional' => true,
					'type' => 'number',
					'values' => array(user::active, user::deactive,user::suspend)
				),
				'avatar' => array(
					'optional' => true,
					'type' => 'file'
				),
				'socialnets' => array(
					'optional' => true
				),
				'visibility_email' => array(
					'optional' => true,
					'type' => 'bool',
					'empty' => true
				),
				'visibility_cellphone' => array(
					'optional' => true,
					'type' => 'bool',
					'empty' => true
				),
				'visibility_phone' => array(
					'optional' => true,
					'type' => 'bool',
					'empty' => true
				),
				'visibility_socialnetworks_'.socialnetwork::twitter => array(
					'optional' => true,
					'type' => 'bool',
					'empty' => true
				),
				'visibility_socialnetworks_'.socialnetwork::twitter => array(
					'optional' => true,
					'type' => 'bool',
					'empty' => true
				),
				'visibility_socialnetworks_'.socialnetwork::facebook => array(
					'optional' => true,
					'type' => 'bool',
					'empty' => true
				),
				'visibility_socialnetworks_'.socialnetwork::skype => array(
					'optional' => true,
					'type' => 'bool',
					'empty' => true
				),
				'visibility_socialnetworks_'.socialnetwork::gplus => array(
					'optional' => true,
					'type' => 'bool',
					'empty' => true
				),
				'visibility_socialnetworks_'.socialnetwork::instagram => array(
					'optional' => true,
					'type' => 'bool',
					'empty' => true
				),
				'visibility_socialnetworks_'.socialnetwork::telegram => array(
					'optional' => true,
					'type' => 'bool',
					'empty' => true
				),
				'avatar_remove' => [
					'type' => 'bool',
					'optional' => true
				]
			);
			$this->response->setStatus(false);
			try{
				$formdata = $this->checkinputs($inputs);
				if(isset($formdata['type']) and !usertype::byId($formdata['type'])){
					throw new inputValidation("type");
				}
				if(isset($formdata['country'])){
					if(!country::byId($formdata['country'])){
						throw new inputValidation("country");
					}
				}
				if(isset($formdata['socialnets'])){
					foreach($formdata['socialnets'] as $network => $url){
						switch($network){
							case(socialnetwork::telegram):
								$regex = '/^(https?:\\/\\/(t(elegram)?\\.me|telegram\\.org)\\/)?(?<username>[a-z0-9\\_]{5,32})\\/?$/i';
								break;
							case(socialnetwork::instagram):
								$regex = '/^(https?:\/\/(www\.)?instagram\.com\/)?(?<username>[A-Za-z0-9_](?:(?:[A-Za-z0-9_]|(?:\.(?!\.))){0,28}(?:[A-Za-z0-9_]))?)$/i';
								break;
							case(socialnetwork::skype):
								$regex = '/^(?:(?:callto|skype):)?(?<username>(?:[a-z][a-z0-9\\.,\\-_]{5,31}))(?:\\?(?:add|call|chat|sendfile|userinfo))?$/i';
								$site = "skype:";
								break;
							case(socialnetwork::twitter):
								$regex = '/^(?:https?:\/\/(?:.*\.)?twitter\.com\/)?(?<username>[A-z 0-9 _]+)\/?$/i';
								break;
							case(socialnetwork::facebook):
								$regex = '/^(?:https?:\/\/(?:www\.)?(?:facebook|fb)\.com\/)?(?<username>[A-z 0-9 _ - \.]+)\/?$/i';
								break;
							case(socialnetwork::gplus):
								$regex = '/^(?:https?:\/\/plus\.google\.com\/)?(?<username>(?:\+[a-z0-9\_]+|\d{21}))\/?$/i';
								break;
							default:
								throw new inputValidation("socialnets[{$network}]");
						}
						if($url){
							if(preg_match($regex, $url, $matches)){
								$formdata['socialnets'][$network] = $matches['username'];
							}else{
								throw new inputValidation("socialnets[{$network}]");
							}
						}
					}
				}

				if(isset($formdata['avatar'])){
					if($formdata['avatar']['error'] == 0){
						$type = getimagesize($formdata["avatar"]['tmp_name']);
						if(!in_array($type[2], array(IMAGETYPE_JPEG ,IMAGETYPE_GIF, IMAGETYPE_PNG))){
							throw new inputValidation("avatar");
						}
					}elseif($formdata['avatar']['error'] == 4){
						unset($formdata['avatar']);
					}elseif(isset($formdata['avatar']['error'])){
						throw new inputValidation("avatar");
					}
				}
				if(isset($formdata['avatar'])){
					$file = new file\local($formdata['avatar']['tmp_name']);
					$tmpfile = new file\tmp();
					$type = getimagesize($file->getPath());
					switch($type[2]){
						case(IMAGETYPE_JPEG):
							$image = new image\jpeg($file);
							$type_name = 'jpg';
							break;
						case(IMAGETYPE_GIF):
							$image = new image\gif($file);
							$type_name = 'gif';
							break;
						case(IMAGETYPE_PNG):
							$image = new image\png($file);
							$type_name = 'png';
							break;
					}
					$image->resize(200, 200)->saveToFile($tmpfile);
					$formdata['avatar'] = 'sotrage/public_avatar/'.$tmpfile->md5().'.'.$type_name;
					$avatar = new file\local(packages::package('userpanel')->getFilePath($formdata['avatar']));
					$avatar->getDirectory()->make(true);
					$tmpfile->copyTo($avatar);
				}
				if(!isset($formdata['avatar']) or !is_string($formdata['avatar'])){
					unset($formdata['avatar']);
				}
				if(isset($formdata['avatar_remove']) and $formdata['avatar_remove']){
					$formdata['avatar'] = null;
				}
				if(isset($formdata['password']) and $formdata['password']){
					$user->password_hash($formdata['password']);
				}
				unset($formdata['password']);
				$user->save($formdata);
				unset($formdata['avatar']);
				if(isset($formdata['socialnets'])){
					foreach($formdata['socialnets'] as $network => $username){
						if($username){
							$edited = false;
							foreach($user->socialnetworks as $socialnet){
								if($socialnet->network == $network){
									$edited = true;
									$socialnet->username = $username;
									$socialnet->save();
									break;
								}
							}
							if(!$edited){
								$socialnet = new socialnetwork();
								$socialnet->user = $user->id;
								$socialnet->network = $network;
								$socialnet->username = $username;
								$socialnet->save();
							}
						}else{
							foreach($user->socialnetworks as $socialnet){
								if($socialnet->network == $network){
									$socialnet->delete();
									break;
								}
							}
						}
					}
				}
				if(authorization::is_accessed('profile_edit_privacy')){
					$visibilities = $user->getOption("visibilities");
					if(!is_array($visibilities)){
						$visibilities = array();
					}
					foreach(array(
						'email',
						'cellphone',
						'phone',
						'socialnetworks_'.socialnetwork::telegram,
						'socialnetworks_'.socialnetwork::instagram,
						'socialnetworks_'.socialnetwork::skype,
						'socialnetworks_'.socialnetwork::twitter,
						'socialnetworks_'.socialnetwork::facebook,
						'socialnetworks_'.socialnetwork::gplus,
					) as $field){
						if(array_key_exists('visibility_'.$field, $formdata)){
							if($formdata['visibility_'.$field]){
								$visibilities[] = $field;
							}elseif(($key = array_search($field, $visibilities)) !== false){
								unset($visibilities[$key]);
							}
						}
					}
					$visibilities = array_values(array_unique($visibilities));
					$user->setOption("visibilities", $visibilities);
				}
				$log = new log();
				$log->type = log::user_edit;
				$log->users = array_unique(array($user->id, authentication::getID()));
				$log->params = array(
					'user' => $user->id,
					'inputs' => $formdata
				);
				$log->save();
				$this->response->setStatus(true);
				$view->setDataForm($user->toArray());
			}catch(inputValidation $error){
				print_r($error);
				$view->setFormError(FormError::fromException($error));
				$view->setDataForm($this->inputsvalue($inputs));
			}catch(InputDataType $error){
				$view->setFormError(FormError::fromException($error));
				$view->setDataForm($this->inputsvalue($inputs));
			}catch(duplicateRecord $error){
				$view->setFormError(FormError::fromException($error));
				$view->setDataForm($this->inputsvalue($inputs));
			}

		}else{
			$this->response->setStatus(true);
		}
		$view->setForm();
		$this->response->setView($view);
		return $this->response;
	}
	public function delete($data){
		authorization::haveOrFail('users_delete');
		$types = authorization::childrenTypes();
		$user = user::where("id", $data['user'])->where("type", $types, 'in')->getOne();
		if(!$user){
			throw new NotFound;
		}
		$view = view::byName("\\packages\\userpanel\\views\\users\\delete");
		if(http::is_post()){
			$user->delete();
			$log = new log();
			$log->type = log::user_delete;
			$log->users = array_unique(array($user->id, authentication::getID()));
			$log->params = array(
				'user' => $user->id,
			);
			$log->save();
			$this->response->setStatus(true);
			$this->response->go(userpanel\url('users'));
		}else{
			$this->response->setStatus(true);
			$view->setDataForm($user->toArray());
			$this->response->setView($view);
		}
		return $this->response;
	}
}
