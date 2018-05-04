
jQuery(document).ready(function ($) {

    jQuery('#cegg-parsers-tabs').tabs();
    jQuery("#sug_btn_group").buttonset();
    jQuery("#trend_google, #trend_goods").button();
    keywords_count();

    jQuery("#tool_capitalise").button({
        icons: {
            primary: "ui-icon-triangle-1-n"
        },
        text: false
    }).next().button({
        icons: {
            primary: "ui-icon-carat-1-n"
        },
        text: false
    }).next().button({
        icons: {
            primary: "ui-icon-arrow-1-s"
        },
        text: false
    }).next().button({
        icons: {
            primary: "ui-icon-minus"
        },
        text: false
    }).next().button({
        icons: {
            primary: "ui-icon-plus",
        },
        text: false
    }).next().button({
        icons: {
            primary: "ui-icon-closethick",
        },
        text: false
    });


    /** Suggestion tool */
    var sug_parser = 'sug_google';
    //sug parser source
    jQuery("#sug_btn_group input").click(function () {
        sug_parser = jQuery(this).val();
        if (jQuery('#sug_query').val())
            suggest(jQuery('#sug_query').val(), sug_parser);
    });
    jQuery('#sug_query').keyup(function () {
        suggest(jQuery(this).val(), sug_parser);
    });
    //--------------------------------------


    //add buttons
    jQuery('#add_selected').click(function () {
        add_selected()
    });

    jQuery('#add_all').click(function () {
        add_all();
    });

    // keywords count
    jQuery('#keywords').keyup(function (e) {
        var code = e.which;
        if (code == 13) {
            keywords_count();
        }
    });

    //tools
    jQuery('#tool_capitalise').click(function (event) {
        event.preventDefault();
        var keywords = jQuery('#keywords').val();
        jQuery('#keywords').val(keywords.capitalize());
    });

    jQuery('#tool_sort').click(function (event) {
        event.preventDefault();
        var keywords = jQuery('#keywords').val();
        jQuery('#keywords').val(SortWords(keywords));
    });

    jQuery('#tool_delete').click(function (event) {
        event.preventDefault();
        jQuery('#keywords').val('');
        keywords_count();
    });

    jQuery('#tool_add_minus').click(function (event) {
        event.preventDefault();
        var keywords = jQuery('#keywords').val();
        jQuery('#keywords').val(addMinus(keywords));
    });

    jQuery('#tool_del_minus').click(function (event) {
        event.preventDefault();
        var keywords = jQuery('#keywords').val();
        jQuery('#keywords').val(delMinus(keywords));
    });

    jQuery('#tool_upper_first').click(function (event) {
        event.preventDefault();
        var keywords = jQuery('#keywords').val();
        jQuery('#keywords').val(upperFirst(keywords));
    });


    /**
     * Hot Trends
     */
    jQuery("#trend_google").click(function () {
        jQuery("#trend_google").prop("disabled", true).addClass("ui-state-disabled");
        trend_google();
        jQuery('#trend_google').removeAttr('disabled').removeClass('ui-state-disabled');
    });

    jQuery("#trend_goods").click(function () {
        jQuery("#trend_goods").prop("disabled", true).addClass("ui-state-disabled");
        amazon_trends();
        jQuery('#trend_goods').removeAttr('disabled').removeClass('ui-state-disabled');
    });


    jQuery("#trend_keywords,#sug_keywords,#goods_keywords").change(function () {
        var opt = jQuery(this).children(":selected");
        add_keyword(opt.val());
        opt.remove();
    });
});

function suggest(query, sug_parser) {
    window[sug_parser](query);
}

function sug_yandex(query) {
    var url = 'https://suggest.yandex.ru/suggest-ya.cgi?callback=?&v=4&part=' + encodeURIComponent(query);
    jQuery.getJSON(url, function () {
    })
            .done(function (data) {
                var keywords = '';
                jQuery.each(data[1], function (i, keyword) {
                    keywords = keywords + '<option>' + keyword + '</option>';
                });
                jQuery('#sug_keywords').html(keywords);
            });
}

function sug_market(query) {

    var url = 'http://suggest.market.yandex.ru/suggest-market?callback=?&part=' + encodeURIComponent(query);
    jQuery.getJSON(url, function () {
    })
            .done(function (data) {
                var keywords = '';
                jQuery.each(data[1], function (i, keyword) {
                    keywords = keywords + '<option>' + keyword + '</option>';
                });
                jQuery('#sug_keywords').html(keywords);
            });
}

function sug_amazon(query) {
    request = jQuery.ajax({
        url: 'https://completion.amazon.com/search/complete?method=completion&q=' + encodeURI(query) + '&search-alias=aps&client=amazon-search-ui&mkt=1',
        dataType: 'jsonp',
        cache: true
    });
    request.done(function (data) {
        jQuery('#sug_keywords').empty();
        var keywords = '';
        jQuery.each(data[1], function (i, keyword) {
            keywords = keywords + '<option>' + keyword + '</option>';
        });
        jQuery('#sug_keywords').html(keywords);
    });
}

function sug_google(query) {

    var sitelang = contenteggL10n.sitelang;
    request = jQuery.ajax({
        url: 'https://www.google.com/complete/search?hl=' + sitelang + '&client=serp&js=true&q=' + encodeURIComponent(query),
        dataType: "jsonp",
        cache: true
    });
    request.done(function (data) {
        jQuery('#sug_keywords').empty();
        var keywords = '';
        jQuery.each(data[1], function (i, keyword) {
            keywords = keywords + '<option>' + keyword[0] + '</option>';
        });
        jQuery('#sug_keywords').html(keywords);
    });
}


