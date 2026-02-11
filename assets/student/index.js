// assets/student/index.js

import React from 'react';
import { createRoot } from 'react-dom/client';
import {BrowserRouter, Routes, Route, Navigate} from 'react-router-dom';

import Courses from './src/pages/Courses';
import Quiz from './src/pages/Quiz';
import MyResults from './src/pages/MyResults';
import CourseDetail from './src/pages/CourseDetail';

function App() {
    return (
        <BrowserRouter basename="/student">
            <Routes>
                <Route path="/" element={<Navigate to="/home" replace />} />

                <Route path="/course/:id" element={<CourseDetail />} />
                <Route path="/quiz/:id" element={<Quiz />} />
                <Route path="/results" element={<MyResults />} />

                <Route path="*" element={<Courses />} />
            </Routes>
        </BrowserRouter>
    );
}

const container = document.getElementById('react-student-app');
if (container) {
    const root = createRoot(container);
    root.render(<App />);
}