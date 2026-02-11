import { useEffect, useState } from "react";
import { getStudentResults } from "../api/quizzes.js";

export default function MyResults({ userId }) {
    const [results, setResults] = useState([]);
    const [loading, setLoading] = useState(true);

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

    if (loading) return <div className="p-6">Chargement de vos notes...</div>;

    return (
        <div className="p-6">
            <h1 className="text-3xl font-bold mb-6">Mes résultats</h1>

            <table className="w-full border shadow-sm">
                <thead className="bg-gray-100">
                <tr>
                    <th className="border p-2">Quiz</th>
                    <th className="border p-2">Date</th>
                    <th className="border p-2">Note</th>
                </tr>
                </thead>
                <tbody>
                {results.length > 0 ? (
                    results.map((r) => (
                        <tr key={r.id} className="hover:bg-gray-50">
                            {/* On accède au nom du quiz via l'objet qcm chargé par l'API */}
                            <td className="border p-2">{r.qcm?.nom || "Quiz inconnu"}</td>
                            <td className="border p-2">
                                {new Date(r.submittedAt).toLocaleDateString()}
                            </td>
                            <td className="border p-2 font-bold">
                                    <span className={r.score >= 10 ? "text-green-600" : "text-red-600"}>
                                        {r.score}/20
                                    </span>
                            </td>
                        </tr>
                    ))
                ) : (
                    <tr>
                        <td colSpan="3" className="text-center p-4 text-gray-500">
                            Aucune tentative enregistrée pour le moment.
                        </td>
                    </tr>
                )}
                </tbody>
            </table>
        </div>
    );
}