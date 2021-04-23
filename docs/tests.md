# Browser Tests

The archive frontend system utilises Laravel Dusk browser tests.

## Testing environment
When running tests, Dusk will use the `.env.dusk.local` environment file. An example file has been provided - make a copy of this with the name above. The `AppServiceProvider` will detect this. To enforce any special behaviours to the testing environment, add them in `AppServiceProvider`'s `register()` method.

### Current behaviours
####FakeApi.php
This class creates a fake API response in the expected format and is registered in `AppServiceProvider`. This is so that our browser tests don't have to make real calls to the API in order to test what appears on the page.

## Running tests
Run from the project root:

    php artisan dusk --env=local


## Creating tests
Run from the project root:
    php artisan dusk:make MyTestClass
    

## Current tests
All tests are located at `tests/Browser`

* `VideoPageTest`: Tests that the correct elements appear on an individual video page when provided with a typical API response.
* `VideoPlayerTest`: Tests that the video player source is populated when provided with an API response and that the VideoJS library is applied.
