[![TYPO3 11](https://img.shields.io/badge/TYPO3-11-orange.svg)](https://get.typo3.org/version/11)
[![TYPO3 10](https://img.shields.io/badge/TYPO3-10-orange.svg)](https://get.typo3.org/version/10)
[![Donate](https://img.shields.io/badge/Donate-PayPal-green.svg)](https://www.paypal.me/GeorgRinger/20)

# TYPO3 Extension `news_tagsuggest`

This extension enriches the selection of tags in a news record.
Non existing tags can be created on the fly within the news record.
This feature has been part of EXT:news prior to version 10 and has been extracted into a separate extension.

![](Resources/Public/Screenshots/example.png)
## Installation

Install this extension just as any other TYPO3 extension:

- Use `composer req georgringer/news-tagsuggest`
- download it within the Extension Manager
- or download it from `https://extensions.typo3.org/extension/news_tagsuggest`

## Configuration

The following Page TsConfig can be used to define on which page the new tag is persisted:

```typo3_typoscript
# Tags will be saved on page with ID 123
tx_news.tagPid = 123
```
