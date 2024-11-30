// Firebase Configuration
import { initializeApp } from "https://www.gstatic.com/firebasejs/11.0.2/firebase-app.js";
import { getAnalytics } from "https://www.gstatic.com/firebasejs/11.0.2/firebase-analytics.js";
import { getAuth, GithubAuthProvider } from "https://www.gstatic.com/firebasejs/11.0.2/firebase-auth.js";
import { getFirestore } from "https://www.gstatic.com/firebasejs/11.0.2/firebase-firestore.js";

const firebaseConfig = {
    apiKey: "AIzaSyBSO52n1xmwKHtcrbEWG1Wr5r1WOnPicbk",
    authDomain: "adminhudev.firebaseapp.com",
    projectId: "adminhudev",
    storageBucket: "adminhudev.firebasestorage.app",
    messagingSenderId: "1009850456954",
    appId: "1:1009850456954:web:804aa91bc13cf226e7d4c3",
    measurementId: "G-6XGWG3K0P1"
};

// Initialize Firebase
const app = initializeApp(firebaseConfig);
const analytics = getAnalytics(app);
const auth = getAuth(app);
const db = getFirestore(app);

// Configurar GitHub Provider
const githubProvider = new GithubAuthProvider();
githubProvider.addScope('read:user');
githubProvider.setCustomParameters({
    'allow_signup': 'false',
    'redirect_uri': 'https://adminhudev.github.io/admin/login.html'
});

// Configurar regras de segurança
auth.useDeviceLanguage();

// Lista de usuários autorizados (seu usuário GitHub)
const authorizedUsers = ['AdminhuDev'];

// Habilitar modo de debug em desenvolvimento
if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
    console.log('Debug mode enabled');
}

export { app, analytics, auth, db, githubProvider, authorizedUsers };