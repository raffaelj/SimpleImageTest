# SimpleImage and libgd tests

[SimpleImage](https://github.com/claviska/SimpleImage)

The reason for this test: https://github.com/claviska/SimpleImage/issues/236

and https://github.com/claviska/SimpleImage/issues?utf8=%E2%9C%93&q=is%3Aissue+transparent+

## how to use

copy this repository on a host, call a url to the folder and send a screenshot as issue or pull request

Have a look at the different results (screenshots)... weird...

## notes

* libgd and bundled gd in PHP builds are different
* transparent gif and png have multiple issues
* check if alpha channel exists seems to be possible, but with too much processing, see: [Stackoverflow](https://stackoverflow.com/questions/5495275/how-to-check-if-an-image-has-transparency-using-gd)
* sometimes gif has a black border (rotate)
* sometimes gif and png lose transparency and turn black
* ...

It looks like the issue could be here with a fix for the next gd release (2.2.6): https://github.com/libgd/libgd/issues/432#issuecomment-362257978

> Interesting, PHP's bundled libgd doesn't seem to be affected by this issue, because the implementation was "slightly" different from the start: libgd vs. PHP.
