# ebay-description-scraper
An automated scraper that updates descriptions from an ebay feed

This is based on a LAMP stack (MySQL)

This is a specific use-case. I had to create a way to update the missing descriptions from a listing on a website where the listing used ebay-feeds.

A single part of an ebay-feed would be included in a specific listing inside an iFrame, however a description was needed on that listing page.

So this script runs an automated scraper that visits the iFrame's source and grabs a set of defined char-limit and places them inside that particular description.

This is assuming that your site has a database table that handles the listings and there is a description feed.

It is a pretty specific use-case and I use the HTMLSimpleDom Scraper which normally you get from sourceforge. It's included in here. I realize you'd have to prove that it's legitimate with a checksum or something.

I also included jQuery-latest.

Hopefully it's useful to someone.

It does have a visual componenet so you're not just sitting there starting at a blank screen with a refreshing icon on the tab.
