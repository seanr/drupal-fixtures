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

MENU FIXTURES
=============
Define a menu tree in a file in the folder you have choosen for fixtures (or the default on) and use the naming
convention:

menu--*.yml

Important: note that if you run menu fixture creation manually, you'll have to run them after nodes/taxonomies have
been created.  Menu items created with a path that doesn't exist yet are not saved. This is no issue if you are using
 the fixture-all command.

NODE FIXTURES
=============
Below is a description of the node fixtures convention:

node--*.yml


RUN FIXTURES
============
Use drush to run the fixtures.  The command is drush fixtures-all` and it currently imports users, nodes and menus.
You can also run `drush fixtures-type --type=user or node or menu` to install only one.

VALIDATE FIXTURES
=================
If you have created a bunch of fixtures and you want to be sure, that they are ok,
you can run `drush fixtures-validate-all` to validate all fixtures or `drush fixtures-validate-type --type=user or
menu or node`