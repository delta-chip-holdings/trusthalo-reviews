# TrustHalo Reviews

TrustHalo Reviews is a WordPress plugin by Delta Chip Holdings for displaying Google reviews in a lightweight, responsive slider.

## Phase 3 — TrustHalo Connect onboarding (0.3.0)

This release removes the customer-facing Google Places API key, billing, and Place ID setup. It prepares TrustHalo for a simple customer journey:

1. Install TrustHalo Reviews.
2. Click **Connect Google Business Profile**.
3. Authorize the Google account that manages the business.
4. Choose a location and display reviews with `[trusthalo_reviews]`.

The secure TrustHalo Connect service and Google Business Profile API approval are still in progress. The connection button is therefore disabled in this release, and the shortcode only shows a setup notice to WordPress administrators.

TrustHalo does not ask customers to create a Google Cloud project, enable billing, or manage API keys.

## Free and Pro boundary

Free stays intentionally useful but focused: one location, one slider, one clean style preset, and fixed controls. The planned Pro product will contain multiple locations, additional layouts, complete styling and responsive controls, an Elementor widget, and owner-connected review features.

The future Pro code and licence service will live outside this public repository. The intended protection model is domain-bound licensing, signed server verification, an offline grace period, protected updates/support, and graceful fallback to Free features. No client-distributed PHP plugin can be made completely piracy-proof.

## Local development

1. Place this repository at `wp-content/plugins/trusthalo-reviews`.
2. Activate **TrustHalo Reviews** in WordPress.
3. Open **Settings → TrustHalo Reviews**.
4. Add `[trusthalo_reviews]` to a page, post, or shortcode-compatible builder widget.
5. Confirm that the administrator setup notice appears and visitors see no incomplete slider.

The plugin has no build step or third-party front-end dependency.

## Security

- The public plugin collects no Google API keys and makes no direct Google API requests.
- Never commit future TrustHalo Connect or licensing secrets.
- The future connection will require the business owner's Google authorization.
- Review presentation and any future service must comply with the applicable Google data and attribution requirements.

## Licence

GPL-2.0-or-later. See [LICENSE](LICENSE).
