ServicesIOBundle
======================

_ServicesIO_ is a Symfony bundle that provide you the ability to turn your project into an efficient service oriented element very easily.

_ServicesIO_ basically provide you 2 mains tools entries :

 - a service reader. the reader build for you a model that wrap the input datas.
 
   + from the standart input (i.e Request object)
   
   + from a third party service you want to request

 - a full view layer. Built to get the same behavior as a powerful templating engine, the view layer is called by your controller, build the datatree you want to render, and actually render it. It provide you all the tools you need to build re-usable, flexible and extensible view components.

In the 0.9 version, only the view layer and only the json language are available.

## ServicesIO view

You are probably used to Twig to build and render your HTML views on your projects.

The aim of _ServicesIO_ is to be able to get something as powerful and efficient for dataflow that Twig could be for HTML.

### Overview

_ServicesIO view_ provide you the ability to create :

 - re-usable view components callable by path configuration.
 
 - extensible view components, with datas placeholders and smart way to fill them.
 
 - integration with bundles extension, to override view components by extending bundles.

In a nutshell, this is what you need to know before starting : 

 - The view rendering system runs with a two-steps system :
 
   1 - you will build a tree in your view on a language agnostic datastructure. a structure node is composed of 3 available elements :
   
      + a Collection, than handle others elements.
      
      + an Item, than handle named fields with an element attached.
      
      + a scalar, that will be a piece of data itself (i.e string, int, float).
      
      The construction of this tree, as you will see, would be splitted into different view elements. It always build only one tree at the end.

      Each element is a PHP class. There is no specific language for this view system.

   2 - the rendering of the tree. Once it's fully build, we will be able to turn it into a data langage (only json currently) and send it to output.

### The basics

Now, let's see that in action with a short example.

To make it easy to read, I will remove all comments and code that is not related to our topic. I assume that you already know Symfony.

Let's create a tiny project : 2 _entities_ and 2 _controllers_, and the _views_ to display them as services.

The _Doctrine entities_ :

``` php
class User
{
  /**
   * @ORM\Id
   * @ORM\Column(type="integer")
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  public $id;

  /**
   * @ORM\Column(name="content", type="string", length=255)
   */
  public $name;
}
```

``` php
class Message
{
  /**
   * @ORM\Id
   * @ORM\Column(type="integer")
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  public $id;

  /**
   * @ORM\Column(name="content", type="string", length=255)
   */
  public $title;
	
  /**
   * @ORM\Column(name="content", type="description")
   */
  public $description;
	
  /**
   * @ORM\ManyToOne(targetEntity="User", inversedBy="messages")
   * @ORM\JoinColumn(name="userId", referencedColumnName="id")
   */
  public $user;
}
```

I assume to have 2 users : author and visitor.

I assume to have 3 entries for messages : message1, message2, message3.

And the controllers :

``` php
class AllController extends Controller
{
  public function singleAction($id)
  {
    $single = $this
      ->getDoctrine()
      ->getManager()
      ->getRepository('MyBundle:Message')->findOneById($id);
  }

  public function listingAction(Request $request)
  {
    $listing = $this
      ->getDoctrine()
      ->getManager()
      ->getRepository('MyBundle:Message')->findAll();
  }
}
```

Based on that, we now have to create the view elements for _decorators_ and _entities_, and call the rendering from the _controllers_.

Let's do first the _messageAction_ view, with a _ServicesIO view_ class.

A basic view class has to :

 - extends `Redgem\ServicesIOBundle\Lib\View\View`
 
 - be located in or under `<YOURBUNDLE>\View namespace` (and therefore in the `<YOURBUNDLE>/View` directory). This is mandatory.
 
 - implements the `content()` method to build the view tree.
 
 - be named `<NAME>View` by convention (this is optional).


``` php
namespace MyBundle\View;

use Redgem\ServicesIOBundle\Lib\View\View;

class SingleView extends View
{
  public function content()
  {
    return $this->createItem()
      ->set('message', $this->createItem()
        ->set('id', $this->params['single']->id)
        ->set('title', $this->params['single']->title);
        ->set('description', $this->params['single']->description)
      );
  }
}
```

