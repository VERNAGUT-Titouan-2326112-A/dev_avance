import React from 'react';
import { createRoot } from 'react-dom/client';
import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom';

import Navbar from './src/components/Navbar.jsx';

import Courses from './src/pages/Courses.jsx';
import Quiz from './src/pages/Quiz.jsx';
import MyResults from './src/pages/MyResults.jsx';
import CourseDetail from './src/pages/CourseDetail.jsx';

function App({ userId }) {
    return (
        <BrowserRouter basename="/student">
            <div className="min-h-screen bg-gray-50">
                <Navbar />

                <div className="container mx-auto py-8">
                    <Routes>
                        <Route path="/" element={<Navigate to="/courses" replace />} />
                        <Route path="/home" element={<Navigate to="/courses" replace />} />

                        <Route path="/courses" element={<Courses />} />
                        <Route path="/course/:id" element={<CourseDetail />} />

                        {/* On passe le userId au composant Quiz */}
                        <Route path="/quiz/:id" element={<Quiz userId={userId} />} />

                        {/* On le passe aussi aux résultats pour filtrer ses propres notes plus tard */}
                        <Route path="/results" element={<MyResults userId={userId} />} />

                        <Route path="*" element={<Courses />} />
                    </Routes>
                </div>
            </div>
        </BrowserRouter>
    );
}

const container = document.getElementById('react-student-app');
if (container) {
    const userId = container.getAttribute('data-user-id');

    const root = createRoot(container);

    root.render(<App userId={userId} />);
}