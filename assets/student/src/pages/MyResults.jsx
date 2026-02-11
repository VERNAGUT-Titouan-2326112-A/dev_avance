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

    const getGradeBadge = (score, total = 20) => {
        const percentage = (score / total) * 100;
        if (percentage >= 80) return <span className="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold">Excellent</span>;
        if (percentage >= 60) return <span className="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-bold">Bien</span>;
        if (percentage >= 50) return <span className="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-bold">Moyen</span>;
        return <span className="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-bold">À améliorer</span>;
    };

    if (loading) return <div className="p-6 text-center">Chargement de vos notes...</div>;

    return (
        <div className="p-6 max-w-6xl mx-auto">
            <div className="flex justify-between items-center mb-8">
                <h1 className="text-3xl font-bold flex items-center gap-3">
                    Résultats des QCM
                </h1>
                <button className="bg-emerald-500 text-white px-4 py-2 rounded shadow hover:bg-emerald-600 transition text-sm font-bold flex items-center gap-2">
                    <span>Export en CSV</span>
                </button>
            </div>

            <div className="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
                <table className="w-full text-left border-collapse">
                    <thead>
                    <tr className="bg-blue-600 text-white">
                        <th className="p-4 font-semibold uppercase text-sm">QCM</th>
                        <th className="p-4 font-semibold uppercase text-sm text-center">Date</th>
                        <th className="p-4 font-semibold uppercase text-sm text-center">Score</th>
                        <th className="p-4 font-semibold uppercase text-sm text-center">Note</th>
                        <th className="p-4 font-semibold uppercase text-sm text-center">Actions</th>
                    </tr>
                    </thead>
                    <tbody className="divide-y divide-gray-100">
                    {results.map((r) => (
                        <React.Fragment key={r.id}>
                            <tr className="hover:bg-blue-50/50 transition-colors">
                                <td className="p-4 font-medium text-gray-700">{r.qcm?.nom || "Quiz inconnu"}</td>
                                <td className="p-4 text-center text-gray-500 text-sm">{new Date(r.submittedAt).toLocaleDateString()}</td>
                                <td className="p-4 text-center font-bold text-gray-700">{r.score}/20</td>
                                <td className="p-4 text-center">{getGradeBadge(r.score)}</td>
                                <td className="p-4 text-center">
                                    <button
                                        onClick={() => setExpandedRow(expandedRow === r.id ? null : r.id)}
                                        className="text-blue-600 border border-blue-600 px-3 py-1 rounded text-xs font-bold hover:bg-blue-600 hover:text-white transition"
                                    >
                                        {expandedRow === r.id ? "Fermer" : "Voir détails"}
                                    </button>
                                </td>
                            </tr>

                            {expandedRow === r.id && (
                                <tr>
                                    <td colSpan="5" className="p-6 bg-gray-50 border-t border-b border-gray-200">
                                        <div className="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                                            <div className="p-3 bg-white border border-gray-200 rounded shadow-sm">
                                                <p className="text-[10px] text-gray-400 font-bold uppercase">Nombre de questions</p>
                                                <p className="text-xl font-bold">{r.qcm?.questions?.length || "?"}</p>
                                            </div>
                                            <div className="p-3 bg-white border border-gray-200 rounded shadow-sm border-l-4 border-l-green-500">
                                                <p className="text-[10px] text-green-600 font-bold uppercase">Bonnes réponses</p>
                                                <p className="text-xl font-bold">{r.score}</p>
                                            </div>
                                            <div className="p-3 bg-white border border-gray-200 rounded shadow-sm border-l-4 border-l-red-500">
                                                <p className="text-[10px] text-red-600 font-bold uppercase">Mauvaises réponses</p>
                                                <p className="text-xl font-bold">{(r.qcm?.questions?.length || 0) - r.score}</p>
                                            </div>
                                        </div>

                                        <div className="bg-white p-4 border border-gray-200 rounded shadow-sm">
                                            <h4 className="text-sm font-bold mb-4 flex items-center gap-2">📋 Détail des questions</h4>
                                            <div className="space-y-2">
                                                {r.qcm?.questions?.map((q, idx) => (
                                                    <div key={idx} className="flex justify-between items-center text-sm border-b border-gray-50 pb-2">
                                                        <span className="text-gray-600">Question {idx + 1} : {q.text}</span>
                                                        <span className="font-bold flex items-center gap-1">
                                                                {idx < r.score ? <span className="text-green-600">✔️ Correcte</span> : <span className="text-red-500">❌ Incorrecte</span>}
                                                            </span>
                                                    </div>
                                                ))}
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            )}
                        </React.Fragment>
                    ))}
                    </tbody>
                </table>
            </div>
        </div>
    );
}