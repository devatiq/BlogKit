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
foreach (['BlogGrid', 'PostCard', 'CardGrid'] as $name) {
    $class = "\\BlogKit\\Frontend\\Elementor\\Widgets\\$name\\Main";
    $c = (new $class())->get_controls();
    $check(isset($c['blogkit_show_reading_time'], $c['blogkit_words_per_minute']), "$name has reading time controls");
}
$rt = new ReflectionMethod($w, 'blogkit_get_reading_time');
$rt->setAccessible(true);
$p1 = wp_insert_post(['post_title' => 'RT 450', 'post_status' => 'publish', 'post_content' => '<p>' . trim(str_repeat('word ', 450)) . '</p>[gallery]']);
$p2 = wp_insert_post(['post_title' => 'RT empty', 'post_status' => 'publish', 'post_content' => '']);
$p3 = wp_insert_post(['post_title' => 'RT bn', 'post_status' => 'publish', 'post_content' => trim(str_repeat('বাংলা লেখা ', 150))]);
$check(3 === $rt->invoke($w, $p1, 200), '450 words at 200 wpm = 3 min');
$check(2 === $rt->invoke($w, $p1, 250), '450 words at 250 wpm = 2 min');
$check(2 === $rt->invoke($w, $p1, 9999), 'wpm clamped to 400 max (450 words = 2 min)');
$check(1 === $rt->invoke($w, $p2, 200), 'empty post = 1 min minimum');
$check(2 === $rt->invoke($w, $p3, 200), 'unicode (Bengali) words counted (300 words = 2 min)');
foreach ([$p1, $p2, $p3] as $pid) { wp_delete_post($pid, true); }
foreach (['BlogGrid', 'PostCard', 'CardGrid', 'SmartPostsList'] as $name) {
    $class = "\\BlogKit\\Frontend\\Elementor\\Widgets\\$name\\Main";
    $c = (new $class())->get_controls();
    $check(isset($c['blogkit_show_filter'], $c['blogkit_show_search'], $c['blogkit_filter_all_text'], $c['blogkit_search_placeholder']), "$name has filter/search controls");
    $check(in_array('blogkit-filter', (new $class())->get_script_depends(), true), "$name depends on filter script");
}
$fw = new \BlogKit\Frontend\Elementor\Widgets\BlogGrid\Main(['id' => 'ft1', 'elType' => 'widget', 'widgetType' => 'blogkit-blog-grid', 'settings' => []], []);
$fa = new ReflectionMethod($fw, 'blogkit_apply_filter_args');
$fa->setAccessible(true);
$cat = wp_insert_term('BK Filter Test', 'category');
$cat_slug = get_term($cat['term_id'])->slug;
$pid = wp_insert_post(['post_title' => 'BK filter post', 'post_status' => 'publish', 'post_category' => [$cat['term_id']]]);
$on = ['blogkit_show_filter' => 'yes', 'blogkit_show_search' => 'yes'];
$_GET['blogkit_f_ft1'] = $cat_slug;
$_GET['blogkit_s_ft1'] = '  needle <b>x</b> ';
$a = $fa->invoke($fw, ['category_name' => 'other'], $on);
$check($a['category_name'] === $cat_slug, 'valid filter category overrides widget category');
$check($a['s'] === 'needle x', 'search term sanitized');
$_GET['blogkit_f_ft1'] = 'does-not-exist';
$a = $fa->invoke($fw, ['category_name' => 'other'], $on);
$check($a['category_name'] === 'other', 'unknown category slug ignored');
$_GET['blogkit_s_ft1'] = str_repeat('a', 300);
$a = $fa->invoke($fw, [], $on);
$check(strlen($a['s']) === 100, 'search term limited to 100 chars');
$a = $fa->invoke($fw, [], ['blogkit_show_filter' => '', 'blogkit_show_search' => '']);
$check([] === $a, 'filters ignored when switches are off');
$_GET['blogkit_f_other'] = $cat_slug;
unset($_GET['blogkit_f_ft1'], $_GET['blogkit_s_ft1']);
$a = $fa->invoke($fw, [], $on);
$check([] === $a, 'params of another widget instance ignored');
$fb = new ReflectionMethod($fw, 'blogkit_render_filter_bar');
$fb->setAccessible(true);
$_GET['blogkit_f_ft1'] = $cat_slug;
ob_start();
$fb->invoke($fw, $on + ['category' => [$cat_slug]]);
$html = ob_get_clean();
$check(false !== strpos($html, 'blogkit-filter-bar') && false !== strpos($html, 'is-active') && false !== strpos($html, 'blogkit-filter-search'), 'filter bar renders tabs and search');
$_GET = [];
wp_delete_post($pid, true);
wp_delete_term($cat['term_id'], 'category');
$pc = new \BlogKit\Frontend\Elementor\Widgets\PostCarousel\Main();
$c = $pc->get_controls();
$check('blogkit-post-carousel' === $pc->get_name(), 'Post Carousel widget name');
$check(isset($c['slides_per_view'], $c['space_between'], $c['autoplay'], $c['loop'], $c['blogkit_tags'], $c['blogkit_schema'], $c['blogkit_date_range']), 'Post Carousel has slider + query controls');
$check(in_array('blogkit-post-carousel', $pc->get_script_depends(), true), 'Post Carousel script dependency');
$opts = array_keys(\BlogKit\Admin\Settings::widgets());
$conf = file_get_contents(BLOGKIT_PATH . 'Frontend/Elementor/Configuration.php');
$missing = array_filter($opts, function ($o) use ($conf) { return false === strpos($conf, "'$o'"); });
$check(10 === count($opts) && [] === $missing, 'Settings page lists every widget option used by Configuration');
$check(0 === \BlogKit\Admin\Settings::sanitize_toggle(null) && 0 === \BlogKit\Admin\Settings::sanitize_toggle('') && 1 === \BlogKit\Admin\Settings::sanitize_toggle('1'), 'settings toggle sanitizer');
// Tag filter tabs.
$tg = wp_insert_term('BK Tag Test', 'post_tag');
$tag_slug = get_term($tg['term_id'])->slug;
$tp = wp_insert_post(['post_title' => 'BK tag post', 'post_status' => 'publish', 'tags_input' => [$tag_slug]]);
$c = $fw->get_controls();
$check(isset($c['blogkit_filter_taxonomy']), 'filter taxonomy control exists');
$tag_on = ['blogkit_show_filter' => 'yes', 'blogkit_filter_taxonomy' => 'post_tag'];
$_GET['blogkit_f_ft1'] = $tag_slug;
$a = $fa->invoke($fw, ['tag_slug__in' => ['x'], 'category_name' => 'keep'], $tag_on);
$check($a['tag_slug__in'] === [$tag_slug] && 'keep' === $a['category_name'], 'tag filter sets tag_slug__in and leaves category alone');
$_GET['blogkit_f_ft1'] = 'uncategorized';
$a = $fa->invoke($fw, [], $tag_on);
$check([] === $a, 'category slug is not accepted in tag mode');
$_GET['blogkit_f_ft1'] = $tag_slug;
ob_start();
$fb->invoke($fw, $tag_on + ['blogkit_tags' => [$tag_slug]]);
$html = ob_get_clean();
$check(false !== strpos($html, 'data-slug="' . $tag_slug . '"') && false === strpos($html, 'data-slug="uncategorized"'), 'tag tabs list only tags');
$_GET = [];
wp_delete_post($tp, true);
wp_delete_term($tg['term_id'], 'post_tag');
// Fallback image setting.
delete_option('blogkit_fallback_image');
$check(false !== strpos(\BlogKit\Admin\Settings::fallback_image(), 'placeholder.png'), 'fallback image defaults to built-in placeholder');
update_option('blogkit_fallback_image', 'https://example.com/fb.jpg');
$check('https://example.com/fb.jpg' === \BlogKit\Admin\Settings::fallback_image(), 'fallback image setting is used');
delete_option('blogkit_fallback_image');
// Format badge and accent color.
foreach (['PostCard', 'CardGrid', 'PostCarousel'] as $name) {
    $class = "\BlogKit\Frontend\Elementor\Widgets\\$name\Main";
    $check(isset((new $class())->get_controls()['blogkit_show_format_badge']), "$name has format badge control");
}
$fbm = new ReflectionMethod($w, 'blogkit_render_format_badge');
$fbm->setAccessible(true);
$vp = wp_insert_post(['post_title' => 'BK video post', 'post_status' => 'publish']);
set_post_format($vp, 'video');
$sp = wp_insert_post(['post_title' => 'BK standard post', 'post_status' => 'publish']);
$GLOBALS['post'] = get_post($vp);
setup_postdata($GLOBALS['post']);
ob_start();
$fbm->invoke($w, ['blogkit_show_format_badge' => 'yes']);
$h = ob_get_clean();
$check(false !== strpos($h, 'blogkit-format-video') && false !== strpos($h, 'Video'), 'video post shows Video badge');
ob_start();
$fbm->invoke($w, ['blogkit_show_format_badge' => '']);
$check('' === ob_get_clean(), 'badge off prints nothing');
$GLOBALS['post'] = get_post($sp);
setup_postdata($GLOBALS['post']);
ob_start();
$fbm->invoke($w, ['blogkit_show_format_badge' => 'yes']);
$check('' === ob_get_clean(), 'standard post has no badge');
wp_reset_postdata();
wp_delete_post($vp, true);
wp_delete_post($sp, true);
$check('#12ab9f' === \BlogKit\Admin\Settings::sanitize_color(' #12ab9f ') && '' === \BlogKit\Admin\Settings::sanitize_color('red; x') && '' === \BlogKit\Admin\Settings::sanitize_color(null), 'accent color sanitizer accepts hex only');
exit($fail ? 1 : 0);
