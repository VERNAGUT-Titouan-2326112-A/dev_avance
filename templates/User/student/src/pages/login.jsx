import { useState } from "react";
import axios from "../api/axios";

export default function Login() {
    const [email, setEmail] = useState("");
    const [password, setPassword] = useState("");

    const handleSubmit = async (e) => {
        e.preventDefault();

        try {
            const res = await axios.post("/login", {
                email,
                password,
            });
            console.log(res.data);
        } catch (error) {
            console.error(error);
        }
    };

    return (
        <div className="min-h-screen flex items-center justify-center bg-gray-100">
            <form
                onSubmit={handleSubmit}
                className="bg-white p-6 rounded shadow w-96"
            >
                <h1 className="text-2xl font-bold mb-4 text-center">
                    Connexion Étudiant
                </h1>

                <input
                    type="email"
                    placeholder="Email"
                    className="w-full p-2 border rounded mb-3"
                    onChange={(e) => setEmail(e.target.value)}
                />

                <input
                    type="password"
                    placeholder="Mot de passe"
                    className="w-full p-2 border rounded mb-4"
                    onChange={(e) => setPassword(e.target.value)}
                />

                <button className="w-full bg-blue-500 text-white py-2 rounded hover:bg-blue-600">
                    Se connecter
                </button>
            </form>
        </div>
    );
}
