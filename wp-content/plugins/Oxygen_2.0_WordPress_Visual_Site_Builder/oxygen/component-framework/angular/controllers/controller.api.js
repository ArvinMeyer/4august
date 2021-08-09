/**
 * All API callbacks to handle server responses
 * 
 */

CTFrontendBuilder.controller("ControllerAPI", function($scope, $parentScope, $http, $timeout) {

	$scope.itemOptions = {};

	/**
	 * Show componentize dialog
	 * 
	 * @since 0.4.0
	 * @author Ilya K.
	 */
	
	$scope.showAddItemDialog = function(id, type, termId, termType, source, page, name, category, designSet) {
		
		$parentScope.showDialogWindow();
		
		var currentItem;

		if(typeof(category) !== 'undefined') {
			if(type === 'component') {
				var items = [];

				if(designSet) {
					items = $scope.experimental_components[designSet]['items'][category]['contents'];
				}
				else {
					items = $scope.libraryCategories[category].contents;
				}
				currentItem = _.findWhere(items, {id: id, name: name, page: page, source: source});
			}
			else if(type === 'page' || type === 'template') {
				var items = [];

				if(designSet) {
					if(type === 'template')
						items = $scope.experimental_components[designSet]['templates'];
					else
						items = $scope.experimental_components[designSet]['pages'];
				}
				else {
					items = $scope.libraryPages[category].contents;
				}

				currentItem = _.findWhere(items, {id: id, name: name, source: source});

			}
		}
		else if(typeof(name) !== 'undefined' && typeof(page) !== 'undefined') {
			currentItem = $scope.getSourceComponent(id, name, page);
		}
		else {
		 	currentItem = $scope.getAPIItem(id, type);
		}

		if(type === 'template')
			type = 'page';

		$scope.itemOptions = {
			id: 		 id,
			type: 		 type,
			termId: 	 termId,
			termType: 	 termType,
			currentItem: currentItem
		}

		if(typeof(source) !== 'undefined') {
			$scope.itemOptions['source'] = source;
		}

		if(typeof(page) !== 'undefined') {
			$scope.itemOptions['page'] = page;
		}
		
		if(typeof(designSet) !== 'undefined') {
			$scope.itemOptions['designSet'] = designSet;
		}
			

		$scope.addItem();
		return;

		// $parentScope.dialogForms['showAddItemDialogForm'] = true;

		// jQuery(document).on("keydown", $scope.switchComponent);

	}

	/**
	 * Loop components within one category/design set
	 * 
	 * @since 0.4.0
	 * @author Ilya K.
	 */

	$scope.switchComponent = function(event, direction) {

		if (direction==undefined) {
			// stop if not left or right arrows
			if (event.keyCode != 37 && event.keyCode != 39) {
				return;
			}
			if (event.keyCode == 37) {
				direction = "left";
			}
			if (event.keyCode == 39) {
				direction = "right";
			}
		}

		var currentTerm = $scope.getAPITerm($scope.itemOptions),
			currentKey 	= null;

		// get items list to switch between
		if ( $scope.itemOptions.termType == "design_sets" ) {
			var termItems = currentTerm["children"][$scope.itemOptions.type]["items"];
		}

		if ( $scope.itemOptions.termType == "components" ) {
			var termItems = currentTerm["items"];
		}

		if ( $scope.itemOptions.termType == "pages" ) {
			var termItems = currentTerm;
		}

		// get current item key (not the ID!)
		for (var key in termItems) {
			if (termItems[key].id == $scope.itemOptions.id) {
				currentKey = key;
			}
		}

		if (direction == "left"){
			currentKey--;
			if (currentKey < 0){
				currentKey = termItems.length - 1;
			}
		}

		if (direction == "right"){
			currentKey++;
			if (currentKey > termItems.length - 1){
				currentKey = 0;
			}
		}

		// update id
		var currentItemId = termItems[currentKey].id;

		// update scope
		$scope.itemOptions.id 			= currentItemId;
		$scope.itemOptions.currentItem 	= $scope.getAPIItem(currentItemId, $scope.itemOptions.type);

		if (event) {
			$scope.$apply();
		}
	}


	/**
	 * Insert in builder
	 *
	 * @since 0.4.0
	 * @author Ilya K.
	 */

	$scope.addItem = function(id, type, $event, source, page, designSet) {

		$scope.cancelDeleteUndo();

		if ( id == undefined ) {
			id = $scope.itemOptions.id
		}

		if ( type == undefined ) {
			type = $scope.itemOptions.type;
		}

		if ( source == undefined && $scope.itemOptions.source != undefined) {
			source = $scope.itemOptions.source;
		}

		if ( page == undefined && $scope.itemOptions.page != undefined) {
			page = $scope.itemOptions.page;
		}

		if ( designSet == undefined && $scope.itemOptions.designSet != undefined) {
			designSet = $scope.itemOptions.designSet;
		}

		if ( $event !== undefined ) {
			$event.stopPropagation();
		}

		$scope.itemOptions = {};

		switch (type) {

			case 'component' :
				if(typeof(source) !== 'undefined' && typeof(page) !== 'undefined') {
					$scope.getComponentFromSource(id, source, designSet, page, $scope.addComponentFromSource);
				}
				else {
					// get component from server
					$scope.makeAPICall("get_components", {
						"id": id
					}, $scope.addReusableChildren);
				}

			break;

			case 'page' :
				if(typeof(source) !== 'undefined') {
					$scope.getPageFromSource(id, source, designSet, $scope.addPageFromSource);
				}
				else {
					// get page from server
					$scope.makeAPICall("get_pages", {
						"id": id
					}, $scope.addReusableChildren);
				}

			break;
		}

		$parentScope.hideDialogWindow();
	}


	$scope.getSourceComponent = function(id, name, page) {

		var result = $scope.experimental_components[name]['items'].filter(function(item) {
			return item.id == id && item.page == page;
		});

		return result[0] ? result[0] : null;
	}

	/**
	 * Get item from scope.api_component or .api_pages by id
	 *
	 * @since 0.4.0
	 * @author Ilya K.
	 */
	
	$scope.getAPIItem = function(id, type, property) {

		if (type == "component") {
			var items = $scope.api_components
		}

		if (type == "page") {
			var items = $scope.api_pages
		}

		var result = items.filter(function(item) {
			return item.id == id;
		});

		if ( property !== undefined ) {
			return result ? result[0][property] : null;
		}
		else {
			return result ? result[0] : null;	
		}
	}


	/**
	 * Get term from scope.folders by id and type
	 *
	 * @since 0.4.0
	 * @author Ilya K.
	 */
	
	$scope.getAPITerm = function(options) {

		if (options.termType == "design_sets" || options.termType == "components") {
			var termItems = $scope.folders.library.children[options.termType]["children"]
		}

		if (options.termType == "pages") {
			return $scope.folders.library.children[options.termType]["items"]
		}

		// recursively find term in folders
		function searchFoldersTree(termItems, id) {
			var result = false;
			for (var key in termItems) {
				if (termItems[key].id == id) {
					return termItems[key];
				}
				if (termItems[key]["children"]) {
					result = searchFoldersTree(termItems[key]["children"], id)		
				}
			}
			return result;
		}
		result = searchFoldersTree(termItems, options.termId)

		return result;	
	}


	/**
	 * Show form to update component screenshot
	 *
	 * @since 0.4.0
	 * @author Ilya K.
	 */

	$scope.showUpdateScreenshot = function() {

		$parentScope.hideDialogWindow();
		$parentScope.showDialogWindow();
		$parentScope.dialogForms['showUploadAsset'] = true;
	}


	/**
	 * Update component screenshot
	 *
	 * @since 0.4.0
	 * @author Ilya K.
	 */

	$scope.updateScreenshot = function() {

		$scope.postAsset($scope.componentizeOptions.screenshot, function(){
			
			if ($scope.itemOptions.type == "component"){
				action = "update_component";
			}
			if ($scope.itemOptions.type == "page"){
				action = "update_page";
			}

			$scope.makeAPICall(action, {
				"id": $scope.itemOptions.id,
				"screenshot": $scope.componentizeOptions.assetId
			});

			$scope.componentizeOptions.id = null;
			$parentScope.hideDialogWindow();
		});
	}


	/**
	 * Show create design set dialog
	 *
	 * @since 0.4.0
	 * @author Ilya K.
	 */

	$scope.showCreateDesignSet = function() {

		$parentScope.hideDialogWindow();
		$parentScope.showDialogWindow();
		$parentScope.dialogForms['showAddDesignSet'] = true;
	}


	/**
	 * Send new design set data to server to create
	 *
	 * @since 0.4.0
	 * @author Ilya K.
	 */

	$scope.createDesignSet = function() {

		$scope.makeAPICall("create_design_set", {
			"name": $scope.componentizeOptions.setName,
			"status": $scope.componentizeOptions.status
		});

		$parentScope.hideDialogWindow();

	}


	/**
	 * Show style sheet dialog 
	 * 
	 * @since 0.4.0
	 * @author Ilya K.
	 */
	
	$scope.showStyleSheetDialog = function(name) {

		$scope.componentizeOptions.stylesheetName = name;
		
		$parentScope.showDialogWindow();
		$parentScope.dialogForms['stylesheet'] = true;
	}


	/**
	 * Post style sheet to the DB
	 * 
	 * @since 0.4.0
	 * @author Ilya K.
	 */
	
	$scope.postStyleSheet = function() {
		
		$scope.makeAPICall("post_style_sheet", {
			"name": $scope.componentizeOptions.stylesheetName,
			"content": $scope.styleSheets[$scope.componentizeOptions.stylesheetName],
			"design_set_id": $scope.componentizeOptions.designSetId
		});

		$parentScope.hideDialogWindow();
	}

})