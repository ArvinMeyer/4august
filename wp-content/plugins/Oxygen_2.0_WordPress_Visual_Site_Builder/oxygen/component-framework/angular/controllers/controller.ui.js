/**
 * All UI staff here
 * 
 */

var CTFrontendBuilderUI = angular.module('CTFrontendBuilderUI', [angularDragula(angular),'ngAnimate','ui.codemirror', 'CTCommonDirectives', 'ui.sortable']);

CTFrontendBuilderUI.controller("ControllerUI", function($controller, $anchorScroll, $location, $scope, $timeout, $interval, $window, dragulaService, $compile, ctScopeService) {  
    ctScopeService.store('uiscope', $scope);
    
    angular.element('#ct-artificial-viewport').attr('src', 
        angular.element('#ct-artificial-viewport').attr('data-src')
        );
    
    /**
     * Include other controllers
     */

    $controller('ControllerDragnDrop', {
        $scope: $scope,
        $timeout: $timeout,
        dragulaService: dragulaService
    });

    $controller('ControllerSlider', {
        $scope: $scope,
        $timeout: $timeout,
        $interval: $interval
    });

    // Background Layers
    $scope.ctBgLayerType = 'image';
    $scope.bgLayersSortableOptions = {
      update: function(e, ui) {
        setTimeout(function() {
            var layers = $scope.iframeScope.getOption('background-layers');
            $scope.iframeScope.setOptionModel('background-layers', layers);
        }, 100);
        
      }
    };
    $scope.media_uploader = {};

    $scope.oxygenUIElement      = jQuery("#oxygen-ui");
    $scope.toolbarElement       = jQuery("#oxygen-topbar");
    $scope.viewportContainer    = jQuery("#ct-viewport-container");
    $scope.artificialViewport   = jQuery("#ct-artificial-viewport");
    $scope.viewportRulerWrap    = jQuery("#ct-viewport-ruller-wrap");
    $scope.sidePanelElement     = jQuery("#ct-sidepanel");
    $scope.settingsPanelElement = jQuery("#oxygen-global-settings");
    $scope.verticalSidebar      = jQuery("#oxygen-sidebar");

    $scope.viewportScale        = 1;
    $scope.viewportScaleLocked  = false;

    // variable to show/hide toolbar elements
    $scope.showAllStyles        = true;
    $scope.showClasses          = true;
    $scope.showComponentBar     = false;
    $scope.showDOMTreeNavigator = false;
    $scope.dialogWindow         = false;
    $scope.viewportRullerShown  = false;
    $scope.showSidePanel        = false;
    $scope.showSettingsPanel    = false;
    $scope.styleTabAdvance      = false;
    $scope.activeForEditBgLayer = false;
    $scope.statusBarActive      = false;
    $scope.showDataPanel        = false;
    $scope.showSidebarLoader    = false;
    $scope.builtinContentEditing = false;

    $scope.currentBorder        = "all";

    $scope.actionTabs = {
        "componentBrowser"  : false,
        "advancedSettings"  : false,
        "contentEditing"    : false,
        "settings"          : false,
        "styleSheet"        : false,
        "codeEditor"        : false
    };

    $scope.highlight        = [];
    
    $scope.tabs                         = [];
    $scope.tabs.components              = [];
    $scope.tabs.components.fundamentals = true;

    $scope.tabs.advanced = [];

    // Background tab
    $scope.tabs.advanced.Background     = [];

    // Position & Size tab
    $scope.tabs.advanced.positionSize   = [];
    
    $scope.tabs.settings                = [];
    //$scope.tabs.settings.page           = true;

    $scope.tabs.navMenu                 = [];
    $scope.tabs.slider                  = [];

    $scope.tabs.sidePanel               = [];
    $scope.tabs.sidePanel.DOMTree       = true;

    $scope.tabs.codeEditor              = [];
    $scope.tabs.codeEditor["code-php"]  = true;
    
    $scope.isSelectableEnabled  = false;
    $scope.isDOMNodesSelected   = false;

    // start with no overlays
    $scope.overlaysCount = 0

    $scope.dialogForms = [];

    $scope.iframeScope = false;


    /**
     * Get iframe scope and save within UI scope
     *
     */

    $scope.$on('iframe-scope', function(e, iframeScope) {
        $scope.iframeScope = iframeScope;
    });

    /**
     * Triggered from iframe to apply UI scope 
     *
     * @since 2.0
     * @author Ilya K.
     */

    $scope.safeApply = function() {
        applySceduled = true;
        if ($scope.$root.$$phase != '$apply' && $scope.$root.$$phase != '$digest') {
            $scope.$apply();
        }
        applySceduled = false;
    }

    
    /**
     * Apply iframe scope on UI scope digest
     *
     * @since 2.0
     * @author Ilya K.
     */

    var applySceduled = false;
    $scope.$watch(function() {
        if (applySceduled) return;
        applySceduled = true;
        $scope.$$postDigest(function() {
            applySceduled = false;
            if ($scope.iframeScope){
                $scope.iframeScope.safeApply();
            }
        });
    });


    /**
     * Check if component active by component id
     *
     * @since 0.1.6
     * @return {bool}
     */

    $scope.isActiveId = function(id) {

        if (!$scope.iframeScope) {
            return false;
        }

        return ( id == $scope.iframeScope.component.active.id ) ? true : false;
    }


    $scope.insertShortcodeToLink = function(text) {
        text=text.replace(/\"/ig, "'");
        angular.element('input#wp-link-url').val(text);
    }

    /**
     * Check if component active by component name
     * 
     * @since 0.1
     * @return {bool}
     */
    
    $scope.isActiveName = function(name) {

        if (!$scope.iframeScope) {
            return false;
        }

        return (name == $scope.iframeScope.component.active.name) ? true : false;
    }


    /**
     * Check if component parent active by component id
     *
     * @since 2.0
     * @return {bool}
     */

    $scope.isActiveParentId = function(id) {

        if (!$scope.iframeScope) {
            return false;
        }

        return ( id == $scope.iframeScope.component.active.parent.id ) ? true : false;
    }


    /**
     * Check if component parent active by component id
     *
     * @since 2.0
     * @return {bool}
     */

    $scope.isActiveParentName = function(name) {

        if (!$scope.iframeScope) {
            return false;
        }

        return ( name == $scope.iframeScope.component.active.parent.name ) ? true : false;
    }
    

    /**
     * Set a tab to show
     * 
     * @since 0.1.7
     */
    
    $scope.switchTab = function(tabGroup, tabName) {       

        if (tabGroup=="advanced") {
            $scope.showAllStyles = false;

            if (["custom-js","custom-css","code-js","code-css","code-php"].indexOf(tabName)>=0) {
                $scope.expandSidebar();
            }
            else {
                $scope.toggleSidebar(true);
            }
        } else {
            $scope.iframeScope.selectedNodeType = null;
        }

        if (tabGroup=="sidePanel") {
            if ( $scope.tabs[tabGroup][tabName] != true ) {
                $scope.toggleSidePanel(true);
            }
            else {
                $scope.toggleSidePanel();
            }

            if (tabName=='DOMTree') {
                $scope.iframeScope.highlightDOMNode($scope.iframeScope.component.active.id);
            }
        }

        $scope.tabs[tabGroup] = [];

        if (tabGroup !== "effects") {
            $scope.tabs["effects"] = [];
        }

        if (tabGroup=="components") {
            $scope.iframeScope.closeAllFolders();
        }
        
        switch (tabName) {
            // all tabs with children
            case "position" : 
                $scope.tabs[tabGroup][tabName] = {margin_padding:true};
                break;

            case "background" : 
                $scope.tabs[tabGroup][tabName] = {color:true};
                break;

            case "borders" : 
                $scope.tabs[tabGroup][tabName] = {border:true};
                break;

            case "cssjs" : 
                $scope.tabs[tabGroup][tabName] = {css:true};
                break;

            case "code" : 
                $scope.tabs[tabGroup][tabName] = {'code-php':true};
                break;

            // other regular tabs
            default :
                $scope.tabs[tabGroup][tabName] = ($scope.tabs[tabGroup][tabName]) ? false : true;
        }

        // if advanced/background tab is opened, collapse the background layers to default state
        if(tabGroup === 'advanced' && tabName === 'background')
            $scope.activeForEditBgLayer = false;

        $scope.showSVGIcons = false;
        
        $scope.disableSelectable();

        if(tabName == 'background-gradient') {
          setTimeout(function() {
            $scope.updateGradientMonitor();
          }, 100);
        } 
    }


    /**
     * Check if any subtab open
     * 
     * @since 2.0
     * @author Ilya K.
     */
    
    $scope.hasOpenTabs = function(name) {

        if (undefined===$scope.tabs[name])
            return false;
        
        return Object.keys($scope.tabs[name]).length > 0;
    }


    /**
     * Check if any subtab open
     * 
     * @since 2.0
     * @author Ilya K.
     */
    
    $scope.hasOpenChildTabs = function(name,child) {
        
        if ($scope.tabs[name]===undefined||$scope.tabs[name][child]===undefined)
            return false;

        if ($scope.tabs[name][child] == undefined)
            return false;

        return Object.keys($scope.tabs[name][child]).length > 0;
    }


    /**
     * Set advanced settings tab to show
     * 
     * @since 0.3.0
     */
    
    $scope.switchChildTab = function(tabGroup, tabName, childTabName) {

        if ( tabName=="cssjs" ) {
            $scope.tabs[tabGroup][tabName] = [];
            $scope.tabs[tabGroup][tabName][childTabName] = true;
            $scope.adjustCodeMirrorHeight();
            return false;
        }

        if ( !$scope.tabs[tabGroup] ) {
            $scope.tabs[tabGroup] = [];
        }

        if ( !$scope.tabs[tabGroup][tabName] || typeof $scope.tabs[tabGroup][tabName] !== "object") {
            $scope.tabs[tabGroup][tabName] = [];
        }

        $scope.tabs[tabGroup][tabName][childTabName] = ($scope.tabs[tabGroup][tabName][childTabName]) ? false : true;

        $scope.showSVGIcons = false;

        $scope.disableSelectable();
    }

    
    /**
     * Set advanced settings tab to show
     * 
     * @since 2.0
     */

    $scope.adjustCodeMirrorHeight = function() {

        var timeout = $timeout(function() {
                
            var codeMirrorElement = jQuery(".CodeMirror", "#ct-vertical-sidebar");

            if (codeMirrorElement.length===0) {
                return false;
            };

            var codeMirrorGutterElement = jQuery(".CodeMirror-gutters", "#ct-vertical-sidebar"),
                fakeCodeMirror = jQuery(".fake-code-mirror-last", "#ct-vertical-sidebar"),
                offset = codeMirrorElement.offset(),
                height = window.innerHeight - offset.top - fakeCodeMirror.outerHeight() - 40;

            codeMirrorElement.height(height);
            codeMirrorGutterElement.height(height);

            // cancel timeout
            $timeout.cancel(timeout);
        }, 0, false);
    }


    /**
     * Check if opened tab is not available for current component and switch to default
     * 
     * @since 0.2.4
     */

    $scope.checkTabs = function() {

        if ($scope.iframeScope.log) {
            console.log("checkTabs()")
        }

        if ( $scope.isActiveName("root") ) {
            $scope.closeAllTabs();
            return;
        }

        // check code block tabs
        if ( $scope.isActiveName("ct_code_block")
             && ( $scope.tabs.advanced['custom-js'] || 
             $scope.tabs.advanced['custom-css'] ) 
            ) {
                $scope.showAllStylesFunc();          
        }

        // check code block tabs
        if ( !$scope.isActiveName("ct_code_block")
             && ( $scope.tabs.advanced['code-js'] || 
             $scope.tabs.advanced['code-css'] || 
             $scope.tabs.advanced['code-php'] ) 
            ) {
                $scope.showAllStylesFunc();          
        }

        // check widget
        /*else if ( $scope.isActiveName("ct_widget") ) {
            $scope.closeAllTabs(["componentBrowser"]);
        }
        // check shortcode
        else if ( $scope.isActiveName("ct_shortcode") ) {
            $scope.closeAllTabs(["componentBrowser"]);
        }*/
        // check others
        else if ( $scope.tabs.advanced['code'] && ( $scope.tabs.advanced['code']['code-php'] || 
                    $scope.tabs.advanced['code']['code-js'] ||
                    $scope.tabs.advanced['code']['code-css'] ) && $scope.iframeScope.component.active.name != "ct_code_block" ) {
            $scope.switchChildTab("advanced", "background", "color");
        }

        // check custom JS tab
        /*if ($scope.tabs.advanced['cssjs'] && $scope.tabs.advanced['cssjs']['js'] && ($scope.iframeScope.isEditing('media') || $scope.iframeScope.isEditing('class') || $scope.iframeScope.isEditing('state'))) {
            $scope.switchChildTab('advanced', 'cssjs', 'css');
        }*/
    }


    /**
     * Close all action tabs, except the tabs specified in keepTabs array
     * 
     * @since 0.1.7
     */
    
    $scope.closeAllTabs = function(keepTabs) {

        if ($scope.iframeScope.log) {
            console.log("closeAllTabs()", keepTabs);
        }
        
        if (keepTabs==undefined){
            keepTabs = [];
        }
        
        angular.forEach($scope.actionTabs, function(value, tab) {
            if (keepTabs.indexOf(tab) == -1) {
                $scope.actionTabs[tab] = false;
            }
        });

        $scope.showSVGIcons = false;

        $scope.adjustViewportContainer();
    }


    /**
     * Close a list of tabs or all of them
     * 
     * @since 2.0
     * @author Ilya K.
     */
    
    $scope.closeTabs = function(tabs) {

        if ($scope.iframeScope.log) {
            console.log("closeTabs()", tabs);
        }

        for (var key in $scope.tabs) {
            if ($scope.tabs.hasOwnProperty(key)) {

                if (tabs==undefined){
                    $scope.tabs[key] = false;
                }
                else if (tabs.indexOf(key) >= 0) {
                    $scope.tabs[key] = false;
                }
            }
        }
    }


    /**
     * Switch to code editor if Code Block is active
     * 
     * @since 1.3
     * @author Ilya K.
     */

    $scope.possibleSwitchToCodeEditor = function(tabGroup, tabName) {

        if ( $scope.isActiveName("ct_code_block") ) {
            $scope.switchActionTab("codeEditor");
            $scope.switchTab("codeEditor","code-css");
        }
        else {
            $scope.switchTab(tabGroup, tabName);   
        }
    }


    /**
     * Show all styles tabs
     * 
     * @since 2.0
     * @author Ilya K.
     */

    $scope.showAllStylesFunc = function() {
        
        $scope.showAllStyles=true;
        $scope.tabs['advanced'] = [];
        $scope.toggleSidebar(true);
    }


    /**
     * Toggle sidebar to/from 50%
     * 
     * @since 2.0
     * @author Ilya K.
     */

    $scope.toggleSidebar = function(forceCollapse) {

      if ($scope.iframeScope.log) {
        console.log("toggleSidebar()", forceCollapse);
      }

      var isExpanded = $scope.verticalSidebar.data("expanded"),
          button = jQuery('.oxygen-code-editor-expand', $scope.verticalSidebar);

      if (isExpanded) {
        // collapse
        $scope.verticalSidebar.css({'width': '300px'});
        $scope.adjustViewportContainer()
        $scope.verticalSidebar.data("expanded", false);
        jQuery(button).text(jQuery(button).attr('data-expand'));
      }
      else if (!forceCollapse) {
        // expand
        $scope.verticalSidebar.css({'width': '50%'});
        $scope.adjustViewportContainer();
        $scope.verticalSidebar.data("expanded", true);
        jQuery(button).text(jQuery(button).attr('data-collapse'));
      }

    }


    /**
     * Open sidebar to 50%
     * 
     * @since 2.0
     * @author Ilya K.
     */

    $scope.expandSidebar = function() {

        var timeout = $timeout(function() {
            var button = jQuery('.oxygen-code-editor-expand', $scope.verticalSidebar);

            $scope.verticalSidebar.css({'width': '50%'});
            $scope.adjustViewportContainer();
            $scope.verticalSidebar.data("expanded", true);
            jQuery(button).text(jQuery(button).attr('data-collapse'));
            
            $timeout.cancel(timeout);
        }, 0, false);   
    }


    /**
     * Check is to show tab
     * 
     * @since 0.1.7
     * @return {bool}
     */
    
    $scope.isShowTab = function(tabGroup, tabName) {  

        if ( $scope.tabs[tabGroup] ) {
            return ( $scope.tabs[tabGroup][tabName] ) ? true : false;
        }
        else {
            return false;
        }
    }


    /**
     * Check is to show child tab
     * 
     * @since 0.3.0
     * @return {bool}
     */
    
    $scope.isShowChildTab = function(tabGroup, tabName, childTabName) {  

        if ( $scope.tabs[tabGroup] ) {
            return ( $scope.tabs[tabGroup][tabName] && $scope.tabs[tabGroup][tabName][childTabName] ) ? true : false;
        }
        else {
            return false;
        }
    }


    /**
     * Toggle Side Panel
     *
     * @since 0.1.5
     */

    $scope.toggleSidePanel = function(forceOpen) {

        if (forceOpen==true&&$scope.showSidePanel) {
            return
        } 

        $scope.showSidePanel = !$scope.showSidePanel;

        if (!$scope.showSettingsPanel) {
            if ($scope.showSidePanel) {
                $scope.sidePanelElement.css({width:"300px"});
            }
            else {
                $scope.sidePanelElement.css({width:"0px"});
            }
        }
        else {
            $scope.showSettingsPanel = false;
        }

        $scope.adjustViewportContainer();

        if (!$scope.showSidePanel) {
            $scope.disableSelectable();
        }
    }


    /**
     * Toggle Settings Panel
     *
     * @since 2.0
     * @author Ilya K.
     */

    $scope.toggleSettingsPanel = function() {

        $scope.showSettingsPanel = !$scope.showSettingsPanel;

        if (!$scope.showSidePanel) {
            if ($scope.showSettingsPanel) {
                $scope.settingsPanelElement.css({right:"0px"});
            }
            else {
                $scope.settingsPanelElement.css({right:"-300px"});
            }
        }
        else {
            $scope.settingsPanelElement.css({
                right: "0px"
            });
            $scope.showSidePanel = false;
        }

        $scope.adjustViewportContainer();

        if (!$scope.showSettingsPanel) {
            $scope.disableSelectable();
        }
    }


    /**
     * Show editor panel for contenteditable elements
     *
     * @since 0.1.5
     */

    $scope.enableContentEdit = function(element) {

        if ( $scope.actionTabs["contentEditing"] == true ) {
            return false;
        }

        // switch edit to id
        $scope.iframeScope.switchEditToId();

        var activeComponent = $scope.iframeScope.getActiveComponent();

        if ( !element.is(activeComponent) ){
            activeComponent=element;
            $scope.builtinContentEditing = true;
        }
        else {
            $scope.builtinContentEditing = false;
        }
        
        if ( activeComponent[0].attributes['contenteditable'] ) {
            // FireFox fix for the invisible cursor issue 
            if ( $scope.isActiveName("ct_link_text") ) {
                jQuery("<input style='position:fixed;top:40%;left:40%' type='text'>").appendTo("body").focus().remove();
            }

            activeComponent[0].setAttribute("contenteditable", "true");
            activeComponent[0].setAttribute("spellcheck", "true");

            if(!$scope.iframeScope.isChrome) {
                $scope.iframeScope.disableElementDraggable(true);
            }            
            
            activeComponent.focus();
            
            $scope.iframeScope.setEndOfContenteditable(activeComponent[0]);

            $scope.actionTabs["contentEditing"] = true;
        }

        $scope.iframeScope.hideResizeBox(0.1);
    }


    /**
     * Hide editor panel for contenteditable elements
     *
     * @since 0.1.5
     */

    $scope.disableContentEdit = function() {

        if ( !$scope.actionTabs["contentEditing"] )
            return false;

        if ($scope.iframeScope.log) {
            console.log('disableContentEdit()');
        }

        var activeComponent = $scope.iframeScope.getActiveComponent();

        $scope.builtinContentEditing = false;

        // clear selection
        if (window.getSelection) {
            if (window.getSelection().empty) {  // Chrome
                window.getSelection().empty();
            } else if (window.getSelection().removeAllRanges) {  // Firefox
                window.getSelection().removeAllRanges();
                }
        } else if (document.selection) {  // IE?
            document.selection.empty();
        }

        if ( activeComponent[0].attributes['contenteditable'] ) {

            if(!$scope.iframeScope.isChrome) {
                $scope.iframeScope.disableElementDraggable(false);
            }
          
            var content = activeComponent.html();

            activeComponent.html("");

           /* var el = activeComponent[0];
            while ((el = el.parentElement) && !el.classList.contains('ct_link'));
            if(el)
                el.setAttribute("href", ''); */

            activeComponent[0].setAttribute("contenteditable", "false");
            activeComponent[0].removeAttribute("spellcheck");
            
            activeComponent.html(content);
            
            if ($scope.iframeScope.component.active.name != 'ct_span') {

                if(typeof(activeComponent[0].attributes['plaintext']) === 'undefined' || activeComponent[0].attributes['plaintext'] !== "true") {

                    var content = $scope.iframeScope.getOption('ct_content');
                    var idIncrement = 0;

                    content = content.replace(/\[oxygen[^\]]*\]/ig, function(match) {

                        // create a span component out of match
                        // embed it in the tree as a child of $scope.iframeScope.component.active.id
                        // get the new component's id

                        var newComponent = {
                          id : $scope.iframeScope.component.id + idIncrement, 
                          name : "ct_span"
                        }

                        idIncrement++;

                        // set default options first
                        $scope.iframeScope.applyComponentDefaultOptions(newComponent.id, "ct_span");

                        // insert new component to Components Tree
                        $scope.iframeScope.findComponentItem($scope.iframeScope.componentsTree.children, $scope.iframeScope.component.active.id, $scope.iframeScope.insertComponentToTree, newComponent);

                        // update span options
                        $scope.iframeScope.component.options[newComponent.id]["model"]["ct_content"] = match;
                        $scope.iframeScope.setOption(newComponent.id, "ct_span", "ct_content");

                        return "<span id=\"ct-placeholder-"+newComponent.id+"\"></span>"
                    });

                    $scope.iframeScope.setOptionModel('ct_content', content, $scope.iframeScope.component.active.id, $scope.iframeScope.component.active.name);
                }

                $scope.iframeScope.rebuildDOM($scope.iframeScope.component.active.id);
            }
        }
        else {
            
            var element = activeComponent.find("[contenteditable=true]");

            if (element.length > 0) {
                
                if(!$scope.iframeScope.isChrome) {
                    $scope.iframeScope.disableElementDraggable(false);
                }
              
                var content = element.html();

                element.html("");

                element[0].setAttribute("contenteditable", "false");
                element[0].removeAttribute("spellcheck");
                
                element.html(content);

                var option = element.data('optionname'),
                    content = $scope.iframeScope.getOption(option),
                    idIncrement = 0;

                content = content.replace(/\[oxygen[^\]]*\]/ig, function(match) {

                    var newComponent = {
                        id : $scope.iframeScope.component.id + idIncrement, 
                        name : "ct_span"
                    }

                    idIncrement++;

                    // set default options first
                    $scope.iframeScope.applyComponentDefaultOptions(newComponent.id, "ct_span");

                    // insert new component to Components Tree
                    $scope.iframeScope.findComponentItem($scope.iframeScope.componentsTree.children, $scope.iframeScope.component.active.id, $scope.iframeScope.insertComponentToTree, newComponent);

                    // update span options
                    $scope.iframeScope.component.options[newComponent.id]["model"]["ct_content"] = match;
                    $scope.iframeScope.setOption(newComponent.id, "ct_span", "ct_content");

                    return "<span id=\"ct-placeholder-"+newComponent.id+"\"></span>"
                });

                $scope.iframeScope.setOptionModel(option, content, $scope.iframeScope.component.active.id, $scope.iframeScope.component.active.name);

                var timeout = $timeout(function() {
                    $scope.$apply();
                    $timeout.cancel(timeout);
                }, 0, false);
            }
        }
        
        /*if($scope.iframeScope.component.active.name != 'ct_text_block')
            $scope.rebuildDOM($scope.iframeScope.component.active.id);*/

        $scope.actionTabs["contentEditing"] = false;
        $scope.showDataPanel = false;
        $scope.iframeScope.adjustResizeBox();
    }


    /**
     * Open TinyMCE dialog window and set the text from ct_content
     *
     * @since 2.0
     * @author Ilya K.
     */

    $scope.openTinyMCEDialog = function() {

        $scope.tinyMCEWindow = true;
        var content = $scope.iframeScope.getOption("ct_content");
        
        if ( jQuery('#wp-oxygen_vsb_tinymce-wrap').hasClass('tmce-active') && tinyMCE.get("oxygen_vsb_tinymce") ) {
            tinyMCE.get("oxygen_vsb_tinymce").setContent(content);
        } else{
            jQuery('#oxygen_vsb_tinymce').val(content);
        }
    }


    /**
     * Close TinyMCE dialog window and set the text to ct_content
     *
     * @since 2.0
     * @author Ilya K.
     */

    $scope.closeTinyMCEDialog = function() {

        $scope.tinyMCEWindow = false;
        var content = "";

        if ( jQuery('#wp-oxygen_vsb_tinymce-wrap').hasClass('tmce-active') && tinyMCE.get("oxygen_vsb_tinymce") ) {
            content = tinyMCE.get("oxygen_vsb_tinymce").getContent();
        }
        else {
            content = jQuery('#oxygen_vsb_tinymce').val();
        }

        $scope.iframeScope.setOptionModel("ct_content", content);
        $scope.iframeScope.setOption($scope.component.active.id, $scope.component.active.name, "ct_content");
    }


    /**
     * Wrap active component with link (if not already a link) and show settings
     *
     * @since 0.1.6
     * @author Ilya K.
     */

    $scope.processLink = function() {

        $scope.iframeScope.cancelDeleteUndo();

        if ($scope.iframeScope.log){
            console.log("processLink()");
        }

        var linkComponentId = $scope.iframeScope.getLinkId();

        if (!linkComponentId) {

            // convert to Text Link
            if ($scope.isActiveName("ct_text_block")) {
                $scope.iframeScope.findComponentItem($scope.iframeScope.componentsTree.children, $scope.iframeScope.component.active.id, $scope.iframeScope.updateTreeComponentTag, "ct_link_text");
            }
            else
            // convert to Link Wrapper
            if ($scope.isActiveName("ct_div_block")) {
                $scope.iframeScope.findComponentItem($scope.iframeScope.componentsTree.children, $scope.iframeScope.component.active.id, $scope.iframeScope.updateTreeComponentTag, "ct_link");
                
                // convert all links inside div block
                $scope.iframeScope.findComponentItem($scope.iframeScope.componentsTree.children, $scope.iframeScope.component.active.id, $scope.iframeScope.updateTagsByName, 
                    {from:"ct_link_text",to:"ct_text_block"});
                $scope.iframeScope.findComponentItem($scope.iframeScope.componentsTree.children, $scope.iframeScope.component.active.id, $scope.iframeScope.updateTagsByName, 
                    {from:"ct_link",to:"ct_div_block"});
            }
            else
            if ($scope.iframeScope.component.active.name === 'ct_span') {
                $scope.iframeScope.findComponentItem($scope.iframeScope.componentsTree.children, $scope.iframeScope.component.active.id, $scope.iframeScope.updateTreeComponentTag, "ct_link_text");
    
                // rebuild parent
                var timeout = $timeout(function() {
                    $scope.iframeScope.rebuildDOM($scope.iframeScope.component.active.parent.id);
                    $timeout.cancel(timeout);
                }, 0, false);
            }
            // wrap with Link Wrapper
            else {
                var newComponentId = $scope.iframeScope.wrapComponentWith("ct_link");

                $scope.iframeScope.activateComponent(newComponentId, "ct_link");
            }
        }
        else {
            $scope.iframeScope.activateComponent(linkComponentId, "ct_link");
        }

        var button = jQuery('.oxygen-link-button');
        var timeout = $timeout(function() {
            jQuery('<textarea>')
                .attr('id', 'ct-link-dialog-txt')
                .css('display', 'none')
                .attr('data-linkProperty', button.attr('data-linkProperty'))
                .attr('data-linkTarget', button.attr('data-linkTarget'))
                .appendTo('body');

            wpLink.open('ct-link-dialog-txt'); //open the link popup*/
            
            jQuery('#wp-link-url').val($scope.iframeScope.component.options[$scope.iframeScope.component.active.id]['model']['url']);

            jQuery('#wp-link-target').prop( 'checked', '_blank' === $scope.iframeScope.component.options[$scope.iframeScope.component.active.id]['model']['target'] );
            jQuery('#wp-link-wrap').removeClass('has-text-field');

            jQuery('#oxygen-link-data-dialog-opener').insertAfter(jQuery('#wp-link-wrap.has-text-field #wp-link-url'));
            jQuery('#oxygen-link-data-dialog').insertAfter(jQuery('#wp-link-wrap.has-text-field'));

            $scope.showLinkDataDialog = false;
            $scope.$apply();

            $timeout.cancel(timeout);
        }, 0, false);
    }

    
    /**
     * Convert link components from link Div or Text Block
     * 
     * @since 0.3.3
     * @author Ilya K.
     */

    $scope.removeLink = function() {

        // handle Text Link
        if ($scope.isActiveName("ct_link_text")) {

            var componentParent = $scope.getComponentById($scope.iframeScope.component.active.parent.id);

            if ( !componentParent[0] || componentParent[0].attributes['contenteditable'] ) {
                // convert a ct_link_text to ct_span
                $scope.iframeScope.findComponentItem($scope.iframeScope.componentsTree.children, $scope.iframeScope.component.active.id, $scope.iframeScope.updateTreeComponentTag, "ct_span");
                
                var placeholder = document.getElementById($scope.iframeScope.component.options[$scope.iframeScope.component.active.id]['selector']);

                var parentContent = $scope.iframeScope.component.options[$scope.iframeScope.component.active.parent.id]["id"]['ct_content'];

                $scope.cleanReplace(placeholder, "<span id=\"ct-placeholder-"+$scope.iframeScope.component.active.id+"\"></span>");
            }
            else {
                $scope.iframeScope.findComponentItem($scope.iframeScope.componentsTree.children, $scope.iframeScope.component.active.id, $scope.iframeScope.updateTreeComponentTag, "ct_text_block");
            }
        }

        // handle Link Wrapper
        if ($scope.isActiveName("ct_link")) {
            $scope.iframeScope.findComponentItem($scope.iframeScope.componentsTree.children, $scope.iframeScope.component.active.id, $scope.iframeScope.updateTreeComponentTag, "ct_div_block");
        }
    }



    /**
     * Show overlay to prevent user action when save the page, etc
     * 
     * @since 0.1.3
     */

    $scope.showLoadingOverlay = function(trigger) {

        var pageOverlay = document.getElementById("ct-page-overlay");
            pageOverlay = angular.element(pageOverlay);

        $scope.overlaysCount++;

        //console.log("showLoadingOverlay()", trigger, $scope.overlaysCount);
        pageOverlay.show();
    }


    /**
     * Remove overlay
     * 
     * @since 0.1.3
     */

    $scope.hideLoadingOverlay = function(trigger) {

        var pageOverlay = document.getElementById("ct-page-overlay");
            pageOverlay = angular.element(pageOverlay);

        $scope.overlaysCount--;

        //console.log("hideLoadingOverlay()", trigger, $scope.overlaysCount);
        // hide spinner only when all overlays closed
        if ($scope.overlaysCount === 0) {
            pageOverlay.hide();
        }
    }


    /**
     * Show widget loading overlay
     * 
     * @since 2.0
     */

    $scope.showWidgetOverlay = function(id) {

        if ($scope.iframeScope.log) {
            console.log("showWidgetOverlay()", id);
        }

        var widget = $scope.iframeScope.getComponentById(id),
            position = widget.css("position");

        if (position == "static") {
            widget.addClass("oxygen-positioned-element");
        }

        widget.append("<div class='oxygen-widget-overlay'><i class='fa fa-cog fa-2x fa-spin'></i></div>");
    }


    /**
     * Hide widget loading overlay
     * 
     * @since 2.0
     */

    $scope.hideWidgetOverlay = function(id) {

        var widget = $scope.iframeScope.getComponentById(id);

        jQuery(".oxygen-widget-overlay", widget).remove();
        widget.removeClass("oxygen-positioned-element");
    }

    
    /**
     * Switch action tabs
     * 
     * @since 0.1.7
     */

    $scope.switchActionTab = function(action) {

        if ($scope.iframeScope.log) {
            console.log("switchActionTab()", action);
        }

        $scope.iframeScope.selectedNodeType = null;

        // Do not allow to edit the settings while editing inner_content
        if( action === 'settings' && jQuery('body').hasClass('ct_inner')) {
            alert('To edit the settings for this page, load the containing template in the builder.');
            return;
        }

        // on open Add+ section
        if ( action == "componentBrowser" ) {
            $scope.showAllStylesFunc();
            $scope.iframeScope.stylesheetToEdit = false;
            $scope.styleTabAdvance = false;
            $scope.toggleSidebar(true);
        }

        // Check Code Block tabs
        if ( $scope.tabs.advanced['cssjs'] && (
             $scope.tabs.advanced['cssjs']['js'] ||
             $scope.tabs.advanced['cssjs']['css'] ) && 
             $scope.iframeScope.component.active.name == "ct_code_block" ) {
            
            //$scope.switchChildTab("advanced", "background", "color");
        }

        // check content editing
        if ( action == "contentEditing" ) {

            if ( !$scope.actionTabs["contentEditing"]) {
                $scope.enableContentEdit();
            } 
            else {
                $scope.disableContentEdit();
            }
        }
        else if ( action === 'styleSheet') {
            if($scope.iframeScope.stylesheetToEdit && $scope.iframeScope.stylesheetToEdit !== $scope.actionTabs[action]) {
                $scope.actionTabs = {};
                $scope.actionTabs[action] = $scope.iframeScope.stylesheetToEdit;
            }
            else {
                $scope.actionTabs[action] = false;
            }
        }
        else {
            
            // disable content editing
            $scope.disableContentEdit();

            // set tab flag
            if ( $scope.actionTabs[action] ) {
                $scope.actionTabs[action] = false;
            } 
            else {
                $scope.actionTabs = {};
                $scope.actionTabs[action] = true;
            }
        }

        $scope.adjustViewportContainer();
        $scope.disableSelectable();
    }


    /**
     * Activate action tabs
     * 
     * @since 0.1.7
     */

    $scope.activateActionTab = function(action) {

        // check content editing
        if ( action == "contentEditing" ) {
                
            // close all tabs before enable
            $scope.actionTabs = {};
            $scope.enableContentEdit();
        }
        else {
            
            // disable content editing
            $scope.disableContentEdit();

            $scope.actionTabs = {};
            $scope.actionTabs[action] = true;
        }
    }


    /**
     * Check if action tab is active
     * 
     * @since 0.1.7
     */

    $scope.isActiveActionTab = function(action) {

        return ( $scope.actionTabs[action] ) ? true : false;
    }

    $scope.showBackgroundLayer = function($event) {
        angular.element($event.target).closest('ul').find('> li > div').hide();
        angular.element($event.target).siblings('div').toggle();
    }

    $scope.toggleActiveForEditBgLayer = function(index, $event) {

        if($scope.activeForEditBgLayer === index) {
            $scope.activeForEditBgLayer = false;
        }
        else {
            $scope.activeForEditBgLayer = index;
            $scope.updateGradientMonitor(index); 
        }
    }

    $scope.addBackgroundLayer = function(layerType) {

        var type = typeof($scope.iframeScope.component.options[$scope.iframeScope.component.active.id]['model']['background-layers']);
        
        if(type === 'string' || type === 'undefined') {
            $scope.iframeScope.component.options[$scope.iframeScope.component.active.id]['model']['background-layers'] = [];
        }

        var layer = {
            type: layerType
        }

        if(layerType === 'image') {
            //units
            layer['background-size-width-unit'] = 'px';
            layer['background-size-height-unit'] = 'px';

            //units
            layer['background-position-left-unit'] = 'px';
            layer['background-position-top-unit'] = 'px';

        }
        else if(layerType === 'gradient') {
            layer['colors'] = [];

            layer['radial-position-top-unit'] = '%';
            layer['radial-position-left-unit'] = '%';

        }

        var layers = $scope.iframeScope.component.options[$scope.iframeScope.component.active.id]['model']['background-layers'];
        layers.push(layer);
        $scope.iframeScope.setOptionModel('background-layers', layers);
        
    }

    $scope.updateGradientMonitor = function(index) {

        var allparams = {
            colors: $scope.iframeScope.getOption('gradient').colors
        }

        let filteredColors = _.filter(allparams['colors'], function(color) {
            return color.value && color.value.length > 0;
        })


        let colorStrings = _.map(filteredColors, function(color) { 
            return color.value + 
                (color.position ? ' ' + color.position + color['position-unit']: '');
        });

        // if it is a single color, repeat it once to show a solid layer
        if(colorStrings.length === 1) {
            colorStrings.push(colorStrings[0]);
        }

        if(colorStrings.length > 0)
            angular.element('.ct-gradient-monitor').css('background', 'linear-gradient(90deg, '+colorStrings.join(', ')+')');
        else
            angular.element('.ct-gradient-monitor').css('background', '');
    }

    $scope.addGradientColor = function() {
        
        
        var type = typeof($scope.iframeScope.component.options[$scope.iframeScope.component.active.id]['model']['gradient']);
        
        if(type === 'string' || type === 'undefined') {
            $scope.iframeScope.component.options[$scope.iframeScope.component.active.id]['model']['gradient'] = {};
        }

        var gradient = $scope.iframeScope.component.options[$scope.iframeScope.component.active.id]['model']['gradient'];

        gradient['colors'] = gradient['colors'] || [];

        gradient['colors'].push({
            'position-unit': 'px'
        })

        $scope.iframeScope.setOptionModel('gradient', gradient);
        
    }

    $scope.removeBackgroundLayer = function($event) {
        var index = angular.element($event.target).closest('li').index();

        var layers = $scope.iframeScope.getOption('background-layers');

        layers.splice(index, 1);

        $scope.iframeScope.setOptionModel('background-layers', layers);

    }

    $scope.removeGradientColor = function($event, parentIndex) {
        var index = angular.element($event.target).closest('li').index();
        var gradient = $scope.iframeScope.getOption('gradient');

        
        
        gradient['colors'].splice(index, 1);

        $scope.iframeScope.setOptionModel('gradient', gradient);
    }

    $scope.setGradientForBG = function() {

        var gradient = $scope.iframeScope.getOption('gradient');
        $scope.iframeScope.setOptionModel('gradient', gradient);
        $scope.updateGradientMonitor();
    }

    $scope.toggleGradientRadio = function(param, value, index, $event) {
        // specific to background layers
        var gradient = $scope.iframeScope.getOption('gradient');
        
        if(gradient[param] === value) {
            delete(gradient[param]);
            angular.element($event.target).prop('checked', false);
        }
        else {
            gradient[param] = value;
        }

        $scope.iframeScope.setOptionModel('gradient', gradient);

    }

    /**
     * Uncheck radio button
     * 
     * @since 0.2.3
     */

    $scope.radioButtonClick = function(componentName, paramName, paramValue) {

        if ($scope.iframeScope.log) {
            console.log("radioButtonClick()", componentName, paramName, paramValue);
        }
        
        var modelValue      = $scope.iframeScope.getOption(paramName),
            defaultValue    = $scope.iframeScope.defaultOptions[componentName][paramName];

        if ($scope.iframeScope.isEditing("custom-selector")) {
            var idValue = $scope.iframeScope.component.options[$scope.iframeScope.component.active.id]["model"][paramName];
        }
        else {
            var idValue = $scope.iframeScope.component.options[$scope.iframeScope.component.active.id]["id"][paramName];   
        }

        //console.log(modelValue, defaultValue, paramValue, idValue);
        
        if ($scope.iframeScope.isEditing("id") && !$scope.iframeScope.isEditing("media") && !$scope.iframeScope.isEditing("state")) {
            // set
            if ( modelValue == paramValue && !idValue ) {
                $scope.iframeScope.setOptionModel(paramName, paramValue);
            }
        }
        else {
            idValue = true;
        }

        // unset
        if ( modelValue == paramValue && idValue ) {
            
            $scope.iframeScope.setOptionModel(paramName, "");
        }
    }


    /**
     * Uncheck radio button
     * 
     * @since 2.0
     * @author Ilya K.
     */

    $scope.globalSettingsRadioButtonClick = function(obj, param, value) {

        if ($scope.iframeScope.log) {
            console.log("globalSettingsRadioButtonClick()", param, value);
        }
        
        if (obj[param] == value) {
            obj[param] = "";
        }   
    }


    /**
     * Show pop-up dialog with options
     * 
     * @since 0.2.3
     */
    
    $scope.showDialogWindow = function() {
        
        $scope.dialogWindow = true;
    }


    /**
     * Hide pop-up dialog with options
     * 
     * @since 0.2.3
     */
    
    $scope.hideDialogWindow = function() {
        
        $scope.dialogWindow = false;

        // hide forms
        $scope.dialogForms = [];
        
        jQuery(document).off("keydown", $scope.switchComponent);
    }


    /**
     * Enable/disable selectable for DOM Tree
     * 
     * @since 0.2.4
     * @deprecated
     */

    $scope.switchSelectable = function() {

        if ( $scope.isSelectableEnabled ) {
            $scope.disableSelectable();
        }
        else {
            $scope.enableSelectable();          
        }
    }


    /**
     * Enable selectable for DOM Tree
     * 
     * @since 0.2.4
     * @deprecated
     */

    $scope.enableSelectable = function() {

        if ( $scope.isSelectableEnabled ) {
            return;
        }

        if ($scope.iframeScope.log) {
            console.log("enableSelectable()");
        }

        // fake component
        $scope.activateComponent(-2); // "-1" is for custom selectors

        $scope.isSelectableEnabled = true;

        // init nuSelecatble plugin
        $scope.selectable = angular.element("#ct-dom-tree").nuSelectable({
            items: '.ct-dom-tree-name',
            selectionClass: 'ct-selection-box',
            selectedClass: 'ct-selected-dom-node',
            autoRefresh: 'true',
            onMove: function(selected) {
                if (selected.length > 0 ) {
                    $scope.isDOMNodesSelected = true;
                }
                else {
                    $scope.isDOMNodesSelected = false;
                };
                $scope.$apply();
            },
            onMouseDown: function() {
                $scope.isDOMNodesSelected = false;
                $scope.$apply();
            }
        });
    }

    /**
     * Disable selectable for DOM Tree
     * 
     * @since 0.2.4
     * @deprecated
     */

    $scope.disableSelectable = function() {

        return false;

        if ( !$scope.isSelectableEnabled ) {
            return;
        }

        $scope.isSelectableEnabled = false;

        // remove data and events
        if ( $scope.selectable ) {
            $scope.selectable.removeData();
            $scope.selectable.unbind('mousedown mouseup');
        }

        // clear selection
        $scope.selectable.find('.ct-selected-dom-node').removeClass('ct-selected-dom-node');

        // activate root
        $scope.activateComponent(0, 'root');
    }
    

    /**
     * Check if componenet is in viewport 
     * 
     * @since 0.3.0
     */

    $scope.isElementInViewport = function(el) {

        //special bonus for those using jQuery
        if (typeof jQuery === "function" && el instanceof jQuery) {
            el = el[0];
        }

        if (typeof el.getBoundingClientRect !== "function") {
            return false;
        }

        var rect = el.getBoundingClientRect();

        if ( rect.top >= ($scope.artificialViewport[0].contentWindow.innerHeight || $scope.artificialViewport[0].contentWindow.document.documentElement.clientHeight) ) {
            return "below";
        }

        if ( rect.bottom <= 0 ) {
            return "above";
        }

        return "visible";

        //rect.top >= 0 &&
        //rect.left >= 0 &&
        //rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) //&& /*or $(window).height() */
        //rect.right <= (window.innerWidth || document.documentElement.clientWidth) /*or $(window).width() */
    }

    
    /**
     * Smooth scroll window to component by selector
     * 
     * @since 0.3.0
     */
    
    $scope.scrollToComponent = function(selector) {

        if ($scope.iframeScope.log) {
            console.log("scrollToComponent() #"+ selector);
        }
        
        var target = $scope.artificialViewport.contents().find('#'+selector);
        
        if ( $scope.isElementInViewport(target) == "above" ) {
        
            $scope.artificialViewport.contents().find('html,body').stop().animate({
                scrollTop: target.offset().top - 100
            }, 500);
        }

        if ( $scope.isElementInViewport(target) == "below" ) {
        
            $scope.artificialViewport.contents().find('html,body').stop().animate({
                scrollTop: target.offset().top - window.innerHeight + target.outerHeight() + 100
            }, 500);
        }
    };


    /**
     * Programmatically trigger CodeMirror editor blur event
     * to make code apply when clicking in DOM Tree
     *
     * @since 0.4.0
     * @author Ilya K.
     */
    
    $scope.triggerCodeMirrorBlur = function(id) {
        
        if ( !$scope.bubble ) {
            var editor = jQuery('.CodeMirror', $scope.oxygenUIElement)[0];
            
            if (editor) {
                
                /**
                 * Taken from codemirror.js onBlur() function
                 */
                
                var cm = editor.CodeMirror;
                if (cm.state.focused) {
                    var handlers = editor.CodeMirror._handlers && editor.CodeMirror._handlers["blur"];
                    for (var i = 0; i < handlers.length; ++i) handlers[i].apply(null);
                    cm.state.focused = false;
                    jQuery(cm.display.wrapper).removeClass("CodeMirror-focused");
                }
                clearInterval(cm.display.blinker);
                setTimeout(function() {if (!cm.state.focused) cm.display.shift = false;}, 150);
            }
        }

        // prevent propagation
        $scope.bubble = true;
        if (id==0) {
            $scope.bubble = false;            
        }
    };


    /**
     * Add CodeMirror events
     *
     * @since 0.4.0
     * @author Ilya K.
     */
    
    $scope.codemirrorLoaded = function(_editor){

        var timeout = $timeout(function() {
            // make sure gutter height is updated
            _editor.refresh();
            $timeout.cancel(timeout);
        }, 0, false);
        
        _editor.on("change", function(){

            var timeout = $timeout(function() {
                
                //console.log("codemirrorChangeTimeout()", $scope.iframeScope.component.active.id, $scope.latesetCodeEditorComponentId)
                $scope.latesetCodeEditorComponentId = $scope.iframeScope.component.active.id;

                $timeout.cancel(timeout);
            }, 0, false);
        })
        
        // Update Code Block on Codemirror editor focus out
        _editor.on("blur", function(){
            //console.log("codemirrorBlur()", $scope.latesetCodeEditorComponentId)
            
            switch (_editor.options.type) {

                // Code Block
                
                case "css":
                    //$scope.applyCodeBlockCSS(); // Live edit enabled at the moment
                break;

                case "js":
                    $scope.iframeScope.applyCodeBlockJS($scope.latesetCodeEditorComponentId);
                break;

                case "php":
                    $scope.iframeScope.applyCodeBlockPHP($scope.latesetCodeEditorComponentId);
                break;

                // Component

                case "custom-css":
                    //$scope.applyComponentCSS(); // Live edit enabled at the moment
                break;

                case "custom-js":
                    $scope.iframeScope.applyComponentJS($scope.latesetCodeEditorComponentId);
                break;

                // Stylesheet

                case "stylesheet":
                    //$scope.applyStyleSheet($scope.stylesheetToEdit); // Live edit enabled at the moment
                break;
            }
        });

    };


    /**
     * Show status with a status message
     *
     * @since 2.0
     * @author Ilya K.
     */

    $scope.showStatusBar = function(status) {

        $scope.statusMessage = status;
        $scope.statusBarActive = true;
    }

    
    /**
     * Hide status bar
     *
     * @since 2.0
     * @author Ilya K.
     */

    $scope.hideStatusBar = function() {

        $scope.statusBarActive = false;
        var timeout = $timeout(function() {
            $scope.statusMessage = "";
            $timeout.cancel(timeout);
        }, 400, false);
    }


    /**
     * Compile and insert new component
     *
     * @since 2.0
     * @author Ilya K.
     */

    $scope.cleanInsertUI = function(element, parentElement, index) {

        if ($scope.iframeScope.log) {
            console.log("cleanInsertUI()",parentElement,index);
        }
            
        if ( parentElement ) {
            parentElement = jQuery(parentElement);
            parentElement.html("");
            $scope.insertAtIndexUI(element, parentElement, index);
        } 
        else {
            angular.element(element).replaceWith(element);
        }
    }


    /**
     * Insert child DOM element at a specific index in a parent element
     *
     * @since 0.1.7
     * @author Ilya K.
     */

    $scope.insertAtIndexUI = function(child, parent, index) {

        if ( index === 0 ) {
            parent.prepend(child);
        }
        else if ( index > 0 ) {
            jQuery(">*:nth-child("+index+")", parent).after(child);
        }
        else {
            parent.append(child);
        }
    }

    $scope.applyMenuAim = function() {
        jQuery('.oxygen-add-section-library-flyout-panel').off('mouseleave');
        jQuery('.oxygen-add-section-library-flyout-panel').off('mouseenter');
        jQuery('.oxygen-add-section-library-flyout-panel').on('mouseenter', function() {
            jQuery(this).addClass('oxygen-add-section-library-flyout-panel-open');
        });
        jQuery('.oxygen-add-section-library-flyout-panel').on('mouseleave', function() {
            jQuery(this).removeClass('oxygen-add-section-library-flyout-panel-open');
            jQuery('.oxygen-add-section-library-menu-subcategories a.oxygen-add-section-library-menu-subcategories-active').removeClass('oxygen-add-section-library-menu-subcategories-active');
        });
        setTimeout(function() {
            jQuery('.oxygen-add-section-library-menu-subcategories').menuAim({
                activate: function(e) {
                    
                    jQuery('.oxygen-add-section-library-flyout-category').css('display', 'none');
                    jQuery('#category-' + jQuery(e).data('cat')).css('display', 'block');

                    jQuery(e).addClass('oxygen-add-section-library-menu-subcategories-active');
                    jQuery('.oxygen-add-section-library-flyout-panel').addClass('oxygen-add-section-library-flyout-panel-open');

                },
                deactivate: function(e) {

                    jQuery('.oxygen-add-section-library-menu-subcategories a.oxygen-add-section-library-menu-subcategories-active').removeClass('oxygen-add-section-library-menu-subcategories-active');
                    jQuery('.oxygen-add-section-library-flyout-panel').removeClass('oxygen-add-section-library-flyout-panel-open');

                },
                exitMenu: function(e) {
                    setTimeout(function() {

                        if(!jQuery('.oxygen-add-section-library-flyout-panel').hasClass('oxygen-add-section-library-flyout-panel-open')) {
                            jQuery('.oxygen-add-section-library-menu-subcategories a.oxygen-add-section-library-menu-subcategories-active').removeClass('oxygen-add-section-library-menu-subcategories-active');
                        }

                    }, 100);

                    jQuery('.oxygen-add-section-library-flyout-panel').removeClass('oxygen-add-section-library-flyout-panel-open');

                },
                rowSelector: "> a",
            });
        }, 100);
    }

    /**
     * All UI/jQuery stuff here
     * 
     * @since 0.3
     */
    
    $scope.setupUI = function() {

        /**
         * Hide Colorpicker on iframe document click
         */
         
        jQuery($scope.artificialViewport[0].contentWindow.document)
        .on("click", function(e) {
            // needed to hide colorpicker
            if(!e.target.getAttribute('contenteditable')&&!jQuery(e.target).closest('.ct-active').attr('contenteditable'))
                jQuery("html,body").trigger("click");
        })

        jQuery(document).ready(function() {

            jQuery('body').on('change', '#oxygen-stylesheet-folder-dropdown', function(e) {
                
                $scope.iframeScope.stylesheetToEdit['parent'] = jQuery(e.target).val();
                $scope.$apply();

            });

        });




        /**
         * Apply sticky-header class if scrolled enough
         */

        jQuery($scope.artificialViewport[0].contentWindow).scroll(function() {
            $scope.adjustStickyHeaders($scope.artificialViewport[0].contentWindow)
        })

        $scope.adjustStickyHeaders = function(windowObj) {

            $scope.artificialViewport.contents().find(".oxy-sticky-header").each(function(){

                // skip header with no ng-attributes, this is reusable part and have js code in place already
                if (jQuery(this).attr("ng-attr-component-id")===undefined) {
                    return;
                }
                
                var headerID    = jQuery(this).attr("ng-attr-component-id"),
                    selector    = "#"+$scope.iframeScope.component.options[headerID]["selector"],
                    scrollval   = $scope.iframeScope.component.options[headerID]["model"]["sticky_scroll_distance"],
                    stickySize  = parseInt($scope.iframeScope.getMediaMinSize($scope.iframeScope.component.options[headerID]['model']['sticky-media']));

                if (!scrollval || scrollval < 1 || jQuery(windowObj).scrollTop() > scrollval){
                    if ($scope.artificialViewport.width() > stickySize){
                        $scope.artificialViewport.contents().find(selector).addClass("oxy-sticky-header-active");
                        $scope.artificialViewport.contents().find("body").css("margin-top", $scope.artificialViewport.contents().find(selector).height());
                    }
                }
                else {
                    $scope.artificialViewport.contents().find(selector).removeClass("oxy-sticky-header-active");
                    $scope.artificialViewport.contents().find("body").css("margin-top", "");
                }
            })

        }


        /**
         * Highlight components on hover
         */
        
        // DOM
        $scope.builderElement
        .on("mouseover", ".ct-component:not(.ct-contains-oxy)", function(e){
            e.stopPropagation();
            $scope.artificialViewport.contents().find('.ct-highlight').removeClass('ct-highlight');

            // in case we are editing the ct_inner content, then no need to hilight the outer template elements
            if(jQuery('body').hasClass('ct_inner') 
                && (jQuery(this).hasClass('ct-inner-content') || jQuery(this).closest('.ct-component.ct-inner-content').length < 1 )) {
                return;
            }

            if (jQuery(this).parent().is('.oxy-header-container')) {
                // highlight header row when hover  left/right/center sections
                jQuery(this).parents('.oxy-header-row').addClass('ct-highlight');
            }
            else {
                jQuery(this).addClass('ct-highlight');
            }
        })
        .on("mouseout", ".ct-component", function(e){
            e.stopPropagation();
            if (jQuery(this).parent().is('.oxy-header-container')) {
                // highlight header row when hover  left/right/center sections
                jQuery(this).parents('.oxy-header-row').removeClass('ct-highlight');
            }
            jQuery(this).removeClass('ct-highlight');
        })

        $scope.oxygenUIElement
        // DOM Tree
        .on("mouseover", ".ct-dom-tree-node-anchor", function(e){
            var componentId = jQuery(this).attr("ng-attr-node-id");
            $scope.artificialViewport.contents().find('.ct-component[ng-attr-component-id="'+componentId+'"]').addClass('ct-highlight');
        })
        .on("mouseout", ".ct-dom-tree-node-anchor", function(e){
            var componentId = jQuery(this).attr("ng-attr-node-id");
            $scope.artificialViewport.contents().find('.ct-component[ng-attr-component-id="'+componentId+'"]').removeClass('ct-highlight');
        })

        // Resize box titlebar
        $scope.artificialViewport.contents().find('body')
        .on("mouseover", "#oxygen-resize-box-parent-titlebar, .oxygen-resize-box-top", function(e){
            var componentId = $scope.iframeScope.component.active.parent.id;
            $scope.artificialViewport.contents().find('.ct-component[ng-attr-component-id="'+componentId+'"]').addClass('ct-highlight');
        })
        .on("mouseout", "#oxygen-resize-box-parent-titlebar, .oxygen-resize-box-top", function(e){
            var componentId = $scope.iframeScope.component.active.parent.id;
            $scope.artificialViewport.contents().find('.ct-component[ng-attr-component-id="'+componentId+'"]').removeClass('ct-highlight');
        })


        /**
         * Special property messages
         */

        jQuery("body")
        // Not available for classes
        .on("mouseover", ".oxygen-editing-class:not(.oxygen-editing-media) .not-available-for-classes:not(.oxygen-active-select)", function(e){
            var $this = jQuery(this);
            if ($this.find('.oxygen-active-select').length) {
                return;
            }
            jQuery('#oxy-no-class-msg').css({
                "display": "block",
                "top": $this.offset().top + $this.height(),
            });
        })
        .on("mouseleave", ".oxygen-editing-class:not(.oxygen-editing-media) .not-available-for-classes", function(e){
            jQuery('#oxy-no-class-msg').css({
                "display": "none",
            });
        })
        // not available for media
        .on("mouseover", ".oxygen-editing-media:not(.oxygen-editing-class) .not-available-for-media:not(.oxygen-active-select)", function(e){
            var $this = jQuery(this);
            if ($this.find('.oxygen-active-select').length) {
                return;
            }
            jQuery('#oxy-no-media-msg').css({
                "display": "block",
                "top": $this.offset().top + $this.height(),
            });
        })
        .on("mouseleave", ".oxygen-editing-media:not(.oxygen-editing-class) .not-available-for-media", function(e){
            jQuery('#oxy-no-media-msg').css({
                "display": "none",
            });
        })
        // not available for classes and media
        .on("mouseover", ".oxygen-editing-class.oxygen-editing-media .not-available-for-media.not-available-for-classes:not(.oxygen-active-select)", function(e){
            var $this = jQuery(this);
            if ($this.find('.oxygen-active-select').length) {
                return;
            }
            jQuery('#oxy-no-class-no-media-msg').css({
                "display": "block",
                "top": $this.offset().top + $this.height(),
            });
        })
        .on("mouseleave", ".oxygen-editing-class.oxygen-editing-media .not-available-for-media.not-available-for-classes", function(e){
            jQuery('#oxy-no-class-no-media-msg').css({
                "display": "none",
            });
        })

        .on("mouseover", ".oxygen-editing-class.oxygen-editing-media .not-available-for-media:not(.not-available-for-classes, .oxygen-active-select)", function(e){
            var $this = jQuery(this);
            if ($this.find('.oxygen-active-select').length) {
                return;
            }
            jQuery('#oxy-no-media-msg').css({
                "display": "block",
                "top": $this.offset().top + $this.height(),
            });
        })
        .on("mouseleave", ".oxygen-editing-class.oxygen-editing-media .not-available-for-media:not(.not-available-for-classes)", function(e){
            jQuery('#oxy-no-media-msg').css({
                "display": "none",
            });
        })

        .on("mouseover", ".oxygen-editing-class.oxygen-editing-media :not(.not-available-for-media, .oxygen-active-select).not-available-for-classes", function(e){
            var $this = jQuery(this);
            if ($this.find('.oxygen-active-select').length) {
                return;
            }
            jQuery('#oxy-no-class-msg').css({
                "display": "block",
                "top": $this.offset().top + $this.height(),
            });
        })
        .on("mouseleave", ".oxygen-editing-class.oxygen-editing-media :not(.not-available-for-media).not-available-for-classes", function(e){
            jQuery('#oxy-no-class-msg').css({
                "display": "none",
            });
        })
        
        /**
         * Media upload
         */
        
        /** 
         * In order to make this functionality available for foreground images as well, 
         * this function relies on data- attributes provided in the .oxygen-file-input-browse html element
         * this attributes can be as follows 
         * data-mediaTitle for the title of the media dialog
         * data-mediaButton for the text of the 'insert' button on the media dialog
         * data-mediaProperty for specifying the model's param that will be updated with the url
         * data-heightProperty for updating the height 
         * data-widthProperty for updating the width
         *
         */
        $scope.oxygenUIElement
        .on('click', '.oxygen-file-input-browse', function(e) {
            
            // save the target in scope
            $scope.mediaUploadTarget = jQuery(e.target);
            
            var mediaType = $scope.mediaUploadTarget.attr('data-mediaType');

            if(!$scope.media_uploader[mediaType]) {

                var options = {
                    title:     $scope.mediaUploadTarget.attr('data-mediaTitle') || 'Set Image',
                    button:{
                        text:  $scope.mediaUploadTarget.attr('data-mediaButton') || 'Set Image',
                    },
                    library:{ 
                        type:  $scope.mediaUploadTarget.attr('data-mediaContent') || 'image' 
                    },
                    multiple:  $scope.mediaUploadTarget.attr('data-mediaMultiple') || false,
                }

                if ($scope.mediaUploadTarget.attr('data-mediaMultiple')=='true') {
                    options.state = 'gallery';
                    options.frame = 'post';
                }

                $scope.media_uploader[mediaType] = wp.media(options);

                // gallery
                $scope.media_uploader[mediaType].on("update", function(selection){

                    var ids = [];
                    selection.each( function( image ) {
                        ids.push( image.get( 'id' ) );
                    } );

                    $scope.iframeScope.setOptionModel($scope.mediaUploadTarget.attr('data-mediaProperty'), ids.join(","));
                    $scope.iframeScope.renderComponentWithAJAX('oxy_render_gallery');          
                    $scope.$apply();
                });

                // single
                $scope.media_uploader[mediaType].on("select", function(){

                    var json = $scope.media_uploader[mediaType].state().get("selection").first().toJSON();
                    //console.log(json);
                    // update scope and model
                        
                    if($scope.mediaUploadTarget.attr('data-fieldId')) {
                        jQuery('#'+$scope.mediaUploadTarget.attr('data-fieldId')).val(json.url).trigger('change');
                    }
                    else {
                        $scope.iframeScope.setOptionModel($scope.mediaUploadTarget.attr('data-mediaProperty'), json.url);
                        if ($scope.mediaUploadTarget.attr('data-mediaProperty')=='video_background') {
                            $scope.iframeScope.rebuildDOM($scope.iframeScope.component.active.id);
                        }
                    }

                    if($scope.mediaUploadTarget.attr('data-heightProperty'))
                        $scope.iframeScope.setOptionModel($scope.mediaUploadTarget.attr('data-heightProperty'), json.height);
                    if($scope.mediaUploadTarget.attr('data-widthProperty'))
                        $scope.iframeScope.setOptionModel($scope.mediaUploadTarget.attr('data-widthProperty'), json.width);
                        
                    // set image alt attr
                    $scope.iframeScope.setOptionModel("alt", json.alt);
                    
                    $scope.$apply();
                });
            }

            $scope.media_uploader[mediaType].open();
        })

        
        jQuery('body')
        .on('click', '#wp-link-submit', function(e) {
            
            var attrs = wpLink.getAttrs();
            $scope.iframeScope.setOptionModel(jQuery('#ct-link-dialog-txt').attr('data-linkProperty'), attrs.href);
            $scope.iframeScope.setOptionModel(jQuery('#ct-link-dialog-txt').attr('data-linkTarget'), attrs.target);
            
            $scope.$apply();

            if( attrs.href.trim() === '') {
                $scope.removeLink();
            }

            jQuery('body #ct-link-dialog-txt').remove();
            wpLink.close();

        })
        .on('click', '#wp-link-cancel, #wp-link-close, #wp-link-backdrop', function(e) {
            jQuery('body #ct-link-dialog-txt').remove();
            $scope.showLinkDataDialog = false;
            wpLink.close();
            $scope.$apply();
        });

        /**
         * Builder handle move
         */
        var dragging = false;

        // handle move start
        jQuery('#ct-viewport-handle')
            .mousedown(function(e){    
                e.preventDefault();
           
                dragging = true;

                var ghostbar = jQuery('<div>',{id: 'ct-ghost-viewport-handle'}).prependTo('#ct-viewport-ruller-wrap');

                // init ghost position
                var position = e.pageX-$scope.artificialViewport.offset().left-3;    
                ghostbar.css("left", position/$scope.viewportScale);
                
                // adjust ghost position on move
                jQuery(document).mousemove(function(e){
                    position = e.pageX-$scope.artificialViewport.offset().left-3;
                    ghostbar.css("left", position/$scope.viewportScale);
                });
            })
            .dblclick(function(){
                if ($scope.iframeScope.getCurrentMedia()!= "default") {
                    $scope.iframeScope.setCurrentMedia("default");
                }
                else {
                    $scope.artificialViewport.css("width", "");
                    $scope.hideViewportRuller();
                    $scope.adjustArtificialViewport();
                }
            });

        // handle move end
        jQuery(document).mouseup(function(e){
           if (dragging) {
               
                var width = e.pageX-$scope.artificialViewport.offset().left;

                $scope.setMediaByWidth(width/$scope.viewportScale);

                jQuery('#ct-ghost-viewport-handle').remove();
                jQuery(document).unbind('mousemove');
                dragging = false;

                $scope.$apply();
            }
        });


        $scope.setMediaByWidth = function(width) {

            if (undefined==width) {
                width = $scope.viewportContainer[0].scrollWidth;
            }
            
            var mediaName = $scope.iframeScope.getMediaNameBySize(width);

            if (mediaName) {
                $scope.iframeScope.setCurrentMedia(mediaName, false);
            }

            // adjust viewport
            $scope.adjustViewport(width + "px");
            $scope.adjustArtificialViewport();
            $scope.adjustViewportRuller();
        }

        $scope.showViewportRuller = function() {
            $scope.viewportRulerWrap.css("display", "block");
            $scope.viewportRullerShown = true;
        }

        $scope.hideViewportRuller = function() {
            $scope.viewportRulerWrap.css("display", "");
            $scope.viewportRullerShown = false;
        }

        $scope.adjustViewportRuller = function() {

            $scope.viewportRulerWrap.css("width", 0);

            var offset = 0,
                width = ($scope.viewportRullerWidth > $scope.viewportContainer.width()) ? $scope.viewportRullerWidth : $scope.viewportContainer.width() - offset - 1;

            $scope.viewportRulerWrap.css({
                    left : offset,
                    width : width/$scope.viewportScale,
                    transform : "scale("+$scope.viewportScale+")",
                });

            //console.log("adjustViewportRuller()", offset, container.width() - offset);
            
            jQuery('#ct-viewport-handle').css("left", $scope.artificialViewport.width()-3);

            $scope.viewportRullerWidth = $scope.artificialViewport.width();
        }


        /**
         * Adjust artificial viewport
         *
         * @since 0.3.2
         */
        
        $scope.adjustViewport = function(size) {

            //console.log("adjustViewport()", size);
        
            $scope.artificialViewport.css("width", size);

            $scope.adjustViewportRuller();
        }


        /**
         * Adjust viewport container
         *
         * @since 0.3.2
         */

        $scope.adjustViewportContainer = function(artificialViewportWidth) {

            if ($scope.iframeScope.log) {
                console.log("adjustViewportContainer()", artificialViewportWidth);
            }

            var sidebarWidth = $scope.verticalSidebar.width();
            
            // DOM Tree opened, Add+ opened
            if ( ($scope.showSidePanel || $scope.showSettingsPanel ) && $scope.isActiveActionTab('componentBrowser') ) {

                if (artificialViewportWidth===undefined) {
                    artificialViewportWidth = window.innerWidth - 300 - sidebarWidth - 12
                }

                $scope.viewportContainer.css({
                    marginLeft: sidebarWidth,
                    width: window.innerWidth - 300 - sidebarWidth,
                })
                
                $scope.adjustArtificialViewport(artificialViewportWidth);
                $scope.adjustViewportRuller();
                
                $scope.sidePanelElement.css({
                    width: "300px"
                });
            }
            else

            // DOM Tree opened, Add+ closed
            if ( ($scope.showSidePanel || $scope.showSettingsPanel ) && !$scope.isActiveActionTab('componentBrowser') ) {

                if (artificialViewportWidth===undefined) {
                    artificialViewportWidth = window.innerWidth - 300 - sidebarWidth - 12
                }
               
                $scope.viewportContainer.css({
                    marginLeft: sidebarWidth,
                    width: window.innerWidth - 300 - sidebarWidth,
                    paddingTop: 0
                });
                
                $scope.adjustArtificialViewport(artificialViewportWidth);
                $scope.adjustViewportRuller();
                
                $scope.sidePanelElement.css({
                    width: "300px"
                });
            }
            else

            // DOM Tree closed, Add+ opened
            if ( ( !$scope.showSidePanel && !$scope.showSettingsPanel ) && $scope.isActiveActionTab('componentBrowser') ) {

                if (artificialViewportWidth===undefined) {
                    artificialViewportWidth = window.innerWidth - sidebarWidth - 12
                }
               
                $scope.viewportContainer.css({
                    marginLeft: sidebarWidth,
                    width: window.innerWidth - sidebarWidth,
                });

                $scope.adjustArtificialViewport(artificialViewportWidth);
                $scope.adjustViewportRuller();
                
                $scope.sidePanelElement.css({
                    width: "0"
                });
            }
            else
            
            // All closed
            {   

                if (artificialViewportWidth===undefined) {
                    artificialViewportWidth = window.innerWidth - sidebarWidth - 12
                }

                $scope.viewportContainer.css({
                    marginLeft: sidebarWidth,
                    width: window.innerWidth - sidebarWidth,
                    paddingTop: 0
                });
                
                $scope.adjustArtificialViewport(artificialViewportWidth);
                $scope.adjustViewportRuller();
                
                $scope.sidePanelElement.css({
                    width: "0"
                });
            }

        }


        /**
         * Adjust artificial viewport
         */

        $scope.adjustArtificialViewport = function(artificialViewportWidth) {

            //console.log(artificialViewportWidth);

            var heightOffset = 71; 

            if ($scope.viewportRullerShown) {
                heightOffset += 16;
            }

            // adjust artificial viewport based on "Page width"
            if (!$scope.viewportRullerShown) {

                var viewportContainerWidth = $scope.viewportContainer.width();
                    pageWidth = $scope.iframeScope.getWidth($scope.iframeScope.getPageWidth());

                if (artificialViewportWidth===undefined) {
                    artificialViewportWidth = $scope.artificialViewport.width();
                }
                
                if ( pageWidth.value > artificialViewportWidth ) {
                    
                    var neededSpace = parseInt($scope.iframeScope.getPageWidth()) + 20;
                     
                    $scope.artificialViewport.css({
                        "width": neededSpace,
                        "min-width": ""
                    });
                    
                    // rescale iframe if not fit
                    if ( !$scope.viewportScaleLocked ) {
                        if ( neededSpace > artificialViewportWidth ) {
                            var scale = artificialViewportWidth / neededSpace;
                            $scope.artificialViewport.css({
                                transform: "scale("+scale+")",
                                height: "calc("+(100/scale)+"vh - "+(heightOffset/scale)+"px)"});
                            $scope.viewportScale = scale;
                        }
                        $scope.viewportContainer.css("overflow-x","");
                    }
                    else {
                        $scope.artificialViewport.css({
                            "transform": "scale(1)",
                            "height": "calc(100vh - "+heightOffset+"px)",
                        });
                        $scope.viewportContainer.css("overflow-x","auto");
                        $scope.viewportScale = 1;
                    }
                }
                else
                if ( pageWidth.value < viewportContainerWidth - 12 ) {
                    $scope.artificialViewport.css({
                        "transform": "scale(1)",
                        "height": "calc(100vh - "+heightOffset+"px)",
                    });
                    $scope.viewportContainer.css("overflow-x","auto");
                    if ( !$scope.viewportRullerShown ) {
                        $scope.artificialViewport.css({
                            "width": "",
                            "min-width": ""
                        });
                    }
                    $scope.viewportScale = 1;
                    //console.log("adjustArtificialViewport()", "")
                }

                // unset builder width
                $scope.builderElement.css("width", "");

            }
            else {
                // unset builder width
                $scope.builderElement.css("width", "");
                //console.log("adjustArtificialViewport()", "")

                var viewportContainertWidth = $scope.viewportContainer.width(),
                    artificialViewportWidth = $scope.artificialViewport.width() + 20,
                    scale = viewportContainertWidth / artificialViewportWidth;
                                
                // rescale iframe if not fit
                if ( artificialViewportWidth > viewportContainertWidth ) {
                    
                    if ( !$scope.viewportScaleLocked ) {
                         $scope.artificialViewport.css({
                            transform: "scale("+scale+")",
                            height: "calc("+(100/scale)+"vh - "+(heightOffset/scale)+"px)"
                        });
                        $scope.viewportContainer.css("overflow-x","");
                        $scope.viewportScale = scale;
                    }
                    else {
                        $scope.artificialViewport.css({
                            transform: "scale(1)",
                            height: "calc(100vh - "+heightOffset+"px)",
                            marginBottom: 23
                        });
                        $scope.viewportContainer.css("overflow-x","auto");
                        $scope.viewportScale = 1;
                    }
                }
                else {
                    $scope.artificialViewport.css({
                        transform: "scale(1)",
                        height: "calc(100vh - "+heightOffset+"px)",
                        marginBottom: 23
                    });
                    $scope.viewportContainer.css("overflow-x","auto");
                    $scope.viewportScale = 1;
                }
            }

            $scope.iframeScope.adjustResizeBox();
            
            // safely apply scope
            var timeout = $timeout(function() {
                $scope.$apply();
                $timeout.cancel(timeout);
            }, 0, false);

        }

        /**
         * Lock/unlock viewport scale qo 100%
         *
         * @since 2.0
         * @author Ilya K.
         */

        $scope.lockViewportScale = function() {

          $scope.viewportScaleLocked = !$scope.viewportScaleLocked;
          $scope.adjustViewportContainer();
          $scope.viewportContainer.scrollLeft(0);
        }


        /**
         * Measureboxes
         */
        
        $scope.oxygenUIElement
        .on("click", ".oxygen-measure-box-unit-selector", function(e) {
            // hide all boxes
            jQuery(".oxygen-measure-box", $scope.oxygenUIElement)
                .removeClass("oxygen-measure-box-unit-selector-active")
            // show the box
            jQuery(this).closest(".oxygen-measure-box", $scope.oxygenUIElement).addClass("oxygen-measure-box-unit-selector-active");
            measureboxOutsideClick();
        })
        .on("click", ".oxygen-measure-box-unit", function(e) {
            // hide the box
            jQuery(this).closest(".oxygen-measure-box", $scope.oxygenUIElement).removeClass("oxygen-measure-box-unit-selector-active");
        })
        .on("click", "div:not(.oxygen-measure-box-options)>.oxygen-measure-box>input", function(e) {
            // hide all boxes
            jQuery(".oxygen-measure-box", $scope.oxygenUIElement)
                .removeClass("oxygen-measure-box-unit-selector-active");
            // show one box
            jQuery(this).closest(".oxygen-measure-box", $scope.oxygenUIElement)
                .find('input').focus();
            measureboxOutsideClick();
        })
        .on("focus", ".oxygen-measure-box>input", function(e) {
            // select all text
            this.setSelectionRange(0, this.value.length)
            jQuery(".oxygen-measure-box>input")
                .removeClass("oxygen-measure-box-focused-input");
            jQuery(this)
                .addClass("oxygen-measure-box-focused-input")
                .parents(".oxygen-four-sides-measure-box").addClass("oxygen-measure-box-focused");
        })
        .on("focusout", ".oxygen-measure-box>input", function(e) {
            if ($scope.applyAllInProgress !== true) {
                jQuery(".oxygen-measure-box-focused").removeClass("oxygen-measure-box-focused");                
            }
        })
        // make box not closed when ('html').click triggered 
        .on("click", ".oxygen-measure-box", function(e){
            e.stopPropagation();
        })
        .on("click", ".oxygen-measure-box-units", function(e){
            e.stopPropagation();
        });

        function measureboxOutsideClick() {
            // close the box if user click outside it
            jQuery('html').click(function(clickEvent) {
                // hide all boxes
                jQuery(".oxygen-measure-box", $scope.oxygenUIElement)
                    .removeClass("oxygen-measure-box-unit-selector-active")
                    .removeClass("oxygen-measure-box-active");

                // unbid it immideately
                jQuery(this).unbind(clickEvent);
            });
        }


        /**
         * Apply all/opposite options
         */
        
        // mark/unmark measurebox as 'apply all'
        $scope.oxygenUIElement
        .on("mousedown", ".oxygen-apply-all-trigger", function(e) {
            $scope.applyAllInProgress = true;
        })
        .on("mouseup", ".oxygen-apply-all-trigger", function(e) {
            $scope.applyAllInProgress = false;
            applyAll(this);
            // reselect value
            jQuery(".oxygen-measure-box-focused-input").focus();
            // make it show again
            jQuery(this).parents(".oxygen-four-sides-measure-box").addClass("oxygen-measure-box-focused");
        })

        function applyAll(element, value, unit) {

            if ($scope.iframeScope.log) {
                console.log("applyAll()");
                $scope.iframeScope.functionStart("applyAll()");
            }

            var sizeBox     = jQuery(element).parents(".oxygen-four-sides-measure-box"),
                option      = jQuery(".oxygen-measure-box-focused-input").data("option");

            // get values from $scope if not defined
            if (undefined === value) {
                value = $scope.iframeScope.getOption(option);
            }
            if (undefined === unit) {
                unit = $scope.iframeScope.getOptionUnit(option);
            }

            // loop all size box values to apply currently editing value
            jQuery(".oxygen-measure-box>input", sizeBox).each(function(){

                var option          = jQuery(this).data("option"),
                    currentValue    = $scope.iframeScope.getOption(option),
                    currentUnit     = $scope.iframeScope.getOptionUnit(option);

                if (currentValue != value) {
                    $scope.iframeScope.setOptionModel(option, value, $scope.iframeScope.component.active.id, $scope.iframeScope.component.active.name, true);
                }

                if (currentUnit != unit) {
                    $scope.iframeScope.setOptionUnit(option, unit, true);
                }
            })

            // safely apply scope
            var timeout = $timeout(function() {
                $scope.$apply();
                $timeout.cancel(timeout);
            }, 0, false);

            // update styles
            $scope.iframeScope.outputCSSOptions($scope.iframeScope.component.active.id);

            $scope.iframeScope.functionEnd("applyAll()");
        }
        

        /**
         * Selects
         */
        
        $scope.oxygenUIElement
        .on("click", ".ct-select:not(.ct-ui-disabled,.ct-custom-selector)", function(e) {

            if ( jQuery(this).parents('.ct-style-set-dropdown') ) {
                jQuery(".ct-new-component-class-input",this).focus();
            }

            e.stopPropagation();
        })

        .on("click", ".oxygen-select", function(e) {
            
            // if the click was inside a text input for new classname, do not hide the select dropdown
            if(jQuery(e.target).hasClass('oxygen-classes-dropdown-input')) {
                e.stopPropagation();
                return;
            }

            var isActive = jQuery(this).hasClass("oxygen-active-select");

            // hide all dropdowns
            jQuery(".oxygen-select")
                .removeClass("oxygen-active-select")
                .removeClass("oxygen-active-classes-select")
                .removeClass("oxygen-active-states-select");

            // show dropdown
            if (!isActive) {
                jQuery(this).addClass("oxygen-active-select");
                jQuery(".oxygen-overlay-property-msg").hide();
                selectOutsideClick();
            }

            // focus on search
            jQuery(".oxygen-select-box-option input",this).focus();
            
            e.stopPropagation();
        })

        // don't hide the box on input click
        .on("click", ".oxygen-select-box-option input", function(e) {
            e.stopPropagation();
        })

        // media icon click
        .on("click", ".oxygen-media-query-box, .oxygen-active-selector-box-state, .oxygen-active-selector-box", function(e) {

            var select = jQuery(this).closest('.oxygen-select'),
                isActive = select.hasClass("oxygen-active-select"),
                isActiveStates = select.hasClass("oxygen-active-states-select"),
                isActiveClasses = select.hasClass("oxygen-active-classes-select");

            // hide all dropdowns
            jQuery(".oxygen-select")
                .removeClass("oxygen-active-select")
                .removeClass("oxygen-active-classes-select")
                .removeClass("oxygen-active-states-select");

            // show certain dropdown
            if (!isActive) {
                select.addClass("oxygen-active-select");
                selectOutsideClick();
            }

            if (!isActiveStates && jQuery(this).hasClass('oxygen-active-selector-box-state')) {
                select.addClass("oxygen-active-states-select");
                selectOutsideClick();
            }

            if (!isActiveClasses && jQuery(this).hasClass('oxygen-active-selector-box')) {
                select.addClass("oxygen-active-classes-select");
                selectOutsideClick();
            }

            e.stopPropagation();
        })


        // media item click
        .on("click", ".oxygen-media-query-dropdown li", function(e) {

            // hide all dropdowns
            jQuery(".oxygen-select")
                .removeClass("oxygen-active-select")
                .removeClass("oxygen-active-classes-select")
                .removeClass("oxygen-active-states-select");

            e.stopPropagation();
        })

        function selectOutsideClick() {
            // close the box if user click outside it
            jQuery('html').click(function(clickEvent) {
                // close
                jQuery(".ct-select", $scope.oxygenUIElement).removeClass("ct-active").removeClass("ct-active-media").removeClass("ct-active-states");

                // unbid it immideately
                jQuery(this).unbind(clickEvent);
            });

            // close the box if user click outside it
            jQuery('html').click(function(clickEvent) {
                // close
                jQuery(".oxygen-select", $scope.oxygenUIElement)
                    .removeClass("oxygen-active-select")
                    .removeClass("oxygen-active-classes-select")
                    .removeClass("oxygen-active-states-select");

                // unbid it immideately
                jQuery(this).unbind(clickEvent);
            });
        }

        
        /**
         * Increase descrease measure values with top/bottom key press
         */
        
        $scope.oxygenUIElement
        .on("keydown", ".oxygen-measure-box>input", function(e) {
            
            // increase 
            if (e.keyCode==38) {

                if (this.value == parseFloat(this.value, 10)){
                    this.value++;
                    var input = jQuery(this);
                    input.trigger("change").trigger("input");
                }
            };
            
            // decrease
            if (e.keyCode==40) {

                if (this.value == parseFloat(this.value, 10)){
                    this.value--;
                    var input = jQuery(this);
                    input.trigger("change").trigger("input");
                }
            }
        });


        // Returns a function, that, as long as it continues to be invoked, will not
        // be triggered. The function will be called after it stops being called for
        // N milliseconds. If `immediate` is passed, trigger the function on the
        // leading edge, instead of the trailing.
        function debounce(func, wait, immediate) {
            var timeout;
            return function() {
                var context = this, args = arguments;
                var later = function() {
                    timeout = null;
                    if (!immediate) func.apply(context, args);
                };
                var callNow = immediate && !timeout;
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
                if (callNow) func.apply(context, args);
            };
        };


        /**
         * Open/close DOM tree node options
         */
        
        // toggle on icon click
        $scope.oxygenUIElement
        .on("mousedown", ".ct-more-options-icon", function(e) {

            var isExpanted = jQuery(this).parent().hasClass("ct-more-options-expanded");
                $scope.optionsToOpen = jQuery(this);

            // close all options
            jQuery(".ct-more-options-expanded", $scope.sidePanelElement).removeClass("ct-more-options-expanded");
            
            // open this option
            if ( !isExpanted ) {
                var timeout = $timeout(function(_self) {

                    $scope.optionsToOpen.parent().addClass("ct-more-options-expanded");
                    // cancel timeout
                    $timeout.cancel(timeout);
                }, 100, false);
            }
        })

        .on("click", ":not(.ct-more-options-icon)", function(e) {
            // close all options
            jQuery(".ct-more-options-expanded", $scope.sidePanelElement).removeClass("ct-more-options-expanded");
        })

        // fix for templates dropdown click in content edit mode
        .on("mousedown", ".oxygen-template-previewing-control", function(e) {
            $scope.disableContentEdit();
        })

        $scope.builderElement
        .on("click", '.ct-active:not([contenteditable="true"])', function(e) {
            $scope.disableContentEdit();
        })
        .on("click", '[contenteditable="true"]', function(e) {
            e.stopPropagation();
        })
        
        // This is not working as ng-click triggered first. TODO: find a fix
        // close on item click
        //.on("click", ".ct-more-options-expanded li", function(e) {
        //    jQuery(this).closest('.ct-node-options').removeClass("ct-more-options-expanded");
        //})

        // window resize
        jQuery(window).resize(function() {
            $scope.adjustViewportContainer();
            $scope.adjustCodeMirrorHeight();
        });

        jQuery(window).on('click', function(e) {

            if(jQuery(e.target).closest('.ct-active').length < 1 && 
                jQuery(e.target).closest('.oxygen-formatting-toolbar').length < 1 && 
                jQuery(e.target).closest('#ctdynamicdata-popup').length < 1)
                { 
                    $scope.disableContentEdit();
                }

            /*var clickedComponent = parseInt(jQuery(e.target).closest('.ct-component').attr('ng-attr-component-id'));
           
            $scope.iframeScope.component.active.id = clickedComponent;*/

            
            /*if(clickedComponent === 0) {
                $scope.activateComponent(0, 'root');
            } else if(clickedComponent > 100000) {
                $scope.activateComponent(clickedComponent, 'ct_inner_content');
            }*/
            
           // console.log($scope.iframeScope.component.active.id, parseInt(jQuery(e.target).closest('.ct-component').attr('ng-attr-component-id')));
            
        });


        /**
         * Show/hide Reusable button options
         */

        $scope.oxygenUIElement.on("click", ".oxygen-add-section-element", function(e) {
            jQuery(this).siblings().removeClass('oxygen-add-section-element-active');
            jQuery(this).toggleClass('oxygen-add-section-element-active');
        });
        
    }

	/**
	 * Updates dynamically the contents of the Custom Fields dropdown
	 *
	 * @since 2.0
	 */

    $scope.updateMetaDropdown = function( ) {
        var keys = $scope.current_post_meta_keys;
        var keysList = "";
        for( var i = 0; i < keys.length; i++ ) keysList += "<li ng-click=\"iframeScope.setOptionModel('meta_key','" + keys[ i ] + "');\">" + keys[ i ] + "</li>";
        jQuery( '.ct-ct_data_custom_field-meta_key' ).find('ul.ct-dropdown-list').html( $compile( keysList )($scope) );
    };


    /**
     * Init Select2 after $scope being loaded
     *
     * @since 2.0
     */

    $scope.initSelect2 = function(id, placeholder) {

        var select2 = jQuery("#"+id).select2({
            placeholder: placeholder,
        });

        var timeout = $timeout(function(_self) {
            select2.trigger('change');
            $timeout.cancel(timeout);
        }, 0, false);
    }

});


