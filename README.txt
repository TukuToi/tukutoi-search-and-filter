=== TukuToi Search & Filter ===
Contributors: TukuToi
Donate link: https://www.tukutoi.com/
Tags: search, filter, order, query, classicpress
Requires at least: 4.9
Stable tag: 2.17.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Build Searches and Filters for WordPress Posts, Terms and Users.

== Description ==

With TukuToi Search & Filter you can build custom Queries and Front End filters, to search thru your Post, Terms or Users.

== Changelog ==

= 2.17.0 =
* [Added] Support Conditionals in Loops, and ShortCodes in attributes in loops, while retaining query capability
* [Changed] ShortCode declarations now support a `inner` key, declaring whether ShortCodes is allowed inside attributes

= 2.16.2 =
* [Fixed] Shenanigans with Nested and Attribute ShortCodeds in Loops

= 2.16.1 =
* [Fixed] ShortCodes where not preprocessed
* [Added] Common Files and logic
* [Changed] Filter name to preprocess ShortCodes

= 2.15.0 =
* [Added] Added missing shortcode param in the GUI for loop
* [Removed] Superfluos files
* [Changed] Definitions class constructor requirements 

= 2.14.0 =
* [Added] Added Full Pagination Support for several loops on same page

= 2.13.0 =
* [Added] Added Pagination Support both for reload and for ajax.
* [Added] ShortCode attributes for loop to determine per-page and custom page var 
* [Added] Support for custom and native pagination vars

= 2.12.0 =
* [Added] AJAX search support for on the fly input
* [Added] Sanitize $_GET call for AJAX query

= 2.11.0 =
* [Added] AJAX search support

= 2.10.0 =
* [Added] Optional Select2 Support in Select Search Inputs on front end

= 2.9.0 =
* [Added] Full support for User, Taxonomy and Post Select Search Dropdowns
* [Added] Full support for Select Dropdowns single and multiple type
* [Added] Fixed the two core functions for user and taxonomy dropdowns and added to a (hopefully temporary) plugin file
* [Added] Added full support for all possible Post Query Vars

= 2.5.1 =
* [Fixed] Avoid PHP Notice when URL unknown URL param is passed
* [Changed] Search Template ShortCode is not anymore internal and thus user can insert thru GUI
* [Added] Search Template ShortCode form has now nice notices about correct usage
* [Added] Results Loop ShortCode form has now nice notices about correct usage and functioning GUI
* [Added] Search ShortCodes "Search By" options for post query
* [Added] Button ShortCodes options

= 2.0.0 =
* [Changed] Using new Plugin structure
* [Changed] Removed templating system and added in-editor templating
* [Added] Added ShortCodes for text search and select search
* [Added] Added ShortCodes for Buttons (Reset, Submit, etc)
* [Added] Added ShortCode for Results loop
* [Changed] Refactor, adhere to WPCS

= 1.0.0 =
* [Added] Initial Release