We are here creating a really simple tree with an Item object that containt 3 fields : _id_, _title_, _description_ to display those 3 _message_ fields.

Finally, let's make the _controller_ calling and rendering it : 

The rendering service in _ServicesIOBundle_ is called `servicesio_view`.

Th call takes 2 arguments : 

 - the view classpath. Basically, `<YOURBUNDLE>:<CLASSNAME>`. It can be `<YOURBUNDLE>:<SUBNAMESPACE_1>:<SUBNAMESPACE_2>:<CLASSNAME>` if you want to create a tree under the `MyBunle\View namespace`. It just replicate the namespace path and ommit the View step. I guess you will ask why not using the real full classname, we will see that later :-)

 - an array of parameters. Exactly the same way to do than with Doctrine.

``` php
public function messageAction($id)
{
  $message = $this
    ->getDoctrine()
    ->getManager()
    ->getRepository('MyBundle:Message')->findOneById($id);

  return $this->get('servicesio_view')->render(
    'MyBundle:SingleView',
    array('single' => $single)
  );
}
```

Of course, the _single_ sent as a parameter in the controller is copied in _$this->params['single']_ in the view class.

Let's call it for the first _message_ for example !

``` json
{
	"message" : {
	  "id" : "1",
	  "title" : "message 1 title",
	  "description" : "description 1 title"
	}
}
```

Wow, awesome, this is the _json_ representation of the _MessageView_ class tree !

We could now replicate it for the _listing_ action : 

``` php
namespace MyBundle\View;

use Redgem\ServicesIOBundle\Lib\View\View;

class ListingView extends View
{
  public function content()
  {
    $collection = $this->createCollection();
    
    foreach($this->params['listing'] as $message) {

      $collection->push(
        $this->createItem()
          ->set('id', $this->params['message']->id)
          ->set('title', $this->params['message']->title);
          ->set('description', $this->params['message']->description)
        );
      );
    }

    return $this->createItem()
      ->set('listing', $collection);
  }
}
```

``` php
public function listingAction(Request $request)
{
  $listing = $this
    ->getDoctrine()
    ->getManager()
    ->getRepository('MyBundle:Message')->findAll();

  return $this->get('servicesio_view')->render(
    'MyBundle:ListingView',
    array('listing' => $listing)
  );
}
```

Let's call it, and : 

``` json
{
	"listing" : [
	  {
	    "id" : "1",
	    "title" : "message 1 title",
	    "description" : "description 1 title"
	  },
	  {
	    "id" : "2",
	    "title" : "message 2 title",
	    "description" : "description 2 title"
	  },
	  {
	    "id" : "3",
	    "title" : "message 3 title",
	    "description" : "description 3 title"
	  }
	]
}
```
Excellent ! ... 

### Partials views and fragment controllers

But we have a problem. We did repeat ourselves between the 2 classes to create the partial view of a single _message_, and this is really, really bad.

Fortunaletely, there is a solution : To create a reusable element for that.

And it's really easy :

``` php
namespace MyBundle\View;

use Redgem\ServicesIOBundle\Lib\View\View;

class MessageView extends View
{
  public function content()
  {
    return $this->createItem()
      ->set('id', $this->params['message']->id)
      ->set('title', $this->params['message']->title);
      ->set('description', $this->params['message']->description)
    );
  }
}
```

As yu can see, a reusable element is a totaly regular class view. That mean, you may call it to render directly a controller if you want.

Finally use it on our _SingleView_ and _ListingView_ : 

``` php
namespace MyBundle\View;

use Redgem\ServicesIOBundle\Lib\View\View;

class SingleView extends View
{
  public function content()
  {
    return $this->createItem()
      ->set('message', $this->partial('MyBundle:MessageView', array('message' => $this->params['single'])));
  }
}
```

``` php
namespace MyBundle\View;

use Redgem\ServicesIOBundle\Lib\View\View;

class ListingView extends View
{
  public function content()
  {
    $collection = $this->createCollection();
    
    foreach($this->params['listing'] as $message) {

      $collection->push(
        $this->partial('MyBundle:MessageView', array('message' => $message))
      );
    }

    return $this->createItem()
      ->set('listing', $collection);
  }
}
```

