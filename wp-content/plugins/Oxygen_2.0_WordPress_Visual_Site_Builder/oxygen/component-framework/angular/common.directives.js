var CTCommonDirectives = angular.module('CTCommonDirectives', []);

CTCommonDirectives.factory('ctScopeService', function() {
    var mem = {};
    return {
        store: function(key, val) {
            mem[key] = val;
        },
        get: function(key) {
            return mem[key];
        }
    }
});

CTCommonDirectives.factory('ctOxyCache', function() {
    var mem = {};
    return {
        store: function(key, val) {
            mem[key] = val;
        },
        get: function(key) {
            return mem[key];
        }
    }
});

CTCommonDirectives.directive("ctiriscolorpicker", function() {
    return {
        restrict: "A",
        require: "ngModel",
        scope: {
            ctiriscallback: '=',
        },
        
        link: function(scope, element, attrs, ngModel) {
            var debounceChange = false;
            setTimeout(function(){

                element.alphaColorPicker({
                    change: function(ui) {
                        if(element.val().length == ui.color.toString().length || element.val().length === 0) {
                            if(!debounceChange) {
                                debounceChange = setTimeout(function() {
                                    ngModel.$setViewValue(ui.color.toString());
                                    debounceChange = false;
                                }, 100);
                            }
                        }
                        if(scope.ctiriscallback) {
                            scope.ctiriscallback();
                        }
                    }
                });

                scope.$watch(attrs.ngModel, function( newVal ) {
                    if (!newVal || newVal === "") {
                        // unset background color
                        element.closest('.wp-picker-container').find('.wp-color-result').css("background-color","");
                        return;
                    }

                    element.ctColorPicker('color', newVal);
                });

            }, 0);

        }
    }
});

