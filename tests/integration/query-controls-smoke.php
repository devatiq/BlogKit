<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
/**
 * Smoke test: shared query controls (run: wp eval-file tests/integration/query-controls-smoke.php).
 * Requires Elementor and BlogKit to be active.
 */
$fail = 0;
$check = function ($ok, $msg) use (&$fail) {
    echo ($ok ? 'PASS ' : 'FAIL ') . $msg . "\n";
    if (!$ok) { $fail++; }
};
foreach (['BlogGrid', 'BlogClassicGrid', 'PostCard', 'CardGrid', 'FeaturedGrid', 'SmartPostsList'] as $name) {
    $class = "\\BlogKit\\Frontend\\Elementor\\Widgets\\$name\\Main";
    $c = (new $class())->get_controls();
    $check(isset($c['blogkit_post_types'], $c['blogkit_tags'], $c['blogkit_authors'], $c['blogkit_exclude_posts'], $c['blogkit_date_range'], $c['blogkit_date_after'], $c['blogkit_date_before']), "$name has query controls");
}
foreach (['BlogGrid', 'PostCard', 'CardGrid', 'SmartPostsList'] as $name) {
    $class = "\\BlogKit\\Frontend\\Elementor\\Widgets\\$name\\Main";
    $c = (new $class())->get_controls();
    $check(isset($c['blogkit_pagination_type'], $c['blogkit_load_more_text']), "$name has pagination controls");
    $check(in_array('blogkit-load-more', (new $class())->get_script_depends(), true), "$name depends on load-more script");
}
$w = new \BlogKit\Frontend\Elementor\Widgets\BlogGrid\Main();
$m = new ReflectionMethod($w, 'blogkit_apply_query_args');
$m->setAccessible(true);
$a = $m->invoke($w, ['post_type' => 'post'], ['blogkit_post_types' => ['post', 'bogus', 'attachment'], 'blogkit_exclude_posts' => '5, x, 7', 'blogkit_authors' => ['2', 'abc'], 'blogkit_tags' => ['A B'], 'blogkit_date_range' => '30']);
$check($a['post_type'] === ['post'], 'unknown/attachment post types dropped');
$check($a['post__not_in'] === [5, 7], 'exclude ids sanitized');
$check($a['author__in'] === [2], 'author ids sanitized');
$check($a['tag_slug__in'] === ['a-b'], 'tag slug sanitized');
$check($a['date_query'][0]['after'] === '30 days ago', 'relative date range');
$a = $m->invoke($w, [], ['blogkit_date_range' => 'custom', 'blogkit_date_after' => '2020-01-01', 'blogkit_date_before' => 'garbage']);
$check(isset($a['date_query'][0]['after']) && !isset($a['date_query'][0]['before']), 'custom date: invalid value ignored');
$a = $m->invoke($w, ['post_type' => 'post'], []);
$check($a === ['post_type' => 'post'], 'empty settings leave args unchanged');
$q = new WP_Query($m->invoke($w, ['posts_per_page' => 2], ['blogkit_date_range' => '365']));
$check(empty($q->last_error ?? ''), 'WP_Query runs');
$m2 = new ReflectionMethod($w, 'blogkit_output_schema');
$m2->setAccessible(true);
$tid = wp_insert_post(['post_title' => 'Schema </script><b>test & co', 'post_status' => 'publish']);
$q = new WP_Query(['p' => $tid, 'posts_per_page' => 3]);
ob_start();
$m2->invoke($w, $q, ['blogkit_schema' => '']);
$off = ob_get_clean();
$check('' === $off, 'schema off prints nothing');
ob_start();
$m2->invoke($w, $q, ['blogkit_schema' => 'yes']);
$on = ob_get_clean();
$check(false !== strpos($on, 'application/ld+json') && 1 === substr_count($on, '</script>'), 'schema on prints one script, title cannot break out');
preg_match('#>\s*(\{.*\})\s*</script>#s', $on, $mm);
$json = json_decode($mm[1] ?? '', true);
$check(is_array($json) && 'ItemList' === $json['@type'] && 1 === $json['itemListElement'][0]['position'], 'schema is valid JSON ItemList');
wp_delete_post($tid, true);
exit($fail ? 1 : 0);
