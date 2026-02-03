import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\MemeController::store
 * @see app/Http/Controllers/MemeController.php:306
 * @route '/memes/{meme}/comments'
 */
export const store = (args: { meme: number | { id: number } } | [meme: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/memes/{meme}/comments',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\MemeController::store
 * @see app/Http/Controllers/MemeController.php:306
 * @route '/memes/{meme}/comments'
 */
store.url = (args: { meme: number | { id: number } } | [meme: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { meme: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { meme: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    meme: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        meme: typeof args.meme === 'object'
                ? args.meme.id
                : args.meme,
                }

    return store.definition.url
            .replace('{meme}', parsedArgs.meme.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\MemeController::store
 * @see app/Http/Controllers/MemeController.php:306
 * @route '/memes/{meme}/comments'
 */
store.post = (args: { meme: number | { id: number } } | [meme: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\MemeController::store
 * @see app/Http/Controllers/MemeController.php:306
 * @route '/memes/{meme}/comments'
 */
    const storeForm = (args: { meme: number | { id: number } } | [meme: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: store.url(args, options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\MemeController::store
 * @see app/Http/Controllers/MemeController.php:306
 * @route '/memes/{meme}/comments'
 */
        storeForm.post = (args: { meme: number | { id: number } } | [meme: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: store.url(args, options),
            method: 'post',
        })
    
    store.form = storeForm
const comments = {
    store: Object.assign(store, store),
}

export default comments