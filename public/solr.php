<?php 
require_once('lib.php');

$active_filters_html = null;
$facet_html = null;
$pagination_html = null;
$results_html = null;

$url = "$solr?" . build_search_params($q, $fq, $offset);
$result = json_decode(file_get_contents($url), true);

if ((isset($q) or count($fq) > 0) and isset($result['response']) and
    isset($result['response']['docs']) and
    count($result['response']['docs']) > 0) {

    # pagination
    $data = array(
        'first' => $offset + 1,
        'last' => $offset + $hits_per_page,
        'count' => $result['response']['numFound'],
    );
    if ($offset > 0) {
        $data['previous'] = previous_link($query);
    }
    if ($data['last'] <= $data['count']) {
        $data['next'] = next_link($query);
    }
    $pagination_html = $templates['pagination']($data);

    # results
    $docs = $result['response']['docs'];
    $results = array();
    for ($i = 0; $i < count($docs); $i++) {
        $data = array();
        # raw to begin
        foreach ($hit_fields as $field => $solr_field) {
            $raw_field = $docs[$i][$solr_field];
            if (isset($raw_field)) {
                if (is_array($raw_field)) {
                    $data[$field] = $raw_field[0];
                }
                else {
                    $data[$field] = $raw_field;
                }
            }
        }
        # cleanup
        if (isset($data['thumb'])) {
            $data['thumb'] = str_replace('http:', 'https:', $data['thumb']);
        }
        $data['link'] = "https://exploreuk.uky.edu/catalog/" . $docs[$i]['id'];
        $data['number'] = $offset + $i + 1;
        $results[] = $templates['hit-template']($data);
    }
    $results_html = implode('', $results);
    $front = false;
}
else {
    $front = true;
}

# active filters
$filter_links = array();
if (count($fq) > 0) {
    $navs = array();
    foreach ($fq as $fq_term) {
        preg_match('/(?<name>[^:]+):"(?<value>.*)"/', $fq_term, $matches);
        $name = $matches['name'];
        #$active_filters_html = $name;
        $value = $matches['value'];
        $link = remove_filter($query, $name, $value);
        #$active_filters_html = $link;
        $data_filter = "$name:$value";
        $title = facet_displayname($name) . ':' . $value;
        $navs[] = "<a class=\"close\" href=\"$link\">&times;</a><a href=\"$link\" class=\"selectedNav\" data-filter=\"$data_filter\" title=\"$title\">$title</a><br>";
    }
    $active_filters_html = $templates['chosen-nav-template-php'](array(
        'navs' => implode('', $navs),
    ));
}

# facets
$facet_links = array();
foreach ($facets as $facet) {
    $facet_counts = $result['facet_counts']['facet_fields'][$facet];
    if (count($facet_counts) > 0) {
        $navs_sensible = makeNavsSensible($facet_counts);
        $navs = array();
        foreach ($navs_sensible as $label => $count) {
            $link = add_filter($query, $facet, $label);
            $navs[] = "<a href='$link' title='$label ($count)'>$label</a> ($count)<br>";
        }
        $facet_links[] = $templates['nav-template-php'](array(
            'title' => $facet,
            'navs' => implode('', $navs), #makeNavsSensible($facet_counts),
        ));
    }
}
$facet_html = implode('', $facet_links);
