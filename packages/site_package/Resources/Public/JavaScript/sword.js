var $swords = $('.ce-sword');

if ($swords.length) {
    var $firstSword = $swords.first();

    $window.on('load', function() {
        if ($firstSword.is(':visible') === false) {
            var $accContent = $firstSword.parents('.accordion-content');

            if ($accContent.length) {
                $accContent.prev('.accordion-header').click();
            }

        } else {
            scrollToElementIfNotFullyVisible($firstSword);
        }
    });
}