When calling the `partial()` method to get the subtree from there, it will pass for you the current context (i.e params you sent from the controller) merged with the params you specify in the second method argument.

Of course, the _json_ final rendering is still exactly the same.

We can now easily enrich the data response everywhere : 

``` php
namespace MyBundle\View;

use Redgem\ServicesIOBundle\Lib\View\View;

class UserView extends View
{
  public function content()
  {
    return $this->createItem()
      ->set('id', $this->params['user']->id)
      ->set('name', $this->params['user']->name);
    );
  }
}
```

and 

``` php
namespace MyBundle\View;

use Redgem\ServicesIOBundle\Lib\View\View;

class MessageView extends View
{
  public function content()
  {
    return $this->createItem()
      ->set('id', $this->params['message']->id)
      ->set('title', $this->params['message']->title);
      ->set('description', $this->params['message']->description)
      ->set('user', ($this->params['message']->user == null) ? null
      	: $this->partial('MyBundle:UserView', array('user' => $this->params['message']))
     );
    );
  }
}
```

A _single_ request will now display : 

``` json
{
	"message" : {
	  "id" : "1",
	  "title" : "message 1 title",
	  "description" : "description 1 title",
	  "user": {
	    "id": "1",
	    "name": "author"
	  }
	}
}
```

Additionnaly to `partial()` method, a `controller()` method is available. This method is kind of similar. Instead of calling just a view, it will call a whole Symfony controller as a _fragment_.

Its prototype is :

`function controller($controller, $params = array())`

with :

 - `$controller` : a regular Symfony controller name (a string like _BlogBundle:Post:index_)
 
 - `$params` : an array, that will be merged with current params context and passed to the new controller.
 

The response will be handle on that way :

 - If this new controller return a _ServicesIO view_ response, the tree will be merged on the main tree at the right place.
 
 - Otherwise, the response will be threated as a string a merged on the main tree at the right place.

### Views extensions

We now want to decorate our response with the connected user on the website on the top of the response. It's easy to do with this `controller()` method.

I assume that the user is correctly authenticated by the _Security component_ :

``` php
public function visitorAction(Request $request)
{
  return $this->get('servicesio_view')->render(
    'MyBundle:UserView',
    array('user' => $this->getUser()
  );
}
```

And I add that to my views : 

``` php
namespace MyBundle\View;

use Redgem\ServicesIOBundle\Lib\View\View;

class SingleView extends View
{
  public function content()
  {
    return $this->createItem()
    	->set('visitor', $this->controller('MyBundle:All:visitor'))
    	->set('response', $this->createItem()
        ->set('message', $this->partial('MyBundle:MessageView', array('message' => $this->params['single'])))
      );
  }
}
```

``` php
namespace MyBundle\View;

use Redgem\ServicesIOBundle\Lib\View\View;

class ListingView extends View
{
  public function content()
  {
    $collection = $this->createCollection();
    
    foreach($this->params['listing'] as $message) {

      $collection->push(
        $this->partial('MyBundle:MessageView', array('message' => $message))
      );
    }

    return $this->createItem()
    	->set('visitor', $this->controller('MyBundle:All:visitor'))
    	->set('response', $this->createItem()
        ->set('listing', $collection)
      );
  }
}
```

A _single_ request will now display :

``` json
{
  "visitor": {
    "id": "2",
    "name": "visitor"
  },
  "content": {
	  "message" : {
	    "id" : "1",
	    "title" : "message 1 title",
	    "description" : "description 1 title",
	    "user": {
	      "id": "1",
	      "name": "author"
	    }
	  }
	}
}
```

And my problem of repeating myself is back... on the global decorator. I have created twice the main object with _visitor_ and _response_.

We can solve it by changing our way of thinking. Instead of having only one class to build the tree, let's split it into 2 elements :

 - building the main decorator Item (i.e, with _visitor_ and _response_)
 
 - filling the fields by each view.

First of all, let's crate the decorator thing. It's again a regular _View_ class :

