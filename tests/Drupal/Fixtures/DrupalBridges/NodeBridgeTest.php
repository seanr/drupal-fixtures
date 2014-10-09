<?php
/**
 *
 * PHP Version 5.3
 *
 * @author    Mike Lohmann <mike.lohmann@bauermedia.com>
 * @copyright 2014 Bauer Digital KG
 */
namespace {
  $entityMetadataWrapperCalled = false;
  $nodePrepareCalled = false;
  $nodeGiven = array();

  /**
   * @var  PHPUnit_Framework_MockObject_MockObject
   */
  $entityDrupalWrapperMock = null;
}

namespace Drupal\Fixtures\DrupalBridges {

  use PHPUnit_Framework_MockObject_MockObject;

  function user_load_by_name() {
    $user = new \StdClass();
    $user->uid = 23;
    return $user;
  }

  class NodeBridgeTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var NodeBridgeInterface
     */
    private $subjectToTest;

    /**
     * {@inheritDoc}
     */
    public function setUp() {
      $this->subjectToTest = new NodeBridge();
      $this->subjectToTest->addSpecializedBridge(new ArticleNodeBridgeStub());
    }
    /**
     * {@inheritDoc}
     */
    public function tearDown() {
      global $entityMetadataWrapperCalled, $nodeGiven,
             $entityDrupalWrapperMock, $nodePrepareCalled;

      $entityMetadataWrapperCalled = false;
      $nodeGiven = array();
      $entityDrupalWrapperMock = null;
      $nodePrepareCalled = false;
    }

    /**
     * Tests the createFixtures method of the MenuBridge
     */
    public function testCreateFixtures() {
      global $entityMetadataWrapperCalled, $nodeGiven,
             $entityDrupalWrapperMock, $nodePrepareCalled;

      $bodyText = 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr.';
      $title = 'juhu';
      $language = 'de';
      $date = '2012-08-05 22:48:51';

      $fixNode = array();
      $fixNode['test1'] = new \StdClass();
      $fixNode['test1']->body = $bodyText;
      $fixNode['test1']->date = $date;
      $fixNode['test1']->language = $language;
      $fixNode['test1']->title = $title;
      $fixNode['test1']->type = 'article';



      $entityDrupalWrapperMock = $this->getMockBuilder('EntityDrupalWrapper')
        ->setMethods(array('save', 'value'))
        ->disableOriginalConstructor()
        ->getMock();

      $entityDrupalWrapperMock
        ->expects($this->once())
        ->method('save');


      $this->subjectToTest->createFixtures($fixNode);
      $this->assertTrue($entityMetadataWrapperCalled);
      $this->assertTrue($nodePrepareCalled);
      $this->assertEquals($bodyText, $nodeGiven->body['und'][0]['value']);
      $this->assertEquals($title, $nodeGiven->title);
      $this->assertEquals($language, $nodeGiven->language);
      $this->assertEquals(strtotime($date), $nodeGiven->created);
      $this->assertNull($fixNode['test1']->title);
      $this->assertNull($fixNode['test1']->language);
      $this->assertNull($fixNode['test1']->body);
      $this->assertNull($fixNode['test1']->date);
    }
  }
}

namespace Drupal\Fixtures\DrupalBridges {

  use Drupal\Fixtures\DrupalBridges\Specialized\SpecializedBridgeException;
  use Drupal\Fixtures\DrupalBridges\Specialized\SpecializedBridgeInterface;

  function entity_metadata_wrapper($type, $node) {
    global $entityMetadataWrapperCalled, $nodeGiven, $entityDrupalWrapperMock;
    $entityMetadataWrapperCalled = true;
    $nodeGiven = $node;

    $entityDrupalWrapperMock
      ->method('value')
      ->willReturn($nodeGiven);

    return $entityDrupalWrapperMock;
  }

  function node_object_prepare($node) {
    global $nodePrepareCalled, $nodeGiven;
    $nodePrepareCalled = true;

    $nodeGiven = $node;

    return null;
  }

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
      return array($wrappedNode->value());
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

}
