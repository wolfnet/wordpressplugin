/**
 * This script introduces swipe events to the DOM.
 */

;(function(document, window){

    var SWIPE_IN_PROGRESS = 'wntSwipeInProgress';

    var defaultArguments = {
        callback: null, // This callback function is called when a swipe event occurs
        threshold: 30, // Swipe must travel this distance to register
        allowedTime: 200, // Swipe must take less than this to complete
        direction: null // ('horizontal', 'vertical') This option will prevent unwanted scrolling

    };

    var onTouchStart = function(event, swipeData){
        var touchObj = event.changedTouches[0];

        // Capture the starting point of the gesture.
        swipeData.startX = touchObj.pageX;
        swipeData.startY = touchObj.pageY;

        // Capture the time the gesture started for duration threshold.
        swipeData.startTime = new Date().getTime();

    };

    var onTouchMove = function(event, swipeData){
        var touchObj = event.changedTouches[0];

        // Determine the overall movement
        swipeData.distX = swipeData.startX - touchObj.pageX;
        swipeData.distY = swipeData.startY - touchObj.pageY;

        // Determine the difference between the vertical and horizontal motion.
        var vhDiff = Math.abs(swipeData.distY) / Math.abs(swipeData.distX);

        // prevent scrolling in the wrong direction if the direction is specified.

        if (swipeData.direction == 'horizontal' && vhDiff < 1) {
            event.preventDefault();
        }

        if (swipeData.direction == 'vertical' && vhDiff > 1) {
            event.preventDefault();
        }

    };

    var onTouchEnd = function(event, swipeData){
        var touchObj = event.changedTouches[0];
        var eventDetails = {
            up: false,
            down: false,
            left: false,
            right: false
        };

        // Calculate the difference between start and finish
        swipeData.distX = touchObj.pageX - swipeData.startX;
        swipeData.distY = touchObj.pageY - swipeData.startY;
        swipeData.elapsedTime = new Date().getTime() - swipeData.startTime;

        // Did the swipe occur within the time limit?
        if (swipeData.elapsedTime <= swipeData.allowedTime) {

            // Is the direction left/right?
            if (Math.abs(swipeData.distX) >= swipeData.threshold) {
                // left to right
                if (swipeData.distX > 0) {
                    eventDetails.right = true;
                // right to left
                } else {
                    eventDetails.left = true;
                }
            }

            // Is the direction up/down?
            if (Math.abs(swipeData.distY) >= swipeData.threshold) {
                // top to bottom
                if (swipeData.distY > 0) {
                    eventDetails.down = true;
                // bottom to top
                } else {
                    eventDetails.up = true;
                }
            }

            // If a qualified swipe occurred dispatch a custom invent
            if (eventDetails.up || eventDetails.down || eventDetails.left || eventDetails.right) {
                // NOTE: This wont work in older versions of IE. Luckily there shouldn't be any on
                // devices capable of touch input.
                var swipeEvent = new CustomEvent('wntSwipe', {detail: eventDetails});
                this.dispatchEvent(swipeEvent);
            }

        }

    };

    var onSwipe = function(event, callback){

        if (event.defaultPrevented) {
            return;
        }

        if (callback !== null) {
            callback.call(this, event.detail);
        }

        // Announce directional swipe events

        if (event.detail.up) {
            this.dispatchEvent(new Event('wntSwipeUp'));
        }

        if (event.detail.down) {
            this.dispatchEvent(new Event('wntSwipeDown'));
        }

        if (event.detail.left) {
            this.dispatchEvent(new Event('wntSwipeLeft'));
        }

        if (event.detail.right) {
            this.dispatchEvent(new Event('wntSwipeRight'));
        }

    };

    window.Element.prototype.wolfnetSwipe = function(arg1, arg2) {
        var callback, threshold, allowedTime, direction;

        // function(callback, object)
        if ((arg1 && typeof arg1 == 'function') && (arg2 && typeof arg2 == 'object')) {
            callback = arg1;
            threshold = arg2.threshold || defaultArguments.threshold;
            allowedTime = arg2.allowedTime || defaultArguments.allowedTime;
            direction = arg2.direction || defaultArguments.direction;

        // function(object)
        } else if (arg1 && typeof arg1 == 'object') {
            callback = arg1.callback || defaultArguments.callback;
            threshold = arg1.threshold || defaultArguments.threshold;
            allowedTime = arg1.allowedTime || defaultArguments.allowedTime;
            direction = arg1.direction || defaultArguments.direction;

        }

        // Capture where the user's figure was when they touched the screen
        this.addEventListener('touchstart', function(event){

            // If a swipe is already in progress don't proceed.
            if (this[SWIPE_IN_PROGRESS]) {
                return;
            } else {
                this[SWIPE_IN_PROGRESS] = true;
            }

            // Create an object to hold the state of the swipe event.
            var swipeData = {
                threshold: threshold,
                allowedTime: allowedTime,
                direction: direction,

                // Creating a temporary event handlers function which we use to pass the state data
                // to the primary handler.

                move: function(event){
                    onTouchMove.call(this, event, swipeData);
                },

                end: function(event){
                    onTouchEnd.call(this, event, swipeData);

                    // Indicate the the element is no longer being swiped.
                    this[SWIPE_IN_PROGRESS] = false;

                    // Remove the temporary event handlers because the gesture is complete.
                    this.removeEventListener('touchmove', swipeData.move, false);
                    this.removeEventListener('touchend', swipeData.end, false);

                }

            };

            // Assign temporary event handlers for the duration of the gesture.
            this.addEventListener('touchmove', swipeData.move, false);
            this.addEventListener('touchend', swipeData.end, false);

            // Execute the first part of the gesture logic.
            onTouchStart.call(this, event, swipeData);

        }, false);

        // Execute the callback if the swipe event occurs.
        this.addEventListener('wntSwipe', function(event){
            onSwipe.call(this, event, callback);
        }, false);

        return this;

    };

    // If jQuery exists wrap the swipe functionality into a plugin.
    if (window.jQuery) {

        window.jQuery.fn.wolfnetSwipe = function(callback, threshold, allowedTime, preventDefault){

            return this.each(function(){
                this.wolfnetSwipe(callback, threshold, allowedTime, preventDefault);
            });

        };

    }

})(document, window, undefined);
