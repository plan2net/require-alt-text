# Require alternative text for images in TYPO3 CMS

[![CI](https://github.com/plan2net/require-alt-text/actions/workflows/ci.yml/badge.svg)](https://github.com/plan2net/require-alt-text/actions/workflows/ci.yml)

Editors cannot place an image without an alternative text. The extension works on every image reference
(`sys_file_reference`) in the backend and needs no configuration.

## How it works

TYPO3 lets an image reference use the alternative text of its file (set in the file list) or a text of its own.

- **The file has an alternative text:** the reference shows it as a placeholder and uses it. Nothing is required. An
  editor may still set a text of its own for this place, when the image means something else here.
- **The file has none:** the field on the reference becomes required, and the editor has to write a text.

When saving, an empty text is refused with a message. This check cannot stop the whole save: the record is saved
without the text. Code that creates references, such as an import, has to set the text itself.

> [!TIP]
> Set the alternative text on the file first, then place the image. A text written on the reference while the file
> had none stays there as an override, even after the file gets a text of its own, and the image then has two
> different texts in different places.

## Compatibility

| TYPO3 | PHP       |
|-------|-----------|
| 12.4  | 8.1 – 8.5 |
| 13.4  | 8.2 – 8.5 |
| 14.3  | 8.2 – 8.5 |

## Installation

```bash
composer require plan2net/require-alt-text
```

## License

GPL-2.0-or-later
