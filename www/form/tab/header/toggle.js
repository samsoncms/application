/**
 * Created by onysko on 27.05.2015.
 */

s('.samsoncms-form').pageInit(function (form) {
    s('.tab-toggle-button', form).each(function (link) {
        var parent = link.parent('template-block');
        var content = s('.template-block-content', parent);

        link.click(function () {
            link.hasClass('collapsed') ? content.fadeIn('fast') : content.fadeOut('fast');
            link.toggleClass('collapsed');

            //$(content.DOMElement).slideToggle(400);
        });
    });

    s('.sub-tab-header span', form).each(function (link) {
        link.click(function (link) {
            var parent = link.parent('template-block');

            s('.sub-tab-header span', parent).removeClass('active');
            s('.sub-tab-content.active', parent).hide();
            s('.sub-tab-content', parent).removeClass('active');

            var tab = s(link.className());

            tab.addClass('active');
            tab.fadeIn('fast', function(){
                SamsonCMS_Input.redraw();
            });

            link.addClass('active');
        });
    });

    s('.template-block').each(function (block) {
        if (s('.sub-tab-header span', block).length) {
            var first = s('.sub-tab-header span', block).elements[0];
            s(first.className()).addClass('active');
            first.addClass('active');
        } else if (s('.sub-tab-content', block).length) {
            s('.sub-tab-content', block).addClass('active');
        }
    });

    //SamsonCMS_Input.redraw();
});