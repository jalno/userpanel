import "@jalno/translator";
import * as $ from "jquery";
import "bootstrap";
import "jquery-bootstrap-checkbox";
import "jquery-validation";
import "jschr-bootstrap-modal/js/bootstrap-modal.js";
import "jschr-bootstrap-modal/js/bootstrap-modalmanager.js";
import "select2";
import "jquery-mousewheel";
import "malihu-custom-scrollbar-plugin";
import { AjaxRequest } from "webuilder";

export class Main{
	private static isIE8 = false;
	private static isIE9 = false;
	private static $windowWidth;
	private static $windowHeight;
	private static $pageArea;
	private static runInit():void{
		if (/MSIE (\d+\.\d+);/.test(navigator.userAgent)) {
            var ieversion = new Number(RegExp.$1);
            if (ieversion == 8) {
                Main.isIE8 = true;
            } else if (ieversion == 9) {
                Main.isIE9 = true;
            }
        }
	}
	private static runElementsPosition(): void{
        Main.$windowWidth = $(window).width();
        Main.$windowHeight = $(window).height();
        Main.$pageArea = Main.$windowHeight - $('body > .navbar').outerHeight() - $('body > .footer').outerHeight();
        $('.sidebar-search input').removeAttr('style').removeClass('open');
        Main.runContainerHeight();
    }
	private static runContainerHeight(): void{
        let mainContainer = $('.main-content > .container');
        let mainNavigation = $('.main-navigation');
        if (Main.$pageArea < 760) {
            Main.$pageArea = 760;
        }
        if (Main.$windowWidth >= 768) {
            if (mainContainer.outerHeight() < mainNavigation.outerHeight() && mainNavigation.outerHeight() > Main.$pageArea) {
                mainContainer.css('min-height', mainNavigation.outerHeight());
            } else {
                mainContainer.css('min-height', Main.$pageArea);
            }
        }
    }
	private static runTooltips(): void{
		let $tooltips = $(".tooltips");
        if ($tooltips.length) {
            $tooltips.tooltip();
        }
    }
	private static runPopovers(): void{
		let $popovers = $(".popovers");
        if ($popovers.length) {
            $popovers.popover();
        }
    }
	private static runShowTab(): void{
		let showTabs = $(".show-tab");
        if (showTabs.length) {
            showTabs.bind('click', function (e) {
                e.preventDefault();
                var tabToShow = $(this).attr("href");
                if ($(tabToShow).length) {
                    $('a[href="' + tabToShow + '"]').tab('show');
                }
            });
			let parameter = Main.getParameterByName('tabId');
			if(parameter.length > 0){
				$(`a[href="#${parameter}"]`).tab('show');
			}
        }
    }
	private static getParameterByName(name: string):string{
        name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
        let regex = new RegExp("[\\?&]" + name + "=([^&#]*)");
		let results = regex.exec(location.search);
        return results != null ? decodeURIComponent(results[1].replace(/\+/g, " ")) : "";
    }
	private static runCustomCheck () {
		$('.checkbox label input[type=checkbox], .checkbox-inline input[type=checkbox], .radio label input[type=radio], .radio-inline input[type=radio]').bootstrapCheckbox();
    }
	private static runSearchInput(): void{
        let search_form = $('.sidebar-search');
        let search_input = $('.sidebar-search input', search_form);
        let search_button = $('.sidebar-search button', search_form);
        search_input.data('default', $(search_input).outerWidth());
		search_input.on('focus', function () {
            $(this).animate({
                width: 200
            }, 200);
        });
		search_input.on('blur', function () {
            if ($(this).val() == "") {
                if ($(this).hasClass('open')) {
                    $(this).animate({
                        width: 0,
                        opacity: 0
                    }, 200, function () {
                        $(this).hide();
                    });
                } else {
                    $(this).animate({
                        width: $(this).data('default')
                    }, 200);
                }
            }
        });
        search_button.on('click', function () {
            if (search_input.is(':hidden')) {
                search_input.addClass('open').css({
                    width: 0,
                    opacity: 0
                }).show().animate({
                    width: 200,
                    opacity: 1
                }, 200).focus();
            } else if (search_input.hasClass('open') && search_input.val() == '') {
                search_input.removeClass('open').animate({
                    width: 0,
                    opacity: 0
                }, 200, function () {
                    $(this).hide();
                });
            } else if (search_input.val() == '') {
				search_input.focus();
            }
        });
    }
	public static importValidationTranslator() {
        if (Translator.getDefaultShortLang() !== "en") {
            require(`jquery-validation/dist/localization/messages_${Translator.getDefaultShortLang()}.js`)
        }
    }
	public static SetDefaultValidation(): void{
		if($.hasOwnProperty('validator')){
	        $.validator.setDefaults({
	            errorElement: "span",
	            errorClass: 'help-block',
	            errorPlacement: function (error, element) {
	                if (element.attr("type") == "radio" || element.attr("type") == "checkbox") {
	                    error.insertAfter($(element).closest('.form-group').children('div').children().last());
	                } else if (element.attr("name") == "card_expiry_mm" || element.attr("name") == "card_expiry_yyyy") {
	                    error.appendTo($(element).closest('.form-group').children('div'));
	                } else if(element.parent().hasClass('input-group')){
						error.insertAfter($(element).parent());
					} else {
	                    error.insertAfter(element);
	                }
	            },
	            ignore: ':hidden',
	            highlight: function (element) {

	                $(element).closest('.help-block').removeClass('valid');
	                $(element).closest('.form-group').removeClass('has-success').addClass('has-error').find('.symbol').removeClass('ok').addClass('required');
	            },
	            unhighlight: function (element) {
	                $(element).closest('.form-group').removeClass('has-error');
	            },
	            success: function (label, element) {
	                label.addClass('help-block valid');
	                $(element).closest('.form-group').removeClass('has-error');
	            }
            });
            Main.importValidationTranslator();
		}
	}
	
