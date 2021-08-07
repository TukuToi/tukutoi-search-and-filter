# tukutoi-search-and-filter

Build Searches and Filters for WordPress Posts, Terms and Users.

== Description ==

With TukuToi Search & Filter you can build custom Queries and Front End filters, to search thru your Post, Terms or Users.

== Changelog ==

= 2.12.0 =
* [Added] AJAX search support for on the fly input
* [Added] Sanitize `$_GET` call for AJAX query

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
