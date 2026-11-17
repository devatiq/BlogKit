# BlogKit – Future Feature List

Status: ideas only, not committed to a release. Review one by one.

**Out of scope:** Theme Builder / Query Loop template support. Handled by the separate Prime Builder plugin.

| # | Feature | Notes | Decision |
|---|---------|-------|----------|
| 1 | AJAX "Load more" / infinite scroll | Blog Grid, Card Grid | TBD |
| 2 | Live search + category/tag filter tabs | | TBD |
| 3 | Extra query options: tags, authors, date range, exclude posts, custom post types | | TBD |
| 4 | Reading time, view count, post-format badge | | TBD |
| 5 | Post carousel/slider widget | Swiper already bundled | TBD |
| 6 | Skeleton loading + lazy-load images | | TBD |
| 7 | Schema.org markup, SEO-friendly heading hierarchy | | TBD |
| 8 | Global settings page (widget toggles, default colors, fallback image) | Widget toggle option already exists | TBD |
| 9 | Gutenberg block version | Works without Elementor | TBD |

## Technical follow-ups (from the 1.3.3 audit)
- Load `style.css` only on pages that use a BlogKit widget (`get_style_depends()`).
- Cache `get_terms` results (transient) in Taxonomy Slider/List.
- Cap `posts_per_page` (reject `-1`).
- Wrap remaining SVG/title/excerpt output in `wp_kses` to clear Plugin Check `OutputNotEscaped`.
