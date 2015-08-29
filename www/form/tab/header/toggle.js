/**
 * Created by onysko on 27.05.2015.
 */

s('.samsoncms-form').pageInit(function (form) {
    var hash = window.location.hash;
    var currentBlock = s(hash);
    if (hash == '') {
        currentBlock = s('.template-block:first-child');
        hash = currentBlock.a('id');
    }
    
    // Open/close tab by click on the title of tab
    s('.template-block .template-block-header .tab-header>span:not(.tab-toggle-button)').click(function (e) {
        s('.tab-toggle-button', e.parent()).click();
    });
    
    window.location.hash = hash;
    s('.tab-toggle-button', currentBlock).hasClass('collapsed') ? s('.template-block-content', currentBlock).fadeIn('fast') : s('.template-block-content', currentBlock).fadeOut('fast');
    s('.tab-toggle-button', currentBlock).toggleClass('collapsed');
    s(window).scrollTop(currentBlock.offset());


    s('.tab-header', form).each(function (header) {
        var link = s('.tab-toggle-button', header);
        var parent = link.parent('template-block');
        var content = s('.template-block-content', parent);

        link.click(function () {

            link.hasClass('collapsed') ? content.fadeIn('fast') : content.fadeOut('fast');
            link.toggleClass('collapsed');

            if (!link.hasClass('collapsed')) {
                window.location.hash = parent.a('id');
            }

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

            link.addClass('active');
            tab.show();
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
