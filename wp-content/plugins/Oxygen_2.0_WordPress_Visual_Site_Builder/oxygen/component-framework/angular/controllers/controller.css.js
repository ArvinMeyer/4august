/**
 * All Custom CSS staff here
 * 
 */

CTFrontendBuilder.controller("ControllerCSS", function($scope, $parentScope, $http, $timeout) {

	$scope.idStylesContainer 	= document.getElementById("ct-id-styles");
	$scope.classStylesContainer = document.getElementById("ct-class-styles");

	// Example
	$scope.customSelectorsExample = {
		'#my-custom-id' : {
			'original' : {
				'color' : 'red',
				'font-size' : '24px',
			}
		}
	}

	$scope.selectedCSSFolder = null;

	$scope.expandedFolder = []; // containing reference to the open stylesheet folders
	$scope.expandedSelectorFolder = []; // containing reference to the open selector folders
	$scope.expandedStylesets = []; // containing reference to the open stylsets in the side panel

	$scope.selectedSelectorFolder = null;

	$scope.selectedNodeType = null;

	$scope.selectedStyleSet = null;

	$scope.selectorDetector = {
		mode: false
	};

	$scope.cssFolderMenuOpen = false;
	$scope.selectorFolderMenuOpen = false;

	$scope.componentSelector = {};

	$scope.selectorToEdit 		= false;
	$scope.selectorHighlighted 	= true;
	$scope.expandedStyleSets 	= [];

	$scope.excludeProperties = [
				"ct_content",
				"ct_id",
				"ct_parent",
				"tag",
				"url",
				"src",
				"alt",
				"target",
				"icon-id",
				"section-width",
				"custom-width",
				"container-padding-top",
				"container-padding-right",
				"container-padding-bottom",
				"container-padding-left",
				"background-position-top",
				"background-position-left",
				"background-size-width",
				"background-size-height",
				// ct_video related
				"video-padding-bottom",
				"use-custom",
				"custom-code",
				"embed-src",

				// ct_link_button related
				"button-style",
				"button-size",
				"button-color",
				"button-text-color",

				// background related
				"gradient",
				"background",
				"overlay-color",

				// ct_fancy_icon related
				"icon-size",
				"icon-style",
				"icon-color",
				"icon-background-color",
				
				"custom-js",
				"border-all-width",
				"border-all-style",
				"border-all-color",
				"function_name",
				"full_shortcode",
				"gutter",
				"code-css",
				"code-php",
				"code-js",
				"class_name",
				"id_base",
				"pretty_name",
				"friendly_name",
				'separator',
				'date_format',
				'size',
				'meta_key',
				'hover_color',
				//flex
				'flex-reverse',
				//media
				"stack-columns-vertically",
				"reverse-column-order",
				"set-columns-width-50",
				// nav menu
				"menu_id",
				// video background
				"video_background",
				"video_background_media",
				"video_background_hide"	
				];

	$scope.cache.idCSS 			= "";
	$scope.cache.idStyles 		= {};
	
	$scope.cache.classCSS 		= "";
	$scope.cache.classStyles 	= {};
	
	$scope.cache.selectorCSS 	= "";
	$scope.cache.selectorStyles = {};

	$scope.cache.mediaCSS 		= "";
	$scope.cache.mediaStyles 	= {};

	$scope.contentAdded = [];

	$scope.socialIcons = [];
	$scope.socialIcons.networks = ['facebook', 'instagram', 'twitter', 'linkedin', 'rss', 'youtube'];
	$scope.socialIcons.networkColors = {
		'facebook' 	: '#3b5998',
		'instagram' : '#c32aa3',
		'twitter' 	: '#00b6f1',
		'linkedin' 	: '#007bb6',
		'rss' 		: '#ee802f',
		'youtube' 	: '#ff0000'
	};
	$scope.socialIcons.networkHoverColors = {
		'facebook' 	: '#5b79b8',
		'instagram' : '#e34ac3',
		'twitter' 	: '#20d6ff',
		'linkedin' 	: '#209bd6',
		'rss' 		: '#ffa04f',
		'youtube' 	: '#ff4444'
	};

	$scope.mappedSelectors = {};

	$scope.filterStylesheets = function(item) {

		var filtered = $scope.styleSheets.filter(function(stylesheet) { return stylesheet.id == item.parent; });
		if(filtered.length > 0 && filtered[0].status === 0) {
			return false;
		}
		
		if(parseInt(item.parent) === -1) {
			return false;
		}

		return true;
	}

	$scope.setSelectedSelectorFolder = function(folder) {
		$scope.setSelectedNodeType('selectorfolder');
		$scope.selectedSelectorFolder = folder;
	}

	$scope.getCSSFolder = function(name) {
		return $scope.styleSheets.find(function(item) { 
			return item.id === iframeScope.selectedCSSFolder;
		});
	}

	$scope.setSelectedCSSFolder = function(folder) {
		$scope.selectedCSSFolder = folder;
	}

	$scope.setActiveCSSFolder = function(folder) {
		$scope.setSelectedNodeType('cssfolder');
	}

	$scope.setSelectedNodeType = function(type) {

		if(type !== 'stylesheet') {
			$parentScope.toggleSidebar(true);
		}

		$scope.selectedNodeType = type;
	}

	$scope.greaterThan = function(parameter, value){
		
	    return function(item){
	      return item[parameter] > value;
	    }
	}

	$scope.equalsTo = function(parameter, value) {
		return function(item) {
			return item[parameter] === value;
		}
	}

	$scope.toggleCSSFolder = function(folder) {
		//$scope.expandedFolder[folder.id] = false
		$scope.cssFolderMenuOpen = null;
		folder.status = (folder.status == 1 ? 0 : 1);
		
	}

	$scope.toggleSelectorFolder = function(folder) {
		//$scope.expandedSelectorFolder[folder.key] = false
		folder.status = (folder.status == 1 ? 0 : 1)
	}

	$scope.checkNewCustomSelector = function(selector) {
		if(typeof($scope.customSelectors[selector]) === 'undefined') {
			$scope.customSelectors[selector] = {
				set_name: 'Uncategorized Custom Selectors',
				original: {}
			}
		}

		if(typeof($scope.customSelectors[selector]['set_name']) === 'undefined') {
			$scope.customSelectors[selector]['set_name'] = 'Uncategorized Custom Selectors'
		}

		if(typeof($scope.customSelectors[selector]['original']) === 'undefined') {
			$scope.customSelectors[selector]['original'] = {}
		}
	}

	/**
	 * Set custom CSS selector to edit
	 *
	 * @since 0.2.0
	 * @author Ilya K.
	 */

	$scope.setCustomSelectorToEdit = function(selector) {

		$scope.styleSetActive = false;

		// if the active component had some active css selector, bring it back to edit mode
		if($scope.component.active.id > 0) {
			$scope.activeSelectors[$scope.component.active.id] = (typeof($scope.activeSelectors[$scope.component.active.id]) !== 'undefined') ? $scope.activeSelectors[$scope.component.active.id] : $scope.component.options[$scope.component.active.id].model.activeselector;
			if($scope.activeSelectors[$scope.component.active.id]) {
				$scope.setCurrentClass($scope.activeSelectors[$scope.component.active.id] ? $scope.activeSelectors[$scope.component.active.id] : false);	
			}
		}

		if ($scope.log) {
			console.log("setCustomSelectorToEdit()", selector)
		}

		$scope.selectorToEdit = selector;

		if (typeof selector === "string") {

			$scope.activateComponent(-1, 'ct_selector');

			// if selector exist in classes
			var possibleClass = selector.substr(1);
    		if ( $scope.classes[possibleClass] ) {
    			$scope.setCurrentClass(possibleClass);
    			$scope.populateSelectorFolderDropdown(true);
    			$scope.setSelectedNodeType('class');
    		}
    		else {
    			$scope.setCurrentClass(false);
    			$scope.populateSelectorFolderDropdown();
    			$scope.setSelectedNodeType('selector');
    		}

    		if ($scope.customSelectors[selector]) {
    			$scope.expandedStyleSets[$scope.customSelectors[selector]['set_name']] = true;
			}
		}

		$scope.highlightSelector(true, selector);
		
		if ($parentScope.cleanEmptySelectors && selector !== "") {
            $parentScope.cleanEmptySelectors();
        }
	}

	$scope.setStyleSetActive = function() {
		$scope.setSelectedNodeType('styleset');
		$scope.styleSetActive = true;
		$scope.selectorToEdit = false;
		$scope.currentClass = false;
	}


	$scope.populateSelectorFolderDropdown = function(isClass) {
        var folder = '';
        if(isClass) {
        	var selectorItem = $scope.classes[$scope.currentClass];
        	if(selectorItem) {
        		folder = selectorItem.parent;
        	}
        }
        else {
            if($scope.selectedStyleSet) {
            	folder = $scope.styleSets[$scope.selectedStyleSet]['parent'];
            }
            else {
            	var selectorItem = $scope.customSelectors[$scope.selectorToEdit];
            	if(selectorItem) {
            		folder = selectorItem.parent;
            	}
            }
        }
      
		$scope.currentActiveFolder = folder;
	}

	/**
	 * Set style sheet to edit
	 *
	 * @since 0.3.4
	 * @author Gagan Goraya.
	 */

	$scope.setStyleSheetToEdit = function(stylesheet) {

		var parent = _.findWhere($scope.styleSheets, {id: stylesheet.parent});

        if(parent) {
            $scope.currentActiveStylesheetFolder = parent.name;
        }
        else {
            $scope.currentActiveStylesheetFolder = '';
        }

		$scope.cancelDeleteUndo();
	
		if ($scope.log) {
			console.log("setStyleSheetToEdit()", stylesheet)
		}

		// activate root
		$scope.activateComponent(0);

		$scope.stylesheetToEdit = stylesheet;
		$scope.parentScope.switchActionTab('styleSheet');
		$scope.setSelectedNodeType('stylesheet'); // this must come after the switchActionTab, not before
		$scope.parentScope.expandSidebar();
	}


	/**
	 * Highlight selector elements presented on the page
	 *
	 * @since 0.2.0
	 * @author Ilya K.
	 */

	$scope.highlightSelector = function(forceHighlight, selector, $event) {

		if ($scope.log){
			console.log("highlightSelector()", forceHighlight, selector);
		}

		if (selector===false) {
			$scope.outputCSSStyles("ct-selector-highlight", "");
			return false;
		}

		if ($event){
			$event.stopPropagation();
		}

		var style = "";

		if (undefined === selector) {
			selector = $scope.selectorToEdit;
		}

		if (forceHighlight === undefined) {
			$scope.selectorHighlighted = ! $scope.selectorHighlighted;
		}
		else {
			$scope.selectorHighlighted = forceHighlight;
		}

		// check if selector has any elements on the page
		if ($scope.selectorHighlighted) {
			
			var elements = []; 

			try {
				if (selector.indexOf(".")<0&&selector.indexOf("#")<0&&selector.indexOf("body")<0) {
					elements = angular.element(selector, '.oxygen-body');
				}
				else {
					elements = angular.element(selector);
				}
			}
			catch (err) {
				console.log(err);
			}

			if ( elements.length == 0 ) {

				$scope.selectorHighlighted = false;
				
				//alert("There is no elements for '" + selector + "' selector on this page.")
				$scope.outputCSSStyles("ct-selector-highlight", "");
				return false;
			}
		}

		var state = "";

		if ( $scope.currentState == "original" || $scope.currentState == "hover" || $scope.currentState == "active" || $scope.currentState == "focus" ) {
			state = "";
		}
		else {
			state = ":"+$scope.currentState;
		}

		// check if highlighted
        if ($scope.selectorHighlighted) {
        	style = selector + state + "{outline: 1px dashed rgb(19, 187, 38); outline-offset: 0px;}";
        	if (selector.indexOf(".")<0&&selector.indexOf("#")<0&&selector.indexOf("body")<0) {
        		style = ".oxygen-body " + style;
        	}
        }

        // output to <head>
        $scope.outputCSSStyles("ct-selector-highlight", style);
	}

	$scope.deleteSelectorFolder = function(folder) {
		if($scope.styleFolders.hasOwnProperty(folder)) {
			if(_.findWhere($scope.styleSets, {parent: folder}) || _.findWhere($scope.classes, {parent: folder})) {
				if(!confirm("Are you sure you wish to delete this folder and all classes and selectors inside it?")) {
					return false;
				}
			}

			angular.forEach(iframeScope.styleSets, function(item) { if(item.parent === folder) delete iframeScope.styleSets[item.key];})
			angular.forEach(iframeScope.classes, function(item) { if(item.parent === folder) delete iframeScope.classes[item.key];})

			delete $scope.styleFolders[folder];
		}
	}

	$scope.toggleUncategorizedFolderContents = function(disable) {
		if(disable) {
			_.each(_.filter($scope.styleSets, function(item) { return typeof(item['parent']) === 'undefined' || item['parent'] === null;}), function(item) {
				item['parent'] = -1;
			});
			_.each(_.filter($scope.classes, function(item) { return typeof(item['parent']) === 'undefined' || item['parent'] === null;}), function(item) {
				item['parent'] = -1;
			});
		} else {
			_.each(_.filter($scope.styleSets, function(item) { return item['parent'] === -1 || item['parent'] === "-1";}), function(item) {
				delete item['parent'];
			});
			_.each(_.filter($scope.classes, function(item) { return item['parent'] === -1 || item['parent'] === "-1";}), function(item) {
				delete item['parent'];
			});
		}
	}

	$scope.toggleUncategorizedStyleSheets = function(disable) {
		if(disable) {
			_.each(_.filter($scope.styleSheets, function(item) { return typeof(item['parent']) === 'undefined' || item['parent'] === null || parseInt(item['parent']) === 0;}), function(item) {
				item['parent'] = -1;
			});
		}
		else {
			_.each(_.filter($scope.styleSheets, function(item) { return item['parent'] === -1 || item['parent'] === "-1";}), function(item) {
				item['parent'] = 0;
			});
		}
	}

	$scope.addSelectorFolder = function() {

		$scope.cancelDeleteUndo();

		
		var folder = prompt('Provide a name for the folder').trim();

		// check for validity of the name
    	var re = /^[a-z_-][a-z\d_-]*$/i
	    //var re = /-?[_a-zA-Z]+[_a-zA-Z0-9-]*$/i;

	    if(!re.test(folder)) {
	    	alert("Bad folder name. Special characters and spaces are not allowed");
	    	return;
	    }

	    // check if already exists
		if($scope.styleFolders.hasOwnProperty(folder)) {
			alert("'" + folder + "' already exist.");
			return;
		}

		// finaly add the folder
		$scope.styleFolders[folder] = {
			status: 1
		}
	}

	$scope.addSelectorItem = function() {

		$scope.cancelDeleteUndo();

		var selector = prompt('Type a valid CSS selector').trim();

		try { // check validity
			document.querySelector(selector)
		}
		catch (e) {
			alert("Not a valid selector");
			return false
		}

		// check if already exists
		if($scope.customSelectors.hasOwnProperty(selector)) {
			alert("'" + selector + "' already exist.");
			return;
		}

		// finaly add the selector
		$scope.customSelectors[selector] = {
			set_name: 'Uncategorized Custom Selectors',
			original: {}
		}

		// if a folder is selected
		if($scope.selectedSelectorFolder && $scope.styleFolders.hasOwnProperty($scope.selectedSelectorFolder)) {
			$scope.customSelectors[selector]['parent'] = $scope.selectedSelectorFolder;
		}

		$scope.setCustomSelectorToEdit(selector);
		
		$scope.selectedStyleSet = selector;

		$scope.expandedStyleSets['Uncategorized Custom Selectors'] = true;

		var styleSetFolder = $scope.styleSets['Uncategorized Custom Selectors']['parent'];
		
		if(typeof(styleSetFolder) === 'undefined' ) {
			//expand styleset folder 0
			iframeScope.expandedSelectorFolder[0]=true;
		}
		else if(styleSetFolder === -1) {
			//expand 'uncategorized'
			iframeScope.expandedSelectorFolder['uncategorized'] = true;
		}
		else {
			iframeScope.expandedSelectorFolder[styleSetFolder] = true;
		}
	}

	/**
	 * Add Style Sheet
	 *
	 * @since 0.3.4
	 * @author Gagan Goraya
	 */

	$scope.addStyleSheet = function(isFolder) {

		$scope.cancelDeleteUndo();


		var stylesheet = prompt("Enter a name for the "+(isFolder?'Folder':'Stylesheet'));
		
		if(!stylesheet) {
			return;
		}

		stylesheet = stylesheet.trim();

	    // check for validity of the name
    	var re = /^[a-z_-][a-z\d_-]*$/i
	    //var re = /-?[_a-zA-Z]+[_a-zA-Z0-9-]*$/i;

	    if(!re.test(stylesheet)) {
	    	alert("Bad stylesheet name. Special characters and spaces are not allowed");
	    	return;
	    }
		
	    // check for repeat
	    var filtered = $scope.styleSheets.filter(function(item){ return item.name == stylesheet; });

	    if(filtered.length > 0) {
			alert("'" + stylesheet + "' already exist. Please choose another name.");
			return;
	    }
		
		// find the highest available ID;
		var newId = 1;

		if($scope.styleSheets.length > 0) {
			newId = _.max($scope.styleSheets, function(item) {
				return item.id;
			}).id + 1;
		}

		//$scope.styleSheets[stylesheet] = "";
		if(isFolder) {
			$scope.styleSheets.push({
				id: newId,
				name: stylesheet,
				status: 1,
				folder: 1
			});
		}
		else {
			var parent = $scope.selectedCSSFolder === -1 || $scope.selectedCSSFolder === null ? 0 : $scope.selectedCSSFolder;
			var newSheet = {
				id: newId,
				name: stylesheet,
				css: '',
				parent: parent,
			};

			$scope.styleSheets.push(newSheet);

			// expand the parent folder
			for(key in iframeScope.expandedFolder) { iframeScope.expandedFolder[key] = false };
			iframeScope.expandedFolder[parent] = true;
			iframeScope.setStyleSheetToEdit(newSheet);
		}

		
	}


	/**
	 * Delete Style sheet
	 *
	 * @since 0.3.4
	 * @author Gagan Goraya.
	 */

	$scope.deleteStyleSheet = function(stylesheet) {

		$scope.cancelDeleteUndo();

		if(stylesheet['folder']) {
			if(!confirm("Are you sure you wish to delete this folder and all stylesheets inside it?")) {
				return false;
			}
		}
		else { 

			if (!confirm("Delete \""+stylesheet['name']+"\" stylesheet from install? (Changes will take effect on Save).")) {
				return false;
			}
		}

		if ($scope.log) {
			console.log("deleteStyleSheet()", selector);
		}

		//delete stylesheet;
		$scope.styleSheets.splice($scope.styleSheets.indexOf(stylesheet), 1);

		if(stylesheet['folder']) {
			// make sure that children are removed as well
			$scope.styleSheets = $scope.styleSheets.filter(function(item) { return item.parent != stylesheet.id;})

		}

		// remove the style definitions rendered in the DOM
		var styleSheetContainer = angular.element(document.getElementById("ct-style-sheet-"+stylesheet['name']));

		styleSheetContainer.remove();

		// turn off the editing panel, if this style sheet was being edited
		if($scope.stylesheetToEdit === stylesheet) {
			$scope.stylesheetToEdit = false;
		    $parentScope.actionTabs['styleSheet'] = false;
		    $parentScope.toggleSidebar(true);
		}

		$scope.selectedNodeType = null;
	}	


	/**
	 * Delete custom CSS selector
	 *
	 * @since 0.2.0
	 * @author Ilya K.
	 */

	$scope.deleteCustomSelector = function(selector) {

		$scope.cancelDeleteUndo();

		if(selector === 'Uncategorized Custom Selector') {
			alert("Sorry, you can't delete the Style Set named Uncategorized Custom Selectors");
			return false;
		}

		var confirmed = confirm("Are you sure to delete \""+selector+"\" selector?");
		
		if (!confirmed) {
			return false;
		}

		if ($scope.log) {
			console.log("deleteCustomSelector()", selector);
		}

		delete $scope.customSelectors[selector];

		$scope.selectorToEdit = false;

		if ( $scope.component.active.id == -1 ) {
			$scope.activateComponent(0,'root')
		}
		
		$scope.classesCached = false;
		$scope.outputCSSOptions();
		$scope.unsavedChanges();
	}


    /**
     * Update custom selector parameter value
     *
     * @since 0.2.0
     * @author Ilya K.
     */
    
    $scope.updateCustomSelectorValue = function(parameter, value) {

    	$scope.cancelDeleteUndo();

    	if ($scope.log) {
    		console.log("updateCustomSelectorValue()", parameter, value);
    	}

    	if ($scope.selectorToEdit === "") {
    		return false;
    	}

    	var state 		= $scope.currentState,
    		selector 	= $scope.selectorToEdit.substr(1);

    	// if selector exist in classes
    	if ( $scope.classes[selector] ) {
    		
    		// check if this state already added
    		if (!$scope.classes[selector][state]) {
                $scope.classes[selector][state] = {};
            }
			if (value==""){
				delete $scope.classes[selector][state][parameter];
			}
			else {
    			$scope.classes[selector][state][parameter] = value;
			}
    	}
    	else {

            if ( !$scope.isEditing('media') ) {
				    
				if (!$scope.customSelectors[$scope.selectorToEdit]) {
				    $scope.customSelectors[$scope.selectorToEdit] = {
				    	set_name: 'Uncategorized Custom Selectors'
				    };
				}

				if (!$scope.customSelectors[$scope.selectorToEdit][state]) {
				    $scope.customSelectors[$scope.selectorToEdit][state] = {};
				}

				if (value==""){
					delete $scope.customSelectors[$scope.selectorToEdit][state][parameter];
				}
				else {
					$scope.customSelectors[$scope.selectorToEdit][state][parameter] = value;
				}

            }
            else {
                // init class media options
                if (!$scope.customSelectors[$scope.selectorToEdit]['media']) {
                    $scope.customSelectors[$scope.selectorToEdit]['media'] = {};
                }
                
                if (!$scope.customSelectors[$scope.selectorToEdit]['media'][$scope.currentMedia]) {
                    $scope.customSelectors[$scope.selectorToEdit]['media'][$scope.currentMedia] = {};
                }

                if (!$scope.customSelectors[$scope.selectorToEdit]['media'][$scope.currentMedia][state]) {
                    $scope.customSelectors[$scope.selectorToEdit]['media'][$scope.currentMedia][state] = {};
                }

                // remove empty options
                if ( value == "" ) {
                    delete $scope.customSelectors[$scope.selectorToEdit]['media'][$scope.currentMedia][state][parameter];
                } 
                else {
                    $scope.customSelectors[$scope.selectorToEdit]['media'][$scope.currentMedia][state][parameter] = value;
                }
            }
	    }
    }


	/**
     * Append <style> element into <head> with all passed styles
     *
     * @since 0.2.0
     * @author Ilya K.
     */
    
    $scope.outputCSSStyles = function(name, style) {

    	var styleElement = document.getElementById(name),
    		output = "";
        
        output = "<style id=\"" + name + "\">",
        output += style;
        output += "</style>";
        
        // add style
        if ( styleElement ) {
            //angular.element(styleElement).replaceWith(output);
            styleElement.innerHTML = style;
        } 
        else {
            angular.element("head").append(output);
        }
    }


    /**
     * Delete <style> element into <head> with all passed styles
     *
     * @since 0.3.1
     * @author Ilya K.
     */
    
    $scope.deleteCSSStyles = function(name) {

    	var styleElement = document.getElementById(name); 
        
        if ( styleElement ) {
            angular.element(styleElement).remove();
        }
    }


    $scope.generateDynamicBackgrounds = function(id, force, type, callback) {

    	$scope.dynamicBackgrounds = $scope.dynamicBackgrounds || {};

    	var target = { lock: true };

    	if(type === 'id') {
    		$scope.dynamicBackgrounds[id] = $scope.dynamicBackgrounds[id] || {};
    		$scope.dynamicBackgrounds[id] = target;
    	}
    	else if(type === 'class') {
    		$scope.dynamicBackgrounds['classes'] = $scope.dynamicBackgrounds['classes'] || {}
    		$scope.dynamicBackgrounds['classes'][id] = $scope.dynamicBackgrounds['classes'][id] || {};
    		$scope.dynamicBackgrounds['classes'][id] = target;
    	}
    	else if(type === 'selector') {
    		$scope.dynamicBackgrounds['selectors'] = $scope.dynamicBackgrounds['selectors'] || {}
    		$scope.dynamicBackgrounds['selectors'][id] = $scope.dynamicBackgrounds['selectors'][id] || {};
    		$scope.dynamicBackgrounds['selectors'][id] = target;
    	}

    	

    	var options = {};

    	if(type === 'id') {

    		var component = $scope.findComponentItem($scope.componentsTree.children, id, $scope.getComponentItem);
    		if(!component) {
    			options = {}
    		}
    		else {
	    		options = component.options;
	    	}

    	}
    	else if(type === 'class') {

    		options = $scope.classes[id] || {};

    	}
    	else if(type === 'selector') {
    		options = $scope.customSelectors[id] || {};

    	}

    	var count = 0;
    	
        for(key in options) {

        	if(['ct_id', 'ct_parent', 'set_name', 'parent', 'key', 'status', 'friendly_name', 'selector', 'activeselector', 'classes', 'nicename'].indexOf(key) > -1) {
        		continue;
        	}

        	if(key === 'media') {
        		
        		target['media'] = {};

        		var breakpoints = options['media'];

        		for(bpKey in breakpoints) {
        			target['media'][bpKey] = {};
        			for(stateKey in breakpoints[bpKey]) {
        				if(type === 'id' && breakpoints[bpKey][stateKey]['background-image'] &&
        					breakpoints[bpKey][stateKey]['background-image'].indexOf('[oxygen') > -1) {

        					target['media'][bpKey][stateKey] = breakpoints[bpKey][stateKey]['background-image'];
        					count++;
        				}
        			}
        		}
        	}
        	else
        	if(type === 'id' && options[key]&&options[key]['background-image'] &&
        		options[key]['background-image'].indexOf('[oxygen') > -1) {

        		target[key] = options[key]['background-image'];
        		count++;
        	}

        	
        }


        for(key in target) {

        	if(key === 'lock') {
        		continue;
        	}

        	if(key === 'media') {

        		var breakpoints = target['media'];

        		for(bpKey in breakpoints) {
        			
        			for(stateKey in breakpoints[bpKey]) {
    					$scope.applyShortcodeResults(id, target['media'][bpKey][stateKey], function(contents, params) {
    						
    						//target['media'][params.bpKey][params.stateKey] = contents.trim();

    						if(type === 'selector') {

								$scope.dynamicBackgrounds['selectors'][id]['media'][params.bpKey][params.stateKey] = contents.trim();
							}
							else if(type === 'class') {

								$scope.dynamicBackgrounds['classes'][id]['media'][params.bpKey][params.stateKey] = contents.trim();
							}
							else if(type === 'id') {

								$scope.dynamicBackgrounds[id]['media'][params.bpKey][params.stateKey] = contents.trim();	

							}

    						count--;
    						if(count === 0) {
    							delete target['lock'];
    							callback();
    							return
    						}
    					}, {id: id, bpKey: bpKey, stateKey: stateKey});
    					
        			}
        		}
        	}

        	else {
        		
        		$scope.applyShortcodeResults(id, target[key], function(contents, params) {
        			
					//target[params.key] = contents.trim();
					if(type === 'selector') {

						$scope.dynamicBackgrounds['selectors'][id][params.key] = contents.trim();
					}
					else if(type === 'class') {

						$scope.dynamicBackgrounds['classes'][id][params.key] = contents.trim();
					}
					else if(type === 'id') {
						$scope.dynamicBackgrounds[id][params.key] = contents.trim();	
					}

					count--;
					if(count === 0) {
						delete target['lock'];
						callback();
						return
					}
				}, {id: id, key: key});
        	}
        	
        }

        if(count === 0) {
        	delete target['lock'];
			callback();
        }
        
    }


    /**
     * Output to the <head> CSS styles for all componets, classes and custom selectors
     *
     * @since 0.2.0
     * @author Ilya K.
     */

    $scope.outputCSSOptions = function(id, force) {

		if ($scope.log) {
			console.log("outputCSSOptions()", id);
			$scope.functionStart("outputCSSOptions()");
		}

		var style = "";
		$scope.bufferedOutputStyles = $scope.bufferedOutputStyles || {};
		$scope.bufferedOutputStyles['keys'] = $scope.bufferedOutputStyles['keys'] || [];
		if (($scope.isEditing("class") || $scope.isEditing("id") || force == true)) {
			if($scope.bufferedOutputStyles['keys'].indexOf('id') < 0) {
				$scope.bufferedOutputStyles['keys'].push('id');
			}
		}

		if ($scope.isEditing("class") || !$scope.classesCached) {
			
	    	if($scope.bufferedOutputStyles['keys'].indexOf('classes') < 0) {
				$scope.bufferedOutputStyles['keys'].push('classes');
			}
		}

    	if($scope.bufferedOutputStyles['keys'].indexOf('selectors') < 0) {
			$scope.bufferedOutputStyles['keys'].push('selectors');
    	}

		/**
		 * Handle #ID styles
		 */
		
		if (($scope.isEditing("class") || $scope.isEditing("id") || force == true)) {

			if (undefined !== id && id > 0) {
				$scope.updateComponentCacheStyles(id, function() {

					// clear cache
					$scope.cache.idCSS = "";

					Object.keys($scope.cache.idStyles).map(function(key, index) {
						$scope.cache.idCSS += $scope.cache.idStyles[key];
					});	
					
					$scope.outputCSSStylesAfterWait('id', $scope.cache.idCSS);

				});
			}
			else {
				
				Object.keys($scope.cache.idStyles).map(function(key, index) {
					$scope.cache.idCSS += $scope.cache.idStyles[key];
				});	
				
				$scope.outputCSSStylesAfterWait('id', $scope.cache.idCSS);
				
			}
			
		}

		/**
    	 * Handle .class styles
    	 */
    	
    	if ($scope.isEditing("class") || !$scope.classesCached) {
    		
			if (!$scope.classesCached) {

				// empty styles cache
				$scope.cache.classStyles = [];

				var classKeys = Object.keys($scope.classes);

				var processClassCSS = function(classKey) {

					var processNextClassCSS = function() {

						if(classKeys.length > 0) {
							processClassCSS(classKeys.shift());

						} else {

							// clear CSS cache
							$scope.cache.classCSS = "";

							Object.keys($scope.cache.classStyles).map(function(key, index) {
								$scope.cache.classCSS += $scope.cache.classStyles[key];

							});
							
							$scope.outputCSSStylesAfterWait('classes', $scope.cache.classCSS);

							style = "";

							// set cached flag
							$scope.classesCached = true;
						}
					}

					if ($scope.classes.hasOwnProperty(classKey)) {

						// get states
						var classStates = $scope.classes[classKey];
						$scope.cache.classStyles[classKey] = '';
						// add styles to cache
						if(typeof($scope.classes[classKey]['parent']) === 'undefined' || typeof($scope.styleFolders[$scope.classes[classKey]['parent']]) === 'undefined' || $scope.styleFolders[$scope.classes[classKey]['parent']]['status'] === 1) {
							
							if(parseInt($scope.classes[classKey]['parent']) !== -1) {
								// generate the results of oxy shortcodes for background images
								// and then do the below process as a callback
								$scope.generateDynamicBackgrounds(classKey, force, 'class', function() {

									$scope.cache.classStyles[classKey] = $scope.getSingleClassCSS(classKey, classStates);
									processNextClassCSS();
					    			
								})
				    			
							} else {
								processNextClassCSS();
							}

				    	} else {

				    		processNextClassCSS();
				    	}
						
					}
					else {
						processNextClassCSS();
					}
				}
				
				if(classKeys.length > 0) {

					processClassCSS(classKeys.shift());

				} else {
					// clear CSS cache
					$scope.cache.classCSS = "";
					$scope.outputCSSStylesAfterWait('classes', $scope.cache.classCSS);
					$scope.classesCached = true;
				}

			}
			else {
				if(typeof($scope.classes[$scope.currentClass]['parent']) === 'undefined' || typeof($scope.styleFolders[$scope.classes[$scope.currentClass]['parent']]) === 'undefined' || $scope.styleFolders[$scope.classes[$scope.currentClass]['parent']]['status'] === 1) {
					if(parseInt($scope.classes[$scope.currentClass]['parent']) !== -1) {
		    			$scope.cache.classStyles[$scope.currentClass] = $scope.getSingleClassCSS($scope.currentClass);
					}
		    	}

		    	// clear CSS cache
				$scope.cache.classCSS = "";

				Object.keys($scope.cache.classStyles).map(function(key, index) {
					$scope.cache.classCSS += $scope.cache.classStyles[key];
				});
				
				$scope.outputCSSStylesAfterWait('classes', $scope.cache.classCSS);

				style = "";
			}

			
		}

		

		/**
    	 * Add all custom selectors' options
    	 * 
    	 */
    	
    	// TODO: implement cache clearing and check for cache
    	$scope.cache.selectorStyles = [];
    	$scope.cache.selectorCSS 	= "";
    	
    	var selectorKeys = Object.keys($scope.customSelectors);

		var processSelectorCSS = function(selectorKey) {

			var processNextSelectorCSS = function() {

				if(selectorKeys.length > 0) {

					processSelectorCSS(selectorKeys.shift());

				} else {

					// clear CSS cache
					$scope.cache.selectorCSS = "";

					Object.keys($scope.cache.selectorStyles).map(function(key, index) {
						$scope.cache.selectorCSS += $scope.cache.selectorStyles[key];
					});

					$scope.outputCSSStylesAfterWait('selectors', $scope.cache.selectorCSS);
					
				}
			}

			if ($scope.customSelectors.hasOwnProperty(selectorKey)) {

				var states = $scope.customSelectors[selectorKey];
				$scope.cache.selectorStyles[selectorKey] = '';
				if(typeof($scope.customSelectors[selectorKey]['set_name']) === 'undefined' || typeof($scope.styleSets[$scope.customSelectors[selectorKey]['set_name']]) === 'undefined' || typeof($scope.styleSets[$scope.customSelectors[selectorKey]['set_name']]['parent']) === 'undefined' || typeof($scope.styleFolders[$scope.styleSets[$scope.customSelectors[selectorKey]['set_name']]['parent']]) === 'undefined' || typeof($scope.styleFolders[$scope.styleSets[$scope.customSelectors[selectorKey]['set_name']]['parent']]['status']) === 'undefined' || $scope.styleFolders[$scope.styleSets[$scope.customSelectors[selectorKey]['set_name']]['parent']]['status'] === 1) {
					if(typeof($scope.styleSets[$scope.customSelectors[selectorKey]['set_name']]) === 'undefined' || parseInt($scope.styleSets[$scope.customSelectors[selectorKey]['set_name']]['parent']) !== -1) {
						$scope.generateDynamicBackgrounds(selectorKey, force, 'selector', function() {

			    			$scope.cache.selectorStyles[selectorKey] = $scope.getSingleSelectorCSS(selectorKey, states);
							processNextSelectorCSS();	
						});
					}
					else {
		    			processNextSelectorCSS();
		    		}
		    	}
		    	else {
		    		processNextSelectorCSS();
		    	}
				
			}
			else {
				processNextSelectorCSS();
			}
		}
		
		if(selectorKeys.length > 0) {

			processSelectorCSS(selectorKeys.shift());

		} else {
			// clear CSS cache
			$scope.outputCSSStylesAfterWait('selectors', $scope.cache.selectorCSS);
		}

        $scope.functionEnd("outputCSSOptions()");

    }

    $scope.outputCSSStylesAfterWait = function(param, style) {   	

    	if(param === 'id') {
    		$scope.idStylesContainer.innerHTML = style;
    	}

    	if(param === 'classes') {
    		$scope.classStylesContainer.innerHTML = style;
    	}

    	if(param === 'selectors') {
    		$scope.outputCSSStyles("ct-custom-selectors", style);
    	}
    }

 
	/**
	 * Update component styles in cache array
	 *
	 * @since 0.2.5
	 * @author Ilya K.
	 */
	
	$scope.updateComponentCacheStyles = function(id, callback) {

		$scope.contentAdded = [];

		if ($scope.log) {
			console.log("updateComponentCacheStyles()", id, $scope.component.options[id]);
		}

		$scope.generateDynamicBackgrounds(id, false, 'id', function() {

			// update particular component Styles in cache
			$scope.cache.idStyles[id] = $scope.getSingleComponentCSS($scope.component.options[id], id);
			if(callback) {
				callback();
			}
		});
		
	}


	/**
	 * Update all components styles in cache array
	 *
	 * @since 0.3.0
	 * @author Ilya K.
	 */
	
	$scope.updateAllComponentsCacheStyles = function() {

		$scope.functionStart("updateAllComponentsCacheStyles()");

		for(var id in $scope.component.options) { 
			if ($scope.component.options.hasOwnProperty(id)) {
				
				$scope.updateComponentCacheStyles(id, function() {
					 // clear cache
                    $scope.cache.idCSS = "";

                    Object.keys($scope.cache.idStyles).map(function(key, index) {
                        $scope.cache.idCSS += $scope.cache.idStyles[key];
                    }); 
                    
                    $scope.outputCSSStylesAfterWait('id', $scope.cache.idCSS);
				});
			}
		}

		$scope.functionEnd("updateAllComponentsCacheStyles()");
	}


	/**
	 * Remove component styles in cache array
	 *
	 * @since 0.2.5
	 * @author Ilya K.
	 */
	
	$scope.removeComponentCacheStyles = function(id) {
		// remove from array
		delete $scope.cache.idStyles[id];
	}
	

	$scope.getDynamicBackground = function(type, params) {

		var target, previewBG = false;

		if(type === 'id' && $scope.dynamicBackgrounds[params['id']]) {
			target = $scope.dynamicBackgrounds[params['id']]
		}
		else if(type === 'class' && $scope.dynamicBackgrounds['classes'][params['id']]) {
			target = $scope.dynamicBackgrounds['classes'][params['id']];
		}
		else if(type === 'selector' && $scope.dynamicBackgrounds['selectors'][params['id']]) {
			target = $scope.dynamicBackgrounds['selectors'][params['id']];
		}

		// if media 
		if(params['media']) {
			previewBG = target['media'][params['media']][params['stateName'] === 'id' ? 'original' : params['stateName']];
		}
		else {

			previewBG = target[params['stateName'] === 'id' ? 'original' : params['stateName']];
			
		}

		return previewBG;
	}

    /**
     * Get one single components CSS
     *
     * @since 0.2.5
     * @author Ilya K.
     * @return {string} CSS styles
     */

    $scope.getSingleComponentCSS = function(componentOptions, componentId, isMedia, whichMedia) {

    	if (componentOptions.name == "ct_reusable" && componentOptions.original) {

    		// holder for reusable CSS
			$scope.reusableCSS 			= {};
			$scope.reusableCSS.styles 	= "  ";

			var viewId = componentOptions.original.view_id;

    		// add this item CSS
			if ($scope.postsData[viewId]) {
				$scope.generateTreeCSS($scope.postsData[viewId].post_tree, $scope.reusableCSS);
				$scope.outputCSSStyles("ct-re-usable-styles-"+viewId, $scope.reusableCSS.styles);
			}

			return "";
    	}

    	if ($scope.log) {
    		console.log("getSingleComponentCSS()", componentId, componentOptions, isMedia);
    		$scope.functionStart("getSingleComponentCSS()");
		}

    	var style 		= "",
    		important 	= "",
    		paragraph 	= "",
    		currentState = "",
    		componentDefaults = $scope.defaultOptions[componentOptions.name];

    	if (undefined == componentOptions['original']) {
    		componentOptions['original'] = [];
    	}

		// loop components' states
		for(var stateName in componentOptions) { 
			if (componentOptions.hasOwnProperty(stateName)) {
				var stateOption = componentOptions[stateName];

				// skip "id" original options for media
				if ( stateName=="original" && !isMedia ) {
					continue;
				}

				if (componentId != 0 && typeof(stateOption) === "object" && 
					stateName != "model" && 
					stateName != "classes" &&
					stateName != "media" ) {
					
					// make a copy to not modify original Object
					var mergedOptions 			= angular.copy(componentOptions[stateName]),
						stateOptions  			= $scope.getCSSOptions(componentId, stateName, mergedOptions, componentOptions.name);
						mergedOriginalOptions 	= angular.copy(componentOptions['original']),
						originalOptions 		= $scope.getCSSOptions(componentId, 'original', mergedOriginalOptions, componentOptions.name);

					// apply styles to id
					if (
							// if "id" state
							stateName == "id" ||

							// editing media
							( isMedia && stateName == "original" ) ||
							
							// or if currently editing exactly this id and state
							( stateName 	== $scope.currentState && 
							  componentId 	== $scope.component.active.id && 
							  
							  $scope.isEditing('id') && ( stateName == "hover" || stateName == "active" || stateName == "focus")
							) 
						) 
					{
						currentState = "";
					}
					// apply style to state/pseudo-element
					else {
						currentState = ":" + stateName;
					}

					// check if options is for Paragraph component
					paragraph = ( componentOptions.name == "ct_paragraph" ) ? " p" : "";

					// handle columns gutter
					if ( componentOptions.name == "ct_columns" ) {

						var gutter = $scope.getWidth(componentOptions['original']['gutter']);

						style += '#' + componentOptions.selector + " .ct-column" + currentState + "{";
						style += "margin-right" + ":" + (gutter.value/2) + gutter.unit + ";";
						style += "margin-left" + ":" + (gutter.value/2) + gutter.unit + ";";
						style += '}';
					}

					// handle new columns gutter
					// We might add this in future
					/*if ( componentOptions.name == "ct_new_columns" ) {

						var gutter = $scope.getWidth(componentOptions['original']['gutter']);

						style += '#' + componentOptions.selector + currentState + "{";
						style += "margin-right" + ":-" + (20+gutter.value) + "px;";
						style += "margin-left" + ":-" + (20+gutter.value) + "px;";
						style += "width: calc(100% + "+(40+gutter.value+gutter.value)+"px);";
						style += '}';


						style += '#' + componentOptions.selector + currentState + " > .ct-div-block {";
						style += "margin-right" + ":" + gutter.value + "px;";
						style += "margin-left" + ":" + gutter.value + "px;";
						style += '}';
					}*/

					// placeholder options just for builder
					if (componentOptions['original']["dont_render"]=='true') {
						style += '#' + componentOptions.selector + "{";
						if (componentOptions['original']["placeholder-width"]){
							style += 'width:' + componentOptions['original']["placeholder-width"] + (componentOptions['original']["placeholder-width-unit"] || componentDefaults["placeholder-width-unit"]) + ";";
						}
						if (componentOptions['original']["placeholder-height"]){
							style += 'height:' + componentOptions['original']["placeholder-height"] + (componentOptions['original']["placeholder-height-unit"] || componentDefaults["placeholder-height-unit"]) + ";";
						}
						style += "}";
					}

					// handle section special options
					if ( componentOptions.name == "ct_section" ) {

						style += '#' + componentOptions.selector + ">.ct-section-inner-wrap" + currentState + "{";

							if (undefined==stateOptions['section-width']) {
								stateOptions['section-width'] = componentDefaults['section-width'];
							}
						
							if ( stateOptions['section-width'] == "page-width" ) {
								//style += "max-width" + ":" + $scope.getPageWidth() + "px;";
							}
							if ( stateOptions['section-width'] == "custom" && stateOptions['custom-width']) {
								style += "max-width" + ":" + stateOptions['custom-width'] + ";";
							}
							if ( stateOptions['section-width'] == "full-width" ) {
								style += "max-width:100%;";
							}
						
							// custom-padding
							if ( stateOptions['container-padding-top'] ) {
								style += "padding-top" 		+ ":" + stateOptions['container-padding-top'] + ";";
							}
							if ( stateOptions['container-padding-right'] ) {
								style += "padding-right"	+ ":" + stateOptions['container-padding-right'] + ";";
							}
							if ( stateOptions['container-padding-bottom'] ) {
								style += "padding-bottom" 	+ ":" + stateOptions['container-padding-bottom'] + ";";
							}
							if ( stateOptions['container-padding-left'] ) {
								style += "padding-left" 	+ ":" + stateOptions['container-padding-left'] + ";";
							}

							// flex options since 2.0
							if ( stateOptions['display'] ) {
								style += "display:" + stateOptions['display'] + ";";
							}

							var reverse = (stateOptions['flex-reverse'] == 'reverse') ? "-reverse" : "";
							
							if ( stateOptions['flex-direction'] ) {
								style += "flex-direction:" + stateOptions['flex-direction'] + reverse + ";";
							}

							if ( stateOptions['justify-content'] ) {
								style += "justify-content:" + stateOptions['justify-content'] + ";";
							}

							if ( stateOptions['align-content'] ) {
								style += "align-content:" + stateOptions['align-content'] + ";";
							}

							if ( stateOptions['align-items'] ) {
								style += "align-items:" + stateOptions['align-items'] + ";";
							}

							if ( stateOptions['flex-wrap'] ) {
								style += "flex-wrap:" + stateOptions['flex-wrap'] + ";";
							}
						
						style += '}';

						if (componentOptions['original']["video_background_hide"]) {
							style += "@media (max-width: "+$scope.getMediaMaxSize(componentOptions['original']['video_background_hide'])+") {";
							style += '#' + componentOptions.selector + " .oxy-video-container { display: none; }";
							style += "}";
						}

						if (componentOptions['original']["video_background_overlay"]) {
							style += '#' + componentOptions.selector + " .oxy-video-overlay { background-color: " + componentOptions['original']["video_background_overlay"] + "; }";
						}
					}

					if (componentOptions.name == "oxy_posts_grid") {

						$scope.mapCSSProperty('title_size', 'font-size',                         '.oxy-post-title');
				        $scope.mapCSSProperty('title_color', 'color',                            '.oxy-post-title');
				        $scope.mapCSSProperty('title_hover_color', 'color',                      '.oxy-post-title:hover');
				        $scope.mapCSSProperty('meta_size', 'font-size',                          '.oxy-post-meta');
				        $scope.mapCSSProperty('meta_color', 'color',                             '.oxy-post-meta');
				        $scope.mapCSSProperty('content_size', 'font-size',                       '.oxy-post-content');
				        $scope.mapCSSProperty('content_color', 'color',                          '.oxy-post-content');
				        $scope.mapCSSProperty('read_more_size', 'font-size',                     '.oxy-read-more');
				        $scope.mapCSSProperty('read_more_text_color', 'color',                   '.oxy-read-more');
				        $scope.mapCSSProperty('read_more_button_color', 'background-color',      '.oxy-read-more');
				        $scope.mapCSSProperty('read_more_text_hover_color', 'color',             '.oxy-read-more:hover');
				        $scope.mapCSSProperty('read_more_button_hover_color', 'background-color','.oxy-read-more:hover');
				        $scope.mapCSSProperty('paginate_color', 'color',                         '.oxy-easy-posts-pages');
				        $scope.mapCSSProperty('paginate_alignment', 'text-align',                '.oxy-easy-posts-pages');
				        $scope.mapCSSProperty('paginate_link_color', 'color',                    '.oxy-easy-posts-pages a.page-numbers');
				        $scope.mapCSSProperty('paginate_link_hover_color', 'color',              '.oxy-easy-posts-pages a.page-numbers:hover');

				        if (componentOptions['original']['read_more_display_as']=="button") {
							style += '#' + componentOptions.selector + " .oxy-read-more {";
							style += "text-decoration:none;";
							style += "padding:0.75em 1.5em;";
							style += "line-height:1;";
							style += "border-radius:3px;";
							style += "display:inline-block;";
							style += "}";
						}
						else {
					        $scope.unmapCSSProperty('background-color','.oxy-read-more');
					        $scope.unmapCSSProperty('background-color','.oxy-read-more:hover');
						}

				        style += "@media (max-width: "+$scope.getMediaMaxSize(componentOptions['original']['posts_5050_below'])+") {";
						style += '#' + componentOptions.selector + " .oxy-post { width: 50% !important; }";
						style += "}";

						style += "@media (max-width: "+$scope.getMediaMaxSize(componentOptions['original']['posts_100_below'])+") {";
						style += '#' + componentOptions.selector + " .oxy-post { width: 100% !important; }";
						style += "}";																					
					}

					if (componentOptions.name == "oxy_comment_form") {

				        $scope.mapCSSProperty('form_field_border_color', 'border-color', 	'input, textarea');
        				$scope.mapCSSProperty('form_field_text_color', 'color',          	'input, textarea');
        				$scope.mapCSSProperty('field_border_radius', 'border-radius',    	'input, textarea');

        				$scope.mapCSSProperty('submit_button_background_color', 'background-color',  '#submit');
        				$scope.mapCSSProperty('submit_button_text_color', 'color',                   '#submit');
				    }

				    if (componentOptions.name == "oxy_login_form") {

				        $scope.mapCSSProperty('form_field_border_color', 'border-color', 	'input, textarea');
        				$scope.mapCSSProperty('form_field_text_color', 'color',          	'input, textarea');
        				$scope.mapCSSProperty('field_border_radius', 'border-radius',    	'input, textarea');

        				$scope.mapCSSProperty('submit_button_background_color', 'background-color',  'input[type=submit]');
        				$scope.mapCSSProperty('submit_button_text_color', 'color',                   'input[type=submit]');
				    }

				    if (componentOptions.name == "oxy_search_form") {

				        $scope.mapCSSProperty('form_field_border_color', 'border-color', 	'input, textarea');
        				$scope.mapCSSProperty('form_field_text_color', 'color',          	'input, textarea');
        				$scope.mapCSSProperty('field_border_radius', 'border-radius',    	'input, textarea');

        				$scope.mapCSSProperty('submit_button_background_color', 'background-color',  'input[type=submit]');
        				$scope.mapCSSProperty('submit_button_text_color', 'color',                   'input[type=submit]');
				    }

					if (componentOptions.name == "ct_video") {

						style = style + '#' + componentOptions.selector + ">.oxygen-vsb-responsive-video-wrapper" + currentState + "{";
						
						var videoPadding = componentOptions['original']['video-padding-bottom'] || componentDefaults['video-padding-bottom'];

						if ( videoPadding ) {
							style += "padding-bottom:" + videoPadding + ";";
						}

						style  += "}";
					}

					if (componentOptions.name == "ct_new_columns" && componentOptions['original']['set-columns-width-50']
																  && componentOptions['original']['set-columns-width-50'] != 'never') {

						style += "@media (max-width: "+$scope.getMediaMaxSize(componentOptions['original']['set-columns-width-50'])+") {";
						style += '#' + componentOptions.selector + "> .ct-div-block {width: 50% !important;}";
						style += "}";
					}

					if ( componentOptions.name == "ct_new_columns" ) {
					
						var reverseColumnOrder 			 = parseInt($scope.getMediaMaxSize(componentOptions['original']['reverse-column-order'])) || 0,
							stackColumnsVertically 		 = parseInt($scope.getMediaMaxSize(componentOptions['original']['stack-columns-vertically'])) || 0,
							reverseColumnOrderStyles 	 = "",
							stackColumnsVerticallyStyles = "";

						if (componentOptions['original']['reverse-column-order']=="always") {
							reverseColumnOrder = 9999999999;
						}

						if ( componentOptions['original']['stack-columns-vertically'] && 
							 componentOptions['original']['stack-columns-vertically'] != 'never') {

							stackColumnsVerticallyStyles += "@media (max-width: "+$scope.getMediaMaxSize(componentOptions['original']['stack-columns-vertically'])+") {";
								stackColumnsVerticallyStyles += '#' + componentOptions.selector + "> .ct-div-block {";
								stackColumnsVerticallyStyles += "width: 100% !important;";
								stackColumnsVerticallyStyles += "}";
								
								stackColumnsVerticallyStyles += '#' + componentOptions.selector + "{";
								if (stackColumnsVertically>reverseColumnOrder) {
									stackColumnsVerticallyStyles += "flex-direction: column;";
								}
								else {
									stackColumnsVerticallyStyles += "flex-direction: column-reverse;";
								}
								stackColumnsVerticallyStyles += "}";
							stackColumnsVerticallyStyles += "}";
						}

						if (componentOptions['original']['stack-columns-vertically'] === undefined ) {

							style += "@media (max-width: "+$scope.getMediaMaxSize(componentDefaults['stack-columns-vertically'])+") {";
							style += '#' + componentOptions.selector + "> .ct-div-block {width: 100% !important;}";
							style += "}";
						}

						if ( componentOptions['original']['reverse-column-order'] && 
							 componentOptions['original']['reverse-column-order'] != 'never' && 
							 componentOptions['original']['reverse-column-order'] != 'always' ) {

							reverseColumnOrderStyles += "@media (max-width: "+$scope.getMediaMaxSize(componentOptions['original']['reverse-column-order'])+") {";
							reverseColumnOrderStyles += '#' + componentOptions.selector + "{";
							if (stackColumnsVertically<reverseColumnOrder) {
								reverseColumnOrderStyles += "flex-direction: row-reverse;";
							}
							else {
								reverseColumnOrderStyles += "flex-direction: column-reverse;";
							}
							reverseColumnOrderStyles += "}";
							reverseColumnOrderStyles += "}";
						}

						if ( componentOptions['original']['reverse-column-order'] == 'always') {
		
							style += '#' + componentOptions.selector + "{flex-direction: row-reverse;}";
						}

						if (stackColumnsVertically<reverseColumnOrder) {
							style += reverseColumnOrderStyles;
							style += stackColumnsVerticallyStyles;
						}
						else {
							style += stackColumnsVerticallyStyles;
							style += reverseColumnOrderStyles;
						}

					}

					if ( componentOptions.name == "oxy_header" ) {
						
						var stickyStyles = "";

						if ( componentOptions['original']['sticky-media'] && 
							 componentOptions['original']['sticky-media'] != 'never') {

							stickyStyles += "@media (min-width: "+$scope.getMediaMinSize(componentOptions['original']['sticky-media'])+") {";
								
								stickyStyles += '#' + componentOptions.selector + ".oxy-header-wrapper.oxy-header.oxy-sticky-header.oxy-sticky-header-active {";
								stickyStyles += "position: fixed; top: 0; left: 0; right: 0; z-index: 2147483640;";
								stickyStyles += componentOptions['original']['sticky-background-color'] ? "background-color:"+componentOptions['original']['sticky-background-color']+";" : "";
								var boxShadow = componentOptions['original']['sticky-box-shadow'] || componentDefaults['sticky-box-shadow'];
								stickyStyles += "box-shadow:" + boxShadow +";";
								stickyStyles += "}";

								stickyStyles += '#' + componentOptions.selector + ".oxy-sticky-header-active .oxygen-hide-in-sticky {";
								stickyStyles += "display:none;";
								stickyStyles += "}";
								
							stickyStyles += "}";
						}

						if (stickyStyles) {
							style += stickyStyles;
						}
					}

					if ( componentOptions.name == "oxy_header" || componentOptions.name == "oxy_header_row" ) {
						
						var stackHeaderVerticallyStyles = "";

						if ( componentOptions['original']['stack-header-vertically'] && 
							 componentOptions['original']['stack-header-vertically'] != 'never') {

							stackHeaderVerticallyStyles += "@media (max-width: "+$scope.getMediaMaxSize(componentOptions['original']['stack-header-vertically'])+") {";
								
								stackHeaderVerticallyStyles += '#' + componentOptions.selector + " .oxy-header-container {";
								stackHeaderVerticallyStyles += "flex-direction: column;";
								stackHeaderVerticallyStyles += "}";
								
								stackHeaderVerticallyStyles += '#' + componentOptions.selector + " .oxy-header-container > div{";
								stackHeaderVerticallyStyles += "justify-content: center;";
								stackHeaderVerticallyStyles += "}";
								
							stackHeaderVerticallyStyles += "}";
						}

						if (stackHeaderVerticallyStyles) {
							style += stackHeaderVerticallyStyles;
						}
					}

					if ( componentOptions.name == "oxy_header_row" ) {
						
						var hideRowStyles = "";

						if ( componentOptions['original']['hide-row'] && 
							 componentOptions['original']['hide-row'] != 'never') {

							hideRowStyles += "@media (max-width: "+$scope.getMediaMaxSize(componentOptions['original']['hide-row'])+") {";
								
								hideRowStyles += '#' + componentOptions.selector + " {";
								hideRowStyles += "display: none;";
								hideRowStyles += "}";
								
							hideRowStyles += "}";
						}

						if (hideRowStyles) {
							style += hideRowStyles;				
						}
					}

					if ( componentOptions.name == "ct_slider" ) {

						if ( componentOptions['original']['slider-arrow-color'] && 
							 componentOptions['original']['slider-arrow-color'] == 'lighter') {

								style += '#' + componentOptions.selector + " .unslider-arrow {";
								style += "background-color: rgba(255,255,255,0.2); ";
								style += "}";
						}

						if ( componentOptions['original']['slider-dot-color'] ) {

								style += '#' + componentOptions.selector + " .unslider-nav ol li {";
								style += "border-color: " + componentOptions['original']['slider-dot-color'] + "; ";
								style += "}";

								style += '#' + componentOptions.selector + " .unslider-nav ol li.unslider-active {";
								style += "background-color: " + componentOptions['original']['slider-dot-color'] + "; ";
								style += "}";
						}
					}


					if (componentOptions.name == "ct_link_button") {
						
						if (!isMedia && stateName == "id") {
							var buttonStyle 	= originalOptions['button-style'] || componentDefaults['button-style'],
								buttonColor 	= stateOptions['button-color'],
								buttonSize 		= stateOptions['button-size'],
								buttonTextColor = stateOptions['button-text-color'];
						}
						else {
							var buttonStyle 	= $scope.component.options[componentId] ? $scope.component.options[componentId]['original']['button-style'] : false,
								buttonColor 	= stateOptions['button-color'],
								buttonSize 		= stateOptions['button-size'],
								buttonTextColor = stateOptions['button-text-color'];
						}
						style = style + '#' + componentOptions.selector  + currentState + "{";

						if (buttonStyle == 1 && buttonColor) { // solid
							style += "background-color: " + buttonColor + ";";
							style += "border: 1px solid " + buttonColor + ";";
							if (buttonTextColor) {
								style += "color: " + buttonTextColor + ";";
							}
						}
						
						if (buttonStyle == 2) { // outline
							style += "background-color: transparent;";
							if (buttonColor) {
								style += "border: 1px solid " + buttonColor + ";";
								style += "color: " + buttonColor + ";";
							}
							else {
								style += "color: " + componentDefaults['button-color'] + ";";
							}
						}

						if (buttonSize) {
							var substracted = buttonStyle == 2 ? 1 : 0;
							style += "padding: " + (parseInt(buttonSize)-substracted) + 'px ' + (parseInt(buttonSize)*1.6-substracted) + 'px;';
						}

						style += "}";
					}

					if (componentOptions.name == "ct_fancy_icon") {
						
						if (!isMedia && stateName == "id") {
							var iconStyle 			= originalOptions['icon-style'] || componentDefaults['icon-style'],
								iconColor 			= stateOptions['icon-color'],
								iconBackgroundColor = stateOptions['icon-background-color'] || componentDefaults['icon-background-color'],
								iconPadding 		= stateOptions['icon-padding'] || componentDefaults['icon-padding']+componentDefaults['icon-padding-unit'],
								iconSize 			= stateOptions['icon-size'];
						}
						else {
							var iconStyle 			= $scope.component.options[componentId] ? $scope.component.options[componentId]['original']['icon-style'] : false,
								iconColor 			= stateOptions['icon-color'],
								iconBackgroundColor = stateOptions['icon-background-color'],
								iconPadding 		= stateOptions['icon-padding'],
								iconSize 			= stateOptions['icon-size'];
						}

						style = style + '#' + componentOptions.selector + currentState + "{";

						if (iconStyle == 1) { // outline
							style += "border: 1px solid;";
						}

						if (iconStyle == 2) { // solid
							if (iconBackgroundColor) {
								style += "background-color: " + iconBackgroundColor + ";";
								style += "border: 1px solid " + iconBackgroundColor + ";";
							}
						}
						
						if (iconStyle == 1 || iconStyle == 2) { // outline or solid
						 	if (iconPadding) {
						 		style += "padding:" + iconPadding + ";";
						 	}
						} 

						if (iconColor) {
						 	style += "color:" + iconColor + ";";
						}

						style += "}";

						if (iconSize) {
							style = style + "#" + componentOptions.selector + currentState + ">svg {";
						 	style += "width: " + iconSize + ";";
						 	style += "height: " + iconSize + ";";
						 	style += "}";
						}
					}

					if (componentOptions.name == "oxy_social_icons") {

						var iconStyle 		= componentOptions['original']['icon-style'] || componentDefaults['icon-style'],
							useBrandColors 	= componentOptions['original']['icon-use-brand-colors'] || componentDefaults['icon-use-brand-colors'];

						style += '#' + componentOptions.selector + ".oxy-social-icons {";
							style += "flex-direction: " + ( componentOptions['original']['icon-layout'] || componentDefaults['icon-layout'] ) + ";";
							style += "margin-right: -" + ( componentOptions['original']['icon-space-between-icons'] || componentDefaults['icon-space-between-icons'] ) + "px;";
							style += "margin-bottom: -" + ( componentOptions['original']['icon-space-between-icons'] || componentDefaults['icon-space-between-icons'] ) + "px;";
						style += "}";

						style += '#' + componentOptions.selector + ".oxy-social-icons a {";
							style += "font-size: " + ( componentOptions['original']['icon-size'] || componentDefaults['icon-size'] ) + "px; ";
							style += "margin-right: " + ( componentOptions['original']['icon-space-between-icons'] || componentDefaults['icon-space-between-icons'] ) + "px;";
							style += "margin-bottom: " + ( componentOptions['original']['icon-space-between-icons'] || componentDefaults['icon-space-between-icons'] ) + "px;";
							if (iconStyle=='circle') {
								style += "border-radius: 50%;";
							}
							else if (iconStyle=='square') {
								style += "border-radius: 0%;";
							}
							if (iconStyle!='blank') {
								style += "background-color: " + ( componentOptions['original']['icon-background-color'] || componentDefaults['icon-background-color'] ) + ";";
							}
						style += "}";

						style += '#' + componentOptions.selector + ".oxy-social-icons a:hover {";
						if (iconStyle!='blank') {
							style += "background-color: " + ( componentOptions['original']['icon-background-hover-color'] || componentDefaults['icon-background-hover-color'] ) + ";";
						}
						style += "}";

						if (useBrandColors=='yes') {
							for(var key in $scope.socialIcons.networks) { 
								if ($scope.socialIcons.networks.hasOwnProperty(key)) {
									var network 	= $scope.socialIcons.networks[key],
										color 		= $scope.socialIcons.networkColors[network],
										hoverColor 	= $scope.socialIcons.networkHoverColors[network];

									if (iconStyle!='blank') {
										style += '#' + componentOptions.selector + ".oxy-social-icons a.oxy-social-icons-"+network+" {";
										style += "background-color: "+color+";";
										style += "}";

										style += '#' + componentOptions.selector + ".oxy-social-icons a.oxy-social-icons-"+network+":hover {";
										style += "background-color: "+hoverColor+";";
										style += "}";

										componentOptions['original']['icon-color'] 		= "#ffffff";
										componentOptions['original']['icon-hover-color']= "#ffffff";
									}
									else {
										style += '#' + componentOptions.selector + ".oxy-social-icons a.oxy-social-icons-"+network+" svg {";
										style += "color: "+color+";";
										style += "}";

										style += '#' + componentOptions.selector + ".oxy-social-icons a.oxy-social-icons-"+network+":hover svg {";
										style += "color: "+hoverColor+";";
										style += "}";
									}
								}
							}
						}

						style += '#' + componentOptions.selector + ".oxy-social-icons a svg {";
						if (iconStyle!='blank') {
							style += "width: 0.5em; height: 0.5em;";
						} else {
							style += "width: 1em; height: 1em;";
						}
						style += "color: " + ( componentOptions['original']['icon-color'] || componentDefaults['icon-color'] ) + "; ";
						style += "}";

						style += '#' + componentOptions.selector + ".oxy-social-icons a:hover svg {";
						style += "color: " + ( componentOptions['original']['icon-hover-color'] || componentDefaults['icon-hover-color'] ) + "; ";
						style += "}";
					}


					if (componentOptions.name == "oxy_testimonial") {

						var flexAlign,
				            textAlign,
				            mobileFlexAlign,
				            mobileTextAlign,
				            marginCSS = "",
				            spacing = stateOptions['testimonial_image_spacing'];

				        if (stateOptions["testimonial_layout"]=='vertical') {

				        	marginCSS = "margin:0;";
                			spacing = stateOptions['testimonial_image_spacing'] || componentDefaults['testimonial_image_spacing']+componentDefaults['testimonial_image_spacing-unit'];

				        	if (stateOptions["testimonial_image_position"]=='bottom') {
				            	marginCSS += "margin-top";
				            }
				            else {
				            	marginCSS += "margin-bottom";
				            }
				        } else {
				            if (stateOptions["testimonial_image_position"]=='bottom') {
				            	
				            	marginCSS = "margin:0;";
                				spacing = stateOptions['testimonial_image_spacing'] || componentDefaults['testimonial_image_spacing']+componentDefaults['testimonial_image_spacing-unit'];

				            	marginCSS += "margin-left";
				            }
				            else {
				            	marginCSS += "margin-right";
				            }
				        }

				        marginCSS += ": " + spacing + ";";

						if (stateOptions["testimonial_content_alignment"]=='left') {
				            flexAlign = "flex-start";
				            textAlign = "left";
				        } else if (stateOptions["testimonial_content_alignment"]=='center') {
				            flexAlign = "center";
				            textAlign = "center";
				        } else if (stateOptions["testimonial_content_alignment"]=='right') {
				            flexAlign = "flex-end";
				            textAlign = "right";
				        }

				        if (componentOptions['original']["testimonial_mobile_content_alignment"]=='left') {
				            mobileFlexAlign = "flex-start";
				            mobileTextAlign = "left";
				        } else if (componentOptions['original']["testimonial_mobile_content_alignment"]=='center') {
				            mobileFlexAlign = "center";
				            mobileTextAlign = "center";
				        } else if (componentOptions['original']["testimonial_mobile_content_alignment"]=='right') {
				            mobileFlexAlign = "flex-end";
				            mobileTextAlign = "right";
				        }

				        if (stateOptions["testimonial_layout"]=='vertical') {
				        	style += '#' + componentOptions.selector + ' {' +
				                'flex-direction: column;' +
				            '}';
				        }
				        else if (stateOptions["testimonial_layout"]=='horizontal')  {
				            style += '#' + componentOptions.selector + ' {' +
				                'flex-direction: row;' +
				                'align-items: center;' +
				           '}';
				        }
				        
				        if (stateOptions['testimonial_image_position'] == 'top') {
				            style += '#' + componentOptions.selector + ' .oxy-testimonial-photo-wrap {' +
				                'order: 1;' +
				           '}';
				        } 
				        else if (stateOptions['testimonial_image_position'] == 'bottom') {
				            style += '#' + componentOptions.selector + ' .oxy-testimonial-photo-wrap {' +
				                'order: 3;' +
				           '}';
				        } 

						style += '#' + componentOptions.selector + " .oxy-testimonial-photo {"+
				            "width:"+ stateOptions['testimonial_image_size'] + ";" +
				            "height:"+ stateOptions['testimonial_image_size'] + ";" +
				            marginCSS +
				        "}";

						style += '#' + componentOptions.selector + " .oxy-testimonial-photo-wrap,"+
								 '#' + componentOptions.selector + " .oxy-testimonial-author-wrap,"+
								 '#' + componentOptions.selector + " .oxy-testimonial-content-wrap {"+
				            "align-items:"+ flexAlign + ";" +
				            "text-align:"+ textAlign + ";" +
				        "}";

						style += '#' + componentOptions.selector + " .oxy-testimonial-text {"+
				            "margin-bottom:"+ stateOptions['testimonial_text_space_below'] + ";" +
				            $scope.generateTypographyCSS(stateOptions,'testimonial_text_typography') +
				        "}";

				        style += '#' + componentOptions.selector + " .oxy-testimonial-author {"+
				            "margin-bottom:" + stateOptions['testimonial_author_space_below'] + ";" +
				            $scope.generateTypographyCSS(stateOptions,'testimonial_author_typography') +
				        "}";

				        style += '#' + componentOptions.selector + " .oxy-testimonial-author-info {"+
				            "margin-bottom:" + stateOptions['testimonial_author_info_space_below'] + ";" +
				            $scope.generateTypographyCSS(stateOptions,'testimonial_author_info_typography') +
				        "}";

				        maxSize = $scope.getMediaMaxSize(componentOptions['original']['testimonial_vertical_layout_below'])
				        if (maxSize) {

				        	var marginPosition;
				        	if (stateOptions["testimonial_image_position"]=='bottom') {
		                        marginPosition = "top";
		                    } 
		                    else {
		                        marginPosition = "bottom";
		                    }

					        style += '@media (max-width: '+ maxSize + '){' +
				                '#' + componentOptions.selector + ' {' +
				                    'flex-direction: column !important;' +
				                '}' +  

				                '#' + componentOptions.selector + ' .oxy-testimonial-photo {' +
				                    'margin: 0;'+
				                    'margin-'+marginPosition+':' + stateOptions['testimonial_image_spacing'] + ';' +
				                '}'+

				                '#' + componentOptions.selector + ' .oxy-testimonial-photo-wrap, ' +
				                '#' + componentOptions.selector + ' .oxy-testimonial-author-wrap, ' +
				                '#' + componentOptions.selector + ' .oxy-testimonial-content-wrap {' +
				                    'align-items:' + mobileFlexAlign + ';' +
				                    'text-align:' + mobileTextAlign + ';' +
				                '}'+
				            '}';
				        }

					}


					if (componentOptions.name == "oxy_icon_box") {

						var icon_position_flex_direction,
							icon_vertical_alignment_align_self,
							iconmargincss,
							mobileflexalign,
							mobiletextalign;
						
						if (stateOptions['icon_box_icon_position'] == 'top') {
				            icon_position_flex_direction = 'column';
				        } else if (stateOptions['icon_box_icon_position'] == 'left') {
				            icon_position_flex_direction = 'row';
				        } else if (stateOptions['icon_box_icon_position'] == 'right') {
				            icon_position_flex_direction = 'row-reverse';
				        } else if (stateOptions['icon_box_icon_position'] == 'bottom') {
				            icon_position_flex_direction = 'column-reverse';
				        }

				        if (stateOptions["icon_box_icon_position"]=='left' || stateOptions["icon_box_icon_position"]=='right') {
				        	var left = stateOptions["icon_box_icon_space_before"] || componentDefaults["icon_box_icon_space_before"]+componentDefaults["icon_box_icon_space_before-unit"];
				        		right = stateOptions["icon_box_icon_space_after"] || componentDefaults["icon_box_icon_space_after"]+componentDefaults["icon_box_icon_space_after-unit"];
				            iconmargincss = "margin-left: "+left+";\n";
				            iconmargincss += "margin-right: "+right+";\n";
				            iconmargincss += "margin-bottom: 0; margin-top: 0;";
				        } else {
				            iconmargincss = "margin-top: "+stateOptions["icon_box_icon_space_before"]+";\n";
				            iconmargincss += "margin-bottom: "+stateOptions["icon_box_icon_space_after"]+";\n";
				        }

				        if (stateOptions["icon_box_icon_position"]=='left' || stateOptions["icon_box_icon_position"]=='right') {
				            icon_vertical_alignment_align_self = stateOptions['icon_box_icon_vertical_alignment'];
				        } else {
				            if (stateOptions["icon_box_content_alignment"]=='left') {
				                icon_vertical_alignment_align_self = "flex-start";
				            } else if (stateOptions["icon_box_content_alignment"]=='center') {
				                icon_vertical_alignment_align_self = "center";
				            } else if (stateOptions["icon_box_content_alignment"]=='right') {
				                icon_vertical_alignment_align_self = "flex-end";
				            }          
				        }

				        if (stateOptions["icon_box_mobile_content_alignment"]=='left') {
				            mobileflexalign = "flex-start";
				            mobiletextalign = "left";
				        } else if (stateOptions["icon_box_mobile_content_alignment"]=='center') {
				            mobileflexalign = "center";
				            mobiletextalign = "center";
				        } else if (stateOptions["icon_box_mobile_content_alignment"]=='right') {
				            mobileflexalign = "flex-end";
				            mobiletextalign = "right";
				        } else {
				            mobileflexalign = "flex-start";
				            mobiletextalign = "left";            
				        }

			            style += '#' + componentOptions.selector + ' {'+
			                'text-align: '+ stateOptions['icon_box_content_alignment'] + ";" +
			                'flex-direction: '+ icon_position_flex_direction + ';'+
			            '}';

			            style += '#' + componentOptions.selector + ' .oxy-icon-box-icon {'+
			                iconmargincss +
			                'align-self: ' + icon_vertical_alignment_align_self + ';' +
			            '}';

			            style += '#' + componentOptions.selector + ' .oxy-icon-box-heading {'+
			                $scope.generateTypographyCSS(stateOptions,'icon_box_heading_typography')+
			                'margin-top: ' + stateOptions['icon_box_heading_space_above'] + ';' +
			                'margin-bottom: ' + stateOptions['icon_box_heading_space_below'] + ';' +
			            '}';

			            style += '#' + componentOptions.selector + ' .oxy-icon-box-text {'+
			                $scope.generateTypographyCSS(stateOptions,'icon_box_text_typography')+
			                'margin-top: ' + stateOptions['icon_box_text_space_above'] + ';' +
			                'margin-bottom: ' + stateOptions['icon_box_text_space_below'] + ';' +
			                'align-self: ' + icon_vertical_alignment_align_self + ';' +
			            '}';

			            style += '#' + componentOptions.selector + ' .oxy-icon-box-link {'+
			                'margin-top: ' + stateOptions['icon_box_link_space_above'] + ';' +
			                'margin-bottom: ' + stateOptions['icon_box_link_space_below'] + ';' +
			            '}';

			            maxSize = $scope.getMediaMaxSize(stateOptions['icon_box_vertical_layout_below']);
			            if (maxSize && !isMedia) {
				            style += '@media (max-width: ' + maxSize + '){' +
				                '#' + componentOptions.selector + '.oxy-icon-box {' +
				                    'flex-direction: column !important;' +
				                    'text-align: ' + mobiletextalign + ';' +
				                '}' +

				                '#' + componentOptions.selector + ' .oxy-icon-box-icon {' +
				                    'margin-left: 0;' +
				                    'margin-right: 0;' +
				                    'margin-top: ' + stateOptions["icon_box_icon_space_before"] + ';' +
				                    'margin-bottom: ' + stateOptions["icon_box_icon_space_after"] + ';' +
				                '}' +

				                '#' + componentOptions.selector + ' .oxy-icon-box-icon, .oxy-icon-box-text {' +
				                    'align-self: ' + mobileflexalign + ';' +
				                '}' +
				            '}';
				        }
					}


					if (componentOptions.name == "oxy_pricing_box") {

						var price_flex_alignment, price_flex_direction, price_justify_content, global_justify_content, image_justify_content, cta_justify_content;
						
						if (stateOptions["pricing_box_price_layout"]=='vertical') {
				            price_flex_direction = "column";
				            if (stateOptions["pricing_box_price_alignment"]=='left') {
				                price_flex_alignment = "flex-start";
				            } else if (stateOptions["pricing_box_price_alignment"]=='center') {
				                price_flex_alignment = "center";
				            } else if (stateOptions["pricing_box_price_alignment"]=='right') {
				                price_flex_alignment = "flex-end";
				            } 
				        } else if (stateOptions["pricing_box_price_layout"]=='horizontal') {
				            price_flex_direction = "row";
				        }

				        if (stateOptions["pricing_box_price_layout"]=='horizontal') {
				            if (stateOptions["pricing_box_price_alignment"]=='left') {
				                price_justify_content = "flex-start";
				            } else if (stateOptions["pricing_box_price_alignment"]=='center') {
				                price_justify_content = "center";
				            } else if (stateOptions["pricing_box_price_alignment"]=='right') {
				                price_justify_content = "flex-end";
				            }
				        }

				        if (stateOptions["pricing_box_global_alignment"]=='left') {
				            global_justify_content = "flex-start";
				        } else if (stateOptions["pricing_box_global_alignment"]=='center') {
				            global_justify_content = "center";
				        } else if (stateOptions["pricing_box_global_alignment"]=='right') {
				            global_justify_content = "flex-end";
				        }


				        if (stateOptions["pricing_box_image_alignment"]=='left') {
				            image_justify_content = "flex-start";
				        } else if (stateOptions["pricing_box_image_alignment"]=='center') {
				            image_justify_content = "center";
				        } else if (stateOptions["pricing_box_image_alignment"]=='right') {
				            image_justify_content = "flex-end";
				        } else if (global_justify_content) {
				        	iamge_justify_content = global_justify_content;
				        }

				        if (stateOptions["pricing_box_cta_alignment"]=='left') {
				            cta_justify_content = "flex-start";
				        } else if (stateOptions["pricing_box_cta_alignment"]=='center') {
				            cta_justify_content = "center";
				        } else if (stateOptions["pricing_box_cta_alignment"]=='right') {
				            cta_justify_content = "flex-end";
				        } else if (global_justify_content) {
				        	cta_justify_content = global_justify_content;
				        }


			            style += '#' + componentOptions.selector + ' .oxy-pricing-box-section {' +
			                $scope.generateArrayOptionsCSS(stateOptions, 'pricing_box_global') +
			                'text-align:' + stateOptions['pricing_box_global_alignment'] + ";" +
			            '}';

			            style += '#' + componentOptions.selector + ' .oxy-pricing-box-section.oxy-pricing-box-price {' +
			                'justify-content:' + global_justify_content + ';' +
			            '}';


			            /* IMAGE */
			            style += '#' + componentOptions.selector + ' .oxy-pricing-box-section.oxy-pricing-box-graphic {' +
			                $scope.generateArrayOptionsCSS(stateOptions, 'pricing_box_graphic') +
			                'background-color:' + stateOptions['pricing_box_graphic_background'] + ';' +
			                'text-align:'+ stateOptions['pricing_box_graphic_alignment'] + ';' +
			                'justify-content:' + image_justify_content + ';' +
			            '}';

			            /* TITLE */
			            style += '#' + componentOptions.selector + ' .oxy-pricing-box-section.oxy-pricing-box-title {' +
			                $scope.generateArrayOptionsCSS(stateOptions, 'pricing_box_title') +
			                'text-align:'+ stateOptions['pricing_box_title_alignment'] + ';' +
			                'background-color:' + stateOptions['pricing_box_title_background'] + ';' +
			            '}';

			            style += '#' + componentOptions.selector + ' .oxy-pricing-box-title-title {' +
			                $scope.generateTypographyCSS(stateOptions, 'pricing_box_title_typography') +
			            '}';

			            style += '#' + componentOptions.selector + ' .oxy-pricing-box-title-subtitle {' +
			                $scope.generateTypographyCSS(stateOptions, 'pricing_box_subtitle_typography') +
			            '}';


			            /* PRICE */

			            style += '#' + componentOptions.selector + ' .oxy-pricing-box-section.oxy-pricing-box-price {' +
			                $scope.generateArrayOptionsCSS(stateOptions, 'pricing_box_price') +
			                'text-align:' + stateOptions['pricing_box_price_alignment'] + ';' +
			                'background-color:' + stateOptions['pricing_box_price_background'] + ';' +
			                'flex-direction:' + price_flex_direction + ';' +
			                'justify-content:' + price_justify_content + ';' +
			                'align-items:' + price_flex_alignment + ';' +
			            '}';

			            style += '#' + componentOptions.selector + ' .oxy-pricing-box-currency {' +
			                $scope.generateTypographyCSS(stateOptions, 'pricing_box_price_amount_currency_typography') +
			            '}';

			            style += '#' + componentOptions.selector + ' .oxy-pricing-box-amount-main {' +
			                $scope.generateTypographyCSS(stateOptions, 'pricing_box_price_amount_main_typography') +
			            '}';

			            style += '#' + componentOptions.selector + ' .oxy-pricing-box-amount-decimal {' +
			                $scope.generateTypographyCSS(stateOptions, 'pricing_box_price_amount_decimal_typography') +
			            '}';
			            
			            style += '#' + componentOptions.selector + ' .oxy-pricing-box-term {' +
			                $scope.generateTypographyCSS(stateOptions, 'pricing_box_price_amount_term_typography') +
			            '}';

			            style += '#' + componentOptions.selector + ' .oxy-pricing-box-sale-price {' +
			                $scope.generateTypographyCSS(stateOptions, 'pricing_box_price_sale_typography') +
			                'margin-bottom:' + stateOptions['pricing_box_price_sale_space_below'] + ';' +
			            '}';

			            /* CONTENT */
			            style += '#' + componentOptions.selector + ' .oxy-pricing-box-section.oxy-pricing-box-content {' +
			                $scope.generateArrayOptionsCSS(stateOptions, 'pricing_box_content') +
			                'background-color:' + stateOptions['pricing_box_content_background'] + ';' +
			                'text-align:' + stateOptions['pricing_box_content_alignment'] + ';' +
			                $scope.generateTypographyCSS(stateOptions, 'pricing_box_content_typography') +
			            '}';

			            /* CTA */
			            style += '#' + componentOptions.selector + ' .oxy-pricing-box-section.oxy-pricing-box-cta {' +
			                $scope.generateArrayOptionsCSS(stateOptions, 'pricing_box_cta') +
			                'background-color:' + stateOptions['pricing_box_cta_background'] + ';' +
			                'text-align:' + stateOptions['pricing_box_cta_alignment'] + ';' +
			                'justify-content:' + cta_justify_content + ';' +
			            '}';
					}


					if (componentOptions.name == "oxy_progress_bar") {

						var stripecss = "",
							animation_css = [];
						
						if (stateOptions['progress_bar_stripes'] == 'true') {
				            stripecss = "background-image: linear-gradient(-45deg,rgba(255,255,255,.12) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.12) 50%,rgba(255,255,255,.12) 75%,transparent 75%,transparent);";
				        }
				        if (stateOptions['progress_bar_stripes'] == 'false') {
				            stripecss = 'background-image:none;';
				        }

				        if (stateOptions['progress_bar_animation_stripes'] == 'true') {
				        	var stripesDuration = stateOptions['progress_bar_animation_stripes_duration'] || 
				        				     componentDefaults['progress_bar_animation_stripes_duration'];
				            animation_css['stripes'] = "oxy_progress_bar_stripes "+stripesDuration+" linear infinite";
				        }
				        if (stateOptions['progress_bar_animation_stripes'] == 'false') {
				        	animation_css['stripes'] = "none 0s paused";
				        }

				        if (stateOptions['progress_bar_animate_width'] == 'true') {
				        	var widthDuration = stateOptions['progress_bar_animation_width_duration'] || 
				        				   componentDefaults['progress_bar_animation_width_duration'];
				            animation_css['width'] = "oxy_progress_bar_width "+widthDuration+" ease-out 1";
				        }
				        if (stateOptions['progress_bar_animate_width'] == 'false') {
				        	animation_css['width'] = "none 0s paused";
				        }

				        var bar_animation = [];
				        for(var key in animation_css) {
							bar_animation.push(animation_css[key]);
				        }

			            style += '#' + componentOptions.selector + currentState + ' .oxy-progress-bar-background {' +
			                'background-color:' + stateOptions['progress_bar_background_color'] + ';' +
			                 stripecss;
			            if (animation_css['stripes']) {
			            	style += 'animation:' + animation_css['stripes'] + ';';
			            }
			            style += '}';

			            style += '#' + componentOptions.selector + currentState + ' .oxy-progress-bar-progress-wrap {' +
			                'width:' + stateOptions['progress_bar_progress'] + ';' +
			            '}';

			            style += '#' + componentOptions.selector + currentState + ' .oxy-progress-bar-progress {' +
			                'background-color:' + stateOptions['progress_bar_bar_color'] + ';' +
			                'padding:' + stateOptions['progress_bar_bar_padding'] + ';' +
			                 stripecss +
			                'animation:' + bar_animation.join() + ';' +
			            '}';

			            style += '#' + componentOptions.selector + currentState + ' .oxy-progress-bar-overlay-text {' +
			                $scope.generateTypographyCSS(stateOptions, 'progress_bar_left_text_typography') +
			            '}';

			            style += '#' + componentOptions.selector + currentState + ' .oxy-progress-bar-overlay-percent {' +
			                $scope.generateTypographyCSS(stateOptions, 'progress_bar_right_text_typography') +
			            '}';
					}

					if (componentOptions.name == "oxy_toggle") {
						
						style += '#' + componentOptions.selector + currentState + ' .oxy-expand-collapse-icon {' +
			                'font-size:' + stateOptions['toggle_icon_size'] + ';' +
			            '}';

			            style += '#' + componentOptions.selector + currentState + ' .oxy-expand-collapse-icon::before,' +
			            		 '#' + componentOptions.selector + currentState + ' .oxy-expand-collapse-icon::after {' +
			                'background-color:' + stateOptions['toggle_icon_color'] + ';' +
			            '}';
					}

					if (componentOptions.name == "oxy_superbox") {

						// scaling
				        var superbox_secondary_scale_start  = stateOptions['superbox_secondary_scale_start'],
				        	superbox_secondary_scale_finish = stateOptions['superbox_secondary_scale_finish'],
				        	superbox_primary_scale_start    = stateOptions['superbox_primary_scale_start'],
				        	superbox_primary_scale_finish   = stateOptions['superbox_primary_scale_finish'],
				        	css, 
				        	superbox_secondary_initial_css, superbox_secondary_hover_css,
				        	superbox_primary_initial_css, superbox_primary_hover_css,
				        	superbox_secondary_scale_start_transform_css,
							superbox_secondary_scale_finish_transform_css,
							superbox_primary_scale_start_transform_css,
							superbox_primary_scale_finish_transform_css,
							superbox_secondary_initial_css = "",
							superbox_secondary_hover_css = "",
							superbox_primary_initial_css = "",
							superbox_primary_hover_css = "",
							state = ":hover";

						if ( componentOptions['original']['superbox_editing_mode'] == "as_hovered" ) {
							state = "";
						}

				        if (superbox_secondary_scale_start !== undefined)     superbox_secondary_scale_start_transform_css = "transform: scale("+superbox_secondary_scale_start+");";
				        if (superbox_secondary_scale_finish !== undefined)    superbox_secondary_scale_finish_transform_css = "transform: scale("+superbox_secondary_scale_finish+");";
				        if (superbox_primary_scale_start !== undefined)       superbox_primary_scale_start_transform_css = "transform: scale("+superbox_primary_scale_start+");";
				        if (superbox_primary_scale_finish !== undefined)      superbox_primary_scale_finish_transform_css = "transform: scale("+superbox_primary_scale_finish+");";

				        // sliding
				        if (stateOptions['superbox_secondary_slide_direction']) {
				            css = $scope.slide_position(stateOptions['superbox_secondary_slide_direction'], (stateOptions['superbox_secondary_slide_distance']||'')+"px");

				            if (stateOptions['superbox_secondary_slide_inorout'] == "in") {
				                superbox_secondary_initial_css = css['out_css'];
				                superbox_secondary_hover_css = css['in_css'];
				            } else if (stateOptions['superbox_secondary_slide_inorout'] == "out") {
				                superbox_secondary_initial_css = css['in_css'];
				                superbox_secondary_hover_css = css['out_css'];            
				            }
				        }

				        if (stateOptions['superbox_primary_slide_direction']) {

				            css = $scope.slide_position(stateOptions['superbox_primary_slide_direction'], (stateOptions['superbox_primary_slide_distance']||'')+"px");

				            if (stateOptions['superbox_primary_slide_inorout'] == "in") {
				                superbox_primary_initial_css = css['out_css'];
				                superbox_primary_hover_css = css['in_css'];
				            } else if (stateOptions['superbox_primary_slide_inorout'] == "out") {
				                superbox_primary_initial_css = css['in_css'];
				                superbox_primary_hover_css = css['out_css'];            
				            }
				        }

				        var transitionDuration = stateOptions['superbox_transition_duration'];

				        style += '#' + componentOptions.selector + " .oxy-superbox-secondary, " +
				         		 '#' + componentOptions.selector + " .oxy-superbox-primary {" +
				             'transition-duration:' + transitionDuration + ';' +
				        '}';

				        style += '#' + componentOptions.selector + " .oxy-superbox-secondary {" +
				            'opacity:' + stateOptions['superbox_secondary_opacity_start'] + ";" +
				            superbox_secondary_initial_css +
				            superbox_secondary_scale_start_transform_css +
				        '}';

				        if ( componentOptions['original']['superbox_editing_mode'] !== "as_not_hovered" ) {
					        style += '#' + componentOptions.selector + state + " .oxy-superbox-secondary {" +
					            'opacity:' + stateOptions['superbox_secondary_opacity_finish'] + ";" +
					            'z-index: 2147483642;' +
					            superbox_secondary_hover_css +
					            superbox_secondary_scale_finish_transform_css +
					        '}';
					    }

				        style += '#' + componentOptions.selector + " .oxy-superbox-primary {" +
				            'opacity:' + stateOptions['superbox_primary_opacity_start'] + ";" +
				            superbox_primary_initial_css +
				            superbox_primary_scale_start_transform_css +
				        '}';

				        if ( componentOptions['original']['superbox_editing_mode'] !== "as_not_hovered" ) {
					        style += '#' + componentOptions.selector + state + " .oxy-superbox-primary {" +
					            'opacity:' + stateOptions['superbox_primary_opacity_finish'] + ";" +
					            superbox_primary_hover_css +
					            superbox_primary_scale_finish_transform_css +
					        '}';
					    }
					}

					if (componentOptions.name == "oxy_nav_menu") {
						oxyNavMenuStyle 			= '#' + componentOptions.selector + " .oxy-nav-menu-list" + currentState + "{";
						oxyNavMenuStyleItem 		= '#' + componentOptions.selector + " .menu-item" + currentState + " a {";
						oxyNavMenuStyleActive 		= '#' + componentOptions.selector + " .current-menu-item a" + currentState + "{";
						oxyNavMenuStyleDropdowns 	= '#' + componentOptions.selector + ".oxy-nav-menu:not(.oxy-nav-menu-open) .sub-menu" + currentState + "{";
						oxyNavMenuStyleDropdownsItem= '#' + componentOptions.selector + ".oxy-nav-menu:not(.oxy-nav-menu-open) .sub-menu .menu-item a" + currentState + "{";
						oxyNavMenuStyleNotOpenItem  = '#' + componentOptions.selector + ".oxy-nav-menu:not(.oxy-nav-menu-open) .menu-item a" + currentState + "{";
						oxyNavMenuStyleDropdownsItemHover= '#' + componentOptions.selector + ".oxy-nav-menu:not(.oxy-nav-menu-open) .oxy-nav-menu-list .sub-menu .menu-item a:hover{";

						if (!componentOptions['original']['menu_dropdowns_background-color']) {
							oxyNavMenuStyleDropdowns += "background-color:" + componentOptions['original']['menu_hover_background-color'] + ";";
						}
						
						oxyNavMenuStyleDropdownsItem += "border: 0;";

						if (componentOptions['original']['menu_flex-direction']=='row') {
							oxyNavMenuStyleDropdownsItem += "padding-top:" 		+ componentOptions['original']['menu_padding-top'] + "px;";
							oxyNavMenuStyleDropdownsItem += "padding-bottom:"	+ componentOptions['original']['menu_padding-bottom'] + "px;";
						}
						else {
							oxyNavMenuStyleDropdownsItem += "padding-left:" 	+ componentOptions['original']['menu_padding-top'] + "px;";
							oxyNavMenuStyleDropdownsItem += "padding-rigth:"	+ componentOptions['original']['menu_padding-bottom'] + "px;";
						}

						if (componentOptions['original']['menu_justify-content']) {
							oxyNavMenuStyleNotOpenItem += "justify-content:" + componentOptions['original']['menu_justify-content'] + ";";
						}

						if (componentOptions['original']['menu_responsive']!='never') {
							style += "@media (max-width: "+$scope.getMediaMaxSize(componentOptions['original']['menu_responsive'])+") {";
							style += "#" + componentOptions.selector + " .oxy-nav-menu-list {display: none;}";
							style += "#" + componentOptions.selector + " .oxy-menu-toggle {display: initial;}";
							style += "#" + componentOptions.selector + ".oxy-nav-menu.oxy-nav-menu-open .oxy-nav-menu-list {display: initial;}";
							style += "}";
						}

						var menuWidth 	= parseInt(componentOptions['original']['menu_responsive_icon_size']||40),
							menuWrapSize= parseInt(componentOptions['original']['menu_responsive_icon_size']||40)+(parseInt(componentOptions['original']['menu_responsive_padding_size']||0)*2),
							menuHeight 	= parseInt(menuWidth * 0.8),
							lineHeight 	= parseInt(menuWidth * 0.15),
							top 		= (menuHeight / 2) - (lineHeight / 2);

						if (!isMedia && stateName == "id") {
							style += 
							'#' + componentOptions.selector + ".oxy-nav-menu.oxy-nav-menu-open {"+
								"background-color:" + componentOptions['original']['menu_responsive_background_color'] + ";" +
								"margin-top: 0 !important;" +
								"margin-right: 0 !important;" +
								"margin-left: 0 !important;" +
								"margin-bottom: 0 !important;" +
							
							'} #' + componentOptions.selector + ".oxy-nav-menu.oxy-nav-menu-open .menu-item a {" +
								"color:" 			+ componentOptions['original']['menu_responsive_link_color'] + ";" +
								"padding-top:" 		+ (componentOptions['original']['menu_responsive_padding_top'] || componentOptions['original']['menu_padding-top']) + "px;" +
								"padding-right:" 	+ (componentOptions['original']['menu_responsive_padding_right'] || componentOptions['original']['menu_padding-right']) + "px;" +
								"padding-bottom:" 	+ (componentOptions['original']['menu_responsive_padding_bottom'] || componentOptions['original']['menu_padding-bottom']) + "px;" +
								"padding-left:" 	+ (componentOptions['original']['menu_responsive_padding_left'] || componentOptions['original']['menu_padding-left']) + "px;" +
							
							'} #' + componentOptions.selector + ".oxy-nav-menu.oxy-nav-menu-open .menu-item a:hover {" +
								"color:" + componentOptions['original']['menu_responsive_hover_link_color'] +

							'} #' + componentOptions.selector + ".oxy-nav-menu .menu-item a:hover {" +
								"text-decoration:" + componentOptions['original']['menu_text-decoration'] +

							'} #' + componentOptions.selector + " .oxy-nav-menu-hamburger-wrap {"+
								"width:" 			+ menuWrapSize + "px;" +
								"height:" 			+ menuWrapSize + "px;" +
								"margin-top:" 		+ componentOptions['original']['menu_responsive_icon_margin'] + "px;" +
								"margin-bottom:" 	+ componentOptions['original']['menu_responsive_icon_margin'] + "px;" +
								"background-color:" + componentOptions['original']['menu_responsive_padding_color'] + ";" +
							
							"} #" + componentOptions.selector + " .oxy-nav-menu-hamburger {" +
								"width:"  + menuWidth + "px;" +
								"height:" + menuHeight + "px;" +
							
							"} #" + componentOptions.selector + " .oxy-nav-menu-hamburger .oxy-nav-menu-hamburger-line {" +
								"height:" + lineHeight + "px;" +
								"background-color:" + componentOptions['original']['menu_responsive_icon_color'] + ";" +
							
							"} #" + componentOptions.selector + ".oxy-nav-menu-open .oxy-nav-menu-hamburger .oxy-nav-menu-hamburger-line:first-child {" +
								"top:" + top + "px;" +
							
							"} #" + componentOptions.selector + ".oxy-nav-menu-open .oxy-nav-menu-hamburger .oxy-nav-menu-hamburger-line:last-child {" +
								"top:-"+ top + "px;}";
						}

					}

					// make menu icon same color as links color
					if ((componentOptions.name == "oxy_nav_menu") && 
						 componentOptions['original']["menu_color"]) {
						style += '#' + componentOptions.selector + " .oxy-nav-menu-hamburger-line{";
						style += "background-color:"+componentOptions['original']["menu_color"]+";";
						style += "}";
					}

					// make open menu bg same as header/row bg
					if ((componentOptions.name == "oxy_header" || componentOptions.name == "oxy_header_row") && 
						 componentOptions['original']["background-color"]) {
						style += '#' + componentOptions.selector + " .oxy-nav-menu-open,";
						style += '#' + componentOptions.selector + " .oxy-nav-menu:not(.oxy-nav-menu-open) .sub-menu{";
						style += "background-color:"+componentOptions['original']["background-color"]+";";
						style += "}";
					}

					// handle box-shadow options
					if ( stateOptions['box-shadow-color'] ) {

						var inset 	= (stateOptions['box-shadow-inset']=='inset') 		? stateOptions['box-shadow-inset']+" " : "";
						var hor 	= (stateOptions['box-shadow-horizontal-offset']) 	? stateOptions['box-shadow-horizontal-offset']+"px " : "";
						var ver 	= (stateOptions['box-shadow-vertical-offset']) 		? stateOptions['box-shadow-vertical-offset']+"px " : "";
						var blur 	= (stateOptions['box-shadow-blur']) 				? stateOptions['box-shadow-blur']+"px " : "0px ";
						var spread  = (stateOptions['box-shadow-spread']) 				? stateOptions['box-shadow-spread']+"px " : "";
						
						stateOptions['box-shadow'] = inset+hor+ver+blur+spread+stateOptions['box-shadow-color'];
					}

					// handle text-shadow options
					if ( stateOptions['text-shadow-color'] ) {

						var hor 	= (stateOptions['text-shadow-horizontal-offset']) 	? stateOptions['text-shadow-horizontal-offset']+"px " : "";
						var ver 	= (stateOptions['text-shadow-vertical-offset']) 	? stateOptions['text-shadow-vertical-offset']+"px " : "";
						var blur 	= (stateOptions['text-shadow-blur']) 				? stateOptions['text-shadow-blur']+"px " : "0px ";
						
						stateOptions['text-shadow'] = hor+ver+blur+stateOptions['text-shadow-color'];
					}

					// TODO: add check for elements with no ID styles to not output empty selectors
					style += '#' + componentOptions.selector + paragraph + currentState + "{";
					// make sure its the same selector, and styles are not being applied based on 'just' ID
			    	if ($scope.component.options[componentId] && ($scope.component.options[componentId].selector !== componentOptions.selector && !componentOptions.original)) {
						// do nothing;
			    	}
			    	else if (typeof(stateOptions) === 'object') {

						// loop state's options
						for(var parameter in stateOptions) {

							// skip fake states parameters
							if (parameter.indexOf("hover_")>=0) {
								continue;
							}

							// skip fake responsive parameters
							if (parameter.indexOf("responsive")>=0) {
								continue;
							}

							// skip fake responsive parameters
							if (parameter.indexOf("slider")>=0) {
								continue;
							}

							// skip fake icon parameters
							if (parameter.indexOf("icon")>=0) {
								continue;
							}

							// skip fake SoundCloud parameters
							if (parameter.indexOf("soundcloud")>=0) {
								continue;
							}
							
							if (stateOptions.hasOwnProperty(parameter)) {

								var value = stateOptions[parameter];

								if (parameter=="custom-css") {
									continue;
								}

								if (componentOptions.name == "ct_fancy_icon" &&
									(	parameter=="background" ||
										parameter=="icon-background-color" ||
										parameter=="icon-color" ||
										parameter=="padding" ||
										parameter=="icon-size"

									)) {
									continue;
								}

								// since 2.0
								if ( ["display","flex-direction","flex-wrap","align-items","align-content","justify-content"].indexOf(parameter) >= 0 
									 && componentOptions.name == "ct_section" ) { 
									continue; // this is already added for inner wrap
								}

								// load Web Fonts
								if (parameter == "font-family"||parameter.indexOf("font-family")>-1) {
									$scope.loadWebFont(value);
									if ( value.indexOf(',') === -1 && value.toLowerCase() !== "inherit") {
										value = "'"+value+"'";
									}
								}

								if (parameter.trim().toLowerCase() == "content") {
									//value = "\"" + $scope.addSlashes(value) + "\"";
									value = "\"" + value.replace('"','\\"') + "\"";
									$scope.contentAdded['#' + componentOptions.selector + paragraph + currentState] = true;
								}

								if (parameter=="flex-direction") {
									var reverse = (stateOptions['flex-reverse'] == 'reverse') ? "-reverse" : "";
									style += parameter + ":" + value + reverse + ";";
									continue;
								}

								// css filter property
								if ( parameter == "filter" && stateOptions["filter-amount-"+value] ) {
									value += "("+stateOptions["filter-amount-"+value]+")";
								}
								else if ( parameter == "filter" ) {
									continue;
								}

								// handle specific Nav Menu options that applies to menu items
								if (componentOptions.name == "oxy_nav_menu" && parameter.indexOf("menu_dropdowns_")===0) {
									if (parameter=="menu_dropdowns_background-color" && stateName!== "hover") {
										oxyNavMenuStyleDropdowns += parameter.replace("menu_dropdowns_","") + ":" + value + ";";
									}
									else if (value && $scope.excludeProperties.indexOf(parameter) < 0 && parameter) {
										oxyNavMenuStyleDropdownsItem += parameter.replace("menu_dropdowns_","") + ":" + value + ";";
									}
									// make hover padding the same to prevent jump
									if (parameter.indexOf("menu_dropdowns_padding") == 0) {
										oxyNavMenuStyleDropdownsItemHover += parameter.replace("menu_dropdowns_","") + ":" + value + ";";
									}
								}
								else if (componentOptions.name == "oxy_nav_menu" && parameter.indexOf("menu_active_")===0) {

									if (parameter == "menu_active_border-bottom-width" && componentOptions['original']['menu_padding-bottom'] !== undefined ) {
										// subtrac border from padding
										var newPadding = parseInt(componentOptions['original']['menu_padding-bottom']) - parseInt(stateOptions['menu_active_border-bottom-width']);
										newPadding = (newPadding > 0) ? newPadding : 0;
										oxyNavMenuStyleActive += "padding-bottom:" + newPadding + "px;";
									}

									if (parameter == "menu_active_border-top-width" && componentOptions['original']['menu_padding-top'] !== undefined ) {
										// subtrac border from padding
										var newPadding = parseInt(componentOptions['original']['menu_padding-top']) - parseInt(stateOptions['menu_active_border-top-width']);
										newPadding = (newPadding > 0) ? newPadding : 0;
										oxyNavMenuStyleActive += "padding-top:" + newPadding + "px;";
									}

									// finally just add the value as is
									if (value && $scope.excludeProperties.indexOf(parameter) < 0 && parameter) {
										oxyNavMenuStyleActive += parameter.replace("menu_active_","") + ":" + value + ";";
									}
								}
								else if (componentOptions.name == "oxy_nav_menu" && parameter.indexOf("menu_")===0) {

									if (parameter == "menu_flex-direction") {
										oxyNavMenuStyle += "flex-direction:" + value;
										continue;
									}

									if (parameter == "menu_justify-content"||parameter == "menu_dropdown_arrow") {
										continue;
									}

									if (parameter == "menu_border-bottom-width" && stateName == "hover" && componentOptions['original']['menu_padding-bottom'] !== undefined ) {
										// subtrac border from padding
										var newPadding = parseInt(componentOptions['original']['menu_padding-bottom']) - parseInt(stateOptions['menu_border-bottom-width']);
										newPadding = (newPadding > 0) ? newPadding : 0;
										if (componentOptions['original']['menu_flex-direction']=='row') {
											var paddingProp = "padding-bottom:";
										}
										else {
											var paddingProp = "padding-right:";
											parameter = "border-right-width"
										}
										oxyNavMenuStyleItem += paddingProp + newPadding + "px;";
									}

									if (parameter == "menu_border-top-width" && stateName == "hover" && componentOptions['original']['menu_padding-top'] !== undefined ) {
										// subtrac border from padding
										var newPadding = parseInt(componentOptions['original']['menu_padding-top']) - parseInt(stateOptions['menu_border-top-width']);
										newPadding = (newPadding > 0) ? newPadding : 0;
										if (componentOptions['original']['menu_flex-direction']=='row') {
											var paddingProp = "padding-top:";
										}
										else {
											var paddingProp = "padding-left:";
											parameter = "border-left-width"
										}
										oxyNavMenuStyleItem += paddingProp + newPadding + "px;";
									}
									
									if (parameter == "menu_-webkit-font-smoothing") {
										oxyNavMenuStyleItem += '-moz-osx-font-smoothing' + ":" + (value === 'antialiased' ? 'greyscale' : 'unset') + ";";
									}

									// finally just add the value as is
									if (value && $scope.excludeProperties.indexOf(parameter) < 0 && parameter) {
										oxyNavMenuStyleItem += parameter.replace("menu_","") + ":" + value + ";";
									}
								}
								
								else {

									if (value && $scope.excludeProperties.indexOf(parameter) < 0 && parameter !== "background-layers") {

										if(parameter === 'background-image') {
											// if this has an oxy shortcode, do something about it
											if(value.indexOf('[') > -1 && $scope.dynamicBackgrounds) {
												value = $scope.getDynamicBackground('id', {id: componentId, media: whichMedia, stateName: stateName})
											}
											
											value = 'url('+value+')';

											// trick for overlay color
								            if ( stateOptions['overlay-color'] ) {
								                value = 
								                    "linear-gradient(" + stateOptions['overlay-color'] + "," + stateOptions['overlay-color'] + "), " + value;
								            }
											
										}

										if ($scope.notCSSOptions[componentOptions.name]===undefined||$scope.notCSSOptions[componentOptions.name].indexOf(parameter) < 0){
											style += parameter + ":" + value + ";";
										}
										
									}	
									if (parameter == "-webkit-font-smoothing") {
										style += '-moz-osx-font-smoothing' + ":" + (value === 'antialiased' ? 'greyscale' : 'unset') + ";";
									}
								}
							}
						}	
					}
					
					if ((stateName=="before"||stateName=="after")&&!$scope.contentAdded['#' + componentOptions.selector + paragraph + currentState]) {
						style += "content:\"\";";
						$scope.contentAdded['#' + componentOptions.selector + paragraph + currentState] = true;
					}

					if (stateOptions["custom-css"]) {
						style += stateOptions["custom-css"];
					}

					style += '}';

					// add specific Nav Menu styles that applies to menu items 
					if (componentOptions.name == "oxy_nav_menu") {
						style  += oxyNavMenuStyle + '}' 
								+ oxyNavMenuStyleItem + '}' 
								+ oxyNavMenuStyleActive + '}'
								+ oxyNavMenuStyleDropdowns + '}'
								+ oxyNavMenuStyleNotOpenItem + '}'
								+ oxyNavMenuStyleDropdownsItem + '}'
								+ oxyNavMenuStyleDropdownsItemHover + '}';
					}

				}
			} // end if()
		} // end for()

		style += $scope.getMappedCSS(componentOptions.selector, componentOptions['original']);
		
		if (componentOptions['media']) {

			// make a copy to not modify options
			var componentMedia = angular.copy(componentOptions['media']),
				sortedMedia = $scope.sortedMediaList();

			for (var index in sortedMedia) {

				var mediaName = sortedMedia[index];

				if (componentMedia[mediaName]) {
					// add name and selector
					componentMedia[mediaName].name 		= componentOptions.name;
					componentMedia[mediaName].selector 	= componentOptions.selector;
					style += "@media (max-width: "+$scope.mediaList[mediaName]["maxSize"]+") {";
					style += $scope.getSingleComponentCSS(componentMedia[mediaName], componentId, true, mediaName);
					style += "}";
				}
			}
		}

		$scope.functionEnd("getSingleComponentCSS()");
		return style;
    }


	/**
     * Get all typography CSS from options using paramName as prefix
     *
     * @since 2.0
     * @author Ilya K.
     */

    $scope.generateTypographyCSS = function(options,paramName) {
		
		var styles = "";

		for (var key in options) {
			if (key.indexOf(paramName)>-1) {
				styles += key.replace(paramName+"_",'')+":"+options[key]+";";
			}
		}

		return styles;
    }


    /**
     * Get all CSS from options using paramName as prefix
     *
     * @since 2.0
     * @author Ilya K.
     */

    $scope.generateArrayOptionsCSS = function(options,paramName) {
		
		var styles = "";

		for (var key in options) {
			if (key.indexOf(paramName)>-1) {
				styles += key.replace(paramName+"_",'')+":"+options[key]+";";
			}
		}

		return styles;
    }


    /**
     * Get single class CSS string
     *
     * @since 0.2.5
     * @author Ilya K.
     * @return {sting} CSS code
     */

    $scope.getSingleClassCSS = function(className, classStates) {

    	if ($scope.log) {
    		console.log("getSingleClassCSS()", className);
    		$scope.functionStart("getSingleClassCSS()");
    	}

    	if (undefined === classStates) {
    		classStates = $scope.classes[className];
    	}

    	$scope.contentAdded = [];

    	// add default styles
    	var style = $scope.getSelectorStyles(className, classStates);

    	// add media styles
		if ( $scope.classes[className]['media'] ) {

			var sortedMedia = $scope.sortedMediaList();

			for (var index in sortedMedia) {

				var mediaName = sortedMedia[index];

				if ($scope.classes[className]['media'][mediaName]) {
					
					classStates = $scope.classes[className]['media'][mediaName];
					style += "@media (max-width: "+$scope.mediaList[mediaName]["maxSize"]+") {";
    				style += $scope.getSelectorStyles(className, classStates, false, mediaName);
    				style += "}";
				}
			}
    	}
		
		$scope.functionEnd("getSingleClassCSS()");
		return style;
    }
    

    /**
     * Get single custom selector CSS string
     *
     * @since 1.3
     * @author Ilya K.
     * @return {sting} CSS code
     */

    $scope.getSingleSelectorCSS = function(selectorName, selectorsStates) {

    	if ($scope.log) {
    		console.log("getSingleSelectorCSS()", selectorName);
    		$scope.functionStart("getSingleSelectorCSS()");
    	}

    	if (undefined === selectorsStates) {
    		selectorsStates = $scope.customSelectors[selectorName];
    	}

    	$scope.contentAdded = [];

    	// add default styles
    	var style = $scope.getSelectorStyles(selectorName, selectorsStates, true);

    	// add media styles
		if ( $scope.customSelectors[selectorName]['media'] ) {

			var sortedMedia = $scope.sortedMediaList();

			for (var index in sortedMedia) {

				var mediaName = sortedMedia[index];

				if ($scope.customSelectors[selectorName]['media'][mediaName]) {
					
					selectorsStates = $scope.customSelectors[selectorName]['media'][mediaName];
					style += "@media (max-width: "+$scope.mediaList[mediaName]["maxSize"]+") {";
    				style += $scope.getSelectorStyles(selectorName, selectorsStates, true, mediaName);
    				style += "}";
				}

			}
    	}
		
		$scope.functionEnd("getSingleSelectorCSS()");
		return style;
    }


    /**
     * Get styles for one single class or custom selector
     *
     * @since 1.3
     * @author Ilya K.
     */

    $scope.getSelectorStyles = function(name, states, isCustomSelectors, whichMedia) {

    	if (name==="" || (!isCustomSelectors && typeof($scope.classes[name]) === 'undefined') || (isCustomSelectors && typeof($scope.customSelectors[name]) === 'undefined')) {
    		//skip empty selectors
    		return "";
    	}
    		
		var style = "",
			currentState;

		// loop all class states
		for(var state in states) { 
			if (states.hasOwnProperty(state)) {
				var styles = states[state];

				// skip "media" and sets
				if ( state == "media" || state == "set_name" || state == "key" || state == "parent" || state == "status" || state == "friendly_name" ) {
					continue;
				}

				if ( state == "original" || 
						( name == $scope.selectorToEdit 	&& state == $scope.currentState ) ||
						( name == $scope.currentClass 		&& state == $scope.currentState )
					) 
				{	
					if ( state == "original" || state == "hover" || state == "active" || state == "focus" ) {
						currentState = "";
					}
					else {
						currentState = ":" + state;
					}
				}
				else {
					currentState = ":" + state;
				}

				// filter styles
				var options = $scope.getCSSOptions(null, null, styles);
				var contentAdded = false;

				/**
				 * Special logic to handle Fnacy Icon styles for classes
				 */

				if ((options['icon-style'] !== undefined || options['icon-color'] !== undefined || options['icon-background-color'] !== undefined || options['icon-padding'] !== undefined || options['icon-size'] !== undefined ) && !isCustomSelectors) {

					if (!whichMedia && state == "original") {
						var iconStyle 			= options['icon-style'] || $scope.defaultOptions['ct_fancy_icon']['icon-style'],
							iconColor 			= options['icon-color'] || $scope.defaultOptions['ct_fancy_icon']['icon-color'],
							iconBackgroundColor = options['icon-background-color'] || $scope.defaultOptions['ct_fancy_icon']['icon-background-color'],
							iconPadding 		= options['icon-padding'] || $scope.defaultOptions['ct_fancy_icon']['icon-padding']+$scope.defaultOptions['ct_fancy_icon']['icon-padding-unit'],
							iconSize 			= options['icon-size'] || $scope.defaultOptions['ct_fancy_icon']['icon-size']+$scope.defaultOptions['ct_fancy_icon']['icon-size-unit'];
					}
					else {
						var iconStyle 			= $scope.classes[name]['original']['icon-style'] || $scope.defaultOptions['ct_fancy_icon']['icon-style'],
							iconColor 			= options['icon-color'],
							iconBackgroundColor = options['icon-background-color'],
							iconPadding 		= options['icon-padding'],
							iconSize 			= options['icon-size'];
					}

					style += '.' + name + currentState + "{";

					if (iconStyle == 1) { // outline
						style += "border: 1px solid;";
					}

					if (iconStyle == 2) { // solid
						if (iconBackgroundColor) {
							style += "background-color: " + iconBackgroundColor + ";";
							style += "border: 1px solid " + iconBackgroundColor + ";";
						}
					}

					if (iconStyle == 1 || iconStyle == 2) { // outline or solid
					 	style += "padding:" + iconPadding + ";";
					} 

					if (iconColor) {
					 	style += "color:" + iconColor + ";";
					}

					style += "}";

					if (iconSize) {
						style += '.' + name + currentState + ">svg {";
					 	style += "width: " + iconSize + ";";
					 	style += "height: " + iconSize + ";";
					 	style += "}";
					}
				}

				
				/**
				 * Special logic to handle Button styles for classes
				 */

				if ((options['button-style'] !== undefined || options['button-color'] !== undefined || options['button-size'] !== undefined || options['button-text-color'] !== undefined) && !isCustomSelectors) {
						
						if (!whichMedia && state == "original") {
							var buttonStyle 	= options['button-style'] || $scope.defaultOptions['ct_link_button']['button-style'],
								buttonColor 	= options['button-color'] || $scope.defaultOptions['ct_link_button']['button-color'],
								buttonSize 		= options['button-size'] || $scope.defaultOptions['ct_link_button']['button-size'] + $scope.defaultOptions['ct_link_button']['button-size-unit'],
								buttonTextColor = options['button-text-color'] || $scope.defaultOptions['ct_link_button']['button-text-color'];
						}
						else {
							var buttonStyle 	= $scope.classes[name]['original']['button-style'] || $scope.defaultOptions['ct_link_button']['button-style'],
								buttonColor 	= options['button-color'],
								buttonSize 		= options['button-size'],
								buttonTextColor = options['button-text-color'];
						}
						style += '.' + name  + currentState + "{";

						if (buttonStyle == 1 && buttonColor) { // solid
							style += "background-color: " + buttonColor + ";";
							style += "border: 1px solid " + buttonColor + ";";
							if (buttonTextColor) {
								style += "color: " + buttonTextColor + ";";
							}
						}
						
						if (buttonStyle == 2) { // outline
							style += "background-color: transparent;";
							if (buttonColor) {
								style += "border: 1px solid " + buttonColor + ";";
								style += "color: " + buttonColor + ";";
							}
						}

						if (buttonSize) {
							var substracted = buttonStyle == 2 ? 1 : 0;
							style += "padding: " + (parseInt(buttonSize)-substracted) + 'px ' + (parseInt(buttonSize)*1.6-substracted) + 'px;';
						}

						style += "}";
				}


				/**
				 * Special logic to handle Testimonial styles for classes
				 */

				var isTestimonial = false;
				if ($scope.classes[name]) {
					for (paramName in $scope.classes[name]['original']) {
						if (paramName.indexOf("testimonial_")===0) {
							isTestimonial = true;
						}
					}
				}

				if ( !isCustomSelectors && isTestimonial ) {

						isTestimonial = false;

						var flexAlign,
				            textAlign,
				            mobileFlexAlign,
				            mobileTextAlign,
				            marginCSS = "",
				            spacing = options['testimonial_image_spacing'];

				        if (options["testimonial_layout"]=='vertical') {

				        	marginCSS = "margin:0;";
                			spacing = options['testimonial_image_spacing'] || $scope.defaultOptions['oxy_testimonial']['testimonial_image_spacing']+$scope.defaultOptions['oxy_testimonial']['testimonial_image_spacing-unit'];
							
							if (options["testimonial_image_position"]=='bottom') {
				            	marginCSS += "margin-top";
				            }
				            else {
				            	marginCSS += "margin-bottom";
				            }
				        	
				        } else {
				            
				            if (options["testimonial_image_position"]=='bottom') {

				            	marginCSS = "margin:0;";
                				spacing = options['testimonial_image_spacing'] || $scope.defaultOptions['oxy_testimonial']['testimonial_image_spacing']+$scope.defaultOptions['oxy_testimonial']['testimonial_image_spacing-unit'];

				            	marginCSS += "margin-left";
				            }
				            else {
				            	marginCSS += "margin-right";
				            }
				        }

				        marginCSS += ": " + spacing + ";";

						if (options["testimonial_content_alignment"]=='left') {
				            flexAlign = "flex-start";
				            textAlign = "left";
				        } else if (options["testimonial_content_alignment"]=='center') {
				            flexAlign = "center";
				            textAlign = "center";
				        } else if (options["testimonial_content_alignment"]=='right') {
				            flexAlign = "flex-end";
				            textAlign = "right";
				        }

				        if ($scope.classes[name]['original']["testimonial_mobile_content_alignment"]=='left') {
				            mobileFlexAlign = "flex-start";
				            mobileTextAlign = "left";
				        } else if ($scope.classes[name]['original']["testimonial_mobile_content_alignment"]=='center') {
				            mobileFlexAlign = "center";
				            mobileTextAlign = "center";
				        } else if ($scope.classes[name]['original']["testimonial_mobile_content_alignment"]=='right') {
				            mobileFlexAlign = "flex-end";
				            mobileTextAlign = "right";
				        }

				        if (options["testimonial_layout"]=='vertical') {
				        	style += '.' + name + currentState + ' {' +
				                'flex-direction: column;' +
				            '}';
				        }
				        else if (options["testimonial_layout"]=='horizontal') {
				            style += '.' + name + currentState + ' {' +
				                'flex-direction: row;' +
				                'align-items: center;' +
				           '}';
				        }
				        
				        if (options['testimonial_image_position'] == 'top' || 
				            options['testimonial_image_position'] == '' ) {
				            style += '.' + name + currentState + ' .oxy-testimonial-photo-wrap {' +
				                'order: 1;' +
				           '}';
				        } 
				        else if (options['testimonial_image_position'] == 'bottom') {
				            style += '.' + name + currentState + ' .oxy-testimonial-photo-wrap {' +
				                'order: 3;' +
				           '}';
				        } 

						style += '.' + name + currentState + " .oxy-testimonial-photo {"+
				            "width:"+ options['testimonial_image_size'] + ";" +
				            "height:"+ options['testimonial_image_size'] + ";" +
				            marginCSS +
				        "}";

						style += '.' + name + currentState + " .oxy-testimonial-photo-wrap,"+
								 '.' + name + currentState + " .oxy-testimonial-author-wrap,"+
								 '.' + name + currentState + " .oxy-testimonial-content-wrap {"+
				            "align-items:"+ flexAlign + ";" +
				            "text-align:"+ textAlign + ";" +
				        "}";

						style += '.' + name + currentState + " .oxy-testimonial-text {"+
				            "margin-bottom:"+ options['testimonial_text_space_below'] + ";" +
				            $scope.generateTypographyCSS(options,'testimonial_text_typography') +
				        "}";

				        style += '.' + name + currentState + " .oxy-testimonial-author {"+
				            "margin-bottom:" + options['testimonial_author_space_below'] + ";" +
				            $scope.generateTypographyCSS(options,'testimonial_author_typography') +
				        "}";

				        style += '.' + name + currentState + " .oxy-testimonial-author-info {"+
				            "margin-bottom:" + options['testimonial_author_info_space_below'] + ";" +
				            $scope.generateTypographyCSS(options,'testimonial_author_info_typography') +
				        "}";

				        var maxSize = $scope.getMediaMaxSize($scope.classes[name]['original']['testimonial_vertical_layout_below']);
				        if (maxSize) {
				        	var imageSpacing = options['testimonial_image_spacing'];
				        	if (!imageSpacing) {
				        		imageSpacing = $scope.defaultOptions['oxy_testimonial']['testimonial_image_spacing']+$scope.defaultOptions['oxy_testimonial']['testimonial_image_spacing-unit'];
				        	}
				        	var marginPosition;
				        	if (options["testimonial_image_position"]=='bottom') {
		                        marginPosition = "top";
		                    } 
		                    else {
		                        marginPosition = "bottom";
		                    }
					        style += '@media (max-width: '+ maxSize + '){' +
				                '.' + name + currentState + ' {' +
				                    'flex-direction: column !important;' +
				                '}' +  

				                '.' + name + currentState + ' .oxy-testimonial-photo {' +
				                    'margin: 0;'+
				                    'margin-'+marginPosition+':' + imageSpacing + ';' +
				                '}'+

				                '.' + name + currentState + ' .oxy-testimonial-photo-wrap, ' +
				                '.' + name + currentState + ' .oxy-testimonial-author-wrap, ' +
				                '.' + name + currentState + ' .oxy-testimonial-content-wrap {' +
				                    'align-items:' + mobileFlexAlign + ';' +
				                    'text-align:' + mobileTextAlign + ';' +
				                '}'+
				            '}';
				        } 
					}

				/**
				 * Special logic to handle Icon Box styles for classes
				 */

				var isIconBox = false;
				if ($scope.classes[name]) {
					for (paramName in $scope.classes[name]['original']) {
						if (paramName.indexOf("icon_box_")===0) {
							isIconBox = true;
						}
					}
				}

				if ( !isCustomSelectors && isIconBox ) {

					isIconBox = false;
						
						var icon_position_flex_direction,
							icon_vertical_alignment_align_self,
							iconmargincss,
							mobileflexalign,
							mobiletextalign;
						
						if (options['icon_box_icon_position'] == 'top') {
				            icon_position_flex_direction = 'column';
				        } else if (options['icon_box_icon_position'] == 'left') {
				            icon_position_flex_direction = 'row';
				        } else if (options['icon_box_icon_position'] == 'right') {
				            icon_position_flex_direction = 'row-reverse';
				        } else if (options['icon_box_icon_position'] == 'bottom') {
				            icon_position_flex_direction = 'column-reverse';
				        }

				        if (options["icon_box_icon_position"]=='left' || options["icon_box_icon_position"]=='right') {
				        	var left = options["icon_box_icon_space_before"] || $scope.defaultOptions['oxy_icon_box']["icon_box_icon_space_before"]+$scope.defaultOptions['oxy_icon_box']["icon_box_icon_space_before-unit"];
				        		right = options["icon_box_icon_space_after"] || $scope.defaultOptions['oxy_icon_box']["icon_box_icon_space_after"]+$scope.defaultOptions['oxy_icon_box']["icon_box_icon_space_after-unit"];
				            iconmargincss = "margin-left: "+left+";\n";
				            iconmargincss += "margin-right: "+right+";\n";
				            iconmargincss += "margin-bottom: 0; margin-top: 0;";
				        } else {
				            iconmargincss = "margin-top: "+options["icon_box_icon_space_before"]+";\n";
				            iconmargincss += "margin-bottom: "+options["icon_box_icon_space_after"]+";\n";
				        }

				        if (options["icon_box_icon_position"]=='left' || options["icon_box_icon_position"]=='right') {
				            icon_vertical_alignment_align_self = options['icon_box_icon_vertical_alignment'];
				        } else {
				            if (options["icon_box_content_alignment"]=='left') {
				                icon_vertical_alignment_align_self = "flex-start";
				            } else if (options["icon_box_content_alignment"]=='center') {
				                icon_vertical_alignment_align_self = "center";
				            } else if (options["icon_box_content_alignment"]=='right') {
				                icon_vertical_alignment_align_self = "flex-end";
				            }          
				        }

				        if (options["icon_box_mobile_content_alignment"]=='left') {
				            mobileflexalign = "flex-start";
				            mobiletextalign = "left";
				        } else if (options["icon_box_mobile_content_alignment"]=='center') {
				            mobileflexalign = "center";
				            mobiletextalign = "center";
				        } else if (options["icon_box_mobile_content_alignment"]=='right') {
				            mobileflexalign = "flex-end";
				            mobiletextalign = "right";
				        } else {
				            mobileflexalign = "flex-start";
				            mobiletextalign = "left";            
				        }

			            style += '.' + name + currentState + ' {'+
			                'text-align: '+ options['icon_box_content_alignment'] + ";" +
			                'flex-direction: '+ icon_position_flex_direction + ';'+
			            '}';

			            style += '.' + name + currentState + ' .oxy-icon-box-icon {'+
			                iconmargincss +
			                'align-self: ' + icon_vertical_alignment_align_self + ';' +
			            '}';

			            style += '.' + name + currentState + ' .oxy-icon-box-heading {'+
			                $scope.generateTypographyCSS(options,'icon_box_heading_typography')+
			                'margin-top: ' + options['icon_box_heading_space_above'] + ';' +
			                'margin-bottom: ' + options['icon_box_heading_space_below'] + ';' +
			            '}';

			            style += '.' + name + currentState + ' .oxy-icon-box-text {'+
			                $scope.generateTypographyCSS(options,'icon_box_text_typography')+
			                'margin-top: ' + options['icon_box_text_space_above'] + ';' +
			                'margin-bottom: ' + options['icon_box_text_space_below'] + ';' +
			                'align-self: ' + icon_vertical_alignment_align_self + ';' +
			            '}';

			            style += '.' + name + currentState + ' .oxy-icon-box-link {'+
			                'margin-top: ' + options['icon_box_link_space_above'] + ';' +
			                'margin-bottom: ' + options['icon_box_link_space_below'] + ';' +
			            '}';
						
						var maxSize = $scope.getMediaMaxSize($scope.classes[name]['original']['icon_box_vertical_layout_below']);
				        if (maxSize && !whichMedia) {
				            style += '@media (max-width: ' + maxSize + '){' +
				                '.' + name + currentState + '.oxy-icon-box {' +
				                    'flex-direction: column !important;' +
				                    'text-align: ' + mobiletextalign + ';' +
				                '}' +

				                '.' + name + currentState + ' .oxy-icon-box-icon {' +
				                    'margin-left: 0;' +
				                    'margin-right: 0;' +
				                    'margin-top: ' + options["icon_box_icon_space_before"] + ';' +
				                    'margin-bottom: ' + options["icon_box_icon_space_after"] + ';' +
				                '}' +

				                '.' + name + currentState + ' .oxy-icon-box-icon, .oxy-icon-box-text {' +
				                    'align-self: ' + mobileflexalign + ';' +
				                '}' +
				            '}';
				        }
			    }

				/**
				 * Special logic to handle Progress Bar styles for classes
				 */

				var isProgressBar = false;
				if ($scope.classes[name]) {
					for (paramName in $scope.classes[name]['original']) {
						if (paramName.indexOf("progress_bar_")===0) {
							isProgressBar = true;
						}
					}
				}

				if ( !isCustomSelectors && isProgressBar ) {

						isProgressBar = false;

			    		var stripecss = "",
							animation_css = [];
						
						if (options['progress_bar_stripes'] == 'true') {
				            stripecss = "background-image: linear-gradient(-45deg,rgba(255,255,255,.12) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.12) 50%,rgba(255,255,255,.12) 75%,transparent 75%,transparent);";
				        }
				        if (options['progress_bar_stripes'] == 'false') {
				            stripecss = 'background-image:none;';
				        }

				        if (options['progress_bar_animation_stripes'] == 'true') {
				        	var stripesDuration = options['progress_bar_animation_stripes_duration'] || 
				        						$scope.defaultOptions['oxy_progress_bar']['progress_bar_animation_stripes_duration'];
				            animation_css['stripes'] = "oxy_progress_bar_stripes "+stripesDuration+" linear infinite";
				        }
				        if (options['progress_bar_animation_stripes'] == 'false') {
				        	animation_css['stripes'] = "none 0s paused";
				        }

				        if (options['progress_bar_animate_width'] == 'true') {
				        	var widthDuration = options['progress_bar_animation_width_duration'] || 
				        				   		$scope.defaultOptions['oxy_progress_bar']['progress_bar_animation_width_duration'];
				            animation_css['width'] = "oxy_progress_bar_width "+widthDuration+" ease-out 1";
				        }
				        if (options['progress_bar_animate_width'] == 'false') {
				        	animation_css['width'] = "none 0s paused";
				        }

				        var bar_animation = [];
				        for(var key in animation_css) {
							bar_animation.push(animation_css[key]);
				        }

			            style += '.' + name + currentState + ' .oxy-progress-bar-background {' +
			                'background-color:' + options['progress_bar_background_color'] + ';' +
			                 stripecss +
			                'animation:' + animation_css['stripes'] + ';' +
			            '}';

			            style += '.' + name + currentState + ' .oxy-progress-bar-progress-wrap {' +
			                'width:' + options['progress_bar_progress'] + ';' +
			            '}';

			            style += '.' + name + currentState + ' .oxy-progress-bar-progress {' +
			                'background-color:' + options['progress_bar_bar_color'] + ';' +
			                'padding:' + options['progress_bar_bar_padding'] + ';' +
			                 stripecss +
			                'animation:' + bar_animation.join() + ';' +
			            '}';

			            style += '.' + name + currentState + ' .oxy-progress-bar-overlay-text {' +
			                $scope.generateTypographyCSS(options, 'progress_bar_left_text_typography') +
			            '}';

			            style += '.' + name + currentState + ' .oxy-progress-bar-overlay-percent {' +
			                $scope.generateTypographyCSS(options, 'progress_bar_right_text_typography') +
			            '}';
			    }

				/**
				 * Special logic to handle Progress Bar styles for classes
				 */

				var isPricingBox = false;
				if ($scope.classes[name]) {
					for (paramName in $scope.classes[name]['original']) {
						if (paramName.indexOf("pricing_box_")===0) {
							isPricingBox = true;
						}
					}
				}
			    if ( !isCustomSelectors && isPricingBox ) {

			    		isPricingBox = false;

						var price_flex_alignment, price_flex_direction, price_justify_content, global_justify_content, image_justify_content, cta_justify_content;
						
						if (options["pricing_box_price_layout"]=='vertical') {
				            price_flex_direction = "column";
				            if (options["pricing_box_price_alignment"]=='left') {
				                price_flex_alignment = "flex-start";
				            } else if (options["pricing_box_price_alignment"]=='center') {
				                price_flex_alignment = "center";
				            } else if (options["pricing_box_price_alignment"]=='right') {
				                price_flex_alignment = "flex-end";
				            } 
				        } else if (options["pricing_box_price_layout"]=='horizontal') {
				            price_flex_direction = "row";
				        }

				        if (options["pricing_box_price_layout"]=='horizontal') {
				            if (options["pricing_box_price_alignment"]=='left') {
				                price_justify_content = "flex-start";
				            } else if (options["pricing_box_price_alignment"]=='center') {
				                price_justify_content = "center";
				            } else if (options["pricing_box_price_alignment"]=='right') {
				                price_justify_content = "flex-end";
				            }
				        }

				        if (options["pricing_box_global_alignment"]=='left') {
				            global_justify_content = "flex-start";
				        } else if (options["pricing_box_global_alignment"]=='center') {
				            global_justify_content = "center";
				        } else if (options["pricing_box_global_alignment"]=='right') {
				            global_justify_content = "flex-end";
				        }

				        if (options["pricing_box_image_alignment"]=='left') {
				            image_justify_content = "flex-start";
				        } else if (options["pricing_box_image_alignment"]=='center') {
				            image_justify_content = "center";
				        } else if (options["pricing_box_image_alignment"]=='right') {
				            image_justify_content = "flex-end";
				        } else if (global_justify_content) {
				        	image_justify_content = global_justify_content;
				        }

				        if (options["pricing_box_cta_alignment"]=='left') {
				            cta_justify_content = "flex-start";
				        } else if (options["pricing_box_cta_alignment"]=='center') {
				            cta_justify_content = "center";
				        } else if (options["pricing_box_cta_alignment"]=='right') {
				            cta_justify_content = "flex-end";
				        } else if (global_justify_content) {
				        	cta_justify_content = global_justify_content;
				        }

			            style += '.' + name + currentState + ' .oxy-pricing-box-section {' +
			                $scope.generateArrayOptionsCSS(options, 'pricing_box_global') +
			                'text-align:' + options['pricing_box_global_alignment'] + ";" +
			            '}';

			            style += '.' + name + currentState + ' .oxy-pricing-box-section.oxy-pricing-box-price {' +
			                'justify-content:' + global_justify_content + ';' +
			            '}';


			            /* IMAGE */
			            style += '.' + name + currentState + ' .oxy-pricing-box-section.oxy-pricing-box-graphic {' +
			                $scope.generateArrayOptionsCSS(options, 'pricing_box_graphic') +
			                'background-color:' + options['pricing_box_graphic_background'] + ';' +
			                'text-align:'+ options['pricing_box_graphic_alignment'] + ';' +
			                'justify-content:' + image_justify_content + ';' +
			            '}';

			            /* TITLE */
			            style += '.' + name + currentState + ' .oxy-pricing-box-section.oxy-pricing-box-title {' +
			                $scope.generateArrayOptionsCSS(options, 'pricing_box_title') +
			                'text-align:'+ options['pricing_box_title_alignment'] + ';' +
			                'background-color:' + options['pricing_box_title_background'] + ';' +
			            '}';

			            style += '.' + name + currentState + ' .oxy-pricing-box-title-title {' +
			                $scope.generateTypographyCSS(options, 'pricing_box_title_typography') +
			            '}';

			            style += '.' + name + currentState + ' .oxy-pricing-box-title-subtitle {' +
			                $scope.generateTypographyCSS(options, 'pricing_box_subtitle_typography') +
			            '}';


			            /* PRICE */

			            style += '.' + name + currentState + ' .oxy-pricing-box-section.oxy-pricing-box-price {' +
			                $scope.generateArrayOptionsCSS(options, 'pricing_box_price') +
			                'text-align:' + options['pricing_box_price_alignment'] + ';' +
			                'background-color:' + options['pricing_box_price_background'] + ';' +
			                'flex-direction:' + price_flex_direction + ';' +
			                'justify-content:' + price_justify_content + ';' +
			                'align-items:' + price_flex_alignment + ';' +
			            '}';

			            style += '.' + name + currentState + ' .oxy-pricing-box-currency {' +
			                $scope.generateTypographyCSS(options, 'pricing_box_price_amount_currency_typography') +
			            '}';

			            style += '.' + name + currentState + ' .oxy-pricing-box-amount-main {' +
			                $scope.generateTypographyCSS(options, 'pricing_box_price_amount_main_typography') +
			            '}';

			            style += '.' + name + currentState + ' .oxy-pricing-box-amount-decimal {' +
			                $scope.generateTypographyCSS(options, 'pricing_box_price_amount_decimal_typography') +
			            '}';
			            
			            style += '.' + name + currentState + ' .oxy-pricing-box-term {' +
			                $scope.generateTypographyCSS(options, 'pricing_box_price_amount_term_typography') +
			            '}';

			            style += '.' + name + currentState + ' .oxy-pricing-box-sale-price {' +
			                $scope.generateTypographyCSS(options, 'pricing_box_price_sale_typography') +
			                'margin-bottom:' + options['pricing_box_price_sale_space_below'] + ';' +
			            '}';

			            /* CONTENT */
			            style += '.' + name + currentState + ' .oxy-pricing-box-section.oxy-pricing-box-content {' +
			                $scope.generateArrayOptionsCSS(options, 'pricing_box_content') +
			                'background-color:' + options['pricing_box_content_background'] + ';' +
			                'text-align:' + options['pricing_box_content_alignment'] + ';' +
			                $scope.generateTypographyCSS(options, 'pricing_box_content_typography') +
			            '}';

			            /* CTA */
			            style += '.' + name + currentState + ' .oxy-pricing-box-section.oxy-pricing-box-cta {' +
			                $scope.generateArrayOptionsCSS(options, 'pricing_box_cta') +
			                'background-color:' + options['pricing_box_cta_background'] + ';' +
			                'text-align:' + options['pricing_box_cta_alignment'] + ';' +
			                'justify-content:' + cta_justify_content + ';' +
			            '}';
				}

				var isToggle = false;
				if ($scope.classes[name]) {
					for (paramName in $scope.classes[name]['original']) {
						if (paramName.indexOf("toggle_")===0) {
							isToggle = true;
						}
					}
				}
				
				if ( !isCustomSelectors && isToggle ) {
						style += '.' + name + currentState + ' .oxy-expand-collapse-icon {' +
			                'font-size:' + options['toggle_icon_size'] + ';' +
			            '}';

			            style += '.' + name + currentState + ' .oxy-expand-collapse-icon::before,' +
			            		 '.' + name + currentState + ' .oxy-expand-collapse-icon::after {' +
			                'background-color:' + options['toggle_icon_color'] + ';' +
			            '}';
				}

				var isSuperBox = false;
				if ($scope.classes[name]) {
					for (paramName in $scope.classes[name]['original']) {
						if (paramName.indexOf("superbox_")===0) {
							isSuperBox = true;
						}
					}
				}
			    if ( !isCustomSelectors && isSuperBox ) {
						// scaling
				        var superbox_secondary_scale_start  = options['superbox_secondary_scale_start'],
				        	superbox_secondary_scale_finish = options['superbox_secondary_scale_finish'],
				        	superbox_primary_scale_start    = options['superbox_primary_scale_start'],
				        	superbox_primary_scale_finish   = options['superbox_primary_scale_finish'],
				        	css, 
				        	superbox_secondary_initial_css, superbox_secondary_hover_css,
				        	superbox_primary_initial_css, superbox_primary_hover_css,
				        	superbox_secondary_scale_start_transform_css,
							superbox_secondary_scale_finish_transform_css,
							superbox_primary_scale_start_transform_css,
							superbox_primary_scale_finish_transform_css,
							superbox_secondary_initial_css = "",
							superbox_secondary_hover_css = "",
							superbox_primary_initial_css = "",
							superbox_primary_hover_css = "";

				        if (superbox_secondary_scale_start !== undefined)     superbox_secondary_scale_start_transform_css = "transform: scale("+superbox_secondary_scale_start+");";
				        if (superbox_secondary_scale_finish !== undefined)    superbox_secondary_scale_finish_transform_css = "transform: scale("+superbox_secondary_scale_finish+");";
				        if (superbox_primary_scale_start !== undefined)       superbox_primary_scale_start_transform_css = "transform: scale("+superbox_primary_scale_start+");";
				        if (superbox_primary_scale_finish !== undefined)      superbox_primary_scale_finish_transform_css = "transform: scale("+superbox_primary_scale_finish+");";

				        // sliding
				        if (options['superbox_secondary_slide_direction']) {
				            css = $scope.slide_position(options['superbox_secondary_slide_direction'], (options['superbox_secondary_slide_distance']||'')+"px");

				            if (options['superbox_secondary_slide_inorout'] == "in") {
				                superbox_secondary_initial_css = css['out_css'];
				                superbox_secondary_hover_css = css['in_css'];
				            } else if (options['superbox_secondary_slide_inorout'] == "out") {
				                superbox_secondary_initial_css = css['in_css'];
				                superbox_secondary_hover_css = css['out_css'];            
				            }
				        }

				        if (options['superbox_primary_slide_direction']) {

				            css = $scope.slide_position(options['superbox_primary_slide_direction'], (options['superbox_primary_slide_distance']||'')+"px");

				            if (options['superbox_primary_slide_inorout'] == "in") {
				                superbox_primary_initial_css = css['out_css'];
				                superbox_primary_hover_css = css['in_css'];
				            } else if (options['superbox_primary_slide_inorout'] == "out") {
				                superbox_primary_initial_css = css['in_css'];
				                superbox_primary_hover_css = css['out_css'];            
				            }
				        }

				        var transitionDuration = options['superbox_transition_duration'];

				        style += '.' + name + " .oxy-superbox-secondary, " +
				         		 '.' + name + " .oxy-superbox-primary {" +
				             'transition-duration:' + transitionDuration + ';' +
				        '}';

				        style += '.' + name + " .oxy-superbox-secondary {" +
				            'opacity:' + options['superbox_secondary_opacity_start'] + ";" +
				            superbox_secondary_initial_css +
				            superbox_secondary_scale_start_transform_css +
				        '}';

				        style += '.' + name +":hover .oxy-superbox-secondary {" +
				            'opacity:' + options['superbox_secondary_opacity_finish'] + ";" +
				            'z-index: 2147483642;' +
				            superbox_secondary_hover_css +
				            superbox_secondary_scale_finish_transform_css +
				        '}';

				        style += '.' + name + " .oxy-superbox-primary {" +
				            'opacity:' + options['superbox_primary_opacity_start'] + ";" +
				            superbox_primary_initial_css +
				            superbox_primary_scale_start_transform_css +
				        '}';

				        style += '.' + name + ":hover .oxy-superbox-primary {" +
				            'opacity:' + options['superbox_primary_opacity_finish'] + ";" +
				            superbox_primary_hover_css +
				            superbox_primary_scale_finish_transform_css +
				        '}';
					}

				// handle box-shadow options
				if ( options['box-shadow-color'] ) {

					var inset 	= (options['box-shadow-inset']=='inset') 		? options['box-shadow-inset']+" " : "";
					var hor 	= (options['box-shadow-horizontal-offset']) 	? options['box-shadow-horizontal-offset']+"px " : "";
					var ver 	= (options['box-shadow-vertical-offset']) 		? options['box-shadow-vertical-offset']+"px " : "";
					var blur 	= (options['box-shadow-blur']) 					? options['box-shadow-blur']+"px " : "0px ";
					var spread  = (options['box-shadow-spread']) 				? options['box-shadow-spread']+"px " : "";
						
					options['box-shadow'] = inset+hor+ver+blur+spread+options['box-shadow-color'];
				}

				// handle text-shadow options
				if ( options['text-shadow-color'] ) {

					var hor 	= (options['text-shadow-horizontal-offset']) 	? options['text-shadow-horizontal-offset']+"px " : "";
					var ver 	= (options['text-shadow-vertical-offset']) 		? options['text-shadow-vertical-offset']+"px " : "";
					var blur 	= (options['text-shadow-blur']) 				? options['text-shadow-blur']+"px " : "0px ";
						
					options['text-shadow'] = hor+ver+blur+options['text-shadow-color'];
				}


				if (!isCustomSelectors) {
					style += '.' + name  + currentState;
				}
				else {
					if (name.indexOf(".")<0&&name.indexOf("#")<0&&name.indexOf("body")<0) {
						style += ".oxygen-body "+ name + currentState;
					}
					else {
						style += name + currentState;	
					}
				}

				style += "{";

				// loop all parameters
				for(var parameter in options) { 
					if (options.hasOwnProperty(parameter)) {

						if (parameter=="custom-css") {
							continue;
						}

						// since 2.0
						if ( ["display","flex-direction","flex-wrap","align-items","align-content","justify-content"].indexOf(parameter) >= 0 &&
							 !isCustomSelectors ) { 
							continue; // this will be added for inner wrap
						}
						
						var value = options[parameter];

						if (parameter=="flex-direction") {
							var reverse = (options['flex-reverse'] == 'reverse') ? "-reverse" : "";
							style += parameter + ":" + value + reverse + ";";
							continue;
						}

						// css filter property
						if ( parameter == "filter" && options["filter-amount-"+value] ) {
							value += "("+options["filter-amount-"+value]+")";
						}
						else if ( parameter == "filter" ) {
							continue;
						}
				
						if ( parameter.trim().toLowerCase() == "content" ) {
							//value = "\"" + $scope.addSlashes(value) + "\"";
							value = "\"" + value.replace('"','\\"') + "\"";
							$scope.contentAdded[name+currentState] = true;
						}

						// load Web Fonts
						if ( (parameter == "font-family"||parameter.indexOf("font-family")>-1) && value !== undefined) {
							$scope.loadWebFont(value);
							if ( value.indexOf(',') === -1 && value.toLowerCase() !== "inherit") {
								value = "'"+value+"'";
							}
						}

						if ( value && $scope.excludeProperties.indexOf(parameter) < 0 && parameter !== "background-layers") {

							if(parameter === 'background-image') {
								// if this has an oxy shortcode, do something about it
								if(value.indexOf('[') > -1 && $scope.dynamicBackgrounds) {
									value = $scope.getDynamicBackground(isCustomSelectors?'selector':'class', {id: name, media: whichMedia, stateName: state})
								}
								
								value = 'url('+value+')';

								// trick for overlay color
					            if ( options['overlay-color'] ) {
					                value = 
					                    "linear-gradient(" + options['overlay-color'] + "," + options['overlay-color'] + "), " + value;
					            }
							}

							style += parameter + ":" + value + ";";
						}	

						if (parameter == "-webkit-font-smoothing") {
							style += '-moz-osx-font-smoothing' + ":" + (value === 'antialiased' ? 'greyscale' : 'unset') + ";";
						}
						
					}
				}
				
				if ((state=="before"||state=="after")&&!$scope.contentAdded[name+currentState]) {
					style += "content:\"\";";
					$scope.contentAdded[name+currentState] = true;
				}

				if (options["custom-css"]) {
					style += options["custom-css"];
				}

				style += "}";

				// handle section container
				if ( options['container-padding-top'] 	 ||
					 options['container-padding-right']  ||
					 options['container-padding-bottom'] ||
					 options['container-padding-left']
				 ) {

					style += '.' + name + currentState + " .ct-section-inner-wrap {";
					
						// custom-padding
						if ( options['container-padding-top'] ) {
							style += "padding-top" 		+ ":" + options['container-padding-top'] + ";";
						}
						if ( options['container-padding-right'] ) {
							style += "padding-right"	+ ":" + options['container-padding-right'] + ";";
						}
						if ( options['container-padding-bottom'] ) {
							style += "padding-bottom" 	+ ":" + options['container-padding-bottom'] + ";";
						}
						if ( options['container-padding-left'] ) {
							style += "padding-left" 	+ ":" + options['container-padding-left'] + ";";
						}
					
					style += '}';
				}

				// handle section flex
				if ( ( options['display'] ||
					 options['flex-direction']  ||
					 options['flex-wrap'] ||
					 options['align-items'] ||
					 options['align-content'] ||
					 options['justify-content'] ) &&
					!isCustomSelectors
				 ) {

					style += '.' + name + ":not(.ct-section)" + currentState;
					if ( $scope.isPseudoElement(currentState) ) {
						style += ',.' + name + ".ct-section .ct-section-inner-wrap" + currentState;
					}
					else {
						style += ',.' + name + ".ct-section" + currentState + " .ct-section-inner-wrap";
					}

					style += "{";
					
						if ( options['display'] ) {
							style += "display:" + options['display'] + ";";
						}

						var reverse = (options['flex-reverse'] == 'reverse') ? "-reverse" : "";
						
						if ( options['flex-direction'] ) {
							style += "flex-direction:" + options['flex-direction'] + reverse + ";";
						}

						if ( options['justify-content'] ) {
							style += "justify-content:" + options['justify-content'] + ";";
						}

						if ( options['align-content'] ) {
							style += "align-content:" + options['align-content'] + ";";
						}

						if ( options['align-items'] ) {
							style += "align-items:" + options['align-items'] + ";";
						}

						if ( options['flex-wrap'] ) {
							style += "flex-wrap:" + options['flex-wrap'] + ";";
						}
					
					style += '}';
				}
			}
		}
		return style;
	}


    /**
     * Recursive function to generate CSS based on items tree
     *
     * @since 0.2.2
     * @author Ilya K.
     */

    $scope.generateTreeCSS = function(treeNode, css) {

    	// do nothing if post contet is not made in builder
		if( Object.prototype.toString.call(treeNode) !== '[object Array]' ) {
			return false;
		}

		if ($scope.log){
			console.log("generateTreeCSS()", treeNode, css);
		}

    	// loop all componets
    	angular.forEach(treeNode, function(component) {

    		// add name to options
    		component.options.name = component.name;

    		// set original options if not defined
    		if (undefined === component.options.original) {
    			//component.options.original = {};
    		}
    		
    		component.options.id = component.options.original;

    		// get CSS styles
    		css.styles += $scope.getSingleComponentCSS(component.options, component.id);

			// loop children if any
			if ( component.children ) {
				$scope.generateTreeCSS(component.children, css);
			}
    	});
	}


	/**
     * Apply component CSS
     *
     * @since 0.3.1
     * @author Ilya K.
     */

	$scope.applyComponentCSS = function(id, name) {

		if (undefined==id) {
            id = $scope.component.active.id;
        }

        if (undefined==name) {
            name = $scope.component.active.name;
        }

		// update Tree
		$scope.setOption(id, name, "custom-css", false, false);
	}
	
	
	/**
     * Apply Style Sheet CSS
     *
     * @since 0.3.4
     * @author Gagan Goraya
     */

	$scope.applyStyleSheet = function(stylesheet) {
 		
 		var styleSheetsOutput = "\n"+stylesheet['css']+"\n",
			styleSheetsContainer = document.getElementById("ct-style-sheet-"+stylesheet['name']);

		if(styleSheetsContainer === null) {
			var locations = document.getElementsByClassName("ct-css-location"),
				location = angular.element(locations[locations.length - 1]);

			styleSheetsContainer = angular.element('<style>').attr('id', 'ct-style-sheet-'+stylesheet['name']).addClass('ct-css-location')
			styleSheetsContainer.insertAfter(location);
		}
		else
			styleSheetsContainer = angular.element(styleSheetsContainer);
        
        styleSheetsContainer.empty();
        
        styleSheetsContainer.append(styleSheetsOutput);

        // check for brackets
        if ( bracket = $scope.bracketsFailed(styleSheetsOutput) ) {
        	if ( bracket == "(" || bracket == "{" ) {
        		var position = "closing";
        	}
        	else {
				var position = "opening";
        	}
        	var text = "Warning: there is no " + position + " bracket for \"" + bracket + "\" somewhere in your CSS. This may break page styling."
        	jQuery(".oxygen-code-error-container", $parentScope.oxygenUIElement).show().html(text);
        }
        else {
        	jQuery(".oxygen-code-error-container", $parentScope.oxygenUIElement).hide().html("");
        }

        $scope.unsavedChanges();
    }

	
	/**
     * Add style sheets if not exist
     *
     * @author Ilya K.
     * @since 0.4.0
     */

    $scope.addDesignSetStyleSheets = function(data) {

    	for(var key in data) { 
			if (data.hasOwnProperty(key)) {
				var stylesheet = data[key];
				
				// check for repeat
				if( typeof(stylesheet) === 'object' && typeof($scope.styleSheets[stylesheet["name"]]) === 'undefined' ) {
					$scope.styleSheets[stylesheet["name"]] = $scope.stripSlashes(stylesheet["content"]);
					$scope.applyStyleSheet(stylesheet["name"]);
				}
			}
		}
    }


	/**
     * Helper function to parse width into value and unit
     *
     * @return {object}
     * @author Ilya K.
     */

	$scope.getWidth = function(width) {

		if (!width) {
			return {
				value : "",
				unit : ""
			}
		}

		var value = parseInt(width, 10);
		var unit = '';

		if(value !== width) {
			if ( width.indexOf("%") > -1 ) {
				unit = "%";
			}

			else if ( width.indexOf("em") > -1 ) {
				unit = "em";
			}

			else if ( width.indexOf("rem") > -1 ) {
				unit = "rem";
			}
			else
				unit = "px";
		}

		return {
			value: value,
			unit: unit
		}
	}


	/**
	 * Output page settings CSS
	 *
	 * @since 0.4.0
	 * @author Ilya K.
	 */
	
	$scope.outputPageSettingsCSS = function() {

		var style = '.ct-section-inner-wrap, .oxy-header-container{';
		style += "max-width" + ":" + $scope.getPageWidth() + "px;";
		style += "}";

		// output to head
        $scope.outputCSSStyles("ct-page-settings-styles", style);
	}


	/**
	 * Update global settings default CSS
	 *
	 * @since 2.0
	 * @author Ilya K.
	 */
	
	$scope.updateGlobalSettingsCSS = function() {

		var css 		= "",
			textFont 	= $scope.getGlobalFont('Text'),
			displayFont = $scope.getGlobalFont('Display');

		// Body Text
		css += "body {";
		css += "font-family: "+textFont+";";
		css += "line-height: "+$scope.globalSettings["body_text"]["line-height"]+";";
		css += "font-size: "+$scope.globalSettings["body_text"]["font-size"]+$scope.globalSettings["body_text"]["font-size-unit"]+";";
		css += "font-weight: "+$scope.globalSettings["body_text"]["font-weight"]+";";
		css += "color: "+$scope.globalSettings["body_text"]["color"]+";";
		css += "}";

		css += ".oxy-nav-menu-hamburger-line {";
		css += "background-color: "+$scope.globalSettings["body_text"]["color"]+";";
		css += "}";

		// Headings
		css += "h1, h2, h3, h4, h5, h6 {";
        css += "font-family: "+displayFont+";";
        
        if ($scope.globalSettings.headings["H1"]["font-size"] !== "") { 
        	css += "font-size: "+$scope.globalSettings.headings["H1"]["font-size"]+$scope.globalSettings.headings["H1"]["font-size-unit"]+";";
        }
        if ($scope.globalSettings.headings["H1"]["font-weight"] !== "") { 
        	css += "font-weight: "+$scope.globalSettings.headings["H1"]["font-weight"]+";";
        }
        if ($scope.globalSettings.headings["H1"]["color"] !== "") { 
        	css += "color: "+$scope.globalSettings.headings["H1"]["color"]+";";
        }
        
        css += "}";

        var selector = "h2, h3, h4, h5, h6";

        for(var heading in $scope.globalSettings.headings) { 
			if ($scope.globalSettings.headings.hasOwnProperty(heading)) {

				var headingCss = "";
				
				if (heading=="H1") {
					continue;
				}
				
				if ($scope.globalSettings.headings[heading]["font-size"] !== "") {
					headingCss += "font-size: "+$scope.globalSettings.headings[heading]["font-size"]+$scope.globalSettings.headings[heading]["font-size-unit"]+";";
        		}
        		if ($scope.globalSettings.headings[heading]["font-weight"] !== "") {
        			headingCss += "font-weight: "+$scope.globalSettings.headings[heading]["font-weight"]+";";
       			}
       			if ($scope.globalSettings.headings[heading]["color"] !== "") {
       				headingCss += "color: "+$scope.globalSettings.headings[heading]["color"]+";";
				}
       			if ( headingCss !== "" ) {
					css += selector + "{";
					css += headingCss;
					css += "}";
				}

       			// update selector
       			selector = selector.replace(heading.toLowerCase()+", ", "");
			}
		}

		// Links
		var links = {
			all:"a",
			text_link:".ct-link-text",
			link_wrapper:".ct-link",
			button:".ct-link-button"
		}

		for(var key in links) { 
			if (links.hasOwnProperty(key)) {

				var linksCss = "",
					selector = links[key];
				
				$scope.globalSettings.links[key] = $scope.globalSettings.links[key] || {}
				if ($scope.globalSettings.links[key]["color"] !== "") {
					linksCss += "color: "+$scope.globalSettings.links[key]["color"]+";";
				}
				if ($scope.globalSettings.links[key]["font-weight"] !== "") {
					linksCss += "font-weight: "+$scope.globalSettings.links[key]["font-weight"]+";";
				}
				if ($scope.globalSettings.links[key]["text-decoration"]) {
					linksCss += "text-decoration: "+$scope.globalSettings.links[key]["text-decoration"] +";";
				}
				if ($scope.globalSettings.links[key]["border-radius"]) {
					linksCss += "border-radius: "+$scope.globalSettings.links[key]["border-radius"] + $scope.globalSettings.links[key]["border-radius-unit"] + ";";
				}

				if ( linksCss !== "" ) {
					css += selector + " {";
					css += linksCss;
					css += "}";
				}

				linksCss = "";
				if ($scope.globalSettings.links[key]["hover_color"] !== "") {
					linksCss += "color: "+$scope.globalSettings.links[key]["hover_color"]+";";
				}
				if ($scope.globalSettings.links[key]["hover_text-decoration"]) {
					linksCss += "text-decoration: "+$scope.globalSettings.links[key]["hover_text-decoration"] +";";
				}

				if ( linksCss !== "" ) {
					css += selector + ":hover {";
					css += linksCss;
					css += "}";
				}
				
			}
		}

		css += ".ct-section-inner-wrap {";
		css += "padding-top: "+$scope.globalSettings.sections["container-padding-top"] + $scope.globalSettings.sections["container-padding-top-unit"] + ";";
		css += "padding-left: "+$scope.globalSettings.sections["container-padding-left"] + $scope.globalSettings.sections["container-padding-left-unit"] + ";";
		css += "padding-right: "+$scope.globalSettings.sections["container-padding-right"] + $scope.globalSettings.sections["container-padding-right-unit"] + ";";
		css += "padding-bottom: "+$scope.globalSettings.sections["container-padding-bottom"] + $scope.globalSettings.sections["container-padding-bottom-unit"] + ";";
		css += "}";

		css += ".oxy-header-container {";
		css += "padding-left: "+$scope.globalSettings.sections["container-padding-left"] + $scope.globalSettings.sections["container-padding-left-unit"] + ";";
		css += "padding-right: "+$scope.globalSettings.sections["container-padding-right"] + $scope.globalSettings.sections["container-padding-right-unit"] + ";";
		css += "}";

		// output to head
        $scope.outputCSSStyles("oxygen-global-settings-styles", css);
	}


	/**
	 * Watch globalSettings changes and update global CSS styles
	 *
	 * @since 2.0
	 * @author Ilya K.
	 */
	
	$scope.$watch('globalSettings', function(newVal, oldVal){
		$scope.updateGlobalSettingsCSS();
	}, true);

	
	/**
	 * Watch globalSettings.sections changes and update components CSS styles
	 *
	 * @since 2.0
	 * @author Ilya K.
	 */

	$scope.$watch('globalSettings.sections', function(newVal, oldVal){
		$scope.pageSettingsUpdate();
		$scope.applyModelOptions();
	}, true);


	/**
	 * Check balance beetwen brackets
	 *
	 * @since 1.1.0
	 * @author Ilya K.
	 */

	$scope.bracketsFailed = function(str){

		var removeComments = function(str){
	    	var re_comment = /(\/[*][^*]*[*]\/)|(\/\/[^\n]*)/gm;
		    return (""+str).replace( re_comment, "" );
		};
		
		var getOnlyBrackets = function(str){
		    //var re = /[^()\[\]{}]/g;
		    var re = /[^(){}]/g;
		    return (""+str).replace(re, "");
		};
		
		var areBracketsInOrder = function(str){
		    str = ""+str;
		    var bracket = {
		            "}": "{",
		            ")": "("
		        },
		        openBrackets = [], 
		        isClean = true,
		        i = 0,
		        len = str.length;

		    for(; i<len; i++ ){
		        if( bracket[ str[i] ] ){
		            isClean = ( openBrackets.pop() === bracket[ str[i] ] );
		            if (!isClean) {
		            	openBrackets.push( str[i] );
		            }
		        }else{
		            openBrackets.push( str[i] );
		        }
		    }
		    return ( openBrackets.length ) ? openBrackets[0] : false;
		    //return isClean && !openBrackets.length;
		};
	    
	    str = removeComments(str);
	    str = getOnlyBrackets(str);
	    return areBracketsInOrder(str);
	};

	
	/**
	 * Map component options to CSS propperties and selectors
	 *
	 * @since 2.0
	 * @author Ilya K.
	 */
	
	$scope.mapCSSProperty = function(optionName, propertyName, selector) {

		if (!$scope.mappedSelectors[selector]) {
			$scope.mappedSelectors[selector]={};
		}
		
		if (!$scope.mappedSelectors[selector][propertyName]) {
			$scope.mappedSelectors[selector][propertyName]={};
		}
		
		$scope.mappedSelectors[selector][propertyName] = optionName;
	}


	/**
	 * Map component options to CSS propperties and selectors
	 *
	 * @since 2.0
	 * @author Ilya K.
	 */
	
	$scope.unmapCSSProperty = function(propertyName, selector) {
			
		$scope.mappedSelectors[selector][propertyName] = null;
		delete $scope.mappedSelectors[selector][propertyName];
	}


	/**
	 * Output CSS styles based on mapped properties
	 *
	 * @since 2.0
	 * @author Ilya K.
	 */

	$scope.getMappedCSS = function(componentSelector, options) {

		var style = "";

		for (var selector in $scope.mappedSelectors) {
						
			var selectorObj = $scope.mappedSelectors[selector];

			selector = selector.split(",").map(function(sel) {
			   return '#' + componentSelector+" "+sel;
			}).join(",");
			
			style += selector + "{";
			
			for (var property in selectorObj) {
				var unit = "";
				if (options[selectorObj[property]+'-unit']!==undefined) {
					unit = options[selectorObj[property]+'-unit'];
				}
				style += property + ":" + options[selectorObj[property]] + unit + ";";
			}

			style += "}";
		}

		return style;
	}


	$scope.position_css = function($position, $forcein) {

		var output;

		for (var $prop in $position) {

			var $val = $position[$prop];

            if ($forcein === true) {
                if ($val !== null) {
                    $val = '0';
                }
            }

            if ($val !== null) {
                output = $prop+":"+$val+";";
            }

        }

        return output;
    }

	$scope.slide_position = function($direction, $distanceoverride) {

        var $distance = '100%',
        	$position = [],
        	$return = [];

        if ($distanceoverride !== undefined && $distanceoverride !== "px") {
            $distance = $distanceoverride;
        }

        switch ($direction) {
            case 'left':
                $position['left'] = '-'+$distance;
                break;
            case 'right':
                $position['left'] = ''+$distance;
                break;
            case 'top':
                $position['top'] = '-'+$distance;
                break;
            case 'bottom':
                $position['top'] = ''+$distance;
                break;
        }

        $return['out_css'] = $scope.position_css($position); // css for positioning the slide out of the superbox
        $return['in_css'] = $scope.position_css($position, true); // css for positinoing the slide in the superbox

        return $return;
    }

	
	/**
	 * Specificity Calculator
	 *
	 * https://github.com/keeganstreet/specificity
	 */

	$scope.SPECIFICITY = (function() {
		var calculate,
			calculateSingle,
			compare;

		// Calculate the specificity for a selector by dividing it into simple selectors and counting them
		calculate = function(input) {
			var selectors,
				selector,
				i,
				len,
				results = [];

			// Separate input by commas
			selectors = input.split(',');

			for (i = 0, len = selectors.length; i < len; i += 1) {
				selector = selectors[i];
				if (selector.length > 0) {
					results.push(calculateSingle(selector));
				}
			}

			return results;
		};

		/**
		 * Calculates the specificity of CSS selectors
		 * http://www.w3.org/TR/css3-selectors/#specificity
		 *
		 * Returns an object with the following properties:
		 *  - selector: the input
		 *  - specificity: e.g. 0,1,0,0
		 *  - parts: array with details about each part of the selector that counts towards the specificity
		 *  - specificityArray: e.g. [0, 1, 0, 0]
		 */
		calculateSingle = function(input) {
			var selector = input,
				findMatch,
				typeCount = {
					'a': 0,
					'b': 0,
					'c': 0
				},
				parts = [],
				// The following regular expressions assume that selectors matching the preceding regular expressions have been removed
				attributeRegex = /(\[[^\]]+\])/g,
				idRegex = /(#[^\s\+>~\.\[:]+)/g,
				classRegex = /(\.[^\s\+>~\.\[:]+)/g,
				pseudoElementRegex = /(::[^\s\+>~\.\[:]+|:first-line|:first-letter|:before|:after)/gi,
				// A regex for pseudo classes with brackets - :nth-child(), :nth-last-child(), :nth-of-type(), :nth-last-type(), :lang()
				pseudoClassWithBracketsRegex = /(:[\w-]+\([^\)]*\))/gi,
				// A regex for other pseudo classes, which don't have brackets
				pseudoClassRegex = /(:[^\s\+>~\.\[:]+)/g,
				elementRegex = /([^\s\+>~\.\[:]+)/g;

			// Find matches for a regular expression in a string and push their details to parts
			// Type is "a" for IDs, "b" for classes, attributes and pseudo-classes and "c" for elements and pseudo-elements
			findMatch = function(regex, type) {
				var matches, i, len, match, index, length;
				if (regex.test(selector)) {
					matches = selector.match(regex);
					for (i = 0, len = matches.length; i < len; i += 1) {
						typeCount[type] += 1;
						match = matches[i];
						index = selector.indexOf(match);
						length = match.length;
						parts.push({
							selector: input.substr(index, length),
							type: type,
							index: index,
							length: length
						});
						// Replace this simple selector with whitespace so it won't be counted in further simple selectors
						selector = selector.replace(match, Array(length + 1).join(' '));
					}
				}
			};

			// Replace escaped characters with plain text, using the "A" character
			// https://www.w3.org/TR/CSS21/syndata.html#characters
			(function() {
				var replaceWithPlainText = function(regex) {
						var matches, i, len, match;
						if (regex.test(selector)) {
							matches = selector.match(regex);
							for (i = 0, len = matches.length; i < len; i += 1) {
								match = matches[i];
								selector = selector.replace(match, Array(match.length + 1).join('A'));
							}
						}
					},
					// Matches a backslash followed by six hexadecimal digits followed by an optional single whitespace character
					escapeHexadecimalRegex = /\\[0-9A-Fa-f]{6}\s?/g,
					// Matches a backslash followed by fewer than six hexadecimal digits followed by a mandatory single whitespace character
					escapeHexadecimalRegex2 = /\\[0-9A-Fa-f]{1,5}\s/g,
					// Matches a backslash followed by any character
					escapeSpecialCharacter = /\\./g;

				replaceWithPlainText(escapeHexadecimalRegex);
				replaceWithPlainText(escapeHexadecimalRegex2);
				replaceWithPlainText(escapeSpecialCharacter);
			}());

			// Remove the negation psuedo-class (:not) but leave its argument because specificity is calculated on its argument
			(function() {
				var regex = /:not\(([^\)]*)\)/g;
				if (regex.test(selector)) {
					selector = selector.replace(regex, '     $1 ');
				}
			}());

			// Remove anything after a left brace in case a user has pasted in a rule, not just a selector
			(function() {
				var regex = /{[^]*/gm,
					matches, i, len, match;
				if (regex.test(selector)) {
					matches = selector.match(regex);
					for (i = 0, len = matches.length; i < len; i += 1) {
						match = matches[i];
						selector = selector.replace(match, Array(match.length + 1).join(' '));
					}
				}
			}());

			// Add attribute selectors to parts collection (type b)
			findMatch(attributeRegex, 'b');

			// Add ID selectors to parts collection (type a)
			findMatch(idRegex, 'a');

			// Add class selectors to parts collection (type b)
			findMatch(classRegex, 'b');

			// Add pseudo-element selectors to parts collection (type c)
			findMatch(pseudoElementRegex, 'c');

			// Add pseudo-class selectors to parts collection (type b)
			findMatch(pseudoClassWithBracketsRegex, 'b');
			findMatch(pseudoClassRegex, 'b');

			// Remove universal selector and separator characters
			selector = selector.replace(/[\*\s\+>~]/g, ' ');

			// Remove any stray dots or hashes which aren't attached to words
			// These may be present if the user is live-editing this selector
			selector = selector.replace(/[#\.]/g, ' ');

			// The only things left should be element selectors (type c)
			findMatch(elementRegex, 'c');

			// Order the parts in the order they appear in the original selector
			// This is neater for external apps to deal with
			parts.sort(function(a, b) {
				return a.index - b.index;
			});

			return {
				selector: input,
				specificity: '0,' + typeCount.a.toString() + ',' + typeCount.b.toString() + ',' + typeCount.c.toString(),
				specificityArray: [0, typeCount.a, typeCount.b, typeCount.c],
				parts: parts
			};
		};

		/**
		 * Compares two CSS selectors for specificity
		 * Alternatively you can replace one of the CSS selectors with a specificity array
		 *
		 *  - it returns -1 if a has a lower specificity than b
		 *  - it returns 1 if a has a higher specificity than b
		 *  - it returns 0 if a has the same specificity than b
		 */
		compare = function(a, b) {
			var aSpecificity,
				bSpecificity,
				i;

			if (typeof a ==='string') {
				if (a.indexOf(',') !== -1) {
					throw 'Invalid CSS selector';
				} else {
					aSpecificity = calculateSingle(a)['specificityArray'];
				}
			} else if (Array.isArray(a)) {
				if (a.filter(function(e) { return (typeof e === 'number'); }).length !== 4) {
					throw 'Invalid specificity array';
				} else {
					aSpecificity = a;
				}
			} else {
				throw 'Invalid CSS selector or specificity array';
			}

			if (typeof b ==='string') {
				if (b.indexOf(',') !== -1) {
					throw 'Invalid CSS selector';
				} else {
					bSpecificity = calculateSingle(b)['specificityArray'];
				}
			} else if (Array.isArray(b)) {
				if (b.filter(function(e) { return (typeof e === 'number'); }).length !== 4) {
					throw 'Invalid specificity array';
				} else {
					bSpecificity = b;
				}
			} else {
				throw 'Invalid CSS selector or specificity array';
			}

			for (i = 0; i < 4; i += 1) {
				if (aSpecificity[i] < bSpecificity[i]) {
					return -1;
				} else if (aSpecificity[i] > bSpecificity[i]) {
					return 1;
				}
			}

			return 0;
		};

		return {
			calculate: calculate,
			compare: compare
		};
	}());

});