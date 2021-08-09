/**
 * All Classes staff here
 * 
 */

CTFrontendBuilder.controller("ControllerOptions", function($scope, $parentScope, $timeout) {

    $scope.changesToApply = [];

    $scope.optionsHierarchy = {
        "background" : {
            
            "color"         : ["background-color"],
            "image"         : ["background","background-image","overlay-color","video_background","video_background_overlay"],
            "size"          : ["background-size","background-repeat","background-attachment","background-position-left","background-position-top",
                               "background-size-width","background-size-height","background-clip"],
        },

        "position" : {
            
            "margin_padding" : ["container-padding-top","container-padding-right","container-padding-bottom","container-padding-left",
                                "padding-top","padding-right","padding-bottom","padding-left",
                                "margin-top","margin-right","margin-bottom","margin-left",
                                // units
                                "container-padding-top-unit","container-padding-right-unit","container-padding-bottom-unit","container-padding-left-unit",
                                "padding-top-unit","padding-right-unit","padding-bottom-unit","padding-left-unit",
                                "margin-top-unit","margin-right-unit","margin-bottom-unit","margin-left-unit"],
            "size"           : ["width","min-width","max-width","height","min-height","max-height","section-width",
                                // units
                                "width-unit","min-width-unit","max-width-unit","height-unit","min-height-unit","max-height-unit"],
        },

        "layout" : {
            "position"       : ["display","float","visibility","clear","overflow","position","top","right","bottom","left",
                                // flex options
                                "flex-direction","align-items","justify-content","flex-wrap","flex-reverse","align-content","order"]
        },

        "typography" : {
            
            "typography"    : ["font-family","font-size","font-weight","color","text-align","line-height","letter-spacing","text-decoration","font-style","text-transform", "-webkit-font-smoothing"],
        },

        "borders" : {
            
            "border"        : ["border-top-color","border-top-style","border-top-width",
                               "border-right-color","border-right-style","border-right-width",
                               "border-bottom-color","border-bottom-style","border-bottom-width",
                               "border-left-color","border-left-style","border-left-width"],
            "radius"        : ["border-top-left-radius","border-top-right-radius",
                               "border-bottom-left-radius","border-bottom-right-radius","border-radius"]
        },

        "effects" : {
            "opacity" : ["opacity"],
            "transition" : ['transition-duration','transition-duration-unit','transition-timing-function','transition-delay','transition-delay-unit','transition-property',],
            "box-shadow" : ['box-shadow-inset','box-shadow-color','box-shadow-horizontal-offset','box-shadow-vertical-offset','box-shadow-blur','box-shadow-spread'],
            "text-shadow" : ['text-shadow-color','text-shadow-horizontal-offset','text-shadow-vertical-offset','text-shadow-blur'],
            "css-filter" : ['filter','filter-amount-blur','filter-amount-brightness','filter-amount-contrast','filter-amount-grayscale','filter-amount-hue-rotate','filter-amount-invert','filter-amount-saturate','filter-amount-sepia'],
        },

        "cssjs" : {
            
            "css"   : ["custom-css"],
            "js"    : ["custom-js"]
        },

        "code-php"  : ["code-php"],
        "code-css"  : ["code-css"],
        "code-js"   : ["code-js"]
    }

    $scope.builtinComponents = {};


	/**
     * Update all component's options inside a Components Tree
     * 
     * @since 0.1
     * @author Ilya K.
     */

    $scope.updateTreeComponentOptions = function(key, item, component) {
        
        if ($scope.log) {
            console.log('updateTreeComponentOptions()', key, item, component);
        }

        var componentDefaults   = $scope.defaultOptions[item.name],
            state               = $scope.currentState;

        if (componentDefaults["ct_content"]) {
            item.options["ct_content"] = componentDefaults["ct_content"];
        }

        return;

        // loop all component options
        angular.forEach($scope.component.options[component.id][state], function(value, parameter) {

            // include only options different from defaults and content
            if ( componentDefaults[parameter] != value 
            	|| parameter == 'ct_content' 
            	|| parameter == 'shortcode_tag' 
            	|| $scope.isEditing("media") 
            	|| $scope.isEditing("state") ) {
                
                if ( parameter == 'selector' || 
                     parameter == 'ct_id' || 
                     parameter == 'ct_parent' ||
                     parameter == 'ct_content' ||
                     parameter == 'classes' || 
                     ( parameter == "url" && component.isShortcode ) ) 
                {
                    item.options[parameter] = value;
                }
                else 
                    // add state option
                    if ( state ) {

                        if ( !item.options[state] ) {
                            item.options[state] = {};
                        }

                        item.options[state][parameter] = value;
                    }
            }
            else {
                if ( !item.options[state] ) {
                    // nothing here
                } else {
                    delete item.options[state][parameter];
                }
            }
        });
    }


    /**
     * Update one single component's option inside a Components Tree
     * 
     * @since 0.1.7
     * @author Ilya K.
     */

    $scope.updateTreeComponentOption = function(key, item, component) {
        
        if ($scope.log) {
            console.log('updateTreeComponentOption()', key, item, component);
        }

        var componentDefaults   = $scope.defaultOptions[item.name],
            state               = $scope.currentState,
            parameter           = component.optionName,
            value               = "";

        if ( $scope.optionsWhiteList.indexOf(parameter) === -1 && 
             $scope.optionsWhiteListNoMedia.indexOf(parameter) === -1 ) {
            state = 'original';
        }

        // force "hover" state for "fake_" option
        if (component.optionName.indexOf("fake_")>=0 && component.optionName.indexOf("hover_")>=0) {
            state = "hover";
            parameter = parameter.replace("fake_","");
        }

        if ( parameter == 'selector') {
            value = $scope.component.options[component.id][parameter];
        }
        else {
            if ( $scope.isEditing("id") && !$scope.isEditing("state") && !$scope.isEditing("media") ) {
                value = $scope.component.options[component.id]['id'][parameter];
            }
            else {
                value = $scope.component.options[component.id]["model"][parameter];   
            }
        }

        // change parameter to "color" for "hover_color" option
        if (component.optionName.indexOf("fake_")>=0 && component.optionName.indexOf("hover_")>=0) {
            parameter = parameter.replace("hover_","");
        }

        // handle column width option
        if ( component.tag == "ct_column" && parameter == "width" ) {
            
            if ( !item.options[state] ) {
                item.options[state] = {};
            }
            // update tree
            item.options[state][parameter] = value;

            // update options object
            $scope.component.options[component.id][state][parameter] = value;
            
            return true;
        }

        /**
         * Update current Class
         */

        if ( $scope.isEditing('class') && (
            $scope.optionsWhiteList.indexOf(parameter) >= 0 || 
            $scope.optionsWhiteListNoMedia.indexOf(parameter) >= 0 ||
            // exception for Icons and Buttons
            parameter == 'icon-style' || parameter == 'button-style')) {
            
            if ( $scope.classes[$scope.currentClass] ) {
                
                // clear class cache
                $scope.cache.classStyles[$scope.currentClass] = '';
                
                if (parameter=='icon-style' || parameter == 'button-style') {
                    // set icon-style only for decktop media/original state
                    $scope.classes[$scope.currentClass]['original'][parameter] = value;
                }
                // don't include not CSS options
                else if ( parameter == 'selector'    || 
                     parameter == 'ct_id'       || 
                     parameter == 'ct_parent'   || 
                     parameter == 'ct_content'  || 
                     parameter == 'classes'     || 
                     ( parameter == "url" && component.isShortcode ) ) 
                {
                   // nothing here
                }
                else {
                    // add option to class
                    if ( !$scope.isEditing('media') || 
                          $scope.optionsWhiteListNoMedia.indexOf(parameter) >= 0 ) {

                        // init class state options
                        if (!$scope.classes[$scope.currentClass][state]) {
                            $scope.classes[$scope.currentClass][state] = {};
                        }

                        // remove empty options
                        if (value == "") {
                            delete $scope.classes[$scope.currentClass][state][parameter];
                        }
                        else {
                            $scope.classes[$scope.currentClass][state][parameter] = value;
                        }

                    }
                    else {
                        // init class media options
                        if (!$scope.classes[$scope.currentClass]['media']) {
                            $scope.classes[$scope.currentClass]['media'] = {};
                        }
                        
                        if (!$scope.classes[$scope.currentClass]['media'][$scope.currentMedia]) {
                            $scope.classes[$scope.currentClass]['media'][$scope.currentMedia] = {};
                        }

                        if (!$scope.classes[$scope.currentClass]['media'][$scope.currentMedia][state]) {
                            $scope.classes[$scope.currentClass]['media'][$scope.currentMedia][state] = {};
                        }

                        // remove empty options
                        if ( value == "" ) {
                            delete $scope.classes[$scope.currentClass]['media'][$scope.currentMedia][state][parameter];
                        } 
                        else {
                            $scope.classes[$scope.currentClass]['media'][$scope.currentMedia][state][parameter] = value;
                        }
                    }
                }
            }
        }
        else

        /**
         * Update Media in Components Tree
         */

        if ( $scope.isEditing('media') &&
                $scope.optionsWhiteList.indexOf(parameter) >= 0 && 
                // skip other not CSS options
                parameter != 'selector'   && 
                parameter != 'ct_id'      && 
                parameter != 'ct_parent' 
            ) {

            // update media parameter in $scope.component.options
            $scope.setMediaParameter(component.id, parameter, value, state);

            // init media state if not exist
            if ( !item.options['media'] ) {
                item.options['media'] = {};
            }
            if ( !item.options['media'][$scope.currentMedia] ) {
                item.options['media'][$scope.currentMedia] = {};
            }
            if ( !item.options['media'][$scope.currentMedia][state] ) {
                item.options['media'][$scope.currentMedia][state] = {};
            }

            // remove from Tree if empty
            if ( value == "" || value == undefined ) {

                // remove property
                delete item.options['media'][$scope.currentMedia][state][parameter];
                delete $scope.component.options[component.id]['media'][$scope.currentMedia][state][parameter];
                
                // remove state if empty
                if ($scope.isObjectEmpty(item.options['media'][$scope.currentMedia][state])){
                    delete item.options['media'][$scope.currentMedia][state];
                }
                // remove current media if empty
                if ($scope.isObjectEmpty(item.options['media'][$scope.currentMedia])){
                    delete item.options['media'][$scope.currentMedia];
                }
                // remove media if empty
                if ($scope.isObjectEmpty(item.options['media'])){
                    delete item.options['media'];
                }
            } 
            // add in Tree
            else {
                item.options['media'][$scope.currentMedia][state][parameter] = value;
            }
        }

        /**
         * Update Component Tree
         */
        
        else {

            // add state for options if not exist
            if (!$scope.component.options[component.id][state]){
                $scope.component.options[component.id][state] = {};
            }

            // add state for component if not exist
            if (!item.options[state]){
                item.options[state] = {};
            }
            // set allowed empty options even if value is empty
            if ($scope.allowedEmptyOptions.indexOf(parameter)>-1 && state == "original") {
                $scope.component.options[component.id][state][parameter] = value;
            } 
            else 
            // set option to default for original state if value is empty
            if (!value && state == "original") {
                $scope.component.options[component.id][state][parameter] = componentDefaults[parameter]
            }
            // set to current value
            else {
                if (value == "" ) {
                    delete $scope.component.options[component.id][state][parameter];
                }
                else {
                    $scope.component.options[component.id][state][parameter] = value;
                }
            }
            
            // check units (px, em, etc)
            if ( parameter.indexOf("-unit") > 0 ) {

                var unitOption = parameter.replace("-unit", "");

                // delete both
                if ( $scope.component.options[component.id][state][parameter] == componentDefaults[parameter] &&
                     $scope.component.options[component.id][state][unitOption] == componentDefaults[unitOption] &&
                     !$scope.component.options[component.id]["id"][unitOption])
                {
                    delete item.options[state][parameter];
                    delete item.options[state][unitOption];
                }
                else
                // delete only unit
                if ( $scope.component.options[component.id][state][parameter] == componentDefaults[parameter] ) {
                    delete item.options[state][parameter];
                }
                // add both
                else {
                    item.options[state][parameter] = value;
                    item.options[state][unitOption] = $scope.component.options[component.id][state][unitOption];
                }  
            }
            else
            // check options with units (font-size, etc)
            if ( componentDefaults[parameter+"-unit"] !== undefined &&
                 componentDefaults[parameter+"-unit"] !== $scope.component.options[component.id][state][parameter+"-unit"] ) {
                    
                    if ( value != "" && value != undefined ) {
                        item.options[state][parameter] = value;
                    }
                    else {
                        delete item.options[state][parameter];
                        delete $scope.component.options[component.id][state][parameter];

                        // set back to default value
                        if (state == "original") {
                            $scope.component.options[component.id][state][parameter] = componentDefaults[parameter];
                        }
                    }
            }
            else
                // handle content to replace span HTML with placeholders
                if ( parameter == 'ct_content' ) {

                    var parentComponent     = $scope.getComponentById(component.id),
                        isContentEditable   = false;
                    
                    if ( parentComponent ) {
                        isContentEditable = parentComponent.attr('contenteditable');
                    }

                    // check if component is contenteditable
                    if ( isContentEditable ) {
                    
                        var element         = angular.element("<span>"+value+"</span>"),
                            haveComponents  = false;

                        // loop all child nodes
                        angular.forEach(element.find("*"), function(child) {

                            var childNode   = angular.element(child),
                                componentId = childNode.attr('ng-attr-component-id');

                            // if child is a component
                            if ( componentId ) {

                                childNode.replaceWith("<span id=\"ct-placeholder-"+componentId+"\"></span>");
                                haveComponents = true;
                            }
                        });

                        // update tree value
                        if ( haveComponents ) {
                            item.options[parameter] = element.prop('innerHTML');
                        } 
                        else {
                            item.options[parameter] = value;
                        }
                    }
                    else {
                        item.options[parameter] = value;
                    }
                } 
                // handle options other than "ct_content"
                else {
                    // not CSS options
                    if ( parameter == 'selector'    || 
                         parameter == 'ct_id'       || 
                         parameter == 'ct_parent'   ||
                         parameter == 'classes'     || 
                         ( parameter == "url" && component.isShortcode ) ) 
                    {
                        item.options[parameter] = value;
                    }
                    else 
                        // handle state's option
                        if (state) {

                            if ((value === "" || value === undefined) && $scope.allowedEmptyOptions.indexOf(parameter)===-1) {
                                delete item.options[state][parameter];
                            }
                            else {
                                item.options[state][parameter] = value;
                            }
                        }
                    }
        }
    }


    /**
     * Change component tag
     * 
     * @since 0.3.1
     * @author Ilya K.
     */

    $scope.updateTreeComponentTag = function(id, item, newTag) {
        
        // change tag
        item.name = newTag;
        
        // update active name
        if ( $scope.component.active.id == id ) {
            $scope.component.active.name = newTag;
        }

        // lets also update nicename if exists
        
        if($scope.component.options[id]['nicename'] && $scope.component.options[id]['nicename'].trim() !== '')
            $scope.component.options[item.id]['nicename'] = $scope.calcDefaultComponentTitle(item);

        // rebuild DOM and Tree Navigator
        var timeout = $timeout(function() {
            
            $scope.rebuildDOM(id);
            $scope.updateDOMTreeNavigator(id);
            $timeout.cancel(timeout);
        }, 0, false);
    }

    
    /**
     * Apply component's default options
     * 
     * @since 0.1.7
     */
    
    $scope.applyComponentDefaultOptions = function(id, componentName, component) {

        if ($scope.log) {
            console.log('applyComponentDefaultOptions()', id, componentName);
        }

        // init component options
        if ( !$scope.component.options[id] ) {
            $scope.component.options[id] = {};
        }
        if ( !$scope.component.options[id]['original'] ) {
            $scope.component.options[id]['original'] = {};
        }
        if ( !$scope.component.options[id]['id'] ) {
            $scope.component.options[id]['id'] = {};
        }
            
        // set default options
        for(var name in $scope.defaultOptions[componentName]) { 

            if ($scope.defaultOptions[componentName].hasOwnProperty(name)) {
                
                var value = $scope.defaultOptions[componentName][name];

                // update 'original'
                $scope.component.options[id]['original'][name] = value;

                // load web fonts
                if ( name == "font-family"||name.indexOf("font-family")>-1 ) {
                    $scope.loadWebFont(value);
                }
            }
        }

        // set component selector
        if (!$scope.component.options[id]['selector']) {
            $scope.component.options[id]['selector'] = componentName.slice(3) + "-" + id + "-" + CtBuilderAjax.postId;
        }

        // set component name
        if (!$scope.component.options[id]['name']) {
            $scope.component.options[id]['name'] = componentName;
        }

        // set component category
        if(component && component.options && component.options['ct_category']) {
            $scope.component.options[id]['ct_category'] = component.options['ct_category'];
        }
        // update model
        $scope.component.options[id]['model'] = angular.copy($scope.component.options[id]['original']);
    }

 
    /**
     * Apply components options saved in Components Tree
     * 
     * @since 0.1.8
     * @author Ilya K.
     */
    
    $scope.applyComponentSavedOptions = function(id, componentTreeItem) {

        if ($scope.log) {
            console.log("applyComponentSavedOptions()", id, componentTreeItem);
        }

        // loop component's states
        for(var stateName in componentTreeItem.options) { 
            if (componentTreeItem.options.hasOwnProperty(stateName)) {
                
                var stateOptions = componentTreeItem.options[stateName];

                // check global fonts and delete if not exist
                $scope.checkGlobalFont(id, componentTreeItem);

                // use original options by default
                if (typeof stateOptions === 'object' && stateName != "classes") {

                    if ( !$scope.component.options[id][stateName] ) {
                        $scope.component.options[id][stateName] = {};
                    }

                    // loop state's options
                    for(var optionName in stateOptions) { 
                        if (stateOptions.hasOwnProperty(optionName)) {
                            
                            var optionValue = stateOptions[optionName];

                            // save 'id' options to check later against defaults
                            if ( stateName == "original" ) {
                                $scope.component.options[id]["id"][optionName] = optionValue;
                            }

                            $scope.component.options[id][stateName][optionName] = optionValue;

                            if ($scope.log) {
                                //console.log(stateName, optionName, optionValue);
                            }

                            if ( optionName == "font-family"||optionName.indexOf("font-family")>-1 ) {
                                $scope.loadWebFont(optionValue);
                            }   
                        }
                    }
                }
                else if (typeof stateOptions !== 'object') {

                    /*if ( stateName == "ct_content" ) {
                        // replace template tags if found
                        stateOptions = $scope.filterTemplateTags(stateOptions, id);
                    }*/

                    if ( stateName == "selector" ) {
                        $scope.component.options[id][stateName] = stateOptions;
                    }
                    else {
                        $scope.component.options[id]['original'][stateName] = stateOptions;
                    }
                }
            }
        }
        
        // update model
        $scope.component.options[id]['model'] = angular.copy($scope.component.options[id]['original']);

        // mark component if built in
        if (componentTreeItem.options.oxy_builtin) {
            $scope.builtinComponents[id] = true;
        }
    }


    /**
     * Apply all options to model based on current class, state and media
     * 
     * @since 0.3.2
     * @author Ilya K.
     */
    
    $scope.applyModelOptions = function(id, tag) {

        if (undefined===id) {
            id = $scope.component.active.id;
        }
        
        if (undefined===tag) {
            tag = $scope.component.active.name;
        }

        if ($scope.log) {
            console.log("applyModelOptions()", id, tag);
        }

        // no model for root
        if (id===0||id==="0") {
            return;
        }
        
        // init options
        if (!$scope.component.options[id]) {
            $scope.component.options[id] = [];
        }

        // clear model
        $scope.component.options[id]['model'] = {}

        // apply id's 'original'
        angular.extend( $scope.component.options[id]['model'],
                        $scope.component.options[id]['original'])

        /**
         * ID
         */
        
        if ($scope.isEditing("id")) {
            
            if ($scope.isEditing("media")) {

                $scope.component.options[id]['model'] = {};

                if ($scope.component.options[id]['media'] &&
                    $scope.component.options[id]['media'][$scope.currentMedia]) {
                    $scope.component.options[id]['model'] = angular.copy($scope.component.options[id]['media'][$scope.currentMedia][$scope.currentState] || {})
                }
            }
            else {
               
                if ($scope.isEditing("state")) {
                    $scope.component.options[id]['model'] = angular.copy($scope.component.options[id][$scope.currentState] || {})
                }
            }
        }

        /**
         * Class
         */

        if ($scope.isEditing("class") && typeof($scope.classes[$scope.currentClass]) == "object") {

            $scope.component.options[id]['model'] = angular.copy($scope.classes[$scope.currentClass]['original'] || {});

            if ($scope.isEditing("media")) {

                $scope.component.options[id]['model'] = {};

                if ($scope.classes[$scope.currentClass]['media'] &&
                    $scope.classes[$scope.currentClass]['media'][$scope.currentMedia]) {
                    $scope.component.options[id]['model'] = angular.copy($scope.classes[$scope.currentClass]['media'][$scope.currentMedia][$scope.currentState] || {})
                }

                for(var optionName in $scope.classes[$scope.currentClass][$scope.currentState]) {
                    if ($scope.classes[$scope.currentClass][$scope.currentState].hasOwnProperty(optionName) ) {
                        if ($scope.optionsWhiteListNoMedia.indexOf(optionName)>-1) {
                            $scope.component.options[id]['model'][optionName] = 
                            $scope.classes[$scope.currentClass][$scope.currentState][optionName];
                        }
                    }
                }
            }
            else {
                $scope.component.options[id]['model'] = {};

                if ($scope.classes[$scope.currentClass] &&
                    $scope.classes[$scope.currentClass][$scope.currentState]) {

                    $scope.component.options[id]['model'] = angular.copy($scope.classes[$scope.currentClass][$scope.currentState] || {})
                }
            }
        }

        /**
         * Custom selector
         */

        if ($scope.isEditing("custom-selector") && !$scope.isEditing("class")) {
            
            if ($scope.customSelectors[$scope.selectorToEdit]){
                //$scope.component.options[id]['model'] = angular.copy($scope.customSelectors[$scope.selectorToEdit][$scope.currentState] || {});

                if ($scope.isEditing("media")) {

                    $scope.component.options[id]['model'] = {};

                    if ($scope.customSelectors[$scope.selectorToEdit]['media'] &&
                        $scope.customSelectors[$scope.selectorToEdit]['media'][$scope.currentMedia]) {
                        $scope.component.options[id]['model'] = angular.copy($scope.customSelectors[$scope.selectorToEdit]['media'][$scope.currentMedia][$scope.currentState] || {})
                    }
                }
                else {
                    $scope.component.options[id]['model'] = {};

                    if ($scope.customSelectors[$scope.selectorToEdit] &&
                        $scope.customSelectors[$scope.selectorToEdit][$scope.currentState]) {

                        $scope.component.options[id]['model'] = angular.copy($scope.customSelectors[$scope.selectorToEdit][$scope.currentState] || {})
                    }
                }
            }
        }

        // load fonts
        for(var name in $scope.component.options[id]['model']) {
            if ($scope.component.options[id]['model'].hasOwnProperty(name) && (name=="font-family")||name.indexOf('font-family')>-1) {
                $scope.loadWebFont($scope.component.options[id]['model'][name]);
            }
        }

        // check units
        for(var name in $scope.component.options[id]['model']) {
            if ($scope.component.options[id]['model'].hasOwnProperty(name) && $scope.component.options[id]['model'][name+"-unit"]) {
                
                // only if editing "id"
                if ($scope.isEditing("id") && !$scope.isEditing("media") && !$scope.isEditing("state")) {
            
                    if ($scope.component.options[id]['model'][name+"-unit"] != $scope.defaultOptions[tag][name+"-unit"] &&
                        !$scope.component.options[id]['id'][name] ){
                        
                        delete $scope.component.options[id]['model'][name];
                    }
                }
            }
        }

        if (!$scope.isEditing("custom-selector")) {

            for(var optionName in $scope.component.options[id]['original']) {
                if ($scope.component.options[id]['original'].hasOwnProperty(optionName) ) {
                    if ($scope.optionsWhiteList.indexOf(optionName)===-1) {
                        if ($scope.isEditing("class")&&(optionName=="icon-style"||optionName=="button-style")) {
                            $scope.component.options[id]['model'][optionName] = 
                            $scope.classes[$scope.currentClass]['original'][optionName];
                        }
                        else {
                            if ($scope.isEditing("class")&&$scope.optionsWhiteListNoMedia.indexOf(optionName)>-1){
                                // these options already added above
                            }
                            else {
                                $scope.component.options[id]['model'][optionName] = 
                                $scope.component.options[id]['original'][optionName];
                            }
                        }
                    }
                }
            }
        }

        // Load global defaults
        if (tag=="ct_section") {
            sectionOptions = [
                        'container-padding-top',
                        'container-padding-top-unit',
                        'container-padding-bottom',
                        'container-padding-bottom-unit',
                        'container-padding-left',
                        'container-padding-left-unit',
                        'container-padding-right',
                        'container-padding-right-unit']
            
            for(var key in sectionOptions) {
                if (sectionOptions.hasOwnProperty(key) ) {
                    optionName = sectionOptions[key];
                    if ($scope.component.options[id]['model'][optionName]==undefined || $scope.component.options[id]['model'][optionName]=="") {
                        $scope.component.options[id]['model'][optionName]=$scope.globalSettings.sections[optionName];
                    }
                }
            }
        }
    }


    /**
     * Set Component Options for Components Tree, 
     * update CSS and render shortcode if needed
     * 
     * @since 0.1
     * @author Ilya K.
     */

    $scope.setOption = function(id, tag, optionName, isShortcode, notUpdateCSS) {
        $scope.cancelDeleteUndo();

        if ($scope.log) {
            console.log("setOption() '" + optionName + "' for '" + tag + "' id:" + id + " tag to '" + $scope.component.options[id]['model'][optionName] + "'");
            $scope.functionStart("setOption()");
        }

        if ($scope.component.options[id]['model'][optionName]==null) {
            $scope.component.options[id]['model'][optionName] = "";
        }

        $scope.adjustResizeBox();

        // disable selector detector mode
        if ($parentScope.disableSelectorDetectorMode) {
            $parentScope.disableSelectorDetectorMode();
        }

        if(tag === 'ct_video' && optionName === 'src') {

            if($scope.component.options[id]['model'][optionName].indexOf('[oxygen') !== -1) {
                // resolve url from the oxy shortcode
            
                var callback = function(contents) {
                    $scope.setOptionModel("embed_src",$scope.getYoutubeVimeoEmbedUrl(contents.trim()),id,tag);
                }

                $scope.applyShortcodeResults(id, $scope.component.options[id]['model'][optionName], callback);

            }
            else {
                $scope.setOptionModel("embed_src",$scope.getYoutubeVimeoEmbedUrl($scope.component.options[id]['model'][optionName]),id,tag);
            }
        }

        // update 'id' options
        if ( $scope.isEditing("id") && !$scope.isEditing("media") && !$scope.isEditing("state")) {
            
            if ($scope.component.options[id]['model'][optionName]) {

                // init 'id' options if not defined
                if (!$scope.component.options[id]["id"]) {
                    $scope.component.options[id]["id"] = {};
                }
                $scope.component.options[id]["id"][optionName] = $scope.component.options[id]['model'][optionName];
            }
            else {
                if($scope.component.options[id]['model'][optionName]=="") {

                    // delete empty values and set model to default    
                    if ($scope.allowedEmptyOptions.indexOf(optionName) === -1) {
                        
                        delete $scope.component.options[id]["id"][optionName];

                        // check unit options
                        if ( $scope.component.options[id]["model"][optionName+'-unit'] ) {
                            if ( $scope.component.options[id]["model"][optionName+'-unit'] == $scope.defaultOptions[tag][optionName+'-unit'] ){
                                 
                                 $scope.component.options[id]["model"][optionName]       = $scope.defaultOptions[tag][optionName];
                                 $scope.component.options[id]["original"][optionName]    = $scope.defaultOptions[tag][optionName];
                            }
                        }
                        else 
                            if (optionName!="ct_content") {
                                $scope.component.options[id]["model"][optionName]       = $scope.defaultOptions[tag][optionName];
                                $scope.component.options[id]["original"][optionName]    = $scope.defaultOptions[tag][optionName];
                            }
                    }
                    else {
                        if (!$scope.component.options[id]["id"]) {
                            $scope.component.options[id]["id"] = {};
                        }
                        
                        $scope.component.options[id]["id"][optionName] = $scope.component.options[id]['model'][optionName];
                    }
                }
            }
        }

        // check for empty class name
        if ( $scope.isEditing('class') && $scope.currentClass == "" ) {
            alert("Please choose the class from the list or set a new one.");
            $scope.functionEnd("setOption()");
            return false;
        }

        // make sure column width is no more than 100 and no less than 0
        if ( tag == "ct_column" && optionName == "width" ) {
            if ( $scope.component.options[id]['model'][optionName] > 100 ) {
                $scope.component.options[id]['model'][optionName] = 100;
            }
            if ( $scope.component.options[id]['model'][optionName] < 0 ) {
                $scope.component.options[id]['model'][optionName] = 0;
            }
        }

        // handle fake "border-all-option"
        if ( optionName && optionName.indexOf("border-all") > -1 ) {
            $scope.setOptionModel(optionName.replace("all","top"),      $scope.component.options[id]['model'][optionName]);
            $scope.setOptionModel(optionName.replace("all","right"),    $scope.component.options[id]['model'][optionName]);
            $scope.setOptionModel(optionName.replace("all","bottom"),   $scope.component.options[id]['model'][optionName]);
            $scope.setOptionModel(optionName.replace("all","left"),     $scope.component.options[id]['model'][optionName]);
            $scope.functionEnd("setOption()");
            return;
        }

        // make sure flex direction is not turned on for display block and vice versa
        if ( optionName == "flex-direction" && (
            $scope.component.options[id]['model'][optionName] == "column" ||
            $scope.component.options[id]['model'][optionName] == "row") ) {
            var timeout = $timeout(function() {
                $scope.setOptionModel("display","flex",id,tag)
                $timeout.cancel(timeout);
            }, 0, false);
        }
        if ( optionName == "display" && $scope.component.options[id]['model'][optionName] != "flex" ) {
            $scope.setOptionModel("flex-direction","unset",id,tag);
        }

        // update model if container padding unset
        if (["container-padding-top","container-padding-bottom","container-padding-left","container-padding-right"].indexOf(optionName)>=0
            &&$scope.component.options[id]['model'][optionName]=="") {
            var timeout = $timeout(function() {
                $scope.applyModelOptions(id, tag);
                $scope.parentScope.safeApply();
                $timeout.cancel(timeout);
            }, 0, false);
        }

        // update render Gallery component on Layout change automatically
        if ((["layout","link","lightbox","image_aspect_ratio","images_per_row","space_around_image","image_min_width",
            "gallery_captions","caption_color","caption_background_color","gallery_captions_only_hover","hide_captions_below",
            "image_opacity_hover","image_opacity","transition_duration"].indexOf(optionName)>-1)&&tag=="oxy_gallery") {
            var timeout = $timeout(function() {
                $scope.renderComponentWithAJAX('oxy_render_gallery');
                $timeout.cancel(timeout);
            }, 0, false);
        }

        // update Tabs oxy-tabs-contents-wrapper data to keep those connected
        if (optionName=="selector"&&tag=="oxy_tabs_contents") {
            var timeout = $timeout(function() {
                var tabsSelector    = jQuery($scope.getComponentById(id)).data('oxy-tabs-wrapper'),
                    tabsID          = jQuery("#"+tabsSelector).attr('ng-attr-component-id');

                $scope.setOptionModel('tabs_contents_wrapper', $scope.component.options[id]["selector"], tabsID, "oxy_tabs");

                $scope.$apply();
                $timeout.cancel(timeout);
            }, 0, false);
        }
        if (optionName=="selector"&&tag=="oxy_tabs") {
            var timeout = $timeout(function() {
                var tabsSelector    = jQuery($scope.getComponentById(id)).data('oxy-tabs-contents-wrapper'),
                    tabsID          = jQuery("#"+tabsSelector).attr('ng-attr-component-id');

                $scope.setOptionModel('tabs_wrapper', $scope.component.options[id]["selector"], tabsID, "oxy_tabs_contents");

                $scope.$apply();
                $timeout.cancel(timeout);
            }, 0, false);
        }

        // render component on "dont_render" param update for widget. For shortcode this is done automatically 
        if (optionName=="dont_render"&&tag=="ct_widget") {
            var timeout = $timeout(function() {
                $scope.renderWidget(id,false)
            $timeout.cancel(timeout);
            }, 0, false);
        }
        
        // rebuild social icons on network update
        if ( tag == "oxy_social_icons" && optionName && $scope.socialIcons.networks.indexOf(optionName.replace('icon-','')) >= 0 ) {
            if (
                  $scope.component.options[id]['model'][optionName] == "" ||
                ( $scope.component.options[id]['model'][optionName] != "" && $scope.component.options[id]['original'][optionName] == "" )
            ) {
                var timeout = $timeout(function() {
                    $scope.rebuildDOM(id);
                    $timeout.cancel(timeout);
                }, 0, false);
            }
        }

        // rebuild progress bar to show updated animation
        if ( ["progress_bar_animation_stripes","progress_bar_animate_width","progress_bar_animation_stripes_duration","progress_bar_animation_width_duration"].indexOf(optionName) > -1 ) {
            var timeout = $timeout(function() {
                $scope.rebuildDOM(id);
                $timeout.cancel(timeout);
            }, 0, false);
        }

        if (optionName=="soundcloud_url") {
            $scope.getSoundCloudTrackID($scope.component.options[id]['model'][optionName]);
        }

        var component = {
            id: id,
            tag: tag,
            optionName: optionName,
            isShortcode: isShortcode,
            isData: tag.lastIndexOf('ct_data', 0) === 0 // ugly hack
        }

        // update custom-selector options
        if ( $scope.isEditing('custom-selector') ) {

            //$scope.selectorDetector.mode = false;
            
            var parameter   = component.optionName,
                value       = $scope.component.options[component.id]['model'][parameter];
            
            // don't include not CSS options
            if ( parameter == 'selector'    || 
                 parameter == 'ct_id'       || 
                 parameter == 'ct_parent'   ||
                 parameter == 'ct_content'  || 
                 parameter == 'classes'     || 
                 ( parameter == "url" && component.isShortcode ) ) 
            {
               // nothing here
            }
            else {
                $scope.updateCustomSelectorValue(parameter, value);
                delete $scope.cache.classStyles[$scope.currentClass];
                $scope.outputCSSOptions();
                $scope.unsavedChanges();

                $scope.functionEnd("setOption()");
                return;
            }
        }
        // update Components Tree
        else if ( optionName !== undefined ) {
            $scope.findComponentItem($scope.componentsTree.children, id, $scope.updateTreeComponentOption, component);
            // if fake "hover_" option also update actual option for hover state
            if ( optionName.indexOf("hover_")>=0 && tag != "oxy_posts_grid") {
                component.optionName = "fake_" + component.optionName;
                $scope.findComponentItem($scope.componentsTree.children, id, $scope.updateTreeComponentOption, component);
            }

        } else {
            // update all options if option name is not set
            $scope.findComponentItem($scope.componentsTree.children, id, $scope.updateTreeComponentOptions, component);
        }

        $scope.unsavedChanges();

        // render shortcode
        if ( isShortcode ) {
            $scope.renderShortcode(id, tag);
        }

		// render data component
		if ( component.isData ) {
			$scope.renderDataComponent(id, tag);
		}

        // don't update CSS options when editing content
        if ( optionName == "ct_content" || notUpdateCSS ) {
            $scope.functionEnd("setOption()");
            return;
        }

        // update styles
        $scope.outputCSSOptions(id);

        // if there is no active selector on the current component, have it display ID as current selector
        if($scope.isNotSelectedYet(id) && typeof(optionName) !== 'undefined') // && optionName !== 'ct_content')
            $scope.switchEditToId(true);

        $scope.functionEnd("setOption()");
    }


    /**
     * Set option model value and update Tree
     *
     * @since 0.3.0
     * @author Ilya K.
     */
    
    $scope.setOptionModel = function(optionName, optionValue, id, name, notUpdateCSS) {

        if (undefined === id) {
            id = $scope.component.active.id;
        }
        if (undefined === name) {
            name = $scope.component.active.name;
        }

        //if($scope.isEditing('class')) {
            $scope.classesCached = false;
        //}

        if ($scope.log) {
            console.log("setOptionModel()", optionName, optionValue, id, name, notUpdateCSS);
            $scope.functionStart("setOptionModel()");
        }
        
        // update model
        $scope.component.options[id]['model'][optionName] = optionValue;

        // update Tree
        $scope.setOption(id, name, optionName, false, notUpdateCSS);

        $scope.functionEnd("setOptionModel()");
    }


    /**
     * Get option from model
     *
     * @since 0.3.0
     * @author Ilya K.
     * @return {string}
     */
    
    $scope.getOption = function(optionName, id) {

        if (undefined === id) {
            id = $scope.component.active.id;
        }

        if ( $scope.component.options[id]['model'] && $scope.component.options[id]['model'][optionName] !== undefined ) {
            return $scope.component.options[id]['model'][optionName];
        }
        else {
            return "";
        }
    }


    /**
     * Get Component's CSS options by id
     * 
     * @since 0.1
     * @author Ilya K.
     * @return {Array} [key values pairs of CSS properties]
     */

    $scope.getCSSOptions = function(id, stateName, customOptions, componentName) {
        
        var customCss = [],
            options = {};

        // use passed options (for classes and custom selectors)
        if ( customOptions ) {
            options = angular.copy(customOptions);
        }
        // or get component options
        else {
            if (undefined === id) {
                return {};
            }
            if (undefined === stateName) {
                stateName = $scope.currentState;
            }
            if ($scope.component.options[id]) {
                options = angular.copy($scope.component.options[id][stateName]);
            }
        }

        options = options || {};
        
        if ($scope.log) {
            console.log("getCSSOptions()", id, stateName, options);
        }

        // handle background-image option
        // if ( options['background-image'] ) {
            
        //     options['background-image'] = "url("+options['background-image']+")";

        //     // trick for overlay color
        //     if ( options['overlay-color'] ) {
        //         options['background-image'] = 
        //             "linear-gradient(" + options['overlay-color'] + "," + options['overlay-color'] + "), " + options['background-image'];
        //     }
        // }

        // handle options with units 
        for(var name in options) { 
            if (options.hasOwnProperty(name) && !(componentName == 'ct_video' && name == 'padding-bottom')) {

                if (name.indexOf('-unit') < 0) {

                    if (!componentName) {
                        // #ID
                        if (id && id > 0) {
                            componentName = $scope.component.options[id].name;    
                        }
                        // .class
                        else {
                            componentName = "all";
                        }
                    }

                    // skip options with no units
                    if ( undefined == $scope.defaultOptions[componentName][name+'-unit'] ) {
                        continue;
                    }

                    if (name == 'container-padding-top'||
                        name == 'container-padding-bottom'||
                        name == 'container-padding-left'||
                        name == 'container-padding-right') {
                        var unit = ( options[name+'-unit'] ) ? options[name+'-unit'] : $scope.globalSettings.sections[name+'-unit'];
                        if ( options[name] ) {
                            options[name] += unit;
                        }
                    }
                    else 
                    if (options[name+'-unit'] == 'auto') {
                        options[name] = 'auto';
                    }
                    else {
                        var unit = ( options[name+'-unit'] ) ? options[name+'-unit'] : $scope.defaultOptions[componentName][name+'-unit'];
                        if ( options[name] ) {
                            options[name] += unit;
                        }
                    }

                    delete options[name+'-unit'];
                }
                else {
                    if (options[name] == 'auto') {
                        options[name.replace("-unit", "")] = 'auto';
                    }
                }
            }
        }

        // delete all -unit options
        for(var name in options) { 
            if (options.hasOwnProperty(name) && name.indexOf("-unit") > 0) {
                delete options[name];
            }
        }

        // handle background-position option
        if ( options['background-position-top'] || options['background-position-left'] ) {

            var top   = options['background-position-top'] || "0%",
                left  = options['background-position-left'] || "0%";
            
            options['background-position'] = left;
            options['background-position'] += " " + top;
        }

        // remove fake properties
        options['background-position-top'] = null;
        options['background-position-left'] = null;

        // handle background-size option
        if ( options['background-size'] == "manual" ) {

            var width   = options['background-size-width'] || "auto",
                height  = options['background-size-height'] || "auto";
            
            options['background-size'] = width;
            options['background-size'] += " " + height;
        }

        // remove fake properties
        options['background-size-width'] = null;
        options['background-size-height'] = null;

        for(var name in options) { 
            if (options.hasOwnProperty(name) && name.indexOf("font-family") > -1 && options[name][0] == 'global') {
                if ( customOptions ) {
                    options[name] = $scope.getGlobalFont( options[name][1] );
                }
                else {
                    options[name] = $scope.getComponentFont(id, false, stateName); // TODO: add support for custom selectors
                }
            }
        }

        
        return options;
    }


    /**
     * Check if current component option is different from original
     *
     * @since 0.1.4
     */
    
    $scope.checkOptionChanged = function(id, name) {

        if ($scope.log) {
            //console.log("checkOptionChanged", id, name);
        }

        // TODO: make it work with custom selectors
        if ( $scope.isEditing('custom-selector') ) {
            return false;
        }

        var original = false,
            current  = false;

        // check if changed in current id's state
        if ( $scope.isEditing('state') && $scope.isEditing('id') ) {
        
            // get current state option value
            if ( $scope.component.options[id][$scope.currentState] ) {
                current = $scope.component.options[id][$scope.currentState][name];
            }
            
            // no state option
            if ( ! current ) {
                return false;
            }

            original = $scope.component.options[id]['original'][name];

            // check global fonts
            if ( name == "font-family" ) {
                if (    current && 
                        current[0]  == 'global' && 
                        original[0] == 'global' && 
                        current[1] == original[1] 
                    ) {
                    
                    return false;
                }
            }

            // check and return
            if ( original && current != original ) {
                return "ct-option-different";
            }
        }
        // check if changed in current class
        else {

            if ( $scope.classes[$scope.currentClass] && 
                 $scope.classes[$scope.currentClass][$scope.currentState] &&
                 $scope.classes[$scope.currentClass][$scope.currentState][name] ) 
            {
                return "ct-option-different";
            }
        }
    }


    /**
     * Check what user is currently editing
     *
     * @since 0.1.7
     * @return {bool}
     * @author Ilya K.
     */

    $scope.isEditing = function(query) {

        switch (query) {
            
            case "id" :
                return ( $scope.currentClass === false && $scope.component.active.id >= 0 ) ? true : false;
                break;

            case "class" :
                return ( $scope.currentClass !== false ) ? true : false;
                break;

            case "state" :
                return ( $scope.currentState != "original" ) ? true : false;
                break;

            case "pseudo-element" :
                return ( $scope.isPseudoElement($scope.currentState) ) ? true : false;
                break;

            case "custom-selector" :
                return ( $scope.selectorToEdit !== false ) ? true : false;
                break;

            case "style-sheet" :
                return ( $scope.stylesheetToEdit !== false && typeof($scope.stylesheetToEdit) !== 'undefined' ) ? true : false;
                break;

            case "media" :
                return ( $scope.currentMedia !== "default" ) ? true : false;
                break;

            default:
                return false;
        }
    }


    /**
     * Switch editing state to 'id'
     *
     * @since 0.1.7
     * @author Ilya K.
     */
    
    $scope.switchEditToId = function(explicitly) {
        
        if ($scope.log) {
            console.log('switchEditToId()', $scope.component.active.id)
        }

        var isEditingId = $scope.isEditing('id');

        $scope.setCustomSelectorToEdit(false);
        
        if(!$scope.activeSelectors[$scope.component.active.id])
            $scope.setCurrentClass(false);

        // if done explicitly via selecting from the selectors dropdown
        if(typeof(explicitly) !== 'undefined' && explicitly === true) {
            $scope.activeSelectors = $scope.activeSelectors || {};
            $scope.activeSelectors[$scope.component.active.id] = false;
            $scope.setCurrentClass(false);
        }

        $scope.switchState("original");
        $scope.showClasses = false;

        if ( isEditingId ) {
            return false;
        }
    }


    /** 
     * Switch editing state to 'id' and media to 'default'
     *
     * @since 1.0.1
     * @author Ilya K.
     */
    
    $scope.setEditingStateToDefault = function(media) {

        if ($scope.log) {
            console.log('setEditingStateToDefault()')
        }
        
        //if(!$scope.isEditing("id")) {
            $scope.switchEditToId(true);
        //}
        
        if(!media) {
            $scope.setCurrentMedia("default");
        }

        // safely apply scope
        var timeout = $timeout(function() {
            $scope.$apply();
            $timeout.cancel(timeout);
        }, 0, false);
    }


    /**
     * Called when any of the page settings changed
     *
     * @since 0.2.3
     */
    
    $scope.pageSettingsUpdate = function() {
        
        // update page container width media size
        $scope.mediaList["page-width"].maxSize = $scope.getPageWidth()+"px";
        $scope.mediaList["page-width"].title = "Page container ("+$scope.getPageWidth()+"px) and below";
        
        // update cache
        $scope.updateAllComponentsCacheStyles();
        // output CSS
        $scope.outputCSSOptions();
        $scope.outputPageSettingsCSS();

        $parentScope.adjustViewportContainer();
        
        $scope.unsavedChanges();
    }


    /**
     * Set option unit like 'px', 'em' for options like margin, padding, etc
     *
     * @since 0.3.0
     */
    
    $scope.setOptionUnit = function(option, unit, notUpdateCSS) {

        var optionName = option+"-unit",
            id = $scope.component.active.id,
            tag = $scope.component.active.name;

        // udpate model
        $scope.component.options[id]['model'][optionName] = unit;

        $scope.setOption(id, tag, optionName, false, notUpdateCSS);

        $scope.applyModelOptions();
    }


    /**
     * Get option unit like 'px', 'em' for options like margin, padding, etc
     *
     * @since 0.3.0
     * @return {string}
     */
    
    $scope.getOptionUnit = function(option) {

        var optionName = option+"-unit",
            id = $scope.component.active.id;

        if ( $scope.component.options[id]['model'] && $scope.component.options[id]['model'][optionName] ) {
        	return $scope.component.options[id]['model'][optionName];
        }
        else {
            return $scope.defaultOptions["all"][optionName];
        }
    }


    /**
     * Get component selector
     *
     * @since 0.3.0
     * @return {string}
     */

    $scope.getComponentSelector = function(id) {

        if (undefined == id) {
            id = $scope.component.active.id;
        }
        
        return $scope.component.options[id]['selector'];
    }


    /**
     * Apply component JS
     *
     * @since 0.3.1
     */

    $scope.applyComponentJS = function(id, name, updateTree) {

        $scope.applyingComponentJS = true;
        jQuery(".oxygen-code-error-container", $parentScope.oxygenUIElement).hide().html("");

        if ($scope.log) {
            console.log("applyComponentJS()", id, name);
        }

        if (undefined==id) {
            id = $scope.component.active.id;
        }

        if (undefined==name) {
            name = $scope.component.active.name;
        }

        if (undefined==updateTree) {
            updateTree = true;
        }

        if (updateTree) {
            $scope.setOption(id, name, 'custom-js', false, false);
        }

        var customJS = $scope.getOption('custom-js', id);

        // output if not equal to default value
        if ($scope.defaultOptions[name]['custom-js'] !== customJS ) {
            $scope.outputJSScript("ct_custom_js_", id, customJS);       
        }

        // We don't have Custom JS for any other states
        // output to DOM
        /*angular.forEach($scope.component.options[id], function(option, key) {
            if(key === 'media') {
                // media styles shouldn't have custom js
                angular.forEach(option, function(breakpoint, bpkey) {
                    angular.forEach(breakpoint, function(bpstate, statekey) {
                        if( bpstate['custom-js']){
                            //console.log(bpkey+" "+statekey);
                            $scope.outputJSScript("ct_custom_js_"+bpkey+"-"+statekey+"-", id, bpstate['custom-js']);
                        }
                    });
                });
            }
            else if( key !== 'model' && key !== 'id' && option['custom-js']) {
                $scope.outputJSScript("ct_custom_js_"+key+"-", id, option['custom-js']);
            }
        });*/

        $scope.applyingComponentJS = false;
    }


    /**
     * Check options to see if this current id option 
     * or inherited from defaults
     *
     * @since 0.3.2
     * @return {string} CSS class to grey out values
     */

    $scope.isInherited = function(id, optionName, optionValue) {

        if (undefined===optionValue) {
            optionValue = $scope.getOption(optionName, id);
        }

        // skip not active option
        if ($scope.getOption(optionName, id) != optionValue) {
            return false;
        }

        // editing id 'original'
        if ($scope.isEditing("id") && !$scope.isEditing("media") && !$scope.isEditing("state")) {
            if ($scope.component.options[id]["id"] === undefined || $scope.component.options[id]["id"][optionName] == optionValue) {
                return false;
            }
        }

        // editing id state
        if ($scope.isEditing("id") && !$scope.isEditing("media") && $scope.isEditing("state")) {
            if ($scope.component.options[id][$scope.currentState] &&
                $scope.component.options[id][$scope.currentState][optionName] == optionValue) {
                return false;
            }
        }

        // editing id media
        if ($scope.isEditing("id") && $scope.isEditing("media") ) {
            if ( $scope.component.options[id]['media'] &&
                 $scope.component.options[id]['media'][$scope.currentMedia] &&
                 $scope.component.options[id]['media'][$scope.currentMedia][$scope.currentState] &&
                 $scope.component.options[id]['media'][$scope.currentMedia][$scope.currentState][optionName] == optionValue) {
                return false;
            }
        }

        // editing class
        if ($scope.isEditing("class") && !$scope.isEditing("media") ) {
            if ($scope.classes[$scope.currentClass] &&
                $scope.classes[$scope.currentClass][$scope.currentState] &&
                $scope.classes[$scope.currentClass][$scope.currentState][optionName] == optionValue) {
                return false;
            }
        }

        // editing class media
        if ($scope.isEditing("class") && $scope.isEditing("media") ) {
            if ( $scope.classes[$scope.currentClass] &&
                 $scope.classes[$scope.currentClass]['media'] &&
                 $scope.classes[$scope.currentClass]['media'][$scope.currentMedia] &&
                 $scope.classes[$scope.currentClass]['media'][$scope.currentMedia][$scope.currentState] &&
                 $scope.classes[$scope.currentClass]['media'][$scope.currentMedia][$scope.currentState][optionName] == optionValue) {
                return false;
            }
        }

        // editing custom selector
        if ($scope.isEditing("custom-selector") && !$scope.isEditing("class") ) {

            // media
            if ( $scope.isEditing("media") ) {
                if ( $scope.customSelectors[$scope.selectorToEdit] &&
                     $scope.customSelectors[$scope.selectorToEdit]['media'] &&
                     $scope.customSelectors[$scope.selectorToEdit]['media'][$scope.currentMedia] &&
                     $scope.customSelectors[$scope.selectorToEdit]['media'][$scope.currentMedia][$scope.currentState] &&
                     $scope.customSelectors[$scope.selectorToEdit]['media'][$scope.currentMedia][$scope.currentState][optionName] == optionValue) {
                    return false;
                }
            }

            // desktop
            if (
                $scope.customSelectors[$scope.selectorToEdit] &&
                $scope.customSelectors[$scope.selectorToEdit][$scope.currentState] &&
                $scope.customSelectors[$scope.selectorToEdit][$scope.currentState][optionName] == optionValue) {
                return false;
            }
        }

        return true;
    }


    /**
     * Check options if any of border values has an option
     * or inherited from defaults
     *
     * @since 0.3.2
     * @return {bool}
     */
    
    $scope.isBorderHasStyles = function(side) {

        var width = $scope.isInherited($scope.component.active.id, "border-"+side+"-width");
            style = $scope.isInherited($scope.component.active.id, "border-"+side+"-style");
            color = $scope.isInherited($scope.component.active.id, "border-"+side+"-color");

        return (!width||!style||!color) ? true : false;
    }


    /**
     * Check if tab has at least one option defined
     *
     * @since 0.3.2
     * @author Ilya K.
     * @return {bool}
     */

    $scope.isTabHasOptions = function(key, childKey) {

        if (key===undefined) {

            for (var key in $scope.optionsHierarchy ) {

                if ($scope.optionsHierarchy.hasOwnProperty(key)) {

                    for (var tab in $scope.optionsHierarchy[key]) { 
                        if ($scope.optionsHierarchy[key].hasOwnProperty(tab)) {
                            
                            var subtub = $scope.optionsHierarchy[key][tab];
                            for (var index in subtub) { 
                                var optionName = subtub[index];
                                if ($scope.isInherited($scope.component.active.id, optionName)===false) {

                                    return true;
                                }
                            }
                        }
                    }

                }
            }
        }
        else if (childKey===undefined) {
            
            for (var tab in $scope.optionsHierarchy[key]) { 
                if ($scope.optionsHierarchy[key].hasOwnProperty(tab)) {
                    
                    var subtub = $scope.optionsHierarchy[key][tab];
                    for (var index in subtub) { 
                        var optionName = subtub[index];
                        if ($scope.isInherited($scope.component.active.id, optionName)===false) {

                            return true;
                        }
                    }
                }
            }
        }
        else {

            if (!$scope.optionsHierarchy[key][childKey])
                return false;
                    
            var subtub = $scope.optionsHierarchy[key][childKey];
            for (var index in subtub) { 
                var optionName = subtub[index];
                if ($scope.isInherited($scope.component.active.id, optionName)===false) {

                    return true;
                }
            }
        }
    }


    /**
     * Check if global settings are different and propose user to change
     *
     * @since 1.1.1
     * @author Ilya K.
     */

    $scope.checkGlobalOptions = function(options) {

        // parse options
        try {
            options = JSON.parse(options);
        }
        catch (e) {
            console.log(options);
            return;
        }

        // check global options (fonts)
        if ( options.display !== undefined && options.text !== undefined ) {
            
            if ( $scope.globalSettings.fonts.Display != options.display ||
                 $scope.globalSettings.fonts.Text != options.text ) {

                var confirmed = confirm("This Design Set's recommended fonts are:\r"+
                                        "Display: "+options.display+"\r"+
                                        "Text: "+options.text+"\r"+
                                        "Would you like to change your fonts to the recommended fonts?");
            
                if (confirmed) {
                    // update global settings
                    $scope.setGlobalFont("Display", options.display);
                    $scope.setGlobalFont("Text", options.text);
                }
            }
        }

        // check page settings (page width)
        if ( options["page-width"] !== undefined ) {
            
            if ( $scope.getPageWidth() != options["page-width"] ) {

                var confirmed = confirm("This Design Set's recommended page width is "+options["page-width"]+
                                        "px, but the width of this page is "+$scope.getPageWidth()+
                                        "px. Would you like to change your page width to "+options["page-width"]+"px?");
            
                if (confirmed) {
                    // update page settings
                    $scope.pageSettings['max-width'] = options["page-width"];
                    $scope.pageSettingsUpdate();
                }
            }
        }
    }


    /**
     * Reset Settings > Global Styles to the default values
     *
     * @since 2.0
     * @author Ilya K.
     */

    $scope.resetGlobalStylesToDefault = function() {
        
        if (confirm("Are you sure you wish to reset your Global Settings to the defaults?")) {
            
            angular.copy($scope.globalSettingsDefaults["fonts"],        $scope.globalSettings["fonts"]);
            angular.copy($scope.globalSettingsDefaults["headings"],     $scope.globalSettings["headings"]);
            angular.copy($scope.globalSettingsDefaults["body_text"],    $scope.globalSettings["body_text"]);
            angular.copy($scope.globalSettingsDefaults["links"],        $scope.globalSettings["links"]);
            angular.copy($scope.globalSettingsDefaults["sections"],     $scope.globalSettings["sections"]);
            
            $scope.globalSettings["max-width"] = $scope.globalSettingsDefaults["max-width"];

            $scope.pageSettingsUpdate();
        }
    }


    /**
     * Set Layout and Alignment for Div and Section when first time added
     * for backward compatibilty with previous versions
     *
     * @since 2.0
     * @author Ilya K.
     */

    $scope.setFirstTimeOptions = function(id, name) {
        
        if ( name == "ct_slider" ) { 
            $scope.setOptionModel('slider-arrow-color',     "darker",   id, name);
            $scope.setOptionModel('slider-dot-color',       "#ffffff",  id, name);
            $scope.setOptionModel('slider-show-arrows',     "yes",      id, name);
            $scope.setOptionModel('slider-show-dots',       "yes",      id, name);
            $scope.setOptionModel('slider-autoplay',        "no",       id, name);
            $scope.setOptionModel('slider-autoplay-delay',  "3000",     id, name);
            $scope.setOptionModel('slider-animation',       "horizontal", id, name);
            $scope.setOptionModel('slider-animation-speed', "750",      id, name);
        }

        if ( name == "oxy_nav_menu" ) { 
            $scope.setOptionModel('menu_responsive',                "page-width",   id, name);
            $scope.setOptionModel('menu_responsive_padding_size',   "0",            id, name);
            $scope.setOptionModel('menu_responsive_icon_size',      "40",           id, name);
            $scope.setOptionModel('menu_responsive_icon_margin',    "10",           id, name);
            $scope.setOptionModel('dropdowns',                      "on",           id, name);
            $scope.setOptionModel('menu_dropdown_arrow',            "on",           id, name);
            $scope.setOptionModel('menu_padding-top',               "20",           id, name);
            $scope.setOptionModel('menu_padding-left',              "20",           id, name);
            $scope.setOptionModel('menu_padding-right',             "20",           id, name);
            $scope.setOptionModel('menu_padding-bottom',            "20",           id, name);
            $scope.setOptionModel('menu_hover_background-color',    "#eee",         id, name);
            $scope.setOptionModel('menu_hover_border-top-width',    "3",            id, name);
            $scope.setOptionModel('menu_flex-direction',            "row",          id, name);
        }

        if ( name == "oxy_social_icons" ) { 
            $scope.setOptionModel('icon-facebook',  "https://facebook.com");
            $scope.setOptionModel('icon-instagram', "https://instagram.com");
            $scope.setOptionModel('icon-twitter',   "https://twitter.com");
        }

        if ( name == "oxy_header" ) { 
            $scope.setOptionModel('sticky-media',           "page-width");
            $scope.setOptionModel('sticky_scroll_distance', "300");
        }

        if ( name == "oxy_soundcloud" ) { 
            $scope.setOptionModel('height',             "300");
            $scope.setOptionModel('height-unit',        "px");
            $scope.setOptionModel('width',              "100");
            $scope.setOptionModel('width-unit',         "%");
            $scope.setOptionModel('soundcloud_url',     "https://soundcloud.com/nathaniel-eliason/13-learning-spanish-and-more");
            $scope.setOptionModel('soundcloud_track_id', "331837379");
        }

        if ( name == "oxy_superbox" ) { 
            $scope.setOptionModel('superbox_secondary_opacity_start',   "0");
            $scope.setOptionModel('superbox_secondary_opacity_finish',  "1");
        }

        /*if ( name == "ct_section" ) {   
            // Layout
            $scope.setOptionModel("flex-direction", "column", id, name);
            $scope.setOptionModel("display", "flex", id, name);
            // Alignment
            $scope.setOptionModel("alignment", "center", id, name);
        }

        if ( name == "ct_div_block" ) {   
            // Layout
            $scope.setOptionModel("flex-direction", "column", id, name);
            $scope.setOptionModel("display", "flex", id, name);
        }*/

    }


    /**
     * Check if component display can be flex
     *
     * @since 2.0
     * @author Ilya K.
     */

    $scope.isFlexPossible = function(name) {

        if ( name == undefined ) {
            name = $scope.component.active.name;
        }

        if ( ["ct_div_block","ct_section","ct_link"].indexOf(name) > -1 ) {
            return true;
        }

        return false;
    }


    /**
     * Set text-align option according to horizontal/vertical alignment
     *
     * @since 2.0
     * @author Ilya K.
     */

    $scope.setTextAlign = function() {

        var timeout = $timeout(function() {
            
            var id = $scope.component.active.id;

            if ( $scope.component.options[id]['model']['flex-direction'] == "column" ) {
                
                switch ($scope.component.options[id]['model']['align-items']) {
                    case "flex-start":
                        $scope.setOptionModel("text-align","left")
                        break;
                    
                    case "center":
                        $scope.setOptionModel("text-align","center")
                        break;

                    case "flex-end":
                        $scope.setOptionModel("text-align","right")
                        break;

                    case "stretch":
                        $scope.setOptionModel("text-align","justify")
                        break;

                    default: 
                        $scope.setOptionModel("text-align","")
                        break;                
                }
            }

            if ( $scope.component.options[id]['model']['flex-direction'] == "row" ) {
                
                switch ($scope.component.options[id]['model']['justify-content']) {
                    case "flex-start":
                        $scope.setOptionModel("text-align","left")
                        break;
                    
                    case "center":
                        $scope.setOptionModel("text-align","center")
                        break;

                    case "flex-end":
                        $scope.setOptionModel("text-align","right")
                        break;

                    case "space-between":
                    case "space-around":
                        $scope.setOptionModel("text-align","justify")
                        break;

                    default: 
                        $scope.setOptionModel("text-align","")
                        break;                
                }
            }

            $timeout.cancel(timeout);
        }, 11, false);

    }


    /**
     * Set Easy Posts template and render it
     *
     * @since 2.0
     * @author Ilya K.
     */

    $scope.setEasyPostsTemplate = function(template, needConfirm) {

        if (needConfirm===undefined)
            needConfirm = true;

        if (needConfirm&&!confirm("Current settings for this element will be overwritten. Are you sure?")) {
            return false;
        }

        $scope.setOptionModel("code-php",template.code_php);
        $scope.setOption($scope.component.active.id,$scope.component.active.name,"code-php");
        
        $scope.setOptionModel("code-css",template.code_css);
        $scope.setOption($scope.component.active.id,$scope.component.active.name,"code-css");

        var styles = ["title_size","title_size-unit","title_color","title_hover_color","meta_size","meta_size-unit","meta_color",
                      "content_size","content_size-unit","content_color","read_more_display_as","read_more_size","read_more_size-unit",
                      "read_more_text_color","read_more_text_hover_color","read_more_button_color","read_more_button_hover_color","paginate_color",
                      "paginate_alignment","paginate_link_color","paginate_link_hover_color","posts_per_page","posts_5050_below","posts_100_below"];

        for(var key in styles) {
            var name = styles[key];
            $scope.setOptionModel(name,template[name]); 
        }

        $scope.lastSetEasyPostsTemplate = template.name;

        $scope.renderComponentWithAJAX('oxy_render_easy_posts');
    }


    /**
     * Add current PHP and CSS to Easy Posts custom template
     *
     * @since 2.0
     * @author Ilya K.
     */

    $scope.addEasyPostsTemplate = function() {

        if (!$scope.newEasyPostsTemplate||$scope.newEasyPostsTemplate==="") {
            alert("Please set the preset name");
            return;
        };

        var template = {
            name:$scope.newEasyPostsTemplate,
            code_php: $scope.getOption("code-php"),
            code_css: $scope.getOption("code-css"),
        }

        var styles = ["title_size","title_size-unit","title_color","title_hover_color","meta_size","meta_size-unit","meta_color",
                      "content_size","content_size-unit","content_color","read_more_display_as","read_more_size","read_more_size-unit",
                      "read_more_text_color","read_more_text_hover_color","read_more_button_color","read_more_button_hover_color","paginate_color",
                      "paginate_alignment","paginate_link_color","paginate_link_hover_color","posts_per_page","posts_5050_below","posts_100_below"];
                      
        for(var key in styles) {
            var name = styles[key];
            template[name] = $scope.getOption(name);
        }

        $scope.easyPostsCustomTemplates.push(template);

        $scope.newEasyPostsTemplate = ""
        alert("Your preset was saved successfully");

        $scope.unsavedChanges();
    }


    /**
     * Delete Easy Posts custom template
     *
     * @since 2.0
     * @author Ilya K.
     */

    $scope.deleteEasyPostsTemplate = function(id, $event) {

        if (typeof $event != 'undefined') {
            $event.stopPropagation();
        }

        $scope.easyPostsCustomTemplates.splice(id, 1);
    }


    /**
     * Set Comments List template and render it
     *
     * @since 2.0
     * @author Ilya K.
     */

    $scope.setCommentsListTemplate = function(template) {

        $scope.setOptionModel("code-php",template.code_php);
        $scope.setOption($scope.component.active.id,$scope.component.active.name,"code-php");
        
        $scope.setOptionModel("code-css",template.code_css);
        $scope.setOption($scope.component.active.id,$scope.component.active.name,"code-css");

        $scope.lastSetCommentsListTemplate = template.name;

        $scope.renderComponentWithAJAX('oxy_render_comments_list');
    }


    /**
     * Add current PHP and CSS to Comments List custom template
     *
     * @since 2.0
     * @author Ilya K.
     */

    $scope.addCommentsListTemplate = function() {
        
        if (!$scope.newCommentsListTemplate||$scope.newCommentsListTemplate==="") {
            alert("Please set the preset name");
            return;
        };

        $scope.commentsListCustomTemplates.push({
            name:$scope.newCommentsListTemplate,
            code_php: $scope.getOption("code-php"),
            code_css: $scope.getOption("code-css"),
        })
        
        $scope.newCommentsListTemplate="";
        alert("Your preset was saved successfully");

        $scope.unsavedChanges();
    }


    /**
     * Delete Comments List custom template
     *
     * @since 2.0
     * @author Ilya K.
     */

    $scope.deleteCommentsListTemplate = function(id, $event) {

        if (typeof $event != 'undefined') {
            $event.stopPropagation();
        }

        $scope.commentsListCustomTemplates.splice(id, 1);
    }


    /**
     * Return Page or Global page width value
     *
     * @since 2.0
     * @author Ilya K.
     */

    $scope.getPageWidth = function() {

        if ($scope.pageSettings['max-width']!==undefined && $scope.pageSettings['max-width'] != "" ) {
            return parseInt($scope.pageSettings['max-width'])
        }
        else {
            return parseInt($scope.globalSettings['max-width'])
        }
    }


    /**
     * Set Pricing Box texts to default if empty
     *
     * @since 2.0
     * @author Ilya K.
     */
    
    $scope.reEnablePricingBoxText = function() {

        if ($scope.getOption('pricing_box_package_regular')=='')
            $scope.setOptionModel('pricing_box_package_regular','monthly $399')
        
        if ($scope.getOption('pricing_box_package_subtitle')=='')
            $scope.setOptionModel('pricing_box_package_subtitle','for small business')
        
        if ($scope.getOption('pricing_box_content')==''||$scope.getOption('pricing_box_content')=='<br>')
            $scope.setOptionModel('pricing_box_content','my features<br />another feature<br />my last feature<br />')
    }


    /**
     * Get pretty title for Superbox current editing mode
     *
     * @since 2.0
     * @author Ilya K.
     */

    $scope.getSuperBoxEditingModeTitle = function() {
        
        var mode = $scope.getOption('superbox_editing_mode');

        switch (mode) {
            
            case "primary_only":
                return "Show Primary Only";
            break;

            case "secondary_only":
                return "Show Secondary Only";
            break;

            case "as_hovered":
                return "As If Hovered";
            break;

            case "as_not_hovered":
                return "As If Not Hovered";
            break;

            default:
                return "Live";
        }
    }

    
    /**
     * Unset all border options for currently active component
     *
     * @since 2.0
     * @author Ilya K.
     */
    
    $scope.unsetAllBorders = function() {
            
        var id = $scope.component.active.id,
            borderOptions = [
                "border-all-width",
                "border-all-width-unit",
                "border-all-style",
                "border-all-color",
                "border-top-width",
                "border-top-width-unit",
                "border-top-style",
                "border-top-color",
                "border-right-width",
                "border-right-width-unit",
                "border-right-style",
                "border-right-color",
                "border-bottom-width",
                "border-bottom-width-unit",
                "border-bottom-style",
                "border-bottom-color",
                "border-left-width",
                "border-left-width-unit",
                "border-left-style",
                "border-left-color",
                ];

        for(var key in borderOptions) {
            var option = borderOptions[key];
            $scope.setOptionModel(option,null);
        }
    }

});