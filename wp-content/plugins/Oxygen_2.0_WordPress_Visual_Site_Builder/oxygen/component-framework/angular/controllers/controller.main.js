var CTFrontendBuilder = angular.module('CTFrontendBuilder', [angularDragula(angular),'ngAnimate','ui.codemirror', "dndLists", 'CTCommonDirectives']);

var iframeScope;
var isChrome = /Chrome/.test(navigator.userAgent) && /Google Inc/.test(navigator.vendor);
CTFrontendBuilder.controller("MainController", function($scope, $parentScope, $http, $timeout, $window, ctScopeService, $sce) {
    iframeScope = $scope;
    ctScopeService.store('scope', $scope);
    // log
    $scope.log = false;

    $scope.isInnerContent = false;
    $scope.selectAncestors      = [];
    $scope.sidebarEditFriendlyName  = false;
    $scope.isDesiableDraggable = false;

    $scope.currentActiveFolder = '';
    $scope.currentActiveStylesheetFolder = '';

    $scope.styleFolderSelected = false;
    $scope.isChrome = isChrome;

    $scope.reusableEditLinks = {};
    $scope.outerTemplateData = {};
    // store component HTML tags in an object
    $scope.htmlTags = {};

    // cache
    $scope.cache = [];

    $scope.iconFilter = {};

    // initial values
    $scope.component = {

        // currently active component
        active : {  
            id : 0,
            name : 'root',
            state : 'original', // element state like 'hover'
            parent: {
                id : null,
                name : ""
            }
        },

        // components counter
        id : 1,

        // all components options
        options: {
            0 : {
                'original' : {},
                'media' : {
                    'original' : {}
                }
            }
        }
    }
    

    $scope.addDynamicContent = function(holder, content) {

        iframeScope.addComponent(holder);

        var newComponent = {
            id : $scope.component.id, 
            name : "ct_span"
        }

        // set default options first
        $scope.applyComponentDefaultOptions(newComponent.id, "ct_span");
        
        iframeScope.setOptionModel('ct_content', "<span id=\"ct-placeholder-"+newComponent.id+"\"></span>");

        // insert new component to Components Tree
        $scope.findComponentItem($scope.componentsTree.children, $scope.component.active.id, $scope.insertComponentToTree, newComponent);

        // update span options
        $scope.component.options[newComponent.id]["model"]["ct_content"] = content;

        $scope.setOption(newComponent.id, "ct_span", "ct_content");

        $scope.rebuildDOM(newComponent.id);

    }

    /**
     * Build DOM based on Components Tree JSON
     * 
     * @since 0.1
     */
    
    $scope.init = function() {

        $parentScope.$emit('iframe-scope',$scope);
        $parentScope.$apply();

        $scope.loadComponentsTree($scope.builderInit);
        
        // fonts
        $scope.getWebFontsList();
        $scope.loadSavedGlobalFonts();
        $scope.updateGlobalSettingsCSS();

        $scope.loadSVGIconSets();
        
        // setup UI
        $parentScope.builderElement = jQuery("#ct-builder");
        $parentScope.setupUI();

        angular.element(document).ready(function() {
            angular.element('body').on('keyup', function(e) {
                // if text is being edited, do not propagate, in order to prevent it from sliding the unslider
                if(angular.element(e.target).attr('contenteditable')) {
                    e.stopPropagation();
                }
                
            })
        })


        $parentScope.$apply();

        $scope.setupPageLeaveProtection();
        $scope.setupJSErrorNotice();

        $parentScope.adjustViewportContainer();
        $scope.outputPageSettingsCSS();
        $scope.initMedia();
        $scope.checkAPIstatus()        

        $scope.isInnerContent = jQuery('body').hasClass('ct_inner');
        
        $scope.ajaxVar = CtBuilderAjax;

        // must be in the end of init()
        $parentScope.$emit('iframe-init',$scope);
        $parentScope.$apply();

        $scope.initDynamicShortcodeData();
    }

    $scope.initDynamicShortcodeData = function() {

        $scope.dynamicShortcodePHP = {
            name: 'PHP Function Return value',
            data: 'phpfunction',
            properties: [
                {
                    name: 'Function Name',
                    data: 'function',
                    type: 'text'
                },
                {
                    name: 'Function Arguments (separated by comma)',
                    data: 'arguments',
                    type: 'text'
                }
            ]
        };

        $scope.dynamicShortcodesCFOptions = {
                        name: 'Custom Field/Meta Options',
                        data: 'meta',
                        properties: [
                            {
                                name: 'Key',
                                data: 'key',
                                type: 'select',
                                options: _.map(CtBuilderAjax.oxygenMetaKeys, function(item) { return item})
                            },
                            {
                                name: 'custom',
                                data: 'key',
                                type: 'text'
                            }
                        ]
                    };

        $scope.dynamicShortcodesContentMode = [
            {
                name: 'Post',
                children: [
                    {
                        name: 'Title',
                        data: 'title',
                        properties: [
                            {
                                name: 'Link',
                                data: 'link',
                                type: 'checkbox',
                                value: 'permalink'
                            },
                        ]
                    },
                    {
                        name: 'Content',
                        data: 'content'
                    },
                    {
                        name: 'Excerpt',
                        data: 'excerpt'
                    },
                    {
                        name: 'Date',
                        data: 'date',
                        properties: [
                            {
                                name: 'Format',
                                data: 'format',
                                type: 'text'
                            }
                        ]
                    },
                    {
                        name: 'Categories, Tags, Taxonomies',
                        data: 'terms',
                        properties: [
                            {
                                name: 'Taxonomy',
                                data: 'taxonomy',
                                type: 'select',
                                options: [
                                    'category',
                                    'post_tag'
                                ]
                            },
                            {
                                name: 'Separator',
                                data: 'separator',
                                type: 'text'
                            }
                        ]
                    },
                    $scope.dynamicShortcodesCFOptions,
                    {
                        name: 'Comments Number',
                        data: 'comments_number',
                        properties: [
                            {
                                name: 'No Comments',
                                data: 'zero',
                                type: 'text'
                            },
                            {
                                name: 'One Comment',
                                data: 'one',
                                type: 'text'
                            },
                            {
                                name: 'Multiple Comments (% is replaced by the number of comments)',
                                data: 'more',
                                type: 'text'
                            },
                            {
                                name: 'Link',
                                data: 'link',
                                type: 'checkbox',
                                value: 'comments_link'
                            },
                        ]
                    }

                ]
            },
            {
                name: 'Featured Image',
                children: [
                    {
                        name: 'Title',
                        data: 'featured_image_title'
                    },
                    {
                        name: 'Caption',
                        data: 'featured_image_caption'
                    },
                    {
                        name: 'Alt',
                        data: 'featured_image_alt'
                    },

                ]
            },
            {
                name: 'Author',
                children: [
                    {
                        name: 'Display Name',
                        data: 'author',
                        properties: [
                            {
                                name: 'Link',
                                data: 'link',
                                type: 'select',
                                options: [
                                    'none', 'author_website_url', 'author_posts_url'
                                ],
                                nullVal: 'none'
                            },
                        ]
                    },
                    {
                        name: 'Bio',
                        data: 'author_bio'
                    },
                    {
                        name: 'Meta / Custom Field',
                        data: 'author_meta',
                        properties: [
                            {
                                name: 'Meta Key',
                                data: 'key',
                                type: 'text'
                            }
                        ]
                    }
                ]
            },
            {
                name: 'Current User',
                children: [
                    {
                        name: 'Display Name',
                        data: 'user',
                        properties: [
                            {
                                name: 'Link',
                                data: 'link',
                                type: 'checkbox',
                                value: 'user_website_url'
                            },
                        ]
                    },
                    {
                        name: 'Bio',
                        data: 'user_bio'
                    },
                    {
                        name: 'Meta / Custom Field',
                        data: 'user_meta',
                        properties: [
                            {
                                name: 'Meta Key',
                                data: 'key',
                                type: 'text'
                            }
                        ]
                    }
                ]
            },
            {
                name: 'Blog Info',
                children: [
                    {
                        name: 'Site Title',
                        data: 'bloginfo',
                        append: 'show=\'name\''
                    },
                    {
                        name: 'Site Tagline',
                        data: 'bloginfo',
                        append: 'show=\'description\''
                    },
                    {
                        name: 'Other',
                        data: 'bloginfo',
                        properties: [
                            {
                                name: 'Show',
                                data: 'show',
                                type: 'select',
                                options: [
                                    'wpurl', 'url', 'admin_email', 'charset', 'version', 'html_type', 'text_direction', 'language', 'stylesheet_url', 'stylesheet_directory', 'template_url', 'pingback_url', 'atom_url', 'rdf_url', 'rss_url', 'rss2_url', 'comments_atom_url', 'comments_rss2_url'
                                ]

                            }
                        ]
                    }
                ]
            },
            {
                name: 'Archive',
                children: [
                    {
                        name: 'Archive Title',
                        data: 'archive_title'
                    },
                    {
                        name: 'Archive Description',
                        data: 'archive_description'
                    }
                ]
            },
            {
                name: 'Advanced',
                children: [
                    $scope.dynamicShortcodePHP
                ]
            }
        ];

        $scope.dynamicShortcodesCustomFieldMode = [
            {
                name: 'Post',
                children: [
                    {
                        name: 'Meta / Custom Field',
                        data: 'meta',
                        properties: [
                            {
                                name: 'Meta Key',
                                data: 'key',
                                type: 'select',
                                options: _.map(CtBuilderAjax.oxygenMetaKeys, function(item) { return item })
                            },
                            {
                                name: 'custom',
                                data: 'key',
                                type: 'text'
                            }
                        ]
                    }
                ]

            },
            {
                name: 'Author',
                children: [
                    {
                        name: 'Meta / Custom Field',
                        data: 'author_meta',
                        properties: [
                            {
                                name: 'Meta Key',
                                data: 'key',
                                type: 'text'
                            }
                        ]
                    }
                ]

            },
            {
                name: 'Current User',
                children: [
                    {
                        name: 'Meta / Custom Field',
                        data: 'user_meta',
                        properties: [
                            {
                                name: 'Meta Key',
                                data: 'key',
                                type: 'text'
                            }
                        ]
                    }
                ]

            },
            {
                name: 'Advanced',
                children: [
                    $scope.dynamicShortcodePHP
                ]
            }

        ];
        
        $scope.dynamicShortcodesLinkMode = [
            {
                name: 'Post',
                children: [
                    {
                        name: 'Permalink',
                        data: 'permalink'
                    },
                    {
                        name: 'Comments Link',
                        data: 'comments_link'
                    },
                    {
                        name: 'Meta / Custom Field',
                        data: 'meta',
                        properties: [
                            {
                                name: 'Meta Key',
                                data: 'key',
                                type: 'select',
                                options: _.map(CtBuilderAjax.oxygenMetaKeys, function(item) { return item })
                            },
                            {
                                name: 'custom',
                                data: 'key',
                                type: 'text'
                            }
                        ]
                    }
                ]

            },
            {
                name: 'Featured Image',
                children: [
                    {
                        name: 'Featured Image URL',
                        data: 'featured_image'
                    }
                ]

            },
            {
                name: 'Author',
                children: [
                    {
                        name: 'Author Website URL',
                        data: 'author_website_url'
                    },
                    {
                        name: 'Author Posts URL',
                        data: 'author_posts_url'
                    },
                    {
                        name: 'Meta / Custom Field',
                        data: 'author_meta',
                        properties: [
                            {
                                name: 'Meta Key',
                                data: 'key',
                                type: 'text'
                            }
                        ]
                    }
                ]

            },
            {
                name: 'Current User',
                children: [
                    {
                        name: 'User Website URL',
                        data: 'user_website_url'
                    },
                    {
                        name: 'Meta / Custom Field',
                        data: 'user_meta',
                        properties: [
                            {
                                name: 'Meta Key',
                                data: 'key',
                                type: 'text'
                            }
                        ]
                    }
                ]

            },
            {
                name: 'Advanced',
                children: [
                    $scope.dynamicShortcodePHP
                ]
            }

        ];

        $scope.dynamicShortcodesImageMode = [
            {
                name: 'Post',
                children: [
                    {
                        name: 'Featured Image',
                        data: 'featured_image',
                        properties: [
                            {
                                name: 'Size',
                                data: 'size',
                                type: 'select',
                                options: [
                                    'thumbnail', 'medium', 'medium_large', 'large'
                                ],
                                change: "scope.dynamicDataModel.width = ''; scope.dynamicDataModel.height = ''"
                            },
                            {
                                name: 'or',
                                type: 'label'
                            },
                            {
                                name: 'Width',
                                data: 'width',
                                type: 'text',
                                helper: true,
                                change: "scope.dynamicDataModel.size = scope.dynamicDataModel.width+'x'+scope.dynamicDataModel.height"
                            },
                            {
                                name: 'Height',
                                data: 'height',
                                type: 'text',
                                helper: true,
                                change: "scope.dynamicDataModel.size = scope.dynamicDataModel.width+'x'+scope.dynamicDataModel.height"
                            },
                            {
                                type: 'break'
                            },
                            {
                                name: 'Default',
                                data: 'default',
                                type: 'text',
                            }
                        ]
                    },
                    {
                        name: 'Meta / Custom Field',
                        data: 'meta',
                        properties: [
                            {
                                name: 'Meta Key',
                                data: 'key',
                                type: 'select',
                                options: _.map(CtBuilderAjax.oxygenMetaKeys, function(item) { return item })
                            },
                            {
                                name: 'custom',
                                data: 'key',
                                type: 'text'
                            }
                        ]
                    }
                ]
            },
            {
                name: 'Author',
                children: [
                    {
                        name: 'Author Pic',
                        data: 'author_pic',
                        properties: [
                            {
                                name: 'Size',
                                data: 'size',
                                type: 'select',
                                options: [
                                    '400', '200'
                                ]
                            },
                        ]
                    },
                    {
                        name: 'Meta / Custom Field',
                        data: 'author_meta',
                        properties: [
                            {
                                name: 'Meta Key',
                                data: 'key',
                                type: 'text'
                            }
                        ]
                    }
                ]
            },
            {
                name: 'User',
                children: [
                    {
                        name: 'User Pic',
                        data: 'user_pic',
                        properties: [
                            {
                                name: 'Size',
                                data: 'size',
                                type: 'select',
                                options: [
                                    'Default', '200'
                                ],
                                nullval: 'Default'
                            },
                        ]
                    },
                    {
                        name: 'Meta / Custom Field',
                        data: 'user_meta',
                        properties: [
                            {
                                name: 'Meta Key',
                                data: 'key',
                                type: 'text'
                            }
                        ]
                    }
                ]
            },
            {
                name: 'Advanced',
                children: [
                    $scope.dynamicShortcodePHP
                ]
            }
        ]
    }

    /**
     * Set cursor to the end of contenteditbale. Taken from http://stackoverflow.com/a/3866442/2198798
     *
     * @since 1.1.2
     */

    $scope.setEndOfContenteditable = function(contentEditableElement) {
        var range,selection;
        if(document.createRange)//Firefox, Chrome, Opera, Safari, IE 9+
        {
            range = document.createRange();//Create a range (a range is a like the selection but invisible)
            range.selectNodeContents(contentEditableElement);//Select the entire contents of the element with the range
            
            selection = window.getSelection();//get the selection object (allows you to change selection)
            selection.removeAllRanges();//remove any selections already made
            selection.addRange(range);//make the range you have just created the visible selection

            range.collapse(false);//collapse the range to the end point. false means collapse to end rather than the start
        }
        else if(document.selection)//IE 8 and lower
        { 
            range = document.body.createTextRange();//Create a range (a range is a like the selection but invisible)
            range.moveToElementText(contentEditableElement);//Select the entire contents of the element with the range
            range.collapse(false);//collapse the range to the end point. false means collapse to end rather than the start
            range.select();//Select the range (make it the visible selection
        }
    }

    $scope.insertAtCursor = function(text) {

        var sel, range, html;

        text=text.replace(/\"/ig, "'");

        if (window.getSelection) {
            sel = window.getSelection();
            if (sel.getRangeAt && sel.rangeCount) {
                range = sel.getRangeAt(0);
                range.deleteContents();
                range.insertNode( document.createTextNode(text) );
            }
        } else if (document.selection && document.selection.createRange) {
            document.selection.createRange().text = text;
        }

        angular.element(window.getSelection().focusNode).trigger('input');
    }

    $scope.insertShortcodeToMapAddress = function(text) {
        text=text.replace(/\"/ig, "'");
        var id = $scope.component.active.id;
        $scope.setOptionModel('map_address', text, id);

        $scope.parseMapShortcode(id);
    }

    $scope.parseMapShortcode = function(id) {
        
        if(typeof(id) === 'undefined') {
            id = $scope.component.active.id;
        }

        var map_address = $scope.getOption('map_address', id);

        var callback = function(contents) {
            var element = $scope.getComponentById(id);
            
            var iframe = element.find('iframe');
            if(iframe.length > 0) {
                iframe.attr('src', iframe.attr('src').replace(/q=[^\&]*/, 'q='+contents.trim()));
            }
        }

        $scope.applyShortcodeResults(id, map_address, callback);
    }

    $scope.insertShortcodeToBackground = function(text) {

        text=text.replace(/\"/ig, "'");
        var id = $scope.component.active.id;
        
        $scope.setOptionModel('background-image', text, id);

        //$scope.parseBackgroundShortcode(id);
    }

    $scope.parseBackgroundShortcode = function(id) {

        if(typeof(id) === 'undefined') {
            id = $scope.component.active.id;
        }

        var background = $scope.getOption('background', id);

        var callback = function(contents) {

            var component = $scope.findComponentItem($scope.componentsTree.children, id, $scope.getComponentItem);

            var classes = component.options.classes;

            var element = $scope.getComponentById(id);

            if($scope.isEditing('class') && (!component.options.original || !component.options.original.background)) {
                var foundActiveClass = false;
                var propertyTrumped = false;

                _.each(classes, function(item) {
                   
                    if(!foundActiveClass && item === $scope.currentClass) {
                        foundActiveClass = true;
                    }
                    else if(foundActiveClass) {
                        if(iframeScope.classes[item].original.background) {
                            propertyTrumped = true;
                        }
                    }
                });

                if(!propertyTrumped) {
                    element.css('background-image', 'url('+contents.trim()+')');
                }
            }
            
            if(
                $scope.isEditing('id')) {
                element.css('background-image', 'url('+contents.trim()+')');
            }
            
            
        }

        $scope.applyShortcodeResults(id, background, callback);
    }

    $scope.insertShortcodeToImageAlt = function(text) {
        text=text.replace(/\"/ig, "'");
        var id = $scope.component.active.id;
        $scope.setOptionModel('alt', text, id);
    }

    $scope.insertShortcodeToUrl = function(text) {
        text=text.replace(/\"/ig, "'");
        var id = $scope.component.active.id;
        $scope.setOptionModel('url', text, id);
    }

    $scope.insertShortcodeToSrc = function(text) {
        text=text.replace(/\"/ig, "'");
        var id = $scope.component.active.id;
        $scope.setOptionModel('src', text, id);   
    }

    $scope.insertShortcodeToImage = function(text) {
        var id = $scope.component.active.id;
            text=text.replace(/\"/ig, "'");
            $scope.setOptionModel('src', text, id);
            
        var timeout = $timeout(function() {
            $scope.parseImageShortcode();
            // cancel timeout
            $timeout.cancel(timeout);
        }, 0, false);  
    }

    $scope.parseImageShortcode = function(id) {
        
        if(typeof(id) === 'undefined') {
            id = $scope.component.active.id;
        }

        var src = $scope.getOption('src', id);

        var callback = function(contents) {
            var element = $scope.getComponentById(id);
            element.attr('src', contents.trim());
        }

        $scope.applyShortcodeResults(id, src, callback);

    }

    $scope.applyShortcodeResults = function(id, attribute, callback, param) {
        var matches = [];
        var matchCount = 0;

        var replacer = function(shortcode, contents) {

            matchCount++;

            attribute = attribute.replace(shortcode, contents);

            if(matchCount == matches.length) {
                if(!contents) {
                    contents = '';
                }

                callback(contents, param);
            }
        }

        matches = attribute.match(/\[oxygen[^\]]*\]/ig);

        for(key in matches) {

            var shortcode_data ={
                original: {
                    full_shortcode: matches[key].trim()
                }
            }

            $scope.renderShortcode(id, 'ct_shortcode', replacer, shortcode_data);
        }


    }


    /**
     * Parse YouTube/Vimeo page urls to embeddable links
     * 
     * @since 2.0
     */
    $scope.getYoutubeVimeoEmbedUrl = function(url) {

        if(typeof(url) === 'undefined' || url.trim() === '') {
            return url;
        }
        var regexp = /(youtube\.com|youtu\.be|vimeo\.com)\/(watch\?v\=)?(.*)/;
        var matches = url.match(regexp)

        if(matches[1] && matches[3] && matches[3].indexOf('/') === -1) {
            if(matches[1] == 'youtube.com' || matches[1] == 'youtu.be') {
                if (matches[3].split('&').length>1) {
                    matches[3] = matches[3].split('&')[0]
                }
                return 'https://www.youtube.com/embed/' + matches[3];
            }
            else if(matches[1] == 'vimeo.com') {
                return 'https://player.vimeo.com/video/' + matches[3];
            }
        }
        else {
            return url;
        }

    }

    /**
     * Build DOM from Components Tree
     * 
     * @since 0.1
     */

    $scope.buildComponentsFromTree = function(componentsTree, id, reorder) {

        if ($scope.log) {
            //console.log("buildComponentsFromTree()", id);
        }

        // handle root
        if ( 0 == id ) {
            var element = $scope.getComponentById(id);
            element.empty();
            element = null;
            $scope.buildComponentsFromTree($scope.componentsTree.children);

            return false;
        }


        var stopBuilding = false,
            builtinOffset = 0;

        //for(var index in componentsTree) { 
            //if (componentsTree.hasOwnProperty(index)) {
                //var item = componentsTree[index];

        //for (index = 0; index < componentsTree.length; ++index) {
            //var item = componentsTree[index];

        angular.forEach(componentsTree, function(item, index) {

            if ( !stopBuilding ) {

                if (item.options.oxy_builtin) {
                    builtinOffset++;
                }

                var key = item.id;

                // if we only need to rebuild specific node
                if ( id ) {
                    
                    // found node
                    if ( key == id ) {
                        if(!reorder)
                            $scope.removeComponentFromDOM(id);
                        stopBuilding = true;
                    } 
                    else {
                        // go deeper to find
                        if ( item.children ) {
                            $scope.buildComponentsFromTree(item.children, id, reorder);
                        }
                        // stop from building
                        return false;
                    }
                }
                
                /**
                 * Apply all options
                 */
                
                // set default options first
                $scope.applyComponentDefaultOptions(key, item.name, item);

                // set saved 'original' options
                $scope.applyComponentSavedOptions(key, item);

                // add saved classes
                if ( item.options.classes ) {
                    $scope.componentsClasses[key] = angular.copy(item.options.classes);
                }

                // apply model
                $scope.applyModelOptions(key, item.name);

                // save styles to cache
                $scope.updateComponentCacheStyles(key, function() {
                    // clear cache
                    $scope.cache.idCSS = "";

                    Object.keys($scope.cache.idStyles).map(function(key, index) {
                        $scope.cache.idCSS += $scope.cache.idStyles[key];
                    }); 
                    
                    $scope.outputCSSStylesAfterWait('id', $scope.cache.idCSS);

                });

                /**
                 * Start building
                 */
                
                var type = "";

                // TODO: this is stupid and needs to be changed
                if ( item.options.ct_shortcode ) {
                    type = "shortcode";
                }

                if ( item.options.ct_widget ) {
                    type = "widget";
                }

				if ( item.options.ct_data ) {
					type = "data";
				}

                if ( item.options.ct_sidebar ) {
                    type = "sidebar";
                }

                if ( item.options.ct_nav_menu ) {
                    type = "nav_menu";
                }

                if ( item.options.oxy_builtin ) {
                    type = "builtin";
                }


                var componentParent     = $scope.getComponentById(item.options.ct_parent),
                    componentTemplate   = $scope.getComponentTemplate(item.name, key, type);
                
                if(item.id > 100000) {
                         
                    componentTemplate = componentTemplate.replace(/\s?[^\=\s]*\=\"[^\"]*\"/g, 
                        function(match, contents, offset, s)
                        {
                            if(match.indexOf(' ng-mouse') !== 0) {//(match.indexOf(' class') === 0 || match.indexOf(' id') === 0))
                                /*if(match.indexOf(' class') === 0 && item.name !== "ct_inner_content") {
                                    match = match.substring(0 , match.length-1)+', disabled-div"';
                                }*/
                                return match;
                            }
                            else
                                return '';
                        }
                    );
                }

                // set columns number for Columns component
                if ( item.name == "ct_columns" && item.children ) {
                    $scope.columns[item.id] = item.children.length;
                }

                // handle builtin components
                if (type=="builtin") {
                    var builtInWrap = $scope.getBuiltInWrap(componentParent);
                    $scope.cleanInsert(componentTemplate, builtInWrap, index);
                }
                else

                // handle Re-usable components
                if ( item.name == "ct_reusable" ) {
                    
                    var viewId      = item.options.view_id,
                        componentId = item.options.ct_id;
                    
                    // insert to DOM
                    var innerWrap = $scope.getInnerWrap(componentParent);
                    $scope.cleanInsert(componentTemplate, innerWrap, index-builtinOffset);
                    // load post
                    $scope.loadPostData($scope.addReusableContent, viewId, componentId);
                }
                else

                // handle span component
                if ( item.name == "ct_span" || (item.name == "ct_link_text" && (!componentParent[0] || componentParent[0].attributes['contenteditable']))) {
                    // find if there any placeholder inside any part of the component
                    var noPlaceholders = true;

                    if (componentParent) {
                        var placeholders = componentParent.find('[id^="ct-placeholder"]');
                        if (placeholders.length>0) {
                            noPlaceholders = false;
                        }
                    }

                    if ( componentParent[0] && !componentParent[0].attributes['contenteditable'] && noPlaceholders) {
                        $scope.cleanInsert(componentTemplate, componentParent, index-builtinOffset);
                    }
                    else {

                        var timeout = $timeout(function() {
                            
                            var placeholderID = (item.id>=200000) ? item.id-200000 : item.id,
                                placeholderID = (placeholderID>=100000) ? placeholderID-100000 : placeholderID,
                                placeholder = document.getElementById("ct-placeholder-"+placeholderID);
                            $scope.cleanReplace(placeholder,componentTemplate);

                            // cancel timeout
                            $timeout.cancel(timeout);
                        }, 0, false);
                    }

                }
                else

                // handle ct_link component
                if ( item.name == "ct_link" && componentParent[0] && componentParent[0].attributes['contenteditable'] ) {
                    
                    var timeout = $timeout(function() {

                        var placeholderID = (item.id>=200000) ? item.id-200000 : item.id,
                            placeholderID = (placeholderID>=100000) ? placeholderID-100000 : placeholderID,
                            placeholder = document.getElementById("ct-placeholder-"+placeholderID);

                        $scope.cleanReplace(placeholder,componentTemplate);
                        $scope.rebuildDOM(item.children[0].id);

                        // get highest id number
                        if ( parseInt(item.id) > $scope.component.id && parseInt(item.id) < 100000) {
                            $scope.component.id = parseInt(key);
                        }
                        if ( parseInt(item.id) == $scope.component.id ) {
                            $scope.component.id++;
                        }

                        // cancel timeout
                        $timeout.cancel(timeout);
                    }, 0, false);

                    return;
                } 

                // handle other components
                else {
                    var innerWrap = $scope.getInnerWrap(componentParent);
                    $scope.cleanInsert(componentTemplate, innerWrap, index-builtinOffset);
                }

                if ( item.name == "ct_separator" ) {
                    $scope.separatorAdded = true;
                }

                if (item.name == "ct_inner_content") {
                    if (parseInt(key) >= 100000) {
                        $scope.innerContentRoot = item;
                    }
                    else {
                        $scope.innerContentAdded = true;
                    }
                }

                // get highest id number
                if ( parseInt(key) > $scope.component.id  && parseInt(key) < 100000) {
                    $scope.component.id = parseInt(key);
                }

                if (item.name == "ct_image") {
                    var src = $scope.getOption('src', item.id);
                    if(src.indexOf('[oxygen') > -1) {
                        setTimeout(function() {
                            $scope.parseImageShortcode(item.id);
                        }, 0);
                    }
                }

                if (item.name == "oxy_map") {
                    var map_address = $scope.getOption('map_address', item.id);
                    if(map_address.indexOf('[oxygen') > -1) {
                        setTimeout(function() {
                            $scope.parseMapShortcode(item.id);
                        }, 0);
                    }
                }

                
                // setTimeout(function() {
                //     $scope.parseBackgroundShortcode(item.id);
                // }, 0);
                
                
                // go deeper in Components Tree
                if ( item.children ) {
                    $scope.buildComponentsFromTree(item.children, null, reorder);
                }
            }
        });
        
            //}
        //}
    }


    /**
     * Get currently active component DOM element
     * 
     * @return {jqLite Object}
     * @since 0.1
     */

    $scope.getActiveComponent = function() {

        if ($scope.log) {
            console.log("getActiveComponent()");
        }

        return $scope.getComponentById($scope.component.active.id);
    }


    $scope.setCurrentStylesheetFolder = function(id) {
        $scope.stylesheetToEdit['parent'] = id;

        if(id > 0) {

            var parent = _.findWhere($scope.styleSheets, {id: id});

            if(parent) {
                $scope.currentActiveStylesheetFolder = parent.name;
            }
            else {
                $scope.currentActiveStylesheetFolder = '';
            }
            
        }
        else {
            $scope.currentActiveStylesheetFolder = '';
        }

        $scope.$apply();
    }

    $scope.setCurrentSelectorFolder = function(value) {
        var selector;

        if($scope.isEditing('class')) {
            selector = $scope.classes[$scope.currentClass];
        }
        else {
            if($scope.selectedStyleSet) {
                selector = $scope.styleSets[$scope.selectedStyleSet];
            }
            else {
                selector = $scope.customSelectors[$scope.selectorToEdit];
            }
        }

        if(value !== '') {
            selector['parent'] = value;    
        }
        else {
            delete selector['parent'];
        }
        
        if(selector['parent'])
            $scope.currentActiveFolder = selector['parent']
        else
            $scope.currentActiveFolder = '';

        $scope.$apply();
        $scope.classesCached = false; 
        $scope.outputCSSOptions();
    }

    /**
     * Get component DOM element by ID
     * 
     * @return {jqLite Object}
     * @since 0.1
     */

    $scope.getComponentById = function(id) {

        if ($scope.log) {
            console.log("getComponentById()", id);
        }
        
        // get element by id
        var component = document.querySelector('[ng-attr-component-id="'+id+'"]');

        // return false if no active component found
        if ( !component || component.length == 0 ) {
            return false;
        }
        
        // create jqLite element
        component = angular.element(component);
        
        return component;
    }


    /**
     * Remove currently active component
     * 
     * @since 0.1
     */

    $scope.removeActiveComponent = function() {

        $scope.removeComponentWithUndo($scope.component.active.id, $scope.component.active.name, $scope.component.active.parent.id);
        //$scope.removeComponentById($scope.component.active.id, $scope.component.active.name);
    }

    /**
     * Check if component is empty
     * 
     * @since 2.0
     * @author Ilya K.
     */

    $scope.isEmptyComponent = function(id) {

        if (id===undefined) {
            id = $scope.component.active.id;
        }

        var component = $scope.getComponentById(id);

        if (component.is(':empty')){
            return true;
        }

        return false;
    }
    

    /**
     * Set editable status for component's friendly name in the DOM tree
     * 
     * @since 0.3.3
     * @author gagan goraya
     */    

    $scope.setEditableFriendlyName = function(id) {

        $scope.editableFriendlyName = id;

        // remove &nbsp characters and use default text
        var element = angular.element('[ng-attr-node-id="'+$scope.component.active.id+'"] span.ct-nicename');

        var item = $scope.findComponentItem($scope.componentsTree.children, $scope.component.active.id, $scope.getComponentItem);
        
        var trimmedText = $scope.component.options[$scope.component.active.id]['nicename'].replace(/&nbsp;/g, '').replace(/[^a-zA-Z0-9 ]/g, '');
        
        if(trimmedText === '' && typeof(element.data('defaulttext')) !== 'undefined' && element.data('defaulttext').trim() !== '') {
            trimmedText = element.data('defaulttext');
        }

        item.options['nicename'] = trimmedText;
        
        $scope.component.options[$scope.component.active.id]['nicename'] = trimmedText;
       
        // close the menu
        if(id > 0) {
            jQuery(".ct-more-options-expanded", parent.document).removeClass("ct-more-options-expanded");
        }
    }

    /**
     * searches for the node in the component tree and apply function to update nicename
     * 
     * @since 0.3.3
     * @author gagan goraya
     */    

    $scope.updateFriendlyName = function(id) {
        $scope.findComponentItem($scope.componentsTree.children, id, $scope.updateComponentNiceName);
    }

    /**
     * updates the nicename into the provided item out of the component tree
     * 
     * @since 0.3.3
     * @author gagan goraya
     */   

    $scope.updateComponentNiceName = function(id, item) {
        item.options['nicename'] = $scope.component.options[id]['nicename'];
    }


    /**
     * loads the nicename into the current state from the item of the component tree
     * 
     * @since 0.3.3
     * @author gagan goraya
     */   

    $scope.loadComponentNiceName = function(id, item) {
        if(item.options['nicename'])
            $scope.component.options[id]['nicename'] = item.options['nicename'];
    }


    /**
     * Cut the component and display notice to undo this action
     * 
     * @since 1.2
     * @author Ilya K.
     */

    $scope.removeComponentWithUndo = function(id, name, parentId) {

        $parentScope.disableContentEdit();

        if (name=="oxy_header_left"||name=="oxy_header_center"||name=="oxy_header_right"){
            return false;
        }
        if (name=="oxy_header_row"){
            var header_row = $scope.getComponentById(id);
            if (jQuery(header_row).siblings('.oxy-header-row').length === 0 ) {
                
                $scope.activateComponent(parentId,'oxy_header');
                $scope.removeActiveComponent();
                return;
            }
        }

        $scope.removeComponentById(id, name, parentId);

        if (name=="ct_slide") {
            $scope.rebuildDOM(parentId);
            $scope.titleBarsVisibility("hidden","hidden"); 
        }

        // create notice        
        var noticeContent = "<div>You deleted " + $scope.component.options[id]['nicename'] + 
                            ". <span class=\"ct-undo-delete\" ng-click=\"undoDelete()\">Undo</span></div>";

        $scope.showNoticeModal(noticeContent);
    }

    
    /**
     * Cancel remove timeout
     * 
     * @since 1.2
     * @author Ilya K.
     */

    $scope.undoDelete = function() {

        // find component to paste
        $scope.findComponentItem($scope.componentsTree.children, $scope.componentInsertId, $scope.pasteComponentToTree);

        if ($scope.removedComponentName=="ct_span"){

            // restore previous parent content
            $scope.component.options[$scope.componentInsertId]["model"]["ct_content"] = $scope.removedComponentParentContent;
            $scope.component.options[$scope.componentInsertId]["original"]["ct_content"] = $scope.removedComponentParentContent;
            $scope.setOption($scope.componentInsertId, "", "ct_content");

            $scope.rebuildDOM($scope.componentInsertId);
        }
        else {
            $scope.rebuildDOM($scope.removedComponentId);
        }

        $scope.updateDOMTreeNavigator($scope.removedComponentId);

        $scope.outputCSSOptions($scope.removedComponentId)
        $scope.adjustResizeBox();
        $scope.cancelDeleteUndo();
    }


    /**
     * Cancel remove timeout
     * 
     * @since 1.2
     * @author Ilya K.
     */

    $scope.cancelDeleteUndo = function(id, name) {

        $scope.idToInsert = -1;
        $scope.hideNoticeModal();
    }


    /**
     * Show notice modal
     * 
     * @since 1.2
     * @author Ilya K.
     */

    $scope.showNoticeModal = function(noticeContent) {

        $scope.noticeModalVisible = true;

        // set content
        var noticeContentContainer = window.parent.document.getElementById("ct-notice-content");
        noticeContentContainer = angular.element(noticeContentContainer);

        // inseret to DOM
        angular.element(document).injector().invoke(function($compile) {
            noticeContentContainer.html($compile(noticeContent)($scope));
        });
    }


    /**
     * Hide notice modal
     * 
     * @since 1.2
     * @author Ilya K.
     */

    $scope.hideNoticeModal = function() {

        // clear content
        var noticeContentContainer = window.parent.document.getElementById("ct-notice-content");
        angular.element(noticeContentContainer).html("");

        $scope.noticeModalVisible = false;
    }

    
    /**
     * Remove component from DOM and Components Tree
     * 
     * @since 0.1.8
     */

    $scope.removeComponentById = function(id, name, parentId) {

        if ($scope.log) {
            console.log("removeComponentById()", id, name, parentId);
        }

        if ( id === 0 ) {
            alert('You can not delete root!');
            return;
        }

        // switch state
        $scope.switchState('original');

        // save IDs in scope
        $scope.componentInsertId = parentId;
        $scope.removedComponentId = id;
        $scope.removedComponentName = name;

        // update active parent
        $scope.findParentComponentItem($scope.componentsTree, id, $scope.updateCurrentActiveParent);

        // handle column remove
        if ( name == "ct_column" || (name == "ct_div_block" && $scope.isActiveParent("ct_new_columns")) ) {
            $scope.removeColumn(id);
        } 
        else {

            // remove from Components Tree
            $scope.findParentComponentItem($scope.componentsTree, id, $scope.cutComponentFromTree);

            // remove from DOM
            var component = $scope.getComponentById(id);

            // save index globally
            $scope.newComponentKey = component.index();
            // not sure why we were need the index
            //$scope.newComponentKey = -1;

            $scope.removeComponentFromDOM(id);

            // handle span component
            if ( name == "ct_span" ) {
                var parent = $scope.getComponentById(parentId);

                // save content
                $scope.removedComponentParentContent = $scope.component.options[parentId]["original"]["ct_content"];

                $scope.component.options[parentId]["model"]["ct_content"] = parent[0].innerHTML;
                $scope.component.options[parentId]["original"]["ct_content"] = parent[0].innerHTML;
                $scope.setOption(parentId, "", "ct_content");

                $scope.rebuildDOM(parentId);
            }

            // handle slide component
            if ( name == "ct_slide" ) {
                $scope.rebuildDOM($scope.component.active.id);
            }

            $scope.removeDOMTreeNavigatorNode(id);
        }

        // header/footer separator
        if ( name == "ct_separator" ) {
            $scope.separatorAdded = false;
        }

        if ( name == "ct_inner_content" ) {
            $scope.innerContentAdded = false;
        }

        // activate parent
        if($scope.component.active.parent.id < 100000) {
            $scope.activateComponent($scope.component.active.parent.id, $scope.component.active.parent.name);
        }
        else {
            $scope.activateComponent(0, 'root');
        }

        // remove custom-css
        $scope.deleteCSSStyles("css-code-"+id);
        
        // clear styles cache
        $scope.removeComponentCacheStyles(id);

        $scope.contentEditingEnabled = false;
        $scope.linkEditingEnabled = false;

        $scope.unsavedChanges();
    }

    /**
     * Duplicate component by ID
     * 
     * @since 0.3.0
     */

    $scope.duplicateComponent = function(id, name, parentId) {

        if (undefined === id) {
            id = $scope.component.active.id;
        }

        if (undefined === name) {
            name = $scope.component.active.name;
        }

        if (undefined === parentId) {
            parentId = $scope.component.active.parent.id;
        }

        // never duplicate a ct_inner_content, because there can be only one
        var innerContentExists = false;
        if($scope.component.active.name === 'ct_inner_content') {
            innerContentExists = true;
        }
        // but what if there is an inner_content deep within the hierarchy?
        var component = $scope.findComponentItem($scope.componentsTree.children, id, $scope.getComponentItem);
        // recursively go through the children, and see if there is any element with the name ct_inner_content
        if($scope.findComponentByName(component.children, 'ct_inner_content', $scope.getComponentItem)) {
            innerContentExists = true;
        }

        if(innerContentExists) {
            alert('You cannot add more than one Inner Content component to a template.');
            return;
        }

        var newComponentId = $scope.component.id;

        // copy active Selector state if the it is 'ID'
        if(typeof($scope.activeSelectors[id]) !== 'undefined') {
            if($scope.activeSelectors[id] === false) {
                $scope.activeSelectors[newComponentId] = false;
            }
        }

        $scope.applyComponentDefaultOptions(newComponentId, $scope.component.active.name);
            
        // copy component tree node
        $scope.findParentComponentItem($scope.componentsTree, id, $scope.copyComponentTreeNode);

        // find component to paste
        $scope.findComponentItem($scope.componentsTree.children, parentId, $scope.pasteComponentToTree);

        $scope.activateComponent($scope.componentBuffer.id, $scope.componentBuffer.name);
        
        // increase columns number
        if ( $scope.isActiveParent("ct_columns") ) {
            $scope.columns[$scope.component.active.parent.id]++;
        }
        
        if (name == "ct_slide") {
            $scope.rebuildDOM(parentId);
            $scope.titleBarsVisibility("hidden");
        }
        else {
            $scope.rebuildDOM(newComponentId);
        }

        $scope.updateDOMTreeNavigator(newComponentId);
        $scope.outputCSSOptions(newComponentId);

        // Lets keep the newly added component in memory, this will help to distinguish these from the ones loaded from db
        $scope.justaddedcomponents = $scope.justaddedcomponents || [];
        $scope.justaddedcomponents.push(newComponentId);

        // copy active Selector state. If it is current class
        if(typeof($scope.activeSelectors[id]) !== 'undefined') {
            
            if($scope.activeSelectors[id] !== false)
                $scope.setCurrentClass($scope.activeSelectors[id], true); // the second parameter signifies that we do not want to re-apply model options atm
        }

        // disable undo option
        $scope.cancelDeleteUndo();
    }

    
    /**
     * Activate Component
     * 
     * @since 0.1
     * @author Ilya K.
     */
    
    $scope.activateComponent = function(id, componentName, $event) {

        if ($scope.log) {
            console.log("activateComponent()", id, componentName);
        }

        $scope.selectedNodeType = false;

        // do nothing if in selector detector mode
        if ( $scope.selectorDetector.mode && ( ['ct_widget','ct_code_block','ct_shortcode','oxy_posts_grid','oxy_rich_text','oxy_gallery',
                                               'oxy_nav_menu','oxy_comments','oxy_comment_form','oxy_login_form','oxy_search_form',
                                               'ct_sidebar','ct_inner_content'].indexOf(componentName)>-1 || $scope.hasOxyDataInside(id) ) ) { 
            
            if ( $scope.componentSelector.id == id ) {
                $scope.selectorDetector.bubble = true;
                return false;
            }
        }

        // check if we are bubbling up from component with selector mode activated
        if ( id > 0 && $scope.selectorDetector.mode && $scope.selectorDetector.bubble ) {
            return false;
        }

        // disable selector detector mode
        if (componentName!="ct_selector"&&$parentScope.disableSelectorDetectorMode) {
            $parentScope.disableSelectorDetectorMode();
        }

        if (undefined===componentName) {
            componentName = $scope.component.options[id] ? $scope.component.options[id].name || "" : "";
        }

        if (componentName=="ct_widget") {
            var timeout = $timeout(function() {
                $scope.renderWidget(id,true)
                // cancel timeout
                $timeout.cancel(timeout);
            }, 0, false);            
        }

        $scope.stylesheetToEdit = false;
        $parentScope.actionTabs['styleSheet'] = false;

        // Fix for nested ng-click
        if (typeof $event != 'undefined') {
            // ok, but let it reach the document atleast or some click bindings on the document will not work
            angular.element(document).trigger($event.type);
            $event.stopPropagation();
        }

        if(componentName === 'ct_svg_icon' || componentName === 'ct_fancy_icon') {
            var currenticonid = $scope.component.options[id]['model']['icon-id'];
            $scope.iconFilter.title = '';
            angular.forEach($scope.SVGSets, function(SVGSet, index) {
                if(currenticonid.indexOf(index.split(' ').join('')) === 0) {
                    $scope.currentSVGSet = index;
                    angular.forEach(SVGSet['defs']['symbol'], function(symbol) {
                        if(symbol['@attributes']['id'] === currenticonid.replace(index.split(' ').join(''), ''))
                            $scope.iconFilter.title = symbol.title;
                    });     
                }
            });
        }

        // No need to actiavte component that already active
        if (id == $scope.component.active.id) {
            return false;
        }

        // make it impossible to activate any component from outer template
        if(id >= 100000) {
            //$scope.activateComponent(0, 'root');
            //return false;
        }

        // save component where selector detector were activate
        if (id !== -1) {
            $scope.componentSelector.id = id;
        }

        // unset parent element if activating root
        if (id == 0) {
            $scope.component.active.parent = {};
        }

        // disable selector detector pause mode
        $scope.selectorDetector.modePause = false;
        
        // close structure panel menus       
        jQuery(".ct-more-options-expanded", parent.document).removeClass("ct-more-options-expanded");

        var timeout = $timeout(function() {
            jQuery(".ct-highlight", "#ct-builder").removeClass('ct-highlight');
            // cancel timeout
            $timeout.cancel(timeout);
        }, 300, false);

        // while editing inner content, there is no point in activating the inner_content container
        /*if(componentName === 'ct_inner_content' && jQuery('body').hasClass('ct_inner')) {
            $scope.disableContentEdit();
            $scope.disableSelectable();
            $parentScope.closeAllTabs(["advancedSettings","componentBrowser"]); // keep certain sections
            return;
        }*/

        // fire blur on the previous nav component, if its nicename is being edited
        var previousNavElement = angular.element('[ng-attr-node-id="'+$scope.component.active.id+'"]');
        if(previousNavElement.find('span.ct-nicename.ct-nicename-editing').length) {

            $timeout(function() {
                previousNavElement.find('span.ct-nicename.ct-nicename-editing').trigger('blur');
            }, 0);
        }
        
        $scope.updateBreadcrumbs(id);

        // update dnd-type of resize box -- @author Jason
        $scope.selectedDragElementDNDType = componentName;

        // highlight only if event triggered not from the DOM Tree
        if ($event && !jQuery($event.currentTarget).hasClass('ct-dom-tree-node-anchor')){
            $scope.highlightDOMNode(id);
        }

        // trigger only from DOM Tree
        if ($event && jQuery($event.currentTarget).hasClass('ct-dom-tree-node-anchor') && jQuery($event.currentTarget).parents('.ct-sortable-tabs').length){
            var tab = jQuery($scope.getComponentById(id)).closest('.oxy-tabs-wrapper > div');
            if (tab.length) {
                tab.trigger('click');
            }
            // look if this is Tabs Contents click
            else {
                var tabsContentsTab = jQuery($scope.getComponentById(id)).closest('.oxy-tabs-contents-wrapper > div'),
                    index           = tabsContentsTab.index(),
                    tabsSelector    = tabsContentsTab.closest('.oxy-tabs-contents-wrapper').attr('data-oxy-tabs-wrapper');
                
                jQuery('#'+tabsSelector).children('.oxy-tabs-wrapper > div').eq(index).trigger('click');
            }

        }

        //close backgroundlayers when component is switched
        $scope.parentScope.activeForEditBgLayer = false;

        //close the advanced styles tab in the sidebar
        $scope.parentScope.styleTabAdvance = false;
        
        // disable stuff
        $parentScope.disableContentEdit();
        $parentScope.disableSelectable();
        $parentScope.closeAllTabs(["advancedSettings"]); // keep certain sections
        // TODO: make this list pulled automatically like for toolbar
        $parentScope.closeTabs(['easyPosts','slider','navMenu','gallery','oxy_testimonial','oxy_icon_box','oxy_progress_bar','oxy_pricing_box','oxy_superbox','effects'])

        // temporary fix for basic subtabs
        $parentScope.tabs.slider    = [];
        $parentScope.tabs.navMenu   = [];

        // update active component id and name
        $scope.previouslyActiveId       = $scope.component.active.id;
        $scope.previouslyActiveParentId = $scope.component.active.parent.id;
        $scope.component.active.id      = id;
        $scope.component.active.name    = componentName;

        if (id > 0) {
            // set all edits (class, state...) back to id
            $scope.switchEditToId();

            // update active component parent
            $scope.findParentComponentItem($scope.componentsTree, id, $scope.updateCurrentActiveParent);
        }

        if (componentName=="oxy_header_left"||componentName=="oxy_header_center"||componentName=="oxy_header_right"){
            $scope.activateComponent($scope.component.active.parent.id,"oxy_header_row");
            return;
        }

        if ( $scope.previouslyActiveParentId === $scope.component.active.parent.id ) {
            $scope.hideTitleBars(true, false);
        }
        else {
            $scope.hideTitleBars(true, true);
        }

        // Hide panels
        $scope.showComponentsList   = false;
        $scope.linkEditingEnabled   = false;

        // Hide Expanded
        $parentScope.toggleSidebar(true);

        // apply options
        $scope.applyModelOptions();

        // update CSS for disabled component
        $scope.outputCSSOptions($scope.previouslyActiveId);
        
        $parentScope.checkTabs();

		if(componentName =="ct_data_custom_field") {
			var timeout = $timeout(function() {
				$parentScope.updateMetaDropdown();
                // cancel timeout
                $timeout.cancel(timeout);
			},300, false );
		}

    }


    /**
     * Populate the select breadcrumbs
     *
     * @since 2.0
     * @author Gagans code wrapped to a function by Ilya K.
     */
    
    $scope.updateBreadcrumbs = function(id) {
        
        var item, parent = id, counter = 0;
        $scope.selectAncestors = [];
        while(parent > 0 && counter < 100) {
            counter++;
            item = $scope.findComponentItem($scope.componentsTree.children, parent, $scope.getComponentItem);
            $scope.selectAncestors.push({id: (parent === id ? 0 : parent), name: $scope.calcDefaultComponentTitle(item, true), tag: item.name});
            parent = item.options['ct_parent'];
        }
        if($scope.selectAncestors.length > 1) {
            $scope.selectAncestors.reverse();
        }
    }


    /**
     * Show adjusted outlines, overlays and titlebar
     *
     * @since 2.0
     * @author Ilya K.
     */

    $scope.adjustResizeBox = function() {

        // don't show in content edit mode
        if ( $parentScope.actionTabs["contentEditing"] ) {
            return false;
        }

        if ($scope.log) {
            console.log("adjustResizeBox()");
            $scope.functionStart("adjustResizeBox()");
        }

        var resizeBox = jQuery("#oxygen-resize-box");

        var timeout = $timeout(function() {

            var id          = $scope.component.active.id,
                parentId    = $scope.component.active.parent.id,
                name        = $scope.component.active.name;

            if (id<=0||name=="ct_paragraph"||name=="ct_ul"||name=="ct_li"||name=="ct_selector"||name=="ct_reusable") {
                resizeBox.hide();
                return false;
            }

            resizeBox.show();
            jQuery(".rb").hide();

            var sectionOffset   = 0,
                component       = $scope.getComponentById(id),
                parent          = $scope.getComponentById(parentId),
                offset          = component.offset(),
                height          = component.outerHeight(),
                iframeHeight    = $parentScope.artificialViewport.outerHeight(),
                width           = component.outerWidth(),
                bodyWidth       = jQuery('body').outerWidth(),
                parentOffset    = parent.offset(),
                parentHeight    = parent.outerHeight(),
                parentWidth     = parent.outerWidth();

            if (name=="ct_section") {
                sectionOffset = 2;
                component = jQuery('.ct-inner-wrap',component);
            }
            
            var marginHeight    = component.outerHeight(true),
                marginWidth     = component.outerWidth(true),

                minHeight       = ( parseInt(height) < 30 ) ? parseInt(height)/3 : 10,
                minWidth        = ( parseInt(width) < 30 ) ? parseInt(width)/3 : 10,

                marginTop       = ( parseInt(component.css("margin-top"))       > minHeight ) ? parseInt(component.css("margin-top")) : minHeight,
                marginRight     = ( parseInt(component.css("margin-right"))     > minWidth ) ? parseInt(component.css("margin-right")) : minWidth,
                marginBottom    = ( parseInt(component.css("margin-bottom"))    > minHeight ) ? parseInt(component.css("margin-bottom")) : minHeight,
                marginLeft      = ( parseInt(component.css("margin-left"))      > minWidth ) ? parseInt(component.css("margin-left")) : minWidth,

                paddingTop      = ( parseInt(component.css("padding-top"))      > minHeight ) ? parseInt(component.css("padding-top")) : minHeight,
                paddingRight    = ( parseInt(component.css("padding-right"))    > minWidth ) ? parseInt(component.css("padding-right")) : minWidth,
                paddingBottom   = ( parseInt(component.css("padding-bottom"))   > minHeight ) ? parseInt(component.css("padding-bottom")) : minHeight,
                paddingLeft     = ( parseInt(component.css("padding-left"))     > minWidth ) ? parseInt(component.css("padding-left")) : minWidth;

            // Margin overlays
            if ( ! $parentScope.isActiveName("ct_section") ) {
                $scope.rbMarginTop.css({
                    display: "block",
                    width: width - 1,
                    top: offset.top - marginTop,
                    left: offset.left - 1,
                    height: marginTop
                })
                $scope.rbMarginBottom.css({
                    display: "block",
                    width: width - 1,
                    top: offset.top + height,
                    left: offset.left - 1,
                    height: marginBottom
                })

                if (! $parentScope.isActiveName("ct_headline") &&
                    ! $parentScope.isActiveName("ct_text_block") ) {

                    if ( bodyWidth - width - parseInt(component.css("margin-right")) > minWidth) {
                        $scope.rbMarginLeft.css({
                            display: "block",
                            width: marginLeft,
                            top: offset.top - 1,
                            left: offset.left - marginLeft,
                            height: height
                        })
                    }
                    if ( bodyWidth - width - parseInt(component.css("margin-left")) > minWidth) {
                        $scope.rbMarginRight.css({
                            display: "block",
                            width: marginRight,
                            top: offset.top - 1,
                            left: offset.left + width,
                            height: height
                        })
                    }
                }
            }

            // Padding overlays
            if (! $parentScope.isActiveName("oxy_nav_menu") &&
                ! $parentScope.isActiveName("ct_headline") &&
                ! $parentScope.isActiveName("ct_text_block") &&
                ! $parentScope.isActiveName("ct_link_text") &&
                ! $parentScope.isActiveName("ct_image") &&
                ! $parentScope.isActiveName("ct_svg_icon") &&
                ! $parentScope.isActiveName("ct_fancy_icon") ) {

                $scope.rbPaddingTop.css({
                    display: "block",
                    width: width,
                    top: offset.top,
                    left: offset.left,
                    height: paddingTop
                })
                $scope.rbPaddingBottom.css({
                    display: "block",
                    width: width,
                    top: offset.top + height - paddingBottom,
                    left: offset.left,
                    height: paddingBottom
                })
                
                if ( ! $parentScope.isActiveName("ct_section") ) {

                    $scope.rbPaddingLeft.css({
                        display: "block",
                        width: paddingLeft,
                        top: offset.top + paddingTop,
                        left: offset.left,
                        height: height - paddingTop - paddingBottom
                    })
                    $scope.rbPaddingRight.css({
                        display: "block",
                        width: paddingRight,
                        top: offset.top + paddingTop,
                        left: offset.left + width - paddingRight,
                        height: height - paddingTop - paddingBottom
                    })
                }
            }

            // Outlines. DO NOT delete, we might use this in future
            /*$scope.rbTop.css({
                display: "block",
                width: width + 4 - sectionOffset*2,
                top: offset.top - 2 + sectionOffset,
                left: Math.round(offset.left - 2 + sectionOffset)
            })
            $scope.rbLeft.css({
                display: "block",
                height: height + 4 - sectionOffset*2,
                top: offset.top - 2 + sectionOffset,
                left: Math.round(offset.left - 2 + sectionOffset)
            })
            $scope.rbRight.css({
                display: "block",
                height: height + 4 - sectionOffset*2,
                top: offset.top - 2 + sectionOffset,
                left: Math.round(offset.left + width - sectionOffset)
            })
            $scope.rbBottom.css({
                display: "block",
                width: width + 4 - sectionOffset*2,
                top: offset.top + height - sectionOffset,
                left: Math.round(offset.left - 2 + sectionOffset)
            })*/

            // Titlebar

            // adjust section offset
            var innerWrapMargin = 0;
            if (name=="ct_section") {
                innerWrapMargin = parseFloat(component.css('marginLeft'));
            }

            var outlineOffset       = parseFloat(component.css('outlineOffset')),
                outlineWidth        = parseFloat(component.css('outlineWidth')),
                parentOutlineOffset = parseFloat(parent.css('outlineOffset')),
                parentOutlineWidth  = parseFloat(parent.css('outlineWidth'));

            if (offset.top >= 30) {
                $scope.rbTitleBar.css({
                    display: "",
                    top: offset.top - outlineOffset - $scope.rbTitleBar.height(),
                    left: offset.left - outlineOffset + innerWrapMargin - outlineWidth
                })
            }
            else {
                $scope.rbTitleBar.css({
                    display: "",
                    top: offset.top + outlineOffset + outlineWidth + height,
                    left: offset.left - outlineOffset + innerWrapMargin - outlineWidth
                })
            }

            if ( height > iframeHeight - 60 ) {
                $scope.rbTitleBar.css({
                    top: offset.top + outlineOffset + height - $scope.rbTitleBar.height(),
                })
            }

            // Parent Titlebar
            if ($scope.globalSettings.indicateParents=='true' &&
                !$parentScope.isActiveParentName("ct_inner_content") && 
                !$parentScope.isActiveParentName("oxy_header_left") && 
                !$parentScope.isActiveParentName("oxy_header_center") && 
                !$parentScope.isActiveParentName("oxy_header_right")) {
                
                if (parentId>0) {
                    
                    $scope.rbParentTitleBar
                    .css({
                        display: "" // set display option first, so the width can be calculated
                    });

                    var left = parentOffset.left + parentWidth + parentOutlineOffset + parentOutlineWidth - $scope.rbParentTitleBar.width();

                    if ( left + $scope.rbParentTitleBar.width() > bodyWidth ) {
                        left = bodyWidth - $scope.rbParentTitleBar.width();
                    }

                    $scope.rbParentTitleBar
                    .css({    
                        top: parentOffset.top + parentHeight + parentOutlineOffset + parentOutlineWidth,
                        left: left
                    })
                    if ( parentHeight > iframeHeight - 60 ) {
                        $scope.rbParentTitleBar.css({
                            top: parentOffset.top + parentHeight + parentOutlineOffset - $scope.rbParentTitleBar.height(),
                        })
                    }
                }
                else {
                    $scope.rbParentTitleBar.hide();
                }
            }
            else {
                $scope.rbParentTitleBar.hide();
            }

            // cancel timeout
            $timeout.cancel(timeout);
            $scope.functionEnd("adjustResizeBox()");
        }, 0, false);
    }

    
    /**
     * Hide Resize box
     *
     * @since 2.0
     * @author Ilya K.
     */

    $scope.hideResizeBox = function(timeout) {
        
        jQuery("#oxygen-resize-box").hide(timeout);
    }


    /**
     * Hide Title bars
     *
     * @since 2.0
     * @author Ilya K.
     */

    $scope.hideTitleBars = function(titleBar, parentTitleBar) {

        if (titleBar===true) {
            $scope.rbTitleBar.hide();
        }

        if (parentTitleBar===true) {
            $scope.rbParentTitleBar.hide();
        }
    }


    /**
     * Change Title bars visibility
     *
     * @since 2.0
     * @author Ilya K.
     */

    $scope.titleBarsVisibility = function(titleBar, parentTitleBar) {

        if (titleBar) {
            $scope.rbTitleBar.css('visibility',titleBar);
        }

        if (parentTitleBar) {
            $scope.rbParentTitleBar.css('visibility',parentTitleBar);
        }
    }


    /**
     * Highlight resize box elements if neeeded
     *
     * @since 2.0
     * @author Ilya K.
     */

    $scope.checkResizeBoxOptions = function (optionName) {

        if (["margin-top","margin-right","margin-bottom","margin-left",
             "padding-top","padding-right","padding-bottom","padding-left"].indexOf(optionName)>-1) 
        {
            jQuery("#rb-"+optionName).addClass("rb-currently-editing");
            clearTimeout($scope.resizeBoxTimeout);
            $scope.resizeBoxTimeout = setTimeout(function(){
                jQuery("#rb-"+optionName).removeClass("rb-currently-editing");
            }, 400);
        }
    }


    /**
     * Update current active parent
     *
     * @since 0.1.5
     */

    $scope.updateCurrentActiveParent = function(key, item) {

        $scope.component.active.parent.id   = item.id;
        $scope.component.active.parent.name = item.name;
    }


    /**
     * Disable elememnt draggable
     *
     * @author Jason
     */

    $scope.disableElementDraggable = function(val) {
        $scope.isDesiableDraggable = val;
    }

    $scope.getDragDisabledState = function() {
        return $scope.isDesiableDraggable;
    }


    /**
     * Update current active parent
     *
     * @since 0.1.5
     */

    $scope.isActiveParent = function(name) {

        return ($scope.component.active.parent.name == name) ? true : false;
    }

    /**
     * Get list of component to allow the drag and drop
     *
     * @author Jason
     */
    $scope.getDNDAllowedTypes = function (componentName) {

        //-----
        // Specifying Draggable element
        //-----
        allowedTypes = [];
        switch (componentName) {
            case 'ct-builder':
                allowedTypes = [
                                'ct_section', 'ct_div_block', 'ct_new_columns', 'ct_reusable', 'ct_link', 'ct_slider', 'oxy_header',
                                'ct_inner_content'
                               ];
                break;

            case 'ct_section':
                allowedTypes = [
                                'ct_headline', 'ct_image', 'ct_text_block', 'oxy_rich_text', 'ct_link_text', 'ct_svg_icon',
                                'ct_div_block',
                                'ct_new_columns',
                                'ct_data_comment_form',
                                'ct_video',
                                'ct_link_button',
                                'ct_fancy_icon',
                                'ct_reusable',
                                'ct_shortcode',
                                'ct_widget',
                                'ct_code_block',
                                'ct_link', 
                                'ct_slider',
                                'oxy_social_icons',
                                'oxy_nav_menu',
                                'oxy_map',
                                'oxy_soundcloud',
                                'oxy_posts_grid',
                                'oxy_comments',
                                'oxy_comment_form',
                                'ct_inner_content',
                                'oxy_testimonial',
                                'oxy_icon_box',
                                'oxy_progress_bar',
                                'oxy_pricing_box',
                                'oxy_tabs',
                                'oxy_tabs_contents',
                                'oxy_superbox',
                                'oxy_login_form',
                                'oxy_search_form',
                                'oxy_toggle',
                                'oxy_gallery'
                               ];
                break;

            case 'ct_div_block':
            case 'ct_inner_content':
            case 'oxy_toggle':
                    allowedTypes = [
                                'ct_headline', 'ct_image', 'ct_text_block', 'oxy_rich_text', 'ct_link_text', 'ct_svg_icon',
                                'ct_section', 'ct_div_block',
                                'ct_new_columns',
                                'ct_data_comment_form',
                                'ct_video',
                                'ct_link_button',
                                'ct_fancy_icon',
                                'ct_reusable',
                                'ct_shortcode',
                                'ct_widget',
                                'ct_code_block',
                                'ct_link', 
                                'ct_slider',
                                'oxy_social_icons',
                                'oxy_nav_menu',
                                'oxy_map',
                                'oxy_header',
                                'oxy_soundcloud',
                                'oxy_posts_grid',
                                'oxy_comments',
                                'oxy_comment_form',
                                'ct_inner_content',
                                'oxy_testimonial',
                                'oxy_icon_box',
                                'oxy_progress_bar',
                                'oxy_pricing_box',
                                'oxy_tabs',
                                'oxy_tabs_contents',
                                'oxy_superbox',
                                'oxy_login_form',
                                'oxy_search_form',
                                'oxy_toggle',
                                'oxy_gallery'
                               ];
                break;

            case 'oxy_icon_box':
            case 'oxy_pricing_box':
                    allowedTypes = [
                                'ct_headline', 'ct_image', 'ct_text_block', 'oxy_rich_text', 'ct_link_text', 'ct_svg_icon',
                                'ct_div_block',
                                'ct_data_comment_form',
                                'ct_video',
                                'ct_link_button',
                                'ct_fancy_icon',
                                'ct_reusable',
                                'ct_shortcode',
                                'ct_widget',
                                'ct_code_block',
                                'ct_link', 
                                'oxy_social_icons',
                                'oxy_nav_menu',
                                'oxy_map',
                                'oxy_soundcloud',
                                'oxy_posts_grid',
                                'oxy_comments',
                                'oxy_comment_form',
                                'ct_inner_content',
                                'oxy_testimonial',,
                                'oxy_progress_bar',
                                'oxy_tabs',
                                'oxy_tabs_contents',
                                'oxy_superbox',
                                'oxy_login_form',
                                'oxy_search_form',
                                'oxy_toggle',
                                'oxy_gallery'
                               ];
                break;

            case 'oxy_header':
                allowedTypes = [
                                'oxy_header_row',
                               ];
                break;

            case 'oxy_header_right':
            case 'oxy_header_center':
            case 'oxy_header_left':
                allowedTypes = [
                                'ct_headline', 'ct_image', 'ct_text_block', 'oxy_rich_text', 'ct_link_text', 'ct_svg_icon',
                                'ct_div_block',
                                'ct_new_columns',
                                'ct_data_comment_form',
                                'ct_video',
                                'ct_link_button',
                                'ct_fancy_icon',
                                'ct_reusable',
                                'ct_shortcode',
                                'ct_widget',
                                'ct_code_block',
                                'ct_link', 
                                'ct_slider',
                                'oxy_social_icons',
                                'oxy_nav_menu',
                                'oxy_testimonial',
                                'oxy_icon_box',
                                'oxy_progress_bar',
                                'oxy_pricing_box',
                                'oxy_tabs',
                                'oxy_tabs_contents',
                                'oxy_superbox',
                                'oxy_toggle'
                               ];
                break;

            case 'ct_slide':
                allowedTypes = [
                                'ct_headline', 'ct_image', 'ct_text_block', 'oxy_rich_text', 'ct_link_text', 'ct_svg_icon',
                                'ct_div_block',
                                'ct_new_columns',
                                'ct_data_comment_form',
                                'ct_video',
                                'ct_link_button',
                                'ct_fancy_icon',
                                'ct_reusable',
                                'ct_shortcode',
                                'ct_widget',
                                'ct_code_block',
                                'ct_link', 
                                'oxy_social_icons',
                                'oxy_nav_menu',
                                'oxy_map',
                                'oxy_soundcloud',
                                'oxy_posts_grid',
                                'oxy_comments',
                                'oxy_comment_form',
                                'ct_inner_content',
                                'oxy_testimonial',
                                'oxy_icon_box',
                                'oxy_progress_bar',
                                'oxy_pricing_box',
                                'oxy_tabs',
                                'oxy_tabs_contents',
                                'oxy_superbox',
                                'oxy_login_form',
                                'oxy_search_form',
                                'oxy_toggle',
                                'oxy_gallery'
                               ];
                break;

            case 'ct_link':
                allowedTypes = [
                                'ct_headline', 'ct_image', 'ct_text_block', 'oxy_rich_text', 'ct_link_text', 'ct_svg_icon',
                                'ct_section', 'ct_div_block',
                                'ct_new_columns',
                                'ct_data_comment_form',
                                'ct_video',
                                'ct_link_button',
                                'ct_fancy_icon',
                                'ct_reusable',
                                'ct_shortcode',
                                'ct_widget',
                                'ct_code_block',
                                'ct_inner_content',
                                'oxy_superbox',
                                'oxy_login_form',
                                'oxy_search_form',
                                'oxy_toggle',
                                'oxy_gallery'
                               ];
                break;

            case 'ct_new_columns':
                allowedTypes = [
                                'ct_div_block'
                               ];
                break;

            case 'oxy_tabs':
                allowedTypes = [
                                'oxy_tab'
                               ];
                break;

            case 'oxy_tabs_contents':
                allowedTypes = [
                                'oxy_tab_content'
                               ];
                break;

            case 'oxy_tab':
            case 'oxy_tab_content':
                allowedTypes = [
                                'ct_headline', 'ct_image', 'ct_text_block', 'oxy_rich_text', 'ct_link_text', 'ct_svg_icon',
                                'ct_section', 'ct_div_block',
                                'ct_new_columns',
                                'ct_data_comment_form',
                                'ct_video',
                                'ct_link_button',
                                'ct_fancy_icon',
                                'ct_reusable',
                                'ct_shortcode',
                                'ct_widget',
                                'ct_code_block',
                                'ct_link', 
                                'ct_slider',
                                'oxy_social_icons',
                                'oxy_nav_menu',
                                'oxy_map',
                                'oxy_header',
                                'oxy_soundcloud',
                                'oxy_posts_grid',
                                'oxy_comments',
                                'oxy_comment_form',
                                'ct_inner_content',
                                'oxy_testimonial',
                                'oxy_icon_box',
                                'oxy_progress_bar',
                                'oxy_pricing_box',
                                'oxy_superbox',
                                'oxy_login_form',
                                'oxy_search_form',
                                'oxy_toggle',
                                'oxy_gallery'
                               ];
                break;

        }

        return allowedTypes;

    }

    $scope.trustedSource = function(source) {
        return $sce.trustAsResourceUrl(source);
    }

    $scope.trustedHTML = function(content) {
        return $sce.trustAsHtml(content);
    }

    /**
     * Get Component Tempalte based on its name
     * 
     * @since 0.1
     * @author Ilya K.
     * @author Jason
     */

    $scope.getComponentTemplate = function (componentName, id, type) {

        if ($scope.log) {
            console.log("getComponentTemplate()", componentName, id, type);
        }

        var options   = 'ng-mousedown="activateComponent('+id+ ', \''+componentName+'\', $event);" ' +
                        'ng-attr-component-id="'+id+'" ' + 
                        'ng-class="{\'ct-active\' : parentScope.isActiveId('+id+'),\'ct-active-parent\' : parentScope.isActiveParentId('+id+')&&globalSettings.indicateParents==\'true\'}" ' +
                        'id="{{component.options['+id+'].selector}}" ',

            classes     = 'class="{{getComponentsClasses('+id+', \''+componentName+'\')}}'+ ((type === 'data') ? " ct-data-component" : "") +'" ',

            template    = "";

            // Attributes for drag and drop
            dndListAttr      = 'dnd-list="" ' +
                               'dnd-allowed-types="getDNDAllowedTypes(\'' + componentName + '\')" ' +
                               'dnd-type="\'' + componentName + '\'" ' +
                               'dnd-dragover="dragoverCallback(\'{{component.options['+id+'].selector}}\', event, external, type)" ',

            dndListAttrHorizontal = 'dnd-horizontal-list="isHorizontal" ',
            
            dndDisableIf = '';
            if(!$scope.isChrome) {
                dndDisableIf = 'dnd-disable-if="getDragDisabledState()" ';
            }
 
            dndDraggableAttr = 'dnd-draggable="" ' +
                               dndDisableIf +
                               'dnd-effect-allowed="move" ' +
                               'dnd-type="\'' + componentName + '\'" ' +
                               'dnd-dragstart="dragstartCallback('+id+ ',\'{{component.options['+id+'].selector}}\', event)" ' +
                               'dnd-dragend="dragendCallback('+id+ ', \''+componentName+'\', event)" ';

        // If component is Re-usable, dndDraggableAttr will be overwritten.
        if(componentName == "ct_reusable") {
            dndDraggableAttr = 'dnd-draggable="" ' +
                           dndDisableIf +
                           'dnd-effect-allowed="move" ' +
                           'dnd-type="\'{{component.options['+id+'].dndtype}}\'" ' +
                           'dnd-dragstart="dragstartCallback('+id+ ',\'{{component.options['+id+'].selector}}\', event)" ' +
                           'dnd-dragend="dragendCallback('+id+ ', \''+componentName+'\', event)" ';
        }

        // remove dnd attributes for outer content excluding the Inner Content
        if (id >= 100000 && componentName != "ct_inner_content") {
            dndListAttr = ""; 
            dndListAttrHorizontal = "";
            dndDraggableAttr = "";
        }

        // we can't drag into Inner Content if editing template
        if (componentName == "ct_inner_content" && CtBuilderAjax.oxyTemplate) {
            dndListAttr = ""; 
            dndListAttrHorizontal = "";
        }

        // we can't drag the Inner Content if not editing template
        if (componentName == "ct_inner_content" && !CtBuilderAjax.oxyTemplate) {
            dndDraggableAttr = "";
        }

        // remove dnd attributes for builtin components
        if (type=='builtin') {
            //dndListAttr = ""; 
            //dndListAttrHorizontal = "";
            dndDraggableAttr = "";
        }

        if (type != "shortcode" && type != "widget" && type != "sidebar" && type != "nav_menu" && type != "data") {

            switch (componentName) {

                case 'ct_section':

                    classes = 'class="ct-section {{getComponentsClasses('+id+', \''+componentName+'\')}}" ';

                    template = '<div is-nestable="true" ' + options + classes + dndDraggableAttr + '>' +
                                    '<div class="oxy-video-container">' +
                                        '<video autoplay loop playsinline muted>' +
                                            '<source ng-src="{{trustedSource(component.options['+id+'][\'model\'][\'video_background\'])}}">' +
                                        '</video>' +
                                        '<div class="oxy-video-overlay"></div>' +
                                    '</div>' +
                                    '<div class="ct-section-inner-wrap ct-inner-wrap" ' + dndListAttr + dndListAttrHorizontal + '>' +
                                    '</div>' +
                                '</div>';
                    
                    break

                case 'ct_columns':

                    classes = 'class="ct-columns {{getComponentsClasse('+id+', \''+componentName+'\')}}" ';

                    template = '<div ' + options + classes + '>' +
                                    '<div class="ct-columns-inner-wrap ct-inner-wrap" ng-class="checkEmptyColumns('+id+')"></div>'+
                                '</div>';

                    break

                case 'ct_column': 

                    classes = 'class="ct-column {{getComponentsClasses('+id+', \''+componentName+'\')}}" ';
                    
                    template = '<div is-nestable="true" ' + options + classes + '></div>';
                    break

                case 'ct_headline':

                    var tag = $scope.component.options[id]['model'].tag;

                    template = '<'+tag+' contenteditable="false" ng-model="component.options['+id+'][\'model\'].ct_content" ng-model-options="{ debounce: 10 }" ' + options + classes + dndDraggableAttr +'></'+tag+'>';
                    break

                case 'ct_image':
                    
                    var ngsrc = 'ng-src="{{component.options['+id+'][\'model\'].src}}" ';

                    if($scope.component.options[id]['model'].src.indexOf('[oxygen') > -1) {
                        ngsrc = '';
                    }

                    template = '<img '+ngsrc + options + classes + dndDraggableAttr + ' oxyimageonload/>';
                    break

                case 'ct_video':

                    template = '<div '+ options + classes + dndDraggableAttr + '>';
                    template += '<div ng-if="component.options['+id+'][\'model\'][\'use-custom\'] !== \'1\'" class="oxygen-vsb-responsive-video-wrapper"><iframe src="{{trustedSource(component.options['+id+'][\'model\'].embed_src)}}" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe></div>';
                    template += '<div ng-if="component.options['+id+'][\'model\'][\'use-custom\'] === \'1\'" class="oxygen-vsb-responsive-video-wrapper oxygen-vsb-responsive-video-wrapper-custom" ng-bind-html="trustedHTML(component.options['+id+'][\'model\'][\'custom-code\'])"></div>';
                    template += '</div>';
                    break

                case 'ct_text_block':

                    template = '<div contenteditable="false" ng-model="component.options['+id+'][\'model\'].ct_content" ng-model-options="{ debounce: 10 }" ' + options + classes + dndDraggableAttr + '></div>';
                    break

                case 'ct_paragraph':

                    template = '<div ng-attr-paragraph="true" contenteditable="false" ng-model="component.options['+id+'][\'model\'].ct_content" ng-model-options="{ debounce: 10 }" ' + options + classes + '></div>';
                    break

                case 'ct_div_block':

                    template = '<div is-nestable="true" ' + options + classes + dndDraggableAttr + dndListAttr + dndListAttrHorizontal + '>' +
                                '</div>';
                    break

                case 'ct_new_columns':

                    template = '<div ' + options + classes + dndListAttr + dndListAttrHorizontal + dndDraggableAttr + '>' +
                                '</div>';
                    break

                case 'ct_ul':

                    template = '<ul is-nestable="true" ' + options + classes + '></ul>';
                    break

                case 'ct_li':

                    template = '<li contenteditable="false" ng-model="component.options['+id+'][\'model\'].ct_content" ng-model-options="{ debounce: 10 }"' + options + classes + '></li>';
                    break

                case 'ct_span':

                    var item = $scope.findComponentItem($scope.componentsTree.children, id, $scope.getComponentItem);
                    var parent = $scope.findComponentItem($scope.componentsTree.children, item['options']['ct_parent'], $scope.getComponentItem);

                    //TODO: figure out if the span encloses an oxy shortcode

                    var oxyContent = false,
                    matches = item['options']['ct_content'].match(/\[oxygen[^\]]*\]/i);

                    if(matches) {
                        oxyContent = matches[0];
                    }

                    if(oxyContent && (parent.name === 'ct_headline' ||
                        parent.name === 'ct_text_block' ||
                        parent.name === 'ct_paragraph' ||
                        parent.name === 'ct_li' ||
                        parent.name === 'ct_link_text' ||
                        parent.name === 'oxy_icon_box' ||
                        parent.name === 'oxy_testimonial' ||
                        parent.name === 'ct_link_button')) {

                        classes = 'class="ct-contains-oxy {{getComponentsClasses('+id+', \''+componentName+'\')}}" ';
                        template = '<span ng-attr-span="true" ctrenderoxyshortcode ng-model="component.options['+id+'][\'model\'].ct_content" ng-model-options="{ debounce: 10 }" ' + 'ng-attr-component-id="'+id+'" ' + 
                        'ng-class="{\'ct-active\' : parentScope.isActiveId('+id+'),\'ct-active-parent\' : parentScope.isActiveParentId('+id+')&&globalSettings.indicateParents==\'true\'}" ' +
                        'id="{{component.options['+id+'].selector}}" ' + classes + '></span>';
                    }
                    else {
                        template = '<span ng-attr-span="true" contenteditable="false" ng-model="component.options['+id+'][\'model\'].ct_content" ng-model-options="{ debounce: 10 }" ' + options + classes + '></span>';
                    }
                    
                    break

                case 'ct_link_text':

                    template = '<a href="" contenteditable="false" ng-model="component.options['+id+'][\'model\'].ct_content" ng-model-options="{ debounce: 10 }" ' + options + classes + dndDraggableAttr + '></a>';
                    break

                case 'ct_link_button':

                    template = '<a href="" contenteditable="false" ng-model="component.options['+id+'][\'model\'].ct_content" ng-model-options="{ debounce: 10 }" ' + options + classes + dndDraggableAttr + '></a>';
                    break

                case 'ct_link':

                    template = '<a href="" ' + options + classes + dndDraggableAttr + dndListAttr + dndListAttrHorizontal + 'id="{{component.options['+id+'].selector}}" is-nestable="true" ng-attr-component-id="' + id + '"></a>';
                    break

                case 'ct_svg_icon':
                    classes = 'class="svg_wrapper {{getComponentsClasses('+id+', \''+componentName+'\')}}" ';
                    template = '<div ' + options + classes + dndDraggableAttr +'><svg><use xlink:href="" ng-href="{{\'#\'+component.options['+id+'][\'model\'][\'icon-id\']}}"></use></svg></div>';
                    //template = '<div class="svg_wrapper" ' + dndDraggableAttr + '><svg ' + options + classes + '><use xlink:href="" ng-href="{{\'#\'+component.options['+id+'][\'model\'][\'icon-id\']}}"></use></svg></div>';
                    break

                case 'ct_fancy_icon':
                    
                    classes = 'class="svg_wrapper {{getComponentsClasses('+id+', \''+componentName+'\')}}" ';

                    template = '<div ' + options + classes + dndDraggableAttr +'><svg id="svg-{{component.options['+id+'].selector}}"><use xlink:href="" ng-href="{{\'#\'+component.options['+id+'][\'model\'][\'icon-id\']}}"></use></svg></div>';
                    break

                case 'ct_reusable':

                    template = '<div ' + options + classes + dndDraggableAttr +'></div>';
                    break

                case 'ct_separator':

                    template = '<div ' + options + classes + '></div>';
                    break

                case 'ct_code_block':

                    var tag = $scope.component.options[id]['model'].tag;

                    template = '<'+tag+' ' + options + classes + dndDraggableAttr + '></'+tag+'>';
                    var timeout = $timeout(function() {
                        $scope.applyCodeBlock(id, false);
                        
                        // cancel timeout
                        $timeout.cancel(timeout);
                    }, 0, false);
                    break

                case 'ct_inner_content':
                    var workArea = '';
                    if(id >= 100000) {
                        workArea = ' ct-inner-content-workarea ';
                    }
                    classes = 'class="ct-inner-content'+workArea+' {{getComponentsClasses('+id+', \''+componentName+'\')}}" ';

                   // var placeholder = '<div class="ct-inner-content-placeholder">Page/Post specific content</div>';
                    template = '<div is-nestable="true" ' + 
                        'ng-mousedown="activateComponent('+id+ ', \''+componentName+'\', $event);" ' +
                        'ng-attr-component-id="'+id+'" ' + 
                        'ng-class="{\'ct-active\' : parentScope.isActiveId('+id+')}" ' +
                        'id="{{component.options['+id+'].selector}}" ' + classes + dndDraggableAttr + dndListAttr + '></div>';

                    if(id < 100000) {
                        var timeout = $timeout(function() {
                            if($scope.template.postsList || $scope.template.termsList) {
                                $scope.renderInnerContent(id, componentName);
                            }
                            // cancel timeout
                            $timeout.cancel(timeout);
                        }, 0, false);
                    }
                    break

                case 'oxy_header':

                    classes = 'class="oxy-header-wrapper {{getComponentsClasses('+id+', \''+componentName+'\')}}" ';
                    template = '<section ' + options + classes + dndDraggableAttr + dndListAttr + '>' +
                                '</section>';
                    break

                case 'oxy_header_row':

                    classes = 'class="oxy-header-row {{getComponentsClasses('+id+', \''+componentName+'\')}}" ';
                    template = '<div ' + options + classes + dndDraggableAttr +' dnd-disable-if="isLastRow('+id+')">' +
                                    '<div class="oxy-header-container ct-inner-wrap" ng-class="checkEmptyHeaderRow('+id+')">' +
                                    '</div>' +
                               '</div>';
                    break

                case 'oxy_header_left':
                case 'oxy_header_center':
                case 'oxy_header_right':

                    template = '<div ' + dndListAttr + 'is-nestable="true" ng-attr-component-id="'+id+'" id="{{component.options['+id+'].selector}}" ' +
                               'ng-class="{\'ct-active\':parentScope.isActiveId('+id+'),\'ct-active-parent\':parentScope.isActiveParentId('+id+')&&globalSettings.indicateParents==\'true\'}" ' + classes + '></div>';
                    break

                case 'ct_slider':

                    classes = 'class="ct-slider {{getComponentsClasses('+id+', \''+componentName+'\')}}" ';
                    template = '<div is-nestable="true" ' + options + classes + dndDraggableAttr +'><div class="oxygen-unslider-container" oxygen-slider="true"><ul></ul></div></div>';
                    break

                case 'ct_slide':

                    template = '<li><div is-nestable="true" ' + options + classes + dndListAttr + dndListAttrHorizontal +'></div></li>';
                    break

                case 'oxy_map':

                    template = '<div ' + options + classes + dndDraggableAttr + '><iframe ng-src="{{getMapURL('+id+')}}" frameborder=0></iframe></div>';
                    break

                case 'oxy_social_icons':
                        
                    var html = "";
                        
                    for(var key in $scope.socialIcons.networks) { 
                        if ($scope.socialIcons.networks.hasOwnProperty(key)) {
                            var network = $scope.socialIcons.networks[key];
                            if ($scope.component.options[id]['model']['icon-'+network]) {
                                html += "<a href='' class='oxy-social-icons-"+network+"'><svg><use xlink:href=\"\" ng-href=\"#oxy-social-icons-icon-"+network+"{{component.options["+id+"]['model']['icon-style']=='blank'?'-blank':''}}\"></use></svg></a>";
                            }
                        }
                    }

                    template = '<div ' + options + classes + dndDraggableAttr + '>' + html + '</div>';
                    break

                case 'oxy_soundcloud':

                    template = '<div ' + options + classes + dndDraggableAttr + '><iframe width="100%" height="{{component.options['+id+"]['model']['height']+component.options["+id+"]['model']['height-unit']}}\" ng-src=\"{{getSoundCloudURL("+id+")}}\" frameborder=0></iframe></div>";
                    break

                case 'oxy_posts_grid':

                    template = '<div class="oxy-easy-posts oxy-posts-grid" ' + options + dndDraggableAttr + '></div>';
                    var timeout = $timeout(function() {
                        $scope.renderComponentWithAJAX('oxy_render_easy_posts', id);
                        
                        // cancel timeout
                        $timeout.cancel(timeout);
                    }, 0, false);
                    break

                 case 'oxy_comments':

                    template = '<div ' + options + classes + dndDraggableAttr + '></div>';
                    var timeout = $timeout(function() {
                        $scope.renderComponentWithAJAX('oxy_render_comments_list', id);
                        
                        // cancel timeout
                        $timeout.cancel(timeout);
                    }, 0, false);
                    break

                case 'oxy_comment_form':

                    template = '<div ' + options + classes + dndDraggableAttr + '></div>';
                    var timeout = $timeout(function() {
                        $scope.renderComponentWithAJAX('oxy_render_comment_form', id);
                        
                        // cancel timeout
                        $timeout.cancel(timeout);
                    }, 0, false);
                    break

                case 'oxy_login_form':

                    template = '<div ' + options + classes + dndDraggableAttr + '></div>';
                    var timeout = $timeout(function() {
                        $scope.renderComponentWithAJAX('oxy_render_login_form', id);
                        
                        // cancel timeout
                        $timeout.cancel(timeout);
                    }, 0, false);
                    break

                case 'oxy_search_form':

                    template = '<div ' + options + classes + dndDraggableAttr + '></div>';
                    var timeout = $timeout(function() {
                        $scope.renderComponentWithAJAX('oxy_render_search_form', id);
                        
                        // cancel timeout
                        $timeout.cancel(timeout);
                    }, 0, false);
                    break

                case 'oxy_gallery':

                    template = '<div ' + options + classes + dndDraggableAttr + '></div>';
                    var timeout = $timeout(function() {
                        $scope.renderComponentWithAJAX('oxy_render_gallery',id);
                        
                        // cancel timeout
                        $timeout.cancel(timeout);
                    }, 0, false);
                    break

                case 'oxy_rich_text':

                    template = '<div ng-dblclick="parentScope.openTinyMCEDialog()"' + options + classes + dndDraggableAttr + 'ng-bind-html="trustedHTML(component.options['+id+"]['model']['ct_content'])\">" + '</div>';
                    break

                case 'oxy_testimonial':
                        
                    template = 
                    '<div ' + options + classes + dndDraggableAttr + '>'+
                        '<div class="oxy-testimonial-photo-wrap">'+
                           '<img class="oxy-testimonial-photo" ng-src="{{component.options['+id+'][\'model\'].testimonial_photo}}" oxyimageonload/>'+
                        '</div>'+
                        '<div class="oxy-testimonial-content-wrap">'+
                            '<div class="oxy-testimonial-text" contenteditable="false" data-optionname="testimonial_text" ng-model="component.options['+id+'][\'model\'].testimonial_text" ng-model-options="{ debounce: 10 }" ></div>' +
                            '<div class="oxy-testimonial-author-wrap">'+
                                '<div class="oxy-testimonial-author" contenteditable="false" data-optionname="testimonial_author" ng-model="component.options['+id+'][\'model\'].testimonial_author" ng-model-options="{ debounce: 10 }" ></div>' +
                                '<div class="oxy-testimonial-author-info" contenteditable="false" data-optionname="testimonial_author_info" ng-model="component.options['+id+'][\'model\'].testimonial_author_info" ng-model-options="{ debounce: 10 }" ></div>' +
                            '</div>'+
                        '</div>'+
                    '</div>';

                    break

                 case 'oxy_icon_box':

                    // oxy-icon-box class should be there from very start to proper nesting into ct-inner-wrap
                    classes = 'class="oxy-icon-box {{getComponentsClasses('+id+', \''+componentName+'\')}}" ';
                    
                    template = 
                    '<div is-nestable="true" ' + options + classes + dndDraggableAttr + dndListAttr + dndListAttrHorizontal + '>'+
                        '<div class="oxy-icon-box-icon oxy-builtin-container">'+
                        '</div>'+
                        '<div class="oxy-icon-box-content">'+
                            '<h2 class="oxy-icon-box-heading" contenteditable="false" data-optionname="icon_box_heading" ng-model="component.options['+id+'][\'model\'].icon_box_heading" ng-model-options="{ debounce: 10 }" ></h2>' +
                            '<p class="oxy-icon-box-text" contenteditable="false" data-optionname="icon_box_text" ng-model="component.options['+id+'][\'model\'].icon_box_text" ng-model-options="{ debounce: 10 }" ></p>' +
                            '<div ' + dndListAttr + dndListAttrHorizontal + 'class="oxy-icon-box-link ct-inner-wrap">'+
                            '</div>'+
                        '</div>'+
                    '</div>';

                    break
                    
                case 'oxy_progress_bar':
                    
                    template = '<div ' + options + classes + dndDraggableAttr + '>'+
                      '<div class="oxy-progress-bar-background">'+
                        '<div class="oxy-progress-bar-progress-wrap">'+
                          '<div class="oxy-progress-bar-progress">'+
                            '<div class="oxy-progress-bar-overlay-text" contenteditable="false" data-optionname="progress_bar_left_text" ng-model="component.options['+id+'][\'model\'].progress_bar_left_text" ng-model-options="{ debounce: 10 }"></div>'+
                            '<div class="oxy-progress-bar-overlay-percent" contenteditable="false" data-optionname="progress_bar_right_text" ng-model="component.options['+id+'][\'model\'].progress_bar_right_text" ng-model-options="{ debounce: 10 }">'+
                            '</div>'+
                          '</div>'+
                        '</div>'+
                      '</div>'+
                    '</div>';

                    break

                case 'oxy_pricing_box':

                    // oxy-icon-box class should be there from very start to proper nesting into ct-inner-wrap
                    classes = 'class="oxy-pricing-box {{getComponentsClasses('+id+', \''+componentName+'\')}}" ';

                    template = '<div is-nestable="true"' + options + classes + dndDraggableAttr + dndListAttr + dndListAttrHorizontal + '>'+

                        "<div class='oxy-pricing-box-graphic oxy-pricing-box-section oxy-builtin-container' ng-show='component.options["+id+"][\"model\"].pricing_box_include_graphic==\"yes\"'></div>" + 
                        "<div class='oxy-pricing-box-title oxy-pricing-box-section'>"+
                            '<div class="oxy-pricing-box-title-title" contenteditable="false" data-optionname="pricing_box_package_title" ng-model="component.options['+id+'][\'model\'].pricing_box_package_title" ng-model-options="{ debounce: 10 }"></div>'+
                            '<div class="oxy-pricing-box-title-subtitle" contenteditable="false" data-optionname="pricing_box_package_subtitle" ng-model="component.options['+id+'][\'model\'].pricing_box_package_subtitle" ng-model-options="{ debounce: 10 }"></div>'+
                        "</div>" +
                        '<div ng-if="component.options['+id+'][\'model\'].pricing_box_include_features!=\'no\'" class="oxy-pricing-box-content oxy-pricing-box-section" contenteditable="false" data-optionname="pricing_box_content" ng-model="component.options['+id+'][\'model\'].pricing_box_content" ng-model-options="{ debounce: 10 }"></div>'+
                        "<div class='oxy-pricing-box-price oxy-pricing-box-section'>" +
                            '<span class="oxy-pricing-box-sale-price" contenteditable="false" data-optionname="pricing_box_package_regular" ng-model="component.options['+id+'][\'model\'].pricing_box_package_regular" ng-model-options="{ debounce: 10 }"></span>'+
                            "<span class='oxy-pricing-box-amount'>" +
                                "<span class='oxy-pricing-box-currency'>{{component.options["+id+"][\'model\'].pricing_box_price_amount_currency}}</span>"+
                                "<span class='oxy-pricing-box-amount-main'>{{component.options["+id+"][\'model\'].pricing_box_price_amount_main}}</span>"+
                                "<span class='oxy-pricing-box-amount-decimal'>{{component.options["+id+"][\'model\'].pricing_box_price_amount_decimal}}</span>" +
                            "</span>" +
                            "<span class='oxy-pricing-box-term'>" +
                                "<span class='oxy-pricing-box-amount-term'>{{component.options["+id+"][\'model\'].pricing_box_price_amount_term}}</span>" +
                            "</span>" +
                        "</div>" +
                        '<div class="oxy-pricing-box-cta oxy-pricing-box-section ct-inner-wrap">'+
                        '</div>' +
                    "</div>";
                
                    break

                case 'oxy_tabs':
                    classes = 'class="oxy-tabs-wrapper {{getComponentsClasses('+id+', \''+componentName+'\')}}" ';
                    template = '<div is-nestable="true" ' + options + classes + dndDraggableAttr + dndListAttr + dndListAttrHorizontal + 
                                    'data-oxy-tabs-active-tab-class="{{component.options['+id+'][\'model\'].active_tab_class}}"' + 
                                    'data-oxy-tabs-contents-wrapper="{{component.options['+id+'][\'model\'].tabs_contents_wrapper}}"' + '>' +
                                '</div>';
                    break

                case 'oxy_tabs_contents':
                    classes = 'class="oxy-tabs-contents-wrapper {{getComponentsClasses('+id+', \''+componentName+'\')}}" ';
                    template = '<div is-nestable="true" ' + options + classes + dndDraggableAttr + dndListAttr + dndListAttrHorizontal + 
                               'data-oxy-tabs-wrapper="{{component.options['+id+'][\'model\'].tabs_wrapper}}"' + '>' +
                               '</div>';
                    break

                case 'oxy_tab':
                case 'oxy_tab_content':

                    template = '<div is-nestable="true" ' + options + classes + dndDraggableAttr + dndListAttr + dndListAttrHorizontal + '>' +
                                '</div>';
                    break

                case 'oxy_superbox':
                    template = '<div ' + options + classes + dndDraggableAttr + dndListAttr + dndListAttrHorizontal + '>' +
                                    '<div class="oxy-superbox-wrap oxy-builtin-container">'+ 
                                    '</div>' +
                               '</div>';
                    break

                case 'oxy_toggle':
                    classes = 'class="oxy-toggle {{getComponentsClasses('+id+', \''+componentName+'\')}}" ';
                    template = '<div is-nestable="true" ' + options + classes + dndDraggableAttr + dndListAttr + dndListAttrHorizontal + 
                                    'data-oxy-toggle-active-class="{{component.options['+id+'][\'model\'].toggle_active_class}}"' + 
                                    'data-oxy-toggle-target="{{component.options['+id+'][\'model\'].toggle_target}}"' + 
                                    'data-oxy-toggle-initial-state="{{component.options['+id+'][\'model\'].toggle_init_state}}"' + '>' +
                                    '<div class="oxy-expand-collapse-icon" href="#"></div>' +
                                    '<div class="oxy-toggle-content ct-inner-wrap">' +
                                    '</div>' +
                                '</div>';
                    break

                default: 
                    template = "<span>No Template found</span>"
                    //console.log(componentName);
            }
        }

        // shortcodes 
        else if ( type == "shortcode" ) {

            var tag = $scope.component.options[id]['model'].tag;

            template = '<'+tag+' ' + options + classes + dndDraggableAttr + '></'+tag+'>';
            
            var timeout = $timeout(function() {
                $scope.renderShortcode(id, componentName);
                
                // cancel timeout
                $timeout.cancel(timeout);
            }, 0, false);
        }

        // widgets
        else if ( type == "widget" ) {

            template = '<div ' + options + classes + dndDraggableAttr + '></div>';

            var timeout = $timeout(function() {
                $scope.renderWidget(id);
                
                // cancel timeout
                $timeout.cancel(timeout);
            }, 0, false);
        }

        // nav menu
        else if ( type == "nav_menu" ) {

            template = '<div ' + dndDraggableAttr + options + classes + '></div>';

            var timeout = $timeout(function() {
                $scope.renderNavMenu(id);
                
                // cancel timeout
                $timeout.cancel(timeout);
            }, 0, false);
        }

        // data
        else if ( type == "data" ) {

			var tag = $scope.component.options[id]['model'].tag;

			switch(componentName) {
				case 'ct_data_featured_image':
				case 'ct_data_author_avatar':
					template = '<img ng-src="{{component.options[' + id + '][\'model\'].src}}"' + options + classes + dndDraggableAttr + '/>';
					break;
				default:
					template = '<' + tag + ' ' + options + classes + dndDraggableAttr + '></' + tag + '>';
			}

			var timeout = $timeout(function() {
				$scope.renderDataComponent(id, componentName);

				// cancel timeout
				$timeout.cancel(timeout);
			}, 0, false);
        }

        // sidebar
        else if ( type == "sidebar" ) {

            template = '<div ' + options + classes + '></div>';

            var timeout = $timeout(function() {
                $scope.renderSidebar(id);
                
                // cancel timeout
                $timeout.cancel(timeout);
            }, 0, false);

        }

        if ( componentName != "ct_code_block" ) {
             
            var timeout = $timeout(function() {
                $scope.applyComponentJS(id, componentName, false);

                // cancel timeout
                $timeout.cancel(timeout);
            }, 0, false);
        }

        // store component HTML Tags in an object

        $scope.htmlTags[componentName] = jQuery(template).prop("tagName").toLowerCase();

        return template;
    }


    /**
     * Change element tag name
     * 
     * @since 0.1.7
     */

    $scope.changeTag = function (type, id, name) {

        if (undefined==id) {
            id = $scope.component.active.id;
        }
        if (undefined==name) {
            name = $scope.component.active.name;
        }

        var newComponent = $scope.getComponentTemplate(name, id, type),
            oldComponent = $scope.getComponentById(id);

        $scope.cleanReplace(oldComponent, newComponent);
    }


    /**
     * Get DOM element deepest child to insert
     * 
     * @return jqLite
     * @since 0.1.3
     */
    
    $scope.getInnerWrap = function( element ) {

        if ( !element.hasClass ) {
            return element;
        }

        if ( element.hasClass('ct-slider') ) {
            
            var child = element.find(".oxygen-unslider-container > ul");
            if ( child.prop("tagName") == "UL" ) {
                return child;
            }
            else {
                return element;
            }
        }

        if ( element.hasClass('oxy-icon-box') ) {

            var child = element.children('.oxy-icon-box-content').children('.ct-inner-wrap');
            if ( child ) {
                return child;
            }
            else {
                return element;
            }
        }

        if ( element.hasClass('ct-columns') || element.hasClass('ct-section') || element.hasClass('oxy-header-row') || element.hasClass('oxy-pricing-box') || element.hasClass('oxy-toggle')) {
        
            var child = element.children('.ct-inner-wrap');
            if ( child ) {
                return child;
            }
            else {
                return element;
            }
        }
        else {
            return element;
        }
    }


    /**
     * Get DOM element to insert builtin components
     * 
     * @return jqLite
     * @since 2.0
     * @author Ilya K.
     */
    
    $scope.getBuiltInWrap = function( element ) {

        var child = element.find(".oxy-builtin-container");
        if ( child ) {
            return child;
        }
        else {
            return element;
        }
    }


    /**
     * Get Google Maps URL based on component args
     *
     * @since 2.0
     * @author Ilya K.
     */

    $scope.getMapURL = function(id) {
        
        return $scope.trustedSource("https://maps.google.com/maps?q="+encodeURI($scope.component.options[id]['model'].map_address)+"&t=m&z="+$scope.component.options[id]['model'].map_zoom+"&output=embed&iwloc=near&key="+CtBuilderAjax.googleMapsAPIKey);
    }


    /**
     * Get Google Maps URL based on component args
     *
     * @since 2.0
     * @author Ilya K.
     */

    $scope.getSoundCloudURL = function(id) {
        
        return $scope.trustedSource("https://w.soundcloud.com/player/?url=https%3A//api.soundcloud.com/tracks/"+encodeURI($scope.component.options[id]['model']['soundcloud_track_id'])+"&amp;color="+encodeURI($scope.component.options[id]['model']['soundcloud_color'])+"&amp;auto_play="+encodeURI($scope.component.options[id]['model']['soundcloud_auto_play'])+"&amp;hide_related="+encodeURI($scope.component.options[id]['model']['soundcloud_hide_related'])+"&amp;show_comments="+encodeURI($scope.component.options[id]['model']['soundcloud_show_comments'])+"&amp;show_user=true&amp;show_reposts=false&amp;show_teaser=true&amp;visual=true" );
    }


    /**
     * Wrap selected text with <span> component
     *
     * @since 0.1.8
     */
    
    $scope.wrapWithSpan = function(returnNode) {
        
        var parentId    = $scope.component.active.id,
            parentName  = $scope.component.active.name,

            // get selection
            selection   = $scope.getUserSelection(),

            // create span
            node = document.createElement("span"),
            att  = document.createAttribute("id"),

            // get current component DOM node
            parent = $scope.getActiveComponent();
        
        att.value = "ct-placeholder-" + $scope.component.id;
        node.setAttributeNode(att);

        // update selection
        $scope.replaceUserSelection(node);

        var newComponent = {
            id : $scope.component.id, 
            name : "ct_span"
        }

        // set default options first
        $scope.applyComponentDefaultOptions(newComponent.id, "ct_span");

        // insert new component to Components Tree
        $scope.findComponentItem($scope.componentsTree.children, $scope.component.active.id, $scope.insertComponentToTree, newComponent);

        // update span options
        $scope.component.options[newComponent.id]["model"]["ct_content"] = selection;
        $scope.setOption(newComponent.id, "ct_span", "ct_content");

        // update parent options
        $scope.component.options[parentId]["model"]["ct_content"]       = parent.html();
        $scope.component.options[parentId]["original"]["ct_content"]    = parent.html();
        $scope.setOption(parentId, parentName, "ct_content");

        $scope.rebuildDOM(parentId);

        // activate component
        var timeout = $timeout(function() {
            $scope.activateComponent(newComponent.id, "ct_span");
            // cancel timeout
            $timeout.cancel(timeout);
        }, 0, false);

        if(typeof(returnNode) !== 'undefined')
            return node;
    }


    /**
     * Wrap selected text with <a> tag (not a component)
     *
     * @since 0.1.5
     * @author Ilya K.
     */
    
    $scope.wrapWithLink = function() {

        // get selection
        var sel = $scope.getUserSelection();

        // create link
        var node = document.createElement("a");
        node.appendChild(document.createTextNode(sel));

        // set URL
        var url = prompt("Define link URL", "http://");
        if (url != null) {
            var att = document.createAttribute("href");
            att.value = url;
            node.setAttributeNode(att);
        }
        
        // update selection
        $scope.replaceUserSelection(node);
    }


    /**
     * Get user selection
     *
     * @since 0.1.5
     * @author Ilya K.
     */
    
    $scope.getUserSelection = function() {

        var sel = "";

        if (typeof window.getSelection != "undefined") {
            var sel = window.getSelection();
            if (sel.rangeCount) {
                var container = document.createElement("div");
                for (var i = 0, len = sel.rangeCount; i < len; ++i) {
                    container.appendChild(sel.getRangeAt(i).cloneContents());
                }
                sel = container.innerHTML;
            }
        } else if (typeof document.selection != "undefined") {
            if (document.selection.type == "Text") {
                sel = document.selection.createRange().htmlText;
            }
        }

        return sel;
    }


    /**
     * Replace user selection 
     *
     * @since 0.1.5
     * @author Ilya K.
     */
    
    $scope.replaceUserSelection = function(node) {
        
        var range, html;
        if (window.getSelection && window.getSelection().getRangeAt) {
            range = window.getSelection().getRangeAt(0);
            range.deleteContents();
            range.insertNode(node);
        } else if (document.selection && document.selection.createRange) {
            range = document.selection.createRange();
            html = (node.nodeType == 3) ? node.data : node.outerHTML;
            range.pasteHTML(html);
        }
    }


    /**
     * Helper function to create a list of dom items to be removed from a hierarchy 
     *
     * @since 1.1.0
     * @author Gagan Goraya.
     */

    $scope.listToBeRemoved = function(toBeRemoved, componentsTree, id, collect) {

        if(typeof(collect) === 'undefined')
            collect = false;
    
        var keepGoing = true;

        angular.forEach(componentsTree, function(item, index) {
            
            if(keepGoing) {
               
                if(id && item.id === parseInt(id)) {
                    
                    toBeRemoved.splice(0,toBeRemoved.length);

                    collect = true;
                    keepGoing = false;

                }

                if(collect)
                    toBeRemoved.push(item.id);
                
                if ( item.children ) {
                    $scope.listToBeRemoved(toBeRemoved, item.children, id, collect);
                }
            }
        });
    }

    /**
     * Rebuild DOM node based on current Components Tree
     * 
     * @since 0.1
     * @author Ilya K.
     */
    
    $scope.rebuildDOM = function(id, reorder) {

        if ($scope.log) {
            console.log("rebuildDOM()", id);
        }
        
        $scope.functionStart("rebuildDOM");

        // build children
        if ( $scope.componentsTree.children ) {
            
            if(reorder) {
                
                var toBeRemoved = []
                //recurse and prepare list of items to be removed from the DOM
                $scope.listToBeRemoved(toBeRemoved, $scope.componentsTree.children, id);

                angular.forEach(toBeRemoved.reverse(), function(itemid) {
                    $scope.removeComponentFromDOM(itemid);
                });

            }

            $scope.buildComponentsFromTree($scope.componentsTree.children, id, reorder);
            
            // increment id
            $scope.component.id++;

            $scope.functionEnd("rebuildDOM");
        } 
        else {

            $scope.functionEnd("rebuildDOM");
            return false;
        }
    }


    /**
     * Cut component from DOM
     * 
     * @since 0.1.8
     * @author Ilya K.
     */

    $scope.removeComponentFromDOM = function(id) {

        var component = $scope.getComponentById(id);

        if ( component ) {

            // check if we removing slide or slider
            if ( component.is('.ct-slide, .ct-slider') ) {
                var unslider = component.closest('.unslider');
            }

            component.scope().$destroy();
            component.remove();
            component = null;

            // remove unslider wrap if slider detected
            if (unslider) {
                unslider.remove();
                unslider = null;
            }
        }
    }


    /**
     * Add Layout cells and set their width according to 
     * 
     * @since 2.0
     * @author Ilya K.
     */

    $scope.addPresetColumns = function(columnsWidths) {

        var columnsId = $scope.component.active.id,
            columnsComponent = $scope.getActiveComponent(),
            columnId;

        // clear preset templates
        columnsComponent.html("");

        $scope.updateColumnsOnAdd = false;

        angular.forEach(columnsWidths, function(width) {
            
            $scope.addComponent("ct_div_block");
            
            $scope.setOptionModel("width",width);
            $scope.setOptionModel("width-unit","%");

            columnId = $scope.component.active.id;
            
            // activate columns back
            $scope.activateComponent(columnsId,"ct_new_columns");
        })

        $scope.updateColumnsOnAdd = true;

        $scope.updateDOMTreeNavigator(columnsId);
    }


    /**
     * Add a text block with predifened data shortcode
     * 
     * @since 2.0
     * @author Ilya K.
     */

    $scope.addCustomFieldComponentWithParams = function(dynamicDataModel, dataitem) {

        var shortcode = '[oxygen data="'+dataitem.data+'"';
                    
        var finalVals = {};
        _.each(dataitem.properties, function(property) {
            if(dynamicDataModel.hasOwnProperty(property.data) && dynamicDataModel[property.data].trim !== undefined && 
                dynamicDataModel[property.data].trim()!=='' &&
                !property.helper && dynamicDataModel[property.data] !== property.nullVal) {
                finalVals[property.data] = dynamicDataModel[property.data];
            }
        });

        _.each(finalVals, function(property, key) {
            property = property.replace(/'/g, "__SINGLE_QUOTE__");
            shortcode+=' '+key+'="'+property+'"';
        })

        if(dataitem['append']) {
            shortcode+=' '+dataitem['append'];
        }

        shortcode+=']';

        $scope.addComponent('ct_text_block');

        iframeScope.setOptionModel('ct_content', shortcode);

        var content = iframeScope.getOption('ct_content');
        var idIncrement = 0;

        content = content.replace(/\[oxygen[^\]]*\]/ig, function(match) {

            // create a span component out of match
            // embed it in the tree as a child of $scope.iframeScope.component.active.id
            // get the new component's id

            var newComponent = {
              id : iframeScope.component.id + idIncrement, 
              name : "ct_span"
            }

            idIncrement++;

            // set default options first
            iframeScope.applyComponentDefaultOptions(newComponent.id, "ct_span");

            // insert new component to Components Tree
            iframeScope.findComponentItem(iframeScope.componentsTree.children, iframeScope.component.active.id, iframeScope.insertComponentToTree, newComponent);

            // update span options
            iframeScope.component.options[newComponent.id]["model"]["ct_content"] = match;
            iframeScope.setOption(newComponent.id, "ct_span", "ct_content");

            return "<span id=\"ct-placeholder-"+newComponent.id+"\"></span>"
        });

        iframeScope.setOptionModel('ct_content', content, iframeScope.component.active.id, iframeScope.component.active.name);

        $scope.parentScope.oxygenUIElement.find('#ctdynamicdata-popup').remove();
        $scope.parentScope.oxygenUIElement.find('.oxy-dynamicdata-popup-background').remove();

    }

    $scope.addCustomFieldComponent = function() {
        $scope.dynamicDataModel = {};
        var template = 
        '<div class="oxy-dynamicdata-popup-background"></div>'+
        '<div id="ctdynamicdata-popup" class="oxygen-data-dialog">'+
            '<div>'+
                '<div class="oxygen-data-dialog-data-picker">'+
                    '<ul>'+
                        '<li>'+
                            '<div ng-if="dynamicShortcodesCFOptions.properties" class="oxygen-data-dialog-options">'+
                                '<h1>{{dynamicShortcodesCFOptions.name}} Options</h1>'+
                                '<div>'+
                                '<div class="oxygen-control-wrapper" ng-repeat="property in dynamicShortcodesCFOptions.properties">'+
                                    '<label ng-if="property.name&&property.type!==\'checkbox\'" class="oxygen-control-label"> {{property.name}} </label>'+
                                    // dropdown
                                    '<div ng-if="property.type===\'select\'" class="oxygen-select oxygen-select-box-wrapper">'+
                                        '<div class="oxygen-select-box">'+
                                            '<div class="oxygen-select-box-current">{{dynamicDataModel[property.data]}}</div>'+
                                            '<div class="oxygen-select-box-dropdown"></div>'+
                                        '</div>'+
                                        '<div class="oxygen-select-box-options">'+
                                            '<div ng-repeat="option in property.options" ng-click="dynamicDataModel[property.data]=option;applyChange(property)" class="oxygen-select-box-option">{{option}}</div>'+
                                        '</div>'+
                                    '</div>'+
                                    // input
                                    '<div class="oxygen-input" ng-if="property.type===\'text\'">'+
                                        '<input type="text" ng-model="dynamicDataModel[property.data]" ng-change="applyChange(property)" ng-trim="false"/>'+
                                    '</div>'+
                                    // checkbox
                                    '<label class="oxygen-checkbox" ng-if="property.type===\'checkbox\'" >{{property.name}}'+
                                        '<input type="checkbox" ng-model="dynamicDataModel[property.data]" ng-true-value="\'{{property.value}}\'" ng-change="applyChange(property)" />'+
                                        '<div class="oxygen-checkbox-checkbox" ng-class="{\'oxygen-checkbox-checkbox-active\':dynamicDataModel[property.data]==\'{{property.value}}\'}"></div>'+
                                    '<label>'+

                                    '<br ng-if="property.type===\'break\'" />'+
                                '</div class="oxygen-control-wrapper">'+
                                '</div>'+
                                '<div class="oxygen-apply-button" ng-mousedown="addCustomFieldComponentWithParams(dynamicDataModel, dynamicShortcodesCFOptions)">Insert</div>'+
                            '</div>'+
                        '</li>';
                    '</ul>';
                '</div>';
            '</div>';
        '</div>';

        
        angular.element(document).injector().invoke(function($compile) {

            var compiledElement = $compile(template)($scope);
            
            $scope.parentScope.oxygenUIElement.append(compiledElement);

            $scope.$apply();

        });

    }


    /**
     * Compile and insert new component
     *
     * @since 0.1.6
     * @author Ilya K.
     */

    $scope.cleanInsert = function(element, parentElement, index) {

        angular.element(document).injector().invoke(function($compile) {

            var newScope        = $scope.$new();
            //     existingScope   = angular.element(element).scope();
            
            // if (existingScope) {
            //     //existingScope.$destroy();
            // }

            var compiledElement = $compile(element)(newScope);
            
            if ( parentElement ) {
                $scope.insertAtIndex(compiledElement, parentElement, index);
            } 
            else {
                angular.element(element).replaceWith(compiledElement);
            }

            if(angular.element(element).hasClass('removeOnInsert')) {
                parentElement.html(angular.element(element).html());
            }
        });
    }


    /**
     * Compile and replace component 
     *
     * @since 0.1.7
     * @author Ilya K.
     */

    $scope.cleanReplace = function(element, replacement) {

        angular.element(document).injector().invoke(function($compile) {

            var newScope = $scope.$new(),
                compiledReplacement = $compile(replacement)(newScope);
            
            angular.element(element).replaceWith(compiledReplacement);
        });
    }


    /**
     * Insert child DOM element at a specific index in a parent element
     *
     * @since 0.1.7
     * @author Ilya K.
     */

    $scope.insertAtIndex = function(child, parent, index) {

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

    
    /**
     * Check if component contain [oxygen] data shortcode
     *
     * @since 2.0
     * @author Ilya K.
     */
    
    $scope.hasOxyDataInside = function(id) {

        if(typeof(id) === 'undefined') {
            id = $scope.component.active.id;
        }

        if ($scope.getComponentById(id)) {
            return $scope.getComponentById(id).children('span.ct-contains-oxy').length > 0;
        }
        else {
            return false;
        }
    }


    /**
     * Helper function to escape HTML special chars
     *
     * @since 0.1.7
     * @author Ilya K.
     */
    
    $scope.escapeHtml = function(text) {
        var map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };

        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }
    

    /**
     * Helper function to add slashes before quotes
     *
     * @since 0.1.7
     * @author Ilya K.
     */
    
    $scope.addSlashes = function(str) {

        return (str + '')
            .replace(/[\\"']/g, '\\$&')
            .replace(/\u0000/g, '\\0');
    }

    /**
     * Helper function to strip slashes from server response
     *
     * @since 0.4.0
     * @author Ilya K.
     */
    
    $scope.stripSlashes = function(str){
        return str.replace(/\\(.)/mg, "$1");
    }
    

    /**
     * Helper functions to check fucntion execution speed
     *
     * @since 0.2.?
     * @author Ilya K.
     */
    
    $scope.functionStart = function(name) {
        if ( $scope.log === true ) {
            console.time(name);
        }
    }

    $scope.functionEnd = function(name) {
        if ( $scope.log === true ) {
            console.timeEnd(name);
        }
    }

    
    /**
     * Prevent user from leaving a builder if link clicked or form submitted
     * 
     * @since 0.2.5
     */
    
    $scope.setupPageLeaveProtection = function() {

        // bind click and submit events
        jQuery("#ct-builder")
            .on("click", "a", function(e) {    
                e.preventDefault()
            })
            .on("submit", "form", function(e) {    
                e.preventDefault()
            });
    }


    /**
     * Show confirmation dialong on exit
     *
     * @since 0.3.2
     */
    
    $scope.confirmOnPageExit = function(e) {
        
        // If we haven't been passed the event get the window.event
        e = e || window.event;

        var message = 'There is unsaved changes.';

        // For IE6-8 and Firefox prior to version 4
        if (e) 
        {
            e.returnValue = message;
        }

        // For Chrome, Safari, IE8+ and Opera 12+
        return message;
    };


    /**
     * Attach event to show confirm dialog on exit when all saved
     *
     * @since 0.3.2
     */
    
    $scope.unsavedChanges = function() {

        if ($scope.log) {
            console.log("unsavedChanges()");
        }

        if (window.onbeforeunload===null) {
            window.onbeforeunload = $scope.confirmOnPageExit;
        }

        jQuery("#ct-save-button").addClass("ct-unsaved-changes");
    }


    /**
     * Remove event to hide confirm dialog on exit when all saved
     *
     * @since 0.3.2
     */
    
    $scope.allSaved = function() {
        window.onbeforeunload = null;
        jQuery("#ct-save-button").removeClass("ct-unsaved-changes");
    }


    /**
     * Helper to prevent Angular sort Object on ng-repeat
     * 
     * @since 0.3.2
     */
    
    $scope.notSorted = function(obj) {
        if (!obj) {
            return [];
        }
        return Object.keys(obj);
    }


    $scope.objectToArrayObject = function(obj) {
        if (!obj) {
            return [];
        }

        var arr = _.map(obj, function(item, key) {
            if(typeof(item) === 'object') {
                return Object.assign(item, {'key': key});
            }
            else {
                return item;
            }
        });

        return arr;

    }


    /**
     * Helper to prevent Angular sort Object on ng-repeat
     * 
     * @since 0.3.3
     */
    $scope.isObjectEmpty = function(obj) {
        if (obj) {
            for (var prop in obj) {
                if (obj.hasOwnProperty(prop)) {
                    return false;
                }
            }
        }
        return true;
    }


    /**
     * Helper to encode string with Unicode characters to base64
     * 
     * @since 0.4.0
     * @author Ilya K.
     */
    
    $scope.b64EncodeUnicode = function(str) {
        return btoa(encodeURIComponent(str).replace(/%([0-9A-F]{2})/g, function(match, p1) {
            return String.fromCharCode('0x' + p1);
        }));
    }


    /**
     * This function intercepts the paste event, removes the formatting off the clipboard data
     * and programatically inserts the plain text. 
     * 
     * As undo/redo history of the editable region gets destroyed on programatically inserting 
     * text into the element. This function contains provision for 1 undo (ctrl-z/cmd-z) to restore
     * the state of element to what it was before the the paste.
     * @author gagan goraya
     *
     * @since 0.3.4
     */

    var stripFormatting = function(e) {
        
        var me = this,
            oldContent = jQuery(me).html(),
            strippedPaste = jQuery('<div>').append(e.originalEvent.clipboardData.getData("Text").replace(/(?:\r\n|\r|\n)/g, '%%linebreak%%')).text(),//.replace(/(?:\r\n|\r|\n)/g, '<br />');
            sel, range;

        if (window.getSelection) {

            sel = window.getSelection();
            
            if (sel.rangeCount) {
                range = sel.getRangeAt(0);
                range.deleteContents();
                var nodes = strippedPaste.split('%%linebreak%%'), 
                    node;

                for (var key in nodes) {
                    if (nodes.hasOwnProperty(key)) {
                    
                        node = document.createTextNode(nodes[key]);
                        
                        range.insertNode(node);
                        range = range.cloneRange();
                        range.selectNodeContents(node);
                        range.collapse(false);
                        
                        sel.removeAllRanges();
                        sel.addRange(range);

                        if (key < nodes.length-1) {
                            node = document.createElement('br')
                                                        
                            range.insertNode(node);
                            range = range.cloneRange();
                            range.selectNode(node);
                            range.collapse(false);
                        
                            sel.removeAllRanges();
                            sel.addRange(range);
                        }
                    }
                }
            }

        } else if (document.selection && document.selection.createRange) {

            range = document.selection.createRange();
            range.text = strippedPaste.split('%%linebreak%%').join("");
        }

        // This lets the user undo the paste action.
        var undofunc = function(e) {
            
            if(e.keyCode === 90 && (e.ctrlKey || e.metaKey)) {
                e.preventDefault();
                jQuery(me).html(oldContent);
                jQuery(me).off('keydown', undofunc);
            }
        }
        jQuery(me).on('keydown', undofunc);
        e.preventDefault();
    }

    jQuery('body')
        /*.on('paste', '.ct_paragraph', stripFormatting)
        .on('paste', '.ct_headline', stripFormatting)
        .on('paste', '.ct_li', stripFormatting)
        .on('paste', '.ct_span', stripFormatting)
        .on('paste', '.ct_link_text', stripFormatting)
        .on('paste', '.ct_text_block', stripFormatting)*/
        .on('paste', '[contenteditable]', stripFormatting);

    
    /**
     * Check if user is developer
     */
    
    $scope.isDev = function() {

        var devsArray = [1,2,3,4,5];
        
        return devsArray.indexOf(parseInt($scope.authInfo["token_check"]["ID"],10)) > -1;
    }


    /**
     * Check API response and console.log errors if something wrong
     *
     * @author Ilya K.
     * @since 1.4
     */

    $scope.checkAPIstatus = function() {

        if ($scope.authInfo.token_check.status && 
            $scope.authInfo.token_check.status=="error") {

            // cURL error
            if ($scope.authInfo.token_check.message) { 
                console.log($scope.authInfo.token_check.message)
            }
            else
            // API error
            if ($scope.authInfo.token_check.error &&
                $scope.authInfo.token_check.error.errors ) { 
                console.log($scope.authInfo.token_check.error.errors[Object.keys($scope.authInfo.token_check.error.errors)[0]])
            }
            else {
                console.log($scope.authInfo.token_check)
            }
        }
    }

    
    /**
     * Apply parent scope after each iframe digest
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
            if ($scope.parentScope)
                $scope.parentScope.safeApply();
        });
    });

    
    /**
     * Triggered from UI to apply iframe scope 
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
    
    
// End MainController
});

CTFrontendBuilder.factory('$parentScope', function($window) {
    return $window.parent.angular.element($window.frameElement).scope();
});

/**
 * Collect all controllers into one
 * 
 */

CTFrontendBuilder.controller('BuilderController', function($controller, $scope, $parentScope, $http, $timeout, $window) {

    // reference for views
    $scope.parentScope = $parentScope;

    var locals = {
        $scope: $scope,
        $parentScope: $parentScope,
        $http: $http,
        $timeout: $timeout
    };

    $controller('MainController',           locals);
    $controller('ComponentsTree',           locals);
    $controller('ComponentsStates',         locals);
    $controller('ControllerNavigation',     locals);
    $controller('ControllerColumns',        locals);
    $controller('ControllerAJAX',           locals);
    $controller('ControllerClasses',        locals);
    $controller('ControllerOptions',        locals);
    $controller('ControllerFonts',          locals);
    $controller('ControllerCSS',            locals);
    $controller('ControllerTemplates',      locals);
    $controller('ControllerSVGIcons',       locals);
    $controller('ControllerMediaQueries',   locals);
    $controller('ControllerAPI',            locals);
    $controller('ControllerDragnDropLists', locals);
    $controller('ControllerHeader',         locals);
});
