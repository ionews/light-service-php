LightService PHP
================

[![Latest Stable Version](https://poser.pugx.org/ionews/light-service-php/v/stable.svg)](https://packagist.org/packages/ionews/light-service-php)
[![License](https://poser.pugx.org/ionews/light-service-php/license.svg)](https://packagist.org/packages/ionews/light-service-php)
[![Build Status](https://travis-ci.org/Mcbarros/light-service-php.svg?branch=master)](https://travis-ci.org/Mcbarros/light-service-php)
[![Coverage Status](https://coveralls.io/repos/Mcbarros/light-service-php/badge.png?branch=master)](https://coveralls.io/r/Mcbarros/light-service-php?branch=master)
[![Dependency Status](https://www.versioneye.com/user/projects/5384d05214c15884b3000062/badge.svg)](https://www.versioneye.com/user/projects/5384d05214c15884b3000062)

Small piece of software intended to enforce SRP on PHP apps, thought to be "light" and not use any dependencies. Heavily based on the ideas proposed by two ruby gems:
- [LightService](https://github.com/adomokos/light-service)
- [Interactor](https://github.com/collectiveidea/interactor)

Concept
-------
Each action should have a single responsibility that must be implemented in the `perform` method. An action can access databases, send emails, call services and etc.
When an action is executed, it receives a context which can be read and modified.

To perform more complex operations you must use an organizer chaining multiple actions, which will share the same context during execution. In fact, an organizer is nothing more than an action with a specific implementation, meaning that an action and an organizer share the very same interface. This is useful so you can include an organizer as an action inside another organizer.

Action examples:

```php
class GenerateRandomPassword extends Action {
  protected function perform() {
    $length = 8;
    $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $password = '';

    for ($i = 0; $i < $length; $i++) {
      $password .= $chars[rand(0, strlen($chars) - 1)];
    }

    $this->context->password = $password;
  }
}

class UpdateUserPassword extends Action {
  protected function perform() {
    $user_id = $this->context->user_id;
    $password = $this->context->password;
    // access the database using the method of your choice and update the password
  }
}
```

Organizer example:

```php
class ResetUserPassword extends Organizer {
  protected $organize = ['GenerateRandomPassword', 'UpdateUserPassword', 'EmailUserWithPassword'];
}
```

Call example (from a MVC controller):

```php
class UserController extends BaseController {
  public function resetPassword() {
    $result = ResetUserPassword::execute(['user_id' => $this->request->id]);

    if ($result->success()) {
      // use $result->getContext() to access the results and redirect the app
    } else {
      // error, use $result->getFailureMessage() to access any failure message
    }
  }
}
```

**Keep in mind that you shouldn't use this approach everywhere in your app, but only in the really complex parts of it.**

Fail and Halt
-------------
An action may fail, meaning that it couldn't achieve its goal. To make an action fail just call the `fail` method (optionally passing a message).

```php
class SomeAction extends Action {
  protected function perform() {
    $this->fail('Oh noes');
  }
}

$result = SomeAction::execute([]);
$result->success(); // false
$result->failure(); // true
$result->halted(); // false
$result->getFailureMessage(); // 'Oh noes'
```

If the action is executing inside an organizer and fails, it will prevent the execution of the subsequents actions.
If an action implements a `rollback` method, it will be called after a subsequent action fails. Example: if `EmailUserWithPassword` fails to send an e-mail to the user, we could implement an `rollback` method in the `UpdateUserPassword` to undo the update. Inside the `rollback` method you can access the context in the same way as in `perform`.

```php
class UpdateUserPassword extends Action {
  protected function perform() {
    $user_id = $this->context->user_id;
    $password = $this->context->password;
    // access the database using the method of your choice and update the password
  }

  protected function rollback() {
    // undo the update password
  }
}
```

It's possible to stop the execution chain without fail: using `halt`. Basically it will prevent any subsequent actions of execute, but the result remains a success. You can test if an action/organizer was halted using the `halted` method.

```php
class SomeAction extends Action {
  protected function perform() {
    $this->halt();
  }
}

$result = SomeAction::execute([]);
$result->success(); // true
$result->failure(); // false
$result->halted(); // true
```

Before and After
----------------
A `before` method can be implemented if you need to do any setup pre-execution. If the `fail` method is called inside the `before`, `perform` will never be called.
In the same way, an `after` method can be implemented so you can do any cleanup, but keep in mind that if `before` or `perform` fails it will never be called.

```php
class SomeAction extends Action {
  protected function before() {
    // any setup
  }

  protected function perform() {
    // perform
  }

  protected function after() {
    // cleanup
  }
}
```

Expects and Promises
--------------------
Expectations and promises can be defined for each action. If an action has a set of expectations, it will automatically fails if these aren't met.

```php
class UpdateUserPassword extends Action {
  protected $expects = ['user_id', 'password'];

  protected function perform() {
    $user_id = $this->context->user_id;
    $password = $this->context->password;
    // access the database using the method of your choice and update the password
  }
}

$result = UpdateUserPassword::execute(['user_id' => 1]);
$result->success(); // false
$result->getFailureMessage(); // 'Expectations were not met'
```

Similarly, an action will fail if a set of promises are defined and these are not present in the context at the end of execution.

```php
class GenerateRandomPassword extends Action {
  protected $promises = ['password'];

  protected function perform() {
    $length = 8;
    $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $password = '';

    for ($i = 0; $i < $length; $i++) {
      $password .= $chars[rand(0, strlen($chars) - 1)];
    }

    //$this->context->password = $password;
  }
}

$result = GenerateRandomPassword::execute([]);
$result->success(); // false
$result->getFailureMessage(); // 'Promises were not met'
```

This feature is particularly useful so you can explicitly define the interface between the actions.

Iterator Action
---------------
It's an action that will be performed over an array.

```php
class SomeAction extends IteratorAction {
  protected $over = 'key_of_the_array_in_context'

  protected function each($key, $value) {
    ...
  }
}

```

Requirements
------------
- PHP 5.3+

Installation and Usage
----------------------

Contributing
------------
You know the drill!

License
-------
Released under GPLv2
