# Word to HTML Converter (LibreOffice Headless)

[![Packagist](https://img.shields.io/packagist/v/ton-vendor/word-to-html.svg)](https://packagist.org/packages/ton-vendor/word-to-html)
[![License](https://img.shields.io/github/license/ton-vendor/word-to-html.svg)](LICENSE)

A simple PHP library to convert **Word documents (.doc, .docx)** into **HTML** using [LibreOffice](https://www.libreoffice.org/) in headless mode.  
Useful for extracting structured HTML content from Word files inside PHP applications.

---

## 🚀 Features
- Convert `.doc` and `.docx` to clean HTML.
- Uses LibreOffice `--headless` mode.
- Handles images automatically (exported to a separate folder).
- Returns HTML as string or file path.
- Framework-agnostic (works with Drupal, Symfony, Laravel, etc.).

---

## 📦 Installation

Require the package with Composer:

```bash
composer require ton-vendor/word-to-html
