import React, { useEffect, useState } from 'react';
import { useParams, Link } from 'react-router-dom';
import api from '../api/axios';

export default function CourseDetail() {
    const { id } = useParams();
    const [course, setCourse] = useState(null);

    useEffect(() => {
        api.get(`/courses/${id}`).then(res => setCourse(res.data));
    }, [id]);

    if (!course) return <div className="p-6">Chargement du contenu...</div>;

    return (
        <div className="container" style={{ display: 'block', maxWidth: '900px', margin: '0 auto', padding: '20px' }}>
            <Link to="/courses" style={{ color: '#1e05f3', marginBottom: '20px', display: 'block', fontWeight: 'bold' }}>
                ← Retour à la liste des cours
            </Link>

            <h1 className="title-login" style={{ textAlign: 'left', marginTop: '0' }}>{course.title}</h1>
            <p style={{ color: '#666', marginBottom: '30px' }}>{course.description}</p>

            {/* --- SECTION VIDÉOS --- */}
            {course.videos && course.videos.length > 0 && (
                <div className="student-card">
                    <h2 style={{ marginBottom: '20px' }}>🎥 Vidéos pédagogiques</h2>
                    {course.videos.map(video => (
                        <div key={video.id} style={{ marginBottom: '20px' }}>
                            <h3 style={{ marginBottom: '10px' }}>{video.title}</h3>
                            <video controls style={{ width: '100%', borderRadius: '8px' }}>
                                <source src={`/uploads/video/${video.path}`} type="video/mp4" />
                            </video>
                        </div>
                    ))}
                </div>
            )}

            {/* --- SECTION DOCUMENTS PDF --- */}
            <div className="student-card">
                <h2 style={{ marginBottom: '20px' }}>📚 Supports de cours (PDF)</h2>
                {course.documents && course.documents.length > 0 ? (
                    course.documents.map(doc => (
                        <div key={doc.id} style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', padding: '15px 0', borderBottom: '1px solid #eee' }}>
                            <span style={{ fontWeight: 'bold' }}>{doc.title}</span>
                            <div style={{ display: 'flex', gap: '10px' }}>
                                {/* Bouton LIRE (Ouvre le PDF) */}
                                <a
                                    href={`/uploads/pdf/${doc.path}`}
                                    target="_blank"
                                    rel="noreferrer"
                                    className="btn-secondary"
                                >
                                    Lire
                                </a>
                                {/* Bouton TÉLÉCHARGER */}
                                <a
                                    href={`/uploads/pdf/${doc.path}`}
                                    download={doc.title}
                                    className="btn-primary"
                                    style={{ backgroundColor: '#10b981' }}
                                >
                                    Télécharger
                                </a>
                            </div>
                        </div>
                    ))
                ) : (
                    <p style={{ fontStyle: 'italic', color: '#999' }}>Aucun document disponible.</p>
                )}
            </div>

            {/* --- SECTION QUIZ --- */}
            <div className="student-card" style={{ borderLeft: '5px solid #1e05f3' }}>
                <h2 style={{ marginBottom: '20px' }}>📝 Exercices & Quiz</h2>
                {course.qcms && course.qcms.length > 0 ? (
                    course.qcms.map(quiz => (
                        <div key={quiz.id} style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', padding: '10px 0' }}>
                            <div>
                                <span className="badge-quiz">{quiz.theme}</span>
                                <h3 style={{ margin: '5px 0 0 0' }}>{quiz.nom}</h3>
                            </div>
                            <Link to={`/quiz/${quiz.id}`} className="btn-primary">
                                Faire le test
                            </Link>
                        </div>
                    ))
                ) : (
                    <p style={{ fontStyle: 'italic', color: '#999' }}>Aucun quiz disponible.</p>
                )}
            </div>
        </div>
    );
}