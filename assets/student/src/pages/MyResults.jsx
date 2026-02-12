import React, { useEffect, useState } from "react";
import { getStudentResults } from "../api/quizzes.js";

export default function MyResults({ userId }) {
    const [results, setResults] = useState([]);
    const [loading, setLoading] = useState(true);
    const [expandedRow, setExpandedRow] = useState(null);

    useEffect(() => {
        if (userId) {
            getStudentResults(userId)
                .then((data) => {
                    setResults(data);
                    setLoading(false);
                })
                .catch((err) => {
                    console.error("Erreur résultats:", err);
                    setLoading(false);
                });
        }
    }, [userId]);

    // --- CALCUL DES SCORES RÉELS ---
    const calculateRealScore = (rawScore, totalQuestions) => {
        // Si le score en base (ex: 20) est plus grand que le nombre de questions (ex: 3),
        // c'est qu'il est sur 20. On fait la règle de trois.
        if (totalQuestions > 0 && rawScore > totalQuestions) {
            return Math.round((rawScore * totalQuestions) / 20);
        }
        return rawScore;
    };

    // --- BADGES COULEUR ---
    const getBadgeStyle = (score, total) => {
        const percentage = total > 0 ? (score / total) * 100 : 0;
        if (percentage >= 80) return { bg: '#dcfce7', color: '#166534', label: 'Excellent' };
        if (percentage >= 60) return { bg: '#dbeafe', color: '#1e40af', label: 'Bien' };
        if (percentage >= 50) return { bg: '#fef9c3', color: '#854d0e', label: 'Moyen' };
        return { bg: '#fee2e2', color: '#991b1b', label: 'À améliorer' };
    };

    if (loading) return <div style={{ padding: '50px', textAlign: 'center', color: '#666' }}>Chargement...</div>;

    return (
        <div style={{ maxWidth: '1200px', margin: '0 auto', padding: '20px', fontFamily: "'Segoe UI', sans-serif" }}>

            {/* --- STYLE FORCÉ (Pour écraser les conflits CSS) --- */}
            <style>{`
                /* TABLEAU */
                .results-table { width: 100%; border-collapse: separate; border-spacing: 0; background: white; border-radius: 8px; overflow: hidden; border: 1px solid #e5e7eb; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
                .results-thead { background-color: #0284c7; color: white; }
                .results-th { padding: 16px; text-align: left; font-weight: 700; text-transform: uppercase; font-size: 13px; letter-spacing: 0.5px; }
                .results-td { padding: 16px; border-bottom: 1px solid #f3f4f6; color: #1f2937; vertical-align: middle; }
                .results-tr:hover { background-color: #f8fafc; }
                
                /* BOUTONS */
                .btn-csv { background-color: #10b981 !important; color: white !important; border: none; padding: 10px 20px; border-radius: 6px; font-weight: bold; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; }
                .btn-details { background: white; border: 1px solid #0284c7; color: #0284c7; padding: 6px 14px; border-radius: 6px; font-weight: 600; cursor: pointer; transition: all 0.2s; font-size: 13px; }
                .btn-details:hover { background: #0284c7; color: white; }
                .btn-close { background-color: #ef4444; color: white; border: none; padding: 8px 20px; border-radius: 6px; font-weight: bold; cursor: pointer; margin-top: 15px; }

                /* ZONE DÉTAILS */
                .details-box { background-color: #f9fafb; padding: 20px; border-bottom: 1px solid #e5e7eb; }
                .details-inner { background: white; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
                
                /* CARTES STATS */
                .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 25px; }
                .stat-card { background: white; padding: 15px; border-radius: 8px; border: 1px solid #e5e7eb; border-left-width: 5px; text-align: center; }
                .stat-title { font-size: 11px; font-weight: 800; text-transform: uppercase; margin-bottom: 5px; }
                .stat-num { font-size: 24px; font-weight: 900; color: #111827; }

                /* LISTE QUESTIONS */
                .q-list { border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden; }
                .q-item { display: flex; justify-content: space-between; padding: 12px 15px; border-bottom: 1px solid #f3f4f6; background: #fff; font-size: 14px; }
                .q-item:last-child { border-bottom: none; }
                .q-correct { color: #166534; font-weight: 700; background: #dcfce7; padding: 2px 8px; border-radius: 4px; font-size: 11px; }
                .q-incorrect { color: #991b1b; font-weight: 700; background: #fee2e2; padding: 2px 8px; border-radius: 4px; font-size: 11px; }
            `}</style>

            {/* EN-TÊTE */}
            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '25px' }}>
                <h1 style={{ fontSize: '28px', fontWeight: '800', color: '#111827', margin: 0 }}>
                    📊 Résultats des QCM
                </h1>
                <button className="btn-csv">📥 Exporter en CSV</button>
            </div>

            {/* TABLEAU */}
            <table className="results-table">
                <thead className="results-thead">
                <tr>
                    <th className="results-th">QCM</th>
                    <th className="results-th">Date</th>
                    <th className="results-th" style={{textAlign: 'center'}}>Score</th>
                    <th className="results-th" style={{textAlign: 'center'}}>Note</th>
                    <th className="results-th" style={{textAlign: 'center'}}>Actions</th>
                </tr>
                </thead>
                <tbody>
                {results.map((r) => {
                    // 1. Calculs
                    const totalQ = r.qcm?.questions?.length || 0;
                    const realScore = calculateRealScore(r.score, totalQ);
                    const badge = getBadgeStyle(realScore, totalQ);
                    const isOpen = expandedRow === r.id;

                    return (
                        <React.Fragment key={r.id}>
                            <tr className="results-tr">
                                <td className="results-td" style={{fontWeight: '600'}}>{r.qcm?.nom || "Quiz"}</td>
                                <td className="results-td">{new Date(r.submittedAt).toLocaleDateString('fr-FR')}</td>
                                <td className="results-td" style={{textAlign: 'center'}}>
                                    <span style={{fontWeight: '900', fontSize: '16px'}}>{realScore}</span>
                                    <span style={{color: '#9ca3af', fontSize: '13px'}}>/{totalQ}</span>
                                </td>
                                <td className="results-td" style={{textAlign: 'center'}}>
                                        <span style={{ backgroundColor: badge.bg, color: badge.color, padding: '4px 12px', borderRadius: '20px', fontSize: '12px', fontWeight: '800' }}>
                                            {badge.label}
                                        </span>
                                </td>
                                <td className="results-td" style={{textAlign: 'center'}}>
                                    <button
                                        className="btn-details"
                                        onClick={() => setExpandedRow(isOpen ? null : r.id)}
                                    >
                                        {isOpen ? "Fermer" : "Voir détails"}
                                    </button>
                                </td>
                            </tr>

                            {/* ZONE DÉPLIÉE */}
                            {isOpen && (
                                <tr>
                                    <td colSpan="5" className="details-box">
                                        <div className="details-inner">

                                            {/* CARTES STATS */}
                                            <div className="stats-grid">
                                                <div className="stat-card" style={{borderLeftColor: '#3b82f6'}}>
                                                    <div className="stat-title" style={{color: '#3b82f6'}}>📝 Questions</div>
                                                    <div className="stat-num">{totalQ}</div>
                                                </div>
                                                <div className="stat-card" style={{borderLeftColor: '#22c55e'}}>
                                                    <div className="stat-title" style={{color: '#16a34a'}}>✅ Correctes</div>
                                                    <div className="stat-num">{realScore}</div>
                                                </div>
                                                <div className="stat-card" style={{borderLeftColor: '#ef4444'}}>
                                                    <div className="stat-title" style={{color: '#dc2626'}}>❌ Erreurs</div>
                                                    <div className="stat-num">{totalQ - realScore}</div>
                                                </div>
                                            </div>

                                            {/* LISTE QUESTIONS */}
                                            <h4 style={{fontWeight: 'bold', marginBottom: '10px', color: '#374151'}}>📋 Détail des réponses</h4>
                                            <div className="q-list">
                                                {r.qcm?.questions?.map((q, idx) => (
                                                    <div key={idx} className="q-item">
                                                            <span style={{color: '#4b5563'}}>
                                                                <span style={{fontWeight: 'bold', color: '#9ca3af', marginRight: '10px'}}>#{idx + 1}</span>
                                                                {q.text}
                                                            </span>
                                                        {idx < realScore ? (
                                                            <span className="q-correct">CORRECTE</span>
                                                        ) : (
                                                            <span className="q-incorrect">INCORRECTE</span>
                                                        )}
                                                    </div>
                                                ))}
                                            </div>

                                            <button className="btn-close" onClick={() => setExpandedRow(null)}>
                                                Fermer les détails
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            )}
                        </React.Fragment>
                    );
                })}
                </tbody>
            </table>
        </div>
    );
}