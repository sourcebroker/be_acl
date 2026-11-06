# INFORMATION:

Based on the TYPO3 13 version of be_acl extension: https://github.com/p2media/typo3-ext--be-acl.

| TYPO3 | be_acl |
|-------|--------|
| 14.3  | 5.x    |
| 13.4  | 4.x    |
| 12.4  | 3.x    |


## Installation

In your `composer.json` add in section `repositories` following:

```json
{
    "type": "vcs",
    "url": "https://github.com/sourcebroker/be_acl.git"
}
```

Then run `composer req sourcebroker/be-acl:^5.0` (TYPO3 14), `^4.0` (TYPO3 13) or `^3.0` (TYPO3 12).
