Drupal Fixtures
======

This is a fork from Tom Verhaeghe's Fixtures project at http://git.drupal.org/sandbox/heartdriven/1722102.git

Fixtures is an idea derived from other frameworks - in this case Symfony and Ruby on Rails.  Fixtures are a YAML way of
writing default content for your website.  If you create your Drupal based website with Fixtures, you might need a set
of default testing data to populate your site once you've installed it.

Features enables you to create your website from a "clean-build path", instead of the classic "upgrade path" approach.
This way, continuous integration is possible.  This creates a little problem: every time you do a clean install of your
drupal website, you have to create content again and again.  Fixtures is a way to permanently create content in a text
file, and the user doesn't have to know a lot about drupal.

USER FIXTURES
=============
Imports users.

You can place files following user*.yml in `variable_get('fixture_load_path')`.
So if you wan to change the default DRUPAL_ROOT . '/../config/fixtures' you can just set the variable.

Example:

```yaml
my-user:
  name: my-login
  mail: my-email@example.com
  pass: my-password
  roles: "authenticated user"
```

MENU FIXTURES
=============
Define a menu tree in a file in the folder you have choosen for fixtures (or the default on) and use the naming
convention:

menu--*.yml

Important: note that if you run menu fixture creation manually, you'll have to run them after nodes/taxonomies have
been created.  Menu items created with a path that doesn't exist yet are not saved. This is no issue if you are using
 the fixture-all command.

Example:
```yaml
custom-menu:
  title: Custom menu
  description: A custom menu, used for main navigation
  items:
    item-home:
      title: Home
      link: home
    item-about-me:
      title: About me
      link: about-me
      items:
        item-general:
          title: General
          link: about-me
        item-resume:
          title: Resume
          link: resume
        item-projects:
          title: Projects
          link: projects
    item-blog:
      title: Blog
      link: blog
    item-contact-me:
      title: Contact me
      link: contact-me
```

NODE FIXTURES
=============
Below is a description of the node fixtures convention:

node--*.yml

Example:

```yaml
page-home:
  title: Home
  type: page
  body: |
        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam cursus diam nec eros aliquam eget auctor tortor pharetra. Fusce sagittis felis a nulla mattis vitae pellentesque lectus consectetur. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut id dui arcu, vitae feugiat purus. Etiam interdum fermentum purus, hendrerit sodales risus suscipit interdum. Donec facilisis condimentum molestie. Ut nec mi vitae est scelerisque dapibus. Aenean quis purus neque, non placerat lectus. Aliquam erat volutpat. Phasellus vehicula bibendum metus eget sodales. Sed porta velit eu massa condimentum et vestibulum dui posuere. Ut lacus est, tempor ut lobortis nec, laoreet semper est. Donec hendrerit nulla sit amet mauris sollicitudin euismod.</p>
                <p>Mauris non mauris id augue tincidunt elementum in vel orci. Curabitur varius enim id odio tempus eget interdum neque aliquet. Fusce eleifend, magna eu ultrices rhoncus, diam nunc rutrum libero, vel interdum erat risus congue turpis. Etiam sed porttitor arcu. Nulla in ipsum in tellus lobortis imperdiet. Nunc venenatis lacinia erat, nec consequat libero placerat nec. Fusce tincidunt varius mattis.</p>
                <p>Nullam tincidunt iaculis nisl, ac sagittis dui lobortis ut. Nunc sed adipiscing massa. Etiam facilisis, turpis id congue blandit, velit nulla aliquam justo, et euismod odio tellus vitae augue. Ut ultrices porttitor imperdiet. Curabitur et lorem et lacus pharetra placerat ut sit amet eros. In luctus mollis nunc in ultrices. Nullam tincidunt arcu id diam commodo eget lobortis felis porttitor. Fusce fringilla ultricies dolor sit amet imperdiet. Proin imperdiet hendrerit pellentesque. Mauris fermentum placerat mi, non laoreet nibh tristique quis. Donec elit enim, tincidunt non gravida sed, porta sit amet ante. Aenean nec magna eu leo ullamcorper elementum volutpat nec orci. Cras ac condimentum ipsum. Nunc ornare hendrerit tellus.</p>
  date: 2012-08-13 15:33:10
  path: home
page-about:
  title: About me
  type: page
  body: |
        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam cursus diam nec eros aliquam eget auctor tortor pharetra. Fusce sagittis felis a nulla mattis vitae pellentesque lectus consectetur. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut id dui arcu, vitae feugiat purus. Etiam interdum fermentum purus, hendrerit sodales risus suscipit interdum. Donec facilisis condimentum molestie. Ut nec mi vitae est scelerisque dapibus. Aenean quis purus neque, non placerat lectus. Aliquam erat volutpat. Phasellus vehicula bibendum metus eget sodales. Sed porta velit eu massa condimentum et vestibulum dui posuere. Ut lacus est, tempor ut lobortis nec, laoreet semper est. Donec hendrerit nulla sit amet mauris sollicitudin euismod.</p>
        <p>Mauris non mauris id augue tincidunt elementum in vel orci. Curabitur varius enim id odio tempus eget interdum neque aliquet. Fusce eleifend, magna eu ultrices rhoncus, diam nunc rutrum libero, vel interdum erat risus congue turpis. Etiam sed porttitor arcu. Nulla in ipsum in tellus lobortis imperdiet. Nunc venenatis lacinia erat, nec consequat libero placerat nec. Fusce tincidunt varius mattis.</p>
        <p>Nullam tincidunt iaculis nisl, ac sagittis dui lobortis ut. Nunc sed adipiscing massa. Etiam facilisis, turpis id congue blandit, velit nulla aliquam justo, et euismod odio tellus vitae augue. Ut ultrices porttitor imperdiet. Curabitur et lorem et lacus pharetra placerat ut sit amet eros. In luctus mollis nunc in ultrices. Nullam tincidunt arcu id diam commodo eget lobortis felis porttitor. Fusce fringilla ultricies dolor sit amet imperdiet. Proin imperdiet hendrerit pellentesque. Mauris fermentum placerat mi, non laoreet nibh tristique quis. Donec elit enim, tincidunt non gravida sed, porta sit amet ante. Aenean nec magna eu leo ullamcorper elementum volutpat nec orci. Cras ac condimentum ipsum. Nunc ornare hendrerit tellus.</p>
  date: 2012-08-13 15:30:10
  path: about-me
```

RUN FIXTURES
============
Use drush to run the fixtures.  The command is drush fixtures-all` and it currently imports users, nodes and menus.
You can also run `drush fixtures-type --type=user or node or menu` to install only one.

VALIDATE FIXTURES
=================
If you have created a bunch of fixtures and you want to be sure, that they are ok,
you can run `drush fixtures-validate-all` to validate all fixtures.

INSTALLATION
============
To install this module you can use composer. Just add this to your composer.json:

```json
"repositories": [
    {
        "type": "vcs",
        "url": "git@github.com:DECK36/drupal-fixtures.git"
    }
],
"require": {
    "deck36/drupal-fixtures": "dev-master",
    "composer/installers": "~1.0"
},
"extra": {
    "installer-paths": {
        "docroot/sites/all/modules/{$name}/": ["type:drupal-module"]
    }
},
```

After execution of `composer.phar install` you have to enable the module by execution of `drush en fixtures`.

DEPENDENCY
==========
When enabling the module, inject and classloader module is loaded.