=== Plugin Name ===
Contributors: beda.s
Donate link: https://www.tukutoi.com/
Tags: query, search, loop, grids
Requires at least: 4.9
Tested up to: 5.7
Stable tag: 1.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Build Searches and Filters for WordPress Posts, Terms and Users.

== Description ==

With TukuToi Search & Filter you can build custom Queries and Front End filters, to search thru your Post, Terms or Users.
Using a templating system you can build fully customized Search inputs or/and Custom loops.

An example setup, in any Page or else PHP template (or through a ShortCode), instantiate the search and loops like below shown.

**Call Loop of Custom Posts using a Custom Template:**
/**
 * Define a set of custom query arguments 
 */
```
$args = array(
		'post_type'              => array( 'post' ),
		'post_status'            => array( 'publish' ),
		'posts_per_page'         => -1,
		'order'                  => 'DESC',
		'orderby'                => 'date',
		'cache_results'          => true,
		'update_post_meta_cache' => true,
		'update_post_term_cache' => true,
	);
tkt_results_render($args, 'your-unique-name', 'path-to-custom-template.php', 'path-to-error-template.php');
```
'path-to-custom-template.php', 'path-to-error-template.php' are simple paths to your custom PHP file where you can (like in a Theme template) design your loop item using the known Theme Template tags like the_title, the_content, and so on.

**Call Loop of Custom Posts using a Custom Template with a Custom Search:**
```
tkt_search_render('your-unique-name', 'path-to-search-form-template.php');

$args = array(
		'post_type'              => array( 'post' ),
		'post_status'            => array( 'publish' ),
		'posts_per_page'         => -1,
		'order'                  => 'DESC',
		'orderby'                => 'date',
		'cache_results'          => true,
		'update_post_meta_cache' => true,
		'update_post_term_cache' => true,
	);
tkt_results_render($args, 'your-unique-name', 'path-to-custom-template.php', 'path-to-error-template.php');
```

Again here everything is as in the first example just that we added a custom search.
The Custom Search can be a HTML Form using little PHP that sends a form with input names matching the possible Query Arguments.
Any query argument set in the Search form *and* in the results render method will be overwritten by the results render method.
This means you can set a *query filter* in the render method and then filter thru those results using the search render method.

'your-unique-name' is simply a string or hash to uniquely identify and bind the search and results instances to each other.
This way you can have more than one group of instances on each page.

Using ShortCodes, the simples version is to use them like this in any page or post.
```
[tkt_search]
[tkt_loop]
```

The ShortCodes currently provide the following attributes:
`[tkt_search]`:
`instance`	The Unique Instance name. 
`template`	The Template path.
`[tkt_loop]`
`args`  	Currently hardcoded array of default query args.
`instance`	The Unique Instance name. 
`template`	The Template path.
`error`		A path to a template to use for no results found.
