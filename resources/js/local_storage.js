import jwt from 'jwt-decode'

export const setToken = (token) => {
    window.localStorage.setItem('token', token)
}

// export const setName = (name) => {
//     window.localStorage.setItem('name', name)
// }

// export const setRole = (role) => {
//     window.localStorage.setItem('role', role)
// }

// export const setId = (id) => {
//     window.localStorage.setItem('id', id)
// }

export const getToken = () => {
    return window.localStorage.getItem('token')
}

export const getUser = () => {
    let decoded;
    if(getToken()){
        decoded = jwt(getToken())

        return {
            full_name: decoded.name || null,
            first_name: decoded.name.split(' ')[0] || null,
            role: decoded.role || null,
            id: decoded.id || null
        }
    }else{
        return {
            full_name: null,
            first_name: null,
            role: null,
            id: null
        }
    }
    
}

export const destroyUser = () => {
    window.localStorage.removeItem('token')
}