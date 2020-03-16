ServicesIOBundle
======================

_ServicesIOBundle_ is a Symfony bundle that provides a way to easily build a services provider.

_ServicesIO_ basically introduces two components :

 - a reader. the reader build for you a model that wrap the input data.
   + from the standart input (i.e Request object).
   + from a third party service you want to request.

 - a view layer to scaffold an object tree view of the reponse. The view layer is called by your controller, build the tree you want to render, and actually render it (json only so far).

## Install the bundle

First of all, add and enable _ServicesIOBundle_ in your project.

Add it in composer :

`composer require redgem/servicesio-bundle:1.0.*`

All set.

## ServicesIO Model

The model will help you to read and decode trees structures (such as json) passed, for instance, as requests to your controllers.

_documentation of the Model component to come later_

## ServicesIO View

You are probably used to _Twig_ to build and render your HTML views on your projects.

The aim of _ServicesIO_ is to be able to get something as powerful and efficient for data trees that _Twig_ could be for HTML.

### Overview

_ServicesIO view_ provide you the ability to create :

 - re-usable view components callable by path configuration.
 - extensible view components, with datas placeholders and smart way to fill them.
 - integration with bundles extension, to override view components by extending bundles.

Here is what you need to know before starting : 

 - The view rendering system runs by a two-steps system :
 
   1 - you will build a tree in your view on a language agnostic datastructure by creating connected nodes. a node is composed of 3 available elements :
      + a Collection, than handle a list of others nodes.
      + an Item, than handle named fields with nodes attached.
      + a scalar, that will be the piece of data to render (i.e _string_, _int_, _float_).

   2 - the rendering of the tree. Once it's fully build, we will be able to turn it into a data langage (only json so far) and send it to output.

### The basics : create your View

Now, let's see that in action with a short example.

To make it easy to read, I will remove all code that is not related to our topic.

Let's create a small example project : 2 _entities_ and 2 _controllers_.

The _Doctrine entities_ :

``` php
class User
{
  /**
   * @MongoDB\Id
   */
  public $id;

  /**
   * @MongoDB\Field(type="string")
   */
  public $name;
}
```

``` php
class Message
{
  /**
   * @MongoDB\Id
   */
  public $id;

  /**
   * @MongoDB\Field(type="string")
   */
  public $title;
	
  /**
   * @MongoDB\Field(type="string")
   */
  public $description;
	
  /**
   * @MongoDB\Field(type="id")
   */
  public $user;
}
```

I assume to have 2 users : _author_ and _visitor_ and 3 entries for messages : message1, message2, message3.

Here are the controllers (without their return calls) :

``` php
class MessageController
{
  public function singleAction(int $id, DocumentManager $documentManager)
  {
    $single = $documentManager
      ->getRepository(Message::class)->find($id);
  }

  public function listingAction(DocumentManager $documentManager)
  {
    $listing = $documentManager
      ->getRepository(Message::class)->findAll();
  }
}
```

Based on that, we now have to create the _View_ elements for _decorators_ and _entities_, and call the rendering from the _controllers_.

Let's complete first the _messageAction_'s View, with a _ServicesIO view_ class.

A basic `View` class has to :

 - extends `Redgem\ServicesIOBundle\Lib\View\View`
 - location is up to you, we recommend using `<APP_OR_YOUR_BUNDLE>\View namespace` (and therefore in the `<APP_OR_YOUR_BUNDLE>/View` directory) to make a clear organisation.
 - implements the `content()` that is the place to build the view tree.
 - class name is also up to you, we recommend to name it `<NAME>View`.


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

We are here creating a simple tree with an Item object that containt 3 childrens : _id_, _title_, _description_ to display our 3 corresponding _message_ fields.

Finally, let's make the _controller_ calling and rendering it : 

The rendering service to call in _ServicesIOBundle_ is called `Redgem\ServicesIOBundle\Lib\View\Service`.

The call takes 2 arguments : 

 - the _viewpath_. the view class name.
 - an array of parameters to send.

``` php
use Redgem\ServicesIOBundle\Lib\View\Service as View;

class MessageController
{
  public function singleAction(int $id, DocumentManager $documentManager, View $view)
  {
    $single = $documentManager
      ->getRepository(Message::class)->find($id);

    return $view->render(
      SingleView::class,
      ['single' => $single]
    );
  }
}
```

The _single_ variable sent as a parameter in the controller is accessible as _$this->params['single']_ in the view class.

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

Nice, we now have the _json_ representation of the _MessageView_ class tree !

We can now replicate it for the _listing_ action : 

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
use Redgem\ServicesIOBundle\Lib\View\Service as View;

