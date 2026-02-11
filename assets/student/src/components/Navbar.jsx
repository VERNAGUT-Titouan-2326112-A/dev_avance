import React from 'react';
import { Link } from "react-router-dom";

export default function Navbar() {
    const handleLogout = () => {
        window.location.href = "/logout";
    };

    return (
        <nav className="bg-white shadow px-6 py-4 flex justify-between items-center mb-6">
            <Link to="/courses" className="font-bold text-xl text-blue-600 hover:text-blue-800 transition">
                🎓 EduLearn
            </Link>

            <div className="flex gap-6 items-center font-medium">
                <Link to="/courses" className="text-gray-600 hover:text-blue-600 transition">
                    Mes Cours
                </Link>
                <Link to="/results" className="text-gray-600 hover:text-blue-600 transition">
                    Mes Résultats
                </Link>
                <button
                    onClick={handleLogout}
                    className="bg-red-50 text-red-600 px-4 py-2 rounded-full hover:bg-red-100 transition"
                >
                    Logout
                </button>
            </div>
        </nav>
    );
}