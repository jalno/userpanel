import * as $ from 'jquery';
import {Main} from '../classes/Main';
import {Login} from '../classes/Login';
import {Register} from '../classes/Register';
$(function(){
	Main.init();
	Login.initIfNeeded();
	Register.initIfNeeded();
});