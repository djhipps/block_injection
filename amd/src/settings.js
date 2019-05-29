// Put this file in path/to/plugin/amd/src
// You can call it anything you like

define(['jquery', 'core/notification', 'core/url'], function($, Note, Url) {

    return {
        init: function() {
            $('.removesetting').click(function() {
                 var categoryid = $(this).data('categoryid'),
                            url = Url.relativeUrl('/blocks/jsinjection/delete_settings.php', {}, false);
                 $.ajax({
                     method: "POST",
                     url: url,
                     data: {categoryid: categoryid}
                 }).done(function(res) {
                      if (res) {
                        Note.alert('Delete Setting', 'Your setting has been deleted.', 'Continue');
                        if ($('.tablerow:visible').length <= 1) {
                            $('.settings-table').hide();
                        } else {
                            $('.tablerow-' + categoryid).hide();
                        }
                     }
                 });

            });
        }
    };
});