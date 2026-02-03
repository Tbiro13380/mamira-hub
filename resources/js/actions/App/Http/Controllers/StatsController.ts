import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\StatsController::index
 * @see app/Http/Controllers/StatsController.php:15
 * @route '/estatisticas'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/estatisticas',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\StatsController::index
 * @see app/Http/Controllers/StatsController.php:15
 * @route '/estatisticas'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\StatsController::index
 * @see app/Http/Controllers/StatsController.php:15
 * @route '/estatisticas'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\StatsController::index
 * @see app/Http/Controllers/StatsController.php:15
 * @route '/estatisticas'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\StatsController::index
 * @see app/Http/Controllers/StatsController.php:15
 * @route '/estatisticas'
 */
    const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\StatsController::index
 * @see app/Http/Controllers/StatsController.php:15
 * @route '/estatisticas'
 */
        indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\StatsController::index
 * @see app/Http/Controllers/StatsController.php:15
 * @route '/estatisticas'
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
* @see \App\Http\Controllers\StatsController::addTear
 * @see app/Http/Controllers/StatsController.php:34
 * @route '/estatisticas/lagrimas'
 */
export const addTear = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: addTear.url(options),
    method: 'post',
})

addTear.definition = {
    methods: ["post"],
    url: '/estatisticas/lagrimas',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\StatsController::addTear
 * @see app/Http/Controllers/StatsController.php:34
 * @route '/estatisticas/lagrimas'
 */
addTear.url = (options?: RouteQueryOptions) => {
    return addTear.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\StatsController::addTear
 * @see app/Http/Controllers/StatsController.php:34
 * @route '/estatisticas/lagrimas'
 */
addTear.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: addTear.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\StatsController::addTear
 * @see app/Http/Controllers/StatsController.php:34
 * @route '/estatisticas/lagrimas'
 */
    const addTearForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: addTear.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\StatsController::addTear
 * @see app/Http/Controllers/StatsController.php:34
 * @route '/estatisticas/lagrimas'
 */
        addTearForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: addTear.url(options),
            method: 'post',
        })
    
    addTear.form = addTearForm
const StatsController = { index, addTear }

export default StatsController