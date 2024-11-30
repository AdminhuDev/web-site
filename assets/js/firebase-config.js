// Firebase Configuration
import { initializeApp } from "https://www.gstatic.com/firebasejs/11.0.2/firebase-app.js";
import { getAnalytics } from "https://www.gstatic.com/firebasejs/11.0.2/firebase-analytics.js";
import { getAuth, GithubAuthProvider } from "https://www.gstatic.com/firebasejs/11.0.2/firebase-auth.js";
import { getFirestore, enableIndexedDbPersistence } from "https://www.gstatic.com/firebasejs/11.0.2/firebase-firestore.js";

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
const githubProvider = new GithubAuthProvider();

// Configurar regras de segurança
auth.useDeviceLanguage();

// Configurar GitHub Provider
githubProvider.setCustomParameters({
    'allow_signup': 'false' // Não permitir novos registros
});

// Habilitar persistência offline do Firestore
enableIndexedDbPersistence(db)
    .catch((err) => {
        if (err.code == 'failed-precondition') {
            console.log('Persistência múltipla não é permitida');
        } else if (err.code == 'unimplemented') {
            console.log('O navegador não suporta persistência');
        }
    });

// Lista de usuários autorizados (seu usuário GitHub)
const authorizedUsers = ['AdminhuDev']; // Seu username do GitHub

export { app, analytics, auth, db, githubProvider, authorizedUsers };