import "@jalno/translator";
import "bootstrap";
import { TooltipOptions } from "bootstrap";
import "jquery-bootstrap-checkbox";
import "jquery-mousewheel";
import "jquery-validation";
import "jschr-bootstrap-modal/js/bootstrap-modal.js";
import "jschr-bootstrap-modal/js/bootstrap-modalmanager.js";
import "malihu-custom-scrollbar-plugin";
import "select2";
import Online from "./Online";

export class Main {
	public static init() {
		Main.runInit();
		Main.importSelect2Translator();
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
		Online.run();
	}
	public static importValidationTranslator() {
		if (Translator.getActiveShortLang() !== "en") {
			require(`jquery-validation/dist/localization/messages_${Translator.getActiveShortLang()}.js`);
		}
	}
	public static importSelect2Translator() {
		if ($.fn.hasOwnProperty("select2") && Translator.getActiveShortLang() !== "en") {
			require(`select2/dist/js/i18n/${Translator.getActiveShortLang()}.js`);
		}
	}
	public static SetDefaultValidation(): void {
		if ($.hasOwnProperty("validator")) {
			$.validator.setDefaults({
				errorElement: "span",
				errorClass: "help-block",
				errorPlacement: (error, element) => {
					if (element.attr("type") == "radio" || element.attr("type") == "checkbox") {
						error.insertAfter($(element).closest(".form-group").children("div").children().last());
					} else if (element.attr("name") == "card_expiry_mm" || element.attr("name") == "card_expiry_yyyy") {
						error.appendTo($(element).closest(".form-group").children("div"));
					} else if (element.parent().hasClass("input-group") || element.attr("name") == "credential") {
						error.insertAfter($(element).parent());
					} else {
						error.insertAfter(element);
					}
				},
				ignore: ":hidden",
				highlight: (element) => {

					$(element).closest(".help-block").removeClass("valid");
					$(element).closest(".form-group").removeClass("has-success").addClass("has-error").find(".symbol").removeClass("ok").addClass("required");
				},
				unhighlight: (element) => {
					$(element).closest(".form-group").removeClass("has-error");
				},
				success: (label, element) => {
					label.addClass("help-block valid");
					$(element).closest(".form-group").removeClass("has-error");
				},
			});
			Main.importValidationTranslator();
		}
	}
	public static isIE(): boolean {
		if (typeof Main._isIE === "undefined") {
			Main.detectMSBrowsers();
		}
		return Main._isIE;
	}
	public static isEdge(): boolean {
		if (typeof Main._isEdge === "undefined") {
			Main.detectMSBrowsers();
		}
		return Main._isEdge;
	}
	private static _isIE: boolean;
	private static _isEdge: boolean;
	private static _IEVersion: number;
	private static _EdgeVersion: number;
	private static $windowWidth;
	private static $windowHeight;
	private static $pageArea;

