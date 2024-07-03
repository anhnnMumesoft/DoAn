import firebase from 'firebase/compat/app';
import 'firebase/compat/auth';
import { getAuth } from 'firebase/auth'
import { initializeApp } from 'firebase/app'
const firebaseConfig = {
  apiKey: "AIzaSyCpoZO7MP46KwZG6etfAHZSaOzbZr9Ny_8",
  authDomain: "verify-otp-e5909.firebaseapp.com",
  projectId: "verify-otp-e5909",
  storageBucket: "verify-otp-e5909.appspot.com",
  messagingSenderId: "389698506629",
  appId: "1:389698506629:web:3c488a395d67865c847eec",
  measurementId: "G-Z3QLCWTY8K"
};

// Initialize Firebasea
firebase.initializeApp(firebaseConfig)
export default firebase;
export const authentication = getAuth(initializeApp(firebaseConfig))