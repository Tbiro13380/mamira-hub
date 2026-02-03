import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\BadgeController::index
 * @see app/Http/Controllers/BadgeController.php:15
 * @route '/conquistas'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/conquistas',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\BadgeController::index
 * @see app/Http/Controllers/BadgeController.php:15
 * @route '/conquistas'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\BadgeController::index
 * @see app/Http/Controllers/BadgeController.php:15
 * @route '/conquistas'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\BadgeController::index
 * @see app/Http/Controllers/BadgeController.php:15
 * @route '/conquistas'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\BadgeController::index
 * @see app/Http/Controllers/BadgeController.php:15
 * @route '/conquistas'
 */
    const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\BadgeController::index
 * @see app/Http/Controllers/BadgeController.php:15
 * @route '/conquistas'
 */
        indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\BadgeController::index
 * @see app/Http/Controllers/BadgeController.php:15
 * @route '/conquistas'
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
* @see \App\Http\Controllers\BadgeController::updateSelected
 * @see app/Http/Controllers/BadgeController.php:64
 * @route '/conquistas/selecionar'
 */
export const updateSelected = (options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: updateSelected.url(options),
    method: 'patch',
})

updateSelected.definition = {
    methods: ["patch"],
    url: '/conquistas/selecionar',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\BadgeController::updateSelected
 * @see app/Http/Controllers/BadgeController.php:64
 * @route '/conquistas/selecionar'
 */
updateSelected.url = (options?: RouteQueryOptions) => {
    return updateSelected.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\BadgeController::updateSelected
 * @see app/Http/Controllers/BadgeController.php:64
 * @route '/conquistas/selecionar'
 */
updateSelected.patch = (options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: updateSelected.url(options),
    method: 'patch',
})

    /**
* @see \App\Http\Controllers\BadgeController::updateSelected
 * @see app/Http/Controllers/BadgeController.php:64
 * @route '/conquistas/selecionar'
 */
    const updateSelectedForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: updateSelected.url({
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PATCH',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\BadgeController::updateSelected
 * @see app/Http/Controllers/BadgeController.php:64
 * @route '/conquistas/selecionar'
 */
        updateSelectedForm.patch = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: updateSelected.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PATCH',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    updateSelected.form = updateSelectedForm
const BadgeController = { index, updateSelected }

export default BadgeController