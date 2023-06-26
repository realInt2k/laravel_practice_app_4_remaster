# DEV
## CSS:
- Go to `${host}/documentation/getting-started/installation.html` to see available syntax for css
## Eloquent
- command `art ide-helper:models` helps you create phpDocs for syntax like eloquent:
  - For example: `art ide-helper:models 'App\Models\Product'`
  - Remember to change `@mixin \Eloquent` to `@mixin Builder` + clean up code.
  - Install Laravel-Query plugin.
## JAVASCRIPT
- ESLINT: https://gist.github.com/EmadAdly/b356690a4cb3b9a79d6757d5ebd6c93c
## SOLID
- A class should do one thing and therefore it should have only a single reason to change.
- A class should be opened for extension and closed for modification.
- Subclass should not narrow down context of parent class.
- Implementing many interfaces is better than having 1 giant chunk of interface.
- Dependencies to depend on interfaces instead of concrete classes.
  - For example, we could have a Repository Interface, and Contextually bind each implementation with its respective 
    Service
- Key: #interfaces #interfaces #interfaces
## Test guidance
- https://github.com/sang-hv/laravel-testing-guide/blob/main/FeatureTest/README.md
## Laravel best practice
- https://github.com/alexeymezenin/laravel-best-practices
