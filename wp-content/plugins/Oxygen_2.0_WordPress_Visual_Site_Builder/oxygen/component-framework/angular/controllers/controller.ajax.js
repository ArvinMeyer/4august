/**
 * All AJAX requests
 * 
 */

CTFrontendBuilder.controller("ControllerAJAX", function($scope, $parentScope, $http, $timeout) {

    // cache for loaded posts data
    $scope.postsData = [];
    

    $scope.showErrorModal = function(status, message, err, url) {
        var errorMsg = "";

        if(status && status > 0) {
            errorMsg = (typeof(message)==='string'?"<h4>"+message+"</h4>":'')
            +"<p><strong>"+(typeof(err)==='string'?err:'')+" "+status+"</strong></p>"
            +"<p>Your server returned a "+status+" error for the request to "+url+".</p>"
            +"<p><a href='http://oxygenbuilder.com/documentation/troubleshooting/troubleshooting-guide/' style='text-decoration: underline; color: #fff;' target='_blank'>Troubleshooting Guide &raquo;</a></p>";
        }
        else {
            errorMsg = (typeof(message)==='string'?"<h4>"+message+"</h4>":'')
            +"<p><strong>"+(typeof(err)==='string'?err:'')+"</strong></p>";
        }

        $scope.showNoticeModal("<div>"+errorMsg+"</div>");
    }

    /**
     * Send Components Tree and page settings to WordPress 
     * in JSON format to save as post content and meta
     * 
     * @since 0.1
     */

    $scope.savePage = function(autoSave) {
        
        if (!autoSave) {
            $parentScope.showLoadingOverlay("savePage()");
        }

        $parentScope.disableContentEdit();

        var params = {
            // CSS classes
            classes : $scope.classes,
            
            // Custom Selectors
            custom_selectors : $scope.customSelectors,
            style_sets : $scope.styleSets,
            style_folders: $scope.styleFolders,

            // Style Sheets
            style_sheets : $scope.styleSheets,            

            // Settings
            page_settings : $scope.pageSettings,
            global_settings : $scope.globalSettings,

            // Easy Posts templates
            easy_posts_templates: $scope.easyPostsCustomTemplates,
            comments_list_templates: $scope.commentsListCustomTemplates,

            // Typekit fonts list
            typekit_fonts: $scope.typeKitFonts,

            // last preview URL
            preview: $scope.previewType == 'post' ? $scope.template.postData.permalink : $scope.template.postData.permalink

        };

        // store the activeSelectors state to each of the components in the tree
        angular.forEach($scope.activeSelectors, function(selector, id) {
            $scope.findComponentItem($scope.componentsTree.children, id, $scope.updateComponentActiveSelector, selector);
        });

        var data =  { 
            params: params,
            tree: $scope.componentsTree
        }

        // Convert Components Tree to JSON string
        var data = JSON.stringify(data);

        var params = {
            action : 'ct_save_components_tree',
            post_id : CtBuilderAjax.postId,
            nonce : CtBuilderAjax.nonce,
        };

        if(jQuery('body').hasClass('ct_inner') || jQuery('body', window.parent.document).hasClass('ct_inner')) {
            params['ct_inner'] = true;
        }

        // Send AJAX request
        $http({
            url : CtBuilderAjax.ajaxUrl,
            method : "POST",
            params : params,
            data : data,
            transformResponse: false,
        })
        .success(function(data, status, headers, config) {
            try {
                if (!autoSave) {
                    var response = JSON.parse(data);
                    //console.log(response);
                    if ( response === 0 ) {
                        $scope.showErrorModal(0, 'YOUR PAGE WAS NOT SAVED BECAUSE YOU ARE NOT LOGGED IN. Open a new browser tab and log back in to WordPress. Then attempt to save the page again.');
                    }
                    else
                    if ( response['post_saved'] == 0 ) {
                        console.log(data);
                        $scope.showErrorModal(0, 'Error occurred while saving');
                    }
                    else {
                        $scope.allSaved();
                        // update page CSS cache
                        // $scope.updatePageCSS();
                    }
                    $parentScope.hideLoadingOverlay("savePage()");
                }
                else {
                    var response = JSON.parse(data);
                    if ( response['post_saved'] != 0 ) {
                        $scope.allSaved();
                    }
                }
            } 
            catch (err) {
                console.log(data);
                console.log(err);
                if (!autoSave) {
                    $scope.showErrorModal(status, 'Error occurred while saving', err);
                }
            }
        })
        .error(function(data, status, headers, config) {
            console.log(data, status);
            if ( !autoSave ) {
                $parentScope.hideLoadingOverlay("savePage()");
            }
        }).then(null, function(response) { 
            $scope.showErrorModal(response.status, 'Error occurred while saving', response.statusText, response.config.url);
        });
    }


    /**
     * Update CSS cache
     * 
     * @since 1.1.1
     * @author Ilya K.
     */

    $scope.updatePageCSS = function() {

        $parentScope.showLoadingOverlay("updatePageCSS()");

        // Send AJAX request
        $http({
            url : CtBuilderAjax.permalink,
            method : "POST",
            params : {
                xlink : 'css',
                action : 'save-css',
            },
            transformResponse: false,
        })
        .success(function(data, status, headers, config) {
            //console.log(data, status);
            $parentScope.hideLoadingOverlay("updatePageCSS()");
        })
        .error(function(data, status, headers, config) {
            
            console.log(data, status);
            
            $parentScope.hideLoadingOverlay("updatePageCSS()");
        }).then(null, function(response) { 
            $scope.showErrorModal(response.status, 'Error occurred while saving CSS', response.statusText, response.config.url);
        });
    }


    /**
     * updates the active Selector into the provided item out of the component tree
     * 
     * @since 0.3.3
     * @author gagan goraya
     */   

    $scope.updateComponentActiveSelector = function(id, item, selector) {

        /**
         * Check if no item found becuase it may be a custom selector
         */

        if (!item) {
            return;
        }

        item.options['activeselector'] = selector;
    }


    /**
     * Send single component or Array of same level components 
     * to save as "ct_template" post via AJAX call
     * 
     * @since 0.2.3
     * @author Ilya K.
     */

    $scope.saveComponentAsView = function(key, component) {

        var params = {
                action : 'ct_save_component_as_view',
                name : $scope.componentizeOptions.name,
                post_id : CtBuilderAjax.postId,
                nonce : CtBuilderAjax.nonce,
            };

        // component(s) to save
        if ( component.constructor === Array ) {
            var children = component;
        }
        else {
            var children = [component];
        }

        // Send AJAX request
        $http({
            url: CtBuilderAjax.ajaxUrl,
            method: "POST",
            params: params,
            data: {
                    'id' : 0,
                    'name' : 'root',
                    'depth' : 0,
                    'children': children
                }
        })
        .success(function(data, status, headers, config) {
            //console.log(data);
            $parentScope.hideLoadingOverlay("saveComponentAsView()");

            if ( data != 0 ) {
                alert("Re-usable part \"" + $scope.componentizeOptions.name + "\" saved successfully.");
                $scope.replaceReusablePart(key, data);
            } 
            else {
                $scope.showErrorModal(0, 'Error occurred while saving \"Re-usable part\".');
            }
        })
        .error(function(data, status, headers, config) {
            console.log(data, status);
            $parentScope.hideLoadingOverlay("saveComponentAsView()");
        }).then(null, function(response) { 
            $scope.showErrorModal(response.status, 'Error occurred while saving \"Re-usable part\".', response.statusText, response.config.url);
        });
    }


    /**
     * Send single component or Array of same level components 
     * to server
     * 
     * @since 0.4.0
     * @author Ilya K.
     */

    $scope.postComponentize = function(key, component) {

        $parentScope.showLoadingOverlay("postComponentize()");
        
        var params = {
                action : 'ct_componentize',
                id_to_update : $scope.componentizeOptions.idToUpdate,
                name : $scope.componentizeOptions.name,
                design_set_id : $scope.componentizeOptions.designSetId,
                category_id : $scope.componentizeOptions.categoryId,
                screenshot : $scope.componentizeOptions.assetId,
                //status : $scope.componentizeOptions.status,
                post_id : CtBuilderAjax.postId,
                nonce : CtBuilderAjax.nonce,
            };
        
        var componentCopy = angular.copy(component);

        // add all class styles to the components tree
        $scope.addComponentClassesStyles(componentCopy);

        // component(s) to save
        if ( component.constructor === Array ) {
            var children = componentCopy;
        }
        else {
            var children = [componentCopy];
        }

        // Send AJAX request
        $http({
            url: CtBuilderAjax.ajaxUrl,
            method: "POST",
            params: params,
            transformResponse: false,
            data: {
                    'id' : 0,
                    'name' : 'root',
                    'depth' : 0,
                    'children': children,
                }
        })
        .success(function(data, status, headers, config) {
            //console.log(data);
            data = JSON.parse(data);
            if (data["status"] == "ok") {
                alert("Re-usable part \"" + $scope.componentizeOptions.name + "\" saved successfully.");
            } 
            else 
            if (data["status"] == "error") {
                $scope.showErrorModal(0, data["message"]);
            } else {
                $scope.showErrorModal(0, "Unknown error occurred while saving \"Re-usable part\".");
            }

            $parentScope.hideLoadingOverlay("postComponentize()");
        })
        .error(function(data, status, headers, config) {
            console.log(data, status);
            $parentScope.hideLoadingOverlay("postComponentize()");
        }).then(null, function(response) { 
            $scope.showErrorModal(response.status, "Error occurred while saving \"Re-usable part\".", response.statusText, response.config.url);
        });
    }


    /**
     * Send asset to the server
     * 
     * @since 0.4.0
     * @author Ilya K.
     */

    $scope.postAsset = function(file, callback) {

        if (file===undefined) {
            alert("No asset provided");
            return;
        }

        //console.log(file)
        $parentScope.showLoadingOverlay("postAsset()");
        
        var params = {
                action : 'ct_post_asset',
                file_name: file["name"],
                file_type: file["type"],
                post_id : CtBuilderAjax.postId,
                nonce : CtBuilderAjax.nonce,
            }
        
        // Send AJAX request
        $http({
            url: CtBuilderAjax.ajaxUrl,
            method: "POST",
            params: params,
            transformResponse: false,
            data: file
        })
        .success(function(data, status, headers, config) {
            //console.log(data);
            data = JSON.parse(data);
            if (data["status"] == "ok") {
                $scope.componentizeOptions.assetId = data["asset_id"];
                callback();
            } 
            else if (data["status"] == "error") {
                $scope.showErrorModal(0, "Error occurred while posting asset:"+data["message"]);
            } else {
                $scope.showErrorModal(0, "Unknown error occurred while posting asset.");
            }
            $parentScope.hideLoadingOverlay("postAsset()");
        })
        .error(function(data, status, headers, config) {
            console.log(data, status);
            $parentScope.hideLoadingOverlay("postAsset()");
        }).then(null, function(response) { 
            $scope.showErrorModal(response.status, "Error occurred while posting asset.", response.statusText, response.config.url);
        });
    }


    /**
     * Send root level component to be saved as re-usable page
     * 
     * @since 0.4.0
     * @author Ilya K.
     */

    $scope.pageComponentize = function() {

        $parentScope.showLoadingOverlay("pageComponentize()");

        var params = {
                action : 'ct_componentize_page',
                name : $scope.componentizeOptions.pageName,
                design_set_id : $scope.componentizeOptions.designSetId,
                //category_id : $scope.componentizeOptions.categoryId,
                screenshot : $scope.componentizeOptions.assetId,
                //status : $scope.componentizeOptions.status,
                post_id : CtBuilderAjax.postId,
                nonce : CtBuilderAjax.nonce,
            };
        
        var componentCopy = angular.copy($scope.componentsTree);

        // add all class styles to the components tree
        $scope.addComponentClassesStyles(componentCopy);

        // Send AJAX request
        $http({
            url: CtBuilderAjax.ajaxUrl,
            method: "POST",
            params: params,
            transformResponse: false,
            data: componentCopy
        })
        .success(function(data, status, headers, config) {
            //console.log(data);
            data = JSON.parse(data);
            if (data["status"] == "ok") {
                alert("Re-usable page \"" + params.name + "\" saved successfully.");
            } 
            else 
            if (data["status"] == "error") {
                $scope.showErrorModal(0, data["message"]);
            } else {
                $scope.showErrorModal(0, "Unknown error occurred while saving \"Re-usable page\".");
            }

            $parentScope.hideLoadingOverlay("pageComponentize()");
        })
        .error(function(data, status, headers, config) {
            console.log(data, status);
            $parentScope.hideLoadingOverlay("pageComponentize()");
        }).then(null, function(response) { 
            $scope.showErrorModal(response.status, "Error occurred while saving \"Re-usable page\".", response.statusText, response.config.url);
        });
    }



    /**
     * Get Components Tree JSON via AJAX
     * 
     * @since 0.1.7
     * @author Ilya K.
     */

    $scope.loadComponentsTree = function(callback, postId, hasSection, componentId) {

        if ($scope.log) {
            console.log("loadComponentsTree()", postId, hasSection, componentId);
        }
        
        $parentScope.showLoadingOverlay("loadComponentsTree()");

        // set default post id
        if ( postId === undefined ) {
            postId = CtBuilderAjax.postId;
        }

        var params = {
                action : 'ct_get_components_tree',
                id : postId,
                post_id : CtBuilderAjax.postId,
                nonce : CtBuilderAjax.nonce,
            };

        
        if(jQuery('body').hasClass('ct_inner')) {
            params['ct_inner'] = true;
        }
        

        // Send AJAX request
        $http({
            url : CtBuilderAjax.ajaxUrl,
            method : "POST",
            params : params,
            transformResponse: false,
        })
        .success(function(data, status, headers, config) {
            try {
                var response = JSON.parse(data);
                callback(response, postId, hasSection, componentId);
            } 
            catch (err) {
                console.log(data, err);
                $scope.showErrorModal(0, 'Error occurred while loading post: '+postId, err);
            }
            $parentScope.hideLoadingOverlay("loadComponentsTree()");
        })
        .error(function(data, status, headers, config) {
            console.log(data, status);
        }).then(null, function(response) { 
            $scope.showErrorModal(response.status, 'Error occurred while loading post: '+postId, response.statusText, response.config.url);
        });
    }


    $scope.renderInnerContent = function(id, componentName) {

        // if(typeof($scope.template.postData.ID) === 'undefined') {
        //     return;
        // }

        var url = CtBuilderAjax.permalink,
            data = {};
        
        // if archive
        if ($scope.previewType === 'term') {
            data.term = $scope.template.postData.term;
            
            // if the postData is empty
            if(!$scope.template.postData || !$scope.template.postData.permalink) {
                url = CtBuilderAjax.ajaxUrl;
            }
            else {
                url = $scope.template.postData.permalink;
            }
        }

        // if single
        else  {
            data.post = $scope.template.postData;

            // if the postData is empty
            if(!$scope.template.postData || !$scope.template.postData.permalink){
                url = CtBuilderAjax.ajaxUrl;
            }
            else {
                // lets make an ajax call directly to the frontend single
                url = data.post.permalink;
            }
        }

        var params = {
            action : 'ct_render_innercontent',
            post_id : CtBuilderAjax.postId,
            nonce : CtBuilderAjax.nonce,
        };

        // Send AJAX request
        $http({
            //url : CtBuilderAjax.ajaxUrl,
            url: url,
            method : "POST",
            params : params,
            transformResponse : false,
        })
        .success(function(data, status, headers, config) {
           if(parseInt(data) !== 0) {
               var component = $scope.getComponentById(id);
               component.html();
               var wrapper = angular.element('<div>');
               wrapper.html(data);
               component.append(wrapper);
               $scope.adjustResizeBox();
           }
           
        })
        .error(function(data, status, headers, config) {
            console.log(data, status);
        }).then(null, function(response) { 
            $scope.showErrorModal(response.status, 'Error occurred while rendering innercontent', response.statusText, response.config.url);
        });
    }


    /**
     * Get WordPress shortcodes generated HTML
     * 
     * @since 0.2.3
     */

    $scope.renderShortcode = function(id, shortcode, callback, shortcode_data) {

        // clear the elemnt HTML if "dont_render" param set
        if ($scope.component.options[id]['original']['dont_render']=='true') {
            var component = $scope.getComponentById(id);
            component.html("");
            return;
        }

        var url = CtBuilderAjax.permalink,
            data = {};

        // if archive
        if ($scope.previewType === 'term') {
            data.term = $scope.template.postData.term;
            
            // if the postData is empty
            if(!$scope.template.postData || !$scope.template.postData.permalink) {
                url = CtBuilderAjax.permalink;
            }
            else {
                url = $scope.template.postData.permalink;
            }
        }

        // if single
        else  {
            data.post = $scope.template.postData;

            // if the postData is empty
            if(!$scope.template.postData || !$scope.template.postData.permalink){
                url = CtBuilderAjax.permalink;
            }
            else {
                // lets make an ajax call directly to the frontend single
                url = data.post.permalink;
            }
        }

        var params = {
            action : 'ct_render_shortcode',
            shortcode_name : shortcode,
            post_id : CtBuilderAjax.postId,
            nonce : CtBuilderAjax.nonce,
        };

        if(callback) { // a mechanism to use cache of oxygen shortcodes
            $scope.oxygenShortcodesCache = $scope.oxygenShortcodesCache || [];
            if( shortcode_data && 
                shortcode_data['original'] && 
                shortcode_data['original']['full_shortcode'] &&
                shortcode_data['original']['full_shortcode'].indexOf('[oxygen') > -1) 
            {
                var existing = _.findWhere($scope.oxygenShortcodesCache, {id: params.post_id, url: url, full_shortcode: shortcode_data['original']['full_shortcode']})

                if(existing) {
                    callback(shortcode_data.original.full_shortcode, existing.result);
                    return;
                }

            }
        }

        // Send AJAX request
        $http({
            //url : CtBuilderAjax.ajaxUrl,
            url: url,
            method : "POST",
            params : params,
            data : shortcode_data?JSON.stringify(shortcode_data):JSON.stringify($scope.component.options[id]),
            transformResponse : false,
        })
        .success(function(data, status, headers, config) {
            if (data || data === "") { // shortcode can return blank and it is ok
                
                var component = $scope.getComponentById(id);
                var container = angular.element('<div>');
                container.html(data);

                if(callback) { // at the moment, this could only be a callback to render oxy shortcodes inline
                    // lets cache the results first of all
                    $scope.oxygenShortcodesCache = $scope.oxygenShortcodesCache || [];

                    var result = container.find('#ct-shortcode-links-scripts').html();

                    $scope.oxygenShortcodesCache.push({
                        id: params.post_id, 
                        url: url, 
                        full_shortcode: shortcode_data['original']['full_shortcode'],
                        result: result
                    });

                    callback(shortcode_data.original.full_shortcode, result);

                }
                else { // otherwise, its just a regular wordpress shortcode being taken care of

                    component.html(container.find('#ct-shortcode-links-scripts').html());

                    var body = component.closest('body');

                    // remove any existing links and scripts for the same shortcode component id
                    body.find('link[data-forId="'+id+'"], script[data-forId="'+id+'"]').remove();

                    // also append the links and scripts into the iframe body
                    container.find('link, script').each(function() {
                        body.append(angular.element(this).attr('data-forId', id));
                    })

                    // trigger (document).ready() so some shortcodes may init
                    var timeout = $timeout(function() {
                        jQuery.ready();
                        $timeout.cancel(timeout);
                    }, 1000, false);
                
                }
            }
            else {
                console.log(data, status);
                if(callback) { // at the moment, this could only be a callback to render oxy shortcodes inline
                    
                    callback(shortcode_data.original.full_shortcode, '');

                }
                $scope.showErrorModal(0 , 'Error occurred while rendering shortcode');
            }

            $scope.adjustResizeBox();
        })
        .error(function(data, status, headers, config) {
            console.log(data, status);
            $scope.adjustResizeBox();
        }).then(null, function(response) { 
            $scope.showErrorModal(response.status, 'Error occurred while rendering shortcode', response.statusText, response.config.url);
        });
    }


    /**
     * Remove warning msg for non-chrome browsers
     * 
     * @since 0.3.4
     * @author gagan goraya
     */

    $scope.removeChromeModal = function(e) {
        
        e.stopPropagation();
        e.preventDefault();
     
        
        if(!jQuery(e.target).hasClass('ct-chrome-modal-bg') && !jQuery(e.target).hasClass('ct-chrome-modal-hide'))
            return;
        
        var params = {
                action : 'ct_remove_chrome_modal',
                post_id : CtBuilderAjax.postId,
                nonce : CtBuilderAjax.nonce,
            };

        // Send AJAX request
        $http({
            url : CtBuilderAjax.ajaxUrl,
            method : "POST",
            params : params,
            transformResponse : false,
        })
        .success(function(data, status, headers, config) {
            jQuery('.ct-chrome-modal-bg').remove();
        })
        .error(function(data, status, headers, config) {
            console.log(data, status);
        }).then(null, function(response) { 
            $scope.showErrorModal(response.status, 'Error occurred while dismissing the notice', response.statusText, response.config.url);
        });
    }

	/**
	 * Get generated HTML from WordPress data
	 *
	 * @since 1.5
	 */

	$scope.renderDataComponent = function(id, component) {

		var url = CtBuilderAjax.permalink,
			data = {};

		// if archive
        if ($scope.previewType === 'term') {
            data.term = $scope.template.postData.term;
            
            // if the postData is empty
            if(!$scope.template.postData || !$scope.template.postData.permalink) {
                url = CtBuilderAjax.ajaxUrl;
            }
            else {
                url = $scope.template.postData.permalink;
            }
        }

        // if single
        else  {
            data.post = $scope.template.postData;

            // if the postData is empty
            if(!$scope.template.postData || !$scope.template.postData.permalink){
                url = CtBuilderAjax.ajaxUrl;
            }
            else {
                // lets make an ajax call directly to the frontend single
                url = data.post.permalink;
            }
        }

		var params = {
			action : 'ct_render_data_component',
			component_name : component,
			post_id : CtBuilderAjax.postId,
			nonce : CtBuilderAjax.nonce,
		};

		// Send AJAX request
		$http({
			//url : CtBuilderAjax.ajaxUrl,
			url: url,
			method : "POST",
			params : params,
			data : JSON.stringify($scope.component.options[id]),
			transformResponse : false,
		})
			.success(function(data, status, headers, config) {
				if (data || data === "") {
					var component = $scope.getComponentById(id);
				    switch( params.component_name ){
                        case "ct_data_featured_image":
                            data = JSON.parse( data );
							component[0].src = data.src;
                            break;
						case "ct_data_author_avatar":
							data = JSON.parse( data );
							component[0].src = data.src;
							break;
                        default:
							component.html(data);
                    }
				}
				else {
					console.log(data, status);
                    $scope.showErrorModal(0, 'Error occurred while rendering the component');
				}
			})
			.error(function(data, status, headers, config) {
				console.log(data, status);
			}).then(null, function(response) { 
                $scope.showErrorModal(response.status, 'Error occurred while rendering the component', response.statusText, response.config.url);
            });
	}

    /**
     * Get WordPress widget generated HTML
     * 
     * @since 0.2.3
     */

    $scope.renderWidget = function(id, isForm) {

        // clear the elemnt HTML if "dont_render" param set
        if ($scope.component.options[id]['original']['dont_render']=='true'&&!isForm) {
            var component = $scope.getComponentById(id);
            component.html("");
            return;
        }

        if ($scope.log) {
            console.log("renderWidget()",id,isForm);
        }

        // Convert Components Tree to JSON
        var data = JSON.stringify({"options" : $scope.component.options[id]}),
            url = CtBuilderAjax.ajaxUrl,
            params = {
                post_id : CtBuilderAjax.postId,
                nonce : CtBuilderAjax.nonce,
            };

        if (isForm) {
            params.action = 'ct_render_widget_form';
            $parentScope.cleanInsertUI("<span></span>", "#ct-dialog-widget-content");
            $parentScope.showSidebarLoader = true;
        }
        else {
            params.action = 'ct_render_widget';
            $parentScope.showWidgetOverlay(id);
            if($scope.template.postData && $scope.template.postData.permalink) {
                url = $scope.template.postData.permalink;
            }
            else {
                url = CtBuilderAjax.permalink;
            }
        }

        // Send AJAX request
        $http({
            url : url,
            method : "POST",
            params : params,
            data : data,
            transformResponse : false,
        })
        .success(function(data, status, headers, config) {
            var component = $scope.getComponentById(id);
            //console.log(data);
            if (data) {
                if (isForm) {
                    var timeout = $timeout(function() {
                        $parentScope.cleanInsertUI("<form id=\"ct-widget-form\" class=\"open\">"+data+"</form>", "#ct-dialog-widget-content");
                        // trigger the 'widget-added' action like in Customizer to support media widgets
                        window.parent.jQuery(window.parent.document).trigger('widget-added', [jQuery("#ct-widget-form",window.parent.document)] );
                        // cancel timeout
                        $timeout.cancel(timeout);
                    }, 0, false);
                    $parentScope.showSidebarLoader = false;
                } 
                else {
                    // fix for SiteOrigin Google maps widget
                    if ( window.google !== undefined && window.google.maps !== undefined && 
                        $scope.component.options[id]["id"]["id_base"] == "sow-google-map") {
                        delete window.google.maps;
                    }
                    component.html(data);
                    var timeout = $timeout(function() {
                        jQuery.ready();
                        $timeout.cancel(timeout);
                    }, 1000, false);
                    $parentScope.hideWidgetOverlay(id);
                    $scope.adjustResizeBox();
                }
            }
            
            if ((!data || component.text() === '')&&!isForm) {
                
                component.html("<div class='ct-blank-widget'>Widget Content</div>");
                //alert('Error occurred while rendering widget');
            }
        })
        .error(function(data, status, headers, config) {
            var component = $scope.getComponentById(id);
            component.html("<div class='ct-blank-widget'>Widget Content<div>");
            //console.log(data, status);
            //alert('Error occurred while rendering widget');
        });
    }


    /**
     * Get WordPress sidebar generated HTML
     * 
     * @since 2.0
     */

    $scope.renderSidebar = function(id, isForm) {

        // Convert Components Tree to JSON
        var data = JSON.stringify({"options" : $scope.component.options[id]}),
            params = {
                post_id : CtBuilderAjax.postId,
                nonce : CtBuilderAjax.nonce,
                action : 'ct_render_sidebar'
            };

        // Send AJAX request
        $http({
            url : CtBuilderAjax.ajaxUrl,
            method : "POST",
            params : params,
            data : data,
            transformResponse : false,
        })
        .success(function(data, status, headers, config) {
            var component = $scope.getComponentById(id);
            //console.log(data);
            if (data) {
                component.html(data);
            }
            
            if(!data || component.text() === '') {
                
                component.html("<div class='ct-blank-widget'>Sidebar Content</div>");
                //alert('Error occurred while rendering widget');
            }
        })
        .error(function(data, status, headers, config) {
            var component = $scope.getComponentById(id);
            component.html("<div class='ct-blank-widget'>Sidebar Content<div>");
            //console.log(data, status);
            //alert('Error occurred while rendering widget');
        });
    }


    /**
     * Get WordPress widget generated HTML
     * 
     * @since 2.0
     * @author Ilya K.
     */

    $scope.renderNavMenu = function(id) {

        if (undefined===id) {
            id = $scope.component.active.id;
        }

        if ($scope.log) {
            console.log("renderNavMenu()",id);
        }

        var url = CtBuilderAjax.permalink;

        if (CtBuilderAjax.oxyTemplate) {
            if($scope.template.postData && $scope.template.postData.permalink) {
                url = $scope.template.postData.permalink;
            }
            else {
                url = CtBuilderAjax.ajaxUrl;
            }
        }

        $parentScope.showWidgetOverlay(id);

        // Convert Components Tree to JSON
        var data = JSON.stringify({"options" : $scope.component.options[id]}),
            params = {
                post_id : CtBuilderAjax.postId,
                nonce : CtBuilderAjax.nonce,
                action : 'oxy_render_nav_menu'
            };

        // Send AJAX request
        $http({
            url : url,
            method : "POST",
            params : params,
            data : data,
            transformResponse : false,
        })
        .success(function(data, status, headers, config) {
            var component = $scope.getComponentById(id);
            //console.log(data);
            if (data) {
                component.html(data);
            } else {
                component.html('No menu found');
            }
            
            $scope.adjustResizeBox();
            $parentScope.hideWidgetOverlay(id);
        })
        .error(function(data, status, headers, config) {
            console.log(data, status);
        }).then(null, function(response) { 
            $scope.showErrorModal(response.status, 'Error occurred while rendering menu', response.statusText, response.config.url);
        });
    }


    /**
     * Get generated component HTML by AJAX
     * 
     * @since 2.0
     * @author Ilya K.
     */

    $scope.renderComponentWithAJAX = function(action, id) {

        if (undefined===id) {
            id = $scope.component.active.id;
        }

        if ($scope.log) {
            console.log("renderComponentWithAJAX()", action, id);
        }

        // render on the fronend page by default
        var url = CtBuilderAjax.permalink;

        if (CtBuilderAjax.oxyTemplate) {
            if($scope.template.postData && $scope.template.postData.permalink) {
                // render on the currently previewing page
                url = $scope.template.postData.permalink;
            }
            else {
                // render on admin-ajax.php if nothing to preview
                url = CtBuilderAjax.ajaxUrl;
            }
        }

        // Convert Components Tree to JSON
        var data = JSON.stringify({"options" : $scope.component.options[id]}),
            params = {
                post_id : CtBuilderAjax.postId,
                nonce : CtBuilderAjax.nonce,
                action : action
            };
    
        $parentScope.showWidgetOverlay(id);

        if (action=='oxy_render_easy_posts') {
            jQuery('.oxygen-easy-posts-ajax-styles').remove();
        }
       
        // Send AJAX request
        $http({
            url : url,
            method : "POST",
            params : params,
            data : data,
            transformResponse : false,
        })
        .success(function(data, status, headers, config) {
            var component = $scope.getComponentById(id);
            //console.log(data);
            if (data) {
                component.html(data);

                if (action=='oxy_render_easy_posts') {
                    component.find(".oxygen-easy-posts-ajax-styles").prependTo('head');
                }
            } else {
                component.html('No data received');
            }
            
            $scope.adjustResizeBox();
            $parentScope.hideWidgetOverlay(id);
        })
        .error(function(data, status, headers, config) {
            console.log(data, status);
        }).then(null, function(response) { 
            $scope.showErrorModal(response.status, 'Error occurred while rendering the component', response.statusText, response.config.url);
        });
    }


    /**
     * Get SVG Icon sets
     * 
     * @since 0.2.1
     */

    $scope.loadSVGIconSets = function() {

        var params = {
                action: 'ct_get_svg_icon_sets',
                post_id : CtBuilderAjax.postId,
                nonce : CtBuilderAjax.nonce,
            };

        // Send AJAX request
        $http({
            url : CtBuilderAjax.ajaxUrl,
            method : "POST",
            params : params,
            transformResponse : false,
        })
        .success(function(data, status, headers, config) {
            //console.log(data);
            try {
                var sets = JSON.parse(data);

                // update scope
                $scope.SVGSets = sets;   
                // set first set as current
                $scope.currentSVGSet = Object.keys(sets)[0]; 
            } 
            catch (err) {
                console.log(data);console.log(err);
                $scope.showErrorModal(0, 'Error occurred while loading SVG icons');
            }
        })
        .error(function(data, status, headers, config) {
            console.log(data, status);
        }).then(null, function(response) { 
            $scope.showErrorModal(response.status, 'Error occurred while loading SVG icons', response.statusText, response.config.url);
        });
    }


    /**
     * Load WP Post object (or array of post objects from one term) 
     * 
     * @since 0.2.0
     */

    $scope.loadTemplateData = function(callback, previewPostId) {
        
        $parentScope.showLoadingOverlay("loadTemplateData()");
        
        var params = {
                action : 'ct_get_template_data',
                template_id : CtBuilderAjax.postId,
                preview_post_id : previewPostId,
                preview_type : $scope.previewType,
                post_id : CtBuilderAjax.postId,
                nonce : CtBuilderAjax.nonce,
            };

        // Send AJAX request
        $http({
            url : CtBuilderAjax.ajaxUrl,
            method : "POST",
            params : params,
            transformResponse : false,
        })
        .success(function(data, status, headers, config) {
            //console.log(data);
            try {
                var response = JSON.parse(data);
                //console.log(response);
                callback(response);
            } 
            catch (err) {
                console.log(data);
                console.log(err);
                $scope.showErrorModal(0, 'Failed to load template data', err);
            }
            $parentScope.hideLoadingOverlay("loadTemplateData()");
        })
        .error(function(data, status, headers, config) {
            console.log(data, status);
            $parentScope.hideLoadingOverlay("loadTemplateData()");
        }).then(null, function(response) { 
            $scope.showErrorModal(response.status, 'Failed to load template data', response.statusText, response.config.url);
        });
    }


    /**
     * Load WP Post object
     * 
     * @since 0.2.3
     * @author Ilya K.
     */

    $scope.loadPostData = function(callback, postId, componentId) {

        // if data exists in the cache
        if($scope.postsData[postId]) {
            callback($scope.postsData[postId], componentId);
            return;
        }

        $parentScope.showLoadingOverlay("loadPostData()");
        
        var params = {
                action : 'ct_get_post_data',
                id : postId,
                post_id : CtBuilderAjax.postId,
                nonce : CtBuilderAjax.nonce,
            };

        // Send AJAX request
        $http({
            url : CtBuilderAjax.ajaxUrl,
            method : "POST",
            params : params,
            transformResponse : false,
        })
        .success(function(data, status, headers, config) {
            //console.log(data);
            try {
                var response = JSON.parse(data);
                callback(response, componentId);
                // save in cache
                $scope.postsData[postId] = response;
            } 
            catch (err) {
                console.log(data);console.log(err);
                $scope.showErrorModal(0, 'Failed to load post data. ID: '+postId, err);
            }
            $parentScope.hideLoadingOverlay("loadPostData()");
        })
        .error(function(data, status, headers, config) {
            console.log(data, status);
            $parentScope.hideLoadingOverlay("loadPostData()");
        }).then(null, function(response) { 
            $scope.showErrorModal(response.status, 'Failed to load post data. ID: '+postId, response.statusText, response.config.url);
        });
    }


    /**
     * Send PHP/HTML code block to server to execute
     * 
     * @since 0.3.1
     */

    $scope.execCode = function(code, placholderSelector, callback) {
        
        var url = CtBuilderAjax.permalink,
            data = {
                code: $scope.b64EncodeUnicode(code),
                query: CtBuilderAjax.query
            };

        // if archive
        if ($scope.previewType === 'term') {
            data.term = $scope.template.postData.term;
            
            // if the postData is empty
            if(!$scope.template.postData || !$scope.template.postData.permalink) {
                url = CtBuilderAjax.ajaxUrl;
            }
            else {
                url = $scope.template.postData.permalink;
            }
        }

        // if single
        else  {
            data.post = $scope.template.postData;

            // if the postData is empty
            if(!$scope.template.postData || !$scope.template.postData.permalink){
                url = CtBuilderAjax.ajaxUrl;
            }
            else {
                // lets make an ajax call directly to the frontend single
                url = data.post.permalink;
            }
        }

        // Convert Components Tree to JSON
        // escape special characters
        /*data.code = data.code.replace(/\n/g, "\\n")
                                      .replace(/\r/g, "\\r")
                                      .replace(/\t/g, "\\t");*/
        data = JSON.stringify(data);

        // Send AJAX request
        $http({
            method: "POST",
            transformResponse: false,
            url: url,
            params: {
                action: 'ct_exec_code',
                post_id: CtBuilderAjax.postId,
                nonce: CtBuilderAjax.nonce,
            },
            data: data,
        })
        .success(function(data, status, headers, config) {
            
            // this one ensures that blank means blank, not spaces
            if(data.trim().length === 0)
                data='';

            // if data is html document. use jquery to extract the content only
            if(data.indexOf('<html') > -1) {
                data = jQuery('<div>').append(data).find('.ct-code-block').html();
            }

            // get rid of any javascript rendered here.
            data = jQuery('<div>').append(data);
            data.find('script').remove();
            data = data.html();

            callback(data, placholderSelector);
        })
        .error(function(data, status, headers, config) {
            console.log(data, status);
        });
    }

     $scope.getStuffFromSource = function(callback, next) {
        
        if(typeof(next) === 'undefined') {
            setTimeout(function() {
                angular.element('.oxygen-sidebar-breadcrumb', window.parent.document).addClass('oxygen-small-progress');
            }, 100);
        }

        // Send AJAX request
        var params = {
            action: 'ct_new_style_api_call',
            call_type: 'get_stuff_from_source',
        };

        if(parseInt(next) === 0) {
            angular.element('.oxygen-sidebar-breadcrumb', window.parent.document).removeClass('oxygen-small-progress');
            return false;
        } else if(!(typeof(next) === 'undefined' || next === null)) {
            params['next'] = next;
        }
        

        $http({
            method: "GET",
            transformResponse: false,
            url: CtBuilderAjax.ajaxUrl,
            params: params
        })
        .success(function(data, status, headers, config) {
            callback(data);

        })
        .error(function(data, status, headers, config) {
            console.log(data, status);
        });
    }

    $scope.getComponentsListFromSource = function(id, name, callback) {
        //$parentScope.showLoadingOverlay("getComponentsListFromSource()");
        setTimeout(function() {
            angular.element('.oxygen-sidebar-breadcrumb', window.parent.document).addClass('oxygen-small-progress');    
        }, 200)
        
        // Send AJAX request
        $http({
            method: "GET",
            transformResponse: false,
            url: CtBuilderAjax.ajaxUrl,
            params: {
                action: 'ct_new_style_api_call',
                call_type: 'get_items_from_source',
                name: name
            }
        })
        .success(function(data, status, headers, config) {
            var isError = false;

            if(!data || data.trim() == '') {
                isError = true;
            }
            
            if(!isError)
                data = JSON.parse(data);

            if(isError || !data['components']) {
                $scope.showErrorModal(0, 'Items not loaded. Try again!');
                angular.element('.oxygen-sidebar-breadcrumb', window.parent.document).removeClass('oxygen-small-progress');
                return;
            }

            angular.element('.oxygen-sidebar-breadcrumb', window.parent.document).removeClass('oxygen-small-progress');
            delete($scope.experimental_components[name]['fresh']);

            $scope.experimental_components[name]['items'] = {};
            // $scope.parallelized_components = $scope.parallelized_components || {};

            // items need to be further classified based on the categories
            _.each(data['components'], function(item) {

                var category = item['category'];
                
                // if(typeof(category) === 'undefined') {
                //     category = 'Other'
                // }

                if(category) {
                    $scope.experimental_components[name]['items'][category] = $scope.experimental_components[name]['items'][category] || {};
                    $scope.experimental_components[name]['items'][category]['slug'] = btoa(category).replace(/=/g, '');
                    $scope.experimental_components[name]['items'][category]['contents'] = $scope.experimental_components[name]['items'][category]['contents'] || [];
                    $scope.experimental_components[name]['items'][category]['contents'].push(item);
                }

            });


            $scope.experimental_components[name]['pages'] = $scope.experimental_components[name]['pages'] || [];
            $scope.experimental_components[name]['templates'] = $scope.experimental_components[name]['templates'] || [];

            _.each(data['pages'], function(item) {
                
                var type = item['type'];

                if(type === 'ct_template') {
                   // $scope.parallelized_components['templates'] = $scope.parallelized_components['templates'] || [];
                    $scope.experimental_components[name]['templates'].push(item);

                }
                else {
                    //$scope.parallelized_components['pages'] = $scope.parallelized_components['pages'] || [];                    
                    $scope.experimental_components[name]['pages'].push(item);

                    // $scope.parallelized_components['pages'].push({
                    //     slug: name,
                    //     item: item
                    // })
                }

            });


            callback(id);
            //$parentScope.hideLoadingOverlay();
            angular.element('.oxygen-sidebar-breadcrumb', window.parent.document).removeClass('oxygen-small-progress');
        })
        .error(function(data, status, headers, config) {
            //angular.element('.oxygen-sidebar-breadcrumb', window.parent.document).removeClass('oxygen-small-progress');
            console.log(data, status);
            //$parentScope.hideLoadingOverlay();
            angular.element('.oxygen-sidebar-breadcrumb', window.parent.document).removeClass('oxygen-small-progress');
        });
    }

    $scope.getPageFromSource = function(id, source, designSet, callback) {
        $parentScope.showLoadingOverlay("getPageFromSource()");
        //angular.element('.oxygen-sidebar-breadcrumb', window.parent.document).addClass('oxygen-small-progress');
        // Send AJAX request
        $http({
            method: "GET",
            transformResponse: false,
            url: CtBuilderAjax.ajaxUrl,
            params: {
                action: 'ct_new_style_api_call',
                call_type: 'get_page_from_source',
                id: id,
                source: btoa(source)
            }
        })
        .success(function(data, status, headers, config) {
            
            //angular.element('.oxygen-sidebar-breadcrumb', window.parent.document).removeClass('oxygen-small-progress');
            callback(data, source, designSet);
            $parentScope.hideLoadingOverlay();
        })
        .error(function(data, status, headers, config) {
            $parentScope.hideLoadingOverlay();
            //angular.element('.oxygen-sidebar-breadcrumb', window.parent.document).removeClass('oxygen-small-progress');
            console.log(data, status);
        });
    }

    $scope.getComponentFromSource = function(id, source, designSet, page, callback) {
        $parentScope.showLoadingOverlay("getComponentFromSource()");
        //angular.element('.oxygen-sidebar-breadcrumb', window.parent.document).addClass('oxygen-small-progress');
        // Send AJAX request
        $http({
            method: "GET",
            transformResponse: false,
            url: CtBuilderAjax.ajaxUrl,
            params: {
                action: 'ct_new_style_api_call',
                call_type: 'get_component_from_source',
                id: id, 
                page: page,
                source: btoa(source)
            }
        })
        .success(function(data, status, headers, config) {
            $parentScope.hideLoadingOverlay();
            //angular.element('.oxygen-sidebar-breadcrumb', window.parent.document).removeClass('oxygen-small-progress');
            callback(data, false, source, designSet);
        })
        .error(function(data, status, headers, config) {
            $parentScope.hideLoadingOverlay();
            //angular.element('.oxygen-sidebar-breadcrumb', window.parent.document).removeClass('oxygen-small-progress');
            console.log(data, status);
        });


    }

	/**
	 * Make API call
	 * 
	 * @since 0.4.0
     * @author Ilya K. 
	 */

    $scope.makeAPICall = function( api_action, data, callback ) {

        $parentScope.showLoadingOverlay("makeAPICall()");
        
		var url = CtBuilderAjax.ajaxUrl,
			params = {
                action: "ct_api_callback",
                api_action: api_action,
                post_id: CtBuilderAjax.postId,
                nonce: CtBuilderAjax.nonce,
			};

		// Send AJAX request
		$http({
			method: "POST",
			url: url,
			data: data,
			params: params
		})
		.success(function(data, status, headers, config) {
			//console.log(data);
            if (data["status"]=="ok") {
                if (callback !== undefined) {
                    callback(data);
                }
                if (data["message"] !== undefined) {
                    alert(data["message"]);
                }
            }
            else if (data["status"]=="error") {
                console.log(data);
                if (data["message"] !== undefined) {
                    $scope.showErrorModal(0, data["message"]);
                }
            }
            $parentScope.hideLoadingOverlay();
		})
		.error(function(data, status, headers, config) {
			console.log(data, status);
            $parentScope.hideLoadingOverlay();
		});
    }


    /**
     * Scrape SoundCloud page with wp_remote_get()
     * 
     * @since 2.0
     * @author Ilya K. 
     */

    $scope.getSoundCloudTrackID = function(soundcloudURL) {

        $parentScope.showLoadingOverlay("getSoundCloudTrackID()");
        
        var url = CtBuilderAjax.ajaxUrl,
            params = {
                action: "oxy_get_soundcloud_track_id",
                soundcloud_url: soundcloudURL,
                post_id: CtBuilderAjax.postId,
                nonce: CtBuilderAjax.nonce,
            };

        // Send AJAX request
        $http({
            method: "POST",
            url: url,
            params: params
        })
        .success(function(data, status, headers, config) {
            if(data){
                $scope.setOptionModel("soundcloud_track_id",data);
            }
            else {
                $scope.showErrorModal(0, 'Error retrieving SoundCloud Track ID. Please check the URL you specified');
            }
            $parentScope.hideLoadingOverlay();
        })
        .error(function(data, status, headers, config) {
            console.log(data, status);
            $parentScope.hideLoadingOverlay();
        });
    }

});
