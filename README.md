# extender examples

This is a repository that contains examples (I share often) on how to use the root `extend.php`.

### Extenders

If you want to use the extenders from `app`, make sure to configure your `composer.json` by adding
`autoload` > `psr-4` > `"App\\": "app/"`, see the `composer.json` in this repository for details.

Also note that I added a `namespace` declaration in the `extend.php`, this changes how to resolve classes.
