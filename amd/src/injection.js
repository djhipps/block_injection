/* eslint-disable no-throw-literal,no-loop-func,capitalized-comments */
// Put this file in path/to/plugin/amd/src
// You can call it anything you like

/* jshint loopfunc:true */


define(['jquery', 'core/log'], function($, log) {

    return {
        init: function(param) {
            log.warn("Init injection", param);
        },
        _privatefunction: function() {
                return "something" ;
        }
    };
});