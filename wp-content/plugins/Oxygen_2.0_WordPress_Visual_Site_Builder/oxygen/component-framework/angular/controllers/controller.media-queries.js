/**
 * All Media Queries stuff
 * 
 */

CTFrontendBuilder.controller("ControllerMediaQueries", function($scope, $parentScope, $http, $timeout, $interval) {


	$scope.currentMedia = "default";

	/**
	 * Init Default Media Queries
	 *
	 * @since 2.0
	 * @author Ilya K.
	 */

	$scope.initMedia = function() {
		
		$scope.mediaList = {

			"default"			 : {
									maxSize : "100%",
									title : "All devices" },

			"page-width"		 : {
									maxSize : $scope.getPageWidth()+'px',
									title : "Page container ("+$scope.getPageWidth()+'px) and below' },
			
			"tablet" 			: {
									maxSize : '992px', 
									title : "Less than 992px" },

			"phone-landscape" 	: {
									maxSize : '768px', 
									title : "Less than 768px" },

			"phone-portrait"	: {
									maxSize : '480px', 
									title : "Less than 480px" },
		}

		$scope.mediaListAbove = {

			"default"			 : {
									minSize : "100%",
									title : "All devices" },

			"page-width"		 : {
									minSize : $scope.getPageWidth()+'px',
									title : "Above Page container ("+$scope.getPageWidth()+'px)' },
			
			"tablet" 			: {
									minSize : '992px', 
									title : "Above 992px" },

			"phone-landscape" 	: {
									minSize : '768px', 
									title : "Above 768px" },

			"phone-portrait"	: {
									minSize : '480px', 
									title : "Above 480px" },
		}
	}


	/**
	 * Set current Media Query to edit
	 * 
	 * @since 0.3.2
	 */
	
	$scope.setCurrentMedia = function(name, viewportUpdate) {

		if ( $scope.getCurrentMedia() == name ) {
			return;
		}

		//console.log("setCurrentMedia", name);
		
		$scope.currentMedia = name;

		// update viewport
		if (viewportUpdate === undefined || viewportUpdate ) {
			var size = $scope.getMediaPreviewSize(name);
			$parentScope.adjustViewport(size);

			if (name=="default") {
				$parentScope.hideViewportRuller();
			}
			else {
				$parentScope.showViewportRuller();
				// reset scale
                //window.parent.jQuery("#ct-artificial-viewport").css("transform", "scale(1)");
			}
		}
		$parentScope.adjustViewportContainer();

		// disable stuff
        $parentScope.disableContentEdit();
        $parentScope.disableSelectable();
        $parentScope.closeAllTabs(["advancedSettings","componentBrowser"]); // keep certain sections

		// apply options
		$scope.applyModelOptions();

        // update all components styles
        /*$scope.updateAllComponentsCacheStyles();
        $scope.classesCached = false;
        $scope.outputCSSOptions(-1, true);*/
        $parentScope.checkTabs();

        //close backgroundlayers when component is switched
        $scope.parentScope.activeForEditBgLayer = false;

        var timeout = $timeout(function() {
			$parentScope.scrollToComponent($scope.getActiveComponent().attr('id'));
			$interval.cancel(timeout);
		}, 0, false);
	}


	/**
	 * Get currently editing Media Query
	 * 
	 * @since 0.3.2
	 */

	$scope.getCurrentMedia = function() {

		return $scope.currentMedia;
	}


	/**
	 * Get Media Query Title by name
	 * 
	 * @since 0.3.2
	 */

	$scope.getMediaTitle = function(name,above) {

		if (!name)
			return "";

		if (name == 'never')
			return 'Never';

		if (name == 'always')
			return 'Always';

		if ($scope.mediaListAbove!==undefined && above === true) {
			return $scope.mediaListAbove[name].title;
		}
		else if ($scope.mediaList!==undefined) {
			return $scope.mediaList[name].title;
		}
		else 
			return "";
	}


	/**
	 * Get Media Query max size by name
	 * 
	 * @since 0.3.2
	 */

	$scope.getMediaMaxSize = function(name) {

		return (typeof($scope.mediaList[name])!=='undefined') ? $scope.mediaList[name].maxSize : false;
	}


	/**
	 * Get Media Query min size by name
	 * 
	 * @since 2.0
	 * @author Ilya K.
	 */

	$scope.getMediaMinSize = function(name) {

		return (typeof($scope.mediaList[name])!=='undefined') ? $scope.mediaListAbove[name].minSize : false;
	}


	/**
	 * Get Media Query Preview Size by name
	 * 
	 * @since 2.0
	 * @author Ilya K.
	 */

	$scope.getMediaPreviewSize = function(name) {

		if ( name == "default" ) 
			return "100%";

		var sortedMedia = $scope.sortedMediaList();
		
		for (var index in sortedMedia) {
			var mediaName = sortedMedia[index];
			
			if (mediaName==name) {

				var name = sortedMedia[parseInt(index)+1];

				if (name !== undefined) {
					previewSize = parseInt($scope.mediaList[name].maxSize)+1;
					return previewSize+"px";
				}
				else 
					return "360px";
			}
		}
	}

	/**
	 * Get currently editing Media Query Size
	 * 
	 * @since 0.3.2
	 */
	
	$scope.getMediaNameBySize = function(width) {

		var medias = [];

		for(var media in $scope.mediaList) { 
			if ($scope.mediaList.hasOwnProperty(media)) {

				//console.log(width + " < " + parseInt($scope.mediaList[media]['maxSize']));
				
				if ( width < parseInt($scope.mediaList[media]['maxSize']) ) {
					medias.push(media);
				}
			}
		}

		if ( medias.length > 0 ) {
			return medias[medias.length-1];
		}
		else {
			return "default";
		}
	}


	/**
	 * Get all medias that may apply to current i.e for max-size: 768px, also should apply max-size: 992px
	 * 
	 * @since 0.3.2
	 * @deprecated 2.0
	 */

	$scope.getAllMediaNames = function(name) {

		if (name=="default") {
			return [];
		}

		var medias = [];

		for(var media in $scope.mediaList) { 
			if ($scope.mediaList.hasOwnProperty(media)) {
				
				if ( parseInt($scope.mediaList[name]['maxSize']) < parseInt($scope.mediaList[media]['maxSize']) ) {
					medias.push(media);
				}
			}
		}
		
		medias.push(name);
		
		return medias;
	}


	/**
	 * Set current Media parameter value
	 * 
	 * @since 0.3.2
	 */
	
	$scope.setMediaParameter = function(id, parameter, value, state) {

		// create objects if not exist
		if (!$scope.component.options[id]['media']) {
			$scope.component.options[id]['media'] = {};
		}
		if (!$scope.component.options[id]['media'][$scope.currentMedia]) {
			$scope.component.options[id]['media'][$scope.currentMedia] = {};
		}
		if (!$scope.component.options[id]['media'][$scope.currentMedia][state]) {
			$scope.component.options[id]['media'][$scope.currentMedia][state] = {};
		}

		$scope.component.options[id]['media'][$scope.currentMedia][state][parameter] = value;
	}


	/**
	 * Check if currently editing component or class certain media has styles
	 * 
	 * @since 0.3.2
	 * @return {bool}
	 * @author Ilya K.
	 */

	$scope.isHasMedia = function(mediaName, id) {

		if (undefined == id) {
			id = $scope.component.active.id;
		}

		if ( $scope.isEditing("id") ) {
			if ($scope.component.active.name == "ct_reusable") {

				var viewId = $scope.component.options[id].original['view_id'];
				
				if ( !$scope.postsData[viewId] ) {
					return false;
				}
				
				var reusableTree = $scope.postsData[viewId]["post_tree"];
				
				if ($scope.findMedia(reusableTree, mediaName)) {
					return true;
				}
				else {
					return false;
				}
			}
			else {
				if ( $scope.component.options[id]['media'] && 
					 $scope.component.options[id]['media'][mediaName] && 
					 $scope.component.options[id]['media'][mediaName][$scope.currentState] ) {
					
					return true;
				}
				else {
					return false;
				}
			}
		}

		if ( $scope.isEditing("class") ) {
			
			if ( $scope.classes[$scope.currentClass] &&
				 $scope.classes[$scope.currentClass]['media'] && 
				 $scope.classes[$scope.currentClass]['media'][mediaName] && 
				 $scope.classes[$scope.currentClass]['media'][mediaName][$scope.currentState] ) {
				
				return true;
			}
			else {
				return false;
			}
		}

		if ( $scope.isEditing("custom-selector") && !$scope.isEditing("class") ) {
			
			if ( $scope.customSelectors[$scope.selectorToEdit] &&
				 $scope.customSelectors[$scope.selectorToEdit]['media'] && 
				 $scope.customSelectors[$scope.selectorToEdit]['media'][mediaName] && 
				 $scope.customSelectors[$scope.selectorToEdit]['media'][mediaName][$scope.currentState] ) {
				
				return true;
			}
			else {
				return false;
			}
		}		
	}


	/**
	 * Check if currently editing component or class has any media styles
	 * 
	 * @since 0.3.2
	 * @return {bool}
	 * @author Ilya K.
	 */

	$scope.isHasMedias = function() {

		for(var media in $scope.mediaList) { 
			if ($scope.mediaList.hasOwnProperty(media)) {

				if (media==="default") 
					continue;
			
				if ( $scope.isHasMedia(media) === true )
					return true;
			}
		}

		return false;
	}


	/**
	 * Remove media styles from component
	 * 
	 * @since 0.3.2
	 */

	$scope.removeComponentMedia = function(mediaName, id) {

		$scope.cancelDeleteUndo();

		if ( $scope.isEditing("id") ) {
			
			if (undefined == id) {
				id = $scope.component.active.id;
			}

			if ( $scope.component.options[id]['media'] && 
				 $scope.component.options[id]['media'][mediaName] && 
				 $scope.component.options[id]['media'][mediaName][$scope.currentState] ) {
				
				$scope.component.options[id]['media'][mediaName][$scope.currentState] = {};
				delete $scope.component.options[id]['media'][mediaName][$scope.currentState];

				$scope.outputCSSOptions(id);
				$scope.applyModelOptions();

				// update Tree
            	$scope.findComponentItem($scope.componentsTree.children, id, $scope.removeMediaFromTree, mediaName);
			}
		}

		if ( $scope.isEditing("class") ) {

			if ( $scope.classes[$scope.currentClass] &&
				 $scope.classes[$scope.currentClass]['media'] && 
				 $scope.classes[$scope.currentClass]['media'][mediaName] && 
				 $scope.classes[$scope.currentClass]['media'][mediaName][$scope.currentState] ) {

				$scope.classes[$scope.currentClass]['media'][mediaName][$scope.currentState] = {};
				delete $scope.classes[$scope.currentClass]['media'][mediaName][$scope.currentState];

				$scope.outputCSSOptions();
				$scope.applyModelOptions();
			}
		}

		$scope.unsavedChanges();

		$scope.adjustResizeBox();
	}


	/**
	 * Remove media styles from Components Tree
	 * 
	 * @since 1.0.1
	 * @author Ilya K.
	 */
	
	$scope.removeMediaFromTree = function(key, item, mediaName) {
		delete item.options.media[mediaName];
	}


	/**
	 * Place the page settings media in right position 
	 * 
	 * @since 2.0.
	 * @author Ilya K.
	 */

	$scope.sortedMediaList = function(above){

		if (above===true) {
			mediaList = $scope.mediaListAbove;
			size = "minSize"
		}
		else {
			mediaList = $scope.mediaList;
			size = "maxSize"
		}

		var medias = ["default"],
			pageWidthAdded = false;
		
		for(var media in mediaList) { 
			if (mediaList.hasOwnProperty(media)) {

				if ( media == "default" || media == "page-width" )
					continue;
				
				if ( (parseInt(mediaList[media][size]) >= parseInt(mediaList['page-width'][size])) || pageWidthAdded ) {
					medias.push(media);
				}
				else {
					medias.push('page-width');
					medias.push(media);
					pageWidthAdded = true;
				} 
			}
		}

		if (!pageWidthAdded) {
			medias.push('page-width');
		}

		return medias;
	}

})