class MessageController
{
  public function listingAction(DocumentManager $documentManager, View $view)
  {
    $listing = $documentManager
      ->getRepository(Message::class)->findAll();

    return $view->render(
      ListingView::class,
      ['listing' => $listing]
    );
  }
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
Excellent !

### Partials views and fragment controllers

We have a problem. We did repeat ourselves between the 2 classes to create the partial view of a _message_.

Fortunately, there is a solution, : to create a reusable element.

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

As yu can see, a reusable element is a totaly regular View class. That mean, you may use it to render directly a controller if you want to.

Finally, we use it on our _SingleView_ and _ListingView_ : 

``` php
namespace MyBundle\View;

use Redgem\ServicesIOBundle\Lib\View\View;

class SingleView extends View
{
  public function content()
  {
    return $this->createItem()
      ->set('message', $this->partial(MessageView::class, ['message' => $this->params['single']]));
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
        $this->partial(MessageView::class, ['message' => $message])
      );
    }

    return $this->createItem()
      ->set('listing', $collection);
  }
}
```

When calling the `partial()` method to get the subtree from there, it will pass for you the current context (i.e params you sent from the controller) merged with the params you add in the second method argument.

Of course, the _json_ final rendering is still exactly the same.

Let's now enrich the data response everywhere with a new _UserView_ :

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
      	: $this->partial(UserView:class, ['user' => $this->params['message']])
      )
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

In addition to to `partial()` method, a `controller()` method is available. Instead of calling just a view, it will call a whole Symfony controller as a _fragment_.

Its prototype is :

`function controller($controller, $params = array())`

with :

 - `$controller` : a regular Symfony controller name (as a string, with full class name)
 - `$params` : an array, that will be merged with current params context and passed to the new controller.

The response will be handle on that way :

 - If this new controller return a _ServicesIO view_ response, the fragment tree will be merged on the main tree at the right place.
 - Otherwise, the response will be threated as a string and merged on the main tree at the right place.

And one more thing : 

- `get($service)` - string $service : a Symfony service name.
-  `getParameter($parameter)`  - string $parameter : a parameter name.

methods are available as well. They just use the _container_ to call a _Symfony service_ or a _parameter_.

### View extensions

We now want to decorate our response with the connected user on the top of it. It's easy to do with this `controller()` method.

I assume that the user is correctly authenticated by the _Security component_ :

``` php
public function visitorAction(View $view)
{
  return $view->render(
    UserView::class,
    ['user' => $this->getUser()] //returns a User object
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
      ->set('visitor', $this->controller('App\MyController\VisitorAction'))
      ->set('response', $this->createItem()
        ->set('message', $this->partial(MessageView::class, ['message' => $this->params['single']]))
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
        $this->partial(MessageView::class, ['message' => $message])
      );
    }

    return $this->createItem()
      ->set('visitor', $this->controller('App\MyController\VisitorAction'))
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

 - a class to build the main decorator node (i.e, with _visitor_ and _response_)
 - filling the fields of the _response_ node by each view classes.

First of all, let's create the decorator thing. It's still a regular _View_ class :

``` php
namespace MyBundle\View;

use Redgem\ServicesIOBundle\Lib\View\View;

class DecoratorView extends View
{
  public function content()
  {
    return $this->createItem()
    	->set('visitor', $this->controller('App\MyController\VisitorAction'))
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
    return DecoratorView::class;
  }

  public function blockFullResponse()
  {
    return $this->createItem()
      ->set('message', $this->partial(MessageView::class, ['message' => $this->params['single']]));
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
    return DecoratorView::class;
  }

  public function blockFullResponse()
  {
    $collection = $this->createCollection();
    
    foreach($this->params['listing'] as $message) {
      $collection->push(
        $this->partial(MessageView::class, ['message' => $message])
      );
    }

    return $this->createItem()
      ->set('listing', $collection);
  }
}
```

We can see two differences :

 - a new `getParent()` method appeared : that means that the root node will be deported in this view class. All the context is passed to this object, and obviously, this is a standard View class you can reuse where you want.
 - the `content()` method is replaced by the _blockFullResponse()_. The `content()` method is the method that create the root node of the tree. It's therefore not compatible with the `getParent() method. A class with a getParent()` method will only fill the placeholders defined above it. This is the purpose of `blockXXX()` where XXX is the placeholder name in CamelCase. methods (i.e blockFullResponse here).

You can of course chain how many levels oh hierarchy you want with `getParent()` and define placeholders into all of them.

`getParent()` usually return a string. It can also return an array :

``` php
public function getParent()
{
  return array(MyFriendBundleView::class, MyBundleDecoratorView::class);
}
```
In this case, the first actually implemented View class will be chosen.

### Why all those fancy stuffs ?

Why doing that ? You may say it would be easier to use the regular extends PHP word for classes, and avoiding using _viewpath_, and you would be right. But _ServicesIO view_ do provides you an easy, flexible, and clear way to build some view reusables pieces.

So Finally, we can call our controllers :

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