/**
 * Animation
 *
 * @since 0.2.2
 */


/**
 * Animate DOM Tree Details
 * 
 */
animateDOMTreeNodeDetails = function($window) {

    return {

        addClass: function(element, className, doneFn) {

            if (className!="ct-dom-tree-node-active") {
                doneFn();
                return false;
            }

            var details = jQuery(".ct-dom-tree-node-details", element);

            details.hide();
            details.stop().slideDown({
                duration: 250,
                easing: "linear",
                complete: function(){
                    doneFn();
                }
            });
        },

        removeClass: function(element, className, doneFn) {

            if (className!="ct-dom-tree-node-active") {
                doneFn();
                return false;
            }

            var details = jQuery(".ct-dom-tree-node-details", element);

            details.stop().slideUp({
                duration: 250,
                easing: "linear",
                complete: function(){
                    doneFn();
                }
            });
        },
    }
}

CTFrontendBuilderUI.animation('.ct-dom-tree-node-anchor', animateDOMTreeNodeDetails);


/**
 * Animate DOM Tree Node
 * 
 */
animateDOMTreeNode = function($window) {

    return {

        addClass: function(element, className, doneFn) {

            console.log("add",className);

            if (className!="ct-dom-tree-node-expanded") {
                doneFn();
                return false;
            }

            var details = jQuery(element).children(".ct-dom-tree-node");

            details.hide();
            details.stop().slideDown(250, function(){
                doneFn();
            });
        },
        removeClass: function(element, className, doneFn) {

            console.log("remove",className);

            if (className!="ct-dom-tree-node-expanded") {
                doneFn();
                return false;
            }

            var details = jQuery(element).children(".ct-dom-tree-node");

            details.stop().slideUp(250, function(){
                doneFn();
            });
        },
    }
}

