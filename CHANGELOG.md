# Changelog

All notable changes to this project are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.1.0] - 2026-10-30

TYPO3 14 support, and the field for the alternative text is required in the form again where the file has no text
of its own.

### Added

- TYPO3 14.3, and PHP 8.4 and 8.5.

### Fixed

- The form marks the field as required again where the file has no alternative text. Since TYPO3 12 only saving
  refused an empty text.
- The extension decides only once TYPO3 has filled in the file's text, instead of relying on running last.
- TYPO3 14 shows the extension's description of the field, as it reads label overrides from a new place.
- TYPO3 14.3 no longer reports `ext_emconf.php` as deprecated: `composer.json` declares the version.
- The package no longer ships the tests and the build files.
- The German error message has its English source.

### Compatibility

- TYPO3 12.4, 13.4 and 14.3, on PHP 8.1 to 8.5.

## [1.0.0] - 2024-11-19

The first release: every image reference needs an alternative text, unless its file has one.

[Unreleased]: https://github.com/plan2net/require-alt-text/compare/v1.1.0...HEAD
[1.1.0]: https://github.com/plan2net/require-alt-text/compare/1.0.0...v1.1.0
[1.0.0]: https://github.com/plan2net/require-alt-text/releases/tag/1.0.0
