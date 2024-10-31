=== NEW! Wordpress Status Monitor ===
Contributors: netping
Tags: status, monitor, uptime, check, expire
Stable tag: 1.2.0
Requires at least: 4.3.0
Tested up to: 5.8.1
Requires PHP: 5.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Status Monitor Plugin for Wordpress. Check your site's availability & receive alarms.

== Description ==

Wordpress Status Monitor is the easy-install admin plugin to check your website’s Status, Resources & Uptime.

= FEATURES =

* **Status Monitor**: Test if your site is up and running and its responsetime.
* **Domain Name**: Check if your domain name is about to expire
* **SSL certificate**: Check the SSL Expiry date of your certificate + if its setup correctly
* **Disk Storage**: Get notified if your web server’s free storage drops below X percent
* **RAM**: Monitor how much memory the server has available
* **CPU**: Inspect what the CPU usage is during the day
* **SLA Check**: Measure your Uptime Percentage (I.e. 99.93%) and compare to whats guaranteed in your hosting SLA (Service Level Agreement).
* **Alarms**: Notify the admin if your site status changes and minimize downtime.
* **Custom Triggers**: Define custom Status or Downtime triggers (e.g. “Alert admin if CPU > 90% for longer than 10 minutes”)
* **Charts**: Get insights & analyze your collected server statistics & measurements on zoomable charts

= PREMIUM PAID FEATURES =

Wordpress Status Monitor is a fully functional plugin as free. But there are some optional premium paid features.

* **Support**: Let us set up & configure the Status Monitoring for you
* **SMS-alerts**: Notify your admin via SMS instead of Email if your site's status changes
* **Faster Check Interval**: Get notified about downtime faster! Check your sites status every minute (instead of every 3rd)
* **Longer history**: Save your collected Status Monitoring data for 180 days instead of 7 days.

= REPORT BUGS =

Please don't hesitate to contact us regarding any problems with our plugin, using our [Support Page](https://wordpress.org/support/plugin/netping-site-monitor), and we will answer your questions as soon as possible.

= CONTRIBUTING =

Do you have any feature requests or ideas how to further develop the status monitoring plugin, please let us know using the [Support Page](https://wordpress.org/support/plugin/netping-site-monitor).

= TERMS =

The Wordpress Status Monitor plugin is relying on the (free) third party service [Netping](https://netping.com) for the status check of your site. You can read netping's terms of service on the following link: [terms](https://netping.com/terms)

= API =

To find out more about the netping API have a look at the [API docs](https://netping.com/apidoc).

keywords: uptime, crash alarm, outage alarm, outage monitoring, response time, analytics, tools, instrumentation, memory measurement, memory statistics, memory metrics, memory full, disk measurement, disk statistics, disk metrics, disk full, admin, admin tool, cpu measurement, cpu statistics, cpu metrics, cpu 100%, "response time"

== Screenshots ==
1. Uptime Diagrams
2. Websites to monitor status of
3. Alert Status Triggers
4. Alert Recipients

== Installation ==

= Installation from within WordPress =

1. Visit **Plugins > Add New**.
2. Search for **Wordpress Status Monitor**.
3. Install and activate the Wordpress Status Monitor plugin.

= Manual installation =

1. Upload the entire `netping-site-monitor` folder to the `/wp-content/plugins/` directory.
2. Visit **Plugins**.
3. Activate the Wordpress Status Monitor plugin.

== Changelog ==
= 1.2.0 =
* Bug fix release

= 1.1.7 =
* More flexible Status Check

= 1.1.6 =
* Bug fix release

= 1.1.5 =
* Better Status monitoring
* Optimized Analytics Page

= 1.1.4 =
* Fixed small init bug

= 1.1.3 =
* Status Monitor changes

= 1.1.2 =
* FAQ Changes
* Added Uptime Docs

= 1.1.1 =
* Small status monitoring fix
* Faster wp-cli support
* Domain Monitoring changes

= 1.1.0 =
* Faster installation
* Monitor wp-cli support

= 1.0.6 =
* Small status bugfix

= 1.0.5 =
* Added admin installation notice
* Small Status Monitoring typo fixed.

= 1.0.4 =
* Better Status Monitor functionality
* More consistent SSL monitor & Uptime Stats

= 1.0.3 =
* Admin metrics now update faster!
* Added an FAQ to the readme.txt file
* New SSL monitoring feature

= 1.0.2 =
* New WP admin button design
* Better menu options
* Status Check data processes faster

= 1.0.1 =
* Graphical WP admin percentage bars
* Small bugfix regarding certificate monitoring
* All your Status Checks now shows up in the same list.

= 1.0.0 =
* Wordpress Status Monitor: Initial Release
* Basic Downtime monitoring

== Frequently Asked Questions ==

**Is the Wordpress Status Monitor Plugin Free?**

Yes. Usage of the Status Monitoring plugin to check your web site uptime is (and will always be) free. There are however some features, like SMS alerts, that are premium paid. Please consider supporting netping by upgrading to a paid account.

**Where do I get support for the Status Monitor Plugin?**

Please use our [Support Page](https://wordpress.org/support/plugin/netping-site-monitor) to ask questions about our Status Monitor Plugin, and we will answer you as we can.

**Can I monitor my SSL certificate with this plugin?**

Absolutely! You can add and monitor up to 3 different sites using the netping dashboard. We will monitor these sites SSL certificates for you, as long as you create an alert trigger for your certificate expiry.

**Can I check my domain name with this plugin? I.e. Create a domain Monitor**

Yes! We will monitor your domains Expiry Date and alert you with a warning. As long as an alert trigger exists.

**When will my domain name expire?**

Please check the section “Domain Expiry” on the plugin start page.

**How often is my site checked?**

Your site’s Status & resource monitoring is going the be checked every third minute, or every minute if you use a paid account.

**How do I get status alerts via SMS?**

SMS messaging is a premium paid service. Please upgrade your account following [this](https://netping.com/dashboard/settings/subscription/usd). link.

**How many SMS messages are included per month?**

In the Essential Uptime Monitoring plan, 50 SMS messages are included per month. To read more about what is included in the netping plans, please visit the [Features & Pricing](https://netping.com/pricing) page.

**Netping says my site is down, but it's not. Why?**

Your Status Check might have sent you a down warning because of a very temporary network glitch somewhere between Netping's status check server and your server. To filter out temporary network glitches and false positives, please select a minimum duration of at least a few minutes in your Check Triggers.

**How do you calculate CPU usage?**

CPU usage and other performance metrics is based on all CPU cores on your web server machine. In other words, if half of all CPU cores on your web server is running on 100%, your CPU Usage will be 50%.

**I did not receive my alert Email, why?**

If you don’t receive the Status Check email we sent you, please check your spam folder. If it’s not there, please contact netping support.

**Can I change the Status Check location?**

Yes. Just Click “Netping Dashboard > Checks > Edit” to change the Uptime Monitoring location.

**Can I create custom Alert Triggers?**

Click “Netping Dashboard > Triggers” to add or edit any Status Alert triggers.

**Can you please add feature X to the plugin?**

We would love to hear about any missing Status Monitor or Uptime Check features. Please let us know using our [Support Page](https://wordpress.org/support/plugin/netping-site-monitor).
