=== Q&A ===
Contributors: scribu, mohanjith, hakan
Tags: questions, answers, community, Q&A, stackoverflow, wordpress-plugins
Requires at least: 3.1
Tested up to: 3.4.1
Stable tag: trunk

Q&A allows any WordPress site to have a fully featured questions and answers section - just like StackOverflow, Yahoo Answers, Quora and more...

== Description ==

Q&A allows any WordPress site to have a fully featured questions and answers section - just like StackOverflow, Yahoo Answers, Quora and more...except better :)

You've seen how engaging, informative, and just plain fun Q&A sites such as Quora can be.

With this plugin, you can bring full Questions and Answers functionality to any WordPress or BuddyPress site in mere minutes!

Chock-full of features such as:

* Full **front-end** capability - users don't ever have to see your site's admin back-end
* **WYSIWYG** editing of both questions and answers
* Snazzy **voting** for both questions and answers
* Integrated **reputation points** system
* Dedicated **user profile** pages
* Easy theme integration using **widgets**
* Fully **customizable** using dedicated template files

This extensive and powerful plugin covers all the question and answer bases right out of the box while easily installed and fully operational in moments -- and highly customizable too!

== Notes ==

Newly registered users are automatically logged in. To prevent spam bots from running amok, we strongly recommend installing this free plugin:

http://wordpress.org/extend/plugins/stop-spammer-registrations-plugin/

== Installation ==

1. Activate plugin.
2. Go to Questions -> Settings to assign capabilities to the roles of your choosing.
3. Go to http://yoursite.com/questions/ask/ to create your first question.

= Styling =

Copy the php files from default-templates into your theme folder and start customizing.

To disable the default CSS, add the following line to your theme's functions.php file:

`add_theme_support( 'qa_style' );`

To disable the default JavaScript, add the following line to your theme's functions.php file:

`add_theme_support( 'qa_script' );`

