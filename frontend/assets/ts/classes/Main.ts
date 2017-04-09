import * as $ from "jquery";
import "bootstrap";
import "icheck";
import "jquery-validation";
import "jschr-bootstrap-modal/js/bootstrap-modal.js";
import "jschr-bootstrap-modal/js/bootstrap-modalmanager.js";
import "select2";
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
        if (mainContainer.outerHeight() < mainNavigation.outerHeight() && mainNavigation.outerHeight() > Main.$pageArea) {
            mainContainer.css('min-height', mainNavigation.outerHeight());
        } else {
            mainContainer.css('min-height', Main.$pageArea);
        }
        if (Main.$windowWidth < 768) {
            mainNavigation.css('min-height', Main.$windowHeight - $('body > .navbar').outerHeight());
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
		let $inputs = $('input[type=checkbox], input[type=radio]');
        if ($inputs.length) {
			let styles = ['square', 'flat'];
			let colors = ['grey', 'red', 'green', 'teal', 'orange', 'purple', 'yellow'];
			for(let i = 0;i<colors.length;i++){
				$('.'+colors[i], $inputs).iCheck({
					checkboxClass: 'icheckbox_minimal-'+colors[i],
					radioClass: 'iradio_minimal-'+colors[i],
					increaseArea: '10%'
				});
			}
			for(let j = 0;j < styles.length;j++){
				for(let i = 0;i<colors.length;i++){
					$(`.${styles[j]}-${colors[i]}`, $inputs).iCheck({
						checkboxClass: `icheckbox_${styles[j]}-${colors[i]}`,
						radioClass: `iradio_${styles[j]}-${colors[i]}`,
						increaseArea: '10%'
					});
				}
			}
        }
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
	private static runLocalization(): void{
    	if($.validator){
			$.extend($.validator.messages, {
				required: "تکمیل این فیلد اجباری است.",
				remote: "لطفا این فیلد را تصحیح کنید.",
				email: ".لطفا یک ایمیل صحیح وارد کنید",
				url: "لطفا آدرس صحیح وارد کنید.",
				date: "لطفا یک تاریخ صحیح وارد کنید",
				dateISO: "لطفا تاریخ صحیح وارد کنید (ISO).",
				number: "لطفا عدد صحیح وارد کنید.",
				digits: "لطفا تنها رقم وارد کنید",
				creditcard: "لطفا کریدیت کارت صحیح وارد کنید.",
				equalTo: "لطفا مقدار برابری وارد کنید",
				accept: "لطفا مقداری وارد کنید که ",
				maxlength: $.validator.format("لطفا بیشتر از {0} حرف وارد نکنید."),
				minlength: $.validator.format("لطفا کمتر از {0} حرف وارد نکنید."),
				rangelength: $.validator.format("لطفا مقداری بین {0} تا {1} حرف وارد کنید."),
				range: $.validator.format("لطفا مقداری بین {0} تا {1} حرف وارد کنید."),
				max: $.validator.format("لطفا مقداری کمتر از {0} حرف وارد کنید."),
				min: $.validator.format("لطفا مقداری بیشتر از {0} حرف وارد کنید.")
			});
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
            $panels.perfectScrollbar({
				handlers: ['click-rail', 'drag-scrollbar', 'keyboard', 'wheel', 'touch'],
                wheelSpeed: 50,
                minScrollbarLength: 20,
                suppressScrollX: true
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
    };
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
		Main.runLocalization();
		Main.runPagination();
	}
}