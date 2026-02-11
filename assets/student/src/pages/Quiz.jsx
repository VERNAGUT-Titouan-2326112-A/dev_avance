import { useEffect, useState } from "react";
import { useParams, useNavigate } from "react-router-dom";
import { getQuiz, submitQuiz } from "../api/quizzes.js";
import QuizCard from "../components/QuizCard.jsx";

export default function Quiz({ userId }) {
    const { id } = useParams();
    const navigate = useNavigate();
    const [quiz, setQuiz] = useState(null);
    const [answers, setAnswers] = useState({});

    // Pour l'affichage du bilan final comme sur la maquette
    const [showSummary, setShowSummary] = useState(false);
    const [summaryData, setSummaryData] = useState({ score: 0, details: [] });

    useEffect(() => {
        getQuiz(id).then(setQuiz);
    }, [id]);

    const handleSubmit = async () => {
        if (!quiz || !quiz.questions) return;

        let calculatedScore = 0;
        const details = []; // Pour stocker le Vrai/Faux par question

        quiz.questions.forEach((question) => {
            const userAnswerId = answers[question.id] || answers[String(question.id)];
            const correctAnswer = question.answers.find((a) =>
                a.correct === true || a.isCorrect === true
            );

            const isCorrect = correctAnswer && userAnswerId != null && String(userAnswerId) === String(correctAnswer.id);

            if (isCorrect) calculatedScore++;

            // On prépare le détail pour la maquette
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

    if (!quiz) return <div className="p-6 text-center">Chargement du contenu pédagogique...</div>;

    // Rendu du Bilan (Maquette EduLearn)
    if (showSummary) {
        return (
            <div className="p-6 max-w-4xl mx-auto bg-white shadow-lg rounded-lg mt-10">
                <h2 className="text-2xl font-bold mb-6 flex items-center gap-2">
                    📊 Bilan du Quiz : {quiz.nom}
                </h2>

                <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                    <div className="p-4 bg-blue-50 border-l-4 border-blue-500 rounded">
                        <p className="text-sm text-blue-600 font-semibold uppercase">Questions</p>
                        <p className="text-2xl font-bold">{quiz.questions.length}</p>
                    </div>
                    <div className="p-4 bg-green-50 border-l-4 border-green-500 rounded">
                        <p className="text-sm text-green-600 font-semibold uppercase">Correctes</p>
                        <p className="text-2xl font-bold">{summaryData.score}</p>
                    </div>
                    <div className="p-4 bg-red-50 border-l-4 border-red-500 rounded">
                        <p className="text-sm text-red-600 font-semibold uppercase">Incorrectes</p>
                        <p className="text-2xl font-bold">{quiz.questions.length - summaryData.score}</p>
                    </div>
                </div>

                <div className="space-y-3 mb-8">
                    <h3 className="font-bold border-bottom pb-2">Détail des questions :</h3>
                    {summaryData.details.map((item, idx) => (
                        <div key={idx} className="flex justify-between items-center p-3 bg-gray-50 rounded border">
                            <span className="text-sm font-medium">Question {idx + 1}: {item.questionText}</span>
                            {item.isCorrect ? (
                                <span className="text-green-600 font-bold flex items-center gap-1">✅ Correcte</span>
                            ) : (
                                <span className="text-red-600 font-bold flex items-center gap-1">❌ Incorrecte</span>
                            )}
                        </div>
                    ))}
                </div>

                <button
                    onClick={() => navigate('/results')}
                    className="w-full bg-blue-600 text-white py-3 rounded-lg font-bold hover:bg-blue-700 transition"
                >
                    Voir tous mes résultats
                </button>
            </div>
        );
    }

    // Rendu du Quiz normal
    return (
        <div className="p-6">
            <h1 className="text-3xl font-bold mb-2">{quiz.nom}</h1>
            <p className="text-gray-600 mb-6">{quiz.theme}</p>

            {quiz.questions.map((q) => (
                <QuizCard
                    key={q.id}
                    question={q}
                    onAnswer={(val) => setAnswers(prev => ({ ...prev, [q.id]: val }))}
                    selectedAnswer={answers[q.id]}
                />
            ))}

            <button
                onClick={handleSubmit}
                className="mt-6 bg-blue-600 text-white px-6 py-2 rounded w-full hover:bg-blue-700 transition"
            >
                Terminer le Quiz et enregistrer ma note
            </button>
        </div>
    );
}