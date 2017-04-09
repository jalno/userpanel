import * as $ from 'jquery';
import {Main} from '../classes/Main';
import {Login} from '../classes/Login';
$(function(){
	Main.init();
	Login.initIfNeeded();
});