function setTabsSticky($elements) {
    $elements.stick_in_parent({
      offset_top: $fixedheader.length ? ($fixedheader.outerHeight()) : 0
    });
}

$(function () {
    $('.tab-navigation').each(function() {
        if (!$(this).parents('.accordion-wrapper').length) {
            setTabsSticky($(this));
        }
    });
});
