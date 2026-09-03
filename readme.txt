=== TrustHalo Reviews ===
Contributors: deltachipholdings
Tags: google reviews, reviews, testimonial, slider, business reviews
Requires at least: 6.2
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 0.3.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Display Google reviews from a connected Business Profile in a clean, responsive slider.

== Description ==

TrustHalo Reviews is being built around TrustHalo Connect: the business owner will sign in to Google, select the Business Profile they manage, and display reviews through the `[trusthalo_reviews]` shortcode.

Customers will not need to create a Google Cloud project, enter a Places API key, find a Place ID, or enable Google billing.

TrustHalo Connect is not live in version 0.3.0. The settings screen shows the intended onboarding flow, while the secure connection service and Google Business Profile API approval are completed. The shortcode shows a setup notice only to site administrators; it remains hidden from visitors until a connection exists.

The Free edition will intentionally provide a focused setup:

* One Google business
* One clean slider layout
* Fixed responsive columns: 3 desktop, 2 tablet, 1 mobile
* Autoplay, arrows, and pagination enabled

== Installation ==

1. Upload the `trusthalo-reviews` folder to `/wp-content/plugins/` or install its ZIP file.
2. Activate TrustHalo Reviews.
3. Go to Settings > TrustHalo Reviews.
4. Add `[trusthalo_reviews]` to a page, post, or shortcode-compatible builder widget.
5. Wait for TrustHalo Connect to launch before connecting a Google Business Profile.

== Frequently Asked Questions ==

= Do I need Google billing or an API key? =

No. TrustHalo Connect will manage the approved Google connection so customers do not have to configure Google Cloud.

= Why does the shortcode not show reviews yet? =

TrustHalo Connect and Google Business Profile API access are still being prepared. Until they are live, the shortcode is intentionally hidden from visitors.

= Will TrustHalo store reviews? =

The future service and plugin will follow the applicable Google data, attribution, and authorization requirements.

== Changelog ==

= 0.3.0 =
* Removed customer Google Places API key and Place ID setup.
* Removed direct Google Places calls from customer WordPress sites.
* Added honest TrustHalo Connect onboarding state.
* Updated documentation for the future owner-authorized connection.

= 0.2.0 =
* Added secure server-side Places API (New) connection.
* Added live Places API retrieval with request-scoped deduplication.
* Added fixed responsive Free slider with autoplay, arrows, and dots.
* Added transparent Free/Pro feature locks in the settings interface.
