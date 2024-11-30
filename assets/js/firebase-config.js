// Firebase Configuration
import { initializeApp } from "https://www.gstatic.com/firebasejs/11.0.2/firebase-app.js";
import { getFirestore } from "https://www.gstatic.com/firebasejs/11.0.2/firebase-firestore.js";
import { getAuth, GithubAuthProvider } from "https://www.gstatic.com/firebasejs/11.0.2/firebase-auth.js";

const firebaseConfig = {
    apiKey: "AIzaSyBSO52n1xmwKHtcrbEWG1Wr5r1WOnPicbk",
    authDomain: "adminhudev.firebaseapp.com",
    projectId: "adminhudev",
    storageBucket: "adminhudev.appspot.com",
    messagingSenderId: "1009850456954",
    appId: "1:1009850456954:web:804aa91bc13cf226e7d4c3",
    measurementId: "G-6XGWG3K0P1"
};

// Initialize Firebase
const app = initializeApp(firebaseConfig);
const db = getFirestore(app);
const auth = getAuth(app);
const githubProvider = new GithubAuthProvider();

// Configurar GitHub Provider
githubProvider.addScope('read:user');
githubProvider.setCustomParameters({
    'allow_signup': 'false',
    'redirect_uri': 'https://adminhudev.github.io/admin/login.html'
});

// Lista de usuários autorizados
const authorizedUsers = ['AdminhuDev'];

export { db, auth, githubProvider, authorizedUsers };