CTFrontendBuilderUI.animation('.ct-dom-tree-node', animateDOMTreeNode);


/**
 * Animate DOM Tree Details
 * 
 */

animateStyleSetNode = function($window) {

    return {

        addClass: function(element, className, doneFn) {

            if (className!="ct-style-set-expanded") {
                doneFn();
                return false;
            }

            var details = jQuery(element).nextAll(".ct-style-set-child-selector");
            
            details.hide();
            details.stop().slideDown(250, function(){
                details.css("height","");
                doneFn();
            });
        },
        removeClass: function(element, className, doneFn) {

            if (className!="ct-style-set-expanded") {
                doneFn();
                return false;
            }

            var details = jQuery(element).nextAll(".ct-style-set-child-selector");
            
            details.stop().slideUp(250, function(){
                doneFn();
            });
        },
    }
}

// CTFrontendBuilderUI.animation('.ct-style-set-node', animateStyleSetNode);
// CTFrontendBuilderUI.animation('.ct-css-node-header', animateStyleSetNode);


/**
 * Disable ng-animate for elements with "ct-no-animate" class
 *
 * @since 0.2.2
 */

CTFrontendBuilderUI.config(['$animateProvider', function($animateProvider){
  // disable animation for elements with the ct-no-animate css class with a regexp.
  // note: "ct-*" is our css namespace
  $animateProvider.classNameFilter(/^((?!(ct-no-animate)).)*$/);
}]);


