/*
This file is part of Ext JS 4.2

Copyright (c) 2011-2013 Sencha Inc

Contact:  http://www.sencha.com/contact

GNU General Public License Usage
This file may be used under the terms of the GNU General Public License version 3.0 as
published by the Free Software Foundation and appearing in the file LICENSE included in the
packaging of this file.

Please review the following information to ensure the GNU General Public License version 3.0
requirements will be met: http://www.gnu.org/copyleft/gpl.html.

If you are unsure which license is appropriate for your use, please contact the sales department
at http://www.sencha.com/contact.

Build date: 2013-05-16 14:36:50 (f9be68accb407158ba2b1be2c226a6ce1f649314)
*/
/**
 * German translation
 * 2007-Apr-07 update by schmidetzki and humpdi
 * 2007-Oct-31 update by wm003
 * 2009-Jul-10 update by Patrick Matsumura and Rupert Quaderer
 * 2010-Mar-10 update by Volker Grabsch
 */
Ext4.onReady(function() {
    
    if (Ext4.Date) {
        Ext4.Date.monthNames = ["Januar", "Februar", "März", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember"];
        
        Ext4.Date.defaultFormat = 'd.m.Y';

        Ext4.Date.getShortMonthName = function(month) {
            return Ext4.Date.monthNames[month].substring(0, 3);
        };

        Ext4.Date.monthNumbers = {
            Jan: 0,
            Feb: 1,
            "M\u00e4r": 2,
            Apr: 3,
            Mai: 4,
            Jun: 5,
            Jul: 6,
            Aug: 7,
            Sep: 8,
            Okt: 9,
            Nov: 10,
            Dez: 11
        };

        Ext4.Date.getMonthNumber = function(name) {
            return Ext4.Date.monthNumbers[name.substring(0, 1).toUpperCase() + name.substring(1, 3).toLowerCase()];
        };

        Ext4.Date.dayNames = ["Sonntag", "Montag", "Dienstag", "Mittwoch", "Donnerstag", "Freitag", "Samstag"];

        Ext4.Date.getShortDayName = function(day) {
            return Ext4.Date.dayNames[day].substring(0, 3);
        };
    }

    if (Ext4.util && Ext4.util.Format) {
        Ext4.util.Format.__number = Ext4.util.Format.number;
        Ext4.util.Format.number = function(v, format) {
            return Ext4.util.Format.__number(v, format || "0.000,00/i");
        };

        Ext4.apply(Ext4.util.Format, {
            thousandSeparator: '.',
            decimalSeparator: ',',
            currencySign: '\u20ac',
            // German Euro
            dateFormat: 'd.m.Y'
        });
    }
});

Ext4.define("Ext4.locale.de.view.View", {
    override: "Ext4.view.View",
    emptyText: ""
});

Ext4.define("Ext4.locale.de.grid.plugin.DragDrop", {
    override: "Ext4.grid.plugin.DragDrop",
    dragText: "{0} Zeile(n) ausgewählt"
});

Ext4.define("Ext4.locale.de.tab.Tab", {
    override: "Ext4.tab.Tab",
    closeText: "Diesen Tab schließen"
});

Ext4.define("Ext4.locale.de.form.Basic", {
    override: "Ext4.form.Basic",
    waitTitle: "Bitte warten..."
});

Ext4.define("Ext4.locale.de.form.field.Base", {
    override: "Ext4.form.field.Base",
    invalidText: "Der Wert des Feldes ist nicht korrekt"
});

Ext4.define("Ext4.locale.de.LoadMask", {
    override: "Ext4.LoadMask",
    loadingText: "Lade Daten..."
});

Ext4.define("Ext4.locale.de.view.AbstractView", {
    override: "Ext4.view.AbstractView",
    loadingText: "Lade Daten..."
});

Ext4.define("Ext4.locale.de.picker.Date", {
    override: "Ext4.picker.Date",
    todayText: "Heute",
    minText: "Dieses Datum liegt von dem erstmöglichen Datum",
    maxText: "Dieses Datum liegt nach dem letztmöglichen Datum",
    disabledDaysText: "",
    disabledDatesText: "",
    nextText: "Nächster Monat (Strg/Control + Rechts)",
    prevText: "Vorheriger Monat (Strg/Control + Links)",
    monthYearText: "Monat auswählen (Strg/Control + Hoch/Runter, um ein Jahr auszuwählen)",
    todayTip: "Heute ({0}) (Leertaste)",
    format: "d.m.Y",
    startDay: 1
});

Ext4.define("Ext4.locale.de.picker.Month", {
    override: "Ext4.picker.Month",
    okText: "&#160;OK&#160;",
    cancelText: "Abbrechen"
});

Ext4.define("Ext4.locale.de.toolbar.Paging", {
    override: "Ext4.PagingToolbar",
    beforePageText: "Seite",
    afterPageText: "von {0}",
    firstText: "Erste Seite",
    prevText: "vorherige Seite",
    nextText: "nächste Seite",
    lastText: "letzte Seite",
    refreshText: "Aktualisieren",
    displayMsg: "Anzeige Eintrag {0} - {1} von {2}",
    emptyMsg: "Keine Daten vorhanden"
});

Ext4.define("Ext4.locale.de.form.field.Text", {
    override: "Ext4.form.field.Text",
    minLengthText: "Bitte geben Sie mindestens {0} Zeichen ein",
    maxLengthText: "Bitte geben Sie maximal {0} Zeichen ein",
    blankText: "Dieses Feld darf nicht leer sein",
    regexText: "",
    emptyText: null
});

Ext4.define("Ext4.locale.de.form.field.Number", {
    override: "Ext4.form.field.Number",
    minText: "Der Mindestwert für dieses Feld ist {0}",
    maxText: "Der Maximalwert für dieses Feld ist {0}",
    nanText: "{0} ist keine Zahl",
    decimalSeparator: ","
});

Ext4.define("Ext4.locale.de.form.field.Date", {
    override: "Ext4.form.field.Date",
    disabledDaysText: "nicht erlaubt",
    disabledDatesText: "nicht erlaubt",
    minText: "Das Datum in diesem Feld muss nach dem {0} liegen",
    maxText: "Das Datum in diesem Feld muss vor dem {0} liegen",
    invalidText: "{0} ist kein gültiges Datum - es muss im Format {1} eingegeben werden",
    format: "d.m.Y",
    altFormats: "j.n.Y|j.n.y|j.n.|j.|j/n/Y|j/n/y|j-n-y|j-n-Y|j/n|j-n|dm|dmy|dmY|j|Y-n-j|Y-m-d",
    startDay: 1
});

Ext4.define("Ext4.locale.de.form.field.ComboBox", {
    override: "Ext4.form.field.ComboBox",
    valueNotFoundText: undefined
}, function() {
    Ext4.apply(Ext4.form.field.ComboBox.prototype.defaultListConfig, {
        loadingText: "Lade Daten ..."
    });
});

Ext4.define("Ext4.locale.de.form.field.VTypes", {
    override: "Ext4.form.field.VTypes",
    emailText: 'Dieses Feld sollte eine E-Mail-Adresse enthalten. Format: "user@example.com"',
    urlText: 'Dieses Feld sollte eine URL enthalten. Format: "http:/' + '/www.example.com"',
    alphaText: 'Dieses Feld darf nur Buchstaben enthalten und _',
    alphanumText: 'Dieses Feld darf nur Buchstaben und Zahlen enthalten und _'
});

Ext4.define("Ext4.locale.de.form.field.HtmlEditor", {
    override: "Ext4.form.field.HtmlEditor",
    createLinkText: 'Bitte geben Sie die URL für den Link ein:'
}, function() {
    Ext4.apply(Ext4.form.field.HtmlEditor.prototype, {
        buttonTips: {
            bold: {
                title: 'Fett (Ctrl+B)',
                text: 'Erstellt den ausgewählten Text in Fettschrift.',
                cls: Ext4.baseCSSPrefix + 'html-editor-tip'
            },
            italic: {
                title: 'Kursiv (Ctrl+I)',
                text: 'Erstellt den ausgewählten Text in Schrägschrift.',
                cls: Ext4.baseCSSPrefix + 'html-editor-tip'
            },
            underline: {
                title: 'Unterstrichen (Ctrl+U)',
                text: 'Unterstreicht den ausgewählten Text.',
                cls: Ext4.baseCSSPrefix + 'html-editor-tip'
            },
            increasefontsize: {
                title: 'Text vergößern',
                text: 'Erhöht die Schriftgröße.',
                cls: Ext4.baseCSSPrefix + 'html-editor-tip'
            },
            decreasefontsize: {
                title: 'Text verkleinern',
                text: 'Verringert die Schriftgröße.',
                cls: Ext4.baseCSSPrefix + 'html-editor-tip'
            },
            backcolor: {
                title: 'Text farblich hervorheben',
                text: 'Hintergrundfarbe des ausgewählten Textes ändern.',
                cls: Ext4.baseCSSPrefix + 'html-editor-tip'
            },
            forecolor: {
                title: 'Schriftfarbe',
                text: 'Farbe des ausgewählten Textes ändern.',
                cls: Ext4.baseCSSPrefix + 'html-editor-tip'
            },
            justifyleft: {
                title: 'Linksbündig',
                text: 'Setzt den Text linksbündig.',
                cls: Ext4.baseCSSPrefix + 'html-editor-tip'
            },
            justifycenter: {
                title: 'Zentrieren',
                text: 'Zentriert den Text in Editor.',
                cls: Ext4.baseCSSPrefix + 'html-editor-tip'
            },
            justifyright: {
                title: 'Rechtsbündig',
                text: 'Setzt den Text rechtsbündig.',
                cls: Ext4.baseCSSPrefix + 'html-editor-tip'
            },
            insertunorderedlist: {
                title: 'Aufzählungsliste',
                text: 'Beginnt eine Aufzählungsliste mit Spiegelstrichen.',
                cls: Ext4.baseCSSPrefix + 'html-editor-tip'
            },
            insertorderedlist: {
                title: 'Numerierte Liste',
                text: 'Beginnt eine numerierte Liste.',
                cls: Ext4.baseCSSPrefix + 'html-editor-tip'
            },
            createlink: {
                title: 'Hyperlink',
                text: 'Erstellt einen Hyperlink aus dem ausgewählten text.',
                cls: Ext4.baseCSSPrefix + 'html-editor-tip'
            },
            sourceedit: {
                title: 'Source bearbeiten',
                text: 'Zur Bearbeitung des Quelltextes wechseln.',
                cls: Ext4.baseCSSPrefix + 'html-editor-tip'
            }
        }
    });
});

Ext4.define("Ext4.locale.de.grid.header.Container", {
    override: "Ext4.grid.header.Container",
    sortAscText: "Aufsteigend sortieren",
    sortDescText: "Absteigend sortieren",
    lockText: "Spalte sperren",
    unlockText: "Spalte freigeben (entsperren)",
    columnsText: "Spalten"
});

Ext4.define("Ext4.locale.de.grid.GroupingFeature", {
    override: "Ext4.grid.feature.Grouping",
    emptyGroupText: '(Keine)',
    groupByText: 'Dieses Feld gruppieren',
    showGroupsText: 'In Gruppen anzeigen'
});

Ext4.define("Ext4.locale.de.grid.PropertyColumnModel", {
    override: "Ext4.grid.PropertyColumnModel",
    nameText: "Name",
    valueText: "Wert",
    dateFormat: "d.m.Y"
});

Ext4.define("Ext4.locale.de.grid.BooleanColumn", {
    override: "Ext4.grid.BooleanColumn",
    trueText: "wahr",
    falseText: "falsch"
});

Ext4.define("Ext4.locale.de.grid.NumberColumn", {
    override: "Ext4.grid.NumberColumn",
    format: '0.000,00/i'
});

Ext4.define("Ext4.locale.de.grid.DateColumn", {
    override: "Ext4.grid.DateColumn",
    format: 'd.m.Y'
});

Ext4.define("Ext4.locale.de.form.field.Time", {
    override: "Ext4.form.field.Time",
    minText: "Die Zeit muss gleich oder nach {0} liegen",
    maxText: "Die Zeit muss gleich oder vor {0} liegen",
    invalidText: "{0} ist keine gültige Zeit",
    format: "H:i"
});

Ext4.define("Ext4.locale.de.form.CheckboxGroup", {
    override: "Ext4.form.CheckboxGroup",
    blankText: "Du mußt mehr als einen Eintrag aus der Gruppe auswählen"
});

Ext4.define("Ext4.locale.de.form.RadioGroup", {
    override: "Ext4.form.RadioGroup",
    blankText: "Du mußt einen Eintrag aus der Gruppe auswählen"
});

Ext4.define("Ext4.locale.de.window.MessageBox", {
    override: "Ext4.window.MessageBox",
    buttonText: {
        ok: "OK",
        cancel: "Abbrechen",
        yes: "Ja",
        no: "Nein"
    }    
});

// This is needed until we can refactor all of the locales into individual files
Ext4.define("Ext4.locale.de.Component", {	
    override: "Ext4.Component"
});

