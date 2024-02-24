# simple-broken-link-finder

So simple broken link finder.

Can't recognize is in comment or not.
Can't resolve dynamic url.
Ignore external link.
Ignore url param.

But fast.


### How To Use

```php
require_once(__DIR__.'/vendor/autoload.php');

$broken_links=Catpow\SimpleBrokenLinkFinder\SimpleBrokenLinkFinder::search(__DIR__.'/public_html');

foreach($broken_links as $broken_link_uri=>$pages){
  printf("%s was missing in %s\n",$broken_link_uri,implode(' and ',$pages));
}
```