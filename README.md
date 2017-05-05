mini-euk
========

This application is a translation of the [Solrstrap](https://github.com/fergiemcdowall/solrstrap)
project into PHP.  This allows a similar interface to be provided without requiring users to
run JavaScript.  Just like Solrstrap, this uses Bootstrap for styling and Handlebars for templates.

The default application is configured for its main purpose, acting as a demonstration
[ExploreUK](https://exploreuk.uky.edu) browser.  You will need to modify this for your own
situation.

See the installation section for configuration details.


Requirements
------------

* Solr server
* Web server with permission to make select queries on the Solr server
* PHP 5.4+
* [Composer](https://getcomposer.org/)


Installation
------------

0. Extract mini-euk in a convenient and descend into the public directory.

1. Run `composer install`.

2. Run `php compile.php`.

3. Edit the file lib.php.  You may need to modify the following:
   * `$solr` - URL for Solr select querying
   * `$facets` - facets you want to show users, in the order you want them to appear
   * `$facets_titles` - human-readable names for the Solr facet names
   * `$hit_fields` - Solr fields you want to be provided to hit-template, the template for search results
   * `$hits_per_page` - how many results to show per page

4. Edit the file solr.php.  The lines 48--51 are specific to ExploreUK and you may want to change them.

5. Serve the public directory as a PHP-enabled website.


License
-------

Copyright 2017 Michael Slone.

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.


This includes files from Bootstrap and Solrstrap, which are licensed under
the Apache License, Version 2.0.

This includes a file from jQuery, which is licensed under the MIT license.
