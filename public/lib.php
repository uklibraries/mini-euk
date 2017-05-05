<?php 
require_once('vendor/autoload.php');

use LightnCandy\LightnCandy;

# Load precompiled templates
$templates = array();
$templates_dir = 'templates';
foreach (glob("$templates_dir/*.php") as $template) {
    $handle = basename($template, '.php');
    $templates[$handle] = include($template);
}

$solr = 'https://selene.ukpdp.org/solr/select/';

$facets = array(
    'format',
    'source_s',
    'pub_date',
);

$facets_titles = array(
    'format' => 'format',
    'source_s' => 'collection',
    'pub_date' => 'publication year',
);

function facet_displayname($facet) {
    global $facets_titles;
    if (isset($facets_titles[$facet])) {
        return $facets_titles[$facet];
    }
    else {
        return 'unknown';
    }
}

function makeNavsSensible($navs) {
    $newNav = array();
    for ($i =0; $i < count($navs); $i += 2) {
        $newNav[$navs[$i]] = $navs[$i + 1];
    }
    return $newNav;
}

$hit_fields = array(
    'title' => 'title_display',
    'thumb' => 'thumbnail_url_s',
    'source' => 'source_s',
    'pubdate' => 'pub_date',
    'format' => 'format',
);

$hits_per_page = 20;
$id_field = 'id';

$hl = true;
$hl_fl = 'title_display';
$hl_simple_pre = '<em>';
$hl_simple_post = '</em>';
$hl_snippets = 3;

$q = null;
$fq = null;
$offset = null;
$raw_params = array();
if (isset($_SERVER['QUERY_STRING'])) {
    $raw_params = explode('&', str_replace('?', '', $_SERVER['QUERY_STRING']));
}
foreach ($raw_params as $raw_param) {
    preg_match('/(?<key>[^=]+)=(?<value>.*)/', $raw_param, $matches);
    $key = urldecode($matches['key']);
    $value = urldecode($matches['value']);
    if ($key == 'q' and strlen($value) > 0) {
        $q = $value;
    }
    elseif ($key == 'fq[]') {
        if (!isset($fq)) {
            $fq = array();
        }
        $fq[] = $value;
    }
    elseif ($key == 'offset') {
        $offset = intval($value);
    }
}

$query = array(
    'q' => $q,
    'fq' => $fq,
    'offset' => $offset,
);

function previous_link($query) {
    global $hits_per_page;
    $pieces = array();
    if (strlen($query['q']) > 0) {
        $pieces[] = 'q=' . urlencode($query['q']);
    }
    foreach ($query['fq'] as $fq_term) {
        $pieces[] = 'fq[]=' . urlencode($fq_term);
    }
    $offset = $query['offset'] - $hits_per_page;
    if ($offset > 0) {
        $pieces[] = 'offset=' . $offset;
    }
    return '?' . implode('&', $pieces);
}

function next_link($query) {
    global $hits_per_page;
    $pieces = array();
    if (strlen($query['q']) > 0) {
        $pieces[] = 'q=' . urlencode($query['q']);
    }
    foreach ($query['fq'] as $fq_term) {
        $pieces[] = 'fq[]=' . urlencode($fq_term);
    }
    $offset = $query['offset'] + $hits_per_page;
    if ($offset > 0) {
        $pieces[] = 'offset=' . $offset;
    }
    return '?' . implode('&', $pieces);
}

function add_filter($query, $facet, $label) {
    $pieces = array();
    if (strlen($query['q']) > 0) {
        $pieces[] = 'q=' . urlencode($query['q']);
    }
    foreach ($query['fq'] as $fq_term) {
        $pieces[] = 'fq[]=' . urlencode($fq_term);
    }
    $pieces[] = 'fq[]=' . urlencode($facet . ':"' . $label . '"');
    if ($offset > 0) {
        $pieces[] = 'offset=' . $offset;
    }
    return '?' . implode('&', $pieces);
}

function remove_filter($query, $facet, $label) {
    $pieces = array();
    if (strlen($query['q']) > 0) {
        $pieces[] = 'q=' . urlencode($query['q']);
    }
    $sought_term = "$facet:\"$label\"";
    foreach ($query['fq'] as $fq_term) {
        if ($fq_term != $sought_term) {
            $pieces[] = 'fq[]=' . urlencode($fq_term);
        }
    }
    if ($offset > 0) {
        $pieces[] = 'offset=' . $offset;
    }
    return '?' . implode('&', $pieces);
}

function build_search_params($q, $fq, $offset) {
    global $facets;
    $pieces = array();
    $pieces[] = "rows=$hits_per_page";
    $pieces[] = 'wt=json';
    $pieces[] = 'q=' . urlencode($q);
    if ($offset > 0) {
        $pieces[] = "start=$offset";
    }
    if (count($facets) > 0) {
        $pieces[] = 'facet=true';
        $pieces[] = 'facet.mincount=1';
        $pieces[] = 'facet.limit=20';
        foreach ($facets as $facet) {
            $pieces[] = "facet.field=$facet";
        }
    }
    if (count($fq) > 0) {
        foreach ($fq as $spec) {
            $pieces[] = 'fq=' . urlencode($spec);
        }
    }
    if ($hl_fl) {
        $pieces[] = 'hl=true';
        $pieces[] = 'hl.fl=' . urlencode($hl_fl);
        $pieces[] = 'hl.simple.pre=' . urlencode($hl_simple_pre);
        $pieces[] = 'hl.simple.post=' . urlencode($hl_simple_post);
        $pieces[] = 'hl.snippets=' . urlencode($hl_snippets);
    }
    return implode('&', $pieces);
}
