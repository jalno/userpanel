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
use \packages\base\IO;
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


class profile extends controller{
	protected $authentication = true;
	public function view($data){
		authorization::haveOrFail('profile_view');
		$view = view::byName("\\packages\\userpanel\\views\\profile\\view");
		$view->setUserData(authentication::getUser());
		$this->response->setStatus(true);
		$this->response->setView($view);
		return $this->response;
	}
	public function edit($data){
		authorization::haveOrFail('profile_edit');
		$types = authorization::childrenTypes();
		$user = authentication::getUser();
		$view = view::byName("\\packages\\userpanel\\views\\profile\\edit");
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
				'password' => array(
					'optional' => true,
					'empty' => true
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
				)
			);
			$this->response->setStatus(false);
			try{
				$formdata = $this->checkinputs($inputs);
				if(isset($formdata['country'])){
					if(!country::byId($formdata['country'])){
						throw new inputValidation("country");
					}
				}
				if(isset($formdata['zip'])){
					if(!preg_match("/^(\\d{5}|\\d{10})$/", $formdata['zip'])){
						throw new inputValidation("zip");
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
						$type = image::getType($formdata['avatar']['tmp_name']);
						if(in_array($type, array(IMAGETYPE_JPEG ,IMAGETYPE_GIF, IMAGETYPE_PNG))){
							$directory = packages::package('userpanel')->getFilePath('storage/public/avatars');
							if(!is_dir($directory)){
								IO\mkdir($directory, true);
							}

							$image = new image($formdata['avatar']['tmp_name']);
							$tmpfile = $directory."/rand".((time() + rand(0, 10000)) * rand(0, 100)  / 100);
							$image->resize(200,200);
							$image->save($tmpfile, $type);
							$name = md5_file($tmpfile);
							if($type == IMAGETYPE_JPEG){
								$type_name = '.jpg';
							}elseif($type == IMAGETYPE_GIF){
								$type_name = '.gif';
							}elseif($type == IMAGETYPE_PNG){
								$type_name = '.png';
							}

							if(rename($tmpfile, $directory.'/'.$name.$type_name)){
								$formdata['avatar'] = "storage/public/avatars/".$name.$type_name;
							}else{
								throw new inputValidation("avatar");
							}
						}else{
							throw new inputValidation("avatar");
						}
					}elseif(isset($formdata['avatar']['error']) and $formdata['avatar']['error'] != 4){
						throw new inputValidation("avatar");
					}
				}

				if(isset($formdata['password']) and $formdata['password']){
					$user->password_hash($formdata['password']);
				}
				unset($formdata['password']);
				if(isset($formdata['avatar']) and !is_string($formdata['avatar'])){
					unset($formdata['avatar']);
				}
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
				$log->users = array(authentication::getID());
				$log->params = array(
					'user' => $user->id,
					'inputs' => $formdata
				);
				$log->save();
				$this->response->setStatus(true);
				$view->setDataForm($user->toArray());
			}catch(inputValidation $error){
				$view->setFormError(FormError::fromException($error));
				$view->setDataForm($this->inputsvalue($inputs));
			}

		}else{
			$this->response->setStatus(true);
			$view->setForm();
		}
		$this->response->setView($view);
		return $this->response;
	}
}
