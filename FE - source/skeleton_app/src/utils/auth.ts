import axios from 'axios';
import { jwtDecode } from 'jwt-decode';
import VueCookies from 'vue-cookies';


// ------------GET METHOD--------------------------------------

export async function getNewAccessToken() {

    axios.post(import.meta.env.VITE_API_URL + 'refreshToken', {})
    .then((response) =>{
        // localStorage.setItem('token', response.data.access_token);
        setAccessToken(response.data.access_token);
        return response.data.access_token;
    }).catch((e) => {
        console.error(e.message);
        
        // TODO - controllare se il refresh token è scaduto, in tal caso fare il logout
        return null;
    });
}

export async function getAccessToken() {
    const token = localStorage.getItem('token');
    if (!token) {
        return await getNewAccessToken();
    }

    try {
        const decodedToken = jwtDecode(token);
        if(decodedToken.exp){
            if (Date.now() >= decodedToken.exp * 1000) {
                return await getNewAccessToken();
            }
        }
        return token;
    } catch (error) {
        console.error('Invalid token:', error);
        return await getNewAccessToken();
    }
}

export function getAuthorizationHeader() {
    return {
        headers: { Authorization: `Bearer ${getAccessToken()}` },
    };
}


// --------------------------------------------------


// ------------SET METHOD--------------------------------------
export function setAccessToken(token : string) {
    localStorage.setItem('token', token);
}

// --------------------------------------------------
export function logOutUser() {
    console.log("logout");
    localStorage.removeItem('token');
}