	private static runInit(): void {
		Main.runFixIE();
	}
	private static runElementsPosition(): void {
		Main.$windowWidth = $(window).width();
		Main.$windowHeight = $(window).height();
		Main.$pageArea = Main.$windowHeight - $("body > .navbar").outerHeight() - $("body > .footer").outerHeight();
		$(".sidebar-search input").removeAttr("style").removeClass("open");
		Main.runContainerHeight();
		Main.fixTableDropdownPosition();
	}
	private static runContainerHeight(): void {
		const mainContainer = $(".main-content > .container");
		const mainNavigation = $(".main-navigation");
		if (Main.$pageArea < 760) {
			Main.$pageArea = 760;
		}
		if (Main.$windowWidth >= 768) {
			if (mainContainer.outerHeight() < mainNavigation.outerHeight() && mainNavigation.outerHeight() > Main.$pageArea) {
				mainContainer.css("min-height", mainNavigation.outerHeight());
			} else {
				mainContainer.css("min-height", Main.$pageArea);
			}
		}
	}
	private static runTooltips(): void {
		const $tooltips = $(".tooltips");
		if ($tooltips.length) {
			$tooltips.each((i, el) => {
				const options: TooltipOptions = {};
				if ($(el).data("tooltips-trigger")) {
					options.trigger = $(el).data("tooltips-trigger");
				}
				$(el).tooltip(options);
			});
		}
	}
	private static runPopovers(): void {
		const $popovers = $(".popovers");
		if ($popovers.length) {
			$popovers.popover();
		}
	}
	private static runShowTab(): void {
		const showTabs = $(".show-tab");
		if (showTabs.length) {
			showTabs.bind("click", function(e) {
				e.preventDefault();
				const tabToShow = $(this).attr("href");
				if ($(tabToShow).length) {
					$('a[href="' + tabToShow + '"]').tab("show");
				}
			});
			const parameter = Main.getParameterByName("tabId");
			if (parameter.length > 0) {
				$(`a[href="#${parameter}"]`).tab("show");
			}
		}
	}
	private static getParameterByName(name: string): string {
		name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
		const regex = new RegExp("[\\?&]" + name + "=([^&#]*)");
		const results = regex.exec(location.search);
		return results != null ? decodeURIComponent(results[1].replace(/\+/g, " ")) : "";
	}
	private static runCustomCheck() {
		$(".checkbox label input[type=checkbox], .checkbox-inline input[type=checkbox], .radio label input[type=radio], .radio-inline input[type=radio]").bootstrapCheckbox();
	}
	private static runSearchInput(): void {
		const $searchForm = $(".sidebar-search");
		const $searchInput = $(".sidebar-search input", $searchForm);
		const $searchButton = $(".sidebar-search button", $searchForm);
		$searchInput.data("default", $($searchInput).outerWidth());
		$searchInput.on("focus", function() {
			$(this).animate({
				width: 200,
			}, 200);
		});
		$searchInput.on("blur", function() {
			if (!$(this).val().length) {
				if ($(this).hasClass("open")) {
					$(this).animate({
						width: 0,
						opacity: 0,
					}, 200, function() {
						$(this).hide();
					});
				} else {
					$(this).animate({
						width: $(this).data("default"),
					}, 200);
				}
			}
		});
		$searchButton.on("click", () => {
			if ($searchInput.is(":hidden")) {
				$searchInput.addClass("open").css({
					width: 0,
					opacity: 0,
				}).show().animate({
					width: 200,
					opacity: 1,
				}, 200).focus();
			} else if ($searchInput.hasClass("open") && !$searchInput.val().length) {
				$searchInput.removeClass("open").animate({
					width: 0,
					opacity: 0,
				}, 200, function() {
					$(this).hide();
				});
			} else if (!$searchInput.val().length) {
				$searchInput.focus();
			}
		});
	}
	private static runPagination(): void {
		$("select.paginate").change(function() {
			window.location.href = $("option:selected", this).data("url");
		});
	}
	private static runSelect2(): void {
		const $elements = $("select.select2");
		if ($elements.length) {
			$elements.select2();
		}
	}
	private static runNavigationToggler(): void {
		$(".navigation-toggler").on("click", () => {
			if (!$("body").hasClass("navigation-small")) {
				$("body").addClass("navigation-small");
			} else {
				$("body").removeClass("navigation-small");
			}
		});
	}
	private static runModuleTools(): void {
		$(".panel-tools .panel-collapse").on("click", function(e) {
			e.preventDefault();
			const el = $(this).parent().closest(".panel").children(".panel-body");
			if ($(this).hasClass("collapses")) {
				$(this).addClass("expand").removeClass("collapses");
				el.slideUp(200);
			} else {
				$(this).addClass("collapses").removeClass("expand");
				el.slideDown(200);
			}
		});
	}
	private static runNavigationMenu(): void {
		const isNavigationSmall = $("body").hasClass("navigation-small");

		$(".main-navigation-menu li.active").addClass("open");
		$(".main-navigation-menu > li a").on("click", function(e) {
			const $parent = $(this).parent();
			const $grandparent = $parent.parent();

			if ($parent.children("ul").hasClass("sub-menu") && ((!isNavigationSmall || Main.$windowWidth < 767) || !$grandparent.hasClass("main-navigation-menu"))) {
				e.preventDefault();
				if (!$parent.hasClass("open")) {
					$parent.addClass("open");
					$grandparent.children("li.open").not($parent).not($(".main-navigation-menu > li.active")).removeClass("open").children("ul").slideUp(200);
					$parent.children("ul").slideDown(200, () => {
						Main.runContainerHeight();
					});
				} else {
					if (!$parent.hasClass("active")) {
						$grandparent.children("li.open").not($(".main-navigation-menu > li.active")).removeClass("open").children("ul").slideUp(200, () => {
							Main.runContainerHeight();
						});
					} else {
						$grandparent.children("li.open").removeClass("open").children("ul").slideUp(200, () => {
							Main.runContainerHeight();
						});
					}
				}
			}
		});
	}
	private static runGoTop(): void {
		$(".go-top").on("click", (e) => {
			e.preventDefault();
			$("html, body").animate({
				scrollTop: 0,
			}, "slow");
		});
	}
	private static runPanelScroll(): void {
		const $panels = $(".panel-scroll");
		if ($panels.length) {
			$panels.mCustomScrollbar({
				axis: "y",
				theme: "minimal-dark",
				mouseWheel: {
					enable: true,
				},
			});
		}
	}
	private static runAccordionFeatures(): void {
		const accordions = $(".accordion");
		if (accordions.length) {
			$(".panel-collapse", accordions).each(function() {
				if (!$(this).hasClass("in")) {
					$(this).prev(".panel-heading").find(".accordion-toggle").addClass("collapsed");
				}
			});
		}
		accordions.collapse().height("auto");
		$(".accordion-toggle", accordions).on("click", function() {
			const $currentTab = $(this);
			$("html,body").animate({
				scrollTop: $currentTab.offset().top - 100,
			}, 1000);
		});
	}
	private static runNavbarToggleListener(): void {
		const $toggle = $(".navbar-header .navbar-toggle");
		const $collapse = $($toggle.data("target"));
		$collapse.on("shown.bs.collapse", () => {
			$("body").addClass("modal-open");
			$collapse.css("height", Main.$windowHeight - $("body > .navbar").height());
		});
		$collapse.on("hidden.bs.collapse", () => {
			$("body").removeClass("modal-open");
		});
	}
	private static fixTableDropdownPosition(): void {
		if (Main.$windowWidth > 768) {
			return;
		}
		const $items = $(".table-responsive [data-toggle=dropdown]").parent();
		$items.on("shown.bs.dropdown", function() {
			const $dropdown = $(".dropdown-menu", this);
			$(this).data("dropdown", $dropdown);
			$("body").append($dropdown.css({
				position: "absolute",
				left: $dropdown.offset().left,
				top: $dropdown.offset().top,
				display: "block",
			}).detach());
		});
		$items.on("hidden.bs.dropdown", function() {
			const $dropdown = $(this).data("dropdown");
			$(this).append($dropdown.css({
				position: "",
				left: "",
				top: "",
				display: "",
			}).detach());
		});
	}
	private static runFixIE(): void {
		if (!Main.isIE()) {
			return;
		}
		$("button[type=submit]").filter(function() {
			const form = $(this).attr("form");
			return form !== null && form !== undefined;
		}).on("click", function() {
			const $form = $(`form#${$(this).attr("form")}`);
			if ($form.length) {
				$form.submit();
			}
		});
	}
	private static detectMSBrowsers(): void {
		const userAgent = window.navigator.userAgent;
		const MSIE = userAgent.indexOf("MSIE ");
		const MSEdge = userAgent.indexOf("Edge/");
		const trident = userAgent.indexOf("Trident/");
		Main._isIE = (MSIE > 0 || trident > 0);
		Main._isEdge = (MSEdge > 0);
		if (MSIE > 0) { // IE 10 or older
			Main._IEVersion = parseInt(userAgent.substring(MSIE + 5, userAgent.indexOf(".", MSIE)), 10);
		}
		if (trident > 0) { // IE 11
			const rv = userAgent.indexOf("rv:");
			Main._IEVersion =  parseInt(userAgent.substring(rv + 3, userAgent.indexOf(".", rv)), 10);
		}
		if (MSEdge > 0) { // Edge
			Main._EdgeVersion = parseInt(userAgent.substring(MSEdge + 5, userAgent.indexOf(".", MSEdge)), 10);
		}
	}
}
