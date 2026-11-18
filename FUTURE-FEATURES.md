# BlogKit – Future Feature List

Status: ideas only, not committed to a release. Review one by one.

**Out of scope:** Theme Builder / Query Loop template support. Handled by the separate Prime Builder plugin.

| # | Feature | Notes | Decision |
|---|---------|-------|----------|
| 1 | AJAX "Load more" / infinite scroll | Blog Grid, Post Card, Card Grid (style 1), Smart Posts List. Fetches the next ?paged=N page, no extra endpoint | Done (unreleased) |
| 2 | Live search + category filter tabs (Blog Grid, Post Card, Card Grid style 1, Smart Posts List). Tag filter tabs not done | Done (unreleased) |
| 3 | Extra query options: tags, authors, date range, exclude posts, custom post types | Done (unreleased). Shared trait `Frontend/Elementor/Traits/QueryControls.php`, 6 widgets | Done (1.3.4) |
| 4 | Reading time (Blog Grid, Post Card, Card Grid style 1) | Done (unreleased). View count and post-format badge not done: a view counter needs writes on every page view | Partly done |
| 5 | Post Carousel widget (Swiper) | Done (unreleased) | Done |
| 6 | Skeleton loading + lazy-load images | | TBD |
| 7 | Schema.org markup (opt-in ItemList JSON-LD, 6 widgets); heading tag is already selectable | Done (unreleased) |
| 8 | Settings → BlogKit page with widget on/off toggles | Done (unreleased). Default colors and fallback image not done | Partly done |
| 9 | Gutenberg block version | Works without Elementor | TBD |

## Technical follow-ups (from the 1.3.3 audit)
- Load `style.css` only on pages that use a BlogKit widget (`get_style_depends()`).
- Cache `get_terms` results (transient) in Taxonomy Slider/List.
- Cap `posts_per_page` (reject `-1`).
- Wrap remaining SVG/title/excerpt output in `wp_kses` to clear Plugin Check `OutputNotEscaped`.
