$(document).ready(function() {
    $helpButton = $('.search-help-button');
    $helpText = $('.search-help');
    
    $helpButton.bind('click', function() {
        if ($helpText.is(':hidden')) {
            $helpText.show();
        } else {
            $helpText.hide();
        }
    });
});
