/*
 * This file has been compiled from: /modules/system/lang/lv/client.php
 */
if ($.wn === undefined) $.wn = {}
if ($.oc === undefined) $.oc = $.wn
if ($.wn.langMessages === undefined) $.wn.langMessages = {}
$.wn.langMessages['lv'] = $.extend(
    $.wn.langMessages['lv'] || {},
    {"markdowneditor":{"formatting":"Format\u0113jums","quote":"Cit\u0101ts","code":"Kods","header1":"Virsraksts 1","header2":"Virsraksts 2","header3":"Virsraksts 3","header4":"Virsraksts 4","header5":"Virsraksts 5","header6":"Virsraksts 6","bold":"Treknraksts","italic":"Kurs\u012bvraksts","unorderedlist":"Nesak\u0101rtots saraksts","orderedlist":"Sak\u0101rtots saraksts","video":"Video","image":"Att\u0113ls","link":"Saite","horizontalrule":"Ievietot horizont\u0101lu l\u012bniju","fullscreen":"Pilnekr\u0101na re\u017e\u012bms","preview":"Priek\u0161skat\u012bjums"},"mediamanager":{"insert_link":"Ievietot multivides saiti","insert_image":"Ievietot multivides att\u0113lu","insert_video":"Ievietot multivides video","insert_audio":"Ievietot multivides audio","invalid_file_empty_insert":"L\u016bdzu, izv\u0113lieties failu uz kuru ievietot saites.","invalid_file_single_insert":"L\u016bdzu, izv\u0113lieties vienu failu.","invalid_image_empty_insert":"L\u016bdzu, izv\u0113lieties ievietojamo(-os) att\u0113lu(-us).","invalid_video_empty_insert":"L\u016bdzu, izv\u0113lieties ievietojamo video failu.","invalid_audio_empty_insert":"L\u016bdzu, izv\u0113lieties ievietojamo audio failu."},"alert":{"confirm_button_text":"Labi","cancel_button_text":"Atcelt","widget_remove_confirm":"No\u0146emt \u0161o logr\u012bku?"},"datepicker":{"previousMonth":"Iepriek\u0161\u0113jais m\u0113nesis","nextMonth":"N\u0101kamais m\u0113nesis","months":["Janv\u0101ris","Febru\u0101ris","Marts","Apr\u012blis","Maijs","J\u016bnijs","J\u016blijs","Augusts","Septembris","Oktobris","Novembris","Decembris"],"weekdays":["Sv\u0113tdiena","Pirmdiena","Otrdiena","Tre\u0161diena","Ceturtdiena","Piektdiena","Sestdiena"],"weekdaysShort":["Sv","P","O","T","C","Pk","S"]},"colorpicker":{"last_color":"Lietot iepriek\u0161 izv\u0113l\u0113to kr\u0101su","aria_palette":"Kr\u0101sas izv\u0113les laukums","aria_hue":"Nokr\u0101sas izv\u0113les sl\u012bdnis","aria_opacity":"Caursp\u012bd\u012bguma izv\u0113les sl\u012bdnis"},"filter":{"group":{"all":"visi"},"scopes":{"apply_button_text":"Piem\u0113rot","clear_button_text":"Not\u012br\u012bt"},"dates":{"all":"visi","filter_button_text":"Filtr\u0113t","reset_button_text":"Atiestat\u012bt","date_placeholder":"Datums","after_placeholder":"Pirms","before_placeholder":"P\u0113c"},"numbers":{"all":"visi","filter_button_text":"Filtr\u0113t","reset_button_text":"Atiestat\u012bt","min_placeholder":"Min","max_placeholder":"Max"}},"eventlog":{"show_stacktrace":"R\u0101d\u012bt atseko\u0161anas inform\u0101ciju","hide_stacktrace":"Sl\u0113pt atseko\u0161anas inform\u0101ciju","tabs":{"formatted":"Format\u0113ts","raw":"Neapstr\u0101d\u0101ts"},"editor":{"title":"Pirmkoda redaktors","description":"J\u016bsu oper\u0113t\u0101jsist\u0113mai j\u0101b\u016bt konfigur\u0113tai t\u0101, lai t\u0101 sp\u0113tu klaus\u012bties uz vienu no \u0161\u012bm URL sh\u0113m\u0101m.","openWith":"Atv\u0113rt ar","remember_choice":"Atcer\u0113ties izv\u0113li \u0161\u012bs sesijas ietvaros","open":"Atv\u0113rt","cancel":"Atcelt"}}}
);

