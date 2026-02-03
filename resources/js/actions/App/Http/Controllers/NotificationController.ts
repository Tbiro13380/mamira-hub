import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\NotificationController::index
 * @see app/Http/Controllers/NotificationController.php:13
 * @route '/notificacoes'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/notificacoes',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\NotificationController::index
 * @see app/Http/Controllers/NotificationController.php:13
 * @route '/notificacoes'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\NotificationController::index
 * @see app/Http/Controllers/NotificationController.php:13
 * @route '/notificacoes'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\NotificationController::index
 * @see app/Http/Controllers/NotificationController.php:13
 * @route '/notificacoes'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\NotificationController::index
 * @see app/Http/Controllers/NotificationController.php:13
 * @route '/notificacoes'
 */
    const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\NotificationController::index
 * @see app/Http/Controllers/NotificationController.php:13
 * @route '/notificacoes'
 */
        indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\NotificationController::index
 * @see app/Http/Controllers/NotificationController.php:13
 * @route '/notificacoes'
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
* @see \App\Http\Controllers\NotificationController::markAsRead
 * @see app/Http/Controllers/NotificationController.php:56
 * @route '/notificacoes/{notification}/read'
 */
export const markAsRead = (args: { notification: number | { id: number } } | [notification: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: markAsRead.url(args, options),
    method: 'post',
})

markAsRead.definition = {
    methods: ["post"],
    url: '/notificacoes/{notification}/read',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\NotificationController::markAsRead
 * @see app/Http/Controllers/NotificationController.php:56
 * @route '/notificacoes/{notification}/read'
 */
markAsRead.url = (args: { notification: number | { id: number } } | [notification: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { notification: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { notification: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    notification: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        notification: typeof args.notification === 'object'
                ? args.notification.id
                : args.notification,
                }

    return markAsRead.definition.url
            .replace('{notification}', parsedArgs.notification.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\NotificationController::markAsRead
 * @see app/Http/Controllers/NotificationController.php:56
 * @route '/notificacoes/{notification}/read'
 */
markAsRead.post = (args: { notification: number | { id: number } } | [notification: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: markAsRead.url(args, options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\NotificationController::markAsRead
 * @see app/Http/Controllers/NotificationController.php:56
 * @route '/notificacoes/{notification}/read'
 */
    const markAsReadForm = (args: { notification: number | { id: number } } | [notification: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: markAsRead.url(args, options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\NotificationController::markAsRead
 * @see app/Http/Controllers/NotificationController.php:56
 * @route '/notificacoes/{notification}/read'
 */
        markAsReadForm.post = (args: { notification: number | { id: number } } | [notification: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: markAsRead.url(args, options),
            method: 'post',
        })
    
    markAsRead.form = markAsReadForm
/**
* @see \App\Http\Controllers\NotificationController::markAllAsRead
 * @see app/Http/Controllers/NotificationController.php:67
 * @route '/notificacoes/read-all'
 */
export const markAllAsRead = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: markAllAsRead.url(options),
    method: 'post',
})

markAllAsRead.definition = {
    methods: ["post"],
    url: '/notificacoes/read-all',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\NotificationController::markAllAsRead
 * @see app/Http/Controllers/NotificationController.php:67
 * @route '/notificacoes/read-all'
 */
markAllAsRead.url = (options?: RouteQueryOptions) => {
    return markAllAsRead.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\NotificationController::markAllAsRead
 * @see app/Http/Controllers/NotificationController.php:67
 * @route '/notificacoes/read-all'
 */
markAllAsRead.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: markAllAsRead.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\NotificationController::markAllAsRead
 * @see app/Http/Controllers/NotificationController.php:67
 * @route '/notificacoes/read-all'
 */
    const markAllAsReadForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: markAllAsRead.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\NotificationController::markAllAsRead
 * @see app/Http/Controllers/NotificationController.php:67
 * @route '/notificacoes/read-all'
 */
        markAllAsReadForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: markAllAsRead.url(options),
            method: 'post',
        })
    
    markAllAsRead.form = markAllAsReadForm
/**
* @see \App\Http\Controllers\NotificationController::getUnreadCount
 * @see app/Http/Controllers/NotificationController.php:76
 * @route '/notificacoes/unread-count'
 */
export const getUnreadCount = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getUnreadCount.url(options),
    method: 'get',
})

getUnreadCount.definition = {
    methods: ["get","head"],
    url: '/notificacoes/unread-count',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\NotificationController::getUnreadCount
 * @see app/Http/Controllers/NotificationController.php:76
 * @route '/notificacoes/unread-count'
 */
getUnreadCount.url = (options?: RouteQueryOptions) => {
    return getUnreadCount.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\NotificationController::getUnreadCount
 * @see app/Http/Controllers/NotificationController.php:76
 * @route '/notificacoes/unread-count'
 */
getUnreadCount.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getUnreadCount.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\NotificationController::getUnreadCount
 * @see app/Http/Controllers/NotificationController.php:76
 * @route '/notificacoes/unread-count'
 */
getUnreadCount.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getUnreadCount.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\NotificationController::getUnreadCount
 * @see app/Http/Controllers/NotificationController.php:76
 * @route '/notificacoes/unread-count'
 */
    const getUnreadCountForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: getUnreadCount.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\NotificationController::getUnreadCount
 * @see app/Http/Controllers/NotificationController.php:76
 * @route '/notificacoes/unread-count'
 */
        getUnreadCountForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: getUnreadCount.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\NotificationController::getUnreadCount
 * @see app/Http/Controllers/NotificationController.php:76
 * @route '/notificacoes/unread-count'
 */
        getUnreadCountForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: getUnreadCount.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    getUnreadCount.form = getUnreadCountForm
const NotificationController = { index, markAsRead, markAllAsRead, getUnreadCount }

export default NotificationController