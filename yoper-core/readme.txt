=== Yoper Core ===
Contributors: yoper
Requires at least: 6.0
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 0.1.0
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Tags: multisite, utilities, core

== Description ==
Core functionality plugin for Yoper sites. Works in single installs and is multisite-aware, providing a place to register shared content types, settings, and admin tools.

== Installation ==
1. Upload the `yoper-core` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the "Plugins" menu in WordPress or network-activate it for Multisite.
3. Adjust settings under the Yoper Core menu.

== Frequently Asked Questions ==
= Is it safe to network-activate? =
Yes. The plugin detects multisite, runs activation routines per site, and applies network defaults to new sites.

== Changelog ==
= 0.1.0 =
* Initial release with base structure, multisite-aware activation, and placeholder CPT/admin settings.