CTCommonDirectives.directive("ctdynamicdata", function($compile, ctScopeService) {
    return {
        restrict: "A",
        replace: true,
        scope: {
            data: "=",
            callback: "=",
        },
        link: function(scope, element, attrs) {
            
            angular.element('body').on('click', '.oxy-dynamicdata-popup-background', function() {
                angular.element('#ctdynamicdata-popup').remove();
                angular.element('.oxy-dynamicdata-popup-background').remove();
            });

            scope.dynamicDataModel = {};
            scope.showOptionsPanel = { item: false };

            scope.processCallback = function(name, dataitem, showOptions) {
                if(showOptions && dataitem.properties && dataitem.properties.length > 0) {
                   scope.showOptionsPanel.item = name+dataitem.data;
                }
                else
                if(scope.callback) {
                    
                    var shortcode = '[oxygen data="'+dataitem.data+'"';
                    
                    var finalVals = {};
                    _.each(dataitem.properties, function(property) {
                        if(scope.dynamicDataModel.hasOwnProperty(property.data) && scope.dynamicDataModel[property.data].trim !== undefined && 
                            scope.dynamicDataModel[property.data].trim()!=='' &&
                            !property.helper && scope.dynamicDataModel[property.data] !== property.nullVal) {
                            finalVals[property.data] = scope.dynamicDataModel[property.data];
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

                    scope.callback(shortcode);
                    angular.element('#ctdynamicdata-popup').remove();
                    angular.element('.oxy-dynamicdata-popup-background').remove();
                }
                scope.dynamicDataModel={};
            }

            scope.applyChange = function(property) {
                if(property.change) {
                    eval(property.change);
                }
            }

            element.on('click', function() {
                scope.showOptionsPanel.item = false;
                angular.element('body #ctdynamicdata-popup').remove();
                angular.element('body .oxy-dynamicdata-popup-background').remove();
                
                var template = '<div class="oxy-dynamicdata-popup-background"></div>'+
                        '<div id="ctdynamicdata-popup" class="oxygen-data-dialog">'+
                            '<h1>Insert Dynamic Data</h1>'+
                            '<div>'+
                                '<div class="oxygen-data-dialog-data-picker"'+
                                    'ng-repeat="item in data">'+
                                    '<h2>{{item.name}}</h2>'+
                                    '<ul>'+
                                        '<li ng-repeat="dataitem in item.children">'+
                                            '<span ng-mousedown="processCallback(item.name, dataitem, true)">{{dataitem.name}}</span>'+
                                            '<div ng-if="dataitem.properties" ng-show="showOptionsPanel.item === item.name+dataitem.data" class="oxygen-data-dialog-options">'+
                                                '<h1>{{dataitem.name}} Options</h1>'+
                                                '<div>'+
                                                '<div class="oxygen-control-wrapper" ng-repeat="property in dataitem.properties">'+
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
                                                '<div class="oxygen-apply-button" ng-mousedown="processCallback(item.name, dataitem)">Insert</div>'+
                                            '</div>'+
                                        '</li>'+
                                    '</ul>'+
                                '</div>'+
                            '</div>'+
                        '</div>';

                var compiledElement = $compile(template)(scope);

                scope.$parent.$parent.oxygenUIElement.append(compiledElement);

                scope.$apply();
            })
        }

    }
});

CTCommonDirectives.directive("ctrenderoxyshortcode", function($http, ctOxyCache) {
    return {
        restrict: "A",
        require: "ngModel",
        link: function(scope, element, attrs, ngModel) {
            
            var callback = function(shortcode, contents) {
                //ctOxyCache.store(shortcode, contents);
                element.html(contents);
            }

            setTimeout(function() {
                var id = parseInt(element.attr('ng-attr-component-id'));
                var shortcode = scope.$parent.getOption('ct_content', id);
                var shortcode_data = {
                    original: {
                        full_shortcode: shortcode
                    }
                }

                // add specific class only for content dynamic data
                if (shortcode.indexOf("data='content'")>0) {
                    
                    // hack needed to properly update components class in components tree
                    scope.$parent.currentClass = "oxy-stock-content-styles";
                    
                    scope.addClassToComponent(id,'oxy-stock-content-styles',false)
                    
                    // hack needed to properly update components class in components tree
                    scope.$parent.currentClass = false;
                }

                // var contents = ctOxyCache.get(shortcode);
                // if(contents) {
                //     callback(shortcode, contents);
                // }
                // else {

                scope.renderShortcode(id, 'ct_shortcode', callback, shortcode_data);

               // }
            }, 0);
        }
    }
})

/**
 * Make HTML5 "contenteditable" support ng-module
 * To enforce plain text mode, use attr data-plaintext="true"
 */

CTCommonDirectives.directive("contenteditable", function($timeout,$interval, ctScopeService) {

    return {
        restrict: "A",
        require: "ngModel",
        link: function(scope, element, attrs, ngModel) {

            element.unbind("paste input");

            function read() {
                ngModel.$setViewValue(element.html());
            }

            function getCaretPosition() {
                
                if(window.getSelection) {
                    selection = window.getSelection();
                    if(selection.rangeCount) {
                        range = selection.getRangeAt(0);
                        return(element.text().length-range.endOffset);
                    }
                }
            }

            function setCaretPosition(caretOffsetRight) {
                var range, selection;

                if(document.createRange) {
                    range = document.createRange();
                    if(element.get(0) && element.get(0).childNodes[0]) {
                        var offset = element.text().length;
                        
                        range.setStart(element.get(0), 0);
                        
                        if(caretOffsetRight > 0 && caretOffsetRight <= offset) {
                            offset -= caretOffsetRight;
                        }
                        range.setEnd(element.get(0).childNodes[0], offset);
                        range.collapse(false);
                        selection = window.getSelection();
                        selection.removeAllRanges();
                        selection.addRange(range);
                        
                    }
                    
                }
                else if(document.selection) {
                    range = document.body.createTextRange();
                    if(element.get(0) && element.get(0).childNodes[0]) {
                        var offset = element.text().length;
                            
                        range.setStart(element.get(0), 0);
                        
                        if(caretOffsetRight > 0 && caretOffsetRight <= offset) {
                            offset -= caretOffsetRight;
                        }
                        range.setEnd(element.get(0).childNodes[0], offset);
                        range.collapse(false);
                        range.select();
                    }
                }
            }

            ngModel.$render = function() {

                element.html(ngModel.$viewValue || "");

            };


            // save element content
            element.bind("input", function(e, paste) {

                scope.$apply(read);
                
                // if it is plaintext mode, replace any html formatting, only in paste mode
                if(paste && typeof(attrs['plaintext']) !== 'undefined' && attrs['plaintext'] === "true") {
                    
                    if(jQuery('<span>').html(element.html()).text().trim() !== element.html().trim().replace('&nbsp;', '')) {
                       // var caretPosition = getCaretPosition();
                       // element.html(jQuery('<span>').html(element.html()).text());
                       // setCaretPosition(caretPosition);
                        element.html(element.text());
                    }

                    ngModel.$setViewValue(element.text());
                }

                // if default text is provided and current text is blank. populate with defaulttext
                if(element.html().trim() === '' && typeof(attrs['defaulttext']) !== 'undefined' && attrs['defaulttext'].trim() !== '') {
                    element.text(attrs['defaulttext']);
                }

                // timeout for angular
                var timeout = $timeout(function() {
                    var dascope = scope,
                        optionName = attrs['optionname'] || "ct_content";

                    if(scope.iframeScope)
                        dascope = scope.iframeScope; 
                    dascope.setOption(dascope.component.active.id, dascope.component.active.name, optionName);
                    $interval.cancel(timeout);
                }, 20, false);
            })

            // trick to update content after paste event performed
            element.bind("paste", function() {
                setTimeout(function() {element.trigger("input", 'paste');}, 0);
            });
            
            // if data-plaintext is NOT set to "true"
            if(typeof(attrs['plaintext']) === 'undefined' || attrs['plaintext'] !== "true") {

                // enable content editing on double click
                element.bind("dblclick", function() {
                    
                    var parentScope = ctScopeService.get('scope').parentScope,
                        optionName = attrs['optionname'] || "ct_content";
                    
                    // before enabling edit content,
                    var content = scope.getOption(optionName);
                    
                    content = content.replace(/\<span id\=\"ct-placeholder-([^\"]*)\"\>\<\/span\>/ig, function(match, id) {
                        
                        var oxy = scope.component.options[parseInt(id)]['model']['ct_content'];

                        var containsOxy = oxy.match(/\[oxygen[^\]]*\]/ig);

                        if(containsOxy) {
                            scope.removeComponentById(parseInt(id), 'span', scope.component.active.id);
                            return oxy;
                        }
                        else {
                            return match;
                        }

                    });

                    scope.setOptionModel(optionName, content, scope.component.active.id, scope.component.active.name)

                    parentScope.enableContentEdit(element);
                    scope.$apply();
                });

                // format as <p> on enter/return press
                if ( element[0].attributes['ng-attr-paragraph'] ) {
                    element.bind('keypress', function(e){
                        if ( e.keyCode == 13 ) {
                            document.execCommand('formatBlock', false, 'p');
                        }
                    });
                }
                else {
                    // format as <br/>
                    element.bind('keypress', function(e){
                        if ( e.keyCode == 13 ) { 
                            document.execCommand('insertHTML', false, '<br><br>');
                            return false;
                        }
                    });
                }
            } 
            // else if it is plaintext mode
            else {
                // we do not need line breaks
                element.bind('keypress', function(e){
                    
                    if ( e.keyCode == 13 ) { 
                        element.blur();
                        return false;
                    }
                });
            }
            
            // if ngBlur is provided
            if(typeof(attrs['ngBlur']) !== 'undefined' || attrs['ngBlur'] !== "") {
                element.bind('blur', function() {
                    var timeout = $timeout(function() {
                        scope.$apply(attrs.ngBlur);
                        $interval.cancel(timeout);
                    }, 0, false);
                })
            }

        }
    };
});

/**
 * Helps an input text field gain focus based on a condition
 * 
 * @since 0.3.3
 * @author Gagan Goraya
 *
 * usage: <input type="text" focus-me="booleanValue" />
 */
 
CTCommonDirectives.directive('focusMe', function($timeout) {
  return {
    scope: { trigger: '=focusMe' },
    link: function(scope, element) {
      scope.$watch('trigger', function(value) {
        if(value === true) { 
          $timeout(function() {
            element[0].focus();
            scope.trigger = false;
          });
        }
      });
    }
  };
});
