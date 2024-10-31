=== MVIS Security Center ===
Contributors: secconsult
Tags: security, permissions, mvis, mvis security center, mvis security, protection, securing, locking, https, ssl, encryption, weak password, hacking, passwords, password
Requires at least: 3.3
Tested up to: 3.5.1
Stable tag: 1.3.5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

MVIS Security Center shows you exactly how to lock down your setup and sends subscribed users real-time vulnerability alerts for their site.

== Description ==

= Important Notice =

MVIS Security Center has been updated to identify weak user accounts that are exploited globally by distributed brute force attacks. Install it and remediate any problems with user accounts immediately.  

= Security has never been this simple! =
MVIS Security Center is a proactive WordPress security plugin that helps you lock down your installation in three simple and clear steps.  

1. Update Check: Find out what components of WordPress are vulnerable or need updating.
1. User Check: Find out which of your user accounts have problems that pose risks to your website.
1. Core Check: Find out which files and settings put your website at risk.

*You'll have all the information you need to protect your website from hackers.* 

= Protect yourself now.  Stay protected in the future. =
Everyday new vulnerabilities are found, and hackers are ready to use them against your websites. 

We at <a href="https://www.sec-consult.com/en" target="_blank">SEC Consult</a> have a dedicated team in multiple timezones that tracks all vulnerabilities and makes them available to you in real-time. 
A subscription comes with the following unique benefits:

1. You'll receive an e-mail alert as soon as vulnerabilities are identified that affect any of your sites.
1. The vulnerability alerts will tell you exactly how to address the vulnerability and become safe again.
1. You'll receive weekly status mails informing you about outdated versions and vulnerabilities in your sites.

*Hackers will never stop attacking. Don't become a victim!* 

== Installation ==
1. Either install it directly through the WordPress admin dashboard or
1. Download the `mvis-security-center.zip` and extract its contents
1. Upload the `mvis-security-center` directory to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently asked questions ==
= What is MVIS Security Center = 

MVIS Security Center is a WordPress plugin that identifies security problems in your website and helps you lock down your WordPress installation in three simple steps. The plugin covers most of the hardening tips of the WordPress Security Codex and includes a lot of additional security checks. It was designed to clearly show at a single glance what security problems exist in your website and to provide you with all the information needed to understand these issues and eliminate them.

The free MVIS Security Center plugin will also show you vulnerabilities that have been made public 30 days ago or longer.  At a small cost, you can subscribe to our MVIS PROtection, which gives you immediate access to the most up-to-date vulnerability information.

= What is MVIS =
MVIS stands for Managed Vulnerability Information Service and is an enterprise grade service provided for our customers around the world. 
Our security experts gather all security vulnerabilities that are disclosed publicly (more than 7000 each year!), pre-filter them to eliminate false positives and thoroughly analyse them for validity, criticality, impact and other relevant criteria. This information is stored in our central database and allows us to give you detailed information about security vulnerabilities in a given software version.

Through MVIS Security Center you can subscribe to MVIS PROtection, which makes this high quality vulnerability information service available to everybody for a small annual subscription fee.

= What is MVIS PROtection = 
MVIS PROtection is a subscription to SEC Consult's Managed Vulnerability Information Service that was specifically created for WordPress. The MVIS Security Center plugin tracks exactly which WordPress version or which of the thousands plugins and themes are installed on your website. As soon as vulnerabilities in any of these components that directly threaten your website are disclosed online, you will receive an e-mail alert telling you the specifics of the threat and the information needed to eliminate the security issues immediately. So you can react well before attackers get a chance to exploit these flaws in your website.

Yes, that is very cool :)

= How much does MVIS PROtection cost =
The current prices can be viewed from within the plugin.

= Does this plugin support Multisite or Windows WordPress installations = 
Limited tests have been conducted for WordPress installations on Windows and for Multisite installations. If you have a WordPress set up on Windows or a Multisite, it would be great if you can give some feedback.

== Screenshots ==

1. The start page of the plugin gives you an overview of all tests within each of the three steps. Hovering over a dot will give you a brief description of the gravity of a given issue.
2. The update check step shows you which plugins are either outdated (orange dot) or contain a known security vulnerability (red dot) that hackers can abuse to attack your site. For non-subscribed users the vulnerability information is 30 days outdated.
3. Clicking on the double arrow symbol of any issue shows detailed information about it. In this case details of a vulnerability in an installed plugin are displayed and the option to update the plugin directly with one click is given. This functionality is only available to subscribed users.   
4. The user check step displays information about user accounts that might pose a threat to your website. For example weak passwords or common usernames with high privileges will be flagged here.
5. Detailed information on how to solve a problem with a specific user account is given.
6. The core check step shows which files and settings might put your website at risk and are configured insecurely.
7. One example of a violation of security best practices that should be resolved.
8. Subscribed users conveniently receive weekly status e-mails for all their active sites summarizing available updates and known vulnerabilties.
9. A detailed report about the specific vulnerability that affects a site is attached in the real-time email alert that for subscribed users. 

== Changelog ==
= 1.3.5 = 
* Resolves a CSS naming conflict with the plugin User Access Manager.
* Adds more details to the file permission checks.
* Updates readme.txt to show the new beautiful weekly html status e-mails.
= 1.3.4 =
* Adds a check for wp-config.php backup files as requested by Christian M. 
= 1.3.3 =
* Fixes a bug that prevented users from being able to click on links in the built-in plugin browser. Bug reported by terminij. 
= 1.3.2 =
* The plugin is now only accessible to super admins in multisite setups and to admins in normal setups.
* The plugin has been adapted to detect insecure credentials that are currently exploited by global bruteforce attacks against WP. 
= 1.3.1 =
* Bugfix in the coupon subscription functionality and in the displaying of one security check
= 1.3 =
* Updated the backdoor script names
* Now allows full subscription for all sites
* Official launch
= 1.2 =
* Adds information about available updates to the weekly status e-mails for all registered sites.
* Fixes a bug that now allows directly one-click upgrading themes as well.
* Changed the registration button to reflect the extended free subscription phase.
= 1.1 =
* Adds a feature that allows users to enable/disable receiving weekly status e-mails for all registered sites.  
* Fixes a bug with installations that have differently named wp-content directories reported by Ian Dunn.
* Fixes two bugs pass by reference bugs with newer PHP versions reported by Ian Dunn.
* Fixes two bugs in the check functionality reported by damian5000.
* Fixes a bug with setting cookies in Safari.
* Improves usability aspects of the user interface.
= 1.0 =
* Initial release starting the BETA phase
