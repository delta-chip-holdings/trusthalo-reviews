# TrustHalo Reviews

TrustHalo Reviews is a WordPress plugin by Delta Chip Holdings for displaying Google reviews in a lightweight, responsive slider.

## Phase 2 — Free foundation (0.2.0)

This release provides:

- one Google business connected with a Places API (New) key and Place ID;
- server-side requests, so the key is not exposed in front-end markup;
- live, normalized review data with request-scoped deduplication and no persistent review storage;
- a `[trusthalo_reviews]` shortcode;
- a fixed 3/2/1 responsive slider;
- autoplay, arrows, and pagination enabled by default;
- a clear admin interface showing which controls are reserved for Pro;
- WordPress capability, nonce, sanitization, escaping, and uninstall safeguards;
- current Google Maps attribution, direct review links, author credit, provider credit, and ordering disclosure.

Google's Places API selects and limits the review records it returns. TrustHalo Free does not claim to retrieve every Google review.

## Free and Pro boundary

Free stays intentionally useful but focused: one location, one slider, one clean style preset, and fixed controls. The planned Pro product will contain multiple locations, additional layouts, complete styling and responsive controls, an Elementor widget, and an owner-connected all-reviews service.

The future Pro code and licence service will live outside this public repository. The intended protection model is domain-bound licensing, signed server verification, an offline grace period, protected updates/support, and graceful fallback to Free features. No client-distributed PHP plugin can be made completely piracy-proof.

## Local development

1. Place this repository at `wp-content/plugins/trusthalo-reviews`.
2. Activate **TrustHalo Reviews** in WordPress.
3. Configure it at **Settings → TrustHalo Reviews**.
4. Use `[trusthalo_reviews]` on a page.

The plugin has no build step or third-party front-end dependency.

Because Google Maps Platform does not currently permit persistent storage of Places review content, TrustHalo requests data when the shortcode renders. Site owners should consider the billing and performance implications when placing the shortcode on high-traffic pages.

## Security

- Never commit Google API keys or future licensing secrets.
- Restrict Google API keys where the hosting environment allows.
- All Google requests run server-side and use a narrow field mask.
- Administrative refreshes require `manage_options` and a valid nonce.
- Review content is not persisted between page requests.
- Sites using the plugin must maintain public Terms of Use and Privacy Policy pages that satisfy Google Maps Platform requirements.

## Licence

GPL-2.0-or-later. See [LICENSE](LICENSE).
