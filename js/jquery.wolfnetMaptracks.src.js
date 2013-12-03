(function($){

    var pluginName = 'wolfnetMapTracks';

    var MapTracks = function () {};

    MapTracks.prototype.createMap = function (mapNode) {
      
        var params = {
            mapName:           $(mapNode).attr("data-wnt-map-name"),
            centerLat:         $(mapNode).attr("data-wnt-map-centerlat"),
            centerLng:         $(mapNode).attr("data-wnt-map-centerlng"),
            mapZoomLevel:      $(mapNode).attr("data-wnt-map-mapzoomlevel"),
            hasHouseView:      $(mapNode).attr("data-wnt-map-hasHouseView") === "true",
            hasStreetView:     $(mapNode).attr("data-wnt-map-hasStreetView") === "true",
            hasMiniMap:        $(mapNode).attr("data-wnt-map-hasMiniMap") === "true",
            showScales:        $(mapNode).attr("data-wnt-map-showScales") === "true",
            showZoom:          $(mapNode).attr("data-wnt-map-showZoom") !== "false",
            showView:          $(mapNode).attr("data-wnt-map-showView") !== "false",
            provider:          $(mapNode).attr("data-wnt-map-provider"),
            mapViewType:       $(mapNode).attr("data-wnt-map-mapViewType"),
            mapDragType:       $(mapNode).attr("data-wnt-map-mapDragType"),
            allowMouseWheel:   $(mapNode).attr("data-wnt-map-allowMouseWheel"),
            hasStartingRect:   $(mapNode).is("[data-wnt-map-brLat]"),
            startingRectangle: {
                tlLat: $(mapNode).attr("data-wnt-map-tlLat"),
                tlLng: $(mapNode).attr("data-wnt-map-tlLng"),
                brLat: $(mapNode).attr("data-wnt-map-brLat"),
                brLng: $(mapNode).attr("data-wnt-map-brLng")
            },
            persistedMapScale: $("#h_map_scale").val(),
            hasPOIIcon:        $(mapNode).is("[data-wnt-map-poi-icon]"),
            poiIcon: {
                icon: $(mapNode).attr("data-wnt-map-poi-icon"),
                lat:  $(mapNode).attr("data-wnt-map-poi-lat"),
                lng:  $(mapNode).attr("data-wnt-map-poi-lng")
            }
        };

        //Support misspelling
        params.isMovable = $(mapNode).attr("data-wnt-map-isMoveable") === "true";

        this.map = new MapTracksMap(mapNode, params);
    }

    var MapTracksMap = function (baseNode, params) {

        mapTracksGlobals.MAP_ID++;

        var map = this;   

        // Parameters
        var mapNode                    = baseNode;
        var mapID                      = mapTracksGlobals.MAP_ID;
        var mapName                    = getParam(params["mapName"], "map");
        var width                      = $(mapNode).width();
        var height                     = $(mapNode).height();
        var mapZoomLevel               = getParam(params["mapZoomLevel"], 6);
        var houseViewZoomLevel         = getParam(params["houseViewZoomLevel"], 1);
        var centerLat                  = getParam(params["centerLat"], 44.970104);
        var centerLng                  = getParam(params["centerLng"], -93.256915);
        var isMovable                  = getParam(params["isMovable"], true);
        var hasMiniMap                 = getParam(params["hasMiniMap"], false);
        var showScales                 = getParam(params["showScales"], true);
        var showZoom                   = getParam(params["showZoom"], true);
        var showView                   = getParam(params["showView"], true);
        var provider                   = getParam(params["provider"], "bing");
        var mapViewType                = getParam(params["mapViewType"], "map");
        var dragType                   = "move"; //getParam(params["mapDragType"], "move");
        var hasStreetView              = getParam(params["hasStreetView"], false);
        var hasHouseView               = getParam(params["hasHouseView"], false);
        // Mouse
        var allowMouseWheel            = getParam(params["allowMouseWheel"], false);
        var mouseWheelEnabled          = false;
        var mouseOverMap               = false;
        var mouseWheelRolloverEnabled  = true;
        // MapQuest pans based on a %, so only one pan is needed.
        var mapquestPan                = getParam(params["mapquestPan"], 25);
        var mapquestMaxZoom            = getParam(params["mapquestMaxZoom"], 15);
        var mapquestMinZoom            = getParam(params["mapquestMinZoom"], 3);
        var mapquestZoomAdjust         = 3;
        // Bing pans based on pixels, so we need separate vertical and horizontal pan amounts.
        var bingVerticalPan            = getParam(params["bingVerticalPan"], 75);
        var bingHorizontalPan          = getParam(params["bingHorizontalPan"], 100);
        // Starting rectangle
        var hasStartingRect            = getParam(params["hasStartingRect"], false);
        var startingRect               = getParam(params["startingRectangle"], {});
        var persistedMapScale          = getParam(params["persistedMapScale"], "");
        // POI Icon
        var hasPOIIcon                 = getParam(params["hasPOIIcon"], false);
        var poiIcon                    = getParam(params["poiIcon"], {});

        // Save starting values for reset
        var startMapZoomLevel    = mapZoomLevel;
        var startCenterLat       = centerLat;
        var startCenterLng       = centerLng;
        var startDragType        = dragType;

        // Disable house view in IE6 - several bugs with bird's-eye view in bing maps.
        if ($.browser.msie && $.browser.version.substring(0, 1) == 6) {
            hasHouseView = false;
        }

        var currentView           = "";
        var mapIsLoading          = "";
        var lastRolloverContent   = "";
        var map_office_ID         = "";
        var mapOrientation        = "";
        var isHouseViewAvailable  = false;
        var isStreetViewAvailable = false;

        // Getters
        this.getBingMapNode = function () {
            return bingMapNode;
        };

        this.getMapQuestNode = function () {
            return mapquestNode;
        };

        this.getCurrentView = function () {
            return currentView;
        };

        this.getDragType = function () {
            return dragType;
        };

        this.getAllowMouseWheel = function () {
            return allowMouseWheel;
        };

        this.getMapOrientation = function () {
            return mapOrientation;
        };

        this.isHouseViewAvailable = function () {
            return isHouseViewAvailable;
        };

        this.isStreetViewAvailable = function () {
            return isStreetViewAvailable;
        };

        this.hasStreetView = function () {
            return hasStreetView;
        };

        this.hasHouseView = function () {
            return hasHouseView;
        };


        this.pauseDrag = function () {
            startDragType = dragType;
            dragType = "";
            if (map.getDragType() == "move") {
                map.setDragMove(false);
            } else {
                map.setDragZoom(false);
            }
        };

        this.resumeDrag = function () {
            dragType = startDragType;
            if (map.getDragType() == "move") {
                map.setDragMove(true);
            } else {
                map.setDragZoom(true);
            }
        };

        this.setMapBindings = function () {
            $bindingFields.centerLat.val(mapquestMap.getCenter().lat);
            $bindingFields.centerLng.val(mapquestMap.getCenter().lng);
            $bindingFields.mapType.val(mapquestMap.getCurrentMapType().id);
            $bindingFields.lrLat.val(mapquestMap.getBounds().lr.lat);
            $bindingFields.lrLng.val(mapquestMap.getBounds().lr.lng);
            $bindingFields.ulLat.val(mapquestMap.getBounds().ul.lat);
            $bindingFields.ulLng.val(mapquestMap.getBounds().ul.lng);
            $bindingFields.zoom.val(map.getZoomLevel());
            $bindingFields.centerLat.change();
            $bindingFields.centerLng.change();
            $bindingFields.mapType.change();
            $bindingFields.lrLat.change();
            $bindingFields.lrLng.change();
            $bindingFields.ulLat.change();
            $bindingFields.ulLng.change();
            $bindingFields.zoom.change();
        };        

        this.setupMouseControls = function () {
            var br = $.browser;
            var showMouseControls = false; // To_Release: default to true
            var map = this;

            if ((br.msie != undefined) && (br.version.slice(0, 1) < 8)) {
                showMouseControls = false;
            }

            $("[data-wnt-controlContainer=][data-wnt-map-name=" + mapName + "]").each(function () {
                $(this).data("controlContainer", map.controlContainer(this, {}));
            });

            $("[data-wnt-nav=][data-wnt-map-name=" + mapName + "]").each(function () {
                if (showView) {
                    // Remove built-in control
                    if (mapControls.view != undefined) {
                        mapquestMap.removeControl(mapControls.view);
                    }
                    // Add custom control
                    mapControls.view = map.navMapControl(this, {});
                    $(this).data("nav", mapControls.view);
                }
            });

            if (showMouseControls) {
                $("[data-wnt-mouseControlContainer=][data-wnt-map-name=" + mapName + "]").each(function () {
                    $(this).data("mouseControlContainer", map.mouseControlContainer(this, {}));
                });

                $("[data-wnt-mouseControl=][data-wnt-map-name=" + mapName + "]").each(function () {
                    $(this).data("mouseControl", map.mouseControl(this, {}));
                });
            }

            $("[data-wnt-pan=][data-wnt-map-name=" + mapName + "]").each(function () {
                if (showZoom) {
                    // Remove built-in control
                    if (mapControls.zoom != undefined) {
                        mapquestMap.removeControl(mapControls.zoom);
                    }
                    // Add custom control
                    mapControls.zoom = map.panZoomMapControl(this, {
                        expandedZoom: $(this).attr("data-wnt-pan-expandedzoom") === "true"
                    });
                    $(this).data("pan", mapControls.zoom);
                }
            });

            $("[data-wnt-poi][data-wnt-map-name=" + mapName + "]").each(function () {
                var poi = this;
                if ($(this).attr("data-wnt-poi") === "mapquest") {
                    var find = map.mapquestFind("jsreqhandler.cfm?sname=spatial.access.mapquest.com&spath=mq&sport=80");
                } else {
                    var find = map.bingFind();
                }

                $(poi).data("poi", find);
                $("[data-wnt-poi-checkbox=]", poi).each(function () {
                    var checkbox = this;
                    var icon = $(this).attr("data-wnt-poi-icon");
                    var value = $(this).val();
                    $(checkbox).click(function () {
                        if ($(checkbox).is(":checked")) {
                            find.add(value, icon);
                        } else {
                            find.remove(value);
                        }
                    });
                });

                $("[data-wnt-poi-textbox=]", poi).each(function () {
                    var textbox = this;
                    var icon = $(this).attr("data-wnt-poi-icon");
                    var oldValue = $(this).val();

                    $(textbox).change(function () {
                        find.remove(oldValue);
                        oldValue = $(textbox).val();
                        find.add(oldValue, icon);
                    });
                });

                $("[data-wnt-poi-select=]", poi).each(function () {
                    var select = this;
                    var icon = $(this).attr("data-wnt-poi-icon");
                    var oldValue = $(this).val();

                    $(select).change(function () {
                        find.remove(oldValue);
                        oldValue = $(select).val();
                        find.add(oldValue, icon);
                    });
                });

                var mapHeight = map.getSize().height;
                var mapWidth = map.getSize().width;
                $("[data-wnt-poi-expand=]", poi).click(function () {
                    map.setSize(mapWidth, mapHeight*2);
                });
                $("[data-wnt-poi-collapse=]", poi).click(function () {
                    map.setSize(mapWidth, mapHeight);
                });

                //modern skin poi externally triggered events
                $("#modernPOIExpand").click(function () {
                    map.setSize(mapWidth, mapHeight * 2);
                });
                $("#modernPOIContract").click(function () {
                    map.setSize(mapWidth, mapHeight);
                });
                $("#modernPOIMapView").click(function () {
                    map.setMapView();
                });
                $("#modernPOIHybridView").click(function () {
                    map.setHybridView();
                });
                $("#modernPOIAerialView").click(function () {
                    map.setSatView();
                });
                $("#modernPOIStreetView").click(function () {
                    map.setStreetView();
                });

            });
        };


        // Load POI Libraries (on poi map toggle)
        this.loadPOILibraries = function () {
            // resize street view on POI Map toggle in the event streetview is the persisted poi map type
            if (googleStreetView != null) {
                google.maps.event.trigger(googleStreetView, "resize");
            }

            if ((eval("typeof VEMap") == "undefined") && $(bingMapNode).attachEvent == undefined){
                this.loadBingLibrary();
            }
        }


        // Load Bing Maps Javascript Libraries
        this.loadBingLibrary = function () {
            var bHead = document.getElementsByTagName("head")[0];
            var bingScript = document.createElement("script");
            bingScript.type = "text/javascript";
            bingScript.src = "http://ecn.dev.virtualearth.net/mapcontrol/mapcontrol.ashx?v=6.2";
            bHead.appendChild(bingScript);
            this.isBingLibraryLoaded();
        };

        // Check to see if Bing Maps Libraries are fully loaded and attachEvent() has been attached on bingMapNode
        this.isBingLibraryLoaded = function () {
            var interval = setInterval(
                function () {
                    if ((eval("typeof VEMap") != "undefined") && (document.getElementById(bingMapNode.id).attachEvent != undefined)) {
                        clearInterval(interval);
                        map.configureBingMap();
                    }
                },
                10
            );
        };



        // Finish configuring the bing Map Node
        this.configureBingMap = function () {
            if (hasHouseView) {
                $(bingMapNode).show();
                bingHouseView = new VEMap(bingMapNode.id);
                bingHouseView.LoadMap(
                    new VELatLong(centerLat, centerLng),
                    houseViewZoomLevel,
                    VEMapStyle.Birdseye
                );
                bingHouseView.HideDashboard();

                // The following line gets the hidden map closer to where it should be
                // so when we do show it the first time, the map doesn't slide as much
                bingHouseView.SetCenter(new VELatLong(centerLat, centerLng));

                // Remove key events except for arrows
                bingHouseView.AttachEvent("onkeydown", function (e) {
                    return ((e.keyCode > 40) || (e.keyCode < 37));
                });
                bingHouseView.AttachEvent("onkeyup", function (e) {
                    return ((e.keyCode > 40) || (e.keyCode < 37));
                });
                bingHouseView.AttachEvent("onkeypress", function(e){
                    return ((e.keyCode > 40) || (e.keyCode < 37));
                });

                bingHouseView.AttachEvent("onendzoom", function (e) {
                    map.onZoomEnd();
                });
                bingHouseView.AttachEvent("onobliquechange", function (e) {
                    map._orientationChanged();
                });

                // Since we don't know when the IsBirdseyeAvalable() is actually returning the correct result we poll it every 500ms
                window.setInterval(function () {
                    map._houseViewClientHandler();
                }, 500);

                // Fix bug with bing maps in firefox 3.5
                if (navigator.userAgent.indexOf("Firefox/3.5") != -1) {
                    bingMapNode.addEventListener("DOMMouseScroll", function (e) {
                            e.stopPropagation();
                            e.preventDefault();
                            e.cancelBubble = false;
                            return false;
                        }, false);
                }
                $(bingMapNode).hide();

            }
        };


        this.reset = function () {
            this.setCenter(startCenterLat, startCenterLng, startMapZoomLevel);
        };


        this.getMapName = function () {
            return mapName;
        }

        // set loading status to trap mouse events
        this.setMapIsLoading = function (status) {
            mapIsLoading = status;
            if (status) {
                if (dragType == "zoom") {
                    $(tmpCursorCoverDiv).css({
                        display: "block",
                        cursor: "progress"
                    });
                }
                // if we're loading, bind preventDefault to IMG tags to prevent map tiles from being dragged on mousedown
                $("img").bind("mousedown", function (e) {
                    e.preventDefault();
                });
            } else {
                $(tmpCursorCoverDiv).css({display: "none"});
                // unbind preventDefault from IMG tags if we're not in drag zoom mode
                if (dragType != "zoom"){
                    $("img").bind("mousedown", function (e) {});
                }
                $("img").bind("mousedown", function (e) {});
            }
        };


        this.setMapView = function () {            
            if (googleStreetView != null) {
                googleStreetView.setVisible(false);
                googleStreetView = null;
            }
            switch (provider) {
                case "mapquest":                  
                    $(bingMapNode).hide();
                    $(mapquestNode).show();
                    mapquestMap.setMapType("map");
                    break;
                case "bing":
                    $(bingMapNode).show();
                    bingHouseView.SetMapStyle(VEMapStyle.Road);
                    break;
            }

            $(mapNode).trigger("enableMouseControls", []);
            this._doMapSync("map");
        };


        this.setOfficeID = function (office_ID) {
            map_office_ID = office_ID;
        };


        this.toggleMouseWheelZoom = function () {
            // only allow mousewheel zoom toggle when in drag/move mode
            if ((dragType == "move") && !$.browser.msie) {
                if (!mouseWheelEnabled) {
                    MQA.withModule("mousewheel", function() {
                        mapquestMap.enableMouseWheelZoom();
                        mapquestMap._mouseZooming = false;
                    });
                    mouseWheelEnabled = true;
                    allowMouseWheel = true;
                    mouseWheelRolloverEnabled = false;
                    this._toggleMouseControl("mouseWheelZoom", true, false);
                } else {
                    MQA.withModule("mousewheel", function() {
                        mapquestMap.enableMouseWheelZoom();
                        mapquestMap._mouseZooming = true;
                    });
                    mouseWheelEnabled = false;
                    allowMouseWheel = false;
                    this._toggleMouseControl("mouseWheelZoom", false, false);
                }
                $(mapNode).trigger("mapControlChanged");
            }
        };


        this.setSatView = function () {
            if (googleStreetView != null) {
                googleStreetView.setVisible(false);
                googleStreetView = null;
            }
            switch (provider) {
                case "mapquest":
                    $(bingMapNode).hide();
                    $(mapquestNode).show();
                    mapquestMap.setMapType("sat");
                    break;
                case "bing":
                    $(bingMapNode).show();
                    bingHouseView.SetMapStyle(VEMapStyle.Aerial);
                    break;
            }
            $(mapNode).trigger("enableMouseControls", []);
            this._doMapSync("sat");
        };


        this.setHybridView = function () {
            if (googleStreetView != null) {
                googleStreetView.setVisible(false);
                googleStreetView = null;
            }
            switch (provider) {
                case "mapquest":
                    $(bingMapNode).hide();
                    $(mapquestNode).show();
                    mapquestMap.setMapType("hyb");
                    break;
                case "bing":
                    $(bingMapNode).show();
                    bingHouseView.SetMapStyle(VEMapStyle.Hybrid);
                    break;
            }
            $(mapNode).trigger("enableMouseControls", []);
            this._doMapSync("hyb");
        };


        this.setStreetView = function () {
            if (hasStreetView && isStreetViewAvailable) {
                $(bingMapNode).hide();
                $(mapquestNode).hide();
                var streetViewOptions = {
                    position: new google.maps.LatLng(centerLat, centerLng),
                    pitch: 10,
                    zoom: 1
                };
                if (googleStreetView == null) {
                    googleStreetView = new google.maps.StreetViewPanorama(document.getElementById("poiMap_map"), streetViewOptions);
                }
                $(mapNode).trigger("disableMouseControls", []);
                this._doMapSync("street");
            }
        };


        this.setHouseView = function () {
            if (this.hasHouseView() && isHouseViewAvailable) {
                $(mapquestNode).hide();
                if (googleStreetView != null) {
                    googleStreetView.setVisible(false);
                    googleStreetView = null;
                }
                $(bingMapNode).show();
                if (provider === "bing") {
                    bingHouseView.SetMapStyle(VEMapStyle.Birdseye);
                }
                $(mapNode).trigger("disableMouseControls", []);
                this._doMapSync("house");
            }
        };


        // Drag-setting

        this.setDragMove = function (persist) {
            if (persist == null) {
                var persist = true;
            }
            // bind preventDefault to IMG tags to prevent map tiles from being dragged on mousedown
            $("img").bind("mousedown", function (e) {
                e.preventDefault();
            });
            dragType = "move";
            mapquestMap._wireDOMEvents(mapquestNode);
            this._toggleMouseControl("dragMove", false, persist);
            $(tmpCursorCoverDiv).css({display: "none"});
        };

        this.setDragZoom = function (persist) {
            if (persist == undefined) {
                var persist = true;
            }
            // remove preventdefault mousedown binding
            $("img").bind("mousedown", function (e) {});
            dragType = "zoom";
            mapquestMap._unwireDOMEvents();
            this._toggleMouseControl("dragZoom", false, persist);
        };


        this.setCenter = function(lat, lng, zoom) {
            switch (provider) {
                case "mapquest":
                    switch (currentView) {
                        case "house":
                            break;
                        case "street":
                            break;
                        default:
                            mapquestMap.setCenter({
                                lat: lat,
                                lng: lng
                            });
                            mapquestMap.setZoomLevel(Number(zoom) + mapquestZoomAdjust);
                            break;
                    }
                    break;
                case "bing":
                    bingHouseView.SetCenterAndZoom(new VELatLong(lat, lng), zoom);
                    break;
            }
        };


        this.getCenter = function () {
            switch (provider) {
                case "mapquest":
                    switch (currentView) {
                        case "house":
                            var centerLatLng = bingHouseView.GetCenter();
                            return { lat: centerLatLng.Latitude, lng: centerLatLng.Longitude };
                            break;
                        case "street":
                            break;
                        default:
                            return mapquestMap.getCenter();
                            break;
                    }
                    break;
                case "bing":
                    var centerLatLng = bingHouseView.GetCenter();
                    return { lat: centerLatLng.Latitude, lng: centerLatLng.Longitude };
                    break;
            }
        };


        this.getBounds = function () {
            switch (provider) {
                case "mapquest":
                    switch (currentView) {
                        case "house":
                            var view = bingHouseView.GetMapView();
                            return {
                                ul: { lat: view.TopLeftLatLong.Latitude, lng: view.TopLeftLatLong.Longitude },
                                lr: { lat: view.BottomRightLatLong.Latitude, lng: view.BottomRightLatLong.Longitude }
                            };
                            break;
                        case "street":
                            break;
                        default:
                            return mapquestMap.getBounds();
                            break;
                    }
                    break;
                case "bing":
                    var view = bingHouseView.GetMapView();
                    return {
                        ul: { lat: view.TopLeftLatLong.Latitude, lng: view.TopLeftLatLong.Longitude },
                        lr: { lat: view.BottomRightLatLong.Latitude, lng: view.BottomRightLatLong.Longitude }
                    };
                    break;
            }
        };


        this.getMapquestMaxZoom = function () {
            return mapquestMaxZoom;
        };


        this.getMapquestMinZoom = function () {
            return mapquestMinZoom;
        };


        // Pan/Zoom/Rotate functions
        /*
            We can't control street view, so we have to use the street view controls.
            Pan/Zoom controls are available on map/sat/hybrid/house view modes.
            Rotate is only available on house view mode
        */

        this.panUp = function () {
            switch (currentView) {
                case "house":
                    bingHouseView.Pan(0, -bingVerticalPan);
                    break;
                case "street":
                    break;
                default:
                    mapquestMap.panNorth(mapquestPan);
                    break;
            }
        };

        this.panDown = function () {
            switch (currentView) {
                case "house":
                    bingHouseView.Pan(0, bingVerticalPan);
                    break;
                case "street":
                    break;
                default:
                    mapquestMap.panSouth(mapquestPan);
                    break;
            }
        };

        this.panRight = function () {
            switch (currentView) {
                case "house":
                    bingHouseView.Pan(bingHorizontalPan, 0);
                    break;
                case "street":
                    break;
                default:
                    mapquestMap.panEast(mapquestPan);
                    break;
            }
        };

        this.panLeft = function () {
            switch (currentView) {
                case "house":
                    bingHouseView.Pan(-bingHorizontalPan, 0);
                    break;
                case "street":
                    break;
                default:
                    mapquestMap.panWest(mapquestPan);
                    break;
            }
        };

        this.zoomIn = function () {
            switch (currentView) {
                case "house":
                    bingHouseView.ZoomIn();
                    break;
                case "street":
                    break;
                default:
                    switch (provider) {
                        case "mapquest":
                            mapquestMap.setZoomLevel(mapquestMap.getZoomLevel() + 1);
                            break;
                        case "bing":
                            bingHouseView.ZoomIn();
                            break;
                    }
                    break;
            }
        };

        this.zoomOut = function () {
            switch (currentView) {
                case "house":
                    bingHouseView.ZoomOut();
                    break;
                case "street":
                    break;
                default:
                    switch (provider) {
                        case "mapquest":
                            mapquestMap.setZoomLevel(mapquestMap.getZoomLevel() - 1);
                            break;
                        case "bing":
                            bingHouseView.ZoomOut();
                            break;
                    }
                    break;
            }
        };

        this.zoomTo = function(zoomLevel) {
            switch (currentView) {
                case "house":
                case "street":
                    break;
                default:
                    switch (provider) {
                        case "mapquest":
                            mapquestMap.setZoomLevel(Number(zoomLevel) + mapquestZoomAdjust);
                            break;
                        case "bing":
                            bingHouseView.SetZoomLevel(zoomLevel);
                            break;
                    }
                    break;
            }
        };

        this.getZoomLevel = function () {
            switch (currentView) {
                case "house":
                case "street":
                    return 1;
                    break;
                default:
                    switch (provider) {
                        case "mapquest":
                            return Number(mapquestMap.getZoomLevel()) - mapquestZoomAdjust;
                            break;
                        case "bing":
                            return bingHouseView.GetZoomLevel();
                            break;
                    }
                    break;
            }
        };

        this.rotateCW = function () {
            if (currentView == "house") {
                switch (bingHouseView.GetBirdseyeScene().GetOrientation()) {
                    case "North":
                        bingHouseView.SetBirdseyeOrientation(VEOrientation.East);
                        break;
                    case "South":
                        bingHouseView.SetBirdseyeOrientation(VEOrientation.West);
                        break;
                    case "West":
                        bingHouseView.SetBirdseyeOrientation(VEOrientation.North);
                        break;
                    case "East":
                        bingHouseView.SetBirdseyeOrientation(VEOrientation.South);
                        break;
                }
            }
        };

        this.rotateCCW = function () {
            if (currentView == "house") {
                switch (bingHouseView.GetBirdseyeScene().GetOrientation()) {
                    case "North":
                        bingHouseView.SetBirdseyeOrientation(VEOrientation.West);
                        break;
                    case "South":
                        bingHouseView.SetBirdseyeOrientation(VEOrientation.East);
                        break;
                    case "West":
                        bingHouseView.SetBirdseyeOrientation(VEOrientation.South);
                        break;
                    case "East":
                        bingHouseView.SetBirdseyeOrientation(VEOrientation.North);
                        break;
                }
            }
        };


        // Add POI methods allow us to add pois to mapquest or bing maps
        /*
            !!!!!!!!!!!! WARNING !!!!!!!!!!!!
            We cannot put POI data obtained from one map provider onto another map provider's map.
            House data from search is fine as that's our own geocode info.
        */

        // Add POI to both MapQuest and Bing
        this.addPoi = function (poi) {
            this.addMapPoi(poi);
            this.addHouseViewPoi(poi);
        };

        // Remove POI from both MapQuest and Bing
        this.removePoi = function (poi) {
            this.removeMapPoi(poi);
            this.removeHouseViewPoi(poi);
        };

        this.addMapPoi = function (poi) {
            switch (provider) {
                case "mapquest":
                    mapquestMap.addShape(poi.getMapquestPoi());
                    break;
                case "bing":
                    this.addHouseViewPoi(poi);
                    break;
            }
        };

        this.removeMapPoi = function (poi) {
            switch (provider) {
                case "mapquest":
                    mapquestMap.removeShape(poi.getMapquestPoi());
                    // This is a hack to work around a bug in MapQuest where the info box doesn't show up if you read a POI.
                    poi.mapquestPoi = null;
                    break;
                case "bing":
                    this.removeHouseViewPoi(poi);
                    break;
            }
        };


        this.addHouseViewPoi = function (poi) {
            if ((currentView == "house") || (provider === "bing")) {
                // Bing throws an error if this POI has already been added, so we just catch it and ignore it.
                try {
                    bingHouseView.AddShape(poi.getBingPoi());
                } catch (e) {
                }
                poi.getBingPoi().Show();
            }
        };

        // There's a bug in the bing bird's-eye where you can't remove a shape you've added to the map, so we'll just hide it and then show it if it's re-added.
        this.removeHouseViewPoi = function (poi) {
            if ((currentView == "house") || (provider === "bing")) {
                poi.getBingPoi().Hide();
            }
        };


        // Resize the map
        this.setSize = function (w, h) {
            width = w;
            height = h;
            $(mapNode).css({ width: width, height: height });
            $(bingMapNode).css({ width: width, height: height });
            $(mapquestNode).css({ width: width, height: height });
            if (hasStreetView && (googleStreetView != null)) {
                google.maps.event.trigger(googleStreetView, "resize");
            }
            if (hasHouseView || (provider == "bing")) {
                bingHouseView.Resize(width, height);
            }
            if (provider == "mapquest") {
                mapquestMap.setSize(new MQA.Size(width, height));
            }
            $(mapNode).trigger("shiftMouseControlContainer", [h]);
        };

        this.getSize = function () {
            return { width: width, height: height };
        };


        this.show = function () {
            $(mapNode).show();
        };


        this.hide = function () {
            $(mapNode).hide();
        };


        this.doBingFind = function (what, callback) {
            if (provider === "bing") {
                bingHouseView.Find(what, null, VEFindType.Businesses, null, 0, 10, false, false, false, false, callback);
            }
        };


        this.canZoomIn = function () {
            switch (currentView) {
                case "house":
                    if (bingHouseView.GetZoomLevel() >= 2) {
                        return false;
                    } else {
                        return true;
                    }
                    break;
                case "street":
                    break;
                default:
                    switch (provider) {
                        case "mapquest":
                            if (map.getZoomLevel() >= mapquestMaxZoom) {
                                return false;
                            } else {
                                return true;
                            }
                            break;
                        case "bing":
                            if (bingHouseView.GetZoomLevel() >= mapquestMaxZoom) {
                                return false;
                            } else {
                                return true;
                            }
                            break;
                    }
                    break;
            }
        };


        this.canZoomOut = function () {
            switch (currentView) {
                case "house":
                    if (bingHouseView.GetZoomLevel() <= 1) {
                        return false;
                    } else {
                        return true;
                    }
                    break;
                case "street":
                    break;
                default:
                    switch (provider) {
                        case "mapquest":
                            if (map.getZoomLevel() <= mapquestMinZoom) {
                                return false;
                            } else {
                                return true;
                            }
                            break;
                        case "bing":
                            if (bingHouseView.GetZoomLevel() <= mapquestMinZoom) {
                                return false;
                            } else {
                                return true;
                            }
                            break;
                    }
                    break;
            }
        };


        this.zoomToRect = function (tlLat, tlLng, brLat, brLng) {
            switch (provider) {
                case "mapquest":
                    var ul = new MQA.LatLng(tlLat, tlLng);
                    var lr = new MQA.LatLng(brLat, brLng);
                    var zoomRect = new MQA.RectLL();
                    zoomRect.ul = ul;
                    zoomRect.lr = lr;
                    mapquestMap.zoomToRect(zoomRect);
                    break;
                case "bing":
                    bingHouseView.SetMapView([new VELatLong(tlLat, tlLng), new VELatLong(brLat, brLng)]);
                    break;
            }
        };


        this._doMapSync = function (view) {
            currentView = view;
            $(mapNode).trigger("currentView", [view]);
            this._orientationChanged();
        };

        this._toggleMouseControl = function (controlType, status, persist) {
            $(mapNode).trigger("toggleMouseControl", [controlType, status, persist]);
        };

        this._houseViewClientHandler = function () {
            if (bingHouseView.IsBirdseyeAvailable() != isHouseViewAvailable) {
                isHouseViewAvailable = bingHouseView.IsBirdseyeAvailable();
                $(mapNode).trigger("isHouseViewAvailable", [bingHouseView.IsBirdseyeAvailable()]);
            }
        };

        this._streetViewClientHandler = function (latLng) {
            if (latLng == null) {
                isStreetViewAvailable = false;
                $(mapNode).trigger("isStreetViewAvailable", [false]);
                // Hide modern skin external "street view" toggle if street view is not available for this property.
                if (typeof $("#modernPOIStreetView") != "undefined") {
                    $("#modernPOIStreetView").hide();
                }
            } else {
                isStreetViewAvailable = true;
                $(mapNode).trigger("isStreetViewAvailable", [true]);
            }
        };

        this._dragZoomMouseDown = function (evt) {
            this.mouseIsDown = true;

            //reset width, height and aspect ratio in case map size has changed (i.e. poi map expand/collapse)
            width = $(mapNode).width();
            height = $(mapNode).height();
            this.aspectRatio = height / width;

            $(tmpCursorCoverDiv).css({
                top: 0 + "px",
                left: 0 + "px",
                height: height + "px",
                width: width + "px"
            });

            // reset offsets to see if search map placement moved (i.e. property features expanded)
            if (mapName == "search") {
                this.coverOffX = parseInt($(mapNode).position().left);
                this.coverOffY = parseInt($(mapNode).position().top);
            } else {
                this.coverOffX = parseInt($(mapNode).offset().left);
                this.coverOffY = parseInt($(mapNode).offset().top);
            }

            $("blockDiv").innerHTML = "";

            this.startY = evt.pageY;
            this.startX = evt.pageX;
            this.startX -= this.coverOffX;
            this.startY -= this.coverOffY;

            $(tmpCursorCoverDiv).css({
                display: "block",
                cursor: "se-resize"
            });
            $(tmpBlockDiv).css({
                display: "block",
                left: this.startX + "px",
                top: this.startY + "px",
                height: "1px",
                width: "1px"
            });

        };



        this._dragZoomMouseMove = function (evt) {
            if (this.mouseIsDown) {

                var y = evt.pageY;
                var x = evt.pageX;

                x -= this.coverOffX;
                y -= this.coverOffY;

                if (this.startX > x) {
                    if (x < 1) {
                        this.drawLeft = 1;
                        this.drawWidth = this.startX-1;
                    } else {
                        this.drawLeft = x;
                        this.drawWidth = this.startX - x;
                    }
                    var bw = startX - x;
                } else {
                    this.drawLeft = this.startX;
                    this.drawWidth = x - this.startX;
                    var bw = x - this.startX;
                }

                // calculate Y based upon aspect Ratio
                var aY = this.drawWidth * this.aspectRatio;

                if (this.startY > y) {

                    if (y < 0) {
                        this.drawTop = 1;
                        this.drawHeight = aY;
                    } else {
                        this.drawTop = this.startY - aY;
                        this.drawHeight = aY;
                    }
                    var bh = this.startY - y;
                } else {
                    this.drawTop = this.startY;
                    this.drawHeight = aY;
                    var bh = y - this.startY;
                }

                $(tmpBlockDiv).css({
                    left: this.drawLeft + 'px',
                    top: this.drawTop + 'px',
                    width: this.drawWidth + 'px',
                    height: this.drawHeight + 'px'
                });

            }
        };

        this._dragZoomMouseUp = function (evt) {

            this.mouseIsDown = false;

            var y = evt.pageY;
            var x = evt.pageX;
            this.startX += this.coverOffX;
            this.startY += this.coverOffY;


            if (x > this.startX) {
                this.endX = this.startX + this.drawWidth;
            } else {
                this.endX = this.startX - this.drawWidth;
            }

            if (y > this.startY) {
                this.endY = this.startY + this.drawHeight;
            } else {
                this.endY = this.startY - this.drawHeight;
            }

            var xDiff = Math.abs(this.endX - this.startX);

            var inBounds = false;
            var tooSmallToZoom = true;

            if (xDiff > 35) {

                if ((Math.abs(this.startX - this.endX) < 8) && (Math.abs(this.startY - this.endY) < 8)) {
                    //alert('fringe case?');
                    var startLL = mapquestMap.pixToLL(new MQA.Point(this.startX, this.startY));
                    var endLL = mapquestMap.pixToLL(new MQA.Point(this.endX, this.endY));
                } else {
                    var startLL = mapquestMap.pixToLL(new MQA.Point(startX-coverOffX,startY-coverOffY));
                    var endLL = mapquestMap.pixToLL(new MQA.Point(endX-coverOffX,endY-coverOffY));
                }

                this.zoomToRect(startLL.getLatitude(), startLL.getLongitude(), endLL.getLatitude(), endLL.getLongitude());
                tooSmallToZoom = false;
                inBounds = true;
            }

            this._dragZoomReset(inBounds, tooSmallToZoom);

        };

        // reset click/drag to zoom bounding box
        this._dragZoomReset = function (inBounds, tooSmallToZoom) {

            var map = this;

            if (dragType == "zoom") {

                startX      = 0;
                startY      = 0;
                endX        = 0;
                endY        = 0;
                drawLeft    = 0;
                drawWidth   = 0;
                drawTop     = 0;
                drawHeight  = 0;
                mouseIsDown = false;

                // If (mouseUp after drag), zoom bounding box and reset
                if (inBounds && !mapIsLoading) {

                    var mapWidth = mapquestMap.getSize().width + 1;
                    var mapHeight = mapquestMap.getSize().height + 1;

                    var animateZoom = true;
                    var br = $.browser;

                    this.setMapIsLoading(true);

                    if ((br.msie != undefined) && (br.version.slice(0, 1) == "8")) {
                        animateZoom = false;
                    }

                    if (animateZoom) {
                        $(tmpBlockDiv).animate({
                            top: "0px",
                            left: "0px",
                            width: mapWidth + "px",
                            height: mapHeight + "px",
                            opacity: 0.2,
                            filter: "alpha(opacity=20)"
                        }, 200, function () {
                            $(tmpBlockDiv).css({
                                display: "none",
                                top: "0px",
                                left: "0px",
                                width: "0px",
                                height: "0px",
                                opacity: 0.6,
                                filter: "alpha(opacity=60)"
                            });
                            $(tmpCursorCoverDiv).css({display: "none"});
                        });
                    } else {
                        $(tmpBlockDiv).css({display: "none"});
                        $(tmpCursorCoverDiv).css({display: "none"});
                    }

                // otherwise mouse is off map (or over GUI controls)... fade out bounding box and reset
                } else {

                    var fadeOutSpeed = 700;
                    if ((tooSmallToZoom != undefined) && tooSmallToZoom) {
                        fadeOutSpeed = 0;
                    }

                    $(tmpBlockDiv).fadeOut(fadeOutSpeed, function () {
                        $(tmpBlockDiv).css({
                            display: "none",
                            top: "0px",
                            left: "0px",
                            width: "0px",
                            height: "0px",
                            opacity: 0.6,
                            filter: "alpha(opacity=60)"
                        });
                        $(tmpCursorCoverDiv).css({display: "none"});
                    });

                }

                this.setMapIsLoading(false);

                // re-bind preventDefault to IMG tags to prevent map tiles from being dragged on mousedown, as we lose the binding on zoomToRect
                $("img").bind("mousedown", function (e) {
                  e.preventDefault();
                });
            }

        };



        this._orientationChanged = function (evt) {
            if (currentView == "house") {
                switch(bingHouseView.GetBirdseyeScene().GetOrientation()) {
                    case VEOrientation.North:
                        mapOrientation = "n";
                        break;
                    case VEOrientation.South:
                        mapOrientation = "s";
                        break;
                    case VEOrientation.West:
                        mapOrientation = "w";
                        break;
                    case VEOrientation.East:
                        mapOrientation = "e";
                        break;
                }
            } else {
                mapOrientation = "n";
            }
            $(mapNode).trigger("mapOrientation", [mapOrientation]);
        };


        this.controlContainer = function (baseNode) {
            var controlId = mapTracksGlobals.CONTROL_ID++;
            var baseAnimateTime = 800;
            var expandAnimateTime = 400;
            // TODO: add expand node
            var expandNode = document.createElement("div");
            $(expandNode).addClass("wntControlExpand").click(function () {
                $(mapNode).trigger("showControls");
            });
            $(baseNode).parent().append(expandNode);
            $(mapNode).bind("hideControls", function () {
                $(baseNode).animate(
                    {left: -400},
                    baseAnimateTime,
                    function () {
                        $(expandNode).animate({left: 0}, expandAnimateTime);
                    }
                );
            });
            $(mapNode).bind("showControls", function () {
                $(expandNode).animate(
                    {left: -71},
                    expandAnimateTime,
                    function () {
                        $(baseNode).animate({left: 0}, baseAnimateTime);
                    }
                );
            });
        };


        this.navMapControl = function (baseNode) {
            var map       = this;
            var controlId = mapTracksGlobals.CONTROL_ID++;

            var closeNode = document.createElement("div");
            $(closeNode).addClass("wntStreetCloseButton")
                .click(function () {
                    map.setMapView();
                })
                .hide();
            $(baseNode.parentNode).append(closeNode);

            var navBackgroundNode = document.createElement("span");
            $(navBackgroundNode).attr("id", "wntNavBackground" + controlId).addClass("wntNavBackground");
            $(baseNode).append(navBackgroundNode);

            var navStartNode = document.createElement("span");
            $(navStartNode).attr("id", "wntNavStart" + controlId).addClass("wntNavStart");
            $(baseNode).append(navStartNode);

            var mapViewNode = document.createElement("span");
            $(mapViewNode).attr("id", "wntNavMapView" + controlId)
                .addClass("wntNavMenuItem")
                .html("Map")
                .click(function () {
                    map.setMapView();
                });
            $(baseNode).append(mapViewNode);

            var hybridViewNode = document.createElement("span");
            $(hybridViewNode)
                .attr("id", "wntNavHybridView" + controlId)
                .addClass("wntNavMenuItem")
                .html("Hybrid")
                .click(function () {
                    map.setHybridView();
                });
            $(baseNode).append(hybridViewNode);

            var satViewNode = document.createElement("span");
            $(satViewNode)
                .attr("id", "wntNavSatView" + controlId)
                .addClass("wntNavMenuItem")
                .html("Aerial")
                .click(function () {
                    map.setSatView();
                });
            $(baseNode).append(satViewNode);

            if (this.hasHouseView()) {
                var houseViewNode = document.createElement("span");
                $(houseViewNode)
                    .attr("id", "wntNavHouseView" + controlId)
                    .addClass("wntNavMenuItem")
                    .html("House View")
                    .click(function () {
                        map.setHouseView();
                    });
                $(baseNode).append(houseViewNode);
            }

            if (this.hasStreetView()) {
                var streetViewNode = document.createElement("span");
                $(streetViewNode)
                    .attr("id", "wntNavStreetView" + controlId)
                    .addClass("wntNavMenuItem")
                    .html("Street View")
                    .click(function () {
                        map.setStreetView();
                    });
                $(baseNode).append(streetViewNode);
            }

            var navEndNode = document.createElement("span");
            $(navEndNode)
                .attr("id", "wntNavEnd" + controlId)
                .addClass("wntNavEnd")
                .click(function () {
                    $(mapNode).trigger("hideControls");
                });
            $(baseNode).append(navEndNode);

            $(mapNode).bind("isStreetViewAvailable", function (e, available) {
                _isStreetViewAvailable(available);
            });
            $(mapNode).bind("isHouseViewAvailable", function (e, available) {
                _isHouseViewAvailable(available);
            });
            $(mapNode).bind("currentView", function (e, view) {
                _currentView(view);
            });
            $(mapNode).bind("mapControlChanged", function () {
                _mapControlChanged();
            });

            var _isStreetViewAvailable = function (available) {
                if (available) {
                    $(streetViewNode).removeClass("notAvailable");
                } else {
                    $(streetViewNode).addClass("notAvailable");
                }
            };

            var _isHouseViewAvailable = function (available) {
                if (available) {
                    $(houseViewNode).removeClass("notAvailable");
                } else {
                    $(houseViewNode).addClass("notAvailable");
                }
            };

            var _mapControlChanged = function () {};

            var _currentView = function (view) {
                if (view == "house") {
                    $(houseViewNode).addClass("selected");
                } else {
                    $(houseViewNode).removeClass("selected");
                }
                if (view == "street") {
                    $(closeNode).show();
                    $(baseNode).hide();
                } else {
                    $(closeNode).hide();
                    $(baseNode).show();
                }
                if (view == "map") {
                    $(mapViewNode).addClass("selected");
                } else {
                    $(mapViewNode).removeClass("selected");
                }
                if (view == "sat") {
                    $(satViewNode).addClass("selected");
                } else {
                    $(satViewNode).removeClass("selected");
                }
                if (view == "hyb") {
                    $(hybridViewNode).addClass("selected");
                } else {
                    $(hybridViewNode).removeClass("selected");
                }
            };

            // Manually trigger events to set initial values
            _currentView(this.getCurrentView());
            _isStreetViewAvailable(this.isStreetViewAvailable());
            _isHouseViewAvailable(this.isHouseViewAvailable());

            return this;
        };


        this.mouseControlContainer = function (baseNode) {
            var controlId         = mapTracksGlobals.CONTROL_ID++;
            var baseAnimateTime   = 800;
            var expandAnimateTime = 400;
            var mapHeight         = 235;

            _shiftMouseControlContainer(mapHeight);
            $(mapNode).bind("shiftMouseControlContainer", function (e, h) {
                _shiftMouseControlContainer(h);
            });
            $(mapNode).bind("disableMouseControls", function (e) {
                _disableMouseControls();
            });
            $(mapNode).bind("enableMouseControls", function (e) {
                _enableMouseControls();
            });

            // TODO: add expand node
            var mouseControlExpandNode = document.createElement("div");
            $(mouseControlExpandNode)
                .addClass("wntMouseControlExpand")
                .click(function () {
                    $(mapNode).trigger("showControls");
                });
            $(baseNode).parent().append(mouseControlExpandNode);

            $(mapNode).bind("hideControls", function () {
                $(baseNode).animate(
                    {left: -400},
                    baseAnimateTime,
                    function () {
                        $(mouseControlExpandNode).animate({left: 0}, expandAnimateTime);
                    }
                );
            });

            $(mapNode).bind("showControls", function () {
                $(mouseControlExpandNode).animate(
                    {left: -71},
                    expandAnimateTime,
                    function() {
                        $(baseNode).animate({left: 0}, baseAnimateTime);
                    }
                );
            });

            var _shiftMouseControlContainer = function (h) {
                var containerTop = h - 25;
                if (mapNode.getMapName() == "poi") {
                    $(baseNode).css({top: containerTop + "px"});
                }
            };

            var _disableMouseControls = function () {
                $(baseNode).hide();
            };

            var _enableMouseControls = function () {
                $(baseNode).show();
            };

        };


        this.mouseControl = function (baseNode) {
            var map       = this;
            var controlId = mapTracksGlobals.CONTROL_ID++;

            // mouse control start
            var mouseControlBackgroundNode = document.createElement('div');
            $(mouseControlBackgroundNode).attr("id", "wntMouseControlBackground" + controlId).addClass("wntMouseControlBackground");
            $(baseNode).append(mouseControlBackgroundNode);

            var mouseControlStartNode = document.createElement("div");
            $(mouseControlStartNode).attr("id", "wntMouseControlStart" + controlId).addClass("wntMouseControlStart");
            $(baseNode).append(mouseControlStartNode);

            // click and drag label
            var clickDragLabel = document.createElement("div");
            $(clickDragLabel)
                .attr("id", "wntClickDragLabel" + controlId)
                .addClass("wntMouseMenuItem")
                .css({
                    cursor: "default",
                    borderLeft: "0px"
                });
            $(baseNode).append(clickDragLabel);

            var clickDragLabelText = document.createElement("div");
            $(clickDragLabelText)
                .attr("id", "wntClickDragLabelText" + controlId)
                .addClass("wntMouseControlContent")
                .html("Click & Drag to: ");
            $(clickDragLabel).append(clickDragLabelText);

            // mouse zoom control
            var mouseZoomControl = document.createElement("div");
            $(mouseZoomControl)
                .attr("id", "wntMouseZoomControl" + controlId)
                .addClass("wntMouseMenuItem")
                .click(function () {
                    map.setDragZoom();
                });
            $(baseNode).append(mouseZoomControl);

            var mouseZoomText = document.createElement("div");
            $(mouseZoomText).attr("id", "wntMouseZoomText" + controlId).addClass("wntMouseControlContent").html("Zoom");
            $(mouseZoomControl).append(mouseZoomText);

            // mouse move control
            var mouseMoveControl = document.createElement("div");
            $(mouseMoveControl)
                .attr("id", "wntmouseMoveControl" + controlId)
                .addClass("wntMouseMenuItem")
                .click(function () {
                    map.setDragMove();
                });
            $(baseNode).append(mouseMoveControl);

            var mouseMoveText = document.createElement("div");
            $(mouseMoveText).attr("id", "wntMouseMoveText" + controlId).addClass("wntMouseControlContent").html("Move");
            $(mouseMoveControl).append(mouseMoveText).addClass("selected");

            // If not IE, add mouse wheel zoom control
            if (!$.browser.msie) {

                // Spacer Node
                var spacerNode = document.createElement("span");
                $(spacerNode).attr("id", "spacerNode" + controlId).addClass("wntMouseMenuSpacer").html(" ");
                $(baseNode).append(spacerNode);

                // mouse wheel zoom control
                var mouseWheelZoomNode = document.createElement("div");
                $(mouseWheelZoomNode)
                    .attr("id", "wntMouseWheelZoom" + controlId)
                    .addClass("wntMouseMenuItem")
                    .click(function () {
                        map.toggleMouseWheelZoom();
                    });
                $(baseNode).append(mouseWheelZoomNode);

                var mouseWheelZoomCheckbox = document.createElement("img");
                $(mouseWheelZoomCheckbox)
                    .attr({
                        id: "wntMouseWheelZoomCheckbox" + controlId,
                        src: "/2_5/images/map/box_unchecked.png"
                    })
                    .css({
                        height: "13px",
                        width: "13px",
                        marginTop: "-2px"
                    });
                $(mouseWheelZoomNode).append(mouseWheelZoomCheckbox);

                var mouseWheelZoomText = document.createElement("div");
                $(mouseWheelZoomText)
                    .attr("id", "wntMouseWheelZoomText" + controlId)
                    .addClass("wntMouseControlContent")
                    .html(" Mouse Wheel Zoom");
                $(mouseWheelZoomText).css({ paddingTop: "0px" });
                $(mouseWheelZoomNode).append(mouseWheelZoomText);

            }

            var mouseControlEndNode = document.createElement("span");
            $(mouseControlEndNode)
                .attr("id", "wntMouseControlEnd" + controlId)
                .addClass("wntNavEnd")
                .click(function () {
                    $(mapNode).trigger("hideControls");
                });
            $(baseNode).append(mouseControlEndNode);

            $(mapNode).bind("toggleMouseControl", function (e, controlType, status, persist) {
                _toggleMouseControl(controlType, status, persist);
            });

            var _toggleMouseControl = function (controlType, status, persist) {
                switch (controlType) {
                    case "mouseWheelZoom":
                        if (!status) {
                            $(mouseWheelZoomNode).removeClass("selected");
                            $(mouseWheelZoomCheckbox).attr("src", "/2_5/images/map/box_unchecked.png");
                        } else {
                            $(mouseWheelZoomNode).addClass("selected");
                            $(mouseWheelZoomCheckbox).attr("src", "/2_5/images/map/box_checked.png");
                        }
                        break;
                    case "dragMove":
                        $(mouseMoveControl).addClass("selected");
                        $(mouseZoomControl).removeClass("selected");
                        $(mouseWheelZoomNode).css({
                            opacity: 1.0,
                            filter: "alpha(opacity=100)"
                        });
                        if (persist){
                            $(mapNode).trigger("mapControlChanged");
                        }
                        break;
                    case "dragZoom":
                        $(mouseZoomControl).addClass("selected");
                        $(mouseMoveControl).removeClass("selected");
                        $(mouseWheelZoomNode).css({
                            opacity: 0.4,
                            filter: "alpha(opacity=40)"
                        });
                        if (persist) {
                            $(mapNode).trigger("mapControlChanged");
                        }
                        break;
                }
            };

            return this;
        };


        this.panZoomMapControl = function (baseNode, params) {
            var map = this;
            var expandedZoom = getParam(params["expandedZoom"], false);
            var controlId = mapTracksGlobals.CONTROL_ID++;

            var _zoomButtonClick = function (zoomLevel) {
                return function () {
                    map.zoomTo(zoomLevel);
                };
            };

            var _currentView = function (view) {
                switch (view) {
                    case "street":
                        $(baseNode).hide();
                        break;
                    case "house":
                        $(baseNode).show();
                        $(zoomContainerNode).show();
                        $(zoomBarNode).hide();
                        $(rotateContainerNode).show();
                        break;
                    default:
                        $(zoomContainerNode).show();
                        if (expandedZoom) {
                            $(zoomBarNode).show();
                        } else {
                            $(zoomBarNode).hide();
                        }
                        $(baseNode).show();
                        $(rotateContainerNode).hide();
                        break;
                }
                _mapZoomEnd();
            };

            var _mapOrientation = function (orientation) {
                $(panBackgroundNode)
                    .removeClass("wntNorth")
                    .removeClass("wntSouth")
                    .removeClass("wntWest")
                    .removeClass("wntEast");
                switch (orientation) {
                    case "n":
                        $(panBackgroundNode).addClass("wntNorth");
                        break;
                    case "s":
                        $(panBackgroundNode).addClass("wntSouth");
                        break;
                    case "w":
                        $(panBackgroundNode).addClass("wntWest");
                        break;
                    case "e":
                        $(panBackgroundNode).addClass("wntEast");
                        break;
                }
            };

            var _mapZoomEnd = function () {
                if (map.canZoomIn()) {
                    $(zoomInNode).removeClass("disabled");
                } else {
                    $(zoomInNode).addClass("disabled");
                }
                if (map.canZoomOut()){
                    $(zoomOutNode).removeClass("disabled");
                } else {
                    $(zoomOutNode).addClass("disabled");
                }
                for (var x = map.getMapquestMaxZoom(); x >= map.getMapquestMinZoom(); x--) {
                    $(zoomBarNode).removeClass("wntZoomLevel_" + x);
                }
                $(zoomBarNode).addClass("wntZoomLevel_" + map.getZoomLevel());
            };

            var panBackgroundNode = document.createElement("div");
            $(panBackgroundNode)
                .attr("id", "wntPanBackground" + controlId)
                .addClass("wntPanBackground")
                .addClass("wntNorth");
            $(baseNode).append(panBackgroundNode);

            var panUpNode = document.createElement("div");
            $(panUpNode)
                .attr("id", "wntPanUp" + controlId)
                .addClass("wntPanUp")
                .click(function () {
                    map.panUp();
                });
            $(panBackgroundNode).append(panUpNode);

            var panDownNode = document.createElement("div");
            $(panDownNode)
                .attr("id", "wntPanDown" + controlId)
                .addClass("wntPanDown")
                .click(function () {
                    map.panDown();
                });
            $(panBackgroundNode).append(panDownNode);

            var panLeftNode = document.createElement("div");
            $(panLeftNode)
                .attr("id", "wntPanLeft" + controlId)
                .addClass("wntPanLeft")
                .click(function () {
                    map.panLeft();
                });
            $(panBackgroundNode).append(panLeftNode);

            var panRightNode = document.createElement("div");
            $(panRightNode)
                .attr("id", "wntPanRight" + controlId)
                .addClass("wntPanRight")
                .click(function () {
                    map.panRight();
                });
            $(panBackgroundNode).append(panRightNode);

            // Create Zoom & Rotate container
            var zrContainerNode = document.createElement("div");
            $(zrContainerNode).attr("id", "wntZRContainer" + controlId).addClass("wntZRContainer");
            $(baseNode).append(zrContainerNode);

            var zrBackgroundNode = document.createElement("div");
            $(zrBackgroundNode).attr("id", "wntZRBackground" + controlId).addClass("wntZRBackground");
            $(zrContainerNode).append(zrBackgroundNode);

            var zrTopNode = document.createElement("div");
            $(zrTopNode).attr("id", "wntZRTop" + controlId).addClass("wntZRTop");
            $(zrContainerNode).append(zrTopNode);

            var zoomContainerNode = document.createElement("div");
            $(zoomContainerNode).attr("id", "wntZoomContainer" + controlId).addClass("wntZoomContainer");
            $(zrContainerNode).append(zoomContainerNode);

            var rotateContainerNode = document.createElement("div");
            $(rotateContainerNode).attr("id", "wntRotateContainer" + controlId).addClass("wntRotateContainer");
            $(zrContainerNode).append(rotateContainerNode);

            var zoomRotateBottom = document.createElement("div");
            $(zoomRotateBottom).attr("id", "wntZoomRotateBottom" + controlId).addClass("wntZoomRotateBottom");
            $(zrContainerNode).append(zoomRotateBottom);

            // Create Zoom controls and attach click events
            var zoomInNode = document.createElement("div");
            $(zoomInNode)
                .attr("id", "wntZoomIn" + controlId)
                .addClass("wntZoomIn")
                .click(function () {
                    map.zoomIn();
                });
            $(zoomContainerNode).append(zoomInNode);

            var zoomBarNode = document.createElement("div");
            $(zoomBarNode).attr("id", "wntZoomBar" + controlId).addClass("wntZoomBar");
            if (expandedZoom) {
                $(zoomBarNode).show();
                for (var x = map.getMapquestMaxZoom(); x >= map.getMapquestMinZoom(); x--) {
                    var zoomButtonNode = document.createElement("div");
                    $(zoomButtonNode)
                        .addClass("wntZoomButton")
                        .addClass("wntZoomButton_" + x)
                        .click(_zoomButtonClick(x));
                    $(zoomBarNode).append(zoomButtonNode);
                }
            } else {
                $(zoomBarNode).hide();
            }
            $(zoomContainerNode).append(zoomBarNode);

            var zoomOutNode = document.createElement("div");
            $(zoomOutNode)
                .attr("id", "wntZoomOut" + controlId)
                .addClass("wntZoomOut")
                .click(function () {
                    map.zoomOut();
                });
            $(zoomContainerNode).append(zoomOutNode);

            var rotateCWNode = document.createElement("div");
            $(rotateCWNode)
                .attr("id", "wntRotateCW" + controlId)
                .addClass("wntRotateCW")
                .click(function () {
                    map.rotateCW();
                });
            $(rotateContainerNode).append(rotateCWNode);

            var rotateCCWNode = document.createElement("div");
            $(rotateCCWNode)
                .attr("id", "wntRotateCCW" + controlId)
                .addClass("wntRotateCCW")
                .click(function () {
                    map.rotateCCW();
                });
            $(rotateContainerNode).append(rotateCCWNode);

            // Manually call events to init controls
            _currentView(map.getCurrentView());
            _mapOrientation(map.getMapOrientation());

            $(mapNode).bind("currentView", function (e, view) {
                _currentView(view);
            });
            $(mapNode).bind("mapOrientation", function (e, orientation) {
                _mapOrientation(orientation);
            });
            $(mapNode).bind("zoomEnd", function (e) {
                _mapZoomEnd();
            });

            return this;
        };


        // Used for Bing
        this.bingFind = function (params) {
            var map = this;
            var findFunc = function (value, callback) {
                map.doBingFind(value, _bingResults);
                var _bingResults = function (shapeLayer, findResults) {
                    var results = [];
                    $(findResults).each(function () {
                        results.push({
                            name: this.Name,
                            lat: this.LatLong.Latitude,
                            lng: this.LatLong.Longitude
                        });
                    });
                    callback(results);
                };
            };
            return this.find(findFunc);
        };


        this.mapquestFind = function (searchUrl, params) {
            var map = this;
            var findFunc = function (value, callback) {
                if (value === "") {
                    this._mapquestResults("", callback);
                } else {
                    var zoomLevel = map.getZoomLevel();
                    var searchRadius = Math.pow(0.54, zoomLevel - 14) / 3; // previously 0.6595012271388594
                    var maxResults = parseInt(searchRadius * 22); // previously 10
                    if (maxResults < 40) {
                        maxResults = 40
                    };
                    if (maxResults > 150) {
                        maxResults = 150
                    };
                    var xml = '<?xml version="1.0" encoding="ISO-8859-1"?><Search Version="0"><RadiusSearchCriteria><MaxMatches>' + maxResults + '</MaxMatches><Radius>' + searchRadius + '</Radius><CenterLatLng><Lat>' + map.getCenter().lat + '</Lat><Lng>' + map.getCenter().lng + '</Lng></CenterLatLng></RadiusSearchCriteria>\n<CoverageName></CoverageName><DBLayerQueryCollection Count="1"><DBLayerQuery><LayerName>MQA.NTPois</LayerName><ExtraCriteria>facility = ' + value + '</ExtraCriteria></DBLayerQuery></DBLayerQueryCollection><FeatureCollection Version="0" Count="0"></FeatureCollection><DTCollection Count="0"></DTCollection><Authentication Version="2"><TransactionInfo></TransactionInfo></Authentication></Search>'

                    $.post(searchUrl, xml, function (response) {_mapquestResults(response, callback);}, "xml");
                }
            };

            var _mapquestResults = function (xml, callback) {
                var results = [];
                $(xml).find("SearchResponse").find("FeatureCollection").find("PointFeature").each(function() {
                    results.push({
                        name: $(this).find("Name").text(),
                        lat:  $(this).find("CenterLatLng").find("Lat").text(),
                        lng:  $(this).find("CenterLatLng").find("Lng").text()
                    });
                });
                callback(results);
            };

            return this.find(findFunc);
        };



        this.find = function (findFunc, params) {
            var self      = {};
            var findQueue = [];
            var searching = false;
            var findCache = {};

            self.add = function (value, icon) {
                if (searching) {
                    findQueue.push({action: "add", value: value, icon: icon});
                } else {
                    _doAdd(value, icon);
                }
            };

            self.remove = function (value) {
                if (searching){
                    findQueue.push({action: "remove", value: value});
                } else {
                    _doRemove(value);
                }
            };

            var _doAdd = function (value, icon) {
                searching = true;
                findFunc(value, function (findResults) {
                    var mapIcon = map.mapIcon(icon, 22, 24);
                    findCache[value] = [];
                    $(findResults).each(function () {
                        var poi = map.poi(this.lat, this.lng, mapIcon, this.name);
                        map.addPoi(poi);
                        findCache[value].push(poi);
                    });
                    _queueNext();
                });
            };

            var _doRemove = function (value) {
                $(findCache[value]).each(function () {
                    map.removePoi(this);
                });
                _queueNext();
            };

            var _queueNext = function () {
                if (findQueue.length > 0) {
                    var request = findQueue.shift();
                    if (request.action === "add") {
                        _doAdd(request.value, request.icon);
                    } else {
                        _doRemove(request.value);
                    }
                } else {
                    searching = false;
                }
            };

            return self;
        };


        this.mapIcon = function (img, width, height) {
            var self          = {};
            var _mapquestIcon = null;
            var _bingIcon     = null;

            var _createMapquestIcon = function () {
                _mapquestIcon = new MQA.Icon(img, width, height);
                return _mapquestIcon;
            };

            var _createBingIcon = function () {
                _bingIcon = '<img src="' + img + '" />';
                getBingIcon = _getBingIcon;
                return _bingIcon;
            };

            self.getMapquestIcon = function () {
                if (_mapquestIcon == null) {
                    _mapquestIcon = _createMapquestIcon();
                }
                return _mapquestIcon;
            };

            self.getBingIcon = function () {
                if (_bingIcon == null) {
                    _bingIcon = _createBingIcon();
                }
                return _bingIcon;
            };

            return self;
        };


        this.poi = function (lat, lng, icon, content, propertyId) {
            var _mapquestPoi = null;
            var _bingPoi     = null;
            var _mouseIsOver = false;
            var self = {};
            var map = this;

            var _createMapquestPoi = function () {
                _mapquestPoi = new MQA.Poi({ lat: lat, lng: lng });
                if ((icon !== undefined) && (icon != null)) {
                    _mapquestPoi.setIcon(icon.getMapquestIcon());
                }
                if ((content !== undefined) && (content != null)) {
                    _mapquestPoi.setRolloverContent(content);
                }
                // Add POI mouseover event for delay house rollover content
                MQA.EventManager.addListener(_mapquestPoi, "mouseover", function (e) {
                    var thisRolloverContent = _mapquestPoi.rolloverContent;
                    lastRolloverContent = thisRolloverContent;
                    _mapquestPoi.rolloverContent = "";
                    _mouseIsOver = true;
                    window.setTimeout(function () {
                        _mapquestPoi.rolloverContent = thisRolloverContent;
                        if (_mouseIsOver && (_mapquestPoi.rolloverWindow == undefined)) {
                            _mapquestPoi.toggleInfoWindowRollover();
                        }
                    }, 500);
                });
                // add POI mouseout event to cancel delayed rollover content
                MQA.EventManager.addListener(_mapquestPoi, "mouseout", function (e) {
                    _mouseIsOver = false;
                    if (_mapquestPoi.rolloverWindow != undefined) {
                        _mapquestPoi.toggleInfoWindowRollover();
                    }
                    if (lastRolloverContent != "") {
                        _mapquestPoi.rolloverContent = lastRolloverContent;
                    }
                });
                if ((propertyId !== undefined) && (propertyId != null)) {
                    MQA.EventManager.addListener(_mapquestPoi, "click", function (e) {
                        map.houseOverClick(propertyId);
                    });
                }
                return _mapquestPoi;
            };

            var _createBingPoi = function () {
                _bingPoi = new VEShape(VEShapeType.Pushpin, new VELatLong(lat, lng));
                if (icon !== undefined && icon != null) {
                    _bingPoi.SetCustomIcon(icon.getBingIcon());
                }
                if (content !== undefined && content != null) {
                    _bingPoi.SetDescription(content);
                }
                return _bingPoi;
            };

            self.getMapquestPoi = function () {
                if (_mapquestPoi == null) {
                    _mapquestPoi = _createMapquestPoi();
                }
                return _mapquestPoi;
            };

            self.getBingPoi = function () {
                if (_bingPoi == null) {
                    _bingPoi = _createBingPoi();
                }
                return _bingPoi;
            };

            return self;
        };



        // persist map control changes separately from setMapBindings() to prevent map redraw
        var _persistMapControl = function () {
            var baseURL = $("#baseURL").val();

            if (baseURL != undefined) {
                if (document.location.protocol == "https:") {
                    baseURL = baseURL.replace("http:", "https:");
                }
                baseURL += "/";
            } else {
                baseURL = "";
            }

            if (mapName != "poiMiniMap") {

                var controlData = {
                    mapViewType:     map.getCurrentView(),
                    mapDragType:     map.getDragType(),
                    allowMouseWheel: map.getAllowMouseWheel()
                }

                $bindingFields.mapViewType.val(controlData.mapViewType);
                $bindingFields.mapDragType.val(controlData.mapDragType);
                $bindingFields.allowMouseWheel.val(controlData.allowMouseWheel);

                persistRequest = $.ajax({
                    type: "POST",
                    url: baseURL + "gateway.cfm?action=persistMapControl",
                    data: controlData
                });

                mapTrack();
            }
        };

        // Track map usage
        var mapTrigger = 0;
        var mapTrack = function () {
            var myMapTrigger = ++mapTrigger;
            var baseURL = $("#baseURL").val();
            if (baseURL != undefined) {
                if (document.location.protocol == "https:") {
                    baseURL = baseURL.replace("http:", "https:");
                }
                baseURL += "/";
            } else {
                baseURL = "";
            }
        };

        // Event Handlers 
        this.onZoomEnd = function (e) {
            map.setMapBindings();
            $(mapNode).trigger("zoomEnd");
        };

        this.onMoveEnd = function (e) {
            map.setMapBindings();
            $(mapNode).trigger("moveEnd");
        };

        this.onMoveStart = function (e) {
            $(mapNode).trigger("moveStart");
        };

        this.onDragEnd = function (e) {
            map.setMapBindings();
            $(mapNode).trigger("dragEnd");
        };

        this.houseOverClick = function (propertyId) {
            $(window).trigger('houseOverClick', [propertyId]);
        };


        $(".wntHOItem").live("mouseup", function () {
            map.houseOverClick($(this).attr("data-property-id"));
        });


        // MapQuest TileMap Functions

        this.bestFit = function () {
            return mapquestMap.bestFit();
        };

        this.addShape = function (shape) {
            return mapquestMap.addShape(shape);
        };

        this.removeAllShapes = function () {
            return mapquestMap.removeAllShapes();
        };

        this.getZoomLevel = function () {
            return Number(mapquestMap.getZoomLevel()) - mapquestZoomAdjust;
        };
        this.setZoomLevel = function (zoomLevel) {
            return mapquestMap.setZoomLevel(Number(zoomLevel) + mapquestZoomAdjust);
        };

        this.getBounds = function () {
            return mapquestMap.getBounds();
        };

        this.pixToLL = function (point) {
            return mapquestMap.pixToLL(point);
        };


        /* *************************************************************************************** */
        // Add child divs for placing maps.
        // Since Google street view is Flash, we don't need a div for it.
        // Bing Map Node
        var bingMapNode = document.createElement("div");
        $(bingMapNode)
            .attr("id", "wntMtBing" + mapID)
            .css("width", width)
            .css("height", height)
            .css("position", "relative")
            .hide();
        $(mapNode).append(bingMapNode);

        // MapQuest Node
        var mapquestNode = document.createElement("div");
        $(mapquestNode)
            .attr("id", "wntMtMapquest" + mapID)
            .css("width", width)
            .css("height", height)
            .appendTo($(mapNode))
            .hide();

        // Create maps

        // MapQuest

        var mapquestMap = {};
        var thisMapquestMap = mapquestMap;
        var mapControls = {};

        var bingHouseView = null;

        if (provider == "mapquest") {         
            mapquestMap = new MQA.TileMap({
                elt: mapquestNode,
                zoom: Number(mapZoomLevel) + mapquestZoomAdjust,
                latLng: new MQA.LatLng(centerLat, centerLng),
                mtype: mapViewType,
                bestFitMargin: 0,
                zoomOnDoubleClick: true
            });
            // Reference to mapquestMap for event-handler definitions
            thisMapquestMap = mapquestMap;

            // MapQuest Logo and Scale Placement
            /*
            if (mapName != "poiMiniMap") {
                var MQLogoYOffset = 28;
            } else {
                var MQLogoYOffset = 5;
            }
            mapquestMap.setLogoPlacement(
                MQA.MapLogo.MAPQUEST,
                new MQA.MapCornerPlacement(
                    MQA.MapCorner.BOTTOM_LEFT,
                    new MQA.Size(0, MQLogoYOffset)
                )
            );
            if (showScales) {
                mapquestMap.setLogoPlacement(
                    MQA.MapLogo.SCALES,
                    new MQA.MapCornerPlacement(
                        MQA.MapCorner.TOP_RIGHT,
                        new MQA.Size(0, 5)
                    )
                );
            } else {
                mapquestMap.setLogoPlacement(
                    MQA.MapLogo.SCALES,
                    new MQA.MapCornerPlacement(
                        MQA.MapCorner.TOP_LEFT,
                        new MQA.Size(0, 5000)
                    )
                );
            }*/

            // Add Mini Map
            if (hasMiniMap) {
                MQA.withModule("insetmapcontrol", function () {
                    thisMapquestMap.addControl(
                        new MQA.InsetMapControl({
                            size: { width: 150, height: 125 },
                            zoom: 3,
                            mapType: "map",
                            minimized: false
                        }),
                        new MQA.MapCornerPlacement(MQA.MapCorner.BOTTOM_RIGHT)
                    );
                });
            }

            // Unwire DOM events if not movable or
            if (!isMovable || (mapName == "poiMiniMap")) {
                mapquestMap._unwireDOMEvents();
            }

            // Click/Drag to Zoom
            if (mapName != "poiMiniMap") {

                var startX      = 0;
                var startY      = 0;
                var endX        = 0;
                var endY        = 0;
                var drawLeft    = 0;
                var drawWidth   = 0;
                var drawTop     = 0;
                var drawHeight  = 0;
                var aspectRatio = height/width;
                var w           = mapquestMap.getSize().width + 1;
                var h           = mapquestMap.getSize().height + 1;
                var coverOffX   = parseInt($(baseNode).position().left);
                var coverOffY   = parseInt($(baseNode).position().top);
                var mouseIsDown = false;

                // Create drag/zoom bounding box
                var tmpBlockDiv = document.createElement("div");
                $(tmpBlockDiv).attr("id", "blockDiv").addClass("wntTmpBlockDiv");
                $(mapquestNode).append(tmpBlockDiv);

                // Create drag/zoom cursor cover element
                var tmpCursorCoverDiv = document.createElement("div");
                $(tmpCursorCoverDiv).attr("id", "cursorCoverDiv").addClass("wntCursorCoverDiv");
                $(mapquestNode).append(tmpCursorCoverDiv);

                // Bind preventDefault to cover div to prevent mouse zoom cover tile from being dragged on mousedown
                $(tmpCursorCoverDiv).bind("mousedown", function (e) {
                    e.preventDefault();
                });

                // Bind drag/zoom mouseDown event
                $(mapquestNode).mousedown(function (e) {
                    if ((dragType == "zoom") && !mapIsLoading) {
                        map._dragZoomMouseDown(e);
                    }
                });

                // Bind drag/zoom mouseUp event
                $(mapquestNode).mouseup(function (e) {
                    if ((dragType == "zoom") && !mapIsLoading) {
                        map._dragZoomMouseUp(e);
                    }
                });

                // Bind drag/zoom mouseMove event
                $(mapquestNode).mousemove(function (e) {
                    if ((dragType == "zoom") && !mapIsLoading) {
                        map._dragZoomMouseMove(e);
                    }
                });

            }

            // Add MapQuest event listeners
            MQA.EventManager.addListener(mapquestMap, "zoomend",   this.onZoomEnd);
            MQA.EventManager.addListener(mapquestMap, "moveend",   this.onMoveEnd);
            MQA.EventManager.addListener(mapquestMap, "movestart", this.onMoveStart);
            MQA.EventManager.addListener(mapquestMap, "dragend",   this.onDragEnd);


            // Mouse Wheel Zooming
            if (allowMouseWheel) {
                MQA.withModule("mousewheel", function () {
                    thisMapquestMap.enableMouseWheelZoom();
                });
            }

            // Add View Types
            if (showView) {
                MQA.withModule("viewoptions", function () {
                    if (mapControls.view == undefined) {
                        mapControls.view = new MQA.ViewOptions();
                        thisMapquestMap.addControl(mapControls.view);
                    }
                });
            }

            // Set up Panning and Zooming
            if (showZoom) {
                MQA.withModule("largezoom", function () {
                    if (mapControls.zoom == undefined) {
                        mapControls.zoom = new MQA.LargeZoom();
                        thisMapquestMap.addControl(
                            mapControls.zoom,
                            new MQA.MapCornerPlacement(MQA.MapCorner.TOP_LEFT, new MQA.Size(5, 5))
                        );
                    }
                });
            }

        }


        // Configure house-view at runtime if Bing map library is preloaded
        if ((typeof bingLibraryLoaded != "undefined") && bingLibraryLoaded && hasHouseView) {
            $(bingMapNode).show();
            bingHouseView = new VEMap(bingMapNode.id);
            bingHouseView.LoadMap(new VELatLong(centerLat, centerLng), houseViewZoomLevel, VEMapStyle.Birdseye);
            bingHouseView.HideDashboard();

            // The following line gets the hidden map closer to where it should be
            // so when we do show it the first time, the map doesn't slide as much
            bingHouseView.SetCenter(new VELatLong(centerLat, centerLng));

            // Remove key events except for arrows
            bingHouseView.AttachEvent("onkeydown", function (e) {
                return ((e.keyCode > 40) || (e.keyCode < 37));
            });
            bingHouseView.AttachEvent("onkeyup", function (e) {
                return ((e.keyCode > 40) || (e.keyCode < 37));
            });
            bingHouseView.AttachEvent("onkeypress", function (e) {
                return ((e.keyCode > 40) || (e.keyCode < 37));
            });

            bingHouseView.AttachEvent("onendzoom", function (e) {
                map.onZoomEnd();
            });
            bingHouseView.AttachEvent("onobliquechange", function (e) {
                map._orientationChanged();
            });

            // Since we don't know when the IsBirdseyeAvalable() is actually returning the correct result, we poll it every 500ms
            window.setInterval(function () {
                map._houseViewClientHandler();
            }, 500);

            // Fix bug with bing maps in FireFox 3.5
            if (navigator.userAgent.indexOf("Firefox/3.5") != -1) {
                bingMapNode.addEventListener("DOMMouseScroll", function (e) {
                        e.stopPropagation();
                        e.preventDefault();
                        e.cancelBubble = false;
                        return false;
                    }, false);
             }
            $(bingMapNode).hide();
        }

        // Google

        // We need to create and destroy the street view Flash app to get it to work in all browsers
        var googleStreetView = null;
        var googleStreetViewClient = null;

        if (hasStreetView) {
            var streetViewLocation = new google.maps.LatLng(centerLat, centerLng);
            googleStreetViewClient = new google.maps.StreetViewService();
            googleStreetViewClient.getPanoramaByLocation(streetViewLocation, 50, function (streetViewLocation) {
                map._streetViewClientHandler(streetViewLocation);
            });
        }

        // Bing
        isHouseViewAvailable = false;
 
        this.setMapView();


        // Bind hidden fields
        var $bindingFields = {
            centerLat:       $('[data-wnt-map-name=' + mapName + '][data-wnt-map-bind=centerLat]'),
            centerLng:       $('[data-wnt-map-name=' + mapName + '][data-wnt-map-bind=centerLng]'),
            mapType:         $('[data-wnt-map-name=' + mapName + '][data-wnt-map-bind=mapType]'),
            lrLat:           $('[data-wnt-map-name=' + mapName + '][data-wnt-map-bind=lrLat]'),
            lrLng:           $('[data-wnt-map-name=' + mapName + '][data-wnt-map-bind=lrLng]'),
            ulLat:           $('[data-wnt-map-name=' + mapName + '][data-wnt-map-bind=ulLat]'),
            ulLng:           $('[data-wnt-map-name=' + mapName + '][data-wnt-map-bind=ulLng]'),
            zoom:            $('[data-wnt-map-name=' + mapName + '][data-wnt-map-bind=zoom]'),
            mapViewType:     $('[data-wnt-map-name=' + mapName + '][data-wnt-map-bind=mapViewType]'),
            mapDragType:     $('[data-wnt-map-name=' + mapName + '][data-wnt-map-bind=mapDragType]'),
            allowMouseWheel: $('[data-wnt-map-name=' + mapName + '][data-wnt-map-bind=allowMouseWheel]')
        };
        this.setMapBindings();


        // Add this to the map node's data
        $(mapNode).data("map", this);

        // Refine Search: Move map to fit coordinate rectangle & zoom to persisted map scale for this search
        if (hasStartingRect) {
            this.zoomToRect(
                startingRect.tlLat,
                startingRect.tlLng,
                startingRect.brLat,
                startingRect.brLng
            );
            if (persistedMapScale != "") {
                this.zoomTo(persistedMapScale);
            }
        }

        // Set map to persisted mapViewType (vs data-wnt-map-view)
        switch (mapViewType) {
            case "map":
                this.setMapView();
                break;
            case "hyb":
                this.setHybridView();
                break;
            case "sat":
                this.setSatView();
                break;
            case "house":
                window.setTimeout(function () { map.setHouseView(); }, 2000);
                break;
            case "street":
                window.setTimeout(function () { map.setStreetView(); }, 2000);
                break;
        }

        // POI Icon
        if (hasPOIIcon) {
            this.addPoi(this.poi(
                poiIcon.lat,
                poiIcon.lng,
                this.mapIcon(poiIcon.icon, 21, 23)
            ));
        }

        // Mouse Controls
        this.setupMouseControls();

        // Persist map control changes
        $(mapNode).bind("currentView mapControlChanged", _persistMapControl);

        if ((mapName !== undefined) && (mapName != "poiMiniMap")) {
            if (map.getDragType() == "move") {
                map.setDragMove(false);
            } else {
                map.setDragZoom(false);
            }
        }
        $("[data-wnt-resetMap=]").click(function () {
            map.reset();
        });

        // Track map usage
        $(map).bind("moveEnd zoomEnd", mapTrack);
        mapTrack();

        return this;
    }


    var getParam = function (param, defaultValue, valueType) {
        var paramValue;
        if (param === undefined) {
            if (defaultValue !== undefined) {
                paramValue = defaultValue;
            } else {
                paramValue = "";
            }
        } else {
            paramValue = param;
        }

        // Fix value type
        if ((valueType === undefined) && (defaultValue !== undefined)) {
            valueType = typeof defaultValue;
        }
        if (valueType !== undefined) {
            switch (valueType) {
                case "boolean":
                    if (typeof paramValue != "boolean") {
                        switch (paramValue) {
                            case 1:
                            case "true":
                            case "yes":
                            case "y":
                                paramValue = true;
                                break;
                            default:
                                paramValue = false;
                                break;
                        }
                    }
                    break;
                case "number":
                    if (typeof paramValue != "number") {
                        paramValue = new Number(paramValue);
                    }
                    break;
            }
        }

        return paramValue;
    };

    var mapTracksGlobals = {
        MAP_ID: 0,
        CONTROL_ID: 0
    };

    var methods = {

        init : function(options) {

            return this.each(function() {

                //beginning of execution
                var wntMapTracks = new MapTracks();
                wntMapTracks.createMap(this);

                var houseoverData = options.houseoverData || [];

                for (var i in houseoverData) {
      
                    methods.addHouseOver.call( $(this), [
                        houseoverData[i].lat,
                        houseoverData[i].lng,
                        houseoverData[i].content,
                        options.houseoverIcon,
                        options.mapId
                        ]);
                }

                var builtMap = $('#' + options.mapId).data('map');
                builtMap.bestFit();
            });

        },

        addHouseOver : function(args) {
            return this.each(function() {
                var lat       = args[0];
                var lng       = args[1];
                var content   = args[2];
                var houseIcon = args[3];
                var mapId     = args[4];
 
                var componentMap = $('#' + mapId).data('map');
                var houseoverIcon = componentMap.mapIcon(houseIcon,30,30);
                var houseover = componentMap.poi(lat, lng, houseoverIcon, content, 123);
                componentMap.addPoi(houseover);
            });
        }

    }

    $.fn[pluginName] = function(method)
    {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));

        }
        else if (typeof method === 'object' || !method) {
            return methods.init.apply( this, arguments );

        }
        else {
            $.error( 'Method ' +  method + ' does not exist on jQuery.' + pluginName );

        }

    }


})(jQuery);
