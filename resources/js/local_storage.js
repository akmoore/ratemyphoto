export const setToken = (token) => {
    window.localStorage.setItem('token', token)
}

export const setName = (name) => {
    window.localStorage.setItem('name', name)
}

export const setRole = (role) => {
    window.localStorage.setItem('role', role)
}

export const setId = (id) => {
    window.localStorage.setItem('id', id)
}

export const getToken = () => {
    return window.localStorage.getItem('token')
}

export const getUser = () => {
    return {
        full_name: window.localStorage.getItem('name') || null,
        first_name: window.localStorage.getItem('name') ? window.localStorage.getItem('name').split(' ')[0] : null,
        role: window.localStorage.getItem('role') || null,
        id: window.localStorage.getItem('id') || null
    }
}

export const destroyUser = () => {
    window.localStorage.removeItem('token')
    window.localStorage.removeItem('name')
    window.localStorage.removeItem('role')
    window.localStorage.removeItem('id')
}