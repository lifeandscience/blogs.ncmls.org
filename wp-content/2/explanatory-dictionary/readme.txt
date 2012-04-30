=== Explanatory Dictionary ===
Contributors: Ruben Sargsyan
Donate link: http://rubensargsyan.com/donate/
Tags: explanatory dictionary, vocabulary, glossary, lexicon, explain, explanation, tooltips, descriptions
Requires at least: 2.6
Tested up to: 3.0.1

This plugin is used when there are words, words expressions or sentences to be explained in the posts or pages of your wordpress blog.

== Description ==

This plugin is used when there are some words, words expressions or sentences to be explained in the posts or pages of your wordpress blog. It will help the visitors to read the explanations of the words (words expressions, sentences) you need via tooltips. It can also be used as a glossary.

== Installation ==

1. Upload the explanatory-dictionary directory (including all files within) to the /wp-content/plugins/ directory
2. Activate the plugin through the Plugins menu in WordPress
3. If you want to show all words (words expressions, sentences) with their explanations like a glossary in a post or a page, so add [explanatory dictionary] in it.

== Frequently Asked Questions ==

= There is no explanation tooltip after activation. =

Make sure that the code `<?php wp_footer(); ?>` is included in your theme's `footer.php` file just before the `</body>` line.

== Screenshots ==

1. The Explanation Tooltips
2. Explanatory Dictionary Options
3. Manage Explanatory Dictionary

== Changelog ==

= 2.0 =

* Now you can set an external CSS for styling the explanatory dictionary.
* You can separate the shown words (words expressions, sentences) of the explanatory dictionary by the alphabet.
* You can exclude the words (words expressions, sentences) from being explained by getting those words (words expressions, sentences) into [no explanation][/no explanation] tags.

= 1.5 =

* The bug which shows an error when you use several characters in explanatory dictionary is fixed.

= 1.4 =

* You can add words (words expressions, sentences) which are already existed in the words (words expressions, sentences) you added before.
* You can edit the explanations of the dictionary.
* The problem with posts images captions is solved.

= 1.3 =

* Added new options - "Set Explaining Word (Words Expression, Sentence) Style", "Exclude", "Limit".

= 1.2 =

* The problem that the tooltip appears not near the word in the several themes is already solved.

= 1.1 =

* Added new option "unicode".
* Now you can show all words (words expressions, sentences) with their explanations.
* Fixed some bugs.

= 1.0 =
* First release.