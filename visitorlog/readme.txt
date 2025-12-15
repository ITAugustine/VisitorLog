=== VisitorLog ===

Contributors:      itaugustine
Tags:              security, ddos, admin protection, database protection, spam 
Author:            IT-Augustine
Author URI:        https://profiles.wordpress.org/itaugustine/
Requires PHP:      7.4
Requires at least: 5.7
Tested up to:      6.9
Stable tag:        1.0.5
License:           GPLv2 or later
License URI:       https://www.gnu.org/licenses/gpl-3.0.html

The VisitorLog plugin adds useful functionality to your site in the field of security, statistics and some other useful functions.
 
== Description ==

The plugin implements four main directions:
1. Site security.
2. Statistics of visits.
3. Tabular and graphical representation of the necessary information for the site administrator.
4. Necessary and useful functions in the work of the administrator.

= 1. Site Security =
  • Registration of DDoS attacks;
  • Protection from 404 events;
  • Protection of the administrative panel;
  • Protection of the database;
  • File system protection;
  • Spam protection in comments;
  • Spam protection in feedback forms;
  • Bot recognition;
  • Blocking bots with control instructions in the file .htaccess;
  • Blocking bots by redirection;
  • Creation of a database of unwanted IP addresses (blacklist);
  • Firewall.

= 2. Statistics of site visits =
  • generating statistics of site visits by categories of visitors: visitors, administrators, bots;
  • maintaining statistics of site visits by the hour;
  • display statistics on the screen in the form of tables and graphs;
  • statistics on IP addresses are stored in the database;

= 3. Reports, Tables, Logs =
  3.1 Reports
    • Database backup report;
    • system information (server, WordPress, PHP, MySQL, plugins, ...);
    • file and folder access report;
    • Plugin update report.
  3.2 Tables
    • blacklist of unwanted IP addresses;
    • address table when login to the admin panel fails;
    • spam address table;
    • address table at event 404;
    • a table of unwanted IP addresses that the administrator creates independently;
    • IP addresses can be entered into tables automatically or the administrator can enter them manually.
    • IP addresses in the tables are blocked automatically or the administrator does it manually.
  3.3 Logs
    • visitor log (all site visitors, admins, bots are registered);
    • Log with administrators' IP addresses (all dynamic and static addresses are stored);
    • account activity log, duration of sessions in the system;
    • Action Log;
    • Error log.

= 4. Other useful functions =
  • site maintenance mode, blocking access to the site except for administrators;
  • deleting all comments from the database;
  • Cron, a log of scheduled tasks.

= Documentation =
  You can find a more detailed description in the documentation for the plugin or on our website.
  Dev IT-Augustine

== External services ==

= https://www.google.com/recaptcha/api.js
  /* PLEASE DO NOT COPY AND PASTE THIS CODE. */ Google asks you not to insert this code into your scripts. 
  The plugin uses this to protect against spam in comments, as well as to protect against intruders when logging into the Wordpress admin panel.
  This service is provided by Google. 
  Link to privacy policy - wp-admin/admin.php?page=visitorlog_privacy_policy

= https://www.google.com/recaptcha/api/siteverify
  Using this link, the plugin connects to the Google API to receive recaptcha. 
  The plugin uses this feature to protect against spam in comments, as well as to protect against intruders when logging into the admin panel of your Wordpress site.
  This service is provided by Google.
  Link to privacy policy - wp-admin/admin.php?page=visitorlog_privacy_policy

= smtp.google.com
  The plugin uses this link to the google SMTP mail resource to send messages from the site.
  Link to privacy policy - wp-admin/admin.php?page=visitorlog_privacy_policy

= http://127.0.0.1
  Using this link, the plugin redirects unwanted visitors, identified intruders who carry out attacks on the admin panel and pages of the Wordpress site.
  Link to privacy policy - wp-admin/admin.php?page=visitorlog_privacy_policy

= www.w3.org
  The plugin uses this link in the bootstrap.css and maintenance.css style libraries, which are used to style the Visitorlog plugin frontend.
  Link to privacy policy - wp-admin/admin.php?page=visitorlog_privacy_policy

= https://itaugustine.com
  This link connects the user to the developer's website to access the full version of the plugin with advanced features. 
  Security Line developers.
  Link to privacy policy - wp-admin/admin.php?page=visitorlog_privacy_policy

= https://www.gnu.org/licenses/gpl-2.0.txt
  The plugin allows the user to click on the link and read the text of the GNU GENERAL PUBLIC LICENSE.
  Link to privacy policy - wp-admin/admin.php?page=visitorlog_privacy_policy  

= https://wordpress.org/about/requirements
  https://codex.wordpress.org/Debugging_in_WordPress
  https://developer.wordpress.org/advanced-administration/server/file-permissions/
  https://developer.wordpress.org/advanced-administration/security/hardening/
  https://profiles.wordpress.org/itaugustine/
  The plugin allows the user to use Wordpress resources to obtain the necessary information.
  Provided by https://wordpress.org
  Link to privacy policy - wp-admin/admin.php?page=visitorlog_privacy_policy

== Frequently Asked Questions ==

= Is it possible to use the plugin on multi-sites? =
The plugin has not been developed or tested for multisites.

= The plugin is multifunctional, how does it affect the speed of the site? =
The plugin has a wide range of functions and in order to speed up work with data, we have distributed processing and analysis. 
Where possible, we have applied processing to Cron functions, as well as to administrator requests in the panel. 
The data analysis time is minimized as much as possible and does not have a big impact on the server response to requests from site visitors.

== Installation ==

1. You can install the VisitorLog plugin by following the usual installation procedure, or by uploading the plugin to the /wp-content/plugins directory/.
2. After installation, activate the plugin via the plugins menu in WordPress.
3. Set the plugin settings in the installation wizard by following the suggested steps sequentially. The wizard will install the database, tables and fill them with initial data.

== Screenshots ==

1. Report on the current day's visits
2. Overview - Security. Control panel, Log & blocking of visitors
3. Overview - Security. DataBase protection
4. Overview - Security. Firewall
5. Control panel
6. Security - Admin panel protection
7. Security - DataBase protection
8. Tables - Failed Logins
9. Logs - Visitor Log 
10. Guide

== Changelog ==

= 1.0.5 =
* Release Date - December 12, 2025 *
* Translating the plugin into Chinese

= 1.0.4 =
* Release Date - December 11, 2025 *
* Translating the plugin into Russian

= 1.0.3 =
* Release Date - December 10, 2025 *
* Translating the plugin into French

= 1.0.2 =
* Release Date - December 9, 2025 *
* Translating the plugin into German

= 1.0.1 =
* Release Date - December 3, 2025 *
* Bumped the "Tested up to" tag to WP 6.9

= 1.0.0 =
* Release Date - November 25th 2025 *
* Initial version *
