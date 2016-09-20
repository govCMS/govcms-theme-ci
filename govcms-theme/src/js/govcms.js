jQuery(document).ready(function() {

    jQuery(".move-to-top").appendTo("#top-and-first-wrapper");
    jQuery(".move-into-top").appendTo("section#about > div.container > div.row");

    jQuery("#search-api-page-search-form").wrap("<div class='col-md-10 col-md-offset-1'></div>");
    jQuery("#block-workbench-block").addClass("container");
    jQuery("ul.tabs.primary").addClass("nav nav-tabs").removeClass("tabs").removeClass("primary");
  // Conditional form logic
    // Set initial state, hide it...
    jQuery('.drupal-version-list').toggleClass('sr-only');
    jQuery('.drupal-version-checkbox').change(function() {
        // toggle on change
        jQuery('.drupal-version-list').toggleClass('sr-only');
    });

  // Add extra sites btn functionality
    jQuery('.site-info-add').click(function() {
        // duplicate .site-info form element
        var htmlToInsert = jQuery('.site-info')[0].outerHTML;
        // increment the id number
        var numItems = jQuery('.site-info').length;
        var numItemsNext = numItems + 1;
        htmlToInsert = htmlToInsert.replace(/1\"/g, numItemsNext+'"');
        // append
        jQuery('.site-info').last().after( htmlToInsert );
        return false;
    });

  // Make left and right split same height, noting the either may be shorter
    jQuery( ".split" ).each(function() {
        if(jQuery(window).width() > 993) {
            height1 = jQuery('.section-one', this).height();
            height2 = jQuery('.section-two', this).height();
            if (height1 > height2) {
                rightHeight = jQuery('.section-one', this).height();
                jQuery('.section-two', this).height(rightHeight);
            } else {
                rightHeight = jQuery('.section-two', this).height();
                jQuery('.section-one', this).height(rightHeight);
            }
        }
    });
    jQuery('.fancyCounter').each(function () {
        var isInView = isElementVisible(jQuery(this));
        if(isInView && !jQuery(this).hasClass('counted') && !jQuery(this).hasClass('counting')){
            jQuery(this).addClass('counting');
            startCounter(this);
        }
    });

    jQuery('div.region-content [class^="icon-"], div.region-content .fancyCounter').each(function () {
        jQuery(this).addClass("do-fade")
        var isInView = isElementVisible(jQuery(this));
        if(isInView && !jQuery(this).hasClass('faded')){
            jQuery(this).addClass('faded');
        }
    });

    jQuery('a').filter(function () {
        return this.hostname && this.hostname !== location.hostname;
    }).append('<span class="sr-only"> external resource</span>');

    jQuery(window).scroll(function () {
        jQuery('.fancyCounter').each(function () {
            var isInView = isElementVisible(jQuery(this));
            if (isInView && !jQuery(this).hasClass('counted') && !jQuery(this).hasClass('counting')) {
                jQuery(this).addClass('counting');
                startCounter(this);
            }
        });

        jQuery('div.region-content [class^="icon-"], div.region-content .fancyCounter').each(function () {
            var isInView = isElementVisible(jQuery(this));
            if (isInView && !jQuery(this).hasClass('faded')) {
                jQuery(this).addClass('faded');
            }
        });

    });
});


function startCounter(theObject) {
    jQuery(theObject).prop('Counter',0).animate({
        Counter: jQuery(theObject).text()
    }, {
        duration: 1300,
        easing: 'swing',
        step: function (now) {
            jQuery(theObject).text(Math.ceil(now));
        },
        complete: function () {
            jQuery(theObject).addClass("counted");
            jQuery(theObject).removeClass("counting");
        }
    });
}



function isElementVisible(theElement) {
    var TopView = jQuery(window).scrollTop();
    var BotView = TopView + jQuery(window).height();
    var TopElement = jQuery(theElement).offset().top;
    var BotElement = TopElement + jQuery(theElement).height();
    // return ((BotElement <= BotView) && (TopElement >= TopView));
    return (((BotElement <= BotView) && (TopElement >= TopView)) || (!(BotElement <= TopView) && !(TopElement >= BotView)));
}




function priceCalc() {
  // Setup vars
  pageViews = jQuery('#page-views').val();
  siteCount = jQuery('#number-of-sites').val();

  // Calculate it:
  totalAnnualCost = 199; // for now.....
  totalSetupCost = 88;  // for now.....

  // Display it
  jQuery('#calcForm').addClass('fade-out');
  jQuery('#calcResults #totalAnnualCost').text(totalAnnualCost);
  jQuery('#calcResults #totalSetupCost').text(totalSetupCost);
  jQuery('#calcResults #calcHeading').text('Your estimated total costs are');
  jQuery('#calcResults').removeClass('sr-only');
  jQuery('#calcResults').removeClass('fade-out');
  jQuery('#calcResults').addClass('fade-in');

  // Stop form submission
  return false;
  e.preventDefault();
}

function priceReCalc() {
  // Display it
  jQuery('#calcForm').removeClass('fade-out');
  jQuery('#calcResults').removeClass('fade-in');
  jQuery('#calcResults').addClass('fade-out');
  jQuery('#calcResults #calcHeading').text('Calculate your costs');
  jQuery('#calcForm').addClass('fade-in');
  jQuery('#calcResults').addClass('sr-only');

  // Stop anchor link loading
  return false;
  e.preventDefault();
}
