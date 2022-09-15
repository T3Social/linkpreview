Changelog
=========

1.16.1 - (April 16, 2022)
--------------------------
Minimum PHP Version is now 7.4!

- Enh: Better handle too old PHP Versions


1.16.0 - (April 16, 2022)
--------------------------
Minimum PHP Version is now 7.4!

- Enh: Switched OpenGraph Library to `fusonic/opengraph`

1.15.6 - (October 21, 2021)
--------------------------
- Fix #18: Fix encoding

1.15.5 - (August 23, 2021)
--------------------------
- Fix: Added missing composer dependencies


1.15.4 - (August 23, 2021)
--------------------------
- Fix #3: Fix link preview of forbidden sites without provided user agent
- Fix #16: Fix incorrect link preview for non-UTF-8 sites


1.15.3 - (March 24, 2021)
-------------------------
- Fix: Exclude exception for URLs without content


1.15.2 - (March 24, 2021)
-------------------------
- Fix #10: Fix undefined errors


1.15.1 - (March 23, 2021)
-------------------------
- Enh #6: Display validation errors on fetching new url
- Fix #9: Fix parsing content from different languages
- Enh #5: Migrate to Yii2 HTTP Client
- Fix #3: Fix title overlaps close button


1.15.0 - (February 05, 2021)
----------------------------
- Fix #8: 1.8 compatibility
- Chg: Update HumHub min version to 1.8


1.14.0 - (December 07, 2020)
-----------------------
- Fix #7: Only fetch link info for http/https urls


1.13.0 - (November 10, 2020)
-----------------------
- Fix #4: Favicon is replaced by link preview on firefox
- Chng: Fetch open graph data by https://github.com/euskadi31/Opengraph

1.12.0 - (April 09, 2020)
-----------------------
- Fix: Only render image if image url is available

1.11.0 - (April 06, 2020)
-----------------------
- Fix: Duplicated form field ids
- Fix: Richtext button overlaps linkpreview text
- Fix: Yii Html component usage for images and links
- Chg: Added 1.5 defer compatibility
- Enh: Improved event handler exception handling

1.10.0 - 20 August 2018
-----------------------
- Fix: Restrict linkpreview to post and comments.

1.9 - 17 January 2018
-----------------------
- v1.3 Prosemirror richtext compatibility

1.8.4 - 17 January 2018
-----------------------
- Fix: Breaks console cron jobs on rare scenarios


1.8.3 - 10 November 2017
------------------------
- Fix: Linkpreview image max-width issue.


1.8.2
-----
- Fix: Linkpreview in comment bar not cleared.


1.8.1
-----
- Fix: Word break for linkpreview link overlapping stream entry.


1.8.0
-----
- Fix: Fixed guest mode linkpreview error
- Fix: Fixed paste event (core version 1.2.1)


