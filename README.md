<p align="center">
  <a href="https://roots.io/bedrock/">
    <img alt="Bedrock" src="https://cdn.roots.io/app/uploads/logo-bedrock.svg" height="100">
  </a>
</p>

<h1 align="center"><strong>Real Estate WordPress Project</strong></h1>
<p align="center">Built with Bedrock + ACF + Custom Plugins + REST API + Ajax Filter</p>

---

## Project Description

This is a real estate listings website built on the Bedrock template. It includes a custom post type "Real Estate", with filtering and REST API support

---

## Technologies

- WordPress / Bedrock
- Composer
- Advanced Custom Fields (ACF)
- Bootstrap (understrap child theme)
- Custom Plugins (Real Estate CPT, Real Estate API)
- REST API
- XML (parsing)
- Ajax (filter/pagination)
- Custom WP CLI

---

## Structure

- `/web/app/themes/understrap-child` - Child customized theme
- `/web/app/plugins/real-estate-cpt` - Plugin (custom post type and taxonomy registration)
- `/web/app/plugins/real-estate-api` - Plugin (REST API, Ajax filter, shortcode, widget, XML import, Ordering)
- `web/app/mu-plugins/real-estate-seeder.php` - Seeder (fish data)

---

## Project Deployment

1. Clone repo and install dependencies:
```bash
composer install
```

2. Create `.env` file:
```env
DB_NAME=...
DB_USER=...
DB_PASSWORD=...
WP_ENV=development
WP_HOME=http://localhost
WP_SITEURL=${WP_HOME}/wp
```

3. Activate the plugins (need installed ACF Pro plugin)

---

## Seeding Real Estate Data

For generation demo realestate posts use custom WP-CLI command:

```bash
wp re-seeder generate
```

> This command will generate 10 realestate posts with randomized data and images

---

## REST API

See full API documentation here:  
[web/app/plugins/real-estate-api/README.md](web/app/plugins/real-estate-api/README.md)

