# Statamic Bard Link Suggestions
<a href="https://justbetter.nl" title="JustBetter">
    <img src="./art/header.png" alt="JustBetter logo">
</a>
A Statamic addon that adds **link suggestions** to the Bard editor.

While you type in Bard, this addon searches for possible links and shows a dropdown of suggestions. Suggestions are sourced from:

- **Statamic entries** (based on configurable fields per collection)
- **Sitemaps** (URLs indexed from one or more sitemap XML files)

### Requirements

- Statamic v6
- Laravel 12+
- PHP 8.3+
- Statamic Eloquent Driver (this package queries entries via Eloquent/Stache)

### Installation

Install the package via Composer:

```bash
composer require just-better/bard-link-suggestions
```

Run the migrations:

```bash
php artisan migrate
```

### Usage

Once installed, Bard will start requesting suggestions while you type.

- Suggestions appear after typing **3+ characters** of the current word (debounced).
- Clicking a suggestion turns the current word into a link.
- Only **super users** and users with the **marketeer** flag will see suggestions.

### Configuration (Control Panel)

Go to the Control Panel settings page:

- **JustBetter → Suggestions settings**

On this page you can configure:

- **Queryable fields per collection**: choose which entry fields should be searched.
- **Sitemap URLs**: add one or more sitemap XML URLs (per Statamic site).

### Sitemap indexing

Sitemap suggestions are backed by a database table that stores URLs extracted from your sitemap(s).

Index all configured sitemaps using:

```bash
php artisan statamic-bard-suggestions:index-sitemaps
```

Notes:

- Indexing runs via the **queue**. Make sure a queue worker is running.
- When you update sitemap settings, indexing/pruning jobs are dispatched automatically.

### Quality

To ensure the quality of this package, run:

```bash
composer quality
```

This will execute:

1. Tests
2. Static analysis
3. Code style checks

### Testing

```bash
composer test
```

## Credits

- [Bob Wezelman](https://github.com/BobWez98)
- [All Contributors](../../contributors)

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

<a href="https://justbetter.nl" title="JustBetter">
    <img src="./art/footer.svg" alt="JustBetter logo">
</a>
