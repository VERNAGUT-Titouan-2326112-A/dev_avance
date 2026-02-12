import { useEffect, useState } from "react";
import { useParams, useNavigate } from "react-router-dom";
import { getQuiz, submitQuiz } from "../api/quizzes.js";
import QuizCard from "../components/QuizCard.jsx";

export default function Quiz({ userId }) {
    const { id } = useParams();
    const navigate = useNavigate();
    const [quiz, setQuiz] = useState(null);
    const [answers, setAnswers] = useState({});

    // Pour l'affichage du bilan final
    const [showSummary, setShowSummary] = useState(false);
    const [summaryData, setSummaryData] = useState({ score: 0, details: [] });

    useEffect(() => {
        getQuiz(id).then(setQuiz);
    }, [id]);

    const handleSubmit = async () => {
        if (!quiz || !quiz.questions) return;

        let calculatedScore = 0;
        const details = [];

        quiz.questions.forEach((question) => {
            const userAnswerId = answers[question.id] || answers[String(question.id)];
            const correctAnswer = question.answers.find((a) =>
                a.correct === true || a.isCorrect === true
            );

            const isCorrect = correctAnswer && userAnswerId != null && String(userAnswerId) === String(correctAnswer.id);

            if (isCorrect) calculatedScore++;

            details.push({
                questionText: question.text,
                isCorrect: isCorrect
            });
        });

        setSummaryData({ score: calculatedScore, details: details });
        setShowSummary(true);

        try {
            await submitQuiz(id, calculatedScore, answers, userId);
        } catch (error) {
            console.error(error.response?.data);
        }
    };

    if (!quiz) return <div style={{padding: '50px', textAlign: 'center', fontWeight: 'bold', color: '#666'}}>Chargement du contenu pédagogique...</div>;

    // --- STYLE CSS INTEGRE (NUCLÉAIRE) ---
    const styles = `
        .quiz-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 40px 20px;
            font-family: 'Segoe UI', sans-serif;
        }

        /* En-têtes */
        .quiz-header { margin-bottom: 30px; }
        .quiz-theme { 
            background-color: #dbeafe; color: #1e40af; 
            padding: 4px 12px; border-radius: 20px; 
            font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;
            display: inline-block; margin-bottom: 10px;
        }
        .quiz-title { font-size: 32px; font-weight: 900; color: #111827; margin: 0; }

        /* Cartes Stats (Bilan) */
        .quiz-stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 30px; }
        .quiz-stat-card { 
            background: white; padding: 20px; border-radius: 12px; 
            border: 1px solid #e5e7eb; border-left-width: 5px; 
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); text-align: center;
        }
        .q-stat-label { font-size: 12px; font-weight: 800; text-transform: uppercase; margin-bottom: 5px; }
        .q-stat-val { font-size: 28px; font-weight: 900; color: #111827; }

        /* Liste Détails */
        .quiz-details-list { background: white; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden; margin-bottom: 30px; }
        .quiz-detail-header { background: #f9fafb; padding: 15px 20px; border-bottom: 1px solid #e5e7eb; font-weight: 700; color: #374151; }
        .quiz-detail-item { 
            display: flex; justify-content: space-between; align-items: center; 
            padding: 15px 20px; border-bottom: 1px solid #f3f4f6; 
        }
        .quiz-detail-item:last-child { border-bottom: none; }

        /* Boutons */
        .btn-quiz-primary {
            width: 100%;
            background-color: #1e05f3; /* BLEU EDULEARN */
            color: white;
            padding: 16px;
            border-radius: 12px;
            font-size: 16px; font-weight: 700;
            border: none; cursor: pointer;
            transition: background 0.2s;
            box-shadow: 0 4px 10px rgba(30, 5, 243, 0.2);
        }
        .btn-quiz-primary:hover { background-color: #1604cb; }

        .btn-quiz-secondary {
            width: 100%;
            background-color: white;
            color: #1e05f3;
            border: 2px solid #1e05f3;
            padding: 14px;
            border-radius: 12px;
            font-size: 16px; font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-quiz-secondary:hover { background-color: #f0f9ff; }
        
        /* Espacement questions */
        .question-wrapper { margin-bottom: 30px; }
    `;

    // --- VUE BILAN (SUMMARY) ---
    if (showSummary) {
        return (
            <div className="quiz-container">
                <style>{styles}</style>

                <div className="quiz-header" style={{textAlign: 'center'}}>
                    <span className="quiz-theme">Terminé</span>
                    <h2 className="quiz-title">📊 Bilan : {quiz.nom}</h2>
                </div>

                {/* STATS GRID */}
                <div className="quiz-stats-grid">
                    <div className="quiz-stat-card" style={{borderLeftColor: '#3b82f6'}}>
                        <div className="q-stat-label" style={{color: '#3b82f6'}}>Total Questions</div>
                        <div className="q-stat-val">{quiz.questions.length}</div>
                    </div>
                    <div className="quiz-stat-card" style={{borderLeftColor: '#22c55e'}}>
                        <div className="q-stat-label" style={{color: '#16a34a'}}>Correctes</div>
                        <div className="q-stat-val">{summaryData.score}</div>
                    </div>
                    <div className="quiz-stat-card" style={{borderLeftColor: '#ef4444'}}>
                        <div className="q-stat-label" style={{color: '#dc2626'}}>Erreurs</div>
                        <div className="q-stat-val">{quiz.questions.length - summaryData.score}</div>
                    </div>
                </div>

                {/* LISTE DÉTAILS */}
                <div className="quiz-details-list">
                    <div className="quiz-detail-header">Correction détaillée</div>
                    {summaryData.details.map((item, idx) => (
                        <div key={idx} className="quiz-detail-item">
                            <span style={{color: '#4b5563', fontWeight: '500'}}>
                                <span style={{fontWeight:'bold', color:'#9ca3af', marginRight:'10px'}}>#{idx + 1}</span>
                                {item.questionText}
                            </span>
                            {item.isCorrect ? (
                                <span style={{color: '#166534', fontWeight:'700', background:'#dcfce7', padding:'4px 10px', borderRadius:'6px', fontSize:'12px'}}>✅ Correcte</span>
                            ) : (
                                <span style={{color: '#991b1b', fontWeight:'700', background:'#fee2e2', padding:'4px 10px', borderRadius:'6px', fontSize:'12px'}}>❌ Incorrecte</span>
                            )}
                        </div>
                    ))}
                </div>

                <button onClick={() => navigate('/results')} className="btn-quiz-primary">
                    Voir tous mes résultats
                </button>
            </div>
        );
    }

    // --- VUE QUIZ ACTIF ---
    return (
        <div className="quiz-container">
            <style>{styles}</style>

            <div className="quiz-header">
                <span className="quiz-theme">{quiz.theme}</span>
                <h1 className="quiz-title">{quiz.nom}</h1>
            </div>

            {quiz.questions.map((q, index) => (
                <div key={q.id} className="question-wrapper">
                    {/* On passe les props au composant enfant qui gère son propre affichage */}
                    <QuizCard
                        question={q}
                        questionIndex={index + 1} // Optionnel si tu veux afficher le numéro
                        onAnswer={(val) => setAnswers(prev => ({ ...prev, [q.id]: val }))}
                        selectedAnswer={answers[q.id]}
                    />
                </div>
            ))}

            <button onClick={handleSubmit} className="btn-quiz-primary" style={{marginTop: '20px'}}>
                Terminer le Quiz et enregistrer ma note
            </button>
        </div>
    );
}