// ebay hardcoded callback
jQuery.namespace = function () {
    var a = arguments, o = null, i, j, d;
    for (i = 0; i < a.length; i = i + 1) {
        d = a[i].split(".");
        o = window;
        for (j = 0; j < d.length; j = j + 1) {
            o[d[j]] = o[d[j]] || {};
            o = o[d[j]];
        }
    }
    return o;
};
vjoObj = jQuery.namespace("vjo.darwin.domain.finding.autofill.AutoFill");
vjoObj._do = function (data) {
    sug_ebay_callback(data);
};


function sug_ebay(query) {
    request = jQuery.ajax({
        url: 'https://autosug.ebay.com/autosug?kwd=' + encodeURIComponent(query),
        dataType: "jsonp",
        cache: true
    });
    request.done(function (data) {
    });
}

function sug_ebay_callback(data) {
    jQuery('#sug_keywords').empty();
    var keywords;
    jQuery.each(data.res.sug, function (i, keyword) {
        keywords = keywords + '<option>' + keyword + '</option>';
    });
    jQuery('#sug_keywords').html(keywords);
}

function keywords_count() {
    keywords_uniq();
    var d = jQuery('#keywords').val();
    jQuery('#k_count').text(d.split(/\n/).length);
}

function keywords_uniq() {
    // delete duplicates
    var arr = jQuery("#keywords").val().split("\n");
    var arrDistinct = new Array();
    jQuery(arr).each(function (index, item) {
        if (jQuery.inArray(item, arrDistinct) == -1)
            arrDistinct.push(item);
    });
    jQuery("#keywords").val(arrDistinct.join("\n"));
}

String.prototype.capitalize = function () {
    return this.replace(/(^|\s)([a-zа-я])/g, function (m, p1, p2) {
        return p1 + p2.toUpperCase();
    });
};

function SortWords(words) {
    var warr = new Array();
    warr = words.split("\n");
    //warr.pop();
    warr = warr.sort();
    return warr.join('\n');
}

function addMinus(words) {
    words = words.replace(/^(?!\[)/gm, '[');
    words = words.replace(/([^?:\]])$/gm, "$1]");
    return words;
}

function delMinus(words) {
    words = words.replace(/^\[/gm, '');
    words = words.replace(/\]$/gm, '');
    return words;
}

function upperFirst(words) {
    words = words.toLowerCase();
    return words.replace(/(^|\n)(.)/g, function (m, p1, p2, offset, s) {
        return m.toUpperCase();
    });
}


/**
 * Hot Trends
 */

function trend_google() {

    var sitelang = contenteggL10n.sitelang;

    jQuery('#trend_keywords').empty();
    var zone = 'com';

    if (sitelang == 'ru')
        zone = 'ru';
    else if (sitelang == 'de')
        zone = 'de';
    else if (sitelang == 'fr')
        zone = 'fr';
    else if (sitelang == 'uk')
        zone = 'com.ua';
    else if (sitelang == 'th')
        zone = 'co.th';
    else if (sitelang == 'tr')
        zone = 'com.tr';
    else if (sitelang == 'es')
        zone = 'es';
    else if (sitelang == 'it')
        zone = 'it';
    else if (sitelang == 'el')
        zone = 'gr';
    else if (sitelang == 'jp')
        zone = 'co.jp';
    else
        zone = 'com';

    var site = "https://www.google." + zone + "/trends/hottrends/atom/hourly";
    var yql = "select * from htmlstring where url='" + site + "' AND xpath='.//a/text()'";
    var url = "https://query.yahooapis.com/v1/public/yql?q=" + encodeURIComponent(yql) + "&format=json&env=store%3A%2F%2Fdatatables.org%2Falltableswithkeys";

    jQuery.getJSON(url, function () {
    }).done(function (data) {
        var keywords = {};
        list = data.query.results.result.split("\n");
        jQuery.each(list, function (i, keyword) {
            keywords[keyword] = keyword;
            jQuery('#trend_keywords')
                    .append(jQuery("<option></option>")
                            .attr("value", keyword)
                            .text(keyword));
        });
    });
}
function amazon_trends() {

    var category_id = jQuery('#amazon_categ').val();
    var amazon_section = jQuery('#amazon_section').val();

    var site = 'http://www.amazon.com/gp/rss/' + amazon_section + '/' + category_id + '/';
    var yql = "select * from htmlstring where url='" + site + "' AND xpath='.//span/a/text()'";
    var url = "https://query.yahooapis.com/v1/public/yql?q=" + encodeURIComponent(yql) + "&format=json&env=store%3A%2F%2Fdatatables.org%2Falltableswithkeys";

    jQuery('#goods_keywords').empty();
    jQuery.getJSON(url, function () {
    }).done(function (data) {
        var keywords = {};
        list = data.query.results.result.split("\n");
        jQuery.each(list, function (i, keyword) {
            keywords[keyword] = keyword;
            jQuery('#goods_keywords')
                    .append(jQuery("<option></option>")
                            .attr("value", keyword)
                            .text(keyword));
        });
    });
}

function add_keyword(keyword) {
    if (jQuery("#keywords").val())
        jQuery("#keywords").val(jQuery("#keywords").val() + "\n");
    jQuery("#keywords").val(jQuery("#keywords").val() + keyword);
    keywords_count();
}
