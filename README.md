# Latter #

## What is it? ##

Latter is a form generation library for [Kohana](http://www.kohanaframework.org).

## Buzzwords ##

* Uses **[Mustache](http://mustache.github.com/) templates**
* Imports **[MangoDB](https://github.com/Wouterrr/MangoDB)** models
* Implements some **HTML5** field types and attributes (and aims to eventually support all of them).

## What does it do? ##

Currently it allows you to do the following:

* Create `form`s and set their `action` and `method` attributes.
* Add fields and set the text for their `label` tags.
* Load values from `$_POST`, `$_GET` or your own array.
* Validate the form and have the error messages displayed next to the fields.
* Import `string`, `int` and `date` fields from a MangoDB model and then have that model's validation run when the form is validated, with any error messages from the model added to their corresponding form fields.
* Add rules to fields.
* Be very flexible about how the form is rendered.

## What will it do, eventually? ##

* Import all MangoDB field types.
* Import ORM models.
* Translate labels.
* Support all HTML5 field types and attributes.
* Be clever about rules you have added. If for example the `not_empty` rule is added then the `required` attribute will be set on the field.

For more information, please check out the Wiki.