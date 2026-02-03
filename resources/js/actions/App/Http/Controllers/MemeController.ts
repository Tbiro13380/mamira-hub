import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\MemeController::index
 * @see app/Http/Controllers/MemeController.php:18
 * @route '/memes'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/memes',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\MemeController::index
 * @see app/Http/Controllers/MemeController.php:18
 * @route '/memes'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\MemeController::index
 * @see app/Http/Controllers/MemeController.php:18
 * @route '/memes'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\MemeController::index
 * @see app/Http/Controllers/MemeController.php:18
 * @route '/memes'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\MemeController::index
 * @see app/Http/Controllers/MemeController.php:18
 * @route '/memes'
 */
    const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\MemeController::index
 * @see app/Http/Controllers/MemeController.php:18
 * @route '/memes'
 */
        indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\MemeController::index
 * @see app/Http/Controllers/MemeController.php:18
 * @route '/memes'
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
* @see \App\Http\Controllers\MemeController::store
 * @see app/Http/Controllers/MemeController.php:153
 * @route '/memes'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/memes',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\MemeController::store
 * @see app/Http/Controllers/MemeController.php:153
 * @route '/memes'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\MemeController::store
 * @see app/Http/Controllers/MemeController.php:153
 * @route '/memes'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\MemeController::store
 * @see app/Http/Controllers/MemeController.php:153
 * @route '/memes'
 */
    const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: store.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\MemeController::store
 * @see app/Http/Controllers/MemeController.php:153
 * @route '/memes'
 */
        storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: store.url(options),
            method: 'post',
        })
    
    store.form = storeForm
/**
* @see \App\Http\Controllers\MemeController::vote
 * @see app/Http/Controllers/MemeController.php:220
 * @route '/memes/{meme}/vote'
 */
export const vote = (args: { meme: number | { id: number } } | [meme: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: vote.url(args, options),
    method: 'post',
})

vote.definition = {
    methods: ["post"],
    url: '/memes/{meme}/vote',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\MemeController::vote
 * @see app/Http/Controllers/MemeController.php:220
 * @route '/memes/{meme}/vote'
 */
vote.url = (args: { meme: number | { id: number } } | [meme: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return vote.definition.url
            .replace('{meme}', parsedArgs.meme.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\MemeController::vote
 * @see app/Http/Controllers/MemeController.php:220
 * @route '/memes/{meme}/vote'
 */
vote.post = (args: { meme: number | { id: number } } | [meme: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: vote.url(args, options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\MemeController::vote
 * @see app/Http/Controllers/MemeController.php:220
 * @route '/memes/{meme}/vote'
 */
    const voteForm = (args: { meme: number | { id: number } } | [meme: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: vote.url(args, options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\MemeController::vote
 * @see app/Http/Controllers/MemeController.php:220
 * @route '/memes/{meme}/vote'
 */
        voteForm.post = (args: { meme: number | { id: number } } | [meme: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: vote.url(args, options),
            method: 'post',
        })
    
    vote.form = voteForm
/**
* @see \App\Http\Controllers\MemeController::storeComment
 * @see app/Http/Controllers/MemeController.php:306
 * @route '/memes/{meme}/comments'
 */
export const storeComment = (args: { meme: number | { id: number } } | [meme: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeComment.url(args, options),
    method: 'post',
})

storeComment.definition = {
    methods: ["post"],
    url: '/memes/{meme}/comments',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\MemeController::storeComment
 * @see app/Http/Controllers/MemeController.php:306
 * @route '/memes/{meme}/comments'
 */
storeComment.url = (args: { meme: number | { id: number } } | [meme: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return storeComment.definition.url
            .replace('{meme}', parsedArgs.meme.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\MemeController::storeComment
 * @see app/Http/Controllers/MemeController.php:306
 * @route '/memes/{meme}/comments'
 */
storeComment.post = (args: { meme: number | { id: number } } | [meme: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeComment.url(args, options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\MemeController::storeComment
 * @see app/Http/Controllers/MemeController.php:306
 * @route '/memes/{meme}/comments'
 */
    const storeCommentForm = (args: { meme: number | { id: number } } | [meme: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: storeComment.url(args, options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\MemeController::storeComment
 * @see app/Http/Controllers/MemeController.php:306
 * @route '/memes/{meme}/comments'
 */
        storeCommentForm.post = (args: { meme: number | { id: number } } | [meme: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: storeComment.url(args, options),
            method: 'post',
        })
    
    storeComment.form = storeCommentForm
const MemeController = { index, store, vote, storeComment }

export default MemeController