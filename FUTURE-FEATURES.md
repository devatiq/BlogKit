# BlogKit – Feature List

**Out of scope:** Theme Builder / Query Loop template support. Handled by the separate Prime Builder plugin.

| # | Feature | Status |
|---|---------|--------|
| 1 | AJAX "Load more" / infinite scroll (Blog Grid, Post Card, Card Grid style 1, Smart Posts List) | Done, 1.3.6 |
| 2 | Live search + category/tag filter tabs (same four widgets) | Done, 1.3.9 (tag filter 1.3.10) |
| 3 | Extra query options: post types, tags, authors, exclude posts, date range | Done, 1.3.4 |
| 4 | Reading time | Done, 1.3.8 |
| 4b | Post format badge (Post Card, Card Grid style 1, Post Carousel) | Done, unreleased |
| 5 | Post Carousel widget (Swiper) | Done, 1.3.9 |
| 6 | Skeleton loading + lazy-load images | Skipped: WordPress already lazy-loads thumbnails |
| 7 | Schema.org ItemList JSON-LD (opt-in) | Done, 1.3.7 |
| 8 | Settings → BlogKit: widget on/off (1.3.9), fallback image (1.3.10), accent color (unreleased) | Done |
| 9 | Gutenberg block version | Skipped: needs its own build tooling |
| 10 | View count | Skipped: needs a database write on every page view |

## Technical follow-ups (from the 1.3.3 audit)
- Load `style.css` only on pages that use a BlogKit widget (`get_style_depends()`).
- Cache `get_terms` results (transient) in Taxonomy Slider/List.
- Cap `posts_per_page` (reject `-1`).
- Wrap remaining SVG/title/excerpt output in `wp_kses` to clear Plugin Check `OutputNotEscaped`.
