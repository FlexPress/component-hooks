# FlexPress theme component

## Install via pimple
Before we done anything else we need to setup the configs in pimple

```
$pimple['configHookable'] = function() {
  return new Config();
};

$pimple['hooker'] = function ($c) {
  return new Hooker($c['objectStorage'], array(
    $c['configHookable']
  ));
};

```

What we have done here is created two configs, one for the hooker and one for a hookable class, we have then passed in the configHookable to the hooker so it can hook all its hooks up. 

Note that that $c['objectStorage'] is the SPLObjectStorage class.

## Setting up hooks

To add hook we must first create a class that uses the HookableTrait, lets make a very basic class that does this:

```
class Config {

  use HookableTrait;

}
```
Simples, all done but we should probably add a hook method, so lets do that now:
```
class Config {

  use HookableTrait;
  
  /**
   * @type action
   */
  public function adminInit() {
    echo "Hello, this is the admin hook being fired";
  }

}
```
This example adds a hook for the config class and the adminInit function for the action admin_init, not that the method is in camelcase.

As well as setting up actions you can add filters like this:
```
class Config {

  use HookableTrait;
  
  /**
   * @type action
   */
  public function adminInit() {
    echo "Hello, this is the admin hook being fired";
  }
  
  /**
   * @type filter
   */
  public function theTitle($title) {
    return strip_tags($title);
  }

}
```

In this example we have added a filter that strips the tags from the title by hooking into the the_title filter.

So for filters you simply add this to the docblock above the method:

```
/**
 * @type action
 */
```

and for filters you do the same but change the type to filter:

```
/**
 * @type filter
 */
```

## Advanced usage

As well as specifying the type of hook you can also specify the priority like this:

```
/**
 * @type action
 * @priority 10
 */
```

And finally you can also specify the number of paramters you expect like this:

```
/**
 * @type action
 * @priority 10
 * @params 3
 */
```

Which allows you to add both action and filter hooks, speficy the priority as well as the number of params you expect, so nothing is taken away from the add_action and add_filter functions.
