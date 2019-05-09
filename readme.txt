=== Expiring Posts ===
Contributors: ivankk
Tags: post-expiry, expiring-posts, expire, expiring, expire-posts
Requires at least: 3.0.1
Tested up to: 5.2
Requires PHP: 5.3
Stable tag: 1.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==

This plugin adds functionality to expire a post on a given date.

This plugin currently only works with the classical editor.

It does this by adding a new "Expires" date field in the Publish box.

By default posts don't expire, but you can add a date instead.

Once that date is reached, the post is marked as expired and is no longer visible to the end user.

== Dev Notes ==
* Dev occurs via https://github.com/ivankruchkoff/wp-expiring-posts
* the new post status is `expired`
* a filter called `exp_disable_expiration_for_this_post` exists to disable this feature on a per-post basis.

== FAQ ==
= If I enable this plugin, what will happen to my existing posts? =
Nothing, the posts current post state will remain unchanged.

= Will I be able to see expired posts? =
Yes, they will be visible in the _expired_ view within the post entry list for your post type.

= Will this plugin work with custom post types too? =
Yes.

= If I have a post set to expire and I use the filter to disable expiry for a speicific post, what happens? =
When the `exp_disable_expiration_for_this_post` filter is used, the UI for control post expiry will not be shown, and the post will not expire. In other words, the filter overrides post expiration.

== Screenshots ==

1. By default a post never expires.
2. But you can add a date instead.
