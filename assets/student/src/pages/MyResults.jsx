import { useEffect, useState } from "react";
import api from "../api/axios";

export default function MyResults() {
    const [results, setResults] = useState([]);

    useEffect(() => {
        api.get("/results").then((res) => setResults(res.data));
    }, []);

    return (
        <div className="p-6">
            <h1 className="text-3xl font-bold mb-6">Mes résultats</h1>

            <table className="w-full border">
                <thead className="bg-gray-100">
                <tr>
                    <th className="border p-2">Cours</th>
                    <th className="border p-2">Note</th>
                </tr>
                </thead>
                <tbody>
                {results.map((r) => (
                    <tr key={r.id}>
                        <td className="border p-2">{r.course}</td>
                        <td className="border p-2">{r.score}/20</td>
                    </tr>
                ))}
                </tbody>
            </table>
        </div>
    );
}
