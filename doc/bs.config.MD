# bs.config - BlueSpice client-side config
## Description
Config contains all statically loaded config variables, much like mw.config.
If will auto-parse and initialize itself with all variables from mw.config that follow the following naming patterns:
- bsConfigVar
- BSConfigVar
- bsgConfigVar

All names will be normalized to remove the bs/BS/bsg prefix.

### bs.config.get( name )
All variables can be accessed by calling `bs.config.get( 'varName' )`. In this case, `varName` can be the variable name
with or without the prefix, eg. both of these are valid: `bsgMyVar` and `MyVar`.

### bs.config.getDeferred( name|[names], forceRemote, context )
In addition to local variables, that originate from `mw.config`, `bs.config` is also to retrieve config variables asynchronously.
Not all variables support async retrieval, only those who follow the correct server-side implementation.

This function returns a promise, which resolves with the return value, or fails with an error message.
Argument passed to `getDeferred()` can be a name of single variable (with or without the prefix), or an array of variable names.
If single var name was passed, content of the response will be the value of the config var directly, while, if an array was passed,
an object containing `varName => value` pairs will be returned.

`forceRemote` - if set to true will re-retrieve all of the requested vars remotely, ignoring local cache
`context` - object containing custom context data - all vars in the context must have valid context names

This function allows passing in the arrays, in order to minimize the number of API calls to the backend, so that all
values are returned in one call.

#### Caching
Every time value is retrieved from remote sources, it will be caches in client-side object, and future calls will
only retrieve a value from the remote source if variable does not exist locally.
To force the values to be retrieved remotely, pass `true` for `forceRemote` param.

### bs.config.has( name, checkRemote )
This function can be called to check if the variable is present in the config. As before, name passed can be with or without
the prefix, all names will be normalized.
If `checkRemote` is false (default: false), only local variables will be checked. If it is true, remote variables will be checked as well.
Only single variable names can be passed as `name`.

## Adding variables to config
#### Static variables
To add static variables, just call OutputPage::addJsConfigVars() as usual, with making sure all variable names have the prefix `bsg`.

#### Remote variables
In order to add variables that will be accessed remotely, implement an instance of `BlueSpice\IJSConfigVariable`.
There is a base class `BlueSpice\JSConfigVariable`, which implements `getConfig()` function to return the `bsg` config, and `getContext()` that returns current context.

Such classes must be registered in `extension.json` attribute `BlueSpiceFoundationJSConfigVars`, and must follow the pattern:
`varName` => `factoryCallback`
