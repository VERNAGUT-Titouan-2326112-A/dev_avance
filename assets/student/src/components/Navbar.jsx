import { Link } from "react-router-dom";
import { useAuth } from "../context/AuthContext";

export default function Navbar() {
    const { user, logout } = useAuth();

    return (
        <nav className="bg-white shadow px-6 py-3 flex justify-between">
            <Link to="/courses" className="font-bold text-blue-600">
                EduLearn
            </Link>

            {user && (
                <div className="flex gap-4 items-center">
                    <Link to="/courses">Cours</Link>
                    <Link to="/results">Résultats</Link>
                    <button onClick={logout} className="text-red-500">
                        Déconnexion
                    </button>
                </div>
            )}
        </nav>
    );
}
