"use strict";
(function() {
    document.addEventListener('DOMContentLoaded', function() {
        var executeThis = function() {
            var options = Joomla.getOptions('j51_module_progress');
            if (options.length) {
                options.forEach(function(option) {
                    var counter = new CountUp(option.id, 0, option.counts, option.decimal, option.animation_length, {});

                    new Waypoint({
                        element: document.getElementById(option.number),
                        handler: function(direction) {
                            setTimeout(counter.start, option.delay);
                            this.element.classList.add("animated");                            
                        },
                        offset: "85%"
                    });
                });
            }

        };

        var checkWaypoint = function(){
            if (typeof Waypoint === "undefined") {
                setTimeout(checkWaypoint, 50);
            } else {
                executeThis();
            }
        };
        checkWaypoint();
        
    });
})();
