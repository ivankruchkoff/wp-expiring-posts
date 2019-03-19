# Expiring Posts
This plugin adds functionality to expire a post on a given date.

It does this by adding a new "Expires" date field in the Publish box. 

By default a post never expires:

![Default expiry state](expiry_default.jpg)

But you can add a date instead:

![Expiry set](expiry_set.jpg)

Once that date is reached, the post is marked as expired and is no longer visible to the end user.

### Dev Notes
* the new post status is `expired`
* a filter called `exp_disable_expiration_for_this_post` exists to disable this feature on a per-post basis.

### FAQ
##### If I turn on this plugin, what will happen to my old posts?
Nothing. The default state is to never expire.

##### Will I be able to see any expired plugins?
Yes, they will be visible in the list of entries for you rpost type.

##### Does this work with custom post types too?
Yes.

##### If I have posts set to expire and I use the filter to disable expiry for that post, what happens?
The UI for control post expiry will not be shown, and the post will not expire. IOW the filter wins.
