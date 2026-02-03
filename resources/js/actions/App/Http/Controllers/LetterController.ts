import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\LetterController::index
 * @see app/Http/Controllers/LetterController.php:18
 * @route '/cartas'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/cartas',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\LetterController::index
 * @see app/Http/Controllers/LetterController.php:18
 * @route '/cartas'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\LetterController::index
 * @see app/Http/Controllers/LetterController.php:18
 * @route '/cartas'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\LetterController::index
 * @see app/Http/Controllers/LetterController.php:18
 * @route '/cartas'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\LetterController::index
 * @see app/Http/Controllers/LetterController.php:18
 * @route '/cartas'
 */
    const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\LetterController::index
 * @see app/Http/Controllers/LetterController.php:18
 * @route '/cartas'
 */
        indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\LetterController::index
 * @see app/Http/Controllers/LetterController.php:18
 * @route '/cartas'
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
* @see \App\Http\Controllers\LetterController::create
 * @see app/Http/Controllers/LetterController.php:87
 * @route '/cartas/escrever'
 */
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/cartas/escrever',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\LetterController::create
 * @see app/Http/Controllers/LetterController.php:87
 * @route '/cartas/escrever'
 */
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\LetterController::create
 * @see app/Http/Controllers/LetterController.php:87
 * @route '/cartas/escrever'
 */
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\LetterController::create
 * @see app/Http/Controllers/LetterController.php:87
 * @route '/cartas/escrever'
 */
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\LetterController::create
 * @see app/Http/Controllers/LetterController.php:87
 * @route '/cartas/escrever'
 */
    const createForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: create.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\LetterController::create
 * @see app/Http/Controllers/LetterController.php:87
 * @route '/cartas/escrever'
 */
        createForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: create.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\LetterController::create
 * @see app/Http/Controllers/LetterController.php:87
 * @route '/cartas/escrever'
 */
        createForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: create.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    create.form = createForm
/**
* @see \App\Http\Controllers\LetterController::store
 * @see app/Http/Controllers/LetterController.php:92
 * @route '/cartas'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/cartas',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\LetterController::store
 * @see app/Http/Controllers/LetterController.php:92
 * @route '/cartas'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\LetterController::store
 * @see app/Http/Controllers/LetterController.php:92
 * @route '/cartas'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\LetterController::store
 * @see app/Http/Controllers/LetterController.php:92
 * @route '/cartas'
 */
    const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: store.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\LetterController::store
 * @see app/Http/Controllers/LetterController.php:92
 * @route '/cartas'
 */
        storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: store.url(options),
            method: 'post',
        })
    
    store.form = storeForm
/**
* @see \App\Http\Controllers\LetterController::destroy
 * @see app/Http/Controllers/LetterController.php:234
 * @route '/cartas/{letter}'
 */
export const destroy = (args: { letter: number | { id: number } } | [letter: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/cartas/{letter}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\LetterController::destroy
 * @see app/Http/Controllers/LetterController.php:234
 * @route '/cartas/{letter}'
 */
destroy.url = (args: { letter: number | { id: number } } | [letter: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { letter: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { letter: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    letter: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        letter: typeof args.letter === 'object'
                ? args.letter.id
                : args.letter,
                }

    return destroy.definition.url
            .replace('{letter}', parsedArgs.letter.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\LetterController::destroy
 * @see app/Http/Controllers/LetterController.php:234
 * @route '/cartas/{letter}'
 */
destroy.delete = (args: { letter: number | { id: number } } | [letter: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

    /**
* @see \App\Http\Controllers\LetterController::destroy
 * @see app/Http/Controllers/LetterController.php:234
 * @route '/cartas/{letter}'
 */
    const destroyForm = (args: { letter: number | { id: number } } | [letter: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: destroy.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'DELETE',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\LetterController::destroy
 * @see app/Http/Controllers/LetterController.php:234
 * @route '/cartas/{letter}'
 */
        destroyForm.delete = (args: { letter: number | { id: number } } | [letter: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: destroy.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'DELETE',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    destroy.form = destroyForm
/**
* @see \App\Http\Controllers\LetterController::toggleLike
 * @see app/Http/Controllers/LetterController.php:129
 * @route '/cartas/{letter}/like'
 */
export const toggleLike = (args: { letter: number | { id: number } } | [letter: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: toggleLike.url(args, options),
    method: 'post',
})

toggleLike.definition = {
    methods: ["post"],
    url: '/cartas/{letter}/like',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\LetterController::toggleLike
 * @see app/Http/Controllers/LetterController.php:129
 * @route '/cartas/{letter}/like'
 */
toggleLike.url = (args: { letter: number | { id: number } } | [letter: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { letter: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { letter: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    letter: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        letter: typeof args.letter === 'object'
                ? args.letter.id
                : args.letter,
                }

    return toggleLike.definition.url
            .replace('{letter}', parsedArgs.letter.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\LetterController::toggleLike
 * @see app/Http/Controllers/LetterController.php:129
 * @route '/cartas/{letter}/like'
 */
toggleLike.post = (args: { letter: number | { id: number } } | [letter: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: toggleLike.url(args, options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\LetterController::toggleLike
 * @see app/Http/Controllers/LetterController.php:129
 * @route '/cartas/{letter}/like'
 */
    const toggleLikeForm = (args: { letter: number | { id: number } } | [letter: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: toggleLike.url(args, options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\LetterController::toggleLike
 * @see app/Http/Controllers/LetterController.php:129
 * @route '/cartas/{letter}/like'
 */
        toggleLikeForm.post = (args: { letter: number | { id: number } } | [letter: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: toggleLike.url(args, options),
            method: 'post',
        })
    
    toggleLike.form = toggleLikeForm
/**
* @see \App\Http\Controllers\LetterController::storeComment
 * @see app/Http/Controllers/LetterController.php:178
 * @route '/cartas/{letter}/comments'
 */
export const storeComment = (args: { letter: number | { id: number } } | [letter: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeComment.url(args, options),
    method: 'post',
})

storeComment.definition = {
    methods: ["post"],
    url: '/cartas/{letter}/comments',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\LetterController::storeComment
 * @see app/Http/Controllers/LetterController.php:178
 * @route '/cartas/{letter}/comments'
 */
storeComment.url = (args: { letter: number | { id: number } } | [letter: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { letter: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { letter: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    letter: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        letter: typeof args.letter === 'object'
                ? args.letter.id
                : args.letter,
                }

    return storeComment.definition.url
            .replace('{letter}', parsedArgs.letter.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\LetterController::storeComment
 * @see app/Http/Controllers/LetterController.php:178
 * @route '/cartas/{letter}/comments'
 */
storeComment.post = (args: { letter: number | { id: number } } | [letter: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeComment.url(args, options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\LetterController::storeComment
 * @see app/Http/Controllers/LetterController.php:178
 * @route '/cartas/{letter}/comments'
 */
    const storeCommentForm = (args: { letter: number | { id: number } } | [letter: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: storeComment.url(args, options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\LetterController::storeComment
 * @see app/Http/Controllers/LetterController.php:178
 * @route '/cartas/{letter}/comments'
 */
        storeCommentForm.post = (args: { letter: number | { id: number } } | [letter: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: storeComment.url(args, options),
            method: 'post',
        })
    
    storeComment.form = storeCommentForm
const LetterController = { index, create, store, destroy, toggleLike, storeComment }

export default LetterController