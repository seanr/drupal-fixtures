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

article-1:
  title: Today a new star is born from fixtures
  language: de
  field_category: Stars
  field_channel: BRAVO
  field_tags:
    - Beauty
    - Stars
  type: article
  body:
    value: |
        Today something went wrong.  I still can't figure out exactly, but i'm still going to blog about
        it.  I <em>really</em> don't care if anyone reads this, but I want this off my chest.  I'm a really
        boring writer so I end up writing some rubbish in this YAML file.

        Have fun reading something completely useless :-)
    format: full_html
  date: 2012-08-05 22:48:51
  field_brv_3column_teaser_title: Katze ist Katze in Fixtures
  author: editor_fixtures
  path: blog/today-something-went-wrong
  field_image: ../config/fixtures/img/cat.jpg

```

You will need to implement a Specialized NodeBridge Class which can handle your node types.
It is necessary that the NAME is equal to the type of node you want to handle. Means: NAME = 'article' handles
node->type = article.

After that you need to define them as a service and tag them with: drupal.fixtures.drupal_node_bridge_specialized.

Example:

```php

    class ArticleNodeBridgeStub implements SpecializedBridgeInterface {
    /**
     * @const
     */
    const NAME = 'article';

    /**
     * {@inheritDoc}
     */
    public function process(\StdClass $fixNode) {
      $node = new \StdClass();
      $node->is_new = TRUE;
      $node->title = $fixNode->title;
      unset($fixNode->title);

      $node->language = $fixNode->language;
      unset($fixNode->language);

      $node->type = $fixNode->type;
      unset($fixNode->type);

      $node->created = strtotime($fixNode->date);
      unset($fixNode->date);
      $node->changed = time();

      // Shown on startpage or not
      // 1 = show
      $node->promote = property_exists(
        $fixNode,
        'promote'
      ) ? $fixNode->promote : 1;

      // Published or not published that is here the question
      // 1 = published
      $node->status = property_exists(
        $fixNode,
        'status'
      ) ? $fixNode->status : 1;

      $node->body = array(
        'und' => array(
          0 => array('value' => $fixNode->body)
        )
      );

      $wrappedNode = $this->wrapNode($this->prepareNode($node));
      unset($fixNode->body);

      $wrappedNode->save();
      return $wrappedNode->value();
    }

    /**
     * @throws SpecializedBridgeException
     * @return string
     */
    public function getName() {
      return self::NAME;
    }

    /**
     * @param \StdClass $node
     *
     * @throws SpecializedBridgeException
     */
    protected function prepareNode(\StdClass $node) {
      // return null in case of success
      if (null !== node_object_prepare($node)) {
        throw new SpecializedBridgeException('Node Object Preparation failed.');
      }

      return $node;
    }

    /**
     * @param \StdClass $node
     * @param string    $type
     *
     * @return \EntityDrupalWrapper
     */
    protected function wrapNode(\StdClass $node, $type = 'node') {
      return entity_metadata_wrapper($type, $node);
    }

  }

```


RUN FIXTURES
============
Use drush to run the fixtures.  The command is drush fixtures-all` and it currently imports users, nodes and menus.
You can also run `drush fixtures-type --type=user or node or menu` to install only one.

VALIDATE FIXTURES
=================
If you have created a bunch of fixtures and you want to be sure, that they are ok,
you can run `drush fixtures-validate-all` to validate all fixtures.

If you want to validate your different node type fixtures before using them you can implement the the 
SpecializedNodeValidatorInterface and ValidatorInterface or extend the BaseSpecializedNodeValidator class.

After that you need to define them as a service and tag them with: drupal.fixtures.drupal_node_validator_specialized.
 
Example:

```php

    use Drupal\Fixtures\Validators\Specialized\BaseSpecializedNodeValidator;
    
    class ArticleNodeValidatorStub extends BaseSpecializedNodeValidator {
      /**
       * @const string
       */
      const NAME = 'article';
    
      /**
       * @return array
       */
      protected function getKeyMap() {
        return array(
          'title' => 1,
          'type' => 1,
          'body' => 1,
          'date' => 1,
          'path' => 1,
          'language' => 1
        );
      }
    }

```

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
When enabling the module dic is loaded.