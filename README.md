**Contributors:** ansarahmad97
**Tags:** RSS, news aggregator, content summary, WordPress tagline  
**Requires at least:** 5.0  
**Tested up to:** 6.x  
**Stable tag:** 1.0.0  
**License:** GPLv2 or later  
**License URI:** http://www.gnu.org/licenses/gpl-2.0.html  

## Description

This WordPress plugin automatically fetches and displays the latest headlines from RSS feeds, generating summaries for each article using the Diffbot API. Additionally, the plugin updates the WordPress tagline every 2 hours, showing different taglines on different pages.

### Key Features:
- Retrieves the latest 10 news headlines every 2 hours from the RSS Aggregator plugin.
- Summarizes news articles using the Diffbot API.
- Displays the summaries on a "Latest Headlines" page, with the last update time.
- Dynamically updates the WordPress tagline on different pages every 2 hours.

### How It Works:
1. **Cron Job 1:** Every 2 hours, the plugin fetches the latest 10 URLs from your RSS feed aggregator and sends them to the Diffbot API, which generates summaries of the news articles. These summaries are displayed on a "Latest Headlines" page, along with the time when the summaries were last generated.
   
2. **Cron Job 2:** The second cron job updates the WordPress tagline every 2 hours, displaying different taglines on different pages for a dynamic user experience.

### Installation

1. Upload the plugin folder to the `/wp-content/plugins/` directory or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. The two cron jobs will automatically start running every 2 hours to retrieve news summaries and update the tagline.

### Usage

- View the latest news summaries on the **"Latest Headlines"** page.
- See different taglines on different pages of your website, updated every 2 hours.

### Requirements

- A valid API key for Diffbot (for article summarization).
- RSS Aggregator plugin to fetch feeds from various sources.

### Frequently Asked Questions

**Q:** How often does the plugin update summaries and taglines?  
**A:** The plugin updates both summaries and taglines every 2 hours through cron jobs.

**Q:** Can I customize the number of headlines or taglines?  
**A:** Yes, developers can modify the code to adjust the number of headlines or change the frequency of tagline updates.

### Changelog

**1.0.0**  
* Initial release with automated news summarization from RSS feeds and dynamic WordPress tagline updates.

### License

This plugin is licensed under the GPLv2 or later. You can find the full text of the license [here](http://www.gnu.org/licenses/gpl-2.0.html).
