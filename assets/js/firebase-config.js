// Firebase Configuration
import { initializeApp } from "https://www.gstatic.com/firebasejs/11.0.2/firebase-app.js";
import { getFirestore } from "https://www.gstatic.com/firebasejs/11.0.2/firebase-firestore.js";
import { getAuth, GithubAuthProvider } from "https://www.gstatic.com/firebasejs/11.0.2/firebase-auth.js";

const firebaseConfig = {
    apiKey: "AIzaSyBXxZu_0DAFXTxZkIpqKFXp3AJxbGUQHZY",
    authDomain: "adminhudev.firebaseapp.com",
    projectId: "adminhudev",
    storageBucket: "adminhudev.appspot.com",
    messagingSenderId: "1048772240903",
    appId: "1:1048772240903:web:c4a2c6a4c9c3a7c9a9c9a9"
};

// Initialize Firebase
const app = initializeApp(firebaseConfig);
const db = getFirestore(app);
const auth = getAuth(app);
const githubProvider = new GithubAuthProvider();

// Configurar GitHub Provider
githubProvider.addScope('read:user');
githubProvider.setCustomParameters({
    'allow_signup': 'false'
});

// Lista de usuários autorizados
const authorizedUsers = ['AdminhuDev'];

export { db, auth, githubProvider, authorizedUsers };