	private static runPagination(): void{
		$('select.paginate').change(function(){
			window.location.href = $('option:selected', this).data('url');
		})
	}
	private static runSelect2(): void{
		let $elements = $('select.select2');
		if($elements.length){
			$elements.select2();
		}
	}
	private static runNavigationToggler(): void{
        $('.navigation-toggler').on('click', function () {
            if (!$('body').hasClass('navigation-small')) {
                $('body').addClass('navigation-small');
            } else {
                $('body').removeClass('navigation-small');
            }
        });
    }
	private static runModuleTools(): void {
        $('.panel-tools .panel-collapse').on('click', function (e) {
            e.preventDefault();
            let el = $(this).parent().closest(".panel").children(".panel-body");
            if ($(this).hasClass("collapses")) {
                $(this).addClass("expand").removeClass("collapses");
                el.slideUp(200);
            } else {
                $(this).addClass("collapses").removeClass("expand");
                el.slideDown(200);
            }
        });
    }
    private static runNavigationMenu(): void{
		let navigation_small = $('body').hasClass('navigation-small');

        $('.main-navigation-menu li.active').addClass('open');
        $('.main-navigation-menu > li a').on('click', function (e) {
			let $parent = $(this).parent();
			let $grandparent = $parent.parent();

            if ($parent.children('ul').hasClass('sub-menu') && ((!navigation_small || Main.$windowWidth < 767) || !$grandparent.hasClass('main-navigation-menu'))) {
				e.preventDefault();
				if (!$parent.hasClass('open')) {
                    $parent.addClass('open');
                    $grandparent.children('li.open').not($parent).not($('.main-navigation-menu > li.active')).removeClass('open').children('ul').slideUp(200);
                    $parent.children('ul').slideDown(200, function () {
                        Main.runContainerHeight();
                    });
                } else {
                    if (!$parent.hasClass('active')) {
                        $grandparent.children('li.open').not($('.main-navigation-menu > li.active')).removeClass('open').children('ul').slideUp(200, function () {
                            Main.runContainerHeight();
                        });
                    } else {
                        $grandparent.children('li.open').removeClass('open').children('ul').slideUp(200, function () {
                            Main.runContainerHeight();
                        });
                    }
                }
            }
        });
    }
    private static runGoTop(): void {
        $('.go-top').on('click', function (e) {
            e.preventDefault();
            $("html, body").animate({
                scrollTop: 0
            }, "slow");
        });
    }
	private static runPanelScroll(): void{
		let $panels = $(".panel-scroll");
        if ($panels.length) {
            $panels.mCustomScrollbar({
                axis:"y",
                theme:"minimal-dark",
                mouseWheel:{
                    enable:true
                }
            });
        }
    }
	private static runAccordionFeatures():void {
		let accordions = $('.accordion');
        if (accordions.length) {
            $('.panel-collapse', accordions).each(function () {
                if (!$(this).hasClass('in')) {
                    $(this).prev('.panel-heading').find('.accordion-toggle').addClass('collapsed');
                }
            });
        }
        accordions.collapse().height('auto');
        let lastClicked;

        $('.accordion-toggle', accordions).on('click', function () {
            let currentTab = $(this);
            $('html,body').animate({
                scrollTop: currentTab.offset().top - 100
            }, 1000);
        });
    }
    private static runNavbarToggleListener():void {
        const $toggle = $('.navbar-header .navbar-toggle');
        const $collapse = $($toggle.data('target'));
        $collapse.on('shown.bs.collapse', () => {
            $('body').addClass('modal-open');
            $collapse.css('height', Main.$windowHeight - $('body > .navbar').height());
        });
        $collapse.on('hidden.bs.collapse', () => {
            $('body').removeClass('modal-open');
        });
    }
    private static runOnlinePing():void {
        setInterval(() => {
            AjaxRequest({
                url: 'userpanel/online',
                cache: false,
                data:{ajax:1}
            });
        }, 15000);
    }
	public static init(){
		Main.runInit();
		Main.runSearchInput();
		Main.runSelect2();
		Main.runElementsPosition();
		Main.runNavigationToggler();
		Main.runNavigationMenu();
		Main.runGoTop();
		Main.runModuleTools();
		Main.runTooltips();
		Main.runPopovers();
		Main.runPanelScroll();
		Main.runShowTab();
		Main.runAccordionFeatures();
		Main.runCustomCheck();
		Main.runPagination();
		Main.runNavbarToggleListener();
		Main.runOnlinePing();
	}
}