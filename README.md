LightService PHP
================
Small piece of software intended to enforce SRP on PHP apps, thought to be "light" and not use any dependencies. Heavily based on the ideas proposed by two ruby gems:
- [LightService](https://github.com/adomokos/light-service)
- [Interactor](https://github.com/collectiveidea/interactor)



Concept
-------
Each action should have a single responsibility that must be implemented in the perform method. An action can access databases, send emails, call services and etc.
When an action is executed, it receives a context which can be read and modified.

To perform more complex operations you must use an organizer chaining multiple actions, which will share the same context during execution. In fact, an organizer is nothing more than an action with a specific implementation, meaning that an action and an organizer share the very same interface. This is useful so you can include an organizer as an action inside another organizer.

Action examples:

```php
class GenerateRandomPassword extends LightAction {
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

class UpdateUserPassword extends LightAction {
  protected function perform() {
    $user_id = $this->context->user_id;
    $password = $this->context->password;
    // access the database using the method of your choice and update the password
  }
}
```

Organizer example:

```php
class ResetUserPassword extends LightOrganizer {
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

Before and After
----------------

Expects and Promises
--------------------

Iterator Action
---------------

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