When you feel the Q&A section is ready for prime time, if your theme supports [custom menus](http://en.support.wordpress.com/menus/), you could add direct links to http://yoursite.com/questions/ and even to http://yoursite.com/questions/ask/ to your main menu.

== Changelog ==

= 1.3.0 =
* Added full width Q&A pages selection option
* Added Users with Highest Reputation widget
* Questions Per Page is now adjustable
* Question status count included in admin Right Now Dashboard box
* Changed optional question save status from draft to pending now
* Features synchronized with Q&A Lite

= 1.2.0.1 =
* Fixed a minor display bug (forgotten closing italics tag)

= 1.2.0 =
* Added possibility to save questions in draft mode for all user levels
* Anon questions can be assigned to an existing author
* Added possibility to remove commenting in Buddypress activity stream
* Css settings can be set using admin panel now
* Added wp_editor filter, post filter and several other filter hooks to control how questions are saved and messages are sent 
* Disabling of WP editor is now possible
* Answers per page can be set from admin side
* Admin UI improved
* More explanatory reply in case of a duplicate question submission
* Added proper uninstall functionality
* Default capabilities are now better set during installation
* Fixed unauthorized users accessing question pages. They are now redirected to a selectable page instead
* Fixed css issue for accepted answer icon
* Fixed notices displayed in WP Debug mode
* activity-action class is included in css file to prevent links become invisible

= 1.1.9 =
* Fixed: Up, Down buttons in BuddyPress default theme
* Fixed: Unable to change the Question Category once the Question has been added

= 1.1.8 =
* Fixed: settings item

= 1.1.7 =
* Fixed: new wordpress user after Q&amp;A anon answer No New User Emails

= 1.1.6 =
* New configurable options QA_FLOOD_SECONDS, QA_SLUG_ROOT, QA_SLUG_ASK,
  QA_SLUG_EDIT, QA_SLUG_UNANSWERED, QA_SLUG_TAGS, QA_SLUG_CATEGORIES, QA_SLUG_USER,
  QA_ANSWER_ACCEPTED, QA_ANSWER_ACCEPTING, QA_ANSWER_UP_VOTE, QA_QUESTION_UP_VOTE,
  QA_DOWN_VOTE, QA_DOWN_VOTE_PENALTY, QA_ANSWERS_PER_PAGE, QA_DEFAULT_TEMPLATE_DIR .
  Define them in wp-config.php to override default options.
* Filter questions by tag or category in Questions Widget

= 1.1.5 =
* Fixed: Q&A - not HTTPS / SSL compatible
* Fixed: Only Super Admin sees the settings menu in Q&A plugin
* Fixed: Moderation plugin works with Questions but not Answers in Q&A plugin
* Fixed: Q&A manage email subscription

= 1.1.4 =
* Fixed: Nothing shows up in my settings tab

= 1.1.3 =
* Fixed: Settings menu slug

= 1.1.2 =
* Thesis compatibility

= 1.1.1 =
* Fixed: Question author email notification no happening
* Now works with WPML

= 1.1.0 =
* Possible fix for Call to undefined function get_current_site() 

= 1.0.9 =
* Possible fix for missing domain name in e-mail notification
* Fixed: Q&A user can access Questions tab from WP dashboard

= 1.0.8 =
* Fixed: QA Answers not shown to visitors even though they are given the capability

= 1.0.7 =
* Better theme compatibility for Twenty Eleven, Twety Ten, BP Default and Products themes
* Make your own theme style mod and copy to qa/theme-mods/css/custom-<theme_directory>.css
* More styling improvements
* Added visitor role to control what users not logged in can use in QA

= 1.0.6 =
* QA post terms not saved if the user is not logged in

= 1.0.5 =
* Allow susbscribers view answers and questions by default
* New filters qa_before_menu, qa_first_menu_item, qa_last_menu_item, qa_after_menu, is_qa_page, qa_get_url, qa_is_question_answered, qa_time, qa_user_link, qa_get_question_link, qa_question_score, qa_question_status
* New actions qa_before_content, qa_before_question_loop, qa_before_question, qa_before_question_stats, qa_after_question_stats, qa_before_question_summary, qa_after_question_summary, qa_after_question,
  qa_after_question_loop, qa_after_content, qa_before_question_meta, qa_after_question_meta, qa_before_edit_answer, qa_after_edit_answer

= 1.0.4 =
* Fixed: WYSIWYG editor doesn't wrap
* Fixed: Votes Array Error

= 1.0.3 =
* Fixed: BP Default theme issues
* Fixed: BP Default child theme issues
* Fixed: BP 1.5 compatibility
* Fixed: Tag search when not logged in
* No more slow down message for Administrators (users with manage_options capability)
* Fixed: Rating by subscribers
* Fixed: Missing question title in answer notification
* Fixed: Notified as new question when it's infact a new answer
* Fixed: 404 error in ask page
* Auto generate answer title to help backend answer management
* Fixed: QA capabilities not taking affect
* BuddyPress Activity stream integration
* Switch frontend WYSIWYG editor to TinyMCE and Quicktags
* WordPress 3.3 compatibility

= 1.0.2 =
* BuddyPress integration
* prevent extra large font on single question page
* don't penalize users for downvoting questions, only answers
* New question e-mail notification

= 1.0.1 =
* show message when non-logged-in user tries to vote
* fix reputation points bug
* load archive-question.php template even when there are no unanswered questions

= 1.0 =
* ajaxified voting and answer accepting
* allow users to accept their own answers (without gaining reputation)
* fixed compatibility with Theme My Login plugin
* more descriptive error messages
* sturdier CSS

= 1.0-beta2 =
* changed default CSS
* added widgets: question list, question tags, question categories
* added sidebar to default templates
* added <body> class to qa templates
* fixed issue with WP-Polls plugin
* fixed issue with form not working in IE
* other minor bugfixes

= 1.0-beta1 =
* initial release

67022-1337220918

67022-1341303680-au

18246-1344386710-ai