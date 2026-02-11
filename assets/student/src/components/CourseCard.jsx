import React from 'react';
import { Link } from "react-router-dom";

export default function CourseCard({ course }) {
    return (
        <div style={{
            border: '2px solid #e5e7eb',
            borderRadius: '12px',
            padding: '20px',
            marginBottom: '20px',
            backgroundColor: '#fff',
            boxShadow: '0 4px 6px -1px rgba(0, 0, 0, 0.1)'
        }} className="course-card">
            <h2 style={{ fontSize: '1.5rem', fontWeight: 'bold', color: '#1a202c' }}>
                {course.title}
            </h2>
            <p style={{ color: '#4a5568', margin: '10px 0' }}>
                {course.description}
            </p>

            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginTop: '15px' }}>
                <span style={{
                    backgroundColor: '#ebf8ff',
                    color: '#2b6cb0',
                    padding: '4px 12px',
                    borderRadius: '999px',
                    fontSize: '0.875rem',
                    fontWeight: '600'
                }}>
                    {course.level || 'Étudiant'}
                </span>

                <Link
                    to={`/course/${course.id}`}
                    style={{
                        backgroundColor: '#1e05f3',
                        color: '#fff',
                        padding: '8px 20px',
                        borderRadius: '8px',
                        textDecoration: 'none',
                        fontWeight: 'bold'
                    }}
                >
                    Accéder au cours
                </Link>
            </div>
        </div>
    );
}