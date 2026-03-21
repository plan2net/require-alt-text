# Require Alternative Text for Images in TYPO3 CMS

[![Tests](https://github.com/plan2net/require-alt-text/actions/workflows/tests.yml/badge.svg)](https://github.com/plan2net/require-alt-text/actions/workflows/tests.yml)

An accessibility extension that enforces alternative text on all image references
in the TYPO3 backend. Prevents editors from saving images without providing
descriptive alt text.

## How It Works

The extension uses two mechanisms to enforce alt text:

1. **FormDataProvider** — When the backend form loads, the `alternative` field on
   `sys_file_reference` records is marked as required. The "Set value" checkbox
   and null defaults are removed, making the field mandatory in the UI.

2. **Evaluation class** — When saving a record, the alternative text is validated.
   If it is empty, a flash message error is displayed and the empty value is
   rejected.

> [!NOTE]
> The evaluation class prevents the empty value from being written to the
> `alternative` field, but it does not prevent the record itself from being
> saved — TYPO3's eval mechanism has no way to abort an entire DataHandler
> operation. The record is created with a `NULL` alternative text instead.
> The primary enforcement layer is the FormDataProvider (1), which makes the
> field required in the backend form so editors cannot submit without filling
> it in. Code that creates `sys_file_reference` records programmatically
> (e.g. import scripts) must ensure alternative text is provided.

If a **placeholder** is configured for the alternative text field, the requirement
is relaxed — the placeholder value will be used as fallback.

## Compatibility

| TYPO3   | PHP         |
|---------|-------------|
| 12.4    | 8.2 – 8.3   |
| 13.4    | 8.2 – 8.4   |
| 14.x    | 8.2 – 8.4   |

## Installation

```bash
composer require plan2net/require-alt-text
```

No configuration required. The extension works automatically once installed.

## Development

```bash
# Install dependencies
composer install

# Run static analysis
composer phpstan

# Check code style
composer cs:check

# Fix code style
composer cs:fix

# Run unit tests
composer test:unit

# Run functional tests
composer test:functional
```

## License

GPL-2.0-or-later
