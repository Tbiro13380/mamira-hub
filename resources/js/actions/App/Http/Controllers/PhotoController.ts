import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\PhotoController::index
 * @see app/Http/Controllers/PhotoController.php:14
 * @route '/fotos'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/fotos',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\PhotoController::index
 * @see app/Http/Controllers/PhotoController.php:14
 * @route '/fotos'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\PhotoController::index
 * @see app/Http/Controllers/PhotoController.php:14
 * @route '/fotos'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\PhotoController::index
 * @see app/Http/Controllers/PhotoController.php:14
 * @route '/fotos'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\PhotoController::index
 * @see app/Http/Controllers/PhotoController.php:14
 * @route '/fotos'
 */
    const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\PhotoController::index
 * @see app/Http/Controllers/PhotoController.php:14
 * @route '/fotos'
 */
        indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\PhotoController::index
 * @see app/Http/Controllers/PhotoController.php:14
 * @route '/fotos'
 */
        indexForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    index.form = indexForm
/**
* @see \App\Http\Controllers\PhotoController::store
 * @see app/Http/Controllers/PhotoController.php:44
 * @route '/fotos'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/fotos',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\PhotoController::store
 * @see app/Http/Controllers/PhotoController.php:44
 * @route '/fotos'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\PhotoController::store
 * @see app/Http/Controllers/PhotoController.php:44
 * @route '/fotos'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\PhotoController::store
 * @see app/Http/Controllers/PhotoController.php:44
 * @route '/fotos'
 */
    const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: store.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\PhotoController::store
 * @see app/Http/Controllers/PhotoController.php:44
 * @route '/fotos'
 */
        storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: store.url(options),
            method: 'post',
        })
    
    store.form = storeForm
/**
* @see \App\Http\Controllers\PhotoController::destroy
 * @see app/Http/Controllers/PhotoController.php:70
 * @route '/fotos/{photo}'
 */
export const destroy = (args: { photo: number | { id: number } } | [photo: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/fotos/{photo}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\PhotoController::destroy
 * @see app/Http/Controllers/PhotoController.php:70
 * @route '/fotos/{photo}'
 */
destroy.url = (args: { photo: number | { id: number } } | [photo: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { photo: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { photo: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    photo: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        photo: typeof args.photo === 'object'
                ? args.photo.id
                : args.photo,
                }

    return destroy.definition.url
            .replace('{photo}', parsedArgs.photo.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\PhotoController::destroy
 * @see app/Http/Controllers/PhotoController.php:70
 * @route '/fotos/{photo}'
 */
destroy.delete = (args: { photo: number | { id: number } } | [photo: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

    /**
* @see \App\Http\Controllers\PhotoController::destroy
 * @see app/Http/Controllers/PhotoController.php:70
 * @route '/fotos/{photo}'
 */
    const destroyForm = (args: { photo: number | { id: number } } | [photo: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: destroy.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'DELETE',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\PhotoController::destroy
 * @see app/Http/Controllers/PhotoController.php:70
 * @route '/fotos/{photo}'
 */
        destroyForm.delete = (args: { photo: number | { id: number } } | [photo: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: destroy.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'DELETE',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    destroy.form = destroyForm
const PhotoController = { index, store, destroy }

export default PhotoController