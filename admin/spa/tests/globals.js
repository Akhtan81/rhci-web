const getTimezone = () => {
    var defValue = 'utc'

    try {
        var value = Intl.DateTimeFormat().resolvedOptions().timeZone
        if (typeof value === 'string') {
            return value
        }
    } catch (ignore) {
    }

    return defValue
}

const AppParameters = {
    timezone: getTimezone(),
    googleMapsApiKey: '54321',
    locale: 'en',
    locales: 'ru|kz|en'.split('|'),
    isAuthenticated: false,
    payments: {
        stripe: {
            clientId: '12345',
            redirectUrl: 'http://0.0.0.0:12020/oauth/stripe/callback',
        }
    },
    user: {
        id: null,
        name: '',
        isAdmin: false,
        email: '',
        phone: '',
        avatar: {
            id: null,
            url: '',
        },
        partner: {
            id: null,
            provider: '',
            accountId: '',
        }
    }

};

const AppRouter = {
    GET: {
        partnerMe: '/api/v2/partners/me',
        orderLocations: '/api/v2/orders/locations',
        orders: '/api/v2/orders',
        order: '/api/v2/orders/999'.replace('999', '__ID__'),
        districts: '/api/v2/geo/districts',
        district: '/api/v2/partners/999'.replace('999', '__ID__'),
        cities: '/api/v2/geo/cities',
        regions: '/api/v2/geo/regions',
        countries: '/api/v2/geo/countries',
        partners: '/api/v2/partners',
        partner: '/api/v2/partners/999'.replace('999', '__ID__'),
        categories: '/api/v2/order-categories',
        partnerCategories: '/api/v2/partner-categories',
        category: '/api/v2/order-categories/999'.replace('999', '__ID__'),
        partnerCategory: '/api/v2/partner-categories/999'.replace('999', '__ID__'),
    },
    POST: {
        media: '/api/v2/media',
        partner: '/api/v2/partners',
        partnerSignup: '/api/v1/partners/signup',
        login: '/api/v2/login',
        category: '/api/v2/order-categories',
        userPasswordReset: '/api/v1/users/reset-password',
    },
    PUT: {
        partnerMe: '/api/v2/partners/me',
        order: '/api/v2/orders/999'.replace('999', '__ID__'),
        partner: '/api/v2/partners/999'.replace('999', '__ID__'),
        category: '/api/v2/order-categories/999'.replace('999', '__ID__'),
        partnerCategory: '/api/v2/partner-categories/999'.replace('999', '__ID__'),
        userPasswordSet: '/api/v1/users/__TOKEN__/password',
    },
    DELETE: {
        category: '/api/v2/order-categories/999'.replace('999', '__ID__'),
    }
}

export default {
    AppParameters,
    AppRouter,
    getTimezone
}