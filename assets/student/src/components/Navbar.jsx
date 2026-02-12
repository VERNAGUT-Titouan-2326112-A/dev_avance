import React from 'react';
import { Link, useLocation } from "react-router-dom";

export default function Navbar() {
    const location = useLocation();

    // Utilitaire pour la classe active
    const getLinkClass = (path) => {
        const baseClass = "nav-item";
        return location.pathname === path ? `${baseClass} nav-active` : baseClass;
    };

    return (
        <>
            <style>{`
                /* --- CSS NAVBAR "PRO" --- */
                .navbar-pro {
                    background-color: white;
                    border-bottom: 1px solid #e5e7eb;
                    position: sticky;
                    top: 0;
                    z-index: 100;
                    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
                    font-family: 'Segoe UI', sans-serif;
                }

                /* Conteneur pour aligner avec le reste du site (1200px) */
                .navbar-container {
                    max-width: 1200px;
                    margin: 0 auto;
                    padding: 0 20px;
                    height: 70px; /* Hauteur fixe pour centrer verticalement */
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                }

                /* Liens */
                .nav-links-wrapper {
                    display: flex;
                    gap: 40px; /* Espace entre les liens */
                    height: 100%;
                }

                .nav-item {
                    text-decoration: none;
                    color: #64748b; /* Gris doux */
                    font-weight: 700;
                    font-size: 14px;
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                    display: flex;
                    align-items: center;
                    border-bottom: 3px solid transparent; /* Bordure invisible par défaut */
                    transition: all 0.2s ease;
                    height: 100%; /* Prend toute la hauteur pour le hover */
                }

                .nav-item:hover {
                    color: #1e05f3; /* Bleu survol */
                }

                .nav-active {
                    color: #1e05f3; /* Bleu actif */
                    border-bottom-color: #1e05f3; /* Soulignement bleu */
                }

                /* Logo (Optionnel si tu veux le remettre plus tard) */
                .nav-brand {
                    font-size: 20px;
                    font-weight: 900;
                    color: #1e05f3;
                    text-decoration: none;
                    display: flex;
                    align-items: center;
                    gap: 8px;
                }
            `}</style>

            <nav className="navbar-pro">
                <div className="navbar-container">
                    {/* Partie Droite : Tes liens */}
                    <div className="nav-links-wrapper">
                        <Link to="/courses" className={getLinkClass('/courses')}>
                            Mes Cours
                        </Link>
                        <Link to="/results" className={getLinkClass('/results')}>
                            Mes Résultats
                        </Link>
                    </div>
                </div>
            </nav>
        </>
    );
}