``` php
namespace MyBundle\View;

use Redgem\ServicesIOBundle\Lib\View\View;

class DecoratorView extends View
{
  public function content()
  {
    return $this->createItem()
    	->set('visitor', $this->controller('MyBundle:All:visitor'))
    	->set('response', null, 'fullResponse');
  }
}
```

There is a new third argument on the `set()` method. This third argument is a string, and set up a name for the placeholder option. The placeholder is an entry on the tree that can be replaced later by an another value.

In case of placeholder, the second value (_null_ here) is a default value that will be displayed if the placeholder is not filled.

We can now transform our _SingleView_ and _ListingView_ to use this decorator :

``` php
namespace MyBundle\View;

use Redgem\ServicesIOBundle\Lib\View\View;

class SingleView extends View
{
  public function getParent()
  {
    return 'MyBundle:DecoratorView';
  }

  public function blockFullResponse()
  {
    return $this->createItem()
      ->set('message', $this->partial('MyBundle:MessageView', array('message' => $this->params['single'])));
  }
}
```

``` php
namespace MyBundle\View;

use Redgem\ServicesIOBundle\Lib\View\View;

class ListingView extends View
{
  public function getParent()
  {
    return 'MyBundle:DecoratorView';
  }

  public function blockFullResponse()
  {
    $collection = $this->createCollection();
    
    foreach($this->params['listing'] as $message) {

      $collection->push(
        $this->partial('MyBundle:MessageView', array('message' => $message))
      );
    }

    return $this->createItem()
      ->set('listing', $collection);
  }
}
```

We can see two differences :

 - a new `getParent()` method is there : that mean that the root content will be deported in this view class. All the context is passed to this object, and obviously, this is a view class you can reuse where you want.

 - the `content()` method is replaced by the _blockFullResponse()_. The `content()` method is the method that create the root of the tree. It's therefore not compatible with the `getParent() method. A class with a getParent()` method will only fill the placeholders defined above it. This is the purpose of `blockXXX()` methods (i.e blockFullResponse here).

You can of course chain how many levels oh heirarchy you want with `getParent()` and define placeholders into all of them.

`getParent()` usually return a string. It can also return an array :

``` php
public function getParent()
{
  return array('MyFriendBundle:aView', 'MyBundle:DecoratorView');
}
```
In this case, the first actually implemented view will be choosen.

### Why all those fancy stuffs ?

Why doing that ? You may say it would be easier to use the regular extends PHP word for classes, and you would be right. But _ServicesIO view_ do follow the hierarchy of Symfony bundles.

For example, if you have a new bundle _MyNewBundle_ :

``` php
class MyNewBundle extends Bundle
{
  public function getParent()
  {
    return 'MyBundle';
  }
}
```

and if you define a new _MyNewBundle\View\SingleView_, _MyNewBundle\View\ListingView_, _MyNewBundle\View\MessageView_, _MyNewBundle\View\UserView_ or _MyNewBundle\View\DecoratorView_, it will magically replace the corresponding view or MyBundle without changing paths (like _MyBundle:SingleView_) anywhere.

_ServicesIO_ provide you the ability to build some part of data rendering that will be overriten by some new parts you don't know, according to the Symfony regular behavior !

``` json
{
  "visitor": {
    "id": "2",
    "name": "visitor"
  },
  "content": {
	  "message" : {
	    "id" : "1",
	    "title" : "message 1 title",
	    "description" : "description 1 title",
	    "user": {
	      "id": "1",
	      "name": "author"
	    }
	  }
	}
}
```

``` json
{
  "visitor": {
    "id": "2",
    "name": "visitor"
  },
  "content": {
    "listing" : [
      {
        "id" : "1",
        "title" : "message 1 title",
        "description" : "description 1 title",
        "user": {
          "id": "1",
          "name": "author"
        }
      },
      {
        "id" : "2",
        "title" : "message 2 title",
        "description" : "description 2 title",
        "user": {
          "id": "1",
          "name": "author"
        }
      },
      {
        "id" : "3",
        "title" : "message 3 title",
        "description" : "description 3 title",
        "user": {
          "id": "1",
          "name": "author"
        }
      }
    ] 
  }
}
```

