// Firebase Configuration
import { initializeApp } from "https://www.gstatic.com/firebasejs/11.0.2/firebase-app.js";
import { getFirestore } from "https://www.gstatic.com/firebasejs/11.0.2/firebase-firestore.js";

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

export { db };