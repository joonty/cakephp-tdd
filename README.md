# TDD plugin for CakePHP 2

**Notice:** this repository will no longer receive any updates. It works *pretty* well with CakePHP ~> 2.0.5, but 2.1.0+ introduced some major API changes that causes it to break. If anyone is interested in carrying this project on then please let me know.

## Helpful tools for test driven development with the CakePHP framework

100% code coverage is the aim of test driven development, but it can be hard and
laborious to achieve after baking code through CakePHP, as straight away
you have a lot of untested code.

This plugin, along with providing some helpful testing tools, bakes fully working tests
that will give complete coverage of baked model and controller code from the outset of a project.

This allows for immediate refactoring and modification of baked code, with the assurance that TDD gives you in noticing knock on effects.

Please note that this plugin is only compatible with CakePHP 2+.

## Overview

This plugin features:

- An extension of the core bake shell, that generates complete test cases
- A new test case class, to use instead of `CakeTestCase` where required
- Validation rules on the model are analysed, to produce dummy data that will pass these rules (for use in tests)
- A few mock classes for isolating test cases from each other
- A mock class includer, that also allows mock objects to be used as controller components
- A helpful command line executable to make CLI testing easier (Unix only)

## How does it work?

Instead of using the core bake shell, use the `Tdd.bake` shell that extends it:

```bash
Console/cake Tdd.bake
```

Everything from here should be familiar, but instead of the options to bake tests and fixtures,
these are generated automatically when baking models and controllers. Furthermore with controllers, admin
routing is also tested, and the Auth and Session components are mocked.

## Testing tools

When creating test cases, extend the new `TddTestCase` class (or `TddControllerTestCase`
for testing controllers), which itself extends the `CakeTestClass`.
This class does some things to isolate tests from each other
(such as making cache configurations temporary), and it provides
a couple of useful methods to access fixtures and fixture data. For example:

```php

class UserTestCase extends TddTestCase {
	public $fixtures = array('app.user');

	//Set up, etc...

	public function testDoSomethingWithFixtureRecords() {
		//It would be helpful to get access to all the fixture data for 'user'
		$data = $this->fixtureData('user');

		//This is the $records array in the fixture class

		foreach ($data as $record) {
			//Do something
		}
	}

	public function testDoSomethingWithOneFixtureRecord() {
		//Get a single record, with the given array offset
		$record = $this->fixtureRecord('user',2);

		//Do database stuff, check result against the record...
	}

	public function testDoSomethingWithANewFixtureRecord() {
		//Generate some new data on the fly
		$newRecord = $this->newFixtureRecord('user');

		$this->User->create();
		$this->User->save(array('User'=>$newRecord));
		//etc...
	}
}
```

Why provide access to the fixture data? Let's say we have the following record in our fixture:

```php
class UserFixture extends Fixture {
	// Table set up...

	public $records = array(
		array(
			'id' => 1,
			'name' => 'John Smith',
			'email' => 'john@example.com',
			'password'=>'password'
		)
	);
}
```

Now lets say we want to test a `getByEmail()` method on the User model. We could hard-code "john@example.com" as our test email subject, expecting to get this fixture record back.
But what if another developer needs to change the fixture record for their own purposes? The test is now broken.
It's better to remove duplication by retrieving what's already in the fixture, and using that to fetch the email, like so:

```
//Test case

public function testGetByEmail() {
	$data = $this->fixtureRecord('user',0);

	//Even if the email is changed, it doesn't matter
	$result = $this->User->getByEmail($data['email']);

	$this->assertInternalType('array',$result);
	$this->assertEqual($result['User']['email'],$data['email']);
}
```

The `newFixtureRecord()` method picks a random fixture record and manipulates the data slightly to make it unique. This is useful for testing edit and add methods on controllers, for instance.

## Command line tools

Anyone who has used a CakePHP application from a Unix command line will probably have come across permissions problems with the temporary directory. This is because the web server is running as a different user to you, and temporary files are saved owned by this user.
The only way around this is to keep running `chown` and `chmod` to fix the permissions.

This plugin provides a wrapper around the CakePHP console to check the files in the temporary directory for incorrect owners and permissions, and correct them. It also adds an executable to `/usr/local/bin/cake`, which can be run as just `cake` (providing the path environment variable includes `/usr/local/bin`). This can be run from anywhere in the application path (with a few caveats).

It also provides a test shell, which allows for some neat test loading. It also assumes that you are running the `app` tests, so that option is ommitted. Also, often when developing you want to run groups of test cases, but it's too temporary to create a new test suite. The Tdd test shell allows you to use glob patterns to include multiple test files, e.g:

```bash
cake Tdd.test "Controller/*"
```

This runs all the Controller test cases. If using a glob pattern, it has to be in quotes to stop the shell passing in a whole load of files to STDIN - trust me, it doesn't work! The standard method of picking individual test cases still works, as do all the normal options to the testsuite shell.

Another handy addition is the `caketest` shell. This eliminates the need to type `cake Tdd.test`, and shortens it to `caketest`. Yeh, I'm lazy, so what? Here are some examples of it in action:

```bash
#Run all app tests
caketest "*"
#Run all model tests
caketest "Model/*"
#Run a specific test
caketest Model/User
```

