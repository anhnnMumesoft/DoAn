import firebase from 'firebase/compat/app';
import 'firebase/compat/auth';
import { getAuth } from 'firebase/auth'
import { initializeApp } from 'firebase/app'
const firebaseConfig = {
  apiKey: "AIzaSyDpEoQanG8KQ1TrkV32-a5TbDmiLFiQvdY",
  authDomain: "doan-8a64b.firebaseapp.com",
  projectId: "doan-8a64b",
  storageBucket: "doan-8a64b.appspot.com",
  messagingSenderId: "943023644392",
  appId: "1:943023644392:web:c31fec8376d5ce0cd6335d",
  measurementId: "G-FH2D7NZ9QQ"
};
// Initialize Firebasea
firebase.initializeApp(firebaseConfig)
export default firebase;
export const authentication = getAuth(initializeApp(firebaseConfig))