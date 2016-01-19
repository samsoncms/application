/**
 * Created by onysko on 27.05.2015.
 */

SamsonCMS_InputINIT_TAB = function(form) {
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

    // Show sub tabs with locale
    function showSubTab(tab) {
        tab.css('display', 'inline-block');
    }

    // If tab have to be opened
    if (s('.tab-toggle-button', currentBlock).hasClass('collapsed')) {

        s('.template-block-content', currentBlock).fadeIn('fast');

        // Show subtabs in current block
        showSubTab(s('.sub-tab-header', s('.tab-toggle-button', currentBlock).parent()));

        // Tab have to be hided
    } else {
        s('.template-block-content', currentBlock).fadeOut('fast');
    }

    s('.tab-toggle-button', currentBlock).toggleClass('collapsed');
    s.scrollPageTo(currentBlock.offset().top, 200);


    s('.tab-header', form).each(function (header) {
        var link = s('.tab-toggle-button', header);
        var parent = link.parent('template-block');
        var content = s('.template-block-content', parent);
        var subHeaders = s('.sub-tab-header', parent);

        link.click(function () {
            // Content of the tab is hided
            if (link.hasClass('collapsed')) {
                showSubTab(subHeaders);
            } else {
                // Hide content
                subHeaders.fadeOut('fast');
            }

            $(content.DOMElement).slideToggle('slow');
            link.toggleClass('collapsed');

            if (!link.hasClass('collapsed')) {
                s.scrollPageTo(parent.offset().top, 200, function() {
                    window.location.hash = parent.a('id');
                });
            }
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

            // Set current locale as active in all tabs
            s('.sub-tab-header span', block).each(function(e){
                var currentLocale = e.a('data-current-locale');
                if (currentLocale == e.text()) {
                    s(e.className()).addClass('active');
                    e.addClass('active');
                }
            });
        } else if (s('.sub-tab-content', block).length) {
            s('.sub-tab-content', block).addClass('active');
        }
    });

    //SamsonCMS_Input.redraw();
};

// Bind input
SamsonCMS_Input.bind(SamsonCMS_InputINIT_TAB, '.samsoncms-form');
