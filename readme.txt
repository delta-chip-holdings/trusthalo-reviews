=== TrustHalo Reviews ===
Contributors: deltachipholdings
Tags: google reviews, reviews, testimonial, slider, business reviews
Requires at least: 6.2
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 0.2.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Display reviews from one Google business in a clean, responsive slider.

== Description ==

TrustHalo Reviews retrieves review data live on the WordPress server with Google Places API (New) and renders a lightweight slider through the `[trusthalo_reviews]` shortcode.

The Free edition intentionally provides a focused setup:

* One Google business
* One clean slider layout
* Fixed responsive columns: 3 desktop, 2 tablet, 1 mobile
* Autoplay, arrows, and pagination enabled
* Server-side API requests so the API key is not exposed in page markup
* Required Google Maps, review-author, direct-source, ordering, and provider attributions

Google currently determines and limits the reviews returned by the Places API. TrustHalo cannot guarantee that every review will be returned in the Free API-based connection.

== Installation ==

1. Upload the `trusthalo-reviews` folder to `/wp-content/plugins/` or install its ZIP file.
2. Activate TrustHalo Reviews.
3. Go to Settings > TrustHalo Reviews.
4. Enter a Google Places API key with Places API (New) enabled and the business Place ID.
5. Save, then select "Test Google connection".
6. Add `[trusthalo_reviews]` to a page, post, or shortcode-compatible builder widget.

== Frequently Asked Questions ==

= Does TrustHalo expose my Google API key? =

No. Requests are made by the WordPress server. The key is stored in the WordPress options table and never printed in slider markup or front-end JavaScript.

= Why do I see only a few reviews? =

Google Places API returns a limited, Google-selected review set. Owner-connected review retrieval is reserved for a future Pro service.

= Does TrustHalo store the reviews? =

No. Current Google Maps Platform rules do not allow TrustHalo to persistently cache review content. Data is retrieved live when the shortcode is rendered and reused only within that single WordPress request.

= Are there Google Maps Platform requirements for my website? =

Yes. Google requires appropriate attribution and publicly accessible Terms of Use and Privacy Policy pages. TrustHalo renders the content-level attributions, but each site owner remains responsible for their own policies and Google Maps Platform account compliance.

== Changelog ==

= 0.2.0 =
* Added secure server-side Places API (New) connection.
* Added live Places API retrieval with request-scoped deduplication.
* Added fixed responsive Free slider with autoplay, arrows, and dots.
* Added transparent Free/Pro feature locks in the settings interface.
* Added sanitization, nonces, capability checks, and uninstall cleanup.
* Added Google Maps attribution, direct review links, relevance disclosure, and third-party provider credits.