//! moment.js locale configuration v2.22.2

;(function (global, factory) {
   typeof exports === 'object' && typeof module !== 'undefined'
       && typeof require === 'function' ? factory(require('../moment')) :
   typeof define === 'function' && define.amd ? define(['../moment'], factory) :
   factory(global.moment)
}(this, (function (moment) { 'use strict';


    var units = {
        'ss': 'sekundes_sekundēm_sekunde_sekundes'.split('_'),
        'm': 'minūtes_minūtēm_minūte_minūtes'.split('_'),
        'mm': 'minūtes_minūtēm_minūte_minūtes'.split('_'),
        'h': 'stundas_stundām_stunda_stundas'.split('_'),
        'hh': 'stundas_stundām_stunda_stundas'.split('_'),
        'd': 'dienas_dienām_diena_dienas'.split('_'),
        'dd': 'dienas_dienām_diena_dienas'.split('_'),
        'M': 'mēneša_mēnešiem_mēnesis_mēneši'.split('_'),
        'MM': 'mēneša_mēnešiem_mēnesis_mēneši'.split('_'),
        'y': 'gada_gadiem_gads_gadi'.split('_'),
        'yy': 'gada_gadiem_gads_gadi'.split('_')
    };
    /**
     * @param withoutSuffix boolean true = a length of time; false = before/after a period of time.
     */
    function format(forms, number, withoutSuffix) {
        if (withoutSuffix) {
            // E.g. "21 minūte", "3 minūtes".
            return number % 10 === 1 && number % 100 !== 11 ? forms[2] : forms[3];
        } else {
            // E.g. "21 minūtes" as in "pēc 21 minūtes".
            // E.g. "3 minūtēm" as in "pēc 3 minūtēm".
            return number % 10 === 1 && number % 100 !== 11 ? forms[0] : forms[1];
        }
    }
    function relativeTimeWithPlural(number, withoutSuffix, key) {
        return number + ' ' + format(units[key], number, withoutSuffix);
    }
    function relativeTimeWithSingular(number, withoutSuffix, key) {
        return format(units[key], number, withoutSuffix);
    }
    function relativeSeconds(number, withoutSuffix) {
        return withoutSuffix ? 'dažas sekundes' : 'dažām sekundēm';
    }

    var lv = moment.defineLocale('lv', {
        months : 'janvāris_februāris_marts_aprīlis_maijs_jūnijs_jūlijs_augusts_septembris_oktobris_novembris_decembris'.split('_'),
        monthsShort : 'jan_feb_mar_apr_mai_jūn_jūl_aug_sep_okt_nov_dec'.split('_'),
        weekdays : 'svētdiena_pirmdiena_otrdiena_trešdiena_ceturtdiena_piektdiena_sestdiena'.split('_'),
        weekdaysShort : 'Sv_P_O_T_C_Pk_S'.split('_'),
        weekdaysMin : 'Sv_P_O_T_C_Pk_S'.split('_'),
        weekdaysParseExact : true,
        longDateFormat : {
            LT : 'HH:mm',
            LTS : 'HH:mm:ss',
            L : 'DD.MM.YYYY.',
            LL : 'YYYY. [gada] D. MMMM',
            LLL : 'YYYY. [gada] D. MMMM, HH:mm',
            LLLL : 'YYYY. [gada] D. MMMM, dddd, HH:mm'
        },
        calendar : {
            sameDay : '[Šodien pulksten] LT',
            nextDay : '[Rīt pulksten] LT',
            nextWeek : 'dddd [pulksten] LT',
            lastDay : '[Vakar pulksten] LT',
            lastWeek : '[Pagājušā] dddd [pulksten] LT',
            sameElse : 'L'
        },
        relativeTime : {
            future : 'pēc %s',
            past : 'pirms %s',
            s : relativeSeconds,
            ss : relativeTimeWithPlural,
            m : relativeTimeWithSingular,
            mm : relativeTimeWithPlural,
            h : relativeTimeWithSingular,
            hh : relativeTimeWithPlural,
            d : relativeTimeWithSingular,
            dd : relativeTimeWithPlural,
            M : relativeTimeWithSingular,
            MM : relativeTimeWithPlural,
            y : relativeTimeWithSingular,
            yy : relativeTimeWithPlural
        },
        dayOfMonthOrdinalParse: /\d{1,2}\./,
        ordinal : '%d.',
        week : {
            dow : 1, // Monday is the first day of the week.
            doy : 4  // The week that contains Jan 4th is the first week of the year.
        }
    });

    return lv;

})));

