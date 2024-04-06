$.tooltipster.setDefaults({
    contentAsHTML: true,
    IEmin: 9,
    interactive: false,
    maxWidth: 400,
    minWidth: 100,
    zIndex: 9998,
    theme: ['tooltipster-default', 'tooltipster-default-ulrich'],
    updateAnimation: 'fade',
    // Optionen über data-Attribut übergeben: data-tooltipster='{"side":"left","animation":"slide"}'
    functionInit: function(instance, helper) {
        var $origin = $(helper.origin),
            dataOptions = $origin.attr('data-tooltipster');

        if (dataOptions) {
            dataOptions = JSON.parse(dataOptions);

            $.each(dataOptions, function(name, option){
                instance.option(name, option);
            });
        }
    }
});

function initTitleTooltips($wrapper) {
    $wrapper.find('.js-tooltip').tooltipster({
        trigger: 'hover',
        maxWidth: 400,
        theme: ['tooltipster-default', 'tooltipster-default-ulrich', 'tooltipster-default-ulrich-tooltip'],
    });
}

$(function () {
    initTitleTooltips($body);
});
