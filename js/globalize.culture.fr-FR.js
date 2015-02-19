/*
 * Globalize Culture fr-FR
 *
 * http://github.com/jquery/globalize
 *
 * Copyright Software Freedom Conservancy, Inc.
 * Dual licensed under the MIT or GPL Version 2 licenses.
 * http://jquery.org/license
 *
 * This file was generated by the Globalize Culture Generator
 * Translation: bugs found in this file need to be fixed in the generator
 */

(function( window, undefined ) {

var Globalize;

if ( typeof require !== "undefined" &&
	typeof exports !== "undefined" &&
	typeof module !== "undefined" ) {
	// Assume CommonJS
	Globalize = require( "globalize" );
} else {
	// Global variable
	Globalize = window.Globalize;
}

Globalize.addCultureInfo( "fr-FR", "default", {
	name: "fr-FR",
	englishName: "French (France)",
	nativeName: "franÃ§ais (France)",
	language: "fr",
	numberFormat: {
		",": "Â ",
		".": ",",
		"NaN": "Non NumÃ©rique",
		negativeInfinity: "-Infini",
		positiveInfinity: "+Infini",
		percent: {
			",": "Â ",
			".": ","
		},
		currency: {
			pattern: ["-n $","n $"],
			",": "Â ",
			".": ",",
			symbol: "â‚¬"
		}
	},
	calendars: {
		standard: {
			firstDay: 1,
			days: {
				names: ["dimanche","lundi","mardi","mercredi","jeudi","vendredi","samedi"],
				namesAbbr: ["dim.","lun.","mar.","mer.","jeu.","ven.","sam."],
				namesShort: ["di","lu","ma","me","je","ve","sa"]
			},
			months: {
				names: ["janvier","fÃ©vrier","mars","avril","mai","juin","juillet","aoÃ»t","septembre","octobre","novembre","dÃ©cembre",""],
				namesAbbr: ["janv.","fÃ©vr.","mars","avr.","mai","juin","juil.","aoÃ»t","sept.","oct.","nov.","dÃ©c.",""]
			},
			AM: null,
			PM: null,
			eras: [{"name":"ap. J.-C.","start":null,"offset":0}],
			patterns: {
				d: "dd/MM/yyyy",
				D: "dddd d MMMM yyyy",
				t: "HH:mm",
				T: "HH:mm:ss",
				f: "dddd d MMMM yyyy HH:mm",
				F: "dddd d MMMM yyyy HH:mm:ss",
				M: "d MMMM",
				Y: "MMMM yyyy"
			}
		}
	}
});

}( this ));
