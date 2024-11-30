// Firebase Configuration
import { initializeApp } from "https://www.gstatic.com/firebasejs/11.0.2/firebase-app.js";
import { getAnalytics } from "https://www.gstatic.com/firebasejs/11.0.2/firebase-analytics.js";
import { getAuth } from "https://www.gstatic.com/firebasejs/11.0.2/firebase-auth.js";
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

export { app, analytics, auth, db };