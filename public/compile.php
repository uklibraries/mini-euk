<?php
require_once('lib.php');

use LightnCandy\LightnCandy;

$template_dir = '../handlebars';

$dest_dir = 'templates';

foreach (glob("$template_dir/*.handlebars") as $source) {
    $basename = basename($source, '.handlebars');
    $target = "$dest_dir/$basename.php";
    print "* $source -> $target\n";
    $phpStr = LightnCandy::compile(file_get_contents($source), array(
        'flags' => LightnCandy::FLAG_ELSE,
        'helpers' => array(
            'facet_displayname' => function($facet) {
                return facet_displayname($facet);
            }
        )
    ));
    file_put_contents($target, '<?php ' . $phpStr . '?>');
}
