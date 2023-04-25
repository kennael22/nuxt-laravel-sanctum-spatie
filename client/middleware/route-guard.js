export default function(context) {
    if (context.route.meta.length) {
        if (!!context.route.meta[0].routeAccess && !window.$nuxt.$can(context.route.meta[0].routeAccess)) {
            window.$nuxt.error({ statusCode: 404 })
        }
    }
}

//how to use
//	middleware: ['route-guard'],
//  meta: { routeAccess: <your-permission> },
