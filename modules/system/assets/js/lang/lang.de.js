/*
 * This file has been compiled from: /modules/system/lang/de/client.php
 */
if ($.wn === undefined) $.wn = {}
if ($.oc === undefined) $.oc = $.wn
if ($.wn.langMessages === undefined) $.wn.langMessages = {}
$.wn.langMessages['de'] = $.extend(
    $.wn.langMessages['de'] || {},
    {"markdowneditor":{"formatting":"Formatierung","quote":"Zitat","code":"Code","header1":"\u00dcberschrift 1","header2":"\u00dcberschrift 2","header3":"\u00dcberschrift 3","header4":"\u00dcberschrift 4","header5":"\u00dcberschrift 5","header6":"\u00dcberschrift 6","bold":"Fett","italic":"Kursiv","unorderedlist":"Normale Liste","orderedlist":"Nummerierte Liste","video":"Video","image":"Bild","link":"Link","horizontalrule":"Horizontale Linie","fullscreen":"Vollbild","preview":"Vorschau"},"mediamanager":{"insert_link":"Link aus Medienbibliothek","insert_image":"Bild aus Medienbibliothek","insert_video":"Video aus Medienbibliothek","insert_audio":"Audio aus Medienbibliothek","invalid_file_empty_insert":"Bitte Datei ausw\u00e4hlen.","invalid_file_single_insert":"Bitte nur eine Datei w\u00e4hlen.","invalid_image_empty_insert":"Bitte ein Bilddatei ausw\u00e4hlen.","invalid_video_empty_insert":"Bitte ein Videodatei ausw\u00e4hlen.","invalid_audio_empty_insert":"Bitte eine Audiodatei ausw\u00e4hlen."},"alert":{"confirm_button_text":"OK","cancel_button_text":"Abbrechen","widget_remove_confirm":"Dieses Widget entfernen?"},"datepicker":{"previousMonth":"Vorheriger Monat","nextMonth":"N\u00e4chsten Monat","months":["Januar","Februar","M\u00e4rz","April","Mai","Juni","Juli","August","September","Oktober","November","Dezember"],"weekdays":["Sonntag","Montag","Dienstag","Mittwoch","Donnerstag","Freitag","Samstag"],"weekdaysShort":["So","Mo","Di","Mi","Do","Fr","Sa"]},"colorpicker":{"last_color":"Benutze die zuletzt ausgew\u00e4hlte Farbe","aria_palette":"Farbauswahl-Palette","aria_hue":"Farbtonwahl-Regler","aria_opacity":"Transparenz-Regler"},"filter":{"group":{"all":"Alle"},"scopes":{"apply_button_text":"Anwenden","clear_button_text":"L\u00f6schen"},"dates":{"all":"Alle","filter_button_text":"Filter","reset_button_text":"Zur\u00fccksetzen","date_placeholder":"Datum","after_placeholder":"Nach","before_placeholder":"Vor"},"numbers":{"all":"Alle","filter_button_text":"Filter","reset_button_text":"Zur\u00fccksetzen","min_placeholder":"Min","max_placeholder":"Max"}},"eventlog":{"show_stacktrace":"Stacktrace anzeigen","hide_stacktrace":"Stacktrace ausblenden","tabs":{"formatted":"Formatiert","raw":"Raw"},"editor":{"title":"Quellcode-Editor","description":"Das Betriebssystem sollte so konfiguriert sein, dass es auf eines dieser URL-Schemas h\u00f6rt.","openWith":"\u00d6ffnen mit","remember_choice":"Ausgew\u00e4hlte Option f\u00fcr diese Session merken","open":"\u00d6ffnen","cancel":"Abbrechen"}}}
);

//! moment.js locale configuration v2.22.2

;(function (global, factory) {
   typeof exports === 'object' && typeof module !== 'undefined'
       && typeof require === 'function' ? factory(require('../moment')) :
   typeof define === 'function' && define.amd ? define(['../moment'], factory) :
   factory(global.moment)
}(this, (function (moment) { 'use strict';


    function processRelativeTime(number, withoutSuffix, key, isFuture) {
        var format = {
            'm': ['eine Minute', 'einer Minute'],
            'h': ['eine Stunde', 'einer Stunde'],
            'd': ['ein Tag', 'einem Tag'],
            'dd': [number + ' Tage', number + ' Tagen'],
            'M': ['ein Monat', 'einem Monat'],
            'MM': [number + ' Monate', number + ' Monaten'],
            'y': ['ein Jahr', 'einem Jahr'],
            'yy': [number + ' Jahre', number + ' Jahren']
        };
        return withoutSuffix ? format[key][0] : format[key][1];
    }

    var de = moment.defineLocale('de', {
        months : 'Januar_Februar_März_April_Mai_Juni_Juli_August_September_Oktober_November_Dezember'.split('_'),
        monthsShort : 'Jan._Feb._März_Apr._Mai_Juni_Juli_Aug._Sep._Okt._Nov._Dez.'.split('_'),
        monthsParseExact : true,
        weekdays : 'Sonntag_Montag_Dienstag_Mittwoch_Donnerstag_Freitag_Samstag'.split('_'),
        weekdaysShort : 'So._Mo._Di._Mi._Do._Fr._Sa.'.split('_'),
        weekdaysMin : 'So_Mo_Di_Mi_Do_Fr_Sa'.split('_'),
        weekdaysParseExact : true,
        longDateFormat : {
            LT: 'HH:mm',
            LTS: 'HH:mm:ss',
            L : 'DD.MM.YYYY',
            LL : 'D. MMMM YYYY',
            LLL : 'D. MMMM YYYY HH:mm',
            LLLL : 'dddd, D. MMMM YYYY HH:mm'
        },
        calendar : {
            sameDay: '[heute um] LT [Uhr]',
            sameElse: 'L',
            nextDay: '[morgen um] LT [Uhr]',
            nextWeek: 'dddd [um] LT [Uhr]',
            lastDay: '[gestern um] LT [Uhr]',
            lastWeek: '[letzten] dddd [um] LT [Uhr]'
        },
        relativeTime : {
            future : 'in %s',
            past : 'vor %s',
            s : 'ein paar Sekunden',
            ss : '%d Sekunden',
            m : processRelativeTime,
            mm : '%d Minuten',
            h : processRelativeTime,
            hh : '%d Stunden',
            d : processRelativeTime,
            dd : processRelativeTime,
            M : processRelativeTime,
            MM : processRelativeTime,
            y : processRelativeTime,
            yy : processRelativeTime
        },
        dayOfMonthOrdinalParse: /\d{1,2}\./,
        ordinal : '%d.',
        week : {
            dow : 1, // Monday is the first day of the week.
            doy : 4  // The week that contains Jan 4th is the first week of the year.
        }
    });

    return de;

})));