/**
 * Used to set componentize screenshots
 *
 */

CTFrontendBuilderUI.directive('fileModel', ['$parse', function ($parse) {
    return {
        restrict: 'A',
        link: function(scope, element, attrs) {
            var model = $parse(attrs.fileModel);
            var modelSetter = model.assign;
            
            element.bind('change', function(){
                scope.$apply(function(){
                    modelSetter(scope, element[0].files[0]);
                });
            });
        }
    };
}]);


CTFrontendBuilderUI.directive('oxygenResizableSidebar', function($timeout,$interval) {

        return {
            restrict: 'AE',
            link: function(scope, element, attr) {

                var style = window.getComputedStyle(element[0], null),
                    dir = ['right'],
                    w,
                    vx = 1, // if centered double velocity
                    vy = 1, // if centered double velocity
                    inner = '<span></span>',
                    start,
                    dragDir,
                    axis,
                    info = {};

                var getClientX = function(e) {
                    return e.touches ? e.touches[0].clientX : e.clientX;
                };

                var getClientY = function(e) {
                    return e.touches ? e.touches[0].clientY : e.clientY;
                };

                var dragging = function(e) {
                    var offset = axis === 'x' ? start - getClientX(e) : start - getClientY(e);
                    var newValue, prop;

                    switch(dragDir) {
                    
                        case 'right':
                            prop = 'width';
                            newValue = w - (offset * vx);
                            var button = jQuery('.oxygen-code-editor-expand', scope.verticalSidebar);
                            if (newValue<300) { 
                              // collapse
                              scope.verticalSidebar.data("expanded", false);
                              jQuery(button).text(jQuery(button).attr('data-expand'));
                              break;
                            }
                            else {
                              // expand
                              scope.verticalSidebar.data("expanded", true);
                              jQuery(button).text(jQuery(button).attr('data-collapse'));
                            }
                            console.log(offset, w);
                            element[0].style[prop] = w - (offset * vx) + 'px';
                            break;
                    }
                };
                var dragEnd = function(e) {

                    scope.adjustViewportContainer();
                    jQuery("#resize-overlay").hide();

                    document.removeEventListener('mouseup', dragEnd, false);
                    document.removeEventListener('mousemove', dragging, false);
                    document.removeEventListener('touchend', dragEnd, false);
                    document.removeEventListener('touchmove', dragging, false);
                    element.removeClass('rg-no-transition');
                };
                var dragStart = function(e, direction) {
                    
                    jQuery("#resize-overlay").show();

                    dragDir = direction;
                    axis = ( dragDir.indexOf('left') >= 0 || dragDir.indexOf('right') >= 0 ) ? 'x' : 'y';
                    start = axis === 'x' ? getClientX(e) : getClientY(e);
                    w = parseInt(style.getPropertyValue('width'));

                    //prevent transition while dragging
                    element.addClass('rg-no-transition');

                    document.addEventListener('mouseup', dragEnd, false);
                    document.addEventListener('mousemove', dragging, false);
                    document.addEventListener('touchend', dragEnd, false);
                    document.addEventListener('touchmove', dragging, false);

                    // Disable highlighting while dragging
                    if(e.stopPropagation) e.stopPropagation();
                    if(e.preventDefault) e.preventDefault();
                    e.cancelBubble = true;
                    e.returnValue = false;

                    scope.$apply();
                };

                dir.forEach(function (direction) {
                    var grabber = document.createElement('div');

                    // add class for styling purposes
                    grabber.setAttribute('class', 'rg-' + direction);
                    grabber.innerHTML = inner;
                    element[0].appendChild(grabber);
                    grabber.ondragstart = function() { return false; };

                    var down = function(e) {
                        var disabled = (scope.rDisabled === 'true');
                        if (!disabled && (e.which === 1 || e.touches)) {
                            // left mouse click or touch screen
                            dragStart(e, direction);
                        }
                    };
                    grabber.addEventListener('mousedown', down, false);
                    grabber.addEventListener('touchstart', down, false);
                });
            }
        };
    });


/**
 * Attach actions to content editor buttons
 *
 */

CTFrontendBuilderUI.directive('ctEditButton', function($timeout,$interval) {

    return {
        link:function(scope,element,attrs) {

            element.bind('mousedown', function(event) {

                event.preventDefault();
                
                var role = attrs.ngEditRole;
                
                switch(role) {
                    case 'link':
                        var sLnk=prompt('Write the URL','http:\/\/');
                        if(sLnk&&sLnk!=''){
                            scope.artificialViewport[0].contentWindow.document.execCommand('createlink', false, sLnk);
                        }
                    case 'p':
                        scope.artificialViewport[0].contentWindow.document.execCommand('formatBlock', false, role);
                        break;
                    default:
                        scope.artificialViewport[0].contentWindow.document.execCommand(role, false, null);
                        break;
                }
                scope.$apply();
                // timeout for angular
                var timeout = $timeout(function() {
                    scope.iframeScope.setOption(scope.iframeScope.component.active.id, scope.iframeScope.component.active.name, 'ct_content');
                    $interval.cancel(timeout);
                }, 0, false);
            })
        }
    }
})