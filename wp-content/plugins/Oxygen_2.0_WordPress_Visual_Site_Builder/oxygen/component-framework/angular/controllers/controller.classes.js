/**
 * All Classes staff here
 * 
 */


CTFrontendBuilder.controller("ControllerClasses", function($scope, $parentScope, $timeout) {

    $scope.currentClass         = false;
    $scope.activeSelectors      = {};
    $scope.componentsClasses    = [];
  	$scope.classes              = {};
    $scope.newcomponentclass = { name: ''};



    /**
     * Update component classes in Components Tree
     * 
     * @since 0.1.7
     * @author Ilya K.
     */

    $scope.updateTreeComponentClasses = function(key, item, removeClass) {

		// remove class
		if ( removeClass ) {
			index = item.options.classes.indexOf(removeClass);
			if ( index > -1 ) {
				// remove class
				item.options.classes.splice(index, 1);
			}
		}
		// or add it
		else {
			if ( !item.options.classes ) {
				item.options.classes = [];
			}
			item.options.classes.push($scope.currentClass);
		}
    }


    /**
     * Set Current Class and apply it's options
     * 
     * @since 0.1.7
     * @author Ilya K.
     */

    $scope.setCurrentClass = function(className, silent) {

        if ($scope.log) {
            console.log('setCurrentClass()', className);
        }
    	
        $scope.switchState('original');
        
		//$scope.disableContentEdit();

        $scope.currentClass = className;
        
        // also set the current class for the particular component in order use it when user comes back to this component
        if(className !== false) {
            $scope.activeSelectors = $scope.activeSelectors || {};
            $scope.activeSelectors[$scope.component.active.id] = className;
        }


        $scope.showClasses = false;

        if(silent)
            return;

        // apply options
        $scope.applyModelOptions();

        $scope.outputCSSOptions($scope.component.active.id);

        $parentScope.checkTabs();
   	}


    /**
     * Remove certian class from active component
     * 
     * @since 0.1.8
     * @author Ilya K.
     */

    $scope.removeComponentClass = function(className, id) {

        if ($scope.log) {
            console.log('removeComponentClass()', className, id);
        }

        $scope.switchEditToId();

        if (undefined === id) {
            id = $scope.component.active.id;
        }

        // check if component has any classes
        if ( $scope.componentsClasses[id] ) {
            // look for class we need
            key = $scope.componentsClasses[id].indexOf(className);
        }
        else {
            key = -1;
        }

        // if component already have this class applied
        if ( key > -1 ) {
            // remove this class
            $scope.componentsClasses[id].splice(key, 1);
            var remove = className;
        }

        // if this is the active selector being edited, remove it from activeselectors
        // because the component's active selector should default back to its ID
        if($scope.activeSelectors[id] === className) {
            $scope.activeSelectors[id] = false;
            $scope.switchEditToId();
        }

        // update component classes in tree
        $scope.findComponentItem($scope.componentsTree.children, id, $scope.updateTreeComponentClasses, remove);

        $scope.unsavedChanges();
    }


    /**
     * Add class to component
     * 
     * @since 0.1.8
     */

    $scope.tryAddClassToComponent = function(id) {

        $scope.cancelDeleteUndo();

        var className = $scope.newcomponentclass.name;

        if(typeof(className) === 'undefined' || className.trim() === '')
            className = prompt("Class name:");
        
        if (className != null) {

            var valid = $scope.validateClassName(className);

            if (!valid) {
                
                alert("Wrong selector name. Name must begin with an underscore (_), a hyphen (-), or a letter(a–z), followed by any number of hyphens, underscores or letters.");
                return false;
            };
            
            $scope.addClassToComponent(id, className);

            //clear the new component class name text field
            $scope.newcomponentclass.name = '';
        }
    }


    /**
     * Primary purpose is to listen for a return key input in the new class name textbox
     * 
     * @since 0.3.3
     * @author Gagan Goraya
     */

    $scope.processClassNameInput = function(e, id) {

        // create the className if it is a return key
        if(e.keyCode === 13) {
            $scope.tryAddClassToComponent(id);

            // hide the dropdown
            jQuery(".oxygen-select",$parentScope.oxygenUIElement).trigger("click");
        }

    }


    /**
     * When choose a selector is clicked for a new element
     * 
     * @since 0.3.3
     * @author Gagan Goraya
     */

    $scope.onSelectorDropdown = function() {
        // helps the new class name field gain focus
        $parentScope.ctSelectBoxFocus = true;
    }


    /**
     * Determines if a css or ID selector has been explicitly selected for editing for the current component
     * 
     * @since 0.3.3
     */

    $scope.isNotSelectedYet = function(id) {

        if($scope.activeSelectors && typeof($scope.activeSelectors[id]) !== 'undefined' )  {
            return false;
        }

        if($scope.justaddedcomponents && $scope.justaddedcomponents.indexOf(id) > -1) {
            return true;
        }

        return false;
    }

    /**
     * Add class to component
     * 
     * @since 0.1.8
     */

    $scope.addClassToComponent = function(id, className, setCurrent) {

        if ($scope.log) {
            console.log('addClassToComponent()', id, className);
        }

        if (undefined===setCurrent) {
            setCurrent = true;
        }
            
        // check if component has any classes
        if ( $scope.componentsClasses[id] ) {
            // look for the class we need
            key = $scope.componentsClasses[id].indexOf(className);
        }
        else {
            // create empty array for this component classes
            $scope.componentsClasses[id] = [];
            key = -1;
        }

        var newlyCreatedClass = false;

        // if newly created class
        if ( !$scope.classes[className] ) {    
            // create object
            $scope.classes[className] = {};
            $scope.classes[className]['original'] = {};
            newlyCreatedClass = true;
        }

        // if component already have this class applied
        if ( key > -1 ) {
            if (setCurrent) { 
                $scope.setCurrentClass(className);
            }
            return false;
        }
        else {
            // add this class to component
            $scope.componentsClasses[id].push(className);
            if (setCurrent) { 
                $scope.setCurrentClass(className);
            }
        }

        // columns default width unit check
        if (newlyCreatedClass && id==$scope.component.active.id && $parentScope.isActiveName('ct_div_block') && $parentScope.isActiveParentName('ct_new_columns')) {
            $scope.setOptionModel("width-unit", "%", id, "ct_div_block");
        }

        // update component classes in tree
        $scope.findComponentItem($scope.componentsTree.children, id, $scope.updateTreeComponentClasses);

        $scope.unsavedChanges();
    }
    

    /**
     * Add class to component not using builder UI
     * 
     * @since 2.0
     */

    $scope.addClassToComponentSafe = function(id, className) {
        
        // hack needed to properly update components class in components tree
        $scope.currentClass = className;
                    
        $scope.addClassToComponent(id, className, false)
                    
        // hack needed to properly update components class in components tree
        $scope.currentClass = false;
    }


	 /**
     * Check if component has particular class added
     * 
     * @since 0.1.7
     */

    $scope.isComponentHasClass = function(id, className) {

        // if has any classes
    	if ( $scope.componentsClasses[id] ) {
 			
            // look for this particular class
 			key = $scope.componentsClasses[id].indexOf(className);

 			if ( key > -1 ) {
 				return true;
 			}
 			else {
 				return false;
 			}
 		}
 		else {
 			return false;
 		}
    }


    /**
     * Ask user if he wants to delete a class from install
     * 
     * @since 0.2.5
     */

    $scope.tryDeleteClass = function(className) {

        $scope.cancelDeleteUndo();

    	var confirmed = confirm("Delete \""+className+"\" from install? (Changes will take effect on Save)");
		
		if (!confirmed) {
			return false;
		}

        $scope.deleteClass(className);
    }


    /**
     * Delete class and all references from install
     * 
     * @since 0.1.7
     */

    $scope.deleteClass = function(className) {

        if ($scope.log) {
            console.log("deleteClass()", className);
        }

        // delete from classes
        delete $scope.classes[className];

        $scope.selectorToEdit   = false;
        $scope.currentClass     = false;

        // delete from component classes
        angular.forEach($scope.componentsClasses, function(componentClasses, componentId) {

            var key = componentClasses.indexOf(className);

            if (key > -1) {
                // remove class
                componentClasses.splice(key, 1);

                // update component classes in Tree
                $scope.findComponentItem($scope.componentsTree.children, componentId, $scope.updateTreeComponentClasses, className);
            }
        });

        if ($scope.component.active.id == -1) {
            $scope.activateComponent(0,'root')
        }

        delete $scope.cache.classStyles[$scope.currentClass];
        $scope.outputCSSOptions();

        $scope.unsavedChanges();
    }


    /**
     * Return all component's classes concatenated into one string
     * 
     * @since 0.2.0
     */

    $scope.getComponentsClasses = function(id, componentName) {

        var classNames = "ct-component " + componentName; 

        if ( componentName != "ct_section" && componentName != "ct_columns" && componentName != "ct_column" ) {
            classNames += " " + componentName.replace(new RegExp("_", 'g'), "-");
        }

    	if ( $scope.componentsClasses[id] ) {

            var classesList = angular.copy($scope.componentsClasses[id]);
            
            // remove active class for components like Toggle or Tabs
            for(var key in classesList) { 
                if (classesList.hasOwnProperty(key)) {
                    var className = classesList[key];

                    if (className && className.indexOf("-tab-active") > 0 && className.indexOf("tabs") === 0) {
                        classesList[key] = null;
                    }

                    if (className && className.indexOf("-expanded") > 0 && className.indexOf("toggle-") === 0) {
                        classesList[key] = null;
                    }
                }
            }

    		classNames += " " + classesList.join(" ");
    	}

        if ( componentName == "ct_svg_icon") {
            classNames += " ct-" + $scope.component.options[id]['model']['icon-id'];
        }

        if ( componentName == "ct_link" || componentName == "ct_link_text" || componentName == "ct_link_button" ) {
            classNames += " ct-links";
        }

        if ( componentName == "ct_section" && $scope.component.options[id]['model']['video_background']) {
            classNames += " oxy-video-background";
        }

        if ( componentName == "oxy_header" && $scope.component.options[id]['model']['sticky_header']=="yes") {
            classNames += " oxy-sticky-header";
        }

        if ( componentName == "oxy_header_row" && $scope.component.options[id]['model']['hide_in_sticky']=="yes") {
            classNames += " oxygen-hide-in-sticky";
        }

        if ( componentName == "oxy_nav_menu" && $scope.component.options[id]['model']['dropdowns']=="on") {
            classNames += " oxy-nav-menu-dropdowns";
        }

        if ( componentName == "oxy_nav_menu" && $scope.component.options[id]['model']['menu_dropdown_arrow']=="on") {
            classNames += " oxy-nav-menu-dropdown-arrow";
        }

        if ( componentName == "oxy_nav_menu" && $scope.component.options[id]['model']['menu_responsive_dropdowns']=="on") {
            classNames += " oxy-nav-menu-responsive-dropdowns";
        }

        if ( componentName == "oxy_nav_menu" && $scope.component.options[id]['model']['menu_flex-direction']=="column") {
            classNames += " oxy-nav-menu-vertical";
        }

        if ( componentName == "oxy_gallery" ) {
            if ( $scope.component.options[id]['model']['layout']=="masonry") {
                classNames += " oxy-gallery-masonry";
            }
            else {
                classNames += " oxy-gallery-flex";
            }

            if ( $scope.component.options[id]['model']['gallery_captions']=="yes") {
                classNames += " oxy-gallery-captions";
            }
        }

        if ( $scope.component.options[id]['model']['dont_render']=="true" ) {
            classNames += " oxy-placeholder-element";
        }

        if ( componentName == "oxy_testimonial" ) {
            if ( $scope.component.options[id]['model']['testimonial_layout']=="vertical") {
                classNames += " oxy-testimonial-vertical";
            }
            else {
                classNames += " oxy-testimonial-horizontal";
            }
        }

        if ( componentName == "oxy_superbox" ) {
            if ( $scope.component.options[id]['model']['superbox_editing_mode']!="live") {
                classNames += " oxy-superbox-editing-"+$scope.component.options[id]['model']['superbox_editing_mode'];
            }
        }
        
        return classNames;
    }


    /**
     * Add new class
     * 
     * @since 0.2.0
     */

    $scope.addClass = function(className) {

		// check if this class already added
		if ( !$scope.classes[className]) {

			$scope.classes[className] = {};
            $scope.classes[className]['original'] = {};

            return true;
        } 
        else {
        	return false;
        }
    }


    /**
     * Validate a class name
     * 
     * @since 0.2.0
     */

    $scope.validateClassName = function(name) {
    	var re = /^[a-z_-][a-z\d_-]*$/i
	    //var re = /-?[_a-zA-Z]+[_a-zA-Z0-9-]*$/i;
	    return re.test(name);
	}

});