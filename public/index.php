<?php 
require_once('solr.php');
?>
<!DOCTYPE html>
<html>
  <head>
    <title>ExploreStatic</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=EDGE" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap -->
    <link href="/bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet" media="screen">
    <link href="/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">

    <link href="css/solrstrap.css" rel="stylesheet" media="screen">
  </head>
  <body screen_capture_injected="true">

    <!--actual visible HTML-->
    <div class="navbar navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <a class="brand" href="/">ExploreStatic</a>
          <form class="navbar-search" style="float:right">
            <div id="search-div">
              <input type="text" class="search-query span8" placeholder="ExploreUK" onfocus="this.value = this.value;" id="solrstrap-searchbox" name="q">
            </div>
          </form>
        </div>
      </div>
    </div>
    <div class="container">
      <div class="row">
        <div id="solrstrap-facets" class="span4">
        <?php #echo $url; ?>
        <?php if (isset($active_filters_html)) {
            echo $active_filters_html;
        } ?>
        <?php if (isset($facet_html)) {
            echo $facet_html;
        } ?>
        </div>
        <div id="solrstrap-hits" class="span8">
        <?php if (isset($pagination_html)) {
            echo $pagination_html;
        } ?>
        <?php if (isset($results_html)) {
            echo $results_html;
        } ?>
        <?php if (isset($pagination_html)) {
            echo $pagination_html;
        } ?>
        <?php if ($front) {
            require_once('home.php');
        } ?>
        </div>
      </div>
    </div>

    <!--load scripts at the bottom-->
    <script type="text/javascript" src="/js/jquery.min.js"></script>
    <script type="text/javascript" src="/bootstrap/js/bootstrap.min.js"></script>
  </body>
</html>
