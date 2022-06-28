# TukuToi Search & Filter

Build Searches and Filters for WordPress Posts, Terms and Users.

## Description

With TukuToi Search & Filter you can build custom Queries and Front End filters, to search thru your Post, Terms or Users.
The plugin should be used together with TukuToi ShortCodes, to ensure full library of ShortCodes to display data.

## Changelog 

### 2.29.1
[Fixed] Uninstall checked on wrong plugin slug

### 2.29.0
[Added] Support for Custom Taxonomy Queries both in AJAX and Reload.

### 2.28.4
[Fixed] Missing Text Domains and some Comments for CPCS Review.

### = 2.28.3
[Fixed] Undefined pag_arg when search is not ajax.

### = 2.28.2
[Fixed] Template applied to Loop results got duplicated to other loop results when on ajax/same page

### 2.28.1
* [Added] Merge ShortCode Types and declarations instead of overwriting filter argument

### 2.28.0
* [Added] Instance is now passed to the tkt_src_fltr_query_args filter so we can target specific loops

### 2.26.0
* [Fixed] Search, Pagination Reset and Loop now all act on their own instances if several on same page
* [Fixed] Mixing both types of search and loop (AJAX and reload) now works smooth with all search inputs and loops
* [Fixed] Mixing Select2 and non-Select2 on the same search instance now works smooth
* [Fixed] All localised scripts now load on demand only, and build an object to update, instead of appending objects
* [Changed] JavaScript is now NameSpaced for localised, but global usage.

### 2.25.2
* [Added] Filter type (AJAX or Full page Reload) GUI Option
* [Added] Custom paginate Links function with li_classes, ul_classes, a_classes, current_classes support
* [Added] New ShortCode to display a Spinner or else value during AJAX operations
* [Added] Reset functionality with native reset type button on both AJAX and Reload
* [Fixed] Post Query Args post_type was not validated to array if comma separated, but instead just defaulted to text validation (and sanitization). **Teaching moment**: Luckily all TukuToi Plugins have a standard default fallback for sanitization (sanitize_text_field) or this would have been a securtiy issue, but just a bug
* [Fixed] Several Select2 instances on same Searh Form are now working
* [Changed] Several GUI improvements in form of hints and warnings
* [Changed] Privided method to maybe localise scripts

### 2.20.3
* [Fixed] use $.on('event', selector, function()) instead of shorthand $.live('event', function())
* [Added] Filter to filter the main query after Search and Filter settings are passed to it: tkt_src_fltr_query_args
* [Fixed] Custom Category Selector for search inputs did not render custom classes even if passed to shortcode
* [Fixed] Changed query type to be `post_tag` instead of (wrongly) using `tag`

### 2.19.0
* [Added] Full AJAX Pagination support with dynamically updating pagination links
* [Changed] Some code refactor and safety additions

### 2.18.1
* [Added] Support for Custom ShortCodes inside Attributes
* [Fixed] Conditional was broken if only TukuToi ShortCode wihout Search and Filters was active

### 2.17.0
* [Added] Support Conditionals in Loops, and ShortCodes in attributes in loops, while retaining query capability
* [Changed] ShortCode declarations now support a `inner` key, declaring whether ShortCodes is allowed inside attributes

### 2.16.2 
* [Fixed] Shenanigans with Nested and Attribute ShortCodeds in Loops

### 2.16.1
* [Fixed] ShortCodes where not preprocessed
* [Added] Common Files and logic
* [Changed] Filter name to preprocess ShortCodes

### 2.15.0 
* [Added] Added missing shortcode param in the GUI for loop
* [Removed] Superfluos files
* [Changed] Definitions class constructor requirements

### 2.14.0 
* [Added] Added Full Pagination Support for several loops on same page

### 2.13.0 
* [Added] Added Pagination Support both for reload and for ajax.
* [Added] ShortCode attributes for loop to determine per-page and custom page var 
* [Added] Support for custom and native pagination vars

### 2.12.0 
* [Added] AJAX search support for on the fly input
* [Added] Sanitize `$_GET` call for AJAX query

### 2.11.0 
* [Added] AJAX search support

### 2.10.0 
* [Added] Optional Select2 Support in Select Search Inputs on front end

### 2.9.0 
* [Added] Full support for User, Taxonomy and Post Select Search Dropdowns
* [Added] Full support for Select Dropdowns single and multiple type
* [Added] Fixed the two core functions for user and taxonomy dropdowns and added to a (hopefully temporary) plugin file
* [Added] Added full support for all possible Post Query Vars

### 2.5.1 
* [Fixed] Avoid PHP Notice when URL unknown URL param is passed
* [Changed] Search Template ShortCode is not anymore internal and thus user can insert thru GUI
* [Added] Search Template ShortCode form has now nice notices about correct usage
* [Added] Results Loop ShortCode form has now nice notices about correct usage and functioning GUI
* [Added] Search ShortCodes "Search By" options for post query
* [Added] Button ShortCodes options

### 2.0.0 
* [Changed] Using new Plugin structure
* [Changed] Removed templating system and added in-editor templating
* [Added] Added ShortCodes for text search and select search
* [Added] Added ShortCodes for Buttons (Reset, Submit, etc)
* [Added] Added ShortCode for Results loop
* [Changed] Refactor, adhere to WPCS

### 1.0.0 
* [Added] Initial Release
