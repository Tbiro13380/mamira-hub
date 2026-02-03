import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\LetterController::store
 * @see app/Http/Controllers/LetterController.php:178
 * @route '/cartas/{letter}/comments'
 */
export const store = (args: { letter: number | { id: number } } | [letter: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/cartas/{letter}/comments',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\LetterController::store
 * @see app/Http/Controllers/LetterController.php:178
 * @route '/cartas/{letter}/comments'
 */
store.url = (args: { letter: number | { id: number } } | [letter: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return store.definition.url
            .replace('{letter}', parsedArgs.letter.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\LetterController::store
 * @see app/Http/Controllers/LetterController.php:178
 * @route '/cartas/{letter}/comments'
 */
store.post = (args: { letter: number | { id: number } } | [letter: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\LetterController::store
 * @see app/Http/Controllers/LetterController.php:178
 * @route '/cartas/{letter}/comments'
 */
    const storeForm = (args: { letter: number | { id: number } } | [letter: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: store.url(args, options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\LetterController::store
 * @see app/Http/Controllers/LetterController.php:178
 * @route '/cartas/{letter}/comments'
 */
        storeForm.post = (args: { letter: number | { id: number } } | [letter: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: store.url(args, options),
            method: 'post',
        })
    
    store.form = storeForm
const comments = {
    store: Object.assign(store, store),
}

export default comments