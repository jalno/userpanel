<?php
namespace packages\userpanel\controllers;
use \packages\base;
use \packages\base\{http, translator, db, inputValidation, db\duplicateRecord, db\InputDataType, db\parenthesis, views\FormError, image, IO\file, packages};

use \packages\userpanel;
use \packages\userpanel\{user, logs, user\socialnetwork, usertype, authorization, authentication, controller, view, country, log, events\settings as settingsEvent};

class profile extends controller{
	protected $authentication = true;
	public function view($data){
		authorization::haveOrFail('profile_view');
		$view = view::byName("\\packages\\userpanel\\views\\profile\\view");
		$user = authentication::getUser();
		$settingsEvent = new settingsEvent();
		$settingsEvent->setUser($user);
		$settingsEvent->trigger();
		$view->setSettings($settingsEvent->get());
		$view->setUserData($user);
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
		$settingsEvent = new settingsEvent();
		$settingsEvent->setUser($user);
		$settingsEvent->trigger();
		$view->setSettings($settingsEvent->get());
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
				),
				'avatar_remove' => [
					'type' => 'bool',
					'optional' => true
				]
			);
			$this->response->setStatus(false);
			try{
				$logsfeilds = [
					'name', 
					'lastname',
					'password',
					'zip',
					'city',
					'country',
					'address',
					'phone',
					'avatar',
				];

				$old = [];
				foreach($logsfeilds as $field){
					$old[$field] = $user->original_data[$field];
				}
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
				$inputs = [
					'oldData' => [],
					'newData' => []
				];
				if(authorization::is_accessed('profile_edit_privacy')){
					$visibilities = $user->getOption("visibilities");
					if(!is_array($visibilities)){
						$visibilities = array();
					}
					$inputs['oldData']['visibilities'] = $visibilities;
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
					$inputs['newData']['visibilities'] = $visibilities;
				}
				foreach($old as $field => $val){
					if($val != $user->original_data[$field]){
						$inputs['oldData'][$field] = $val;
						$inputs['newData'][$field] = $user->$field;
					}
				}
				if(isset($inputs['oldData']['password'])){
					$inputs['oldData']['password'] = "***";
				}
				if(isset($inputs['newData']['password'])){
					$inputs['newData']['password'] = "***";
				}
				$log = new log();
				$log->title = translator::trans("log.profileEdit");
				$log->type = logs\userEdit::class;
				$log->user = $user->id;
				$log->parameters = $inputs;
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
	public function settings($data){
		authorization::haveOrFail('profile_settings');
		$settingsEvent = new settingsEvent();
		$user = authentication::getUser();
		$settingsEvent->setUser($user);
		$settingsEvent->trigger();
		if(!$settingsEvent->get()){
			throw new base\NotFound();
		}
		$view = view::byName("\\packages\\userpanel\\views\\profile\\settings");
		$view->setUser($user);
		$view->setSettings($settingsEvent->get());
		$this->response->setStatus(true);
		$this->response->setView($view);
		return $this->response;
	}
	public function change($data){
		authorization::haveOrFail('profile_settings');
		$settingsEvent = new settingsEvent();
		$user = authentication::getUser();
		$settingsEvent->setUser($user);
		$settingsEvent->trigger();
		$settings = $settingsEvent->get();
		if(!$settings){
			throw new base\NotFound();
		}
		$this->response->setView($view = view::byName("\\packages\\userpanel\\views\\profile\\settings"));
		$view->setUser($user);
		$view->setSettings($settings);
		$this->response->setStatus(true);
		$inputsRules = [];
		$logs = array();
		foreach ($settings as $setting) {
			if ($SRules = $setting->getInputs()) {
				$SRules = $inputsRules = array_merge($inputsRules, $SRules);
				$ginputs = $this->checkinputs($SRules);
				$logs = array_merge($logs, $setting->store($ginputs, $user));
			}
		}
		$view->setDataForm($this->inputsvalue($inputsRules));
		if ($logs) {
			$inputs = array(
				"oldData" => array(),
				"newData" => array(),
			);
			foreach ($logs as $log) {
				$inputs["oldData"][$log->getName()] = array("title" => $log->getTitle(), "value" => $log->getOldValue());
				$inputs["newData"][$log->getName()] = array("title" => $log->getTitle(), "value" => $log->getNewValue());
			}
			$log = new log();
			$log->title = translator::trans("log.profileEdit");
			$log->type = logs\userEdit::class;
			$log->user = $user->id;
			$log->parameters = $inputs;
			$log->save();
		}
		return $this->response;
	}
}
