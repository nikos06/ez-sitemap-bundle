Blend Ez Sitemap Bundle
=======================

This bundle generates a dynamic sitemap based on your ezpublish stored content.  It has two options, a route which generates content when a user visit, or an ezpublish/console command to manually generate the sitemap.xml (e.g. with a cron job).  If you are using a front cache like varnish, the first option should work well.  Otherwise, the scheduled task may be a better option.

This bundle does *not* support advanced options like custom routes or multiple sites.  It should be considered unstable and alpha-quality, and configuration options may change and break things.

## Installation
use composer to require the latest package from packagist.  Full release versions, e.g. from "0.0.1" to "1.0.0" will almost certainly break your configuration and your site.

## Configuration
For 1.x releases, the following configuration options are used in your ezpublish.yml or config.yml

```yml
blend_ez_sitemap:
  allowed_sections:
    - 1
    - public
  allowed_content_types:
    - text_page
    - folder
  main_url: http://example.org
```

allowed_sections is a list of either ezpublish section Ids or section names.  To not limit by section, use a an empty array, e.g. `[]`
allowed_content_types is a list of content_type_identifiers.  Set to `[]` to not limit content types.
main_url is the protocol + domain for the base domain for the sitemap.

If you encounter an error about user not having access to 'view' 'section', your user (probably anonymous) does not have permission to get a list of sessions.  You can either grant this permission, or use the section IDs instead of names in the allowed_sections.

## Running the command
To run the command from your console:
```bash
php ezpublish/console blend:ez-sitemap:generate
```

This will add the sitemap file to the default location, 'web/sitemap